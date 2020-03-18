<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

use Google\Protobuf\ServiceDescriptorProto;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

/**
 * Generates gRPC service interfaces and classes.
 */
class ClientService
{
    use FileWriter;

    use NamespaceConverter;

    private const INTERFACE_TPL = 'serviceInterface.tpl';

    private const SERVICE_TPL = 'clientService.tpl';

    private const TO_PROTO_METHOD_TPL = 'toProtoConverter.tpl';

    /**
     * @var TemplateWrapper
     */
    private $interfaceTemplate;

    /**
     * @var TemplateWrapper
     */
    private $serviceTemplate;

    /**
     * @var TemplateWrapper
     */
    private $toProtoTemplate;

    /**
     * @param string $templatesPath
     * @param string $outputPath
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __construct(string $templatesPath, string $outputPath)
    {
        $loader = new FilesystemLoader($templatesPath);
        $twig = new Environment($loader, [
            'cache' => false,
        ]);
        $this->interfaceTemplate = $twig->load(self::INTERFACE_TPL);
        $this->serviceTemplate = $twig->load(self::SERVICE_TPL);
        $this->toProtoTemplate = $twig->load(self::TO_PROTO_METHOD_TPL);
        $this->outputPath = $outputPath;
    }

    /**
     * Generates gRPC service interfaces and classes.
     *
     * @param string $namespace
     * @param ServiceDescriptorProto $descriptorProto
     * @return array
     */
    public function run(string $namespace, ServiceDescriptorProto $descriptorProto): array
    {
        $dtoNamespace = $this->fromProto($namespace, 'Data');
        $serviceNamespace = str_replace('\Proto', '', $namespace);
        $protoServiceClass = $namespace . '\\'. $descriptorProto->getName() . 'Client';
        $reflectionClass = ReflectionClass::createFromName($protoServiceClass);

        $methods = [];
        /** @var \Google\Protobuf\MethodDescriptorProto $method */
        foreach ($descriptorProto->getMethod() as $method) {
            $paramChunks = explode('.', $method->getInputType());
            $param = end($paramChunks);
            $returnChunks = explode('.', $method->getOutputType());
            $return = end($returnChunks);
            $reflectionMethod = $reflectionClass->getMethod($method->getName());
            $reflectionParam = $reflectionMethod->getParameter('argument');
            $mInput = '\\'. $dtoNamespace . '\\' . $param;
            $pInput = '\\' . (string) $reflectionParam->getType();
            $mOutput = '\\'. $dtoNamespace . '\\' . $return;
            $pOutput = '\\' . $namespace . '\\' . $return;
            $methods[] = [
                'name' => $method->getName(),
                'input' => [
                    'interface' => $mInput,
                    'content' => $this->getRequestDtoContent($mInput, $pInput),
                ],
                'output' => [
                    'class' => $mOutput,
                    'interface' => '\\'. $dtoNamespace . '\\' . $return . 'Interface',
                    'content' => $this->getResponseDtoContent($mOutput, $pOutput),
                ],
                'proto' => [
                    'input' => $pInput,
                    'output' => $pOutput,
                ],
            ];
        }

        $interfaceName = $descriptorProto->getName() . 'Interface';
        $content = $this->interfaceTemplate->render([
            'namespace' => $serviceNamespace,
            'name' => $interfaceName,
            'methods' => $methods
        ]);

        $path = $this->convertToDirName($serviceNamespace);
        $this->writeFile($content, $path, $interfaceName . '.php');

        $content = $this->serviceTemplate->render([
            'namespace' => $serviceNamespace,
            'interface' => $interfaceName,
            'name' => $descriptorProto->getName(),
            'methods' => $methods,
            'proto' => [
                'class' => '\\' . $protoServiceClass,
            ],
        ]);
        $this->writeFile($content, $path, $descriptorProto->getName() . '.php');

        return [
            'interface' => $serviceNamespace . '\\' . $interfaceName,
            'class' => $serviceNamespace . '\\' . $descriptorProto->getName()
        ];
    }

    /**
     * Gets list of class properties used for getters and setters generation.
     *
     * @param string $fqcn
     * @param string $in
     * @param string $out
     * @return array
     */
    private function getListOfClassProps(string $fqcn, string $in = 'in_type', string $out = 'out_type'): array
    {
        $props = [];
        $reflection = ReflectionClass::createFromName($fqcn);
        $methods = $reflection->getImmediateMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            // skip not getter methods to do not duplicate properties
            if (strpos($method->getName(), 'get', 0) === false) {
                continue;
            }

            $type = $method->getReturnType()->getName();
            $property = [
                'name' => substr($method->getName(), 3),
                $in => $type,
                'array' => false,
                'object' => false
            ];
            // getter returns object
            if (!$method->getReturnType()->isBuiltin()) {
                $property[$in] = '\\' . str_replace('Interface', '', $type);
                $property[$out] = '\\' . $this->getProtoFqcn($type);
                $property['object'] = true;
                $property['props'] = $this->getListOfClassProps($type, $in, $out);
            // getter returns array of objects
            } elseif ($method->getReturnType()->getName() === 'array') {
                $docType = str_replace('[]', '', (string) $method->getDocBlockReturnTypes()[0]);
                $property['props'] = $this->getListOfClassProps($docType, $in, $out);
                $property['array'] = true;
                $property[$in] = str_replace('Interface', '', $docType);
                $property[$out] = $this->getProtoFqcn($docType);
            }
            $props[] = $property;
        }

        return $props;
    }

    /**
     * Generates DTO converter method body based on provided Request DTO.
     *
     * @param string $fqcn
     * @return string
     */
    private function getRequestDtoContent(string $fqcn, string $proto): string
    {
        $props = $this->getListOfClassProps($fqcn);
        $content = $this->renderPropertiesTree('value', $fqcn, 'proto', $proto, $props);
        return $content;
    }

    /**
     * Generates DTO converter method body based on provided Response DTO.
     *
     * @param string $fqcn
     * @param string $proto
     * @return string
     */
    private function getResponseDtoContent(string $fqcn, string $proto): string
    {
        $props = $this->getListOfClassProps($fqcn, 'out_type', 'in_type');
        $content = $this->renderPropertiesTree('value', $proto, 'out', $fqcn, $props);
        return $content;
    }

    /**
     * Renders converter method body for provided input and output classes.
     *
     * @param string $inVar
     * @param string $inType
     * @param string $outVar
     * @param string $outType
     * @param array $properties
     * @return string
     */
    private function renderPropertiesTree(
        string $inVar,
        string $inType,
        string $outVar,
        string $outType,
        array $properties
    ): string {
        return $this->toProtoTemplate->render(
            [
                'in_var' => $inVar,
                'in_type' => $inType,
                'out_var' => $outVar,
                'out_type' => $outType,
                'props' => $properties
            ]
        );
    }

    /**
     * Gets proto-generated DTO FQCN from Magento DTO.
     *
     * @param string $fqcn
     * @return string
     */
    private function getProtoFqcn(string $fqcn): string
    {
        return str_replace('Interface', '', $this->toProto($fqcn, 'Data'));
    }
}

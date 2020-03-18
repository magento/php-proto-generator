<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

use Google\Protobuf\DescriptorProto;
use Google\Protobuf\FieldDescriptorProto;
use Google\Protobuf\FieldDescriptorProto\Type;
use Google\Protobuf\FileDescriptorProto;
use Google\Protobuf\ServiceDescriptorProto;
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

    use TypeResolver;

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
    public function run(string $namespace, ServiceDescriptorProto $descriptorProto, FileDescriptorProto $fileDescriptorProto): array
    {
        $serviceNamespace = str_replace('\Proto', '', $namespace);
        $protoServiceClass = $namespace . '\\'. $descriptorProto->getName() . 'Client';

        $methods = [];
        /** @var \Google\Protobuf\MethodDescriptorProto $method */
        foreach ($descriptorProto->getMethod() as $method) {
            $pInput = $this->convertProtoNameToFqcn($method->getInputType());
            $pOutput = $this->convertProtoNameToFqcn($method->getOutputType());
            $mInput = $this->fromProto($pInput, 'Data');
            $mOutput = $this->fromProto($pOutput, 'Data');
            $methods[] = [
                'name' => $method->getName(),
                'input' => [
                    'interface' => $mInput . 'Interface',
                    'content' => $this->getRequestDtoContent($mInput, $pInput, $fileDescriptorProto),
                ],
                'output' => [
                    'class' => $mOutput,
                    'interface' => $mOutput . 'Interface',
                    'content' => $this->getResponseDtoContent($mOutput, $pOutput, $fileDescriptorProto),
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
     * Gets list of message properties properties used for getters and setters generation.
     *
     * @param string $fqcn
     * @param string $in
     * @param string $out
     * @return array
     */
    private function getPropertyList(
        string $fqcn,
        FileDescriptorProto $fileDescriptorProto,
        string $in = 'in_type',
        string $out = 'out_type'
    ): array {
        $props = [];
        $methodDescriptor = $this->getMessageDescriptorByName($fileDescriptorProto, $fqcn);
        /** @var \Google\Protobuf\FieldDescriptorProto $field */
        foreach ($methodDescriptor->getField() as $field) {
            $type = $this->getType($field);
            $name = str_replace('_', '', ucwords($field->getName(), '_'));
            $property = [
                'name' => $name,
                $in => $type,
                'array' => false,
                'object' => false
            ];
            if ((int) $type === Type::TYPE_MESSAGE) {
                $className = $this->convertProtoNameToFqcn($field->getTypeName());
                // getter returns array of objects
                if ($field->getLabel() === FieldDescriptorProto\Label::LABEL_REPEATED) {
                    $property['props'] = $this->getPropertyList($className, $fileDescriptorProto, $in, $out);
                    $property['array'] = true;
                    $property[$in] = $this->fromProto($className, 'Data');
                    $property[$out] = $className;
                    // getter returns an object
                } else {
                    $property[$in] = $this->fromProto($className, 'Data');
                    $property[$out] = $className;
                    $property['object'] = true;
                    $property['props'] = $this->getPropertyList($className, $fileDescriptorProto, $in, $out);
                }
            }
            $props[] = $property;
        }

        return $props;
    }

    /**
     * Generates DTO converter method body based on provided Request DTO.
     *
     * @param string $fqcn
     * @param string $proto
     * @param FileDescriptorProto $fileDescriptorProto
     * @return string
     */
    private function getRequestDtoContent(
        string $fqcn,
        string $proto,
        FileDescriptorProto $fileDescriptorProto
    ): string {
        $props = $this->getPropertyList($proto, $fileDescriptorProto);
        $content = $this->renderPropertiesTree('value', $fqcn, 'proto', $proto, $props);
        return $content;
    }

    /**
     * Generates DTO converter method body based on provided Response DTO.
     *
     * @param string $fqcn
     * @param string $proto
     * @param FileDescriptorProto $fileDescriptorProto
     * @return string
     */
    private function getResponseDtoContent(
        string $fqcn,
        string $proto,
        FileDescriptorProto $fileDescriptorProto
    ): string {
        $props = $this->getPropertyList($proto, $fileDescriptorProto, 'out_type', 'in_type');
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
     * Gets Protobuf message descriptor by name.
     *
     * @param FileDescriptorProto $fileDescriptorProto
     * @param string $fqcn
     * @return DescriptorProto|null
     */
    private function getMessageDescriptorByName(FileDescriptorProto $fileDescriptorProto, string $fqcn): ?DescriptorProto
    {
        $chunks = explode('\\', $fqcn);
        $fieldName = end($chunks);
        /** @var \Google\Protobuf\DescriptorProto $descriptor */
        foreach ($fileDescriptorProto->getMessageType() as $descriptor) {
            if ($descriptor->getName() === $fieldName) {
                return $descriptor;
            }
        }

        return null;
    }
}

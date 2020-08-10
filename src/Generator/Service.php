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
use Google\Protobuf\ServiceDescriptorProto;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

/**
 * Generates gRPC service interfaces and classes.
 */
class Service
{
    use FileWriter;

    use NamespaceConverter;

    use TypeResolver;

    private const INTERFACE_TPL = 'serviceInterface.tpl';

    private const SERVICE_TPL = 'clientService.tpl';

    private const IN_MEMORY_SERVICE_TPL = 'inMemoryClientService.tpl';

    private const SERVER_PROXY_TPL = 'serverProxy.tpl';

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
    private $inMemoryServiceTemplate;

    /**
     * @var TemplateWrapper
     */
    private $serverProxyTemplate;

    /**
     * @var TemplateWrapper
     */
    private $toProtoTemplate;

    /**
     * @param string $templatesPath
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __construct(string $templatesPath)
    {
        $loader = new FilesystemLoader($templatesPath);
        $twig = new Environment($loader, ['cache' => false]);
        $this->interfaceTemplate = $twig->load(self::INTERFACE_TPL);
        $this->serviceTemplate = $twig->load(self::SERVICE_TPL);
        $this->inMemoryServiceTemplate = $twig->load(self::IN_MEMORY_SERVICE_TPL);
        $this->serverProxyTemplate = $twig->load(self::SERVER_PROXY_TPL);
        $this->toProtoTemplate = $twig->load(self::TO_PROTO_METHOD_TPL);
    }

    /**
     * Generates gRPC service interfaces and classes.
     *
     * @param string $namespace
     * @param ServiceDescriptorProto $descriptorProto
     * @param array $fileDescriptorAggr
     * @return array
     */
    public function run(
        string $namespace,
        ServiceDescriptorProto $descriptorProto,
        array $fileDescriptorAggr
    ): \Generator {
        $serviceNamespace = str_replace('Proto', 'Api', $namespace);
        $protoServiceClass = $namespace . '\\' . $descriptorProto->getName() . 'Client';

        $methods = [];
        $imports = [];
        $protoImports = [];
        /** @var \Google\Protobuf\MethodDescriptorProto $method */
        foreach ($descriptorProto->getMethod() as $method) {
            $pInput = $this->convertProtoNameToFqcn($method->getInputType());
            $pOutput = $this->convertProtoNameToFqcn($method->getOutputType());
            $mInput = $this->fromProto($pInput, 'Api\\Data');
            $mOutput = $this->fromProto($pOutput, 'Api\\Data');
            $imports[] = $mInput . 'Interface';
            $imports[] = $mOutput . 'Interface';
            $protoImports[] = $pInput;
            $protoImports[] = $pOutput;
            $methods[] = [
                'name' => $method->getName(),
                'input' => $this->prepareType($mInput, $pInput, $fileDescriptorAggr),
                'output' => $this->prepareType($mOutput, $pOutput, $fileDescriptorAggr),
                'proto' => [
                    'input' => $this->getNameFromFqcn($pInput),
                    'output' => $this->getNameFromFqcn($pOutput),
                ],
            ];
        }

        $imports = array_unique($imports);
        $protoImports = array_unique($protoImports);

        yield [
            'class' => $serviceNamespace . '\\' . $descriptorProto->getName(),
            'interface' => $serviceNamespace . '\\' . $descriptorProto->getName() . 'Interface'
        ];

        yield $this->generateServiceInterface(
            $descriptorProto->getName() . 'Interface',
            $serviceNamespace,
            $methods,
            $imports
        );

        yield $this->generateServiceInterface(
            $descriptorProto->getName() . 'ServerInterface',
            $serviceNamespace,
            $methods,
            $imports
        );

        // generate gRPC client service
        $protoImports[] = '\\' . $protoServiceClass;
        $content = $this->serviceTemplate->render(
            [
                'namespace' => $serviceNamespace,
                'interface' => $descriptorProto->getName() . 'Interface',
                'name' => $descriptorProto->getName(),
                'methods' => $methods,
                'imports' => $imports,
                'proto' => [
                    'class' => $this->getNameFromFqcn($protoServiceClass),
                    'imports' => $protoImports
                ]
            ]
        );
        yield $this->createFile(
            $this->convertToDirName($serviceNamespace) . '/' . $descriptorProto->getName() . '.php',
            $content
        );

        // generate gRPC in-memory client
        $content = $this->inMemoryServiceTemplate->render(
            [
                'namespace' => $serviceNamespace,
                'interface' => $descriptorProto->getName() . 'Interface',
                'name' => 'InMemory' . $descriptorProto->getName(),
                'methods' => $methods,
                'imports' => $imports,
                'serverInterface' => $descriptorProto->getName() . 'ServerInterface'
            ]
        );
        yield $this->createFile(
            $this->convertToDirName($serviceNamespace) . '/' . 'InMemory' . $descriptorProto->getName() . '.php',
            $content
        );

        // generate gRPC proxy server
        $content = $this->serverProxyTemplate->render(
            [
                'namespace' => $serviceNamespace,
                'interface' => '\\' . $namespace . '\\' . $descriptorProto->getName() . 'Interface',
                'name' => $descriptorProto->getName() . 'ProxyServer',
                'methods' => $methods,
                'imports' => $imports,
                'protoImports' => $protoImports,
                'serverInterface' => $descriptorProto->getName() . 'ServerInterface'
            ]
        );
        yield $this->createFile(
            $this->convertToDirName($serviceNamespace) . '/' . $descriptorProto->getName() . 'ProxyServer' . '.php',
            $content
        );
    }

    /**
     * Gets list of message properties properties used for getters and setters generation.
     *
     * @param string $fqcn
     * @param array $fileDescriptorAggregate
     * @param string $in
     * @param string $out
     * @return array
     */
    private function getPropertyList(
        string $fqcn,
        array $fileDescriptorAggregate,
        string $in = 'in_type',
        string $out = 'out_type'
    ): array {
        $props = [];
        $methodDescriptor = $this->getMessageDescriptorByName($fileDescriptorAggregate, $fqcn);
        if ($methodDescriptor === null) {
            throw new \InvalidArgumentException('Cannot find proto file descriptor for ' . $fqcn);
        }
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
            if ($field->getLabel() === FieldDescriptorProto\Label::LABEL_REPEATED) {
                $property['array'] = true;
            }
            if ((int) $type === Type::TYPE_MESSAGE) {
                $className = $this->convertProtoNameToFqcn($field->getTypeName());
                // getter returns array of objects
                $property[$in] = $this->fromProto($className, 'Api\\Data');
                $property[$out] = $className;
                $property['props'] = $this->getPropertyList($className, $fileDescriptorAggregate, $in, $out);
                $property['object'] = true;
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
     * @param array $fileDescriptorAggregate
     * @return string
     */
    private function getMagentoToProtoDtoConverterContent(
        string $fqcn,
        string $proto,
        array $fileDescriptorAggregate
    ): string {
        $props = $this->getPropertyList($proto, $fileDescriptorAggregate);
        $content = $this->renderPropertiesTree('value', $fqcn, 'proto', $proto, $props);
        return $content;
    }

    /**
     * Generates DTO converter method body based on provided Response DTO.
     *
     * @param string $fqcn
     * @param string $proto
     * @param array $fileDescriptorAggregate
     * @return string
     */
    private function getProtoToMagentoDtoConverterContent(
        string $fqcn,
        string $proto,
        array $fileDescriptorAggregate
    ): string {
        $props = $this->getPropertyList($proto, $fileDescriptorAggregate, 'out_type', 'in_type');
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
     * @param array $fileDescriptorAggregate
     * @param string $fqcn
     * @return DescriptorProto|null
     */
    private function getMessageDescriptorByName(array $fileDescriptorAggregate, string $fqcn): ?DescriptorProto
    {
        $chunks = explode('\\', $fqcn);
        $fieldName = end($chunks);
        foreach ($fileDescriptorAggregate as $fileDescriptor) {
            /** @var \Google\Protobuf\DescriptorProto $descriptor */
            foreach ($fileDescriptor->getMessageType() as $descriptor) {
                if ($descriptor->getName() === $fieldName) {
                    return $descriptor;
                }
            }
        }

        return null;
    }

    /**
     * Generates service interface class.
     *
     * @param string $interfaceName
     * @param string $namespace
     * @param array $methods
     * @param array $imports
     * @return \Google\Protobuf\Compiler\CodeGeneratorResponse\File
     */
    private function generateServiceInterface(
        string $interfaceName,
        string $namespace,
        array $methods,
        array $imports
    ) {
        $content = $this->interfaceTemplate->render(
            [
                'namespace' => $namespace,
                'name' => $interfaceName,
                'methods' => $methods,
                'imports' => $imports
            ]
        );
        $path = $this->convertToDirName($namespace);
        return $this->createFile($path . '/' . $interfaceName . '.php', $content);
    }

    /**
     * Splits FQCN by `\` and returns a class/interface name.
     *
     * @param string $fqcn
     * @return string
     */
    private function getNameFromFqcn(string $fqcn): string
    {
        $chunks = explode('\\', $fqcn);
        $className = array_pop($chunks);
        return $className;
    }

    /**
     * @param string $magentoType
     * @param string $protoType
     * @param array $fileDescriptorAggr
     * @return array
     */
    private function prepareType(string $magentoType, string $protoType, array $fileDescriptorAggr): array
    {
        return [
            'class' => $magentoType,
            'type' => $this->getNameFromFqcn($magentoType . 'Interface'),
            'toProtoContent' => $this->getMagentoToProtoDtoConverterContent(
                $magentoType,
                $protoType,
                $fileDescriptorAggr
            ),
            'fromProtoContent' => $this->getProtoToMagentoDtoConverterContent(
                $magentoType,
                $protoType,
                $fileDescriptorAggr
            ),
        ];
    }
}

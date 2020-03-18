<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

use Google\Protobuf\DescriptorProto;
use Google\Protobuf\FieldDescriptorProto;
use Google\Protobuf\FieldDescriptorProto\Label;
use Google\Protobuf\FieldDescriptorProto\Type;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

/**
 * Generates Magento DTO interfaces and their implementations.
 */
class Dto
{
    use FileWriter;

    use NamespaceConverter;

    private const CLASS_TPL = 'dto.tpl';

    private const INTERFACE_TPL = 'dtoInterface.tpl';

    private const TYPE_MESSAGE = 'message';

    private static $typeMap = [
        Type::TYPE_DOUBLE => 'double',
        Type::TYPE_FLOAT => 'float',
        Type::TYPE_INT64 => 'int',
        Type::TYPE_UINT64 => 'int',
        Type::TYPE_INT32 => 'int',
        Type::TYPE_BOOL => 'bool',
        Type::TYPE_STRING => 'string',
        Type::TYPE_MESSAGE => self::TYPE_MESSAGE,
        Type::TYPE_UINT32 => 'int',
        Type::TYPE_SINT32 => 'int',
        Type::TYPE_SINT64 => 'int',
    ];

    /**
     * Twig template
     *
     * @var TemplateWrapper
     */
    private $classTemplate;

    /**
     * @var TemplateWrapper
     */
    private $interfaceTemplate;

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
            'cache' => false
        ]);
        $this->classTemplate = $twig->load(self::CLASS_TPL);
        $this->interfaceTemplate = $twig->load(self::INTERFACE_TPL);
        $this->outputPath = $outputPath;
    }

    /**
     * Generates DTO interfaces and classes.
     *
     * @param string $namespace
     * @param DescriptorProto $descriptor
     * @return array `['interface' => FQCN, 'class' => FQCN]`
     */
    public function run(string $namespace, DescriptorProto $descriptor): array
    {
        $fields = [];
        $dtoNamespace = $this->fromProto($namespace, 'Data');

        /** @var \Google\Protobuf\FieldDescriptorProto $field */
        foreach ($descriptor->getField() as $field) {
            $name = str_replace('_', '', ucwords($field->getName(), '_'));
            $type = $docType = $this->getType($field);
            // check if a getter method parameter is a simple type
            if ($type === self::TYPE_MESSAGE) {
                $type = $docType = $this->fromProto($this->convertProtoNameToFqcn($field->getTypeName()), 'Data') . 'Interface';
                // check if message is repeated
                if ($field->getLabel() === Label::LABEL_REPEATED) {
                    $docType .= '[]';
                    $type = 'array';
                }
            }
            $fields[] = [
                'name' => $name,
                'type' => $type,
                'propertyName' => lcfirst($name),
                'doc' => [
                    'input' => $docType,
                    'output' => $docType
                ]
            ];
        }

        $content = $this->classTemplate->render([
            'namespace' => $dtoNamespace,
            'class' => $descriptor->getName(),
            'fields' => $fields
        ]);
        $path = $this->convertToDirName($dtoNamespace);
        $this->writeFile($content, $path, $descriptor->getName() . '.php');

        $content = $this->interfaceTemplate->render([
            'namespace' => $dtoNamespace,
            'class' => $descriptor->getName(),
            'fields' => $fields
        ]);
        $this->writeFile($content, $path, $descriptor->getName() . 'Interface.php');

        return [
            'interface' => $dtoNamespace . '\\' . $descriptor->getName() . 'Interface',
            'class' => $dtoNamespace . '\\' . $descriptor->getName(),
        ];
    }

    /**
     * Detects PHP type based on proto type.
     *
     * @param FieldDescriptorProto $field
     * @return string
     */
    private function getType(FieldDescriptorProto $field): string
    {
        $type = self::$typeMap[$field->getType()] ?? null;
        if ($type === null) {
            throw new \InvalidArgumentException('{' . $field->getName() . ':' . $field->getType() . '} is not supported');
        }

        return (string)$type;
    }
}
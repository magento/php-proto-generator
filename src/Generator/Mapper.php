<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

use Google\Protobuf\DescriptorProto;
use Google\Protobuf\FieldDescriptorProto\Label;
use Google\Protobuf\FieldDescriptorProto\Type;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

/**
 * Generates Magento DTO interfaces and their implementations.
 */
class Mapper
{
    use FileWriter;

    use NamespaceConverter;

    use TypeResolver;

    private const CLASS_TPL = 'DtoMapper.tpl';

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
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __construct(string $templatesPath)
    {
        $loader = new FilesystemLoader($templatesPath);
        $twig = new Environment($loader, [
            'cache' => false
        ]);
        $this->classTemplate = $twig->load(self::CLASS_TPL);
    }

    /**
     * Generates DTO interfaces and classes.
     *
     * @param string $namespace
     * @param DescriptorProto $descriptor
     * @return array `['files' => File[], 'preferences' => ['interface' => FQCN, 'class' => FQCN]]`
     */
    public function run(string $namespace, DescriptorProto $descriptor): array
    {
        $fields = [];
        $files = [];
        $dtoNamespace = $this->fromProto($namespace, 'Api\\Data');

        /** @var \Google\Protobuf\FieldDescriptorProto $field */
        foreach ($descriptor->getField() as $field) {
            $name = str_replace('_', '', ucwords($field->getName(), '_'));
            $type = $docType = $this->getType($field);
            $isSimple = true;
            // check if a getter method parameter is a simple type
            if ((int) $type === Type::TYPE_MESSAGE) {
                $type = $docType = $this->fromProto(
                        $this->convertProtoNameToFqcn($field->getTypeName()),
                        'Api\\Data')
                    . 'Mapper';
                $isSimple = false;
                // check if message is repeated
                if ($field->getLabel() === Label::LABEL_REPEATED) {
                    $docType .= '[]';
                    $type = 'array';
                    $isSimple = true;
                }
            }
            $fields[] = [
                'name' => $name,
                'type' => $type,
                'simple' => $isSimple,
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
        $files[] = $this->createFile($path . '/' . $descriptor->getName() . 'Mapper' . '.php', $content);

        return [
            'files' => $files
        ];
    }
}
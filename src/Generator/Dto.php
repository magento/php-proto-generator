<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

use Google\Protobuf\DescriptorProto;
use Roave\BetterReflection\Reflection\ReflectionClass;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

/**
 * Generates Magento DTO interfaces and their implementations.
 */
class Dto
{
    use FileWriter;

    private const CLASS_TPL = 'dto.tpl';

    private const INTERFACE_TPL = 'dtoInterface.tpl';

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
        $dtoNamespace = str_replace('Proto', 'Data', $namespace);
        $reflectionClass = $this->getProtoReflection($namespace, $descriptor->getName());

        /** @var \Google\Protobuf\FieldDescriptorProto $field */
        foreach ($descriptor->getField() as $field) {
            $name = str_replace('_', '', ucwords($field->getName(), '_'));
            $methodInfo = $reflectionClass->getMethod('get' . $name);
            $type = $docType = (string) $methodInfo->getDocBlockReturnTypes()[0];
            // check if a getter method parameter is a simple type
            if ($this->isFQCN($docType)) {
                if ($this->isRepeatedField($docType)) {
                    // proto generated code for getter methods does not have a correct return type
                    $setterDocType = $this->getRepeatedSetterParam($reflectionClass, 'set' . $name);
                    $docType = str_replace('Proto', 'Data', $setterDocType) . 'Interface[]';
                    $type = 'array';
                } else {
                    $type = $docType = str_replace('Proto', 'Data', $docType) . 'Interface';
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
     * Gets reflection class for proto-generated class.
     *
     * @param string $namespace
     * @param string $className
     * @return ReflectionClass
     */
    private function getProtoReflection(string $namespace, string $className): ReflectionClass
    {
        return ReflectionClass::createFromName($namespace . '\\' . $className);
    }

    /**
     * Checks if provided type is FQCN.
     *
     * @param string $type
     * @return bool
     */
    private function isFQCN(string $type): bool
    {
        $types = ['string', 'float', 'int', 'bool', 'array'];
        return !in_array($type, $types);
    }

    /**
     * Checks if generated class from proto is a repeated field.
     *
     * @param string $fqcn
     * @return bool
     */
    private function isRepeatedField(string $fqcn): bool
    {
        $reflection = ReflectionClass::createFromName($fqcn);
        return $reflection->implementsInterface(\ArrayAccess::class);
    }

    /**
     * Returns a param type for proto generated setter method.
     *
     * @param ReflectionClass $class
     * @param string $method
     * @return string
     */
    private function getRepeatedSetterParam(ReflectionClass $class, string $method): string
    {
        $methodInfo = $class->getMethod($method);
        $pattern = '/@param\s(\S{1,})\[/';
        preg_match($pattern, $methodInfo->getDocComment(), $matches, PREG_OFFSET_CAPTURE, 0);
        return $matches[1][0];
    }
}
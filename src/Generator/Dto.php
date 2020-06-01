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
class Dto
{
    use FileWriter;

    private const CLASS_TPL = 'dto.tpl';

    private const INTERFACE_TPL = 'dtoInterface.tpl';

    private const MAPPER_TPL = 'DtoMapper.tpl';

    private const ARRAY_MAPPER_TPL = 'ArrayMapper.tpl';

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
     * @var TemplateWrapper
     */
    private $arrayMapperTemplate;

    /**
     * @var TemplateWrapper
     */
    private $dtoMapperTemplate;

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
        $this->interfaceTemplate = $twig->load(self::INTERFACE_TPL);
        $this->arrayMapperTemplate = $twig->load(self::ARRAY_MAPPER_TPL);
        $this->dtoMapperTemplate = $twig->load(self::MAPPER_TPL);
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
        $files = [];

        $dtoDescriptor = new DescriptorMagentoDto();
        $contentData = $dtoDescriptor->describe($namespace, $descriptor);

        $content = $this->classTemplate->render($contentData);
        $path = $this->convertToDirName($contentData['namespace']);
        $files[] = $this->createFile($path . '/' . $descriptor->getName() . '.php', $content);

        $content = $this->interfaceTemplate->render($contentData);
        $files[] = $this->createFile($path . '/' . $descriptor->getName() . 'Interface.php', $content);

        $content = $this->dtoMapperTemplate->render($contentData);
        $files[] = $this->createFile($path . '/' . $descriptor->getName() . 'Mapper.php', $content);

        $content = $this->arrayMapperTemplate->render($contentData);
        $files[] = $this->createFile($path . '/' . $descriptor->getName() . 'ArrayMapper.php', $content);

        return [
            'files' => $files,
            'preferences' => [
                'interface' => $contentData['namespace'] . '\\' . $descriptor->getName() . 'Interface',
                'class' => $contentData['namespace'] . '\\' . $descriptor->getName(),
            ],
        ];
    }
}
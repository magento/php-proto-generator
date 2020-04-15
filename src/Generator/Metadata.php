<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

use Google\Protobuf\Compiler\CodeGeneratorResponse\File;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;

/**
 * Generates Metadata class.
 */
class Metadata
{
    use FileWriter;

    private const METADATA_TPL = 'metadata.tpl';

    /**
     * @var TemplateWrapper
     */
    private $template;

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

        $this->template = $twig->load(self::METADATA_TPL);
    }

    /**
     * Generates Metadata class.
     *
     * @param string $namespace
     * @return File
     */
    public function run(string $namespace): File {
        $name = 'Metadata';
        $namespace = str_replace('Proto', $name, $namespace);
        $content = $this->template->render([
            'name' => $name,
            'namespace' => $namespace
        ]);

        $path = $this->convertToDirName($namespace);
        return $this->createFile($path . '/' . $name . '.php', $content);
    }
}

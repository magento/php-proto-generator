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
 * Generates di.xml file.
 */
class Di
{
    use FileWriter;

    private const DI_TPL = 'di.tpl';

    /**
     * @var TemplateWrapper
     */
    private $diTemplate;

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
        $this->diTemplate = $twig->load(self::DI_TPL);
    }

    /**
     * Generates di.xml based on provided list of preferences.
     *
     * @param array $preferences `['interface' => FQCN, `class` => FQCN]`
     * @return File
     */
    public function run(array $preferences, string $path): File
    {
        $content = $this->diTemplate->render([
            'preferences' => $preferences,
        ]);
        return $this->createFile($path . '/etc/di.xml', $content);
    }
}

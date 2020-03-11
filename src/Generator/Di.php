<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

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
        $this->diTemplate = $twig->load(self::DI_TPL);
        $this->outputPath = $outputPath;
    }

    /**
     * Generates di.xml based on provided list of preferences.
     *
     * @param array $preferences `['interface' => FQCN, `class` => FQCN]`
     */
    public function run(array $preferences, string $path): void
    {
        $content = $this->diTemplate->render([
            'preferences' => $preferences,
        ]);
        $this->writeFile($content, $path . '/etc', 'di.xml');
    }
}

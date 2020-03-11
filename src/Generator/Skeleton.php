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
 * Generates Magento module skeleton: composer.json, registration.php, etc/module.xml
 */
class Skeleton
{
    use FileWriter;

    private const COMPOSER_TPL = 'composer.tpl';

    private const REGISTRATION_TPL = 'registrationPhp.tpl';

    private const MODULE_XML_TPL = 'moduleXml.tpl';

    /**
     * @var TemplateWrapper
     */
    private $composerTemplate;

    /**
     * @var TemplateWrapper
     */
    private $registrationTemplate;

    /**
     * @var TemplateWrapper
     */
    private $moduleXmlTemplate;

    /**
     * @param string $templatesPath
     * @param string $outputPath
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __construct(string $templatesPath, string $outputPath)
    {
        $this->outputPath = $outputPath;
        $loader = new FilesystemLoader($templatesPath);
        $twig = new Environment($loader, [
            'cache' => false
        ]);

        $this->composerTemplate = $twig->load(self::COMPOSER_TPL);
        $this->registrationTemplate = $twig->load(self::REGISTRATION_TPL);
        $this->moduleXmlTemplate = $twig->load(self::MODULE_XML_TPL);
    }

    /**
     * Generates module skeleton including `composer.json`, `registration.php`, `etc/module.xml`.
     *
     * @param string $vendor
     * @param string $module
     */
    public function run(string $vendor, string $module): void
    {
        $path = $vendor . '/' . $module;
        $moduleName = $vendor . '_' . $module;
        $this->generateComposer($vendor, $module, $path);
        $this->generateRegistration($moduleName, $path);
        $this->generateModuleXml($moduleName, $path);
    }

    /**
     * Generates composer json file.
     *
     * @param string $vendor
     * @param string $module
     * @param string $path
     */
    private function generateComposer(string $vendor, string $module, string $path): void
    {
        $content = $this->composerTemplate->render([
            'module' => [
                'name' => strtolower($vendor) . '/module-' . strtolower($module),
                'namespace' => $vendor . '\\\\' . $module . '\\\\'
            ],
        ]);
        $this->writeFile($content, $path, 'composer.json');
    }

    /**
     * Generates `registration.php` file.
     *
     * @param string $moduleName
     * @param string $path
     */
    private function generateRegistration(string $moduleName, string $path): void
    {
        $content = $this->registrationTemplate->render([
            'module' => [
                'name' => $moduleName
            ],
        ]);
        $this->writeFile($content, $path, 'registration.php');
    }

    /**
     * Generates `etc/module.xml` file.
     *
     * @param string $moduleName
     * @param string $path
     */
    private function generateModuleXml(string $moduleName, string $path): void
    {
        $content = $this->moduleXmlTemplate->render([
            'module' => [
                'name' => $moduleName
            ],
        ]);
        $this->writeFile($content, $path . '/etc', 'module.xml');
    }
}

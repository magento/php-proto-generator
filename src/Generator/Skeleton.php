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
 * Generates Magento module skeleton: composer.json, registration.php, etc/module.xml
 */
class Skeleton
{
    use FileWriter;

    private const COMPOSER_TPL = 'composer.tpl';

    private const REGISTRATION_TPL = 'registrationPhp.tpl';

    private const README_MD_TPL = 'readmeMd.tpl';

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
    private $readmeMdTemplate;

    /**
     * @var TemplateWrapper
     */
    private $moduleXmlTemplate;

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

        $this->composerTemplate = $twig->load(self::COMPOSER_TPL);
        $this->registrationTemplate = $twig->load(self::REGISTRATION_TPL);
        $this->readmeMdTemplate = $twig->load(self::README_MD_TPL);
        $this->moduleXmlTemplate = $twig->load(self::MODULE_XML_TPL);
    }

    /**
     * Generates module skeleton including `composer.json`, `registration.php`, `etc/module.xml`.
     *
     * @param string $vendor
     * @param string $module
     * @return File[]
     */
    public function run(string $vendor, string $module): \Generator
    {
        $path = $vendor . '/' . $module;
        $moduleName = $vendor . '_' . $module;
        yield $this->generateComposer($vendor, $module, $path);
        yield $this->generateRegistration($moduleName, $path);
        yield $this->generateReadmeMd($moduleName, $path);
        yield $this->generateModuleXml($moduleName, $path);
    }

    /**
     * Generates composer json file.
     *
     * @param string $vendor
     * @param string $module
     * @param string $path
     * @return File
     */
    private function generateComposer(string $vendor, string $module, string $path): File
    {
        $name = $this->getModuleName($module);
        $content = $this->composerTemplate->render([
            'module' => [
                'name' => strtolower($vendor) . '/module-' . $name,
                'namespace' => $vendor . '\\\\' . $module . '\\\\'
            ],
        ]);
        return $this->createFile($path . '/composer.json', $content);
    }

    /**
     * Generates `registration.php` file.
     *
     * @param string $moduleName
     * @param string $path
     * @return File
     */
    private function generateRegistration(string $moduleName, string $path): File
    {
        $content = $this->registrationTemplate->render([
            'module' => [
                'name' => $moduleName
            ],
        ]);
        return $this->createFile($path . '/registration.php', $content);
    }

    /**
     * Generates `README.md` file.
     *
     * @param string $moduleName
     * @param string $path
     * @return File
     */
    private function generateReadmeMd(string $moduleName, string $path): File
    {
        $content = $this->readmeMdTemplate->render([
            'module' => [
                'name' => $moduleName
            ],
        ]);
        return $this->createFile($path . '/README.md', $content);
    }

    /**
     * Generates `etc/module.xml` file.
     *
     * @param string $moduleName
     * @param string $path
     * @return File
     */
    private function generateModuleXml(string $moduleName, string $path): File
    {
        $content = $this->moduleXmlTemplate->render([
            'module' => [
                'name' => $moduleName
            ],
        ]);
        return $this->createFile($path . '/etc/module.xml', $content);
    }

    /**
     * Converts camel case name to hyphen separated lower case words.
     *
     * @param string $value
     * @return string
     */
    private function getModuleName(string $value): string
    {
        $pattern = '/(?:^|[A-Z])[a-z]+/';
        preg_match_all($pattern, $value, $matches);
        return strtolower(implode('-', $matches[0]));
    }
}

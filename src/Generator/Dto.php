<?php
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

use Google\Protobuf\Compiler\CodeGeneratorRequest;

class Dto
{
    /**
     * Twig template
     *
     * @var \Twig\TemplateWrapper
     */
    private $template;
    /**
     * @var string
     */
    private $outputPath;

    /**
     * Dto constructor.
     * @param string $templatesPath
     * @param string $outputPath
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __construct(string $templatesPath, string $outputPath)
    {
        $loader = new \Twig\Loader\FilesystemLoader($templatesPath);
        $twig = new \Twig\Environment($loader, [
            'cache' => false
        ]);
        $this->template = $twig->load('MagentoDto.php');
        $this->outputPath = $outputPath;
    }

    public function run(string $namespace, \Google\Protobuf\DescriptorProto $descriptor): void
    {
        $fields = [];
        /** @var \Google\Protobuf\FieldDescriptorProto $field */
        foreach ($descriptor->getField() as $field) {
            $fields[] = ['name' => ucfirst($field->getName())];
        }

        $content = $this->template->render([
            'namespace' => $namespace,
            'class' => $descriptor->getName(),
            'fields' => $fields
        ]);
        $this->writeFile($namespace, $descriptor->getName(), $content);
    }

    private function writeFile($namespace, $class, $content)
    {
        $dir = str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
        $dir = rtrim($this->outputPath, '\\/') . DIRECTORY_SEPARATOR . $dir;
        if (!is_dir($dir)) {
            mkdir($dir, 0744, true);
        }
        file_put_contents($dir . DIRECTORY_SEPARATOR . $class . '2.php', $content);
    }
}
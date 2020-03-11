<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

use Google\Protobuf\ServiceDescriptorProto;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TemplateWrapper;
use Roave\BetterReflection\Reflection\ReflectionClass;

/**
 * Generates gRPC service interfaces and classes.
 */
class ClientService
{
    use FileWriter;

    private const INTERFACE_TPL = 'serviceInterface.tpl';

    private const SERVICE_TPL = 'clientService.tpl';

    /**
     * @var TemplateWrapper
     */
    private $interfaceTemplate;

    /**
     * @var TemplateWrapper
     */
    private $serviceTemplate;

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
            'cache' => false,
        ]);
        $this->interfaceTemplate = $twig->load(self::INTERFACE_TPL);
        $this->serviceTemplate = $twig->load(self::SERVICE_TPL);
        $this->outputPath = $outputPath;
    }

    /**
     * Generates gRPC service interfaces and classes.
     *
     * @param string $namespace
     * @param ServiceDescriptorProto $descriptorProto
     * @return array
     */
    public function run(string $namespace, ServiceDescriptorProto $descriptorProto): array
    {
        $dtoNamespace = str_replace('Proto', 'Data', $namespace);
        $serviceNamespace = str_replace('\Proto', '', $namespace);
        $protoServiceClass = $namespace . '\\'. $descriptorProto->getName() . 'Client';
        $reflectionClass = ReflectionClass::createFromName($protoServiceClass);

        $methods = [];
        /** @var \Google\Protobuf\MethodDescriptorProto $method */
        foreach ($descriptorProto->getMethod() as $method) {
            $paramChunks = explode('.', $method->getInputType());
            $param = end($paramChunks);
            $returnChunks = explode('.', $method->getOutputType());
            $return = end($returnChunks);
            $reflectionMethod = $reflectionClass->getMethod($method->getName());
            $reflectionParam = $reflectionMethod->getParameter('argument');
            $methods[] = [
                'name' => $method->getName(),
                'input' => [
                    'interface' => '\\'. $dtoNamespace . '\\' . $param . 'Interface',
                    'methods' => $this->getListOfClassProps('\\' . $dtoNamespace . '\\' . $param . 'Interface'),
                ],
                'output' => [
                    'class' => '\\'. $dtoNamespace . '\\' . $return,
                    'interface' => '\\'. $dtoNamespace . '\\' . $return . 'Interface',
                ],
                'proto' => [
                    'input' => '\\' . (string) $reflectionParam->getType(),
                    'output' => '\\' . $namespace . '\\' . $return
                ],
            ];
        }

        $interfaceName = $descriptorProto->getName() . 'Interface';
        $content = $this->interfaceTemplate->render([
            'namespace' => $serviceNamespace,
            'name' => $interfaceName,
            'methods' => $methods
        ]);

        $path = $this->convertToDirName($serviceNamespace);
        $this->writeFile($content, $path, $interfaceName . '.php');

        $content = $this->serviceTemplate->render([
            'namespace' => $serviceNamespace,
            'interface' => $interfaceName,
            'name' => $descriptorProto->getName(),
            'methods' => $methods,
            'proto' => [
                'class' => '\\' . $protoServiceClass,
            ],
        ]);
        $this->writeFile($content, $path, $descriptorProto->getName() . '.php');

        return [
            'interface' => $serviceNamespace . '\\' . $interfaceName,
            'class' => $serviceNamespace . '\\' . $descriptorProto->getName()
        ];
    }

    /**
     * Get list of class properties used for getters and setters generation.
     *
     * @param string $fqcn
     * @return array
     */
    private function getListOfClassProps(string $fqcn): array
    {
        $result = [];
        $reflection = ReflectionClass::createFromName($fqcn);
        $methods = $reflection->getImmediateMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if (strpos($method->getName(), 'get', 0) !== false) {
                $result[] = ['name' => substr($method->getName(), 3)];
            }
        }

        return $result;
    }
}

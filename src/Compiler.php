<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen;

use Google\Protobuf\Compiler\CodeGeneratorRequest;
use Magento\ProtoGen\Generator\Di;
use Magento\ProtoGen\Generator\ClientService;
use Magento\ProtoGen\Generator\NamespaceConverter;
use Magento\ProtoGen\Generator\Skeleton;

/**
 * Generates Magento module files.
 */
class Compiler
{
    use NamespaceConverter;

    /**
     * @var Generator\Dto
     */
    private $dtoGenerator;

    /**
     * @var Di
     */
    private $diGenerator;

    /**
     * @var Skeleton
     */
    private $skeletonGenerator;

    /**
     * @var ClientService
     */
    private $clientServiceGenerator;

    /**
     * @param string $templatesPath
     * @param string $outputPath
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __construct(string $templatesPath, string $outputPath)
    {
        $this->dtoGenerator = new Generator\Dto($templatesPath, $outputPath);
        $this->diGenerator = new Di($templatesPath, $outputPath);
        $this->skeletonGenerator = new Skeleton($templatesPath, $outputPath);
        $this->clientServiceGenerator = new ClientService($templatesPath, $outputPath);
    }

    /**
     * Generate module files.
     *
     * @param $rawRequest
     * @throws \Exception
     */
    public function run($rawRequest): void
    {
        $preferences = [];
        $request = new CodeGeneratorRequest();
        $request->mergeFromString($rawRequest);

        /** @var \Google\Protobuf\FileDescriptorProto $proto */
        foreach ($request->getProtoFile() as $proto) {
            $namespace = $this->convertProtoNameToFqcn($proto->getPackage());

            /** @var \Google\Protobuf\DescriptorProto $descriptor */
            foreach ($proto->getMessageType() as $descriptor) {
                $preferences[] = $this->dtoGenerator->run($namespace, $descriptor);
            }

            /** @var \Google\Protobuf\ServiceDescriptorProto $service */
            foreach ($proto->getService() as $service) {
                $this->clientServiceGenerator->run($namespace, $service, $proto);
            }
        }

        $namespaceChunk = explode('\\', $namespace);
        [$vendor, $module] = [$namespaceChunk[0], $namespaceChunk[1]];
        $this->skeletonGenerator->run($vendor, $module);

        // all proto files will be part of the same module
        $path = implode('/', [$vendor, $module]);
        $this->diGenerator->run($preferences, $path);
    }
}
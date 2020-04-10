<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen;

use Google\Protobuf\Compiler\CodeGeneratorRequest;
use Google\Protobuf\Compiler\CodeGeneratorResponse;
use Magento\ProtoGen\Generator\Di;
use Magento\ProtoGen\Generator\Metadata;
use Magento\ProtoGen\Generator\Service as ServiceGenerator;
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
     * @var ServiceGenerator
     */
    private $clientServiceGenerator;

    /**
     * @var Metadata
     */
    private $metadataGenerator;

    /**
     * @param string $templatesPath
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function __construct(string $templatesPath)
    {
        $this->dtoGenerator = new Generator\Dto($templatesPath);
        $this->diGenerator = new Di($templatesPath);
        $this->skeletonGenerator = new Skeleton($templatesPath);
        $this->clientServiceGenerator = new ServiceGenerator($templatesPath);
        $this->metadataGenerator = new Metadata($templatesPath);
    }

    /**
     * Generates module files.
     *
     * @param $rawRequest
     * @throws \Exception
     */
    public function run($rawRequest): void
    {
        $preferences = [];
        $request = new CodeGeneratorRequest();
        $request->mergeFromString($rawRequest);
        $protoAggregate = [];
        $files = [];

        /** @var \Google\Protobuf\FileDescriptorProto $proto */
        foreach ($request->getProtoFile() as $proto) {
            $protoAggregate[] = $proto;
            $namespace = $this->convertProtoNameToFqcn($proto->getPackage());

            /** @var \Google\Protobuf\DescriptorProto $descriptor */
            foreach ($proto->getMessageType() as $descriptor) {
                $result = $this->dtoGenerator->run($namespace, $descriptor);
                foreach ($result['files'] as $file) {
                    $files[] = $file;
                }
                $preferences[] = $result['preferences'];
            }

            /** @var \Google\Protobuf\ServiceDescriptorProto $service */
            foreach ($proto->getService() as $service) {
                foreach ($this->clientServiceGenerator->run($namespace, $service, $protoAggregate) as $file) {
                    $files[] = $file;
                }
            }
        }

        $namespaceChunk = explode('\\', $namespace);
        [$vendor, $module] = [$namespaceChunk[0], $namespaceChunk[1]];
        foreach ($this->skeletonGenerator->run($vendor, $module) as $file) {
            $files[] = $file;
        }

        $files[] = $this->metadataGenerator->run($namespace);

        // all proto files will be part of the same module
        $path = implode('/', [$vendor, $module]);
        $files[] = $this->diGenerator->run($preferences, $path);

        $response = new CodeGeneratorResponse();
        $response->setFile($files);
        // output all generated files to STDOUT, so protoc can write them by `magento_out` path
        echo $response->serializeToString();
    }
}

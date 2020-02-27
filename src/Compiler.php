<?php
declare(strict_types=1);

namespace Magento\ProtoGen;

use Google\Protobuf\Compiler\CodeGeneratorRequest;

class Compiler {
    private $dtoGenerator;

    public function __construct(string $templatesPath,  string $outputPath)
    {
        $this->dtoGenerator = new Generator\Dto($templatesPath, $outputPath);
    }

    public function run($rawRequest): void
    {
        $request = new CodeGeneratorRequest();

        $request->mergeFromString($rawRequest);
        /** @var \Google\Protobuf\FileDescriptorProto $proto */
        foreach($request->getProtoFile() as $proto) {
            $namespaceChunk = explode('.', $proto->getPackage());
            $namespaceChunk = array_map('ucfirst', $namespaceChunk);
            $namespace = implode('\\', $namespaceChunk);

            /** @var \Google\Protobuf\DescriptorProto $descriptor */
            foreach($proto->getMessageType() as $descriptor) {
                $this->dtoGenerator->run($namespace, $descriptor);
            }
            /** @var \Google\Protobuf\ServiceDescriptorProto $service */
            foreach ($proto->getService() as $service) {
                //var_dump($service->getName());
            }
        }
    }
}
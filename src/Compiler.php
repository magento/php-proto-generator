<?php
declare(strict_types=1);

namespace Magento\ProtoGen;

use Google\Protobuf\Compiler\CodeGeneratorRequest;

class Compiler {
    private $twig = null;

    public function __construct()
    {
        $loader = new \Twig\Loader\FilesystemLoader('templates');
        $this->twig = new \Twig\Environment($loader, [
            'cache' => false
        ]);
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
                $dtoTemplate = $this->twig->load('MagentoDto.php');
                $fields = [];
                /** @var \Google\Protobuf\FieldDescriptorProto $field */
                foreach ($descriptor->getField() as $field) {
                    $fields[] = ['name' => ucfirst($field->getName())];
                }

                $content = $dtoTemplate->render([
                    'namespace' => $namespace,
                    'class' => $descriptor->getName(),
                    'fields' => $fields
                ]);
                var_dump($content);
            }
            /** @var \Google\Protobuf\ServiceDescriptorProto $service */
            foreach ($proto->getService() as $service) {
                //var_dump($service->getName());
            }
        }
    }
}
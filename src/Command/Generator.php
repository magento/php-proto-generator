<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates Magento packages for provided proto definitions.
 */
class Generator extends Command
{
    protected static $defaultName = 'packages:generate';

    protected function configure()
    {
        $this->setDescription("Generates Magento packages")
            ->addArgument('proto-dir', InputArgument::REQUIRED, 'Directory with proto files')
            ->addArgument('output-dir', InputArgument::REQUIRED, 'Output directory')
            ->addOption('composer_version', 'c', InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $protoDir = $input->getArgument('proto-dir');
        $outputDir = $input->getArgument('output-dir');
        $version = $input->getOption('composer_version');

        if (!is_dir($protoDir)) {
            throw new \InvalidArgumentException('Provided proto directory doesn\'t exist');
        }

        if (!is_writable($outputDir)) {
            throw new \InvalidArgumentException('Output directory is not writable');
        }
        $protos = [];
        foreach (new \DirectoryIterator($protoDir) as $fileInfo) {
            if ($fileInfo->isFile() && $fileInfo->getExtension() == 'proto') {
                $protos[] = $fileInfo->getRealPath();
            }
        }

        $protos = implode(' ', $protos);

        $command = 'protoc'
            . ' --php_out=' . $outputDir
            . ' --php-grpc_out=' . $outputDir
            . ' --grpc_out=' . $outputDir
            . ' --magento_out=';
        if (!empty($version)) {
            $command .= 'version=' . escapeshellarg($version) . ':';
        }

        $command .= $outputDir
            . ' --plugin=protoc-gen-php-grpc=/usr/local/bin/protoc-gen-php-grpc'
            . ' --plugin=protoc-gen-grpc=/usr/local/bin/grpc_php_plugin'
            . ' --plugin=protoc-gen-magento=protoc-gen-magento'
            . ' -I ' . $protoDir
            . ' ' . $protos;

        exec($command, $out, $code);

        if ($code != 0) {
            throw new \RuntimeException(
                'Compilation failed with the following message:'
                . PHP_EOL . implode(PHP_EOL, $out)
            );
        }

        $command = 'vendor/bin/php-cs-fixer fix ' . $outputDir;
        exec($command, $out, $code);

        return $code;
    }
}
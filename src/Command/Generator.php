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
        $protoList = [];
        $dirIterator = new \RecursiveDirectoryIterator($protoDir);
        $iterator = new \RecursiveIteratorIterator($dirIterator);
        $regex = new \RegexIterator($iterator, '/^.+\.proto$/i', \RecursiveRegexIterator::GET_MATCH);
        foreach ($regex as $it) {
            $protoList[] = $it[0];
        }

        if (empty($protoList)) {
            throw new \InvalidArgumentException('No protobuf contracts found in "' . $protoDir . '"');
        }

        $protoList = implode(' ', $protoList);

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
            . ' ' . $protoList;

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
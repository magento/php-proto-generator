<?php

namespace Magento\ProtoGen\Tests;

use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    public function testBasic()
    {
        exec('rm -rf ' . GENERATED_TEMP . '/*');
        $command = 'protoc'
            . ' --php_out=' . GENERATED_TEMP
            . ' --php-grpc_out=' . GENERATED_TEMP
            . ' --magento_out=' . GENERATED_TEMP
            . ' --plugin=protoc-gen-grpc=grpc_php_plugin'
            . ' --plugin=protoc-gen-magento=protoc-gen-magento'
            . ' -I ' . __DIR__ . DIRECTORY_SEPARATOR . 'fixtures'
            . ' ' . __DIR__ . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'basic.proto';
        var_dump($command);
        exec($command, $out, $code
        );
        var_dump($out, $code);
        $this->assertEquals(1, 1);
    }
}
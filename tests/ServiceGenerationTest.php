<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Test;

use Magento\Grpc\Api\Data\GreetingRequest;
use Magento\Grpc\Api\Data\GreetingRequestInterface;
use Magento\Grpc\Api\Data\GreetingResponseInterface;
use Magento\Grpc\Api\GreetingServiceInterface;
use Magento\Grpc\Api\GreetingServiceProxyServer;
use Magento\Grpc\Api\GreetingServiceServerInterface;
use Magento\Grpc\Api\InMemoryGreetingService;
use Magento\Grpc\Proto\GreetingRequest as ProtoGreetingRequest;
use Magento\Grpc\Proto\GreetingResponse as ProtoGreetingResponse;
use Magento\Grpc\Proto\GreetingServiceInterface as ProtoGreetingServiceInterface;
use PHPUnit\Framework\TestCase;
use Spiral\GRPC\ContextInterface as GrpcContextInterface;

class ServiceGenerationTest extends TestCase
{
    use StringFormatter;

    private const OUTPUT_PATH = GENERATED . '/Magento/Grpc/';

    public static function setUpBeforeClass(): void
    {
        exec('rm -rf ' . GENERATED . '/*');
    }

    public static function tearDownAfterClass(): void
    {
        exec('rm -rf ' . GENERATED . '/*');
    }

    /**
     * Runs proto-based package generation.
     */
    public function testGeneration(): void
    {
        $binary = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'generator packages:generate';
        $in = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures';
        $out = GENERATED;
        $command = "$binary $in $out";

        exec($command, $out, $code);

        self::assertEquals(0, $code);
    }

    /**
     * Checks if composer.json is generated correctly.
     *
     * @depends testGeneration
     */
    public function testComposer(): void
    {
        $path = self::OUTPUT_PATH . 'composer.json';
        $content = file_get_contents($path);
        self::assertFileIsReadable($path);
        self::assertStringContainsString('magento/module-grpc', $content);
        self::assertStringContainsString('Magento\\\\Grpc\\\\', $content);
    }

    /**
     * Checks if gRPC services generated correctly.
     *
     * @depends testGeneration
     */
    public function testGrpcServiceInterface(): void
    {
        $path = self::OUTPUT_PATH . 'Api/GreetingServiceInterface.php';
        self::assertFileIsReadable($path);
        $class = new \ReflectionClass(GreetingServiceInterface::class);

        $method = $class->getMethod('greet');
        self::assertInstanceOf(\ReflectionMethod::class, $method);

        self::assertFileIsReadable(self::OUTPUT_PATH . 'Api/Data/GreetingRequestInterface.php');
        $inputParam = $method->getParameters()[0];
        self::assertEquals(GreetingRequestInterface::class, $inputParam->getType()->getName());

        self::assertFileIsReadable(self::OUTPUT_PATH . 'Api/Data/GreetingResponseInterface.php');
        $returnType = $method->getReturnType();
        self::assertEquals(GreetingResponseInterface::class, $returnType->getName());
    }

    /**
     * @depends testGeneration
     */
    public function testServerInterfaceGeneration(): void
    {
        $path = self::OUTPUT_PATH . 'Api/GreetingServiceServerInterface.php';
        self::assertFileIsReadable($path);
        $class = new \ReflectionClass(GreetingServiceServerInterface::class);

        $method = $class->getMethod('greet');
        self::assertInstanceOf(\ReflectionMethod::class, $method);

        self::assertEquals(
            GreetingRequestInterface::class,
            $method->getParameters()[0]->getType()->getName()
        );
        self::assertEquals(
            GreetingResponseInterface::class,
            $method->getReturnType()->getName()
        );
    }

    /**
     * @depends testGeneration
     */
    public function testInMemoryServiceGeneration(): void
    {
        $path = self::OUTPUT_PATH . 'Api/InMemoryGreetingService.php';
        self::assertFileIsReadable($path);
        $class = new \ReflectionClass(InMemoryGreetingService::class);

        self::assertArrayHasKey(
            GreetingServiceInterface::class,
            array_flip($class->getInterfaceNames())
        );

        $method = $class->getMethod('greet');
        self::assertInstanceOf(\ReflectionMethod::class, $method);

        self::assertEquals(
            GreetingRequestInterface::class,
            $method->getParameters()[0]->getType()->getName()
        );
        self::assertEquals(
            GreetingResponseInterface::class,
            $method->getReturnType()->getName()
        );
    }

    /**
     * @depends testGeneration
     */
    public function testProxyServerGeneration(): void
    {
        $path = self::OUTPUT_PATH . 'Api/GreetingServiceProxyServer.php';
        self::assertFileIsReadable($path);
        $class = new \ReflectionClass(GreetingServiceProxyServer::class);

        self::assertArrayHasKey(
            ProtoGreetingServiceInterface::class,
            array_flip($class->getInterfaceNames())
        );

        $method = $class->getMethod('greet');
        self::assertInstanceOf(\ReflectionMethod::class, $method);

        self::assertEquals(GrpcContextInterface::class, $method->getParameters()[0]->getType()->getName());
        self::assertEquals(ProtoGreetingRequest::class, $method->getParameters()[1]->getType()->getName());
        self::assertEquals(ProtoGreetingResponse::class, $method->getReturnType()->getName());
    }

    /**
     * Checks if generated DTOs methods have correct arguments and return types.
     *
     * @depends testGrpcServiceInterface
     */
    public function testDtoMethods(): void
    {
        $class = new \ReflectionClass(GreetingRequestInterface::class);
        $getFieldsMethod = $class->getMethod('getFields');
        self::assertEquals('array', $getFieldsMethod->getReturnType()->getName());

        $docBlock = $getFieldsMethod->getDocComment();
        self::assertStringContainsString('@return \Magento\Grpc\Api\Data\RepeatedFieldsInterface[]', $docBlock);
    }

    /**
     * Checks a case when DTO method returns scalar array.
     *
     * @depends testGrpcServiceInterface
     */
    public function testScalarArrays(): void
    {
        $class = new \ReflectionClass(GreetingRequestInterface::class);
        $getFieldsMethod = $class->getMethod('getValues');
        self::assertEquals('array', $getFieldsMethod->getReturnType()->getName());

        $docBlock = $getFieldsMethod->getDocComment();
        self::assertStringContainsString('@return string[]', $docBlock);
    }

    /**
     * Checks that generated DTOs are final classes.
     *
     * @depends testGeneration
     */
    public function testDtoIsNotExtendable(): void
    {
        $path = self::OUTPUT_PATH . 'Api/Data/GreetingRequest.php';
        self::assertFileIsReadable($path);

        $class = new \ReflectionClass(GreetingRequest::class);
        self::assertTrue($class->isFinal());
    }
}

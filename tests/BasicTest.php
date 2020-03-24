<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Tests;

use PHPUnit\Framework\TestCase;

class BasicTest extends TestCase
{
    protected function setUp(): void
    {
        exec(
            'rm -rf ' . dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR  . 'tmp'
            . '/*'
        );
    }

    public function testBasic()
    {
        $binary = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'generator packages:generate';
        $in = __DIR__ . DIRECTORY_SEPARATOR . 'fixtures';
        $out = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR  .'tmp';
        $command = "$binary $in $out";

        exec($command, $out, $code);

        self::assertEquals(0, $code);
    }

    protected function tearDown(): void
    {
        exec(
            'rm -rf ' . dirname(__DIR__) . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR  . 'tmp'
            . '/*'
        );
    }
}
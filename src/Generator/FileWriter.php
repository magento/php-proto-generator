<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

use Google\Protobuf\Compiler\CodeGeneratorResponse\File;

/**
 * Helper trait to work with file system.
 */
trait FileWriter
{
    /**
     * Converts namespace to directory name format.
     *
     * @param $namespace
     * @return string
     */
    private function convertToDirName(string $namespace): string
    {
        return str_replace('\\', DIRECTORY_SEPARATOR, $namespace);
    }

    /**
     * Creates output file.
     *
     * @param string $path
     * @param string $content
     * @return File
     */
    private function createFile(string $path, string $content): File
    {
        $file = new File();
        $file->setName($path);
        $file->setContent($content);
        return $file;
    }
}

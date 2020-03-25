<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

/**
 * Helper trait to work with file system.
 */
trait FileWriter
{
    /**
     * @var string
     */
    private $outputPath;

    /**
     * Writes a file. Creates a directory if it does not exist.
     *
     * @param string $content
     * @param string $path
     * @param string $filename
     */
    private function writeFile(string $content, string $path, string $filename): void
    {
        $dir = $this->outputPath . '/' . $path;
        if (!is_dir($dir)) {
            mkdir($dir, 0744, true);
        }
        file_put_contents($dir . DIRECTORY_SEPARATOR . $filename, $content);
    }

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
}

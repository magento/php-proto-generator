<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Test;

/**
 * Contains methods to format strings.
 */
trait StringFormatter
{
    /**
     * Removes all space symbols from string.
     *
     * @param string $input
     * @return string
     */
    public function removeSpaces(string $input): string
    {
        $pattern = '/\s+/';
        return preg_replace($pattern, '', $input);
    }
}

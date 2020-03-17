<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

trait NamespaceConverter
{
    private static $protoNamespace = 'Proto';

    public function fromProto(string $namespace, string $replace): string
    {
        return str_replace(self::$protoNamespace, $replace, $namespace);
    }

    public function toProto(string $namespace, string $old): string
    {
        return str_replace($old, self::$protoNamespace, $namespace);
    }
}

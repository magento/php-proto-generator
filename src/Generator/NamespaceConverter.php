<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

/**
 * Helper trait to convert namespaces.
 */
trait NamespaceConverter
{
    private static $protoNamespace = 'Proto';

    /**
     * Converts Protobuf namespace to PHP FQCN.
     *
     * @param string $namespace
     * @param string $replace
     * @return string
     */
    public function fromProto(string $namespace, string $replace): string
    {
        return str_replace(self::$protoNamespace, $replace, $namespace);
    }

    /**
     * Converts Magento namespace to Protobuf namespace.
     *
     * @param string $namespace
     * @param string $old
     * @return string
     */
    public function toProto(string $namespace, string $old): string
    {
        return str_replace($old, self::$protoNamespace, $namespace);
    }

    /**
     * Converts proto name to PHP FQCN.
     *
     * @param string $name
     * @return string
     */
    public function convertProtoNameToFqcn(string $name): string
    {
        $namespaceChunk = explode('.', $name);
        $namespaceChunk = array_map('ucfirst', $namespaceChunk);
        return implode('\\', $namespaceChunk);
    }
}

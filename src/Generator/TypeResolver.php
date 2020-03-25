<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

use Google\Protobuf\FieldDescriptorProto;
use Google\Protobuf\FieldDescriptorProto\Type;

/**
 * Helper which contains methods to resolve proto types to PHP native.
 */
trait TypeResolver
{
    private static $typeMap = [
        Type::TYPE_DOUBLE => 'double',
        Type::TYPE_FLOAT => 'float',
        Type::TYPE_INT64 => 'int',
        Type::TYPE_UINT64 => 'int',
        Type::TYPE_INT32 => 'int',
        Type::TYPE_BOOL => 'bool',
        Type::TYPE_STRING => 'string',
        Type::TYPE_MESSAGE => Type::TYPE_MESSAGE,
        Type::TYPE_UINT32 => 'int',
        Type::TYPE_SINT32 => 'int',
        Type::TYPE_SINT64 => 'int',
    ];

    /**
     * Detects PHP type based on proto type.
     *
     * @param FieldDescriptorProto $field
     * @return string
     */
    public function getType(FieldDescriptorProto $field): string
    {
        $type = self::$typeMap[$field->getType()] ?? null;
        if ($type === null) {
            throw new \InvalidArgumentException('{' . $field->getName() . ':' . $field->getType() . '} is not supported');
        }

        return (string)$type;
    }
}

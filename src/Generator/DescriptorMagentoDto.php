<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\ProtoGen\Generator;

use Google\Protobuf\DescriptorProto;
use Google\Protobuf\FieldDescriptorProto\Label;
use Google\Protobuf\FieldDescriptorProto\Type;

/**
 * Describes the data needed to construct DTO
 */
class DescriptorMagentoDto {

    use NamespaceConverter;

    use TypeResolver;

    /**
     * Based on the Proto Descriptor create array representing the data needed to create for the Magento DTO and Mappers
     *
     * @param string $namespace
     * @param DescriptorProto $descriptor
     * @return array
     */
    public function describe(string $namespace, DescriptorProto $descriptor) {
        $dtoNamespace = $this->fromProto($namespace, 'Api\\Data');
        /** @var \Google\Protobuf\FieldDescriptorProto $field */
        foreach ($descriptor->getField() as $field) {
            /**
             * Name is used in to construct getters and setters name. Thus we convert proto fields names
             * from snake case to the camel case to be used as a part of getter/setter name
             */
            $name = str_replace('_', '', ucwords($field->getName(), '_'));

            /**
             * Field name is the name of the field from the proto message without conversion. The standard for the
             * proto is snake case. This is what would be used as internal field name representation and for the REST
             * serialization
             */
            $fieldName = $field->getName();

            /**
             * Element Type is either type of the field for the scalar and Object types or type of the element of the
             * array for the Array type.
             *
             * Type is the type of the field for the scalar and Object types or "array" for the Array type
             *
             * DocType is the type of the field for the scalar and Object types or type with "[]" suffix for the array
             */
            $elementType = $type = $docType = $this->getType($field);

            /**
             * Simple field type indicates that it would be type casted to the type of the field
             */
            $isSimple = true;

            /**
             * Object field indicates that it is not scalar type. In case of the Object, mapper would be used for
             * serialization of the field data, in case of Array, mapper would be called on the elements of the array
             */
            $isObject = false;

            // check if a getter method parameter is a simple type
            if ((int) $type === Type::TYPE_MESSAGE) {
                $elementType = $type = $docType = $this->fromProto(
                        $this->convertProtoNameToFqcn($field->getTypeName()),
                        'Api\\Data')
                    . 'Interface';
                $isSimple = false;
                $isObject = true;
            }
            // check if message is repeated
            if ($field->getLabel() === Label::LABEL_REPEATED) {
                $docType .= '[]';
                $type = 'array';
                $isSimple = true;
            }
            $fields[] = [
                'name' => $name,
                'fieldName' => $fieldName,
                'type' => $type,
                'elementType' => $elementType,
                'simple' => $isSimple,
                'is_object' => $isObject,
                'propertyName' => lcfirst($name),
                'doc' => [
                    'input' => $docType,
                    'output' => $docType
                ]
            ];
        }

        return [
            'namespace' => $dtoNamespace,
            'class' => $descriptor->getName(),
            'fields' => $fields
        ];
    }
}

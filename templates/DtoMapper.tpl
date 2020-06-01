<?php
# Generated by the Magento PHP proto generator.  DO NOT EDIT!

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace {{ namespace }};

use Magento\Framework\ObjectManagerInterface;

final class {{ class }}Mapper
{
    /**
     * @var string
     */
    private static $dtoClassName = {{ class }}::class;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager) {
        $this->objectManager = $objectManager;
    }

    /**
    * Set the data to populate the DTO
    *
    * @param mixed $data
    * @return $this
    */
    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    /**
    * Build new DTO populated with the data
    *
    * @return {{class}}
    */
    public function build() {
        $dto = $this->objectManager->create(self::$dtoClassName);
        foreach ($this->data as $key => $valueData) {
            $this->setByKey($dto, $key, $valueData);
        }
        return $dto;
    }

    /**
    * Set the value of the key using setters.
    *
    * In case if the field is object, the corresponding Mapper would be create and DTO representing the field data
    * would be built
    *
    * @param {{class}} $dto
    * @param string $key
    * @param mixed $value
    */
    private function setByKey({{class}} $dto, string $key, $value): void
    {
        switch ($key) {
{% for field in fields %}
{% if field.simple %}
            case "{{ field.fieldName }}":
                $dto->set{{ field.name }}(({{ field.type }}) $value);
                break;
{% else %}
{% if field.type == "array" %}
            case "{{ field.fieldName }}":
                $convertedArray = [];
                foreach ($value as $element) {
                    $convertedArray[] = $this->objectManager
                        ->get({{ field.elementType }}::class)
                        ->setData($element)
                        ->build();
                }
                $dto->set{{ field.name }}($convertedArray);
                break;
{% else %}
            case "{{ field.fieldName }}":
                $dto->set{{ field.name }}(
                   $this->objectManager
                       ->get({{ field.type }}::class)
                       ->setData($value)
                       ->build());
                break;
{% endif %}
{% endif %}
{% endfor %}
        }
    }
}
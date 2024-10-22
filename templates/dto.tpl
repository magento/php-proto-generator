<?php
# Generated by the Magento PHP proto generator.  DO NOT EDIT!

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace {{ namespace }};

/**
 * Autogenerated description for {{ class }} class
 *
 * phpcs:disable Magento2.PHP.FinalImplementation
 * @SuppressWarnings(PHPMD)
 * @SuppressWarnings(PHPCPD)
 */
final class {{ class }} implements {{ class }}Interface
{
{% for field in fields %}

    /**
     * @var {{ field.type }}
     */
    private ${{ field.propertyName }};
{% endfor %}
{% for field in fields %}
    {% if field.simple %}

    /**
     * @inheritdoc
     *
     * @return {{ field.doc.output }}
     */
    public function get{{ field.name }}(): {{ field.type }}
    {
         return ({{ field.type }}) $this->{{ field.propertyName }};
    }
    {% else %}

    /**
     * @inheritdoc
     *
     * @return {{ field.doc.output }}|null
     */
    public function get{{ field.name }}(): ?{{ field.type }}
    {
        return $this->{{ field.propertyName }};
    }
    {% endif %}

    /**
     * @inheritdoc
     *
     * @param {{ field.doc.input }} $value
     * @return void
     */
    public function set{{ field.name }}({{ field.type }} $value): void
    {
        $this->{{ field.propertyName }} = $value;
    }
{% endfor %}
}

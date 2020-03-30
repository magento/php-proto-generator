<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace {{ namespace }};

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
     * @return {{ field.doc.output }}
     */
    public function get{{ field.name }}(): {{ field.type }}
    {
         return ({{ field.type }}) $this->{{ field.propertyName }};
    }
    {% else %}
    /**
     * @return {{ field.doc.output }}|null
     */
    public function get{{ field.name }}(): ?{{ field.type }}
    {
        return $this->{{ field.propertyName }};
    }
    {% endif %}

    /**
     * @param {{ field.doc.input }} $value
     * @return void
     */
    public function set{{ field.name }}({{ field.type }} $value): void
    {
        $this->{{ field.propertyName }} = $value;
    }
{% endfor %}
}

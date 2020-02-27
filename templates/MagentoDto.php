<?php
declare(strict_types=1);
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace {{ namespace }};

class {{ class }}
{
{% for field in fields %}
    private ${{ field.name|lower }};
{% endfor %}

{% for field in fields %}
    public function get{{ field.name }}()
    {
        return $this->{{ field.name|lower }};
    }

    public function set{{ field.name }}($value)
    {
        $this->{{ field.name|lower }} = $value;
    }
{% endfor %}
}

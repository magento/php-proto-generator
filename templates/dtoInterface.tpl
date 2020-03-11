<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace {{ namespace }};

interface {{ class }}Interface
{
{% for field in fields %}

    public function get{{ field.name }}(): {{ field.type }};

    public function set{{ field.name }}({{ field.type }} $value): void;
{% endfor %}
}

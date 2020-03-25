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

    /**
     * @return {{ field.doc.output }}
     */
    public function get{{ field.name }}(): {{ field.type }};

    /**
     * @param {{ field.doc.input }} $value
     * @return void
     */
    public function set{{ field.name }}({{ field.type }} $value): void;
{% endfor %}
}

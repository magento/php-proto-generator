<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace {{ namespace }};

interface {{ name }}
{
{% for method in methods %}

    public function {{ method.name }}({{ method.input.interface }} $request): {{ method.output.interface }};
{% endfor %}
}

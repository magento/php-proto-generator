<?php
# Generated by the Magento PHP proto generator.  DO NOT EDIT!

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace {{ namespace }};

class {{ name }} implements {{ interface }}
{
    private $service;

    public function __construct(
        {{ serverInterface }} $service
    ) {
        $this->service = $service;
    }
{% for method in methods %}

    public function {{ method.name }}({{ method.input.interface }} $request): {{ method.output.interface }}
    {
        return $this->service->{{ method.name }}($request);
    }
{% endfor %}
}
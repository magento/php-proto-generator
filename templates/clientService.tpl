<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace {{ namespace }};

class {{ name }} implements {{ interface }}
{
    private $protoClient;

    public function __construct(
        string $hostname,
        array $options,
        ?string $channel = null
    ) {
        $this->protoClient = new {{ proto.class }}($hostname, $options, $channel);
    }
{% for method in methods %}

    public function {{ method.name }}({{ method.input.interface }} $request): {{ method.output.interface }}
    {
        $protoRequest = $this->{{ method.name }}ToProto($request);
        [$protoResult, $status] = $this->protoClient->{{ method.name }}($protoRequest)->wait();
        if ($status->code !== 0) {
            throw new \RuntimeException($status->details, $status->code);
        }
        $result = $this->{{ method.name }}FromProto($protoResult);
        return $result;
    }

    private function {{ method.name }}ToProto({{ method.input.interface }} $value): {{ method.proto.input}}
    {
        {{ method.input.toProtoContent|raw }}

        return $proto;
    }

    private function {{ method.name }}FromProto({{ method.proto.output }} $value): {{ method.output.interface }}
    {
        {{ method.output.fromProtoContent|raw }}

        return $out;
    }
{% endfor %}
}

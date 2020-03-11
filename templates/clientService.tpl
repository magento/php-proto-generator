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
        // @TODO add error handling
        [$protoResult, $status] = $this->protoClient->{{ method.name }}($protoRequest);
        $result = $this->{{ method.name }}FromProto($protoResult);
        return $result;
    }

    private function {{ method.name }}ToProto({{ method.input.interface }} $value): {{ method.proto.input}}
    {
        // @TODO does not work for complex objects
        $proto = new {{ method.proto.input }}();
{% for getter in method.input.methods %}
        $proto->set{{ getter.name }}($value->get{{ getter.name }}());
{% endfor %}

        return $proto;
    }

    private function {{ method.name }}FromProto({{ method.proto.output }} $value): {{ method.output.interface }}
    {
        $result = new {{ method.output.class }}();

        return $result;
    }
{% endfor %}
}

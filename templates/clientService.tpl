<?php
# Generated by the Magento PHP proto generator.  DO NOT EDIT!

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace {{ namespace }};

use {{ proto.class|split('\\', 2)[1] }};
{% for method in methods %}
use {{ method.input.interface|split('\\', 2)[1] }};
use {{ method.output.interface|split('\\', 2)[1] }};
use {{ method.proto.input|split('\\', 2)[1] }};
use {{ method.proto.output|split('\\', 2)[1] }};
{% endfor %}
{% set protoClassName = proto.class|split('\\')|last %}

/**
 * Autogenerated description for {{ name }} class
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class {{ name }} implements {{ interface }}
{
    /**
     * @var {{ protoClassName }}
     */
    private $protoClient;

    /**
     * @param string $hostname
     * @param array $options
     * @param string|null $channel
     */
    public function __construct(
        string $hostname,
        array $options,
        ?string $channel = null
    ) {
        $this->protoClient = new {{ protoClassName }}($hostname, $options, $channel);
}
{% for method in methods %}
    {% set methodInputInterfaceName = method.input.interface|split('\\')|last %}
    {% set methodOutputInterfaceName = method.output.interface|split('\\')|last %}
    {% set methodProtoInputName = method.proto.input|split('\\')|last %}
    {% set methodProtoOutputName = method.proto.output|split('\\')|last %}

    /**
     * @inheritdoc
     *
     * @param {{ methodInputInterfaceName }} $request
     * @return {{ methodOutputInterfaceName }}
     * @throws \Throwable
     */
    public function {{ method.name }}({{ methodInputInterfaceName }} $request): {{ methodOutputInterfaceName }}
    {
    $protoRequest = $this->{{ method.name }}ToProto($request);
    [$protoResult, $status] = $this->protoClient->{{ method.name }}($protoRequest)->wait();
    if ($status->code !== 0) {
    throw new \RuntimeException($status->details, $status->code);
    }
    return $this->{{ method.name }}FromProto($protoResult);
    }

    /**
     * Autogenerated description for {{ method.name }} method
     *
     * @param {{ methodInputInterfaceName }} $value
     * @return {{ methodProtoInputName }}
     */
    private function {{ method.name }}ToProto({{ methodInputInterfaceName }} $value): {{ methodProtoInputName}}
    {
    {{ method.input.toProtoContent|raw }}

    return $proto;
    }

    /**
     * Autogenerated description for {{ method.name }} method
     *
     * @param {{ methodProtoOutputName }} $value
     * @return {{ methodProtoOutputName }}
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    private function {{ method.name }}FromProto({{ methodProtoOutputName }} $value): {{ methodOutputInterfaceName }}
    {
    {{ method.output.fromProtoContent|raw }}

    return $out;
    }
{% endfor %}
}

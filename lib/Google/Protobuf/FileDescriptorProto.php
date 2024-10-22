<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: descriptor.proto

namespace Google\Protobuf;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Describes a complete .proto file.
 *
 * Generated from protobuf message <code>google.protobuf.FileDescriptorProto</code>
 */
class FileDescriptorProto extends \Google\Protobuf\Internal\Message
{
    /**
     * file name, relative to root of source tree
     *
     * Generated from protobuf field <code>string name = 1;</code>
     */
    protected $name = '';
    /**
     * e.g. "foo", "foo.bar", etc.
     *
     * Generated from protobuf field <code>string package = 2;</code>
     */
    protected $package = '';
    /**
     * Names of files imported by this file.
     *
     * Generated from protobuf field <code>repeated string dependency = 3;</code>
     */
    private $dependency;
    /**
     * Indexes of the public imported files in the dependency list above.
     *
     * Generated from protobuf field <code>repeated int32 public_dependency = 10;</code>
     */
    private $public_dependency;
    /**
     * Indexes of the weak imported files in the dependency list.
     * For Google-internal migration only. Do not use.
     *
     * Generated from protobuf field <code>repeated int32 weak_dependency = 11;</code>
     */
    private $weak_dependency;
    /**
     * All top-level definitions in this file.
     *
     * Generated from protobuf field <code>repeated .google.protobuf.DescriptorProto message_type = 4;</code>
     */
    private $message_type;
    /**
     * Generated from protobuf field <code>repeated .google.protobuf.EnumDescriptorProto enum_type = 5;</code>
     */
    private $enum_type;
    /**
     * Generated from protobuf field <code>repeated .google.protobuf.ServiceDescriptorProto service = 6;</code>
     */
    private $service;
    /**
     * Generated from protobuf field <code>repeated .google.protobuf.FieldDescriptorProto extension = 7;</code>
     */
    private $extension;
    /**
     * Generated from protobuf field <code>.google.protobuf.FileOptions options = 8;</code>
     */
    protected $options = null;
    /**
     * This field contains information about the original source code.
     * You may safely remove this entire field without harming runtime
     * functionality of the descriptors -- the information is needed only by
     * development tools.
     *
     * Generated from protobuf field <code>.google.protobuf.SourceCodeInfo source_code_info = 9;</code>
     */
    protected $source_code_info = null;
    /**
     * The syntax of the proto file.
     * The supported values are "proto2" and "proto3".
     *
     * Generated from protobuf field <code>string syntax = 12;</code>
     */
    protected $syntax = '';

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $name
     *           file name, relative to root of source tree
     *     @type string $package
     *           e.g. "foo", "foo.bar", etc.
     *     @type string[]|\Google\Protobuf\Internal\RepeatedField $dependency
     *           Names of files imported by this file.
     *     @type int[]|\Google\Protobuf\Internal\RepeatedField $public_dependency
     *           Indexes of the public imported files in the dependency list above.
     *     @type int[]|\Google\Protobuf\Internal\RepeatedField $weak_dependency
     *           Indexes of the weak imported files in the dependency list.
     *           For Google-internal migration only. Do not use.
     *     @type \Google\Protobuf\DescriptorProto[]|\Google\Protobuf\Internal\RepeatedField $message_type
     *           All top-level definitions in this file.
     *     @type \Google\Protobuf\EnumDescriptorProto[]|\Google\Protobuf\Internal\RepeatedField $enum_type
     *     @type \Google\Protobuf\ServiceDescriptorProto[]|\Google\Protobuf\Internal\RepeatedField $service
     *     @type \Google\Protobuf\FieldDescriptorProto[]|\Google\Protobuf\Internal\RepeatedField $extension
     *     @type \Google\Protobuf\FileOptions $options
     *     @type \Google\Protobuf\SourceCodeInfo $source_code_info
     *           This field contains information about the original source code.
     *           You may safely remove this entire field without harming runtime
     *           functionality of the descriptors -- the information is needed only by
     *           development tools.
     *     @type string $syntax
     *           The syntax of the proto file.
     *           The supported values are "proto2" and "proto3".
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Descriptor::initOnce();
        parent::__construct($data);
    }

    /**
     * file name, relative to root of source tree
     *
     * Generated from protobuf field <code>string name = 1;</code>
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * file name, relative to root of source tree
     *
     * Generated from protobuf field <code>string name = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setName($var)
    {
        GPBUtil::checkString($var, True);
        $this->name = $var;

        return $this;
    }

    /**
     * e.g. "foo", "foo.bar", etc.
     *
     * Generated from protobuf field <code>string package = 2;</code>
     * @return string
     */
    public function getPackage()
    {
        return $this->package;
    }

    /**
     * e.g. "foo", "foo.bar", etc.
     *
     * Generated from protobuf field <code>string package = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setPackage($var)
    {
        GPBUtil::checkString($var, True);
        $this->package = $var;

        return $this;
    }

    /**
     * Names of files imported by this file.
     *
     * Generated from protobuf field <code>repeated string dependency = 3;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getDependency()
    {
        return $this->dependency;
    }

    /**
     * Names of files imported by this file.
     *
     * Generated from protobuf field <code>repeated string dependency = 3;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setDependency($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->dependency = $arr;

        return $this;
    }

    /**
     * Indexes of the public imported files in the dependency list above.
     *
     * Generated from protobuf field <code>repeated int32 public_dependency = 10;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getPublicDependency()
    {
        return $this->public_dependency;
    }

    /**
     * Indexes of the public imported files in the dependency list above.
     *
     * Generated from protobuf field <code>repeated int32 public_dependency = 10;</code>
     * @param int[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setPublicDependency($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::INT32);
        $this->public_dependency = $arr;

        return $this;
    }

    /**
     * Indexes of the weak imported files in the dependency list.
     * For Google-internal migration only. Do not use.
     *
     * Generated from protobuf field <code>repeated int32 weak_dependency = 11;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getWeakDependency()
    {
        return $this->weak_dependency;
    }

    /**
     * Indexes of the weak imported files in the dependency list.
     * For Google-internal migration only. Do not use.
     *
     * Generated from protobuf field <code>repeated int32 weak_dependency = 11;</code>
     * @param int[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setWeakDependency($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::INT32);
        $this->weak_dependency = $arr;

        return $this;
    }

    /**
     * All top-level definitions in this file.
     *
     * Generated from protobuf field <code>repeated .google.protobuf.DescriptorProto message_type = 4;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getMessageType()
    {
        return $this->message_type;
    }

    /**
     * All top-level definitions in this file.
     *
     * Generated from protobuf field <code>repeated .google.protobuf.DescriptorProto message_type = 4;</code>
     * @param \Google\Protobuf\DescriptorProto[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setMessageType($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Protobuf\DescriptorProto::class);
        $this->message_type = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated .google.protobuf.EnumDescriptorProto enum_type = 5;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getEnumType()
    {
        return $this->enum_type;
    }

    /**
     * Generated from protobuf field <code>repeated .google.protobuf.EnumDescriptorProto enum_type = 5;</code>
     * @param \Google\Protobuf\EnumDescriptorProto[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setEnumType($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Protobuf\EnumDescriptorProto::class);
        $this->enum_type = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated .google.protobuf.ServiceDescriptorProto service = 6;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Generated from protobuf field <code>repeated .google.protobuf.ServiceDescriptorProto service = 6;</code>
     * @param \Google\Protobuf\ServiceDescriptorProto[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setService($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Protobuf\ServiceDescriptorProto::class);
        $this->service = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated .google.protobuf.FieldDescriptorProto extension = 7;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Generated from protobuf field <code>repeated .google.protobuf.FieldDescriptorProto extension = 7;</code>
     * @param \Google\Protobuf\FieldDescriptorProto[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setExtension($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Protobuf\FieldDescriptorProto::class);
        $this->extension = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>.google.protobuf.FileOptions options = 8;</code>
     * @return \Google\Protobuf\FileOptions
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Generated from protobuf field <code>.google.protobuf.FileOptions options = 8;</code>
     * @param \Google\Protobuf\FileOptions $var
     * @return $this
     */
    public function setOptions($var)
    {
        GPBUtil::checkMessage($var, \Google\Protobuf\FileOptions::class);
        $this->options = $var;

        return $this;
    }

    /**
     * This field contains information about the original source code.
     * You may safely remove this entire field without harming runtime
     * functionality of the descriptors -- the information is needed only by
     * development tools.
     *
     * Generated from protobuf field <code>.google.protobuf.SourceCodeInfo source_code_info = 9;</code>
     * @return \Google\Protobuf\SourceCodeInfo
     */
    public function getSourceCodeInfo()
    {
        return $this->source_code_info;
    }

    /**
     * This field contains information about the original source code.
     * You may safely remove this entire field without harming runtime
     * functionality of the descriptors -- the information is needed only by
     * development tools.
     *
     * Generated from protobuf field <code>.google.protobuf.SourceCodeInfo source_code_info = 9;</code>
     * @param \Google\Protobuf\SourceCodeInfo $var
     * @return $this
     */
    public function setSourceCodeInfo($var)
    {
        GPBUtil::checkMessage($var, \Google\Protobuf\SourceCodeInfo::class);
        $this->source_code_info = $var;

        return $this;
    }

    /**
     * The syntax of the proto file.
     * The supported values are "proto2" and "proto3".
     *
     * Generated from protobuf field <code>string syntax = 12;</code>
     * @return string
     */
    public function getSyntax()
    {
        return $this->syntax;
    }

    /**
     * The syntax of the proto file.
     * The supported values are "proto2" and "proto3".
     *
     * Generated from protobuf field <code>string syntax = 12;</code>
     * @param string $var
     * @return $this
     */
    public function setSyntax($var)
    {
        GPBUtil::checkString($var, True);
        $this->syntax = $var;

        return $this;
    }

}


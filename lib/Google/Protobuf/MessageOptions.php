<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: descriptor.proto

namespace Google\Protobuf;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Set true to use the old proto1 MessageSet wire format for extensions.
 * This is provided for backwards-compatibility with the MessageSet wire
 * format.  You should not use this for any other reason:  It's less
 * efficient, has fewer features, and is more complicated.
 * The message must be defined exactly as follows:
 *   message Foo {
 *     option message_set_wire_format = true;
 *
 * Generated from protobuf message <code>google.protobuf.MessageOptions</code>
 */
class MessageOptions extends \Google\Protobuf\Internal\Message
{
    /**
     *   }
     * Note that the message cannot have any defined fields; MessageSets only
     * have extensions.
     * All extensions of your type must be singular messages; e.g. they cannot
     * be int32s, enums, or repeated messages.
     * Because this is an option, the above two restrictions are not enforced by
     * the protocol compiler.
     *
     * Generated from protobuf field <code>bool message_set_wire_format = 1;</code>
     */
    protected $message_set_wire_format = false;
    /**
     * Disables the generation of the standard "descriptor()" accessor, which can
     * conflict with a field of the same name.  This is meant to make migration
     * from proto1 easier; new code should avoid fields named "descriptor".
     *
     * Generated from protobuf field <code>bool no_standard_descriptor_accessor = 2;</code>
     */
    protected $no_standard_descriptor_accessor = false;
    /**
     * Is this message deprecated?
     * Depending on the target platform, this can emit Deprecated annotations
     * for the message, or it will be completely ignored; in the very least,
     * this is a formalization for deprecating messages.
     *
     * Generated from protobuf field <code>bool deprecated = 3;</code>
     */
    protected $deprecated = false;
    /**
     * Whether the message is an automatically generated map entry type for the
     * maps field.
     * For maps fields:
     *     map<KeyType, ValueType> map_field = 1;
     * The parsed descriptor looks like:
     *     message MapFieldEntry {
     *         option map_entry = true;
     *         KeyType key = 1;
     *         ValueType value = 2;
     *     }
     *     repeated MapFieldEntry map_field = 1;
     * Implementations may choose not to generate the map_entry=true message, but
     * use a native map in the target language to hold the keys and values.
     * The reflection APIs in such implementations still need to work as
     * if the field is a repeated message field.
     * NOTE: Do not set the option in .proto files. Always use the maps syntax
     * instead. The option should only be implicitly set by the proto compiler
     * parser.
     *
     * Generated from protobuf field <code>bool map_entry = 7;</code>
     */
    protected $map_entry = false;
    /**
     * The parser stores options it doesn't recognize here. See above.
     *
     * Generated from protobuf field <code>repeated .google.protobuf.UninterpretedOption uninterpreted_option = 999;</code>
     */
    private $uninterpreted_option;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type bool $message_set_wire_format
     *             }
     *           Note that the message cannot have any defined fields; MessageSets only
     *           have extensions.
     *           All extensions of your type must be singular messages; e.g. they cannot
     *           be int32s, enums, or repeated messages.
     *           Because this is an option, the above two restrictions are not enforced by
     *           the protocol compiler.
     *     @type bool $no_standard_descriptor_accessor
     *           Disables the generation of the standard "descriptor()" accessor, which can
     *           conflict with a field of the same name.  This is meant to make migration
     *           from proto1 easier; new code should avoid fields named "descriptor".
     *     @type bool $deprecated
     *           Is this message deprecated?
     *           Depending on the target platform, this can emit Deprecated annotations
     *           for the message, or it will be completely ignored; in the very least,
     *           this is a formalization for deprecating messages.
     *     @type bool $map_entry
     *           Whether the message is an automatically generated map entry type for the
     *           maps field.
     *           For maps fields:
     *               map<KeyType, ValueType> map_field = 1;
     *           The parsed descriptor looks like:
     *               message MapFieldEntry {
     *                   option map_entry = true;
     *                   KeyType key = 1;
     *                   ValueType value = 2;
     *               }
     *               repeated MapFieldEntry map_field = 1;
     *           Implementations may choose not to generate the map_entry=true message, but
     *           use a native map in the target language to hold the keys and values.
     *           The reflection APIs in such implementations still need to work as
     *           if the field is a repeated message field.
     *           NOTE: Do not set the option in .proto files. Always use the maps syntax
     *           instead. The option should only be implicitly set by the proto compiler
     *           parser.
     *     @type \Google\Protobuf\UninterpretedOption[]|\Google\Protobuf\Internal\RepeatedField $uninterpreted_option
     *           The parser stores options it doesn't recognize here. See above.
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\Descriptor::initOnce();
        parent::__construct($data);
    }

    /**
     *   }
     * Note that the message cannot have any defined fields; MessageSets only
     * have extensions.
     * All extensions of your type must be singular messages; e.g. they cannot
     * be int32s, enums, or repeated messages.
     * Because this is an option, the above two restrictions are not enforced by
     * the protocol compiler.
     *
     * Generated from protobuf field <code>bool message_set_wire_format = 1;</code>
     * @return bool
     */
    public function getMessageSetWireFormat()
    {
        return $this->message_set_wire_format;
    }

    /**
     *   }
     * Note that the message cannot have any defined fields; MessageSets only
     * have extensions.
     * All extensions of your type must be singular messages; e.g. they cannot
     * be int32s, enums, or repeated messages.
     * Because this is an option, the above two restrictions are not enforced by
     * the protocol compiler.
     *
     * Generated from protobuf field <code>bool message_set_wire_format = 1;</code>
     * @param bool $var
     * @return $this
     */
    public function setMessageSetWireFormat($var)
    {
        GPBUtil::checkBool($var);
        $this->message_set_wire_format = $var;

        return $this;
    }

    /**
     * Disables the generation of the standard "descriptor()" accessor, which can
     * conflict with a field of the same name.  This is meant to make migration
     * from proto1 easier; new code should avoid fields named "descriptor".
     *
     * Generated from protobuf field <code>bool no_standard_descriptor_accessor = 2;</code>
     * @return bool
     */
    public function getNoStandardDescriptorAccessor()
    {
        return $this->no_standard_descriptor_accessor;
    }

    /**
     * Disables the generation of the standard "descriptor()" accessor, which can
     * conflict with a field of the same name.  This is meant to make migration
     * from proto1 easier; new code should avoid fields named "descriptor".
     *
     * Generated from protobuf field <code>bool no_standard_descriptor_accessor = 2;</code>
     * @param bool $var
     * @return $this
     */
    public function setNoStandardDescriptorAccessor($var)
    {
        GPBUtil::checkBool($var);
        $this->no_standard_descriptor_accessor = $var;

        return $this;
    }

    /**
     * Is this message deprecated?
     * Depending on the target platform, this can emit Deprecated annotations
     * for the message, or it will be completely ignored; in the very least,
     * this is a formalization for deprecating messages.
     *
     * Generated from protobuf field <code>bool deprecated = 3;</code>
     * @return bool
     */
    public function getDeprecated()
    {
        return $this->deprecated;
    }

    /**
     * Is this message deprecated?
     * Depending on the target platform, this can emit Deprecated annotations
     * for the message, or it will be completely ignored; in the very least,
     * this is a formalization for deprecating messages.
     *
     * Generated from protobuf field <code>bool deprecated = 3;</code>
     * @param bool $var
     * @return $this
     */
    public function setDeprecated($var)
    {
        GPBUtil::checkBool($var);
        $this->deprecated = $var;

        return $this;
    }

    /**
     * Whether the message is an automatically generated map entry type for the
     * maps field.
     * For maps fields:
     *     map<KeyType, ValueType> map_field = 1;
     * The parsed descriptor looks like:
     *     message MapFieldEntry {
     *         option map_entry = true;
     *         KeyType key = 1;
     *         ValueType value = 2;
     *     }
     *     repeated MapFieldEntry map_field = 1;
     * Implementations may choose not to generate the map_entry=true message, but
     * use a native map in the target language to hold the keys and values.
     * The reflection APIs in such implementations still need to work as
     * if the field is a repeated message field.
     * NOTE: Do not set the option in .proto files. Always use the maps syntax
     * instead. The option should only be implicitly set by the proto compiler
     * parser.
     *
     * Generated from protobuf field <code>bool map_entry = 7;</code>
     * @return bool
     */
    public function getMapEntry()
    {
        return $this->map_entry;
    }

    /**
     * Whether the message is an automatically generated map entry type for the
     * maps field.
     * For maps fields:
     *     map<KeyType, ValueType> map_field = 1;
     * The parsed descriptor looks like:
     *     message MapFieldEntry {
     *         option map_entry = true;
     *         KeyType key = 1;
     *         ValueType value = 2;
     *     }
     *     repeated MapFieldEntry map_field = 1;
     * Implementations may choose not to generate the map_entry=true message, but
     * use a native map in the target language to hold the keys and values.
     * The reflection APIs in such implementations still need to work as
     * if the field is a repeated message field.
     * NOTE: Do not set the option in .proto files. Always use the maps syntax
     * instead. The option should only be implicitly set by the proto compiler
     * parser.
     *
     * Generated from protobuf field <code>bool map_entry = 7;</code>
     * @param bool $var
     * @return $this
     */
    public function setMapEntry($var)
    {
        GPBUtil::checkBool($var);
        $this->map_entry = $var;

        return $this;
    }

    /**
     * The parser stores options it doesn't recognize here. See above.
     *
     * Generated from protobuf field <code>repeated .google.protobuf.UninterpretedOption uninterpreted_option = 999;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getUninterpretedOption()
    {
        return $this->uninterpreted_option;
    }

    /**
     * The parser stores options it doesn't recognize here. See above.
     *
     * Generated from protobuf field <code>repeated .google.protobuf.UninterpretedOption uninterpreted_option = 999;</code>
     * @param \Google\Protobuf\UninterpretedOption[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setUninterpretedOption($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Google\Protobuf\UninterpretedOption::class);
        $this->uninterpreted_option = $arr;

        return $this;
    }

}


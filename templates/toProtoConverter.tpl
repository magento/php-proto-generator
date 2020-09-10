{% macro property_tree(in_type, in_var, out_type, out_var, props, i) %}
    // convert data from {{ in_type }}
    // to {{ out_type }}
    /** @var {{ in_type }} ${{ in_var }} **/
    $p = function () use (${{ in_var }}) {
        $r = new {{ out_type }}();
{% for prop in props %}
{% set i = i + 1 %}
{% if prop.object %}
    {% if prop.array %}
        $res = [];
        {% set v_name = 'item' ~ i %}
        foreach (${{ in_var}}->get{{ prop.name }}() as ${{ v_name }}) {
            {{ _self.property_tree(prop.in_type, v_name, prop.out_type, out_var, prop.props, i) }}
            $res[] = ${{ out_var }};
        }
$r->set{{ prop.name }}($res);
    {% else %}
        {% set var_name = 'prop' ~ i %}
        ${{ var_name }} = ${{ in_var }}->get{{ prop.name }}();
        if (${{ var_name }} !== null) {
            {{ _self.property_tree(prop.in_type, var_name, prop.out_type, out_var, prop.props, i) }}
            $r->set{{ prop.name }}(${{ out_var}});
        }
    {% endif %}
{% elseif prop.array %}
    $values = [];
    foreach (${{ in_var }}->get{{ prop.name }}() as $newValue) {
        $values[] = $newValue;
    }
    $r->set{{ prop.name }}($values);
{% else %}
        $r->set{{ prop.name }}(${{ in_var }}->get{{ prop.name }}());
{% endif %}
{% endfor %}
        return $r;
    };
    ${{ out_var }} = $p();
{%- endmacro %}
{% set i = 0 %}
{{ _self.property_tree(in_type, in_var, out_type, out_var, props, i)}}
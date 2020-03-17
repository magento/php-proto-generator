{% macro property_tree(in_type, in_var, out_type, out_var, props) %}
    // convert data from {{ in_type }}
    // to {{ out_type }}
    $p = function () use (${{ in_var }}) {
        $r = new {{ out_type }}();
{% for prop in props %}
{% if prop.object %}
        $prop = ${{ in_var }}->get{{ prop.name }}();
        {{ _self.property_tree(prop.in_type, 'prop', prop.out_type, out_var, prop.props) }}
        $r->set{{ prop.name }}(${{ out_var}});

{% elseif prop.array %}
        $res = [];
        foreach (${{ in_var}}->get{{ prop.name }}() as $item) {
            {{ _self.property_tree(prop.in_type, 'item', prop.out_type, out_var, prop.props) }}
            $res[] = ${{ out_var }};
        }
        $r->set{{ prop.name }}($res);

{% else %}
        $r->set{{ prop.name }}(${{ in_var }}->get{{ prop.name }}());
{% endif %}
{% endfor %}
        return $r;
    };
    ${{ out_var }} = $p();
{%- endmacro %}
{{ _self.property_tree(in_type, in_var, out_type, out_var, props)}}
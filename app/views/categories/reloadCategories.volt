{%- cache "sidebar" -%}
	{%- if categories is defined -%}
        {%- for category in categories -%}
            <li>
                {{-
                    link_to(
                        'category/' ~ category.id ~ '/' ~ category.slug,
                        '<span class="label label-default pull-right">' ~ category.number_posts ~ '</span>' ~ category.name
                    )
                -}}
            </li>
        {%- endfor -%}
    {%- endif -%}
{%- endcache -%}

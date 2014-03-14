{% cache "sidebar" %}
	{%- if categories is defined -%}
		{%- for category in categories -%}
			<li>
				{{- link_to('category/' ~ category.id ~ '/' ~ category.slug,
					category.name ~ '<span class="label label-default" style="float: right">' ~ category.number_posts ~ '</span>')
				-}}
			</li>
		{%- endfor -%}
	{%- endif -%}
{% endcache %}
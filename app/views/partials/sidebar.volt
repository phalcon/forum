

{#<div class="sidebar">

	{% if session.get('identity') %}
		{{ link_to('post/discussion', 'Start a Discussion', 'class': 'btn btn-large btn-info', 'rel': 'nofollow') }}
	{% else %}
		{{ link_to('login/oauth/authorize', 'Log In with Github', 'class': 'btn btn-large btn-info', 'rel': 'nofollow') }}
	{% endif %}

	{% cache "sidebar" %}
	<ul class="nav nav-tabs nav-stacked">
	{% for category in categories %}
		<li>
			{{ link_to('category/' ~ category.id ~ '/' ~ category.slug,
				category.name ~ '<span class="number-posts label">' ~ category.number_posts ~ '</span>')
			}}
		</li>
	{% endfor %}
	</ul>
	{% endcache %}

</div>#}

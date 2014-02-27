{{ content() }}

<div class="view-discussion">
	<p>
		<h1>Recent Activity</h1>
	</p>

	<table width="90%" align="center" class="table">
		<tr>
			<td colspan="2">
				<div class="row">
					<ul class="nav nav-tabs">
						{% set orders = [
							'': 'Forum',
							'/irc': 'IRC'
						] %}
						{% for order, label in orders %}
							{% if order == '' %}
								<li class="active">
							{% else %}
								<li>
							{% endif %}
								{{ link_to('activity' ~ order, label) }}
							</li>
						{% endfor %}
					</ul>
				</div>
			</td>
		</tr>
		{% for activity in activities %}
		<tr>
			<td class="small" valign="top">
				<img src="https://secure.gravatar.com/avatar/{{ activity.user.gravatar_id }}?s=48" class="img-rounded">
			</td>
			<td>
				<div class="activity">
					<span>{{ link_to('user/' ~ activity.user.id ~ '/' ~ activity.user.login, activity.user.name|e) }}</span>

					{% if activity.type == 'U' %}
					has joined the forum
					{% elseif activity.type == 'P' %}
					has posted {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.post.title|e) }}
					{% elseif activity.type == 'C' %}
					has commented in {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.post.title|e) }}
					{% endif %}

					<span class="date">{{ date('M d/Y H:i', activity.created_at) }}</span>
				</div>
			</td>
		</tr>
		{% endfor %}
	</table>

</div>

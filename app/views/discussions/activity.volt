{{ content() }}

<div class="view-discussion activity-container">

    <h1>Recent Activity</h1>

	<ul class="nav nav-tabs">
		{%- set orders = ['': 'Forum', '/irc': 'IRC'] %}
		{%- for order, label in orders -%}
			{%- if order == '' -%}
			<li class="active">
			{%- else -%}
			<li>
			{%- endif -%}
			{{ link_to('activity' ~ order, label) }}
			</li>
		{%- endfor -%}
	</ul>

	<table width="90%" align="center" class="table table-striped">
		{%- for activity in activities -%}
			{%- if activity.post and activity.post.deleted != 1 -%}
			<tr>
				<td class="small hidden-xs" valign="top">
					{{ image(gravatar(activity.user.email), 'class': 'img-rounded') }}
				</td>
				<td>
					<div class="activity">
						<span>{{ link_to('user/' ~ activity.user.id ~ '/' ~ activity.user.login, activity.user.name|e) }} </span>

						{%- if activity.type == 'U' -%}
						has joined the forum
						{%- elseif activity.type == 'P' -%}
						has posted {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.post.title|e) }}
						{%- elseif activity.type == 'C' -%}
						has commented in {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.post.title|e) }}
						{%- endif -%}

						<span class="date"> {{ activity.getHumanCreatedAt() }}</span>
					</div>
				</td>
			</tr>
			{%- endif -%}
		{%- endfor -%}
	</table>

</div>

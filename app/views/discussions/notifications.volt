{{ content() }}

<div class="view-discussion activity-container">

    <h1>Notifications</h1>

	<table width="90%" align="center" class="table table-striped">
		{%- for activity in notifications -%}
			{%- if activity.post and activity.post.deleted != 1 -%}
				<tr>
					<td class="small hidden-xs" valign="top">
						{{ image(gravatar(activity.userOrigin.email), 'class': 'img-rounded') }}
					</td>
					<td>
						<div class="activity{% if activity.was_read == 'N' %} unread{% endif %}">
							<span>{{ link_to('user/' ~ activity.userOrigin.id ~ '/' ~ activity.userOrigin.login, activity.userOrigin.name|e) }} </span>

							{%- if activity.type == 'U' -%}
							has joined the forum
							{%- endif -%}

							{%- if activity.type == 'P' -%}
							has upvoted your post {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.post.title|e) }}
							{%- endif -%}

							{%- if activity.type == 'C' -%}
								{% if activity.post.users_id == user.id %}
									has commented in your post {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug ~ '#C' ~ activity.posts_replies_id, activity.post.title|e) }}
								{%- else -%}
									has commented in {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug ~ '#C' ~ activity.posts_replies_id, activity.post.title|e) }}
								{%- endif -%}
							{%- endif -%}

							{%- if activity.type == 'R' -%}
							has upvoted in your reply in {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug ~ '#C' ~ activity.posts_replies_id, activity.post.title|e) }}
							{%- endif -%}

							{%- if activity.type == 'A' -%}
							has accepted your reply in {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug ~ '#C' ~ activity.posts_replies_id, activity.post.title|e) }}
							{%- endif -%}

							{%- if activity.type == 'B' -%}
							you've earned the "{{ link_to('help/badges', activity.extra) }}" badge
							{%- endif -%}

							{%- if activity.type == 'O' -%}
							you've earned the "{{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.extra) }}" badge
							{%- endif -%}

							{% if activity.type == 'V' %}
							you've earned the "{{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug ~ '#C' ~ activity.posts_replies_id, activity.extra) }}" badge
							{%- endif -%}

							<span class="date"> {{ activity.getHumanCreatedAt() }}</span>
						</div>
					</td>
				</tr>
			{%- endif -%}
			{% do activity.markAsRead() %}
			{%- else -%}
			<tr>
				<td colspan="2" align="center">
					You don't have any new notifications
				</td>
			</tr>
		{%- endfor -%}
	</table>

</div>


<div class="user-profile">
	<table align="center">
		<tr>
			<td class="small" valign="top">
				<img src="https://secure.gravatar.com/avatar/{{ user.gravatar_id }}?s=64" class="img-rounded">
			</td>
			<td align="left" valign="top">
				<h1>{{ user.name }}</h1>
				<p>
					<span>joined {{ date('M d/Y', user.created_at) }}</span><br>
					<span>posts {{ numberPosts }}</span> / <span>replies {{ numberReplies }}</span><br>
					<a href="https://github.com/{{ user.login }}">Github Profile</a>
				</p>
				<p>
					<ul class="nav nav-tabs">
						<li class="active"><a href="#">Recent Activity</a><li>
					</ul>
				</p>
				<p>
					{% for activity in activities %}
						<div class="activity">
							{% if activity.type == 'U' %}
							has joined the forum
							{% elseif activity.type == 'P' %}
							has posted {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.post.title|e) }}
							{% elseif activity.type == 'C' %}
							has commented in {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.post.title|e) }}
							{% endif %}

							<span class="date">{{ date('M d/Y H:i', activity.created_at) }}</span>
						</div>
					{% endfor %}
				</p>
			</td>
		</tr>
	</table>
</div>
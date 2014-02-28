
<div class="user-profile container">
	<table align="center">
		<tr>
			<td class="small remove-image" valign="top">
				<img src="https://secure.gravatar.com/avatar/{{ user.gravatar_id }}?s=64&amp;r=pg&amp;d=identicon" class="img-rounded">
			</td>
			<td align="left" valign="top">
				<h1>{{ user.name|e }}</h1>
				<p>
					<span>joined <b>{{ date('M d/Y', user.created_at) }}</b></span><br>
					<span>posts <b>{{ numberPosts }}</b></span> / <span>replies <b>{{ numberReplies }}</b></span><br>
					<span>reputation <b>{{ user.karma }}</b></span><br>
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
							<span class="date">{{ activity.getHumanCreatedAt() }}</span>
						</div>
					{% endfor %}
				</p>
			</td>
		</tr>
	</table>
</div>
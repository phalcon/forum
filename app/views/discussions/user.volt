
<hr>

<div align="center" class="container">
	<div class="user-profile">
		<table align="center">
			<tr>
				<td class="small hidden-xs" valign="top">
					<img src="https://secure.gravatar.com/avatar/{{ user.gravatar_id }}?s=64&amp;r=pg&amp;d=identicon" class="img-rounded" width="64" height="64">
				</td>
				<td align="left" valign="top">
					<h1>{{ user.name|e }}</h1>
					<span class="login">{{ user.login }}</span><br>
					<p>
						<span>joined <b>{{ date('M d/Y', user.created_at) }}</b></span><br>
						<span>posts <b>{{ numberPosts }}</b></span> / <span>replies <b>{{ numberReplies }}</b></span><br>
						<span>reputation <b>{{ user.karma }}</b></span><br>
						<span>reputation ranking <b>#{{ ranking }}</b> of <b>{{ total_ranking }}</b></span><br>
						<a href="https://github.com/{{ user.login }}">Github Profile</a>
					</p>
					<p>
						<ul class="nav nav-tabs">
							<li class="active"><a href="#">Recent Activity</a><li>
						</ul>
					</p>
					<p>
						<table class="table table-striped">
						{% for activity in activities %}
							<tr><td>
								{% if activity.type == 'U' %}
								has joined the forum
								{% elseif activity.type == 'P' %}
								has posted {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.post.title|e) }}
								{% elseif activity.type == 'C' %}
								has commented in {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.post.title|e) }}
								{% endif %}
								<span class="date">{{ activity.getHumanCreatedAt() }}</span>
							</td></tr>
						{% endfor %}
						</table>
					</p>
				</td>
			</tr>
		</table>
	</div>
</div>

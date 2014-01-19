{{ content() }}

{% set currentUser = session.get('identity') %}

<div class="view-discussion">
	<p>
		<h1>{{ post.title|e }}</h1>
	</p>

	<table class="view-list-posts" align="center">
		<tr>
			<td class="small" valign="top">
				<img src="https://secure.gravatar.com/avatar/{{ post.user.gravatar_id }}?s=48" class="img-rounded">
			</td>
			<td>
				<div class="post-header">
					<span>{{ link_to('user/' ~ post.user.id ~ '/' ~ post.user.login, post.user.name|e) }}</span>
					posted this <span>{{ date('M d/Y H:i', post.created_at) }}</span>

					<div class="posts-buttons">
						{% if post.users_id == currentUser %}
							{{ link_to('edit/discussion/' ~ post.id, '<i class="icon-edit" title="Edit"></i>') }}
						{% endif %}
					</div>

				</div>
				<div class="post-content">
					{{ post.content|e|nl2br }}
				</div>
			</td>
		</tr>

		{% for reply in post.replies %}
		<tr>
			<td class="small" valign="top">
				<img src="https://secure.gravatar.com/avatar/{{ reply.user.gravatar_id }}?s=48" class="img-rounded">
			</td>
			<td>
				<div class="post-header">
					<span>{{ link_to('user/' ~ reply.user.id ~ '/' ~ reply.user.login, reply.user.name|e) }}</span>
					commented <span>{{ date('M d/Y H:i', reply.created_at) }}</span>

					<div class="posts-buttons">
						<a name="C{{ reply.id }}" href="#C{{ reply.id }}"><i class="icon-globe" title="Permalink"></i></a>
						{% if reply.users_id == currentUser %}
							<i class="icon-edit reply-edit" title="Edit" data-id="{{ reply.id }}"></i>
							<i class="icon-remove reply-remove" title="Delete" data-id="{{ reply.id }}"></i>
						{% endif %}
					</div>

				</div>
				<div class="post-content">
					{{ reply.content|e|nl2br }}
				</div>
			</td>
		</tr>
		{% endfor %}

		<tr>
		{% if currentUser %}
		<tr>
			<td valign="top" class="small">
				<img src="https://secure.gravatar.com/avatar/{{ session.get('identity-gravatar') }}?s=48" class="img-rounded">
			</td>
			<td>
				<ul class="nav nav-tabs preview-nav">
					<li class="active"><a href="#" onclick="return false">Comment</a></li>
					<li><a href="#" onclick="return false">Preview</a></li>
					<li class="pull-right">{{ link_to('help', 'Help', 'class': 'help') }}</li>
				</ul>

				<form method="post" autocomplete="off">
					<p>
						<div id="comment-box">
							{{ hidden_field('id', 'value': post.id) }}
							{{ text_area("content", "rows": 5, "placeholder": "Leave a comment") }}
						</div>
						<div id="preview-box" style="display:none"></div>
					</p>
					<p>
						<div class="pull-left">
							{{ link_to('', 'Back to discussions') }}
						</div>
						<div class="pull-right">
							<button type="submit" class="btn btn-success">Add Comment</button>
						</div>
					</p>
				</form>
			</td>
		{% else %}
			<td></td>
			<td>
				<div class="pull-left">
					{{ link_to('', 'Back to discussions') }}
				</div>
				<div class="pull-right">
					{{ link_to('login/oauth/authorize', 'Log In to Comment', 'class': 'btn btn-info') }}
				</div>
			</td>
		{% endif %}
		</tr>
	</table>

</div>

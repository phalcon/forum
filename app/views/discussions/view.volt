{{ content() }}

{% set currentUser = session.get('identity') %}

<div class="view-discussion">
	<p>
		<h1>{{ post.title|e }}</h1>
	</p>

	<table width="90%" align="center">
		<tr>
			<td class="small" valign="top">
				<img src="https://secure.gravatar.com/avatar/{{ post.user.gravatar_id }}?s=48" class="img-rounded">
			</td>
			<td>
				<div class="post-header">
					<span>{{ link_to('user/' ~ post.user.id ~ '/' ~ post.user.login, post.user.name) }}</span>
					posted this <span>{{ date('M d/Y H:i', post.created_at) }}</span>
				</div>
				<div class="post-content">
					{{ post.content|e|nl2br }}
				</div>
			</td>
		</tr>

		{% for reply in post.replies %}
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td class="small" valign="top">
				<img src="https://secure.gravatar.com/avatar/{{ reply.user.gravatar_id }}?s=48" class="img-rounded">
			</td>
			<td>
				<div class="post-header">
					<span>{{ link_to('user/' ~ reply.user.id ~ '/' ~ reply.user.login, reply.user.name) }}</span>
					commented <span>{{ date('M d/Y H:i', reply.created_at) }}</span>

					<div class="posts-buttons">
						<a name="C{{ reply.id }}" href="#C{{ reply.id }}"><i class="icon-globe" title="Permalink"></i></a>
						{% if reply.users_id == currentUser %}
							<i class="icon-edit" title="Edit" data-id="{{ reply.id }}"></i>
							<i class="icon-remove" title="Delete" data-id="{{ reply.id }}"></i>
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
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td valign="top">
				<img src="https://secure.gravatar.com/avatar/{{ session.get('identity-gravatar') }}?s=48" class="img-rounded">
			</td>
			<td>
				<form method="post" autocomplete="off">
					<p>
						{{ hidden_field('id', 'value': post.id) }}
						{{ text_area("content", "rows": 5, "placeholder": "Leave a comment") }}
					</p>
					<p>
						<div align="left">
							{{ link_to('', 'Back to discussions') }}
						</div>
						<div align="right">
							<button type="submit" class="btn btn-success">Add Comment</button>
						</div>
					</p>
				</form>
			</td>
		{% else %}
			<td></td>
			<td>
				<div align="pulll-eft">
					{{ link_to('', 'Back to discussions') }}
				</div>
				<div align="pull-right">
					{{ link_to('login/oauth/authorize', 'Log In to Comment', 'class': 'btn btn-info') }}
				</div>
			</td>
		{% endif %}
		</tr>
	</table>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>

    {{ javascript_include("js/forum.js") }}

    <script type="text/javascript">Forum.initializeView('{{ url() }}');</script>
</div>


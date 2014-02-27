{{ content() }}

{% set currentUser = session.get('identity'), moderator = session.get('identity-moderator') %}

<div class="container">

	<ol class="breadcrumb">
		<li>{{ link_to('', 'Home') }}</a></li>
		<li>{{ link_to('category/' ~ post.category.id ~ '/' ~ post.category.slug, post.category.name) }}</a></li>
	</ol>

	<p>
		<h1>{{ post.title|e }}</h1>
	</p>

	<table class="table discussion" align="center">
		<tr>
			<td valign="top" align="center" class="small">
				<img src="https://secure.gravatar.com/avatar/{{ post.user.gravatar_id }}?s=48&amp;r=pg&amp;d=identicon" class="img-rounded"><br>
				<span>{{ link_to('user/' ~ post.user.id ~ '/' ~ post.user.login, post.user.name|e) }}</span>
			</td>
			<td class="post-body">
				<div class="post-header">
					<div class="posts-buttons">
						<a name="C{{ reply.id }}" href="#C{{ reply.id }}">
							<span class="action-date">
								posted this <span>{{ date('M d/Y H:i', post.created_at) }}</span>
							</span>
						</a>
					</div>
				</div>
				<div class="post-content">
					{{ markdown.render(post.content|e) }}
				</div>
				<div class="posts-buttons">
					{% if post.users_id == currentUser or moderator == 'Y' %}
						{{ link_to('edit/discussion/' ~ post.id, 'Edit', "class": "btn btn-default btn-xs") }}
						{{ link_to('delete/discussion/' ~ post.id, 'Delete', "class": "btn btn-default btn-xs") }}
					{% endif %}
				</div>
			</td>
		</tr>

		{% for reply in post.replies %}
		<tr>
			<td class="small" valign="top" align="center">
				<img src="https://secure.gravatar.com/avatar/{{ reply.user.gravatar_id }}?s=48&amp;r=pg&amp;d=identicon" class="img-rounded"><br>
				<span>{{ link_to('user/' ~ reply.user.id ~ '/' ~ reply.user.login, reply.user.name|e) }}</span>
			</td>
			<td class="post-body">
				<div class="post-header">
					<div class="posts-buttons" align="right">
						<a name="C{{ reply.id }}" href="#C{{ reply.id }}">
							<span class="action-date">
								commented <span>{{ date('M d/Y H:i', reply.created_at) }}</span>
							</span>
						</a>
						{% if reply.users_id == currentUser or moderator == 'Y' %}
							<br>
							<a class="btn btn-default btn-xs reply-edit" data-id="{{ reply.id }}">Edit</a>
							<a class="btn btn-default btn-xs reply-delete" data-id="{{ reply.id }}">Delete</a>
							<br>
						{% endif %}
					</div>
				</div>
				<div class="post-content">
					{{ markdown.render(reply.content|e) }}
				</div>
			</td>
		</tr>
		{% endfor %}

		<tr>
		{% if currentUser %}
		<tr>
			<td valign="top" class="small" align="center">
				<img src="https://secure.gravatar.com/avatar/{{ session.get('identity-gravatar') }}?s=48&amp;r=pg&amp;d=identicon" class="img-rounded"><br>
				<span>{{ link_to('', 'You') }}</span>
			</td>
			<td>
				<ul class="nav nav-tabs preview-nav">
					<li class="active"><a href="#" onclick="return false">Comment</a></li>
					<li><a href="#" onclick="return false">Preview</a></li>
					<li class="pull-right">{{ link_to('help', 'Help', 'class': 'help') }}</li>
				</ul>

				<form method="post" autocomplete="off" role="form">
					<p>
						<div id="comment-box">
							{{ hidden_field('id', 'value': post.id) }}
							{{ text_area("content", "rows": 5, "placeholder": "Leave a comment", "class": "form-control") }}
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

				<script type="text/javascript">
					window.onload = function(){
						var editor = new Editor();
						editor.render();
					};
				</script>
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



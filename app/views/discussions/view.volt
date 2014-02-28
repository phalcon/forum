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
				<span>{{ link_to('user/' ~ post.user.id ~ '/' ~ post.user.login, post.user.name|e, 'class': 'user-moderator-' ~ post.user.moderator) }}</span><br>
				<span class="karma">{{ post.user.getHumanKarma() }}</span>
			</td>
			<td class="post-body">
				<div class="posts-buttons" align="right">
					{% if post.edited_at > 0 %}
						<span class="action-date action-edit" data-id="{{ post.id }}" data-toggle="modal" data-target="#historyModal">
							edited <span>{{ date('M d/Y H:i', post.edited_at) }}</span>
						</span><br/>
					{% endif %}
					<a name="C{{ post.id }}" href="#C{{ post.id }}">
						<span class="action-date">
							<span>{{ post.getHumanCreatedAt() }}</span>
						</span>
					</a>
				</div>
				<div class="post-content">
					{{ markdown.render(post.content|e) }}
				</div>
				<div class="posts-buttons" align="right">
					{% if post.users_id == currentUser or moderator == 'Y' %}
						{{ link_to('edit/discussion/' ~ post.id, '<span class="glyphicon glyphicon-pencil"></span>&nbsp;Edit', "class": "btn btn-default btn-xs") }}
						{{ link_to('delete/discussion/' ~ post.id, '<span class="glyphicon glyphicon-remove"></span>&nbsp;Delete', "class": "btn btn-default btn-xs") }}
					{% endif %}
					{% if currentUser %}
						<a href="#" onclick="return false" class="btn btn-success btn-xs vote-post-up" data-id="{{ post.id }}">
							<span class="glyphicon glyphicon-thumbs-up"></span>
							{{ post.votes_up }}
						</a>
						<a href="#" onclick="return false" class="btn btn-danger btn-xs vote-post-down" data-id="{{ post.id }}">
							<span class="glyphicon glyphicon-thumbs-down"></span>
							{{ post.votes_down }}
						</a>
					{% endif %}
				</div>
			</td>
		</tr>

		{% for reply in post.replies %}
		<tr>
			<td class="small" valign="top" align="center">
				<img src="https://secure.gravatar.com/avatar/{{ reply.user.gravatar_id }}?s=48&amp;r=pg&amp;d=identicon" class="img-rounded"><br>
				<span>{{ link_to('user/' ~ reply.user.id ~ '/' ~ reply.user.login, reply.user.name|e, 'class': 'user-moderator-' ~ reply.user.moderator) }}</span><br>
				<span class="karma">{{ reply.user.getHumanKarma() }}</span>
			</td>
			<td class="post-body">
				<div class="posts-buttons" align="right">
					{% if reply.edited_at > 0 %}
						<span class="action-date action-edit" data-id="{{ reply.id }}" data-toggle="modal" data-target="#historyModal">
							edited <span>{{ date('M d/Y H:i', reply.edited_at) }}</span>
						</span><br/>
					{% endif %}
					<a name="C{{ reply.id }}" href="#C{{ reply.id }}">
						<span class="action-date">
							<span>{{ reply.getHumanCreatedAt() }}</span>
						</span>
					</a>
				</div>
				<div class="post-content">
					{{ markdown.render(reply.content|e) }}
				</div>
				<div class="posts-buttons" align="right">
					{% if reply.users_id == currentUser or moderator == 'Y' %}
						<br>
						<a class="btn btn-default btn-xs reply-edit" data-id="{{ reply.id }}">
							<span class="glyphicon glyphicon-pencil"></span>&nbsp;Edit
						</a>
						<a class="btn btn-default btn-xs reply-delete" data-id="{{ reply.id }}">
							<span class="glyphicon glyphicon-remove"></span>&nbsp;Delete
						</a>
					{% endif %}
					{% if currentUser %}
						<a href="#" onclick="return false" class="btn btn-success btn-xs vote-reply-up" data-id="{{ reply.id }}">
							<span class="glyphicon glyphicon-thumbs-up"></span>
							{{ reply.votes_up }}
						</a>
						<a href="#" onclick="return false" class="btn btn-danger btn-xs vote-reply-down" data-id="{{ reply.id }}">
							<span class="glyphicon glyphicon-thumbs-down"></span>
							{{ reply.votes_down }}
						</a>
					{% endif %}
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

<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="historyModalLabel">History</h4>
      </div>
      <div class="modal-body" id="historyBody">
        Loading...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header alert-danger">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="errorModalLabel">Error</h4>
      </div>
      <div class="modal-body" id="errorBody">

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

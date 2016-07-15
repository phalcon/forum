{{- content() -}}

{% include 'partials/flash-banner.volt' %}

{%- set currentUser = session.get('identity'), moderator = session.get('identity-moderator') -%}
{%- set tokenKey = security.getPrefixedTokenKey('post-' ~ post.id) -%}
{%- set token = security.getPrefixedToken('post-' ~ post.id) -%}

{%- if (post.votes_up - post.votes_down) <= -3 -%}
	<div class="bs-callout bs-callout-danger">
		<h4>Too many negative votes</h4>
		<p>This post has too many negative votes. The cause of this could be:</p>
		<ul>
			<li>Irrelevant or controversial information</li>
			<li>confusing question or not a real question</li>
			<li>Aggressive vocabulary, excessive rudeness, etc.</li>
		</ul>
	</div>
{% else %}
	{%- if post.accepted_answer == 'Y' -%}
		<div class="bs-callout bs-callout-success">
			<h4>Solved thread</h4>
			<p>This post is marked as solved. If you think the information contained on this thread must be part of the
				official documentation, please contribute submitting a <a href="https://help.github.com/articles/creating-a-pull-request">pull request</a> to its <a href="{{ config.site.docs }}">repository</a>.
			</p>
		</div>
	{%- endif -%}
{%- endif -%}

{%- if post.canHaveBounty() -%}
	{%- set bounty = post.getBounty() -%}
	<div class="bs-callout bs-callout-info">
		<h4>Bounty available!</h4>
		{%- if bounty['type'] == "old" -%}
			<p>
				It has been a while and this question still does not have any answers.
				Answer this question and get additional <span class="label label-info">+{{ bounty['value'] }}</span> points of karma/reputation if the original poster accepts your reply as correct answer
			</p>
		{%- elseif bounty['type'] == "fast-reply" -%}
			<p>
				This post has recently posted.
				Answer this question and get additional <span class="label label-info">+{{ bounty['value'] }}</span> points of karma/reputation if the original poster accepts your reply as correct answer
			</p>
		{%- endif -%}
	</div>
{%- endif -%}

<div itemscope itemtype="http://schema.org/Question">

	<ol class="breadcrumb">
		<li>{{ link_to('', 'Home') }}</li>
		<li>{{ link_to('category/' ~ post.category.id ~ '/' ~ post.category.slug, post.category.name) }}</li>
	</ol>

	<div class="row table-title">
		<div class="col-md-8">
			<h1 class="{% if (post.votes_up - post.votes_down) <= -3 %}post-negative-h1{% endif %}" itemprop="name">
				{{- post.title|e -}}
			</h1>
		</div>
		<div class="col-md-4">
			<table class="table-stats">
				<tr>
					<td>
						<label>Created</label><br>
						<time itemprop="dateCreated" datetime="{{ date('c', post.created_at) }}">
							{{- post.getHumanCreatedAt() -}}
						</time>
					</td>
					<td>
						<label>Last Reply</label><br>
						{{- post.getHumanModifiedAt() ? post.getHumanModifiedAt() : "None" -}}
					</td>
					<td>
						<label>Replies</label><br>
						<span itemprop="answerCount">
							{{- post.number_replies -}}
						</span>
					</td>
					<td>
						<label>Views</label><br>
						{{- post.number_views -}}
					</td>
					<td>
						<label>Votes</label><br>
						<span itemprop="upvoteCount">{{- post.votes_up - post.votes_down -}}</span>
					</td>
				</tr>
			</table>
		</div>
	</div>

	{%- if moderator == 'Y' -%}
		<ul class="nav navbar-nav navbar-right">

		</ul>
	{%- endif -%}

	<div class="discussion">
		<div class="row reply-block">
			<div class="col-md-1 small" align="center">
				{{ image(gravatar.getAvatar(post.user.email), 'width': 48, 'height': 48, 'class': 'img-rounded') }}<br>
				<span itemprop="author" itemscope itemtype="http://schema.org/Person">
					{{ link_to(
					'user/' ~ post.user.id ~ '/' ~ post.user.login,
					'<span itemprop="name">' ~ post.user.name|e ~ '</span>',
					'class': 'user-moderator-' ~ post.user.moderator
					) }}
				</span><br>
				<span class="karma">{{ post.user.getHumanKarma() }}</span>
			</div>
			<div class="col-md-11 post-body{% if (post.votes_up - post.votes_down) <= -3 %} post-negative-body{% endif %}">
				<div class="posts-buttons" align="right">
					{% if post.edited_at > 0 %}
						<span class="action-date action-edit" data-id="{{ post.id }}" data-toggle="modal" data-target="#historyModal">
							edited <span>{{ post.getHumanEditedAt() }}</span>
						</span><br/>
					{% endif %}
					<a name="C{{ post.id }}" href="#C{{ post.id }}">
						<time class="action-date">{{ post.getHumanCreatedAt() }}</time>
					</a>
				</div>
				<div class="post-content">
					{%- cache "post-body-" ~ post.id -%}
						<div itemprop="text">
							{{- markdown.render(post.content|e) -}}
						</div>
					{%- endcache -%}
					{% if post.hasPoll() %}
						{% if voted %}
							{% include 'partials/poll-votes' with ['post': post, 'result': voting] %}
						{% else %}
							{% include 'partials/poll-options' with ['post': post, 'currentUser': currentUser] %}
						{% endif %}
					{% endif %}
				</div>
				<div class="posts-buttons" align="right">
					{%- if post.users_id == currentUser or moderator == 'Y' -%}
						{{ link_to('edit/discussion/' ~ post.id, '<span class="glyphicon glyphicon-pencil"></span>&nbsp;Edit', 'class': 'btn btn-default btn-xs btn-edit-post') }}
						{{ link_to('delete/discussion/' ~ post.id ~ '?' ~ tokenKey ~ '=' ~ token, '<span class="glyphicon glyphicon-remove"></span>&nbsp;Delete', 'class': 'btn btn-default btn-xs btn-delete-post') }}&nbsp;
					{%- endif %}
					{%- if currentUser -%}
						{% if post.isSubscribed(currentUser) %}
							{{ link_to('unsubscribe/discussion/' ~ post.id ~ '?' ~ tokenKey ~ '=' ~ token, '<span class="glyphicon glyphicon glyphicon-eye-close"></span>&nbsp;Unsubscribe', "class": "btn btn-default btn-xs") }}
						{% else %}
							{{ link_to('subscribe/discussion/' ~ post.id ~ '?' ~ tokenKey ~ '=' ~ token, '<span class="glyphicon glyphicon-eye-open"></span>&nbsp;Subscribe', "class": "btn btn-default btn-xs") }}
						{% endif %}
						<a href="#" onclick="return false" class="btn btn-danger btn-xs vote-post-down" data-id="{{ post.id }}">
							<span class="glyphicon glyphicon-thumbs-down"></span>
							{{- post.votes_down -}}
						</a>
						<a href="#" onclick="return false" class="btn btn-success btn-xs vote-post-up" data-id="{{ post.id }}">
							<span class="glyphicon glyphicon-thumbs-up"></span>
							{{ post.votes_up }}
						</a>
					{%- else -%}
						<a href="#" onclick="return false" class="btn btn-danger btn-xs">
							<span class="glyphicon glyphicon-thumbs-down"></span>
							{%- if post.votes_down -%}
								<span itemprop="downvoteCount">{{ post.votes_down }}</span>
							{%- else -%}
								{{ post.votes_down }}
							{%- endif -%}
						</a>
						<a href="#" onclick="return false" class="btn btn-success btn-xs">
							<span class="glyphicon glyphicon-thumbs-up"></span>
							{%- if post.votes_up -%}
								<span itemprop="upvoteCount">{{ post.votes_up }}</span>
							{%- else -%}
								{{- post.votes_up -}}
							{%- endif -%}
						</a>
					{%- endif -%}
				</div>
			</div>
		</div>

		{%- for reply in post.replies -%}
			<div itemprop="suggestedAnswer{%- if reply.accepted == 'Y' -%} acceptedAnswer{%- endif -%}" itemscope itemtype="http://schema.org/Answer" class="reply-block row{% if (reply.votes_up - reply.votes_down) <= -3 %} reply-negative{% endif %}{% if (reply.votes_up - reply.votes_down) >= 4 %} reply-positive{% endif %}{% if reply.accepted == 'Y' %} reply-accepted{% endif %}">
				<div class="col-md-1 small" align="center">
					{{ image(gravatar.getAvatar(reply.user.email), 'width': 48, 'height': 48, 'class': 'img-rounded') }}<br>
					<span itemprop="author" itemscope itemtype="http://schema.org/Person">
						{{ link_to(
						'user/' ~ reply.user.id ~ '/' ~ reply.user.login,
						'<span itemprop="name">' ~ reply.user.name|e ~ '</span>',
						'class': 'user-moderator-' ~ reply.user.moderator
						) }}
					</span><br>
					<span class="karma">{{ reply.user.getHumanKarma() }}</span>
					{%- if reply.accepted == 'Y' -%}
						<div class="accepted-reply">
							<span class="glyphicon glyphicon-ok"></span>
							Accepted<br>answer
						</div>
					{%- endif -%}
				</div>
				<div class="col-md-11">
					{%- if reply.in_reply_to_id > 0 -%}
						{%- set inReplyTo = reply.postReplyTo -%}
						{%- if inReplyTo -%}
						<div class="in-reply-to">
							<a href="#C{{ reply.in_reply_to_id }}"><span class="glyphicon glyphicon-chevron-up"></span> in reply to
								{{ image(gravatar.getAvatar(inReplyTo.user.email), 'width': 24, 'height': 24, 'class': 'img-rounded') }}
								{{ inReplyTo.user.name }}
							</a>
						</div>
						{%- endif -%}
					{%- endif -%}
					<div class="posts-buttons" align="right">
						{%- if reply.edited_at > 0 -%}
							<span class="action-date action-reply-edit" data-id="{{ reply.id }}" data-toggle="modal" data-target="#historyModal">
								edited <span>{{ reply.getHumanEditedAt() }}</span>
							</span><br/>
						{%- endif -%}
						<a name="C{{ reply.id }}" href="#C{{ reply.id }}">
							<time itemprop="dateCreated" datetime="{{ date('c', reply.created_at) }}" class="action-date">{{ reply.getHumanCreatedAt() }}</time>
						</a>
					</div>
					<div class="post-content">
						{%- cache "reply-body-" ~ reply.id -%}
						<div itemprop="text">
							{{- markdown.render(reply.content|e) -}}
						</div>
						{%- endcache -%}
					</div>
					<div class="posts-buttons" align="right">
						{%- if currentUser == post.users_id or moderator == 'Y' -%}
							<br>
							{%- if post.accepted_answer != 'Y' -%}
								<a class="btn btn-default btn-xs reply-accept" data-id="{{ reply.id }}">
									<span class="glyphicon glyphicon-ok"></span>&nbsp;Accept Answer
								</a>&nbsp;
							{%- endif -%}
						{%- endif -%}
						{%- if reply.users_id == currentUser or moderator == 'Y' -%}
							<a class="btn btn-default btn-xs reply-edit" data-id="{{ reply.id }}">
								<span class="glyphicon glyphicon-pencil"></span>&nbsp;Edit
							</a>
							<a class="btn btn-default btn-xs reply-remove" data-id="{{ reply.id }}">
								<span class="glyphicon glyphicon-remove"></span>&nbsp;Delete
							</a>&nbsp;
						{%- endif -%}
						{%- if currentUser -%}
							{%- if reply.users_id != currentUser -%}
							<a class="btn btn-default btn-xs reply-reply" data-id="{{ reply.id }}">
								<span class="glyphicon glyphicon-share-alt"></span>&nbsp;Reply
							</a>&nbsp;
							{%- endif -%}
							<a href="#" onclick="return false" class="btn btn-danger btn-xs vote-reply-down" data-id="{{ reply.id }}">
								<span class="glyphicon glyphicon-thumbs-down"></span>
								{{ reply.votes_down }}
							</a>
							<a href="#" onclick="return false" class="btn btn-success btn-xs vote-reply-up" data-id="{{ reply.id }}">
								<span class="glyphicon glyphicon-thumbs-up"></span>
								{{ reply.votes_up }}
							</a>
						{%- else -%}
							<a href="#" onclick="return false" class="btn btn-danger btn-xs vote-login" data-id="{{ reply.id }}">
								<span class="glyphicon glyphicon-thumbs-down"></span>
								{%- if reply.votes_down -%}
									<span itemprop="downvoteCount">{{ reply.votes_down }}</span>
								{%- else -%}
									{{ reply.votes_down }}
								{%- endif -%}
							</a>
							<a href="#" onclick="return false" class="btn btn-success btn-xs vote-login" data-id="{{ reply.id }}">
								<span class="glyphicon glyphicon-thumbs-up"></span>
								{%- if reply.votes_up -%}
									<span itemprop="upvoteCount">{{ reply.votes_up }}</span>
								{%- else -%}
									{{ reply.votes_up }}
								{%- endif -%}
							</a>
						{%- endif -%}
					</div>
				</div>
			</div>
			{%- endfor -%}

			{%- if post.locked != 'Y' -%}
				<div class="row">
				{%- if currentUser -%}
					<div class="col-md-1 small" align="center">
						<img src="https://secure.gravatar.com/avatar/{{ session.get('identity-gravatar') }}?s=48&amp;r=pg&amp;d=identicon" class="img-rounded" width="48" height="48"><br>
						<span>{{ link_to('', 'You') }}</span>
					</div>
					<div class="col-md-11">

						<ul class="nav nav-tabs preview-nav">
							<li class="active"><a href="#" onclick="return false">Comment</a></li>
							<li><a href="#" onclick="return false">Preview</a></li>
							<li class="pull-right">{{ link_to('help/markdown', 'Help', 'target': '_blank') }}</li>
						</ul>

						<form method="post" autocomplete="off" role="form">
							{{ hidden_field(tokenKey, "value": token, "id": "csrf-token") }}
							<p>
								<div id="comment-box">
									{{- hidden_field('id', 'value': post.id) -}}
									{{- text_area("content", "rows": 5, "class": "form-control") -}}
								</div>
								<div id="preview-box" style="display:none"></div>
							</p>
							<p>
								<div class="pull-left">
									{{- link_to('', 'Back to discussions') -}}
								</div>
								<div class="pull-right">
									<button type="submit" class="btn btn-success">Add Comment</button>
								</div>
							</p>
						</form>
					</div>
				{%- else -%}
					<div class="col-md-1 small" align="center"></div>
					<div class="col-md-11 login-comment">
						<div class="pull-right">
							{{- link_to('login/oauth/authorize', 'Log In to Comment', 'class': 'btn btn-primary') -}}
						</div>
					</div>
				{%- endif -%}
				</div>
			{%- endif -%}

		</div>

		{{- hidden_field('post-id', 'value': post.id) -}}
		<div id="suggested-posts"></div>
		<div id="sticky-progress" style='display:none'></div>

	</div>

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

{%- if currentUser -%}
<div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="replyModalLabel" aria-hidden="true" data-backdrop="static"
   data-keyboard="false">
	<div class="modal-dialog">
		<form method="post" autocomplete="off" role="form">
			{{ hidden_field(tokenKey, "value": token) }}
			<div class="modal-content">

				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="replyModalLabel">Add Reply</h4>
				</div>

				<div class="modal-body" id="errorBody">
					<ul class="nav nav-tabs preview-nav">
						<li class="active"><a href="#" onclick="return false">Comment</a></li>
						<li><a href="#" onclick="return false">Preview</a></li>
						<li class="pull-right">{{ link_to('help/markdown', 'Help', 'parent': '_new') }}</li>
					</ul>
					<p>
						<div id="reply-comment-box">
							{{- hidden_field('id', 'value': post.id) -}}
							{{- hidden_field('reply-id') -}}
							<div id="comment-textarea"></div>
						</div>
						<div id="preview-box" style="display:none"></div>
					</p>
				</div>

				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<input type="submit" class="btn btn-success" value="Add Reply"/>
				</div>
			</div>
		</form>
	</div>
</div>
{%- endif -%}

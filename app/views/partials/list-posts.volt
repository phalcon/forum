
<div class="container">

	<ul class="nav nav-tabs">
		{% set orders = [
			'new': 'All discussions',
			'hot': 'Hot',
			'unanswered': 'Unanswered',
			'my': 'My discussions',
			'answers':'My answers'
		] %}
		{% for order, label in orders %}
			{% if (order == 'my' or order == 'answers') and !session.get('identity') %}
				{% continue %}
			{% endif %}
			{% if order == currentOrder %}
				<li class="active">
			{% else %}
				<li>
			{% endif %}
				{{ link_to('discussions/' ~ order, label) }}
			</li>
		{% endfor %}
	</ul>
</div>

{% if posts|length %}
<div class="container">
	<br/>
	<div class="table-responsive">
		<table class="table table-striped list-discussions" width="90%">
			<tr>
				<th width="50%">Topic</th>
				<th>Users</th>
				<th>Category</th>
				<th>Replies</th>
				<th>Views</th>
				<th>Created</th>
				<th>Last Reply</th>
			</tr>
		{% for post in posts %}
			<tr class="{% if (post.votes_up - post.votes_down) <= -10 %}post-negative{% endif %}">
				<td align="left">
					{% if post.sticked == "Y" %}<span class="glyphicon glyphicon-pushpin"></span>&nbsp;{% endif %}
					{{ link_to('discussion/' ~ post.id ~ '/' ~ post.slug, post.title|e) }}
				</td>
				<td>
					{% for id, user in post.getRecentUsers() %}
					 	<a href="{{ url("user/" ~ id ~ "/" ~ user[0]) }}" title="{{ user[0] }}">
							<img src="https://secure.gravatar.com/avatar/{{ user[1] }}?s=24&amp;r=pg&amp;d=identicon" class="img-rounded">
						</a>
					{% endfor %}
				</td>
				<td>
					<span class="author">{{ link_to('category/' ~ post.category.id ~ '/' ~ post.category.slug, post.category.name) }}</span>
				</td>
				<td align="center">
					<span class="big-number">{% if post.number_replies > 0 %}{{ post.number_replies }}{%endif %}</span>
				</td>
				<td align="center">
					<span class="big-number">{{ post.number_views }}</span>
				</td>
				<td>
					<span class="date">{{ post.getHumanCreatedAt() }}</span>
				</td>
				<td>
					<span class="date">{{ post.getHumanModifiedAt() }}</span>
				</td>
			</tr>
		{% endfor %}
		</table>
	</div>
</div>

<div class="container">
	<ul class="pager">
		{% if offset > 0 %}
			<li class="previous">{{ link_to(paginatorUri ~ '/' ~ (offset - 30), 'Prev') }}</li>
		{% endif %}

		{% if totalPosts.count > 30 %}
			<li class="next">{{ link_to(paginatorUri ~ '/' ~ (offset + 30), 'Next') }}</li>
		{% endif %}
	</ul>
</div>

{% else %}
	<div>There are no posts here</div>
{% endif %}

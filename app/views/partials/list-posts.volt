
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
				<th>Topic</th>
				<th>Users</th>
				<th>Category</th>
				<th>Replies</th>
				<th>Views</th>
				<th>Created</th>
			</tr>
		{% for post in posts %}
			<tr>
				<td align="left">
					{% if post.sticked == "Y" %}<span class="glyphicon glyphicon-pushpin"></span>&nbsp;{% endif %}
					{{ link_to('discussion/' ~ post.id ~ '/' ~ post.slug, post.title|e) }}
				</td>
				<td>
					{% for gravatar in post.getRecentUsers() %}
						<img src="https://secure.gravatar.com/avatar/{{ gravatar }}?s=24&amp;r=pg&amp;d=identicon" class="img-rounded">
					{% endfor %}
				</td>
				<td>
					<span class="author">{{ link_to('category/' ~ post.category.id ~ '/' ~ post.category.slug, post.category.name) }}</span>
				</td>
				<td class="number{% if !post.number_replies %} no-replies{%endif %}" align="center">
					<span class="big-number">{{ post.number_replies }}</span>
				</td>
				<td class="number{% if !post.number_views %} no-views{%endif %}" align="center">
					<span class="big-number">{{ post.number_views }}</span>
				</td>
				<td>
					<span class="date">{{ post.getHumanCreatedAt() }}</span>
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



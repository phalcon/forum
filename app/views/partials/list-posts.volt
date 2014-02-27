
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
			{% if (order == 'my' or order == 'answers' )  and !session.get('identity') %}
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
<table class="table table-striped" width="90%">
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
<div class="row">

{% if offset > 0 %}
	<div class="pagination prev">
		<ul>
			<li>{{ link_to(paginatorUri ~ '/' ~ (offset - 30), 'Prev') }}</li>
		</ul>
	</div>
{% endif %}

{% if totalPosts.count > 30 %}
	<div class="pagination next">
		<ul>
			<li>{{ link_to(paginatorUri ~ '/' ~ (offset + 30), 'Next') }}</li>
		</ul>
	</div>
{% endif %}

</div>

{% else %}
	<div>There are no posts here</div>
{% endif %}



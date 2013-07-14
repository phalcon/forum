<div class="row">
	<ul class="nav nav-tabs">
		{% set orders = [
			'new': 'All discussions',
			'hot': 'Hot',
			'unanswered': 'Unanswered',
			'my': 'My discussions',
                        'answers':'My answers'
		] %}
		{% for order, label in orders %}
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
<div class="row">
<table class="list-posts">
{% for post in posts %}
	<tr>
		<td class="number{% if !post.number_replies %} no-replies{%endif %}" align="center">
			<span class="big-number">{{ post.number_replies }}</span><br>
			replies
		</td>
		<td class="number{% if !post.number_views %} no-views{%endif %}" align="center">
			<span class="big-number">{{ post.number_views }}</span><br>
			views
		</td>
		<td align="left">
			<div class="post">
				<p>
					{{ link_to('discussion/' ~ post.id ~ '/' ~ post.slug, post.title|e) }}
				</p>
				<p>
					<div class="pull-left">
						<span class="date">{{ date('M d/Y', post.created_at) }}</span>
					</div>

					<div class="pull-right">
						<span class="author">category {{ link_to('category/' ~ post.category_id ~ '/' ~ post.category_slug, post.category_name) }}</span>
						<span class="author">author {{ link_to('user/' ~ post.user_id ~ '/' ~ post.user_login, post.user_name) }}</span>
					</div>
				</p>

			</div>
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

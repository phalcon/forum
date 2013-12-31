{{ content() }}

<div class="view-discussion">
	<p>
		<h1>Recent Activity</h1>
	</p>

	<table width="90%" align="center">
		<tr>
			<td colspan="3">
				<div class="row">
					<ul class="nav nav-tabs">
						{% set orders = [
							'': 'Forum',
							'/irc': 'IRC'
						] %}
						{% for order, label in orders %}
							{% if order == '/irc' %}
								<li class="active">
							{% else %}
								<li>
							{% endif %}
								{{ link_to('activity' ~ order, label) }}
							</li>
						{% endfor %}
					</ul>
				</div>
			</td>
		</tr>
		{% for activity in activities %}
		<tr>
			<td class="medium" valign="top">
				<span class="date">{{ date("Y/m/d h:i", activity.datelog )}}</span>
			</td>
			<td class="small" valign="top">
				{{ activity.who }}
			</td>
			<td>
				{{ activity.content|e }}
			</td>
		</tr>
		{% endfor %}
	</table>

</div>
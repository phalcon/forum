
<div class="container help">

	<ol class="breadcrumb">
		<li>{{ link_to('', 'Home') }}</a></li>
		<li>{{ link_to('help', 'Help') }}</a></li>
	</ol>

	<h1>Statistics</h1>

	{% cache "stats" 3600 %}
	<p>
		<table class="table table-stripped" align="center" style="width:300px">
			<tr>
				<td>Threads</td>
				<td align="right">{{ number_format(threads) }}</td>
			</tr>
			<tr>
				<td>Replies</td>
				<td align="right">{{ number_format(replies) }}</td>
			</tr>
			<tr>
				<td>Votes</td>
				<td align="right">{{ number_format(votes) }}</td>
			</tr>
			<tr>
				<td>Users</td>
				<td align="right">{{ number_format(users) }}</td>
			</tr>
			<tr>
				<td>Karma Points</td>
				<td align="right">{{ number_format(karma) }}</td>
			</tr>
			<tr>
				<td>Views</td>
				<td align="right">{{ number_format(views) }}</td>
			</tr>
			<tr>
				<td>User Notifications</td>
				<td align="right">{{ number_format(unotifications) }}</td>
			</tr>
			<tr>
				<td>E-Mail Notifications</td>
				<td align="right">{{ number_format(notifications) }}</td>
			</tr>
			<tr>
				<td>IRC Messages</td>
				<td align="right">{{ number_format(irc) }}</td>
			</tr>
		</table>
	</p>
	{% endcache %}

</div>
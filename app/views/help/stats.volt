<div class="help-container">
    <div class="row">
		{% include 'partials/breadcrumbs.volt' %}
        <div class="col-md-6 help-head">
            <h2>Statistics</h2>
            <section>
				{% cache "stats" 3600 %}
				<table class="table table-stripped" align="left" style="width:300px">
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
				{% endcache %}
            </section>
        </div>
        <div class="col-md-6 help-head">
            <h2>The most active users</h2>
            <section>
                {% cache "activity" 3600 %}
                <table class="table table-stripped" align="left" style="width:300px">
                    {% for i, activity in activities %}
                        <tr>
                            <td>
                                <a href="{{ url("user/" ~ activity.id ~ "/" ~ activity.login) }}" title="{{ activity.name }}">
                                    {{ activity.name }}
                                </a>
                            </td>
                            <td align="right">{{ i + 1 }}</td>
                        </tr>
                    {% endfor %}
                </table>
                {% endcache %}
            </section>
        </div>
    </div>
</div>

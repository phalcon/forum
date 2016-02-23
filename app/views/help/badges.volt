
<div class="help">

	{% include 'partials/breadcrumbs.volt' %}

	<h1>Badges</h1>

	<p>
		Badges are awards that reward users for their contributions, collaboration and participation in the forum.
		Badges enable the community to collectively identify the best contributors.
		This document explains available badges and when they're awarded to users.
	</p>

	<p>
		<table class="table table-stripped" align="center" style="width:700px">
			{% for badge in badges %}
			<tr>
				<td><button type="button" class="btn btn-default btn-sm">{{ badge.getName() }}</button></td>
				<td align="left">{{ badge.getDescription() }}</td>
			</tr>
			{% endfor %}
		</table>
	</p>

</div>

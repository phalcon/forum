
<div class="help">

	{% include 'partials/breadcrumbs.volt' %}

	<h1>Karma/Reputation</h1>

	<p>
		Karma or reputation is a scoring system that rewards users for their contributions, collaboration and participation in the forum.
		The forum awards points for almost any activity undertaken. Karma enable the community
		to collectively identify the best (and worst) contributions. This document explains how many points are given for each activity:
	</p>

	<div align="center">
		<table class="table table-stripped">
			<thead>
				<tr>
					<td><h3>General</h3></td>
				</tr>
			</thead>
			<thead>
				<tr>
					<th>Activity</th>
					<th>Points</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Signing up on the forum</td>
					<td><span class="label label-success">+45</span></td>
				</tr>
				<tr>
					<td>Logging in on the forum</td>
					<td><span class="label label-success">+5</span></td>
				</tr>
				<tr>
					<td>Viewing someone else's post</td>
					<td><span class="label label-success">+2</span></td>
				</tr>
				<tr>
					<td>Getting a visit on your post</td>
					<td><span class="label label-success">+1</span></td>
				</tr>
				<tr>
					<td>Submitting a new post</td>
					<td><span class="label label-success">+10</span></td>
				</tr>
				<tr>
					<td>Adding a reply on someone else's post</td>
					<td><span class="label label-success">+15</span></td>
				</tr>
				<tr>
					<td>Getting a reply by someone else on your post</td>
					<td><span class="label label-success">+5</span></td>
				</tr>
				<tr>
					<td>Getting an own reply as 'accepted answer' by someone else</td>
					<td><span class="label label-success">50 + abs(user_karma - your_karma) / 1000</span></td>
				</tr>
				<tr>
					<td>Accepting someone else's reply as 'accepted answer'</td>
					<td><span class="label label-success">+10</span></td>
				</tr>
				<tr>
					<td>Voting someone else's post (positive or negative)</td>
					<td><span class="label label-success">+10</span></td>
				</tr>
				<tr>
					<td>Getting a positive vote by someone else on your post</td>
					<td><span class="label label-success">5 + abs(user_karma - your_karma) / 1000</span></td>
				</tr>
				<tr>
					<td>Getting a negative vote by someone else on your post</td>
					<td><span class="label label-danger">-(5 + abs(user_karma - your_karma) / 1000)</span></td>
				</tr>
				<tr>
					<td>Getting a positive vote by original poster on your comment</td>
					<td><span class="label label-success">15 + abs(user_karma - your_karma) / 1000</span></td>
				</tr>
				<tr>
					<td>Getting a negative vote by original poster on your comment</td>
					<td><span class="label label-danger">-(15 + abs(user_karma - your_karma) / 1000)</span></td>
				</tr>
				<tr>
					<td>Getting a positive vote by someone else on your comment</td>
					<td><span class="label label-success">10 + abs(user_karma - your_karma) / 1000</span></td>
				</tr>
				<tr>
					<td>Getting a negative vote by someone else on your comment</td>
					<td><span class="label label-danger">-(10 + abs(user_karma - your_karma) / 1000)</span></td>
				</tr>
				<tr>
					<td>Deleting a post</td>
					<td><span class="label label-danger">-15</span></td>
				</tr>
				<tr>
					<td>Deleting a reply</td>
					<td><span class="label label-danger">-15</span></td>
				</tr>
			</tbody>
		</table>
	</div>

	<div align="center">
		<table class="table table-stripped">
			<thead>
				<tr>
					<td><h3>Specific for Moderators</h3></td>
				</tr>
			</thead>
			<thead>
				<tr>
					<th>Activity</th>
					<th>Points</th>
				</tr>
				<tr>
					<td>Improving a post or commentary, moving a post to the right category</td>
					<td><span class="label label-success">+25</span></td>
				</tr>
				<tr>
					<td>Deleting an offensive or spammy post or comment</td>
					<td><span class="label label-success">+10</span></td>
				</tr>
			</thead>
		</table>
	</div>

	<hr>

	<h3>Advantages or Karma/Reputation</h3>
	<ul>
		<li>Earn respect from the community</li>
		<li>Increase the influence of your arguments or opinions based on your historical contributions</li>
		<li>Your posts and answers will gain greater visibility in the forum</li>
		<li>Potentially be promoted to moderator</li>
	</ul>

</div>

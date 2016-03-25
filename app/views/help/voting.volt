<div class="help-container">
	<div class="row">
		{% include 'partials/breadcrumbs.volt' %}
		<div class="col-md-8 col-md-offset-2 help-head">
			<h1>Feedback system</h1>
			<section>
				<p>
					Posts and comments can be voted up or down. Voting enable the community to
					collectively identify the best (and worst) contributions. However, votes aren't unlimited.
					Every time you win 50 points of karma the forum assign you a vote.
					You can only vote once every post or comment. You can spend
					your votes by voting positively or negatively posts and comments in the forum.
				</p>

				<p>
					When your posts or comments have been voted your karma is increased or decreased depending on the karma of who you get the vote.
					When you receive votes from the original poster you get an extra number of points on your karma.
				</p>

				<p>
					You can see how many votes you have on your {{ link_to('settings', 'settings') }} page.
				</p>
			</section>
		</div>
	</div>
</div>

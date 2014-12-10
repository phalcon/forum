{{ content() }}

<hr>

<div align="center" class="container">
	<div class="user-profile">
		<table align="center">
			<tr>
				<td class="small hidden-xs" valign="top">
					<img src="https://secure.gravatar.com/avatar/{{ user.gravatar_id }}?s=64&amp;r=pg&amp;d=identicon" class="img-rounded"
					width="64" height="64">
				</td>
				<td align="left" valign="top">
					<h1>{{ user.name|e }}</h1>
					<p>
						<span>joined <b>{{ date('M d/Y', user.created_at) }}</b></span><br>
						<span>posts <b>{{ numberPosts }}</b></span> / <span>replies <b>{{ numberReplies }}</b></span><br>
						<span>reputation <b>{{ user.karma }}</b></span><br>
						<span>votes available <b>{{ user.votes }}</b></span><br>
						<span>voting points <b>{{ user.votes_points }}/50</b></span><br>
					</p>
					<hr>
					<p>
						<ul class="nav nav-tabs">
							<li class="active"><a href="#">Settings</a><li>
						</ul>
					</p>
					<p>
						<div class="tab-content">
							<div class="tab-pane active" id="settings">
								<form method="post" role="form">
									{{ hidden_field(security.getTokenKey(), "value": security.getToken()) }}
									<div class="form-group">
										<label for="timezone">Timezone</label>
										{{ select_static('timezone', timezones, 'class': 'form-control') }}
									</div>
									<div class="form-group">
										<label for="notifications">E-Mail Notifications</label>
										{{ select_static('notifications', [
											'N': 'Never receive an e-mail notification',
											'Y': 'Receive e-mail notifications from all new threads and comments',
											'P': 'When someone replies to a discussion that I started or replied to'
										], 'class': 'form-control') }}
									</div>
									<div class="form-group">
										<label for="digest">Weekly Digest</label>
										{{ select_static('digest', [
											'Y': 'Yes',
											'N': 'No'
										], 'class': 'form-control') }}
									</div>
									<div class="form-group">
										<label for="digest">Code Highlight Theme</label>
										{{ select_static('theme', [
											'D': 'Dark',
											'L': 'Light'
										], 'class': 'form-control') }}
									</div>
									<div class="form-group">
										<a href="https://en.gravatar.com/">Change your avatar at Gravatar</a>
									</div>
									<div class="form-group">
										<input type="submit" class="btn btn-success" value="Save"/>
									</div>
								</form>
							</div>
						</div>
					</p>
				</td>
			</tr>
		</table>
	</div>
</div>

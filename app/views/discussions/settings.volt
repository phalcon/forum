{{ content() }}

<hr>

<div align="center">
	<div class="user-profile">
		<table align="center">
			<tr>
				<td class="small remove-image" valign="top">
					<img src="https://secure.gravatar.com/avatar/{{ user.gravatar_id }}?s=64&amp;r=pg&amp;d=identicon" class="img-rounded">
				</td>
				<td align="left" valign="top">
					<h1>{{ user.name|e }}</h1>
					<p>
						<span>joined <b>{{ date('M d/Y', user.created_at) }}</b></span><br>
						<span>posts <b>{{ numberPosts }}</b></span> / <span>replies <b>{{ numberReplies }}</b></span><br>
						<span>reputation <b>{{ user.karma }}</b></span><br>
						<span>votes available <b>{{ user.votes }}</b></span><br>
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
									<div class="form-group">
										<label for="timezone">Timezone</label>
										{{ select_static('timezone', timezones, 'class': 'form-control') }}
									</div>
									<div class="form-group">
										<label for="notifications">E-Mail Notifications</label>
										{{ select_static('notifications', [
											'N': 'Never',
											'Y': 'Always',
											'P': 'Someone replied my posts / Someone has replied in posts that I has replied'
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
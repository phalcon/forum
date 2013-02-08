<div class="navbar">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			{{ link_to('', 'Phosphorum', 'class': 'brand') }}
			<div class="nav-collapse">
				<ul class="nav">
					<li>{{ link_to('', 'Discussions') }}</li>
					<li>{{ link_to('activity', 'Activity') }}</li>
					{% if session.get('identity') %}
					<li>{{ link_to('settings', 'Settings') }}</li>
					<li>{{ link_to('logout', 'Logout') }}</li>
					{% endif %}
				</ul>
				<ul class="nav pull-right mobile-dissapear">
					<li>
						<div align="center">
							<form class="form-inline" action="{{ url('search') }}" method="get">
								<input type="text" class="input-medium search-query" name="q" placeholder="Search"/>
							</form>
						</div>
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
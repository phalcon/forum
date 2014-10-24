{% if actionName == 'index' %}
<div class="clearfix">
	<div class="col-lg-9  center-block">
		<div class="panel panel-default">
		  <!-- Default panel contents -->
		  <div class="panel-heading">Statistics</div>
			  <div class="panel-body">
				  Our users have posted a total of <b>{{ threads }}</b> Posts<br>
				  We <b> {{users}} </b> registered users<br>
				  The newest member is <b>{{ users_latest }}</b>
			  </div>
		</div>
	</div>
</div>
{% endif %}
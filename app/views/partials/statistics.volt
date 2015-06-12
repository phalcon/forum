{% if actionName == 'index' %}
<div class="container">
	<div class="span8">
		<div class="panel panel-default">
		  <!-- Default panel contents -->
		  <div class="panel-heading">{{ t('Statistics') }}</div>
			  <div class="panel-body">
				  Our users have posted a total of <b>{{ threads }}</b> Posts<br>
				  We <b> {{users}} </b> registered users<br>
				  The newest member is <b>{{ users_latest }}</b>
			  </div>
			  <div class="panel-footer">
				  <small>{{ t('Last Thread') }}</small>
			  </div>
			   <div class="panel-body">
			   {%- for last_thread in last_threads -%}
				  {{- link_to('discussion/' ~ last_thread.id_post ~ '/' ~ last_thread.slug_post, last_thread.title_post|e) -}}&nbsp; posted by {{ last_thread.name_user }} ({{ last_thread.name_category }})<br>
			   {%- endfor -%}
			  </div>
		</div>
	</div>
	<div class="span4"></div>
</div>
{% endif %}
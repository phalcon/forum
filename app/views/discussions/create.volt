{{ content() }}

<div class="start-discussion">

	<div align="left">
		<h1>Start a Discussion</h1>
	</div>

	<div class="row">
		<div class="span1 remove-image">
			<img src="https://secure.gravatar.com/avatar/{{ session.get('identity-gravatar') }}?s=48" class="img-rounded">
		</div>
		<div class="span9">
			<form method="post" autocomplete="off">

			  <p>
				{{ text_field("title", "placeholder": "Title") }}
			  </p>


			  <p>

			  	<ul class="nav nav-tabs preview-nav">
					<li class="active"><a href="#" onclick="return false">Write</a></li>
					<li><a href="#" onclick="return false">Preview</a></li>
					<li class="pull-right">{{ link_to('help', 'Help', 'class': 'help') }}</li>
				</ul>

				<div id="comment-box">
					{{ text_area("content", "rows": 15, "placeholder": "Leave the content") }}
				</div>
				<div id="preview-box" style="display:none"></div>
			  </p>

			  <p>
			  	<div class="pull-left">
					{{ link_to('', 'Back to discussions') }}
				</div>
			  	<div class="pull-right">
					<button type="submit" class="btn btn-success">Submit Discussion</button>
				</div>
			  </p>

			</form>
		</div>
	</div>
</div>
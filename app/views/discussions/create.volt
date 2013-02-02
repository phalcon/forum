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
			  	{{ select("categoryId", categories, 'using': ['id', 'name'], 'useEmpty': true, 'emptyText': 'Choose a category...') }}
			  </p>

			  <p>
				{{ text_area("content", "rows": 15, "placeholder": "Leave the content") }}
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
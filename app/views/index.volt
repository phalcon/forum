<!DOCTYPE html>
<html lang="en">
	<head>
		<title>{{ get_title(false) }} - Phalcon Framework</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		{%- if canonical is defined -%}
		<link rel="canonical" href="//forum.phalconphp.com/{{ canonical }}"/>
		{%- endif -%}
		{{- stylesheet_link("//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css", false) -}}
		{{- stylesheet_link("//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.css", false) -}}
		{{- stylesheet_link("css/theme.css", true) -}}
		{{- stylesheet_link("css/editor.css", true) -}}
		{{- stylesheet_link("css/diff.css", true) -}}
		{{- stylesheet_link("css/style.css", true) -}}
	</head>
	<body>
		{{ content() }}
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
		<script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.js"></script>
		{{ javascript_include("js/editor.js") }}
		{{ javascript_include("js/forum.js") }}
		<script type="text/javascript">Forum.initializeView('{{ url() }}');</script>
	</body>
</html>
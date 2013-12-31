<!DOCTYPE html>
<html lang="en">
	<head>
		<title>{{ get_title(false) }} - Phalcon Framework</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		{%- if canonical is defined -%}
		<link rel="canonical" href="http://forum.phalconphp.com/{{ canonical }}"/>
		{%- endif -%}
		<link href='http://fonts.googleapis.com/css?family=Ubuntu:400,500,700,300italic,400italic,500italic&amp;subset=latin,cyrillic-ext' rel='stylesheet' type='text/css'>
		{{- stylesheet_link("css/s.css?v=2") -}}
		{# {{ stylesheet_link("css/style.css?v=2") }} #}
	</head>
	<body>
		{{ content() }}
		{# {{ javascript_include("js/jquery.min.js") }}
		{{ javascript_include("bootstrap/js/bootstrap.min.js") }}
		{{ javascript_include("js/twitter.min.js") }}
		{{ javascript_include("js/gs.js") }}
		{{ javascript_include("js/forum.js") }} #}
		{{- javascript_include("js/j.js") -}}
		<script type="text/javascript">Forum.initializeView('{{ url() }}');</script>
	</body>
</html>
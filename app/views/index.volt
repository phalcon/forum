<!DOCTYPE html>
<html lang="en">
	<head>
		<title>{{ get_title(false) }} - Phalcon Framework</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		{%- if canonical is defined -%}
		<link rel="canonical" href="//forum.phalconphp.com/{{ canonical }}"/>
		{%- endif -%}

		{%- if post is defined -%}
		<link rel="author" href="https://github.com/{{ post.user.login }}">
		<link rel="publisher" href="https://forum.phalconphp.com/">
		{%- endif -%}

		{%- if canonical is defined -%}
		<meta property="og:url" content="//forum.phalconphp.com/{{ canonical }}">
		<meta property="og:site_name" content="Phosphorum">
		{%- endif -%}

		{#- CSS resources from jsdelivr cannot be combined due to Bootstrap icons -#}
		{{- stylesheet_link("//cdn.jsdelivr.net/bootstrap/3.1.1/css/bootstrap.min.css", false) -}}
		{{- stylesheet_link("//cdn.jsdelivr.net/prettify/0.1/prettify.css", false) -}}
		{{- stylesheet_link("css/theme.css?v=2.0.3", true) -}}
		{{- stylesheet_link("css/editor.css?v=2.0.3", true) -}}
		{{- stylesheet_link("css/diff.css?v=2.0.3", true) -}}
		{{- stylesheet_link("css/style.css?v=2.0.3", true) -}}
	</head>
	<body>
		{{ content() }}
		<script type="text/javascript" src="//cdn.jsdelivr.net/g/jquery@2.1,bootstrap@3.1,prettify@0.1(prettify.js+lang-css.js+lang-sql.js)"></script>
		{{ javascript_include("js/editor.js?v=2.0.3") }}
		{{ javascript_include("js/forum.js?v=2.0.3") }}
		{{ javascript_include("js/gs.js?v=2.0.3") }}
		<script type="text/javascript">Forum.initializeView('{{ url() }}');</script>
	</body>
</html>

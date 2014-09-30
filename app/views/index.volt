<!DOCTYPE html>
<html lang="en">
	<head>
		{% set url = url() %}

		<title>{{ get_title(false) }} - {{ config.site.name }}</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		{%- if canonical is defined -%}
		<link rel="canonical" href="{{ config.site.url }}{{ canonical }}"/>
		{%- endif -%}

		{%- if post is defined -%}
		<link rel="author" href="https://github.com/{{ post.user.login }}">
		<link rel="publisher" href="http://{{ config.site.url }}/">
		{%- endif -%}

		{%- if canonical is defined -%}
		<meta property="og:url" content="{{ config.site.url }}/{{ canonical }}">
		<meta property="og:site_name" content="Phosphorum">
		{%- endif -%}

		<style type="text/css">
			@font-face {
				font-family: 'icomoon';
					src:url('{{ url }}fonts/icomoon.wofficomoon.eot');
					src:url('{{ url }}/fonts/icomoon.eot?#iefix') format('embedded-opentype'),
					url('{{ url }}/fonts/icomoon.woff') format('woff'),
					url('{{ url }}/fonts/icomoon.ttf') format('truetype'),
					url('{{ url }}/fonts/icomoon.svg#icomoon') format('svg');
				font-weight: normal;
				font-style: normal;
			}
		</style>

		{#- CSS resources from jsdelivr cannot be combined due to Bootstrap icons -#}
		{{- stylesheet_link("//cdn.jsdelivr.net/bootstrap/3.1.1/css/bootstrap.min.css", false) -}}
		{{- stylesheet_link("//cdn.jsdelivr.net/prettify/0.1/prettify.css", false) -}}
		{{- stylesheet_link("css/theme.css?v=2.0.5", true) -}}
		{{- stylesheet_link("css/editor.css?v=2.0.5", true) -}}
		{{- stylesheet_link("css/diff.css?v=2.0.5", true) -}}
		{{- stylesheet_link("css/style.css?v=2.0.5", true) -}}
	</head>
	<body>
		{{ content() }}
		<script type="text/javascript" src="//cdn.jsdelivr.net/g/jquery@2.1,bootstrap@3.1,prettify@0.1(prettify.js+lang-css.js+lang-sql.js)"></script>
		{{ javascript_include("js/editor.js?v=2.0.6") }}
		{{ javascript_include("js/forum.js?v=2.0.6") }}
		{{ javascript_include("js/gs.js?v=2.0.6") }}
		<script type="text/javascript">Forum.initializeView('{{ url() }}');</script>
	</body>
</html>

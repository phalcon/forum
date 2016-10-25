<!DOCTYPE html>
<html lang="en">
	<head>
		{%- set url = url(), theme = session.get('identity-theme') -%}
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title>{{ get_title(false) ~ ' - ' ~ config.site.name }}</title>

		<meta content="{{ config.site.keywords }}" name="keyword">
		<meta content="{{ config.site.description }}" name="description">
		<meta name=generator content="Phalcon Framework {{ version() }}">

		{%- if canonical is defined -%}
		<link rel="canonical" href="{{ config.site.url }}/{{ canonical }}">
		{%- endif -%}

		{%- if post is defined -%}
		<link rel="author" href="https://github.com/{{ post.user.login }}">
		<link rel="publisher" href="{{ config.site.url }}/">
		{%- endif -%}

		{%- if canonical is defined -%}
		<meta property="og:url" content="{{ config.site.url }}/{{ canonical }}">
		<meta property="og:site_name" content="Phosphorum">
		{%- endif -%}

		{#- Embed this font here to avoid Cross-Site issues -#}
		<style type="text/css">
			@font-face {
				font-family: 'icomoon';
					src:url('{{ url }}fonts/icomoon.wofficomoon.eot');
					src:url('{{ url }}fonts/icomoon.eot?#iefix') format('embedded-opentype'),
					url('{{ url }}fonts/icomoon.woff') format('woff'),
					url('{{ url }}fonts/icomoon.ttf') format('truetype'),
					url('{{ url }}fonts/icomoon.svg#icomoon') format('svg');
				font-weight: normal;
				font-style: normal;
			}
		</style>

		{#- CSS resources from jsdelivr cannot be combined due to Bootstrap icons -#}
		{{- stylesheet_link("//cdn.jsdelivr.net/bootstrap/3.3.6/css/bootstrap.min.css", false) -}}
		{{- stylesheet_link("//cdn.jsdelivr.net/prettify/0.1/prettify.css", false) -}}
		{%- if theme == 'L' -%}
		{{- stylesheet_link("css/theme-white.css?v=" ~ app_version, true) -}}
		{%- else -%}
		{{- stylesheet_link("css/theme.css?v=" ~ app_version, true) -}}
		{%- endif -%}
		{{- stylesheet_link("css/editor.css?v=" ~ app_version, true) -}}
		{{- stylesheet_link("css/fonts.css?v=" ~ app_version, true) -}}
		{{- stylesheet_link("css/diff.css?v=" ~ app_version, true) -}}
		{{- stylesheet_link("css/style.css?v=" ~ app_version, true) -}}
		{#- reCaptcha -#}
		{%- if recaptcha.isEnabled() -%}
			{{- recaptcha.getJs() -}}
		{%- endif -%}
	</head>
	<body class="with-top-navbar">
		{{ content() }}
		<script type="text/javascript" src="//cdn.jsdelivr.net/g/jquery@2.2.4,bootstrap@3.3.6,prettify@0.1(prettify.js+lang-css.js+lang-sql.js+lang-yaml.js)"></script>
		{{ javascript_include("js/editor.js?v=" ~ app_version) }}
		{{ javascript_include("js/forum.js?v=" ~ app_version) }}
		{{ javascript_include("js/gs.js?v=" ~ app_version) }}
		<script type="text/javascript">Forum.initializeView('{{ url() }}');</script>
	</body>
</html>

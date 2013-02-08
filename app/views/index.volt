<!DOCTYPE html>
<html lang="en">
	<head>
		<title>{{ get_title(false) }} - Phalcon Framework</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		{% if canonical is defined %}
		<link rel="canonical" href="http://forum.phalconphp.com/{{ canonical }}"/>
		{% endif %}
		{{ stylesheet_link("css/s.css") }}
	</head>
	<body>
		{{ content() }}
		{{ javascript_include("js/j.js") }}
		<script type="text/javascript">Forum.initializeView('{{ url() }}');</script>
	</body>
</html>
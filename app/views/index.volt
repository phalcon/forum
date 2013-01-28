<!DOCTYPE html>
<html lang="en">
	<head>
		<title>{{ get_title(false) }} - Phalcon Framework</title>
		{{ stylesheet_link("bootstrap/css/bootstrap.min.css") }}
		{{ stylesheet_link("css/style.css") }}
	</head>
	<body>
		{{ content() }}
		{{ javascript_include("bootstrap/js/bootstrap.min.js") }}
		<script src="http://phalconphp.com/javascript/gs.js" type="text/javascript"></script>
		<script src="http://phalconphp.com/javascript/twitter.min.js" type="text/javascript"></script>
	</body>
</html>
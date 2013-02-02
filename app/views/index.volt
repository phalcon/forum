<!DOCTYPE html>
<html lang="en">
	<head>
		<title>{{ get_title(false) }} - Phalcon Framework</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		{{ stylesheet_link("bootstrap/css/bootstrap.min.css") }}
		{{ stylesheet_link("bootstrap/css/bootstrap-responsive.min.css") }}
		{{ stylesheet_link("css/style.css") }}
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	</head>
	<body>
		{{ content() }}
		{{ javascript_include("bootstrap/js/bootstrap.min.js") }}
		<script src="http://phalconphp.com/javascript/gs.js" type="text/javascript"></script>
		<script src="http://phalconphp.com/javascript/twitter.min.js" type="text/javascript"></script>
	</body>
</html>
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

		<style type="text/css">
			.navbar-reverse .navbar-nav>li>a {
				font-size: 18px;
			}
			.navbar-reverse .navbar-nav>li>a.btn-default {
				font-size: 12px;
				padding: 5px;
				margin: 5px;
				margin-top: 10px;
			}

			h1 {
				font-size: 30px;
				margin-bottom: 15px;
			}

			.action-date {
				font-size: 11px;
				color: #717171;
			}

			table.discussion>tbody>tr>td {
				padding-bottom: 20px;
			}

			p {
				margin: 10px 0 15px;
			}

			.posts-buttons {
				float: right;
			}

			.posts-buttons a:hover {
				text-decoration: underline;
			}

			table.discussion .small {
				width: 120px;
				text-align: center;
			}

			table.discussion .small a {
				color: #999999;
			}

			table.discussion .post-editor {
				padding-left: 7px;
			}

			.CodeMirror {
				height: 200px;
				border: 1px solid #c0c0c0;
			}

			table.discussion .CodeMirror {
				height: 120px;
			}

			pre.prettyprint {
				padding: 10px;
			}

			table.discussion img {
				max-width: 550px;
				max-height: 600px;
			}

			.action-edit {
				color: #ea7c62;
				cursor: pointer;
			}

			.post-content {
				max-width: 900px;
				overflow-y: scroll;
			}

			@media (min-width: 1280px) {
				.modal-dialog {
					width: 1024px;
				}
			}

			@media (min-width: 1024px) {
				.modal-dialog {
					width: 900px;
				}
			}

			#historyBody {
				max-height: 550px;
				overflow-y: scroll;
			}

		</style>
	</head>
	<body>
		{{ content() }}
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js"></script>
		<script type="text/javascript" src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/prettify/r298/prettify.min.js"></script>
		{{ javascript_include("js/editor.js") }} }
		{{ javascript_include("js/forum.js") }} }
		<script type="text/javascript">Forum.initializeView('{{ url() }}');</script>
	</body>
</html>
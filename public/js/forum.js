
var Forum = {

	_shMain: false,

	_shCss: false,

	_sh: {},

	_shDocument: 0,

	getSh: function(type, code)
	{
		var start = false;

		switch (type) {
			case 'php':
				pre_code = '<pre class="sh_php sh_sourceCode">';
				break;
			case 'css':
				pre_code = '<pre class="sh_css sh_sourceCode">';
				break;
			case 'js':
			case 'javascript':
				pre_code = '<pre class="sh_javascript sh_sourceCode">';
				type = 'javascript';
				break;
			case 'html':
				pre_code = '<pre class="sh_html sh_sourceCode">';
				break;
			case 'sh':
			case 'bash':
				pre_code = '<pre class="sh_sh sh_sourceCode">';
				type = 'sh';
				break;
			case 'sql':
			case 'phql':
				pre_code = '<pre class="sh_sql sh_sourceCode">';
				type = 'sql';
				break;
			default:
				pre_code = '<pre class="sh_sourceCode">';
				break;
		}

		if (!Forum._shCss) {
			var link = document.createElement('link');
			link.type = 'text/css';
			link.rel = 'stylesheet';
			link.href = 'http://phalconphp.com/sh/css/sh_zenburn.css';
			document.body.appendChild(link);
			Forum._shCss = true;
		}

		if (!Forum._shMain) {
			var script = document.createElement('script');
			script.type = "text/javascript";
			script.src = "http://phalconphp.com/sh/sh_main.js"
			document.body.appendChild(script);
			Forum._shDocument++;
			Forum._shMain = true;
			start = true;
		}

		if (typeof Forum._sh[type] === "undefined") {
			var script = document.createElement('script');
			script.type = "text/javascript";
			script.src = "http://phalconphp.com/sh/lang/sh_" + type + ".min.js"
			document.body.appendChild(script);
			Forum._shDocument++;
			Forum._sh[type] = true;
		}

		return pre_code + code + '</pre>';
	},

	highlight: function()
	{
		$('div.post-content').each(function(position, element){
			for (var i=0; i <= 10; i++) {
				var matches = /```([a-z]+)([^`]+)```/gm.exec(element.innerHTML);
				if (matches === null) {
					break;
				}
				var code = Forum.getSh(matches[1], matches[2].replace(new RegExp('<br>', 'g'), ""));
				element.innerHTML = element.innerHTML.replace(matches[0], code);
			}
		});
		if (Forum._shDocument > 0) {
			window.setTimeout(function(){
				sh_highlightDocument();
			}, 500);
		}
	},

	editComment: function()
	{

	}

};
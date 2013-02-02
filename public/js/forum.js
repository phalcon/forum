
/**
 * Forum
 */
var Forum = {

	_uri: '',

	_shMain: false,

	_shCss: false,

	_sh: {},

	_shDocument: 0,

	/**
	 * Starts the highlighters
	 */
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
				type = null;
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

		if (type !== null) {
			if (typeof Forum._sh[type] === "undefined") {
				var script = document.createElement('script');
				script.type = "text/javascript";
				script.src = "http://phalconphp.com/sh/lang/sh_" + type + ".min.js"
				document.body.appendChild(script);
				Forum._shDocument++;
				Forum._sh[type] = true;
			}
		}

		return pre_code + code + '</pre>';
	},

	parseContent: function(html)
	{
		html = html.replace(/```([a-z]+)([^`]+)```(<br>|\n)?/gm, function($0, $1, $2) {
			return Forum.getSh($1, $2.replace(/<br>/g, ""));
		});

		html = html.replace(/```([^`]+)```(<br>|\n)?/gm, function($0, $1, $2) {
			return Forum.getSh(null, $2.replace(/<br>/g, ""));
		});

		//Replace URLs
		html = html.replace(/[a-z]+:\/\/[^\s<>\$]+/g, '<a href="$&">$&</a>');

		//Create links to docs
		html = html.replace(/Phalcon\\[a-zA-Z0-9\\]+/g, function($0) {
			return '<a href="http://docs.phalconphp.com/en/latest/api/' + $0.replace(/\\/g, '_') + '.html">' + $0 + '</a>';
		});

		//Replace user names
		html = html.replace(/[^\w]@(\w+)[^\w\(]/g, function($0, $1) {
			return '<a href="' + Forum._uri + 'user/0/' + $1 + '">' + $0 + '</a>';
		});

		return html;
	},

	/**
	 * Highlights texts enclosed into triple backticks
	 */
	highlight: function()
	{
		$('div.post-content').each(function(position, element){
			element.innerHTML = Forum.parseContent(element.innerHTML);
		});

		if (Forum._shDocument > 0) {
			window.setTimeout(function(){
				sh_highlightDocument();
			}, 500);
		}
	},

	makeCommentEditable: function(response)
	{
		if (response.status == 'OK') {

			var form = document.createElement('FORM');
			form.className = 'edit-form';
			form.method = 'POST';
			form.action = Forum._uri + 'reply/update';

			var textarea = document.createElement('TEXTAREA');
			textarea.name = 'content';
			textarea.rows = 7;
			textarea.value = response.comment;
			form.appendChild(textarea);

			var hidden = document.createElement('INPUT');
			hidden.name = 'id';
			hidden.type = 'hidden';
			hidden.value = response.id;
			form.appendChild(hidden);

			var cancel = document.createElement('INPUT');
			cancel.type = 'button';
			cancel.className = 'btn btn-small pull-left';
			cancel.value = 'Cancel';
			$(cancel).bind('click', { form: form, element: this}, Forum.cancelEditing);
			form.appendChild(cancel);

			var submit = document.createElement('INPUT');
			submit.type = 'buttom';
			submit.className = 'btn btn-success btn-small pull-right';
			submit.value = 'Update Comment';
			$(submit).bind('click', { form: form }, function(event) {
				this.disabled = true;
				event.data.form.submit();
			});
			form.appendChild(submit);

			this.hide();

			this.parent().append(form);
		}
	},

	/**
	 * Cancels the comment editing
	 */
	cancelEditing: function(event)
	{
		//Are you sure you want to delete this?
		var element = $(event.data.element);
		var form = $(event.data.form);

		element.show();
		form.remove();
	},

	/**
	 * Deletes a comment
	 */
	deleteComment: function(event)
	{
		if (confirm('Are you sure you want to delete this?')) {
			var element = $(event.data.element);
			window.location = Forum._uri + 'reply/delete/' + element.data('id');
		}
	},

	/**
	 * Converts the post-comment div into an editable textarea
	 */
	editComment: function(event)
	{
		var element = $(event.data.element);

		var content = $('div.post-content', element.parents()[3]);

		if (content.is(':visible')) {
			$.ajax({
				dataType: 'json',
				url: Forum._uri + 'reply/' + element.data('id'),
				context: content,
			}).done(Forum.makeCommentEditable);
		}
	},

	changeCommentTab: function(event)
	{

		event.data.links.each(function(position, element){
			$(element).removeClass('active');
		});

		$(this).addClass('active');

		if ($('a', this)[0].innerHTML == 'Preview') {

			var content = $('textarea', '#comment-box')[0].value;
			if (content !== '') {
				content = content.replace('\n', '<br>');
				$('#preview-box')[0].innerHTML = Forum.parseContent(content);
			} else {
				$('#preview-box')[0].innerHTML = 'Nothing to preview'
			}

			$('pre', '#preview-box').each(function(postion, element){
				if (typeof sh_languages['php'] !== "undefined") {
					sh_highlightElement(element, sh_languages['php']);
				}
			});

			$('#comment-box').hide();
			$('#preview-box').show();

		} else {
			$('#comment-box').show();
			$('#preview-box').hide();
		}
	},

	/**
	 * Add callbacks to edit/delete buttons
	 */
	addCallbacks: function()
	{
		$('i.reply-edit').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.editComment);
		});
		$('i.reply-remove').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.deleteComment);
		});

		var previewNavLinks = $('ul.preview-nav li');
		previewNavLinks.each(function(position, element) {
			$(element).bind('click', {links: previewNavLinks}, Forum.changeCommentTab);
		});
	},

	/**
	 * Initializes the view (highlighters, callbacks, etc)
	 */
	initializeView: function(uri)
	{
		Forum._uri = uri;
		Forum.highlight();
		Forum.addCallbacks();
	}

};
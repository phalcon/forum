
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

	/**
	 * Highlights texts enclosed into triple backticks
	 */
	highlight: function()
	{
		$('div.post-content').each(function(position, element){

			//Replace Code
			while (true) {
				var matches = /```([a-z]+)([^`]+)```(<br>|\n)?/gm.exec(element.innerHTML);
				if (!matches) {
					break;
				}
				var code = Forum.getSh(matches[1], matches[2].replace(new RegExp('<br>', 'g'), ""));
				element.innerHTML = element.innerHTML.replace(matches[0], code);
			}

			//Replace URLs
			element.innerHTML = element.innerHTML.replace(/[a-z]+:\/\/[\S]+/g, '<a href="$&" target="_new">$&</a>');
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
			submit.type = 'submit';
			submit.className = 'btn btn-success btn-small pull-right';
			submit.value = 'Update Comment';
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

	/**
	 * Add callbacks to edit/delete buttons
	 */
	addCallbacks: function()
	{
		$('i.reply-edit').each(function(position, element){
			$(element).bind('click', {element: element}, Forum.editComment);
		});
		$('i.reply-remove').each(function(position, element){
			$(element).bind('click', {element: element}, Forum.deleteComment);
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
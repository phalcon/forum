
/**
 * Forum
 */
var Forum = {

	_uri: '',

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
				context: content
			}).done(Forum.makeCommentEditable);
		}
	},

	highlightElement: function(element, elementOriginal)
	{
		if (typeof sh_languages !== "undefined") {

			if (element.hasClass('sh_php')) {
				type = 'php';
			} else {
				if (element.hasClass('sh_css')) {
					type = 'css';
				} else {
					if (element.hasClass('sh_html')) {
						type = 'html';
					} else {
						if (element.hasClass('sh_sql')) {
							type = 'sql';
						} else {
							type = 'php';
						}
					}
				}
			}

			if (typeof sh_languages[type] !== "undefined") {
				sh_highlightElement(elementOriginal, sh_languages[type]);
			};
		};
	},

	/**
	 * Changes a tab in a comment, highlightight the preview page
	 */
	changeCommentTab: function(event)
	{

		event.data.links.each(function(position, element){
			$(element).removeClass('active');
		});

		$(this).addClass('active');

		if ($('a', this)[0].innerHTML == 'Preview') {

			var content = $('textarea', '#comment-box')[0].value;
			if (content !== '') {
				content = content.replace(/</g, '&lt;');
				content = content.replace(/>/g, '&gt;');
				content = content.replace('\n', '<br>');
				$('#preview-box')[0].innerHTML = Forum.parseContent(content);
			} else {
				$('#preview-box')[0].innerHTML = 'Nothing to preview'
			}

			$('pre', '#preview-box').each(function(postion, element){
				Forum.highlightElement($(element), element);
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
		Forum.addCallbacks();
		prettyPrint();
	}

};

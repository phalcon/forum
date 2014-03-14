
/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

if (typeof String.prototype.trim === "undefined") {
    String.prototype.trim = function()
    {
        return String(this).replace(/^\s+|\s+$/g, '');
    };
}

/**
 * Forum
 */
var Forum = {

	_uri: '',

	_editor: null,

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
			textarea.className = 'form-control';
			form.appendChild(textarea);

			var hidden = document.createElement('INPUT');
			hidden.name = 'id';
			hidden.type = 'hidden';
			hidden.value = response.id;
			form.appendChild(hidden);

			var cancel = document.createElement('INPUT');
			cancel.type = 'button';
			cancel.className = 'btn btn-default btn-sm pull-left';
			cancel.value = 'Cancel';
			$(cancel).bind('click', { form: form, element: this}, Forum.cancelEditing);
			form.appendChild(cancel);

			var submit = document.createElement('INPUT');
			submit.type = 'buttom';
			submit.className = 'btn btn-success btn-sm pull-right';
			submit.value = 'Update Comment';
			$(submit).bind('click', { form: form }, function(event) {
				this.disabled = true;
				event.data.form.submit();
			});
			form.appendChild(submit);

			this.hide();

			this.parent().append(form);

			var editor = new Editor({ 'element': textarea });
			editor.render();
		}
	},

	addBaseComment: function(response)
	{
		if (response.status == 'OK') {
			var parts = response.comment.split(/\r\n|\r|\n/), str = "\r\n\r\n";
			for (var i = 0; i < parts.length; i++) {
				str += ">" + parts[i] + "\r\n";
			}
			$('#replyModal #comment-textarea').html('<textarea name="content" id="replyContent"></textarea>');
			$('#replyModal').modal('show');
			var textarea = $('#replyModal textarea')[0];
			$(textarea).val(str);
			window.setTimeout(function(){
				var editor = new Editor({
					'element': textarea
				});
				editor.render();
			}, 200)
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
		$('div.posts-buttons', element.parents()[1]).show();
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
		var content = $('div.post-content', element.parents()[2]);
		$('div.posts-buttons', element.parents()[2]).hide();
		if (content.is(':visible')) {
			$.ajax({
				dataType: 'json',
				url: Forum._uri + 'reply/' + element.data('id'),
				context: content
			}).done(Forum.makeCommentEditable);
		}
	},

	/**
	 * Converts the post-comment div into an editable textarea
	 */
	replyReply: function(event)
	{
		var element = $(event.data.element);
		$('#reply-id').val(element.data('id'))
		$.ajax({
			dataType: 'json',
			url: Forum._uri + 'reply/' + element.data('id')
		}).done(Forum.addBaseComment);
	},

	/**
	 * Vote a post up
	 */
	votePostUp: function(event)
	{
		var element = $(event.data.element);
		$.ajax({
			dataType: 'json',
			url: Forum._uri + 'discussion/vote-up/' + element.data('id')
		}).done(function(response){
			if (response.status == "error") {
				$('#errorModal .modal-body').html(response.message);
				$('#errorModal').modal('show');
			} else {
				window.location.reload(true);
			}
		});
	},

	/**
	 * Vote a post up
	 */
	votePostDown: function(event)
	{
		var element = $(event.data.element);
		$.ajax({
			dataType: 'json',
			url: Forum._uri + 'discussion/vote-down/' + element.data('id')
		}).done(function(response){
			if (response.status == "error") {
				$('#errorModal .modal-body').html(response.message);
				$('#errorModal').modal('show');
			} else {
				window.location.reload(true);
			}
		});
	},

	/**
	 * Vote a post up
	 */
	voteReplyUp: function(event)
	{
		var element = $(event.data.element);
		$.ajax({
			dataType: 'json',
			url: Forum._uri + 'reply/vote-up/' + element.data('id')
		}).done(function(response){
			if (response.status == "error") {
				$('#errorModal .modal-body').html(response.message);
				$('#errorModal').modal('show');
			} else {
				window.location.reload(true);
			}
		});
	},

	/**
	 * Vote a post up
	 */
	voteReplyDown: function(event)
	{
		var element = $(event.data.element);
		$.ajax({
			dataType: 'json',
			url: Forum._uri + 'reply/vote-down/' + element.data('id')
		}).done(function(response){
			if (response.status == "error") {
				$('#errorModal .modal-body').html(response.message);
				$('#errorModal').modal('show');
			} else {
				window.location.reload(true);
			}
		});
	},

	/**
	 * Accept a reply as correct answer
	 */
	acceptAnswer: function(event)
	{
		var element = $(event.data.element);
		$.ajax({
			dataType: 'json',
			url: Forum._uri + 'reply/accept/' + element.data('id')
		}).done(function(response){
			if (response.status == "error") {
				$('#errorModal .modal-body').html(response.message);
				$('#errorModal').modal('show');
			} else {
				window.location.reload(true);
			}
		});
	},

	/**
	 * Vote a post up
	 */
	voteLogin: function(event)
	{
		window.location = Forum._uri + 'login/oauth/authorize';
	},

	/**
	 * Shows the latest modification made to a post
	 */
	postHistory: function(event)
	{
		var element = $(event.data.element);
		$.ajax({
			url: Forum._uri + 'discussion/history/' + element.data('id'),
		}).done(function(response){
			$('#historyBody').html(response);
		});
	},

	/**
	 * Shows the latest modification made to a post
	 */
	replyHistory: function(event)
	{
		var element = $(event.data.element);
		$.ajax({
			url: Forum._uri + 'reply/history/' + element.data('id'),
		}).done(function(response){
			$('#historyBody').html(response);
		});
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
		var parent = $(this).parents()[2];
		if ($('a', this).html() == 'Preview') {
			var content = $('textarea', parent).data('editor').codemirror.getValue()
			if (content !== '') {
				$.ajax({
					method: 'POST',
					url: Forum._uri + 'preview',
					data: {'content': content }
				}).done(function(parent, response){
					$('#preview-box', parent).html(response);
					prettyPrint();
				}.bind(this, parent));
			} else {
				$('#preview-box', parent).html('Nothing to preview');
			};
			$('#comment-box, #reply-comment-box', parent).hide();
			$('#preview-box', parent).show();
		} else {
			$('#comment-box, #reply-comment-box', parent).show();
			$('#preview-box', parent).hide();
		}
	},

	reloadCategories: function(event)
	{
		if ($('#categories-dropdown').html().trim() == '') {
			$.ajax({
				method: 'GET',
				url: Forum._uri + 'reload-categories',
			}).done(function(response){
				$('#categories-dropdown').html(response);
			});
		}
	},

	/**
	 * Add callbacks to edit/delete buttons
	 */
	addCallbacks: function()
	{
		$('a.reply-edit').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.editComment);
		});

		$('a.reply-remove').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.deleteComment);
		});

		$('span.action-edit').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.postHistory);
		});

		$('span.action-reply-edit').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.replyHistory);
		});

		$('a.vote-post-up').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.votePostUp);
		});

		$('a.vote-post-down').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.votePostDown);
		});

		$('a.vote-reply-up').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.voteReplyUp);
		});

		$('a.vote-reply-down').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.voteReplyDown);
		});

		$('a.reply-reply').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.replyReply);
		});

		$('a.vote-login').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.voteLogin);
		});

		$('a.reply-accept').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.acceptAnswer);
		});

		$('a.categories-link').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.reloadCategories);
		});

		var previewNavLinks = $('ul.preview-nav li');
		previewNavLinks.each(function(position, element) {
			$(element).bind('click', {links: previewNavLinks}, Forum.changeCommentTab);
		});

		if ($('textarea').length) {
			var editor = new Editor();
			editor.render();
		}
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


/*
  +------------------------------------------------------------------------+
  | Phosphorum                                                             |
  +------------------------------------------------------------------------+
  | Copyright (c) 2013-present Phalcon Team and contributors               |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file LICENSE.txt.                             |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
*/

if (typeof String.prototype.trim === "undefined") {
    String.prototype.trim = function() {
        return String(this).replace(/^\s+|\s+$/g, '');
    };
}

/**
 * Forum
 */
var Forum = {

	_uri: '',

	_editor: null,

	_search: false,

	/**
	 * Transform a comment into a editable box
	 */
	makeCommentEditable: function(response)
	{
		if (response.status == 'OK') {
			var ul = document.createElement('UL');
			ul.className = 'nav nav-tabs preview-nav';
			this.parent().append(ul);

			var li = document.createElement('LI');
			li.className = 'active';
			var a = document.createElement('A');
			a.href = '#';
			a.setAttribute('onclick', 'return false');
			a.setAttribute('data-editor-type', 'edit');
			a.innerHTML = 'Comment';
			li.appendChild(a);
			ul.appendChild(li);

			li = document.createElement('LI');
			a = document.createElement('A');
			a.href = '#';
			a.setAttribute('onclick', 'return false');
			a.setAttribute('data-editor-type', 'edit');
			a.innerHTML = 'Preview';
			li.appendChild(a);
			ul.appendChild(li);

			li = document.createElement('LI');
			li.className = 'pull-right';
			a = document.createElement('A');
			a.href = '/help/markdown';
			a.setAttribute('target', '_blank');
			a.innerHTML = 'Help';
			li.appendChild(a);
			ul.appendChild(li);

			var form = document.createElement('FORM');
			form.className = 'edit-form';
			form.method = 'POST';
			form.action = Forum._uri + 'reply/update';

			var div = document.createElement('DIV');
			div.id = "wmd-button-bar-edit";
			form.appendChild(div);

			var textarea = document.createElement('TEXTAREA');
			textarea.name = 'content';
			textarea.rows = 7;
			textarea.value = response.comment;
			textarea.className = 'form-control input-sm';
			textarea.id = 'wmd-input-edit';
			form.appendChild(textarea);

			div = document.createElement('DIV');
			div.id = 'wmd-preview-edit';
			form.appendChild(div);

			var statusBar = document.createElement('DIV');
			statusBar.className = "editor-statusbar";
			form.appendChild(statusBar);

			var hidden = document.createElement('INPUT');
			hidden.name = 'id';
			hidden.type = 'hidden';
			hidden.value = response.id;
			form.appendChild(hidden);

			var token = document.createElement('INPUT');
			token.name = $('#csrf-token').attr('name');
			token.type = 'hidden';
			token.value = $('#csrf-token').attr('value');
			form.appendChild(token);

			var cancel = document.createElement('INPUT');
			cancel.type = 'button';
			cancel.type = 'button';
			cancel.className = 'btn btn-default btn-sm pull-left';
			cancel.value = 'Cancel';
			$(cancel).bind('click', { form: form, element: this}, Forum.cancelEditing);
			form.appendChild(cancel);

			var submit = document.createElement('INPUT');
			submit.type = 'button';
			submit.className = 'btn btn-success btn-sm pull-right';
			submit.value = 'Update Comment';
			$(submit).bind('click', { form: form }, function(event) {
				this.disabled = true;
				event.data.form.submit();
			});
			form.appendChild(submit);

			this.hide();

			this.parent().append(form);

			Forum.initializeEditPagedown();
			var previewNavLinks = this.siblings("ul").children();
				previewNavLinks.each(function(position, element) {
					$(element).bind('click', {links: previewNavLinks}, Forum.changeCommentTab);
			});
		}
	},

	/**
	 * Shows the reply box
	 */
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
		element.siblings("ul").hide();
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
			window.location = Forum._uri + 'reply/delete/' + element.data('id') + '?' + $('#csrf-token').attr('name') + '=' + $('#csrf-token').attr('value');
		}
	},

	/**
	 * Converts the post-comment div into an editable textarea
	 */
	editComment: function(event)
	{
		var element = $(event.data.element);
		if (element.length) {
			var content = $('div.post-content', element.parents()[2]);
			$('div.posts-buttons', element.parents()[2]).hide();
			if (content.is(':visible')) {
				$.ajax({
					dataType: 'json',
					url: Forum._uri + 'reply/' + element.data('id'),
					context: content
				}).done(Forum.makeCommentEditable);
			}
		} else {
			alert('Cannot trigger event');
		}
	},

	/**
	 * Converts the post-comment div into an editable textarea
	 */
	replyReply: function(event)
	{
		Forum.initializeModalPagedown();

		var element = $(event.data.element);
		if (element.length) {
			$('#reply-id').val(element.data('id'))
			$.ajax({
				dataType: 'json',
				url: Forum._uri + 'reply/' + element.data('id')
			}).done(Forum.addBaseComment);
		} else {
			alert('Cannot trigger event');
		}
	},

	/**
	 * Vote a post up
	 */
	votePostUp: function(event)
	{
		var element = $(event.data.element);
		if (element.length) {
			var csrf = {}
			csrf[$('#csrf-token').attr('name')] = $('#csrf-token').attr('value')
			$.ajax({
				dataType: 'json',
				url: Forum._uri + 'discussion/vote-up/' + element.data('id'),
				data: csrf
			}).done(function(response){
				if (response.status == "error") {
					$('#errorModal .modal-body').html(response.message);
					$('#errorModal').modal('show');
				} else {
					window.location.reload(true);
				}
			});
		} else {
			alert('Cannot trigger event');
		}
	},

	/**
	 * Delete Option from the Poll
	 * @param event
     */
	delOption: function(event)
	{
		event.preventDefault();

		var optionsCount = $('#options-box input[type=text]').length;

		$(this).closest('div.form-group').remove();
		optionsCount--;

		if (optionsCount < 10) {
			$('a.add-poll-option').removeClass('disabled');
		}
	},

	removePoll: function()
	{
		var button     = $(this),
			optionsBox = $('#options-box');

		optionsBox.html('');
		optionsBox.removeClass('post-option-body');

		button.removeClass('btn-danger');
		button.addClass('btn-primary');
		button.text('Attach a poll');
		button.attr('id', 'create-poll');
		button.on('click', Forum.addPoll)
	},

	addPoll: function()
	{
		var button       = $(this),
			optionsCount = $('#options-box input[type=text]').length,
			form         = button.parents('form');

		optionsCount++;

		var optionsBox = $('#options-box');
		var nextId     = 'option' + optionsCount;
		var formGroup  = Forum.createFormGroup();

		var label = document.createElement('LABEL');
		label.className   = 'col-sm-2 control-label';
		label.textContent = 'Name';
		label.htmlFor     = nextId;

		formGroup.appendChild(label);

		var inputGroup = document.createElement('DIV');
		inputGroup.className = 'col-sm-10';

		var input = Forum.createInput(nextId);

		inputGroup.appendChild(input);
		formGroup.appendChild(inputGroup);

		optionsBox.append(formGroup);

		var formGroup2 = formGroup.cloneNode(true);
		optionsBox.append(formGroup2);

		var formGroup3 = Forum.createFormGroup();

		var addOption  = document.createElement('A');
		addOption.textContent = '+ Add option';
		addOption.href        = '#';
		addOption.className   = 'btn btn-sm btn-primary add-poll-option';
		addOption.onclick     = Forum.addOption;

		formGroup3.appendChild(addOption);
		optionsBox.append(formGroup3);

		optionsBox.addClass('post-option-body');

		button.removeClass('btn-primary');
		button.addClass('btn-danger');
		button.text('Remove poll');
		button.attr('id', 'remove-poll');
		button.on('click', Forum.removePoll)
	},

	createFormGroup: function()
	{
		var formGroup  = document.createElement('DIV');
		formGroup.className = 'form-group';
		formGroup.style = "min-height: 34px";

		return formGroup;
	},

	createInput: function(id)
	{
		var input = document.createElement('input');
		input.className = 'form-control';

		if (input.placeholder != 'undefined') {
			input.placeholder = 'Enter an option here';
		}

		input.name = "pollOptions[]";
		input.id   = id;
		input.type = 'text';
		input.setAttribute('maxLength', '64');
		input.setAttribute('required', 'required');

		return input;
	},

	/**
	 * Add Option to the Poll
	 * @param event
     */
	addOption: function(event)
	{
		event.preventDefault();

		var optionsCount = $('#options-box input[type=text]').length,
			addOptionBtn = $('a.add-poll-option'),
			element      = $(this);

		if (element.length && optionsCount < 10) {
			optionsCount++;

			var nextId    = 'option' + optionsCount;
			var formGroup = Forum.createFormGroup()

			var label = document.createElement('LABEL');
			label.className = 'col-sm-2 control-label';
			label.textContent = 'Name';
			label.htmlFor = nextId;

			formGroup.appendChild(label);

			var inputGroup = document.createElement('DIV');
			inputGroup.className = 'col-sm-9';

			var input = Forum.createInput(nextId);

			inputGroup.appendChild(input);
			formGroup.appendChild(inputGroup);

			var removeGroup = document.createElement('DIV');
			removeGroup.className = 'col-sm-1';

			var removeBtn = document.createElement('A');
			removeBtn.title = 'Delete option';
			removeBtn.href = '#';
			removeBtn.className = 'del-poll-option';
			removeBtn.onclick = Forum.delOption;

			var removeSpn = document.createElement('SPAN');
			removeSpn.className = 'glyphicon glyphicon-remove-circle';

			removeBtn.appendChild(removeSpn);
			removeGroup.appendChild(removeBtn);
			formGroup.appendChild(removeGroup);

			addOptionBtn.before($(formGroup));

			if (optionsCount >= 10) {
				addOptionBtn.addClass('disabled');
			}
		}
	},

	/**
	 * Vote for the Poll
     */
	votePoll: function()
	{
		var pollOption = $("input:radio[name ='pollOption']:checked"),
			element    = $(this);

		if (element.length && pollOption.length) {
			var csrf = {},
				tokenInput = $('#csrf-token');

			csrf[tokenInput.attr('name')] = tokenInput.attr('value');

			$.ajax({
				dataType: 'json',
				url: Forum._uri + 'poll/vote/' + element.data('id') + '/' + pollOption.data('id'),
				data: csrf
			}).done(function(response){
				if (response.status == "error") {
					$('#errorModal .modal-body').html(response.message);
					$('#errorModal').modal('show');
				} else {
					window.location.reload(true);
				}
			});
		} else {
			console.error('Cannot trigger event');
		}
	},

	/**
	 * Reflect the Vote button
	 * @param event
     */
	reflectVoteButton: function(event)
	{
		var element = $(event.data.element);
		if (element.length) {
			$('a.vote-poll').removeClass('disabled');
		}
	},

	/**
	 * Vote a post up
	 */
	votePostDown: function(event)
	{
		var element = $(event.data.element);
		if (element.length) {
			var csrf = {},
				tokenInput = $('#csrf-token');

			csrf[tokenInput.attr('name')] = tokenInput.attr('value');
			$.ajax({
				dataType: 'json',
				url: Forum._uri + 'discussion/vote-down/' + element.data('id'),
				data: csrf
			}).done(function(response){
				if (response.status == "error") {
					$('#errorModal .modal-body').html(response.message);
					$('#errorModal').modal('show');
				} else {
					window.location.reload(true);
				}
			});
		} else {
			alert('Cannot trigger event');
		}
	},

	/**
	 * Vote a post up
	 */
	voteReplyUp: function(event)
	{
		var element = $(event.data.element);
		if (element.length) {
			var csrf = {}
			csrf[$('#csrf-token').attr('name')] = $('#csrf-token').attr('value')
			$.ajax({
				dataType: 'json',
				url: Forum._uri + 'reply/vote-up/' + element.data('id'),
				data: csrf
			}).done(function(response){
				if (response.status == "error") {
					$('#errorModal .modal-body').html(response.message);
					$('#errorModal').modal('show');
				} else {
					window.location.reload(true);
				}
			});
		} else {
			alert('Cannot trigger event');
		}
	},

	/**
	 * Vote a post up
	 */
	voteReplyDown: function(event)
	{
		var element = $(event.data.element);
		if (element.length) {
			var csrf = {}
			csrf[$('#csrf-token').attr('name')] = $('#csrf-token').attr('value')
			$.ajax({
				dataType: 'json',
				url: Forum._uri + 'reply/vote-down/' + element.data('id'),
				data: csrf
			}).done(function(response){
				if (response.status == "error") {
					$('#errorModal .modal-body').html(response.message);
					$('#errorModal').modal('show');
				} else {
					window.location.reload(true);
				}
			});
		} else {
			alert('Cannot trigger event');
		}
	},

	/**
	 * Accept a reply as correct answer
	 */
	acceptAnswer: function(event)
	{
		var element = $(event.data.element);
		if (element.length) {
			var csrf = {}
			csrf[$('#csrf-token').attr('name')] = $('#csrf-token').attr('value')
			$.ajax({
				dataType: 'json',
				url: Forum._uri + 'reply/accept/' + element.data('id'),
				data: csrf
			}).done(function(response){
				if (response.status == "error") {
					$('#errorModal .modal-body').html(response.message);
					$('#errorModal').modal('show');
				} else {
					window.location.reload(true);
				}
			});
		} else {
			alert('Cannot trigger event');
		}
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
		if (element.length) {
			$.ajax({
				url: Forum._uri + 'discussion/history/' + element.data('id'),
			}).done(function(response){
				$('#historyBody').html(response);
			});
		}
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
		var extraTitle = '';
        if (typeof $(this).children().attr('data-editor-type') == 'string') {
            extraTitle = '-' + $(this).children().attr('data-editor-type');
        }

        event.data.links.each(function(position, element){
            $(element).removeClass('active');
        });

        $(this).addClass('active');
        var parent = $(this).parents()[2];
        if ($('a', this).html() == 'Preview') {
            var content = $('#wmd-input' + extraTitle).val();
            if (content !== '') {
                $.ajax({
                    method: 'POST',
                    url: Forum._uri + 'preview',
                    data: {'content': content }
                }).done(function(parent, response){
                    $('#wmd-preview' + extraTitle, parent).html(response);

                    $('#wmd-preview' + extraTitle).find('code').each(function(index, element) {
                        Prism.highlightElement($(element)[0]);
                    });

                    $('.wmd-preview' + extraTitle).find('code').each(function(index, element) {
                        Prism.highlightElement($(element)[0]);
                    });
                }.bind(this, parent));
            } else {
                $('#wmd-preview' + extraTitle).html('Nothing to preview');
            };
            $('#wmd-input' + extraTitle).hide();
            $('#wmd-button-bar' + extraTitle).hide();
            $('#wmd-preview' + extraTitle).show();
        } else {
            $('#wmd-input' + extraTitle).show();
            $('#wmd-button-bar' + extraTitle).show();
            $('#wmd-preview' + extraTitle).hide();
        }
    },

	reloadCategories: function(event)
	{
		if ($('.categories-dropdown').html().trim() == '') {
			$.ajax({
				method: 'GET',
				url: Forum._uri + 'reload-categories'
			}).done(function(response){
				$('.categories-dropdown').html(response);
			});
		}
	},

	updateRecommendedPosts: function(response) {
		Forum._search = false;

		var data = response;
		if (typeof data != "object") {
			data = JSON.parse(response);
		}

		var content = $('#recommended-posts-create-content')[0];
		if (data.results.length > 0) {
			content.innerHTML = '';
			for (var i = 0; i < data.results.length; i++) {
				var result = data.results[i];

				var div = document.createElement('DIV');
				div.className = 'recommended-post-create';

				var a = document.createElement('A');
				a.innerHTML = result.title + '<br>';
				a.href = Forum._uri + result.slug;
				div.appendChild(a);

				var span = document.createElement('SPAN');
				span.innerHTML = result.created;
				div.appendChild(span);

				content.appendChild(div);
			}
		} else {
			content.innerHTML = 'There are no suggested posts';
		}
	},

	getRelatedCreate: function()
	{
		if (this.value.length > 2 && Forum._search == false) {
			Forum._search = true;
			$.ajax({
				method: 'POST',
				url: Forum._uri + 'find-related',
				data: { 'title': this.value }
			}).done(Forum.updateRecommendedPosts);
		}
	},

	updateSuggestedPosts: function(response)
	{
		$('#suggested-posts').html(response);
	},

	showSuggestedPosts: function()
	{
		$.ajax({
			method: 'POST',
			url: Forum._uri + 'show-related',
			data: { 'id': $('#post-id').val() }
		}).done(Forum.updateSuggestedPosts);
	},

	/**
	 * Initializes pagedown
	 */
	initializePagedown: function () {
		var converter = Markdown.getSanitizingConverter();
		converter.hooks.chain("preBlockGamut", function (text, rbg) {
			return text.replace(/^ {0,3}""" *\n((?:.*?\n)+?) {0,3}""" *$/gm, function (whole, inner) {
				return "<blockquote>" + rbg(inner) + "</blockquote>\n";
			});
		});
		var editor = new Markdown.Editor(converter);
		editor.run();
	},

	/**
	 * Initializes modal pagedown
	 */
	initializeModalPagedown: function () {
		var converter = new Markdown.Converter();
		converter.hooks.chain("preConversion", function (text) {
			return text.replace(/\b(a\w*)/gi, "*$1*");
		});

		converter.hooks.chain("plainLinkText", function (url) {
			return "This is a link to " + url.replace(/^https?:\/\//, "");
		});
		var editor = new Markdown.Editor(converter, "-modal", options);

		editor.run();
	},

	/**
	 * Initializes edit pagedown
	 */
	initializeEditPagedown: function () {
		var converter = new Markdown.Converter();
		converter.hooks.chain("preConversion", function (text) {
			return text.replace(/\b(a\w*)/gi, "*$1*");
		});

		converter.hooks.chain("plainLinkText", function (url) {
			return "This is a link to " + url.replace(/^https?:\/\//, "");
		});
		var editor = new Markdown.Editor(converter, "-edit", options);

		editor.run();
	},

	/**
	 * Initializes user's autocomplete
	 */
	userAutocomplete: function (id) {
		$(id).mentionsInput({
			onDataRequest:function (mode, query, callback) {
				if (!(new RegExp("^([a-zA-Z0-9\-\.]+)$")).test(query)) {
					return;
				}

				$.ajax({
					dataType: 'json',
					url: Forum._uri + 'users/auto-complete/' + query.toLowerCase()
				}).done(function(response) {
					var data = [];
					for (var i = 0; i < response.length; i++) {
						data.push({
							id: response[i]['uid'],
							name: '@' + response[i]['login'],
							display: response[i]['name'],
							avatar: 'https://secure.gravatar.com/avatar/' + response[i]['gravatar_id'] + '?s=48&r=pg&d=identicon'
						});
					}

					callback.call(this, data);
				});
			}
		});
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

		$('#create-poll').on('click', Forum.addPoll);
		$('#remove-poll').on('click', Forum.removePoll);
		$('a.add-poll-option').on('click', Forum.addOption);
		$('a.del-poll-option').on('click', Forum.delOption);

		$('a.vote-post-down').each(function(position, element) {
			$(element).bind('click', {element: element}, Forum.votePostDown);
		});

		$('a.vote-poll').on('click', Forum.votePoll);

		$('input.pollOption').each(function(position, element) {
			$(element).bind('change', {element: element}, Forum.reflectVoteButton);
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

		var textarea = $('#wmd-input');
		if (textarea.length && !textarea.hasClass('no-editor')) {
            Forum.initializePagedown();
		}
		textarea = $('#wmd-input-edit');
		if (textarea.length && !textarea.hasClass('no-editor')) {
			Forum.initializeEditPagedown();
		}

		$('#recommended-posts-create').each(function(position, element){
			$('#title').on('keyup', null, Forum.getRelatedCreate);
		});

		$('#suggested-posts').each(function(position, element){
			window.setTimeout(Forum.showSuggestedPosts, 1500);
		});

		if ($('div.row').length > 4) {
			$(window).scroll(function() {
				$('#sticky-progress').show();
				var windowTop = $(window).scrollTop();
				var rows = $('div.reply-block'), total = rows.length, position, number = 0;
				for (var i = 0; i < total; i++) {
					position = $(rows[i]).offset();
					if (position.top < windowTop) {
						number++;
					}
				};
				$('#sticky-progress').html((number + 1) + ' / ' + total);
	  		});
	  	}

		$('#wmd-input').change(
			Forum.userAutocomplete('#wmd-input')
		);
		$('#wmd-input-modal').change(
			Forum.userAutocomplete('#wmd-input-modal')
		);
		//todo add for '#wmd-input-edit
	},

	/**
	 * Initializes the view (highlighters, callbacks, etc)
	 */
	initializeView: function(uri)
	{
		Forum._uri = uri;
		Forum.addCallbacks();
	}
};

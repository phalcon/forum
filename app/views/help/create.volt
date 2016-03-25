
<div class="help">

	{% include 'partials/breadcrumbs.volt' %}

	<h1>Creating Posts</h1>

	<p>
		Please take some time to read these recommendations before creating a new post
		to increase your chances of getting good answers.
	</p>

	<p>
		<h4>Open-Source Community</h4>
		Like any open-source community, Phalcon is full of members that will help you voluntarily
		in their free time. Please be kind and respectful to the effort made by others.
	</p>

	<p>
		<h4>Busy People</h4>
		Pretend you're talking to a busy colleague. Be as specific as possible.
		Write titles that summarizes the specific problem.
		Introduce the problem before you post any code.
		Include any error messages and relevant details that could help others to understand your issue.
		If someone posts an answer, be ready to try it out and provide feedback.
	</p>

	<p>
		Posts/Replies in the forum use {{ link_to('help/markdown', 'Markdown') }} to format code.
		If you need to post code use the proper markdown, this makes the code easier to read for everyone:

<pre>```php
public function statsAction()
{
   $this->view->threads = Posts::count();
}
```</pre>

<pre>```html
&lt;p&gt;&lt;h2&gt;Hello&lt;/h2&gt;&lt;p&gt;
```</pre>

<pre>```javascript
$("button").on("click", function(event) { });
```</pre>

	See the reference for {{ link_to('help/markdown', 'more details') }}.

	</p>

	<p>
		<h4>Help others reproduce the problem</h4>
		Some questions require little or no code. Others require much code or invite to reproduce complex, unusual or weird cases
		that are circling your head. In these cases it is better to post the code in more appropriate places as
		<a href="https://gist.github.com/">Gist</a>.
	</p>

	<p>
		Setting up a repository on Github with the relevant parts of the code alongside with a proper explanation
		to reproduce the issue is an excellent idea. If you don't know how to create a repository, please check this
		<a href="https://help.github.com/articles/create-a-repo">article</a>.
	</p>

</div>

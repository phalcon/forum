
<div class="help">

	{% include 'partials/breadcrumbs.volt' %}

	<h1>Markdown</h1>

	<p>
		This forum allows you to use Markdown as markup language when creating posts or adding comments. Markdown
		is also used by Github so it's probably familiar to you. The following guide explain its basic syntax:
	</p>

	<p>
		<h3>Bold and Italics</h3>
	</p>

<p>
		<pre>
*single asterisks*

_single underscores_

**double asterisks**

__double underscores__
</pre>
</p>


	<p>
		<h3>Headings</h3>
		H1 is underlined using equal signs, and H2 is underlined using dashes.
	</p>
	<p>
		<pre>
Header 1
========

Header 2</pre>
	</p>

	<p>
		<h3>Headings</h3>
		Atx-style headers use 1-6 hash characters at the start of the line.
	</p>
	<p>
		<pre>
# Header 1
## Header 2
### Header 3
#### Header 4
##### Header 5
###### Header 6
</pre>
	</p>

	<p>
		<h3>Paragraphs</h3>
		A paragraph is simply one or more consecutive lines of text, separated by one or more blank lines.
	</p>

<p>
<pre>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla neque nisl, fringilla sed blandit non, pretium eu odio.

Lorem ipsum dolor sit amet, consectetur adipiscing elit.
Nulla neque nisl, fringilla sed blandit non, pretium eu odio.
</pre>
</p>

	<p>
		<h3>Unordered Lists</h3>
		Start each line with hyphens, asterisks or pluses.
	</p>

<p>
<pre>
* one
* two
* three
</pre>
</p>

	<p>
		<h3>Ordered Lists Core</h3>
		Start each line with number and a period.
	</p>

<p>
<pre>
1. one
2. two
3. three
</pre>
</p>

	<p>
		<h3>Code Blocks</h3>
	</p>

**Preferred method**
<p>
<pre>
```php
&lt;?php

require __DIR__ . '/vendor/autoload.php';
```
</pre>
</p>

<p>
<pre>
```
$ cd cphalcon/build
$ sudo ./install
```
</pre>
</p>

<p>
<pre>
Lorem ipsum dolor sit amet

    consectetur adipiscing elit.
    Nulla neque nisl, fringilla sed blandit non, pretium eu odio.
</pre>
</p>




<p>
	<h3>Inline Code</h3>
</p>

<p>
<pre>
Don't forget to add `echo $foo;`.

Please replace `&lt;b&gt;` to `&lt;strong&gt;`.
</pre>
</p>

<p>
	<h3>Horizontal Rules</h3>
</p>

<p>
<pre>
* * *

*******

- - - -

--------
</pre>
</p>

<p>
	<h3>Inline Links</h3>
</p>

<p>
<pre>
This is an [inline link](http://example.com).

This [link](http://example.com "example website") has title attribute.
</pre>
</p>

<p>
<pre>
This is an [reference style link][id1].

This [link][id2] has title attribute.

[id1]: http://example.com/
[id2]: http://example.com/ "example website"
</pre>
</p>

<p>
	<h3>Inline Images</h3>
</p>

<p>
<pre>
![Alt text](/path/to/image.png)

![Alt text](/path/to/image.png "Title")
</pre>
</p>

<p>
	<h3>Tables</h3>
</p>

<p>
<pre>
| head | head |
|------|------|
| body | body |
</pre>
</p>

</div>

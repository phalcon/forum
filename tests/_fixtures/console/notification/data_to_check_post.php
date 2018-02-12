<?php

/**
 * Fixture for Notification cest
 *
 * @copyright (c) 2013-present Phalcon Team
 * @link      http://www.phalconphp.com
 * @author    Sergii Svyrydenko <sergey.v.svyrydenko@gmail.com>
 * @package
 *
 * The contents of this file are subject to the New BSD License that is
 * bundled with this package in the file LICENSE.txt
 *
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world-wide-web, please send an email to license@phalconphp.com
 * so that we can send you a copy immediately.
 */

return [
    'post' => 'Some text <script type="text/javascript">alert("test");</script> Text after 
<a href="javascript:alert(1)">xss</a> <img src="javascript:alert(1)" alt="xss" /> 
[xss](https://www.example.com\') $test=getTest();',
    'html' => '<p>Some text &lt;script type=&quot;text/javascript&quot;&gt;alert(&quot;test&quot;);&lt;/script&gt; Text after
&lt;a href=&quot;javascript:alert(1)&quot;&gt;xss&lt;/a&gt; &lt;img src=&quot;javascript:alert(1)&quot; alt=&quot;xss&quot; /&gt;
<a href="https://www.example.com&#039;">xss</a> $test=getTest();</p>',
    'text' => 'Re: title tag

Some text Text after=20
xss:
javascript:alert(1)=20
[x=
ss](https://www.example.com\') $test=3DgetTest();'
];

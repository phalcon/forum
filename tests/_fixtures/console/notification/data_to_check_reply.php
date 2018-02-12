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
    'reply' => 'public function beforeExecuteRoute(\Phalcon\Events\Event $event, \Phalcon\Dispatcher $dispatcher)
    {
        $router = new \Phalcon\Mvc\Router();
        $router->add(\'/index/test\')->setName(\'test\');
        $router->handle();

        if ($router->wasMatched()) {
            //it performs well, according to my expectations
             file_put_contents(\'test1.txt\', \'Well done!\');
        }else{
        //this is sth extraterrestrial !!!
            file_put_contents(\'test2.txt\', \'Phalcon secret parser here\');

            echo \'I am in (|)\';
            exit;
        }
}

<script>alert(\'test\')</script>
<p>test p tag</p>',
    'html' => '<p>public function beforeExecuteRoute(\Phalcon\Events\Event $event, \Phalcon\Dispatcher $dispatcher)
{
$router = new \Phalcon\Mvc\Router();
$router-&gt;add(\'/index/test\')-&gt;setName(\'test\');
$router-&gt;handle();</p>
<pre><code>    if ($router-&gt;wasMatched()) {
        //it performs well, according to my expectations
         file_put_contents(\'test1.txt\', \'Well done!\');
    }else{
    //this is sth extraterrestrial !!!
        file_put_contents(\'test2.txt\', \'Phalcon secret parser here\');

        echo \'I am in (|)\';
        exit;
    }</code></pre>
<p>}</p>
<p>&lt;script&gt;alert(\'test\')&lt;/script&gt;
&lt;p&gt;test p tag&lt;/p&gt;</p>',
    'text' => 'Re: title tag

public function beforeExecuteRoute(\Phalcon\Events\Event=
 $event, \Phalcon\Dispatcher $dispatcher)
{
$router =3D new \Phalcon\Mv=
c\Router();
$router->add(\'/index/test\')->setName(\'test\');
$router->hand=
le();

if ($router->wasMatched()) {
//it performs well, according to =
my expectations
file_put_contents(\'test1.txt\', \'Well done!\');
}else{
=
//this is sth extraterrestrial !!!
file_put_contents(\'test2.txt\', \'Phalco=
n secret parser here\');

echo \'I am in (|)\';
exit;
}
}

test =
p tag',
];

<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

return [
    'token_bot'  => env('DISCORD_BOT_TOKEN', null),
    'message'    => [
        'new_discussions'    => env('DISCORD_MESSAGE_ABOUT_NEW_DISCUSSIONS', false),
        'new_replies'        => env('DISCORD_MESSAGE_ABOUT_REPLIES', false),
        'solved_discussions' => env('DISCORD_MESSAGE_ABOUT_SOLVED_DISCUSSIONS', false),
    ],
    'channel_id' => env('DISCORD_CHANNEL_ID', null),
    'guild_id'   => env('DISCORD_GUILD_ID', null),
];

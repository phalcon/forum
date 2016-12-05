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

namespace Phosphorum\Model;

/**
 * Karma constants
 */
abstract class Karma
{
    const INITIAL_KARMA = 45;

    const LOGIN = 5;

    const ADD_NEW_POST = 10;

    const DELETE_POST = 15;

    const SOMEONE_REPLIED_TO_MY_POST = 5;

    const REPLY_ON_SOMEONE_ELSE_POST = 10;

    const SOMEONE_DELETED_HIS_OR_HER_REPLY_ON_MY_POST = 5;

    const DELETE_REPLY_ON_SOMEONE_ELSE_POST = 10;

    const MODERATE_POST = 25;

    const MODERATE_REPLY = 25;

    const MODERATE_DELETE_POST = 10;

    const MODERATE_DELETE_REPLY = 10;

    const VISIT_ON_MY_POST = 1;

    const MODERATE_VISIT_POST = 4;

    const VISIT_POST = 2;

    const SOMEONE_DID_VOTE_MY_POST = 5;

    const SOMEONE_DID_VOTE_MY_POLL = 1;

    const VOTE_ON_SOMEONE_ELSE_POST = 10;

    const VOTE_ON_SOMEONE_ELSE_POLL = 1;

    const VOTE_UP_ON_MY_REPLY_ON_MY_POST = 15;

    const VOTE_UP_ON_MY_REPLY = 10;

    const VOTE_UP_ON_SOMEONE_ELSE_REPLY = 10;

    const VOTE_DOWN_ON_SOMEONE_ELSE_REPLY = 10;

    const VOTE_DOWN_ON_MY_REPLY_ON_MY_POST = 15;

    const VOTE_DOWN_ON_MY_REPLY = 10;

    const SOMEONE_ELSE_ACCEPT_YOUR_REPLY = 50;
}

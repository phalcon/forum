<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2017 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

namespace Phosphorum\Task;

use Phosphorum\Console\AbstractTask;
use Phosphorum\Discord\DiscordComponent;

class Discord extends AbstractTask
{
    public function send()
    {
        $queue = container('queue');
        $queue->watch('discord');

        /** @var DiscordComponent $discordService */
        $discordService = container('discord');

        $discord = new \Discord\Discord(
            [
                'token' => $discordService->getToken(),
            ]
        );

        $discord->loop->addPeriodicTimer(
            5,
            function () use ($queue, $discord, $discordService) {
                $guild = $discord->guilds->get('id', $discordService->getGuildId());
                $channel = $guild->channels->get('id', $discordService->getChannelId());
                while ($queue->statsTube('discord')["current-jobs-ready"] > 0 && ($job = $queue->reserve())) {
                    $body = $job->getBody();
                    $channel->sendMessage($body['message'], false, $body['embed']);
                    $job->delete();
                }
            }
        ); // each 5 seconds get jobs from queue and send to channel

        $discord->on(
            'ready',
            function () {
                // we don't listen anything so we don't need it
            }
        );

        $discord->run();
    }
}

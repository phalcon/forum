<?php

namespace Helper;

use Codeception\Module;

/**
 * Mail Helper
 *
 * Here you can define custom actions
 * all public methods declared in helper class will be available in $I
 *
 * @package Helper
 */
class Mail extends Module
{
    public function seeHtmlBody($actual, array $attributes = null)
    {
        $attributes = $attributes ?: [];

        $default = [
            'app_name' => 'Phalcon Framework',
            'title'    => '',
            'body'     => '<p></p>',
            'base_uri' => 'http://pforum.loc',
            'post_id'  => '',
            'slug'     => '',
            'reply_id' => '',
            'template_path' => 'mails/reply_notification.html',
        ];

        $attributes = array_merge($default, $attributes);

        $template = file_get_contents(codecept_data_dir($attributes['template_path']));

        $expected = strtr($template, [
            '%app%'      => $attributes['app_name'],
            '%title%'    => $attributes['title'],
            '%body%'     => $attributes['body'],
            '%base_uri%' => $attributes['base_uri'],
            '%post_id%'  => $attributes['post_id'],
            '%slug%'     => $attributes['slug'],
            '%reply_id%' => $attributes['reply_id'],
        ]);

        $this->assertEquals($expected, $actual, 'HTML part of email is wrong for ' . $attributes['template_path']);
    }

    public function getTextPartOfMailFromNotification($template)
    {
        $textPart = explode('--_=_', $template);
        $textPart = explode('_=_', $textPart[1]);

        return str_replace("\r", '', trim($textPart[1]));
    }
}

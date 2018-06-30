<!doctype html>
<!--[if IE 8]> <html lang="en-US" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en-US" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en-US" class="no-js">
<!--<![endif]-->
<head>
    {%- set url = url() -%}

    {%- if config.social is defined and not(config.social.twitter_name is empty) -%}
        {%- set twitter_name = config.social.twitter_name -%}
    {%- else%}
        {%- set twitter_name = "" -%}
    {%- endif -%}

    {%- include "include/base-meta" with ['application_name': config.application.name] -%}

    {%- if noindex is defined and noindex is true -%}
        <meta name="robots" content="noindex">
    {%- endif -%}

    {%- if post is defined and not(post is empty) -%}
        {%- set post_user_name = post.user.name,
                post_user_id = post.user.id,
                post_user_login = post.user.login
        -%}
    {%- else -%}
        {%- set post_user_name = "",
                post_user_id = "",
                post_user_login = ""
        -%}
    {%- endif -%}

    {%- if canonical is defined and not(canonical is empty) -%}
        {# todo: author_picture = gravatar(post.user.email) #}
        {%- set canonical_url = config.application.url ~ canonical,
                author_picture = '',
                author_picture_alt = post.user.name
        -%}
    {%- else -%}
        {%- set canonical_url = config.application.url,
                author_picture = config.application.url ~ '/img/logo.png',
                author_picture_alt = config.application.name
        -%}
    {%- endif -%}

    {%- block seo -%}
        {%- include "include/seo" with [
            'description': config.application.description,
            'keywords': config.application.keywords,
            'canonical_url': canonical_url
        ] -%}
    {%- endblock -%}

    {%- block social -%}
        {%- include "include/social-meta" with [
            'description': config.application.description,
            'keywords': config.application.keywords,
            'application_name': config.application.name,
            'base_url': config.application.url,
            'twitter_name': twitter_name,
            'canonical_url': canonical_url,
            'author_picture': author_picture,
            'author_picture_alt': author_picture_alt
        ] -%}
    {%- endblock -%}

    {%- block page_passport -%}
        {%- if not(action_name is "index") and not(post_user_id is empty) -%}
            {%- include "include/page-passport" with [
                'base_url': config.application.url,
                'post_user_name': post_user_name,
                'post_user_id': post_user_id,
                'post_user_login': post_user_login
            ] -%}
        {%- else -%}
            {%- include "include/no-page-passport" with [
                'application_name': config.application.name,
                'base_url': config.application.url
            ] -%}
        {%- endif -%}
    {%- endblock -%}

    {%- block icons -%}
        {%- include "include/icons" with [
            'base_url': config.application.url
        ] -%}
    {%- endblock -%}

    {%- if config.thirdparty.analytics.enabled and not(config.thirdparty.analytics.tracking_id is empty) -%}
        {%- include "include/analytics" with [
            'canonical_url': canonical_url,
            'analytics_title': get_title(false) ~ ' - ' ~ config.application.name,
            'tracking_id': config.thirdparty.analytics.tracking_id
        ] -%}
    {%- endif %}

    {#- CSS resources from jsdelivr cannot be combined due to Bootstrap icons -#}
    {{ assets.cachedOutputCss('default_css') }}

    {% include "include/ie-support.volt" %}

    {%- if recaptcha.isEnabled() -%}
        {{- recaptcha.getJs() -}}
    {%- endif -%}

    <title>{{ get_title(false) ~ ' - ' ~ config.application.name }}</title>
</head>
<body>
    {%- block header -%}
        {%- include "include/header" with [
            'description': config.application.description,
            'application_name': config.application.name,
            'base_url': config.application.url
        ] -%}
    {%- endblock -%}

    {%- block breadcrumbs -%}{%- endblock -%}

    {%- block content -%}
        {{ content() }}
    {%- endblock -%}

    {%- block footer -%}
        <!-- footer -->
    {%- endblock -%}

    {{ assets.cachedOutputJs('default_js') }}
</body>
</html>

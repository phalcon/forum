<!doctype html>
<!--[if IE 8]> <html lang="en-US" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en-US" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en-US" class="no-js">
<!--<![endif]-->
<head>
    {%- set url = url(), theme = session.get('identity-theme') -%}

    {%- if config.social is defined and not(config.social.twitter_name is empty) -%}
        {%- set twitter_name = config.social.twitter_name -%}
    {%- else%}
        {%- set twitter_name = "" -%}
    {%- endif -%}

    {%- include "include/base-meta" with ['name': config.site.name] -%}

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
        {%- set canonical_url = config.site.url ~ '/' ~ canonical,
                author_picture = gravatar(post.user.email),
                author_picture_alt = post.user.name
        -%}
    {%- else -%}
        {%- set canonical_url = config.site.url,
                author_picture = config.site.url ~ '/img/logo.png',
                author_picture_alt = config.site.name
        -%}
    {%- endif -%}

    {%- block seo -%}
        {%- include "include/seo" with [
            'description': config.site.description,
            'keywords': config.site.keywords,
            'canonical_url': canonical_url
        ] -%}
    {%- endblock -%}

    {%- block social -%}
        {%- include "include/social-meta" with [
            'description': config.site.description,
            'keywords': config.site.keywords,
            'name': config.site.name,
            'base_url': config.site.url,
            'twitter_name': twitter_name,
            'canonical_url': canonical_url,
            'author_picture': author_picture,
            'author_picture_alt': author_picture_alt
        ] -%}
    {%- endblock -%}

    {%- block page_passport -%}
        {%- if action_name is not "index" and not(post_user_id is empty) -%}
            {%- include "include/page-passport" with [
                'base_url': config.site.url,
                'post_user_name': post_user_name,
                'post_user_id': post_user_id,
                'post_user_login': post_user_login
            ] -%}
        {%- else -%}
            {%- include "include/no-page-passport" with [
                'name': config.site.name,
                'base_url': config.site.url
            ] -%}
        {%- endif -%}
    {%- endblock -%}

    {% include "include/ie-support.volt" -%}
</head>
<body>
    {%- block head -%}{%- endblock -%}
    {%- block body -%}{%- endblock -%}
</body>
</html>

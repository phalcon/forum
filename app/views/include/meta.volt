<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<meta name=generator content="{{ config.site.name }}">
<meta name="description" content="{{ config.site.description }}">
<meta name="keyword" content="{{ config.site.keywords }}">

{%- if actionName is not "index" and post is defined and not(post is empty) -%}
    <meta name="author" content="{{ post.user.name }}">
    {#- @todo: introduce Google Plus profile here -#}
    <link rel="author" href="{{ config.site.url ~ '/user/' ~ post.user.id ~ '/' ~ post.user.login }}">
{%- else -%}
    <meta name="publisher" content="{{ config.site.name }}">
    <link rel="publisher" href="{{ config.site.url }}">
{%- endif -%}

{%- if canonical is defined and not(canonical is empty) -%}
    <link rel="canonical" href="{{ config.site.url ~ '/' ~ canonical }}">
    <meta property="og:url" content="{{ config.site.url ~ '/' ~ canonical }}">
    <meta property="og:image" content="{{ gravatar(post.user.email) }}">
    <meta name="twitter:image" content="{{ gravatar(post.user.email) }}">
    <meta name="twitter:image:alt" content="{{ post.user.name }}">
    {#- @todo: introduce Twitter profile here -#}
{%- else -%}
    <link rel="canonical" href="{{ config.site.url }}">
    <meta property="og:url" content="{{ config.site.url }}">
    <meta property="og:image" content="{{ config.site.url ~ '/img/logo.png' }}">
    <meta name="twitter:image" content="{{ config.site.url ~ '/img/logo.png' }}">
    <meta name="twitter:image:alt" content="{{ config.site.name }}">
    {%- if config.social is defined and not(config.social.twitter_name is empty) -%}
        <meta name="twitter:site" content="@{{ config.social.twitter_name }}">
        <meta name="twitter:creator" content="@{{ config.social.twitter_name }}">
    {%- endif -%}
{%- endif -%}

<meta property="og:title" content="{{ get_title(false) ~ ' - ' ~ config.site.name }}">
<meta property="og:description" content="{{ config.site.description }}">
<meta property="og:locale" content="en_US">
<meta property="og:type" content="object">
<meta property="og:site_name" content="{{ config.site.name }}">

<meta name="twitter:title" content="{{ get_title(false) ~ ' - ' ~ config.site.name }}">
<meta name="twitter:description" content="{{ config.site.description }}">
<meta name="twitter:card" content="summary">

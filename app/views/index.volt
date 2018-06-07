<!doctype html>
<!--[if IE 8]> <html lang="en-US" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en-US" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en-US" class="no-js">
<!--<![endif]-->
<head>
    {%- set url = url(), theme = session.get('identity-theme') -%}

    {%- if noindex is defined and noindex is true -%}
        {%- include "include/noindex-meta.volt" -%}
    {%- else -%}
        {%- include "include/meta.volt" -%}
    {%- endif -%}

    {%- include "include/icons.volt" -%}

    {%- if (not(config.analytics is empty)) -%}
        {%- include "include/analytics.volt" -%}
    {%- endif -%}

    {#- CSS resources from jsdelivr cannot be combined due to Bootstrap icons -#}
    {%- if theme == 'L' -%}
        {{ assets.cachedOutputCss('globalWhiteCss') }}
    {%- else -%}
        {{ assets.cachedOutputCss('globalCss') }}
    {%- endif -%}
    {{ assets.cachedOutputCss('editorCss') }}

    {#- reCaptcha -#}
    {%- if recaptcha.isEnabled() -%}
        {{- recaptcha.getJs() -}}
    {%- endif -%}

    <title>{{ get_title(false) ~ ' - ' ~ config.site.name }}</title>
</head>
<body class="with-top-navbar">
    {{ content() }}
    {{ assets.cachedOutputJs('globalJs') }}
    {{ assets.cachedOutputJs('editorJs') }}

    <script type="text/javascript">Forum.initializeView('{{ url() }}');</script>
</body>
</html>

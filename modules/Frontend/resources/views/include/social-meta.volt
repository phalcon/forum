{#- @todo: introduce Google Plus profile here -#}

<meta property="og:url" content="{{ canonical_url }}">
<meta property="og:image" content="{{ author_picture }}">

<meta property="og:title" content="{{ get_title(false) ~ ' - ' ~ name }}">
<meta property="og:description" content="{{ description }}">
<meta property="og:locale" content="en_US">
<meta property="og:type" content="object">
<meta property="og:site_name" content="{{ name }}">

{#- @todo: introduce Twitter profile here -#}

<meta name="twitter:image" content="{{ author_picture }}">
<meta name="twitter:image:alt" content="{{ author_picture_alt }}">

{%- if not(twitter_name is empty) -%}
    <meta name="twitter:site" content="@{{ twitter_name }}">
    <meta name="twitter:creator" content="@{{ twitter_name }}">
{%- endif -%}

<meta name="twitter:title" content="{{ get_title(false) ~ ' - ' ~ name }}">
<meta name="twitter:description" content="{{ description }}">
<meta name="twitter:card" content="summary">

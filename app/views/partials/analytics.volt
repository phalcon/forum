{%- set analytics_url = config.site.url, analytics_title = get_title(false) ~ ' - ' ~ config.site.name -%}

{%- if (canonical is defined and not(canonical is empty)) -%}
    {%- set analytics_url = config.site.url ~ '/' ~ canonical -%}
{%- endif -%}

<!-- Google Analytics -->
<script type="application/javascript">
    window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;
    ga("create", "{{ config.analytics }}", "auto");
    ga("send", "pageview", {
        "page": "{{ analytics_url }}",
        "title": "{{ analytics_title }}"
    });
</script>
<script async src='https://www.google-analytics.com/analytics.js'></script>
<!-- End Google Analytics -->

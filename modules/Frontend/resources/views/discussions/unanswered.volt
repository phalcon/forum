{# @todo: include 'partials/flash-banner.volt' #}

<div class="col-lg-10 col-md-9 discussions-unanswered">
    {{ content() }}

    {% include 'partials/list-posts.volt' %}
</div>

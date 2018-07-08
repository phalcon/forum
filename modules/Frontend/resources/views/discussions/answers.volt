{# @todo: include 'partials/flash-banner.volt' #}

<div class="col-lg-10 col-md-9 {{ controller_name }}-{{ action_name }}">
    {{ content() }}

    <div id="begin" class="forum-block activeBox">
        {{- partial('partials/order-posts') -}}

        {% include 'include/list-posts.volt' %}

        {%- if pager is defined -%}
            {{- partial('partials/paginate', ['pager': pager]) -}}
        {%- endif -%}
    </div>
</div>

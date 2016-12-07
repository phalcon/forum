{% if actionName is 'index' and controllerName is not 'error' or (actionName is 'view' and controllerName is 'categories') %}
<div class="container footer-statistic">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Statistics</div>
                <div class="panel-body">
                    Our users have posted a total of <strong>{{ threads }}</strong> Posts<br>
                    We have <strong>{{users}}</strong> registered users<br>
                    {%- if users_latest is defined and users_latest is not empty  -%}
                        The newest member is <strong>{{ users_latest }}</strong>
                    {% endif %}
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Latest Threads</div>
                <div class="panel-body">
                    {%- for last_thread in last_threads -%}
                        {{- link_to('discussion/' ~ last_thread.id_post ~ '/' ~ last_thread.slug_post, last_thread.title_post|e) -}}&nbsp; posted by {{ last_thread.name_user }} ({{ last_thread.name_category }})<br>
                    {%- endfor -%}
                </div>
            </div>
        </div>
    </div>
</div>
{% endif %}

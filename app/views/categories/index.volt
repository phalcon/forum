{{ flashSession.output() }}

<div class="clearfix">
    <div class="col-lg-9 center-block">
        <div class="panel panel-default">
            <div class="panel-heading">Categories</div>

            <table class="table">
                <thead>
                <tr>
                    <th>#</th>
                    <th class="col-lg-9">Category Name</th>
                    <th></th>
                    <th>Last Message</th>
                </tr>
                </thead>
                <tbody>
                {%- for category in categories -%}
                    <tr>
                        <td>
                            {%- if logged != '' -%}
                                {%- if not_read[category.id].numRows() > 0 -%}
                                    {{ image("icon/new_some.png", "class": "img-rounded") }}
                                {%- else -%}
                                    {{ image("icon/new_none.png", "class": "img-rounded") }}
                                {%- endif -%}
                            {%- else -%}
                                {{ image("icon/new_none.png", "class": "img-rounded") }}
                            {%- endif -%}
                        </td>
                        <td>
                            {{ link_to(category.getUrl(), category.name) }}
                            <br><small>{{ category.description }}</small>
                        </td>
                        <td>
                            {{ posts_per_category[category.id] }} Threads
                        </td>
                        <td>
                            {%- if posts_per_category[category.id] > 0 -%}
                                {{ link_to('discussion/' ~ last_author[category.id][0].post1_id ~ '/' ~ last_author[category.id][0].post1_slug, last_author[category.id][0].post1_title) }}
                                <br> {{ last_author[category.id][0].name_user }}
                            {%- else -%}
                                ---
                            {%- endif -%}
                        </td>
                    </tr>
                {%- endfor -%}
                </tbody>
            </table>
        </div>
    </div>
</div>


{%- if posts|length -%}
    <br/>
    <div align="center">
        <table class="table table-striped list-discussions" width="90%">
            <tr>
                <th width="50%">Topic</th>
                <th class="hidden-xs">Users</th>
                <th class="hidden-xs">Category</th>
                <th class="hidden-xs">Replies</th>
                <th class="hidden-xs">Views</th>
                <th class="hidden-xs">Created</th>
                <th class="hidden-xs">Last Reply</th>
            </tr>
        {%- for post in posts -%}
            {%- if (post.votes_up - post.votes_down) <= -3 -%}
                {%- set row_class = "post-negative" -%}
            {%- else -%}
                {%- set row_class = "post-positive" -%}
            {%- endif -%}
            <tr class="{{ row_class }}">
                <td align="left">

                    {%- if post.sticked == "Y" -%}
                        <span class="glyphicon glyphicon-pushpin"></span>&nbsp;
                    {%- endif -%}
                    {{- link_to('discussion/' ~ post.id ~ '/' ~ post.slug, post.title|e) -}}
                    {%- if post.accepted_answer == "Y" -%}
                        &nbsp;<span class="label label-success">SOLVED</span>
                    {%- else -%}
                        {%- if post.canHaveBounty() -%}
                            &nbsp;<span class="label label-info">BOUNTY</span>
                        {%- endif -%}
                    {%- endif -%}

                </td>
                <td class="hidden-xs">
                    {%- cache "post-users-" ~ post.id -%}
                        {%- for id, user in post.getRecentUsers() -%}
                            <a href="{{ url("user/" ~ id ~ "/" ~ user[0]) }}" title="{{ user[0] }}">
                                {{ image(gravatar(user[1]), 'width': 24, 'height': 24, 'class': 'img-rounded') }}
                            </a>
                        {%- endfor -%}
                    {%- endcache -%}
                </td>
                <td class="hidden-xs">
                    <span class="category">{{ link_to('category/' ~ post.category.id ~ '/' ~ post.category.slug, post.category.name) }}</span>
                </td>
                <td class="hidden-xs" align="center">
                    <span class="big-number">{% if post.number_replies > 0 %}{{ post.number_replies }}{%endif %}</span>
                </td>
                <td class="hidden-xs" align="center">
                    <span class="big-number">{{ post.getHumanNumberViews() }}</span>
                </td>
                <td class="hidden-xs">
                    <span class="date">{{ post.getHumanCreatedAt() }}</span>
                </td>
                <td class="hidden-xs">
                    <span class="date">{{ post.getHumanModifiedAt() }}</span>
                </td>
            </tr>
        {%- endfor -%}
        </table>
    </div>
{%- endif -%}

<div class="col-md-12">
    <ul class="nav nav-tabs">
        {%- set orders = [
            'new': 'All discussions',
            'hot': 'Hot',
            'unanswered': 'Unanswered',
            'my': 'My discussions',
            'answers':'My answers'
        ] -%}
        {%- for order, label in orders -%}
            {%- if (order == 'my' or order == 'answers') and not session.get('identity') -%}
                {%- continue -%}
            {% endif -%}
            <li class="{%- if order == currentOrder -%}active{%- endif -%}">
                {{ link_to('discussions/' ~ order, label) }}
            </li>
        {%- endfor -%}
    </ul>
</div>

{%- if posts|length -%}
<div class="col-md-12">
    <br/>
    <div align="center">
        <table class="table table-striped list-discussions" width="90%">
            <tr>
                {%  if config.theme.use_topics_icon %}
                <th> &nbsp; </th>
                {%- endif -%}
                <th width="38%">Topic</th>
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
            <tr class="{% if post.sticked == "Y" %}row-sticked{% endif %} {{ row_class }}" itemscope itemtype="http://schema.org/Question">
                {%  if config.theme.use_topics_icon %}
                <td>
                    {%- if logged != '' -%}
                        {%- if readposts[post.id] is defined -%}
                            {{ image(config.theme.inactive_topic_icon, 'width': 24, 'height': 24, 'class': 'img-rounded') }}
                        {%- else -%}
                            {{ image(config.theme.active_topic_icon, 'width': 24, 'height': 24, 'class': 'img-rounded') }}
                        {%- endif -%}
                    {%- else -%}
                     {{ image(config.theme.inactive_topic_icon, "width": "24", "height": "24", "class": "img-rounded") }}
                    {%- endif -%}
                </td>
                {%- endif -%}
                <td align="left">

                    {%- if post.sticked == "Y" -%}
                        <span class="octicon octicon-pin"></span>&nbsp;
                    {%- endif -%}
                    <span itemprop="name">
                        {{- link_to('discussion/' ~ post.id ~ '/' ~ post.slug, post.title|e) -}}
                    </span>
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
                    <span class="big-number">
                        <span itemprop="answerCount">
                            {{- post.number_replies -}}
                        </span>
                    </span>
                </td>
                <td class="hidden-xs" align="center">
                    <span class="big-number">{{- post.getHumanNumberViews() -}}</span>
                </td>
                <td class="hidden-xs">
                    <time itemprop="dateCreated" datetime="{{ date('c', post.created_at) }}" class="date">
                        {{- post.getHumanCreatedAt() -}}
                    </time>
                </td>
                <td class="hidden-xs">
                    <span class="date">{{- post.getHumanModifiedAt() -}}</span>
                </td>
            </tr>
        {%- endfor -%}
        </table>
    </div>
</div>

    {%- if pager is defined -%}
        {{- partial('partials/paginate', ['pager': pager]) -}}
    {%- endif -%}

{%- else -%}
<div class="col-md-12" align="center">
    <div class="alert alert-info">There are no posts here</div>
</div>
{%- endif -%}

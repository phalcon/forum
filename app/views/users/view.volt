{{ content() }}

<hr>

<div align="center" class="container">
    <div class="user-profile">
        <table align="center">
            <tr>
                <td class="small hidden-xs" valign="top">
                    {{ image(gravatar.getAvatar(user.email), 'width': 64, 'height': 64, 'class': 'img-rounded') }}
                </td>
                <td align="left" valign="top">
                    <h1>{{ user.name|e }}</h1>
                    <span class="login">{{ user.login }}</span><br>
                    <p>
                        <span>joined <b>{{ date('M d/Y', user.created_at) }}</b></span><br>
                        <span>posts <b>{{ numberPosts }}</b></span> / <span>replies <b>{{ numberReplies }}</b></span><br>
                        <span>reputation <b>{{ user.karma }}</b></span><br>
                        <span>reputation ranking <b>#{{ ranking }}</b> of <b>{{ total_ranking }}</b></span><br>
                        <a href="https://github.com/{{ user.login }}">Github Profile</a>
                    </p>
                    <p>
                        {% for badge in user.badges %}
                            <button type="button" class="btn btn-default btn-sm badge"><span class="badge3"></span> {{ badge.badge }}</button>
                        {% endfor %}
                    </p>
                    <p>
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#">Recent Activity</a><li>
                    </ul>
                    </p>
                    <p>
                    <table class="table table-striped">
                        {%- for activity in activities -%}
                            {%- if activity.post and activity.post.deleted != 1 -%}
                                <tr><td>
                                        {%- if activity.type == 'U' -%}
                                            has joined the forum
                                        {%- elseif activity.type == 'P' -%}
                                            has posted {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.post.title|e) }}
                                        {%- elseif activity.type == 'C' -%}
                                            has commented in {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.post.title|e) }}
                                        {%- endif -%}
                                        &nbsp;<span class="date">{{ activity.getHumanCreatedAt() }}</span>
                                    </td></tr>
                            {%- endif -%}
                        {%- endfor -%}
                    </table>
                    </p>
                </td>
            </tr>
        </table>
    </div>
</div>

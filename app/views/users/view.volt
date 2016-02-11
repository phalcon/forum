{{ content() }}

<div class="row profile">
    <div class="col-md-3">
        <div class="profile-sidebar" itemscope itemtype="http://schema.org/Person">
            <div class="profile-avatar">
                {{ image(avatar, 'class': 'img-responsive', 'itemprop': 'image') }}
            </div>
            <div class="profile-title">
                <div class="profile-title-name">
                    <h1>
                        <span class="user-name" itemprop="name">{{ user.name|e }}</span>
                        <span class="user-login" itemprop="additionalName">{{ user.login }}</span>
                    </h1>
                </div>
            </div>
            <div class="profile-buttons">
                <!-- todo -->
            </div>
            <div class="profile-info">
                <ul class="nav">
                    <li>
                        <span class="octicon octicon-clock"></span>&nbsp;<span>Joined {{ date('M d, y', user.created_at) }}</span>
                    </li>
                    <li>
                        <span class="octicon octicon-gist"></span>&nbsp;<span>Posts {{ numberPosts }}</span>
                    </li>
                    <li>
                        <span class="octicon octicon-comment-discussion"></span>&nbsp;<span>Replies {{ numberReplies }}</span>
                    </li>

                    <li>
                        <span class="octicon octicon-star"></span>&nbsp;<span>Reputation {{ user.karma }}</span>
                    </li>

                    <li>
                        <span class="octicon octicon-pulse"></span>&nbsp;<span>Reputation Ranking <b>#{{ ranking }}</b> of <b>{{ total_ranking }}</b></span>
                    </li>

                    <li>
                        <span class="octicon octicon-octoface"></span>&nbsp;<span><a href="https://github.com/{{ user.login }}">Github Profile</a></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="profile-content">
            <div class="row">
                <h3>User badges</h3>
                <p>
                    {% for badge in user.badges %}
                        <button type="button" class="btn btn-default btn-sm badge">
                            <span class="badge3"></span> {{ badge.badge }}
                        </button>
                    {% endfor %}
                </p>
            </div>
            <div class="row">
                <h3>Recent Activity</h3>
                <div class="naw news public_news">
                    {%- for activity in activities -%}
                        {%- if activity.post and activity.post.deleted != 1 -%}
                            <div class="activity-list">
                                <div class="activity-list-body">
                                    {%- if activity.type == 'U' -%}
                                        {% set icon = 'octicon-organization' %}
                                    {%- elseif activity.type == 'P' -%}
                                        {% set icon = 'octicon-file-text' %}
                                    {%- elseif activity.type == 'C' -%}
                                        {% set icon = 'octicon-comment-discussion' %}
                                    {%- else -%}
                                        {% set icon = 'octicon-star' %}
                                    {%- endif -%}

                                    <span class="octicon {{ icon }} dashboard-event-icon"></span>
                                    <time>{{ activity.getHumanCreatedAt() }}</time><br>
                                    {%- if activity.type == 'U' -%}
                                        has joined the forum
                                    {%- elseif activity.type == 'P' -%}
                                        has posted {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.post.title|e) }}
                                    {%- elseif activity.type == 'C' -%}
                                        has commented in {{ link_to('discussion/' ~ activity.post.id ~ '/' ~ activity.post.slug, activity.post.title|e) }}
                                    {%- endif -%}
                                </div>
                            </div>
                        {%- endif -%}
                    {%- endfor -%}
                </div>
            </div>
        </div>
    </div>
</div>

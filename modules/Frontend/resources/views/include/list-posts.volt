<div class="topic-list">
    {%- if posts|length -%}
        {%- for topic in posts -%}
            {%- if (topic.reply_time != null) -%}
                {%- set topic.p.modifiedAt = topic.reply_time -%}
            {%- endif -%}

            {%- set topic.p.numberReplies = topic.count_replies,
                topic = topic.p,
                vote_class = "vote-relative",
                vote_sign = "",
                vote_count = topic.votesUp - topic.votesDown,
                vote_span = '<span itemprop="upvoteCount">' ~ vote_count ~ '</span>',
                topic_uri = "/discussion/" ~ topic.id ~ "/" ~ topic.slug
            -%}

            {%- if vote_count <= -3 -%}
                {%-
                    set vote_class = "vote-negative",
                    vote_sign = "-",
                    vote_span = '<span itemprop="downvoteCount">' ~ vote_count ~ '</span>'
                -%}
            {%- elseif vote_count > 0 -%}
                {%-
                    set vote_class = "vote-positive",
                    vote_sign = "+"
                -%}
            {%- endif -%}
            <div class="topic-item {% if topic.sticked == "Y" %}topic-sticked{% endif %}" itemscope itemtype="http://schema.org/Question">
                <div class="topic-head">
                    <div class="img-holder">
                        <span itemprop="author">
                            <a href="/user/{{ topic.user.id }}/{{ topic.user.login }}" title="{{ topic.user.login }}">
                                {{ image(gravatar(topic.user.email), 'class': 'img-fluid', 'alt': topic.user.name) }}
                            </a>
                        </span>

                        {% if topic.sticked == "Y" %}
                            <span class="img-badge img-badge-sticked">
                                <i class="fa fa-thumb-tack"></i>
                            </span>
                        {% endif %}
                    </div>
                    <div class="headRight">
                        <a href="{{ topic_uri }}" class="discussion-head">
                            <h3 class="discussion-title" itemprop="name">
                                {{ topic.title|e }}
                            </h3>
                        </a>
                        <ul>
                            {%- set category_class = 'category-label category-' ~ topic.categoryId ~ '-label'  -%}
                            <li>
                                <a href="/category/{{ topic.categoryId }}/{{ topic.category.slug }}">
                                    <span class="{{ category_class }}"></span>{{ topic.category.name }}
                                </a>
                            </li>
                            <li>
                                <time title="Created Date" class="iconic-help">
                                    {# @todo: humanize date #}
                                    <i class="zmdi zmdi-calendar-alt"></i>7d ago
                                </time>
                            </li>
                            <li>
                                <time itemprop="dateCreated" datetime="{{ date('c', topic.createdAt) }}" title="Last Reply" class="iconic-help">
                                    {# @todo: humanize date #}
                                    <i class="zmdi zmdi-time"></i>May 17
                                </time>
                            </li>
                            <li>
                                <span title="Total Replies" class="iconic-help">
                                    <i class="zmdi zmdi-comment"></i>
                                    {# @todo: pluralize 'replies' #}
                                    <span itemprop="answerCount">{{ topic.numberReplies }}</span>&nbsp;replies
                                </span>
                            </li>
                            <li>
                                <span title="Total Views" class="iconic-help">
                                    {# @todo: pluralize 'views' #}
                                    <i class="zmdi zmdi-eye"></i>2934&nbsp;views
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="topic-detail">
                    <div class="topic-counters" onclick="window.location.href='{{ topic_uri }}'">
                        <div class="topic-votes">
                            <div class="mini-counts {{ vote_class }}">
                                {{ vote_sign ~ vote_span }}
                            </div>
                        </div>
                    </div>
                    <div class="topic-excerpt">
                        <p>{{ topic.content|teaser }}</p>
                    </div>
                </div>
            </div>
        {%- endfor -%}
    {%- else -%}
        <div class="jumbotron" style="margin: 1em 0">
            <h1 class="display-4">There! Caught up.</h1>
            <p class="lead">
                There are no new posts here. Set your mind to something new.
            </p>
        </div>
    {%- endif -%}
</div>
<div class="clearfix"></div>

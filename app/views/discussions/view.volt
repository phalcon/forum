{{- content() -}}

{% include 'partials/flash-banner.volt' %}

{%-
    set currentUser  = session.get('identity'),
        moderator    = session.get('identity-moderator'),
        tokenKey     = security.getPrefixedTokenKey('post-' ~ post.id),
        token        = security.getPrefixedToken('post-' ~ post.id),
        postAutorUrl = 'user/' ~ post.user.id ~ '/' ~ post.user.login
-%}

{%- if (post.votes_up - post.votes_down) <= -3 -%}
    {%- include 'partials/post/negative.volt' -%}
{%- elseif post.accepted_answer == 'Y' -%}
    {%- include 'partials/post/accepted.volt' -%}
{%- elseif post.canHaveBounty() -%}
    {%- include 'partials/post/bounty' with ['post': post] -%}
{%- endif -%}

<div itemscope itemtype="http://schema.org/Question">
    {%- include 'partials/post/breadcrumbs' with ['post': post] -%}
    {%- include 'partials/post/post-title' with ['post': post] -%}

    {%- if moderator == 'Y' -%}
        {%- include 'partials/post/moderator-nav' with ['post': post] -%}
    {%- endif -%}

    <div class="discussion">
        <div class="row reply-block">
            <div class="col-md-1 small" align="center">
                {{ image(gravatar.getAvatar(post.user.email), 'width': 48, 'height': 48, 'class': 'img-rounded') }}<br>
                <span itemprop="author" itemscope itemtype="http://schema.org/Person">
                    {{- link_to(postAutorUrl, '<span itemprop="name">' ~ post.user.name|e ~ '</span>', 'class': 'user-moderator-' ~ post.user.moderator) -}}
                </span><br>
                <span class="karma">{{ post.user.getHumanKarma() }}</span>
            </div>

            <div class="col-md-11 post-body{% if (post.votes_up - post.votes_down) <= -3 %} post-negative-body{% endif %}">
                <div class="posts-buttons" align="right">
                    {% if post.edited_at > 0 %}
                        <span class="action-date action-edit" data-id="{{ post.id }}" data-toggle="modal" data-target="#historyModal">
                            edited <span>{{ post.getHumanEditedAt() }}</span>
                        </span><br/>
                    {% endif %}
                    <a name="C{{ post.id }}" href="#C{{ post.id }}">
                        <time class="action-date">{{ post.getHumanCreatedAt() }}</time>
                    </a>
                </div>

                <div class="post-content">
                    {%- cache "post-body-" ~ post.id -%}
                        <div itemprop="text">
                            {{- markdown.render(post.content|e) -}}
                        </div>
                    {%- endcache -%}

                    {% if post.hasPoll() %}
                        {% if voted %}
                            {% include 'partials/poll-votes' with ['post': post, 'result': voting] %}
                        {% else %}
                            {% include 'partials/poll-options' with ['post': post, 'currentUser': currentUser] %}
                        {% endif %}
                    {% endif %}
                </div>

                <div class="posts-buttons" align="right">
                    {%-
                        include 'partials/post/posts-buttons' with [
                            'post': post,
                            'currentUser': currentUser,
                            'moderator': moderator,
                            'tokenKey': tokenKey,
                            'token': token
                        ]
                    -%}
                </div>
            </div>
        </div>

        {%- for reply in post.replies -%}
          {{-
              partial('partials/post/reply', [
                  'post': post,
                  'reply': reply,
                  'gravatar': this.gravatar,
                  'markdown': this.markdown,
                  'moderator': moderator,
                  'currentUser': currentUser
              ])
          -}}
        {%- endfor -%}
        {%- if post.locked != 'Y' -%}
            {{-
                partial('partials/post/comment-form', [
                  'post': post,
                  'gravatar': this.gravatar,
                  'currentUser': currentUser,
                  'tokenKey': tokenKey,
                  'token': token
                ])
            -}}
        {%- endif -%}
    </div>

    {{- hidden_field('post-id', 'value': post.id) -}}
    <div id="suggested-posts"></div>
    <div id="sticky-progress" style='display:none'></div>
</div>

{%- include 'partials/post/history-modal.volt' -%}

{%- include 'partials/post/error-modal.volt' -%}

{%- if currentUser -%}
    {%- include 'partials/post/reply-popup' with ['post': post, 'tokenKey': tokenKey, 'token': token] -%}
{%- endif -%}

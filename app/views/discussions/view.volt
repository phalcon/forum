{{- content() -}}

{% include 'partials/flash-banner.volt' %}

{%-
    set currentUser  = session.get('identity'),
        moderator    = session.get('identity-moderator'),
        tokenKey     = security.getPrefixedTokenKey('post-' ~ post.id),
        token        = security.getPrefixedToken('post-' ~ post.id),
        postAutorUrl = 'user/' ~ post.user.id ~ '/' ~ post.user.login,
        postAutorName= '<span itemprop="name">' ~ post.user.name|e ~ '</span>'
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
    {%-
        include 'partials/post/post-title' with [
            'post': post,
            'postAutorName': postAutorName,
            'postAutorUrl': postAutorUrl
        ]
    -%}

    <div class="discussion">
        <div class="row reply-block">
            <div class="col-md-1 col-sm-1 hidden-xs text-center">
                {{ image(gravatar(post.user.email), 'class': 'img-rounded avatar') }}<br>
                <span itemprop="author" itemscope itemtype="http://schema.org/Person" class="avatar-name">
                    {{- link_to(postAutorUrl, postAutorName, 'class': 'user-moderator-' ~ post.user.moderator) -}}
                </span>
                <span class="karma">{{ post.user.getHumanKarma() }}</span>
            </div>
            <div class="col-md-11 col-sm-11 col-xs-12 post-body{% if (post.votes_up - post.votes_down) <= -3 %} post-negative-body{% endif %}">
                {%- include 'partials/post/post-date' with ['post': post, 'is_edited': is_edited] -%}
                <div class="post-content">
                    {%- cache "post-body-" ~ post.id -%}
                        <div>
                            {{- markdown.render(post.content) -}}
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

                <div class="posts-buttons text-right">
                    {%-
                        include 'partials/post/post-buttons' with [
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

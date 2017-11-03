{%- if reply.accepted == 'Y' -%}
    {%- set answerType = 'suggestedAnswer acceptedAnswer' -%}
{%- else  -%}
    {%- set answerType = 'suggestedAnswer' -%}
{%- endif -%}

{%- if (reply.votes_up - reply.votes_down) <= -3 -%}
    {%- set answerClass1 = 'reply-block row reply-negative' -%}
{%- else  -%}
    {%- set answerClass1 = 'reply-block row' -%}
{%- endif -%}

{%- if (reply.votes_up - reply.votes_down) >= 4 -%}
    {%- set answerClass2 = ' reply-positive' -%}
{%- else -%}
    {%- set answerClass2 = '' -%}
{% endif %}

{%- if reply.accepted == 'Y' -%}
    {%- set answerClass3 = ' reply-accepted' -%}
{%- else -%}
    {%- set answerClass3 = '' -%}
{%- endif -%}

{%- set replyAuthorUrl = 'user/' ~ reply.user.id ~ '/' ~ reply.user.login -%}

<div itemprop="{{ answerType }}" itemscope itemtype="http://schema.org/Answer" class="{{ answerClass1 ~ answerClass2 ~ answerClass3}}">
    <div class="col-md-1 small" align="center">
        {{ image(gravatar(reply.user.email), 'class': 'img-rounded') }}
        <br>

        <span itemprop="author" itemscope itemtype="http://schema.org/Person">
            {{ link_to(replyAuthorUrl, '<span itemprop="name">' ~ reply.user.name|e ~ '</span>', 'class': 'user-moderator-' ~ reply.user.moderator) }}
        </span>
        <br>

        <span class="karma">{{ reply.user.getHumanKarma() }}</span>
        {%- if reply.accepted == 'Y' -%}
            <div class="accepted-reply">
                <span class="glyphicon glyphicon-ok"></span>
                Accepted<br>answer
            </div>
        {%- endif -%}
    </div>
    <div class="col-md-11">
        {%- if reply.in_reply_to_id > 0 -%}
            {%- if reply.postReplyTo -%}
                <div class="in-reply-to">
                    <a href="#C{{ reply.in_reply_to_id }}"><span class="glyphicon glyphicon-chevron-up"></span> in reply to
                        {{ image(gravatar(reply.postReplyTo.user.email), 'width': 24, 'height': 24, 'class': 'img-rounded') }}
                        {{ reply.postReplyTo.user.name }}
                    </a>
                </div>
            {%- endif -%}
        {%- endif -%}
        <div class="posts-buttons" align="right">
            {%- if reply.edited_at > 0 -%}
                <span class="action-date action-reply-edit" data-id="{{ reply.id }}" data-toggle="modal" data-target="#historyModal">
                edited <span>{{ reply.getHumanEditedAt() }}</span>
              </span><br/>
            {%- endif -%}
            <a name="C{{ reply.id }}" href="#C{{ reply.id }}">
                <time itemprop="dateCreated" datetime="{{ date('c', reply.created_at) }}" class="action-date">{{ reply.getHumanCreatedAt() }}</time>
            </a>
        </div>
        <div class="post-content">
            {%- cache "reply-body-" ~ reply.id -%}
            <div itemprop="text">
                {{- markdown.render(reply.content) -}}
            </div>
            {%- endcache -%}
        </div>
        <div class="posts-buttons" align="right">
            {%- if currentUser == post.users_id or moderator == 'Y' -%}
                <br>
                {%- if post.accepted_answer != 'Y' -%}
                    <a class="btn btn-default btn-xs reply-accept" data-id="{{ reply.id }}">
                        <span class="glyphicon glyphicon-ok"></span>&nbsp;Accept<span class="hidden-xs"> Answer</span>
                    </a>&nbsp;
                {%- endif -%}
            {%- endif -%}
            {%- if reply.users_id == currentUser or moderator == 'Y' -%}
                <a class="btn btn-default btn-xs reply-edit" data-id="{{ reply.id }}">
                    <span class="glyphicon glyphicon-pencil"></span>&nbsp;Edit
                </a>
                <a class="btn btn-default btn-xs reply-remove" data-id="{{ reply.id }}">
                    <span class="glyphicon glyphicon-remove"></span>&nbsp;Delete
                </a>&nbsp;
            {%- endif -%}
            {%- if currentUser -%}
                {%- if reply.users_id != currentUser -%}
                    <a class="btn btn-default btn-xs reply-reply" data-id="{{ reply.id }}">
                        <span class="glyphicon glyphicon-share-alt"></span>&nbsp;Reply
                    </a>&nbsp;
                {%- endif -%}
                <a href="#" onclick="return false" class="btn btn-danger btn-xs vote-reply-down" data-id="{{ reply.id }}">
                    <span class="glyphicon glyphicon-thumbs-down"></span>
                    {{ reply.votes_down }}
                </a>
                <a href="#" onclick="return false" class="btn btn-success btn-xs vote-reply-up" data-id="{{ reply.id }}">
                    <span class="glyphicon glyphicon-thumbs-up"></span>
                    {{ reply.votes_up }}
                </a>
            {%- else -%}
                <a href="#" onclick="return false" class="btn btn-danger btn-xs vote-login" data-id="{{ reply.id }}">
                    <span class="glyphicon glyphicon-thumbs-down"></span>
                    {%- if reply.votes_down -%}
                        <span itemprop="downvoteCount">{{ reply.votes_down }}</span>
                    {%- else -%}
                        {{ reply.votes_down }}
                    {%- endif -%}
                </a>
                <a href="#" onclick="return false" class="btn btn-success btn-xs vote-login" data-id="{{ reply.id }}">
                    <span class="glyphicon glyphicon-thumbs-up"></span>
                    {%- if reply.votes_up -%}
                        <span itemprop="upvoteCount">{{ reply.votes_up }}</span>
                    {%- else -%}
                        {{ reply.votes_up }}
                    {%- endif -%}
                </a>
            {%- endif -%}
        </div>
    </div>
</div>

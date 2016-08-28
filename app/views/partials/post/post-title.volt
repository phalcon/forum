{%- set postAutorUrl = 'user/' ~ post.user.id ~ '/' ~ post.user.login  -%}
{%- set postAutorName = '<span itemprop="name">' ~ post.user.name|e ~ '</span>'  -%}

<div class="row table-title">
    <div class="col-lg-8 col-md-7 col-sm-6 col-xs-12 wrapper-author">
        <h1 class="{% if (post.votes_up - post.votes_down) <= -3 %}post-negative-h1{% endif %}" itemprop="name">
            {{- post.title|e -}}
        </h1>
        <div class="visible-xs-block mobile-author">
            <span itemprop="author" itemscope itemtype="http://schema.org/Person">
                {{- link_to(postAutorUrl, postAutorName, 'class': 'user-moderator-' ~ post.user.moderator) -}}
            </span>
            <time itemprop="dateCreated" datetime="{{ date('c', post.created_at) }}">
                {{- post.getHumanCreatedAt() -}}
            </time>
        </div>
    </div>
    <div class="col-lg-4 col-md-5 col-sm-6 hidden-xs text-right wrapper-stats">
        <table class="table-stats" width="100%">
            <tr style="vertical-align: top;">
                <td>
                    <label>Created</label><br>
                    <time itemprop="dateCreated" datetime="{{ date('c', post.created_at) }}">
                        {{- post.getHumanCreatedAt() -}}
                    </time>
                </td>
                <td>
                    <label>Last Reply</label><br>
                    {{- post.getHumanModifiedAt() ? post.getHumanModifiedAt() : "None" -}}
                </td>
                <td>
                    <label>Replies</label><br>
                    <span itemprop="answerCount">
              {{- post.number_replies -}}
            </span>
                </td>
                <td>
                    <label>Views</label><br>
                    {{- post.number_views -}}
                </td>
                <td>
                    <label>Votes</label><br>
                    <span itemprop="upvoteCount">{{- post.votes_up - post.votes_down -}}</span>
                </td>
            </tr>
        </table>
    </div>
</div>

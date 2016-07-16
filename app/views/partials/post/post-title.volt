<div class="row table-title">
    <div class="col-md-8">
        <h1 class="{% if (post.votes_up - post.votes_down) <= -3 %}post-negative-h1{% endif %}" itemprop="name">
            {{- post.title|e -}}
        </h1>
    </div>
    <div class="col-md-4">
        <table class="table-stats">
            <tr>
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

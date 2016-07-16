<div class="row">
    {%- if currentUser -%}
        <div class="col-md-1 small" align="center">
            <img src="https://secure.gravatar.com/avatar/{{ session.get('identity-gravatar') }}?s=48&amp;r=pg&amp;d=identicon" class="img-rounded" width="48" height="48"><br>
            <span>{{ link_to('', 'You') }}</span>
        </div>
        <div class="col-md-11">

            <ul class="nav nav-tabs preview-nav">
                <li class="active"><a href="#" onclick="return false">Comment</a></li>
                <li><a href="#" onclick="return false">Preview</a></li>
                <li class="pull-right">{{ link_to('help/markdown', 'Help', 'target': '_blank') }}</li>
            </ul>

            <form method="post" autocomplete="off" role="form">
                {{ hidden_field(tokenKey, "value": token, "id": "csrf-token") }}
                <p>
                <div id="comment-box">
                    {{- hidden_field('id', 'value': post.id) -}}
                    {{- text_area("content", "rows": 5, "class": "form-control") -}}
                </div>
                <div id="preview-box" style="display:none"></div>
                </p>
                <p>
                <div class="pull-left">
                    {{- link_to('', 'Back to discussions') -}}
                </div>
                <div class="pull-right">
                    <button type="submit" class="btn btn-success">Add Comment</button>
                </div>
                </p>
            </form>
        </div>
    {%- else -%}
        <div class="col-md-1 small" align="center"></div>
        <div class="col-md-11 login-comment">
            <div class="pull-right">
                {{- link_to('login/oauth/authorize', 'Log In to Comment', 'class': 'btn btn-primary') -}}
            </div>
        </div>
    {%- endif -%}
</div>

<div class="row">
    {%- if currentUser -%}
        <div class="col-md-1 small" align="center">
            {{ image(gravatar(session.get('identity-email')), 'width': 48, 'height': 48, 'class': 'img-rounded') }}
            <div>
                {{ link_to('user/' ~ session.get('identity') ~ '/me', 'You') }}
            </div>
        </div>
        <div class="col-md-11">

            <ul class="nav nav-tabs preview-nav">
                <li class="active"><a href="#" onclick="return false">Comment</a></li>
                <li><a href="#" onclick="return false">Preview</a></li>
                <li class="pull-right">{{ link_to('help/markdown', 'Help', 'target': '_blank') }}</li>
            </ul>

            <form method="post" autocomplete="off" role="form">
                {{ hidden_field(tokenKey, "value": token, "id": "csrf-token") }}
                <div style="width: 100%">
                    <div id="comment-box">
                        {{- hidden_field('id', 'value': post.id) -}}
                        {{- text_area("content", "rows": 5, "class": "form-control") -}}
                    </div>
                    <div id="preview-box" style="display:none"></div>
                </div>
                <div style="width: 100%">
                    <div class="pull-left">
                        {{- link_to('/', 'Back to discussions') -}}
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn btn-success">Add Comment</button>
                    </div>
                </div>
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

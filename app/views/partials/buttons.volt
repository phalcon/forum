<div class="nav navbar-nav navbar-right navbar-login">
    <div class="btn-group">
        <a class="btn btn-sm btn-default" href="https://phalcon.link/fund" target="_blank">
            <span class="p-ico"></span>
            Support Us
        </a>
    </div>
    <div class="btn-group">
        {%- if session.get('identity') -%}
            {{- link_to(
            'post/discussion',
            '<span class="octicon octicon-megaphone"></span> Start a Discussion',
            'class': 'btn btn-sm btn-default',
            'rel': 'nofollow'
            ) -}}
        {%- else -%}
            {{- link_to(
            'login/oauth/authorize',
            '<span class="octicon octicon-octoface"></span> Log In',
            'class': 'btn btn-sm btn-default',
            'rel': 'nofollow',
            'title': 'Log In with Github'
            ) -}}
        {%- endif -%}
    </div>
</div>

<div class="nav navbar-nav navbar-right navbar-login">
    {%- if config.patreon is defined and config.patreon.enabled -%}
    <div class="btn-group">
        <a class="btn btn-sm btn-default" href="https://www.patreon.com/bePatron?u={{ config.patreon.id }}" target="_blank">
            <span class="p-ico"></span>
            Patron
        </a>
    </div>
    {% endif %}
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

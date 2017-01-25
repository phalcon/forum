<nav class="navbar navbar-fixed-top navbar-light bg-faded nav-top-menu" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#forum-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            {{ link_to('', config.site.name, 'class': 'navbar-brand', 'title': 'Go to main page') }}
        </div>

        <div class="collapse navbar-collapse" id="forum-navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li>{{- link_to('discussions', '<span class="octicon octicon-comment-discussion"></span>', 'title': 'Discussions') -}}</li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" id="dropdownSearch" role="button" aria-haspopup="true" aria-expanded="false" title="Search">
                        <span class="octicon octicon-search"></span> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownSearch">
                        <li>
                            <div style="">
                                {{- form('search', 'method': 'get', 'autocomplete': 'off') -}}
                                <div class="input-group search-group">
                                    <label class="sr-only" for="forum-search-input"></label>
                                    <input type="text" class="form-control input-sm" name="q" id="forum-search-input">
                                    <span class="input-group-btn">
                                        <input type="submit" class="btn btn-sm btn-primary" value="Search">
                                    </span>
                                </div>
                                {{- end_form() -}}
                                <!--<gcse:searchbox-only></gcse:searchbox-only>-->
                            </div>
                        </li>
                    </ul>
                </li>
                <li>{{ link_to('activity', '<span class="octicon octicon-eye"></span>', 'title': 'Activity') }}</li>
                {%- if session.get('identity') -%}
                    <li class="notification-container">
                        {{- link_to('notifications', '<span class="octicon octicon-globe"></span>', 'title': 'Notifications') -}}
                        {%- if notifications.has() -%}
                            <span class="notification-counter">{{ notifications.getNumber() }}</span>
                        {%- endif -%}
                    </li>
                {%- endif -%}
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle categories-link" data-toggle="dropdown" id="dropdownCategories" role="button" aria-haspopup="true" aria-expanded="false" title="Categories">
                        <span class="octicon octicon-list-unordered"></span> <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu categories-dropdown" aria-labelledby="dropdownCategories">
                        {%- cache "sidebar" -%}
                        {%- if categories is defined -%}
                            {%- for category in categories -%}
                                <li>
                                    {{-
                                    link_to('category/' ~ category.id ~ '/' ~ category.slug,
                                    '<span class="label label-default pull-right">' ~ category.number_posts ~ '</span>' ~ category.name)
                                    -}}
                                </li>
                            {%- endfor -%}
                        {%- endif -%}
                        {%- endcache -%}
                    </ul>
                </li>
                <li>{{ link_to('help', '<span class="octicon octicon-info"></span>', 'title': 'Help') }}</li>
                {%- if session.get('identity') -%}
                    <li>{{ link_to('settings', '<span class="octicon octicon-tools"></span>', 'title': 'Settings') }}</li>
                    <li>{{ link_to('logout', '<span class="octicon octicon-sign-out"></span>', 'title': 'Logout') }}</li>
                {%- endif -%}
            </ul>

            {{ partial("partials/buttons", ["config": this.config]) }}
        </div>
    </div>
</nav>

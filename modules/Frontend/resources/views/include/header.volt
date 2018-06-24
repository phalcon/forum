<header>
    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-lg-10 col-md-12 col-12">
                <div class="row">

                    {# ---- logo ---- #}
                    <div class="col-lg-2 col-md-4 col-5">
                        <div class="logoBox">
                            <a href="{{ base_url }}" title="{{ description }}">
                                {% set logo_src = base_url ~ '/img/logo-header.png?v=' ~ forum_version() %}
                                <img src="{{ logo_src }}" class="img-fluid" alt="{{ application_name }}">
                            </a>
                        </div>
                    </div>

                    {# ---- social bar ---- #}
                    <div class="col-lg-5 col-md-8 col-7 col-md-push-5">
                        <div class="socialBox">
                            <div class="social-box">
                                <ul>
                                    <li>
                                        {{- link_to('discussions', '<i class="zmdi zmdi-comments"></i>', 'title': 'Discussions') -}}
                                    </li>
                                    <li>
                                        {{ link_to('activity', '<i class="zmdi zmdi-eye"></i>', 'title': 'Activity') }}
                                    </li>
                                    {%- if session.get('identity') -%}
                                        <li>
                                            {{- link_to('notifications', '<i class="zmdi zmdi-notifications"></i>', 'title': 'Notifications') -}}
                                            {# @todo: notifications number #}
                                        </li>
                                    {%- endif -%}

                                    <li>
                                        {{ link_to('help', '<i class="zmdi zmdi-pin-help"></i>', 'title': 'Help') }}
                                    </li>

                                    {%- if session.get('identity') -%}
                                        <li>
                                            {{ link_to('activity', '<i class="zmdi zmdi-settings"></i>', 'title': 'Settings') }}
                                        </li>
                                        <li>
                                            {{ link_to('activity', '<i class="zmdi zmdi-sign-in"></i>', 'title': 'Logout') }}
                                        </li>
                                    {%- else -%}
                                        <div class="btns-group">
                                            {{ link_to('login/oauth/authorize', '<i class="zmdi zmdi-github"></i>Log In', 'title': 'Log In') }}
                                        </div>
                                    {%- endif -%}
                                </ul>
                            </div>
                        </div>
                    </div>

                    {# ---- search bar ---- #}
                    <div class="col-lg-5 col-md-12 col-sm-12 col-12  col-md-pull-5">
                        <div class="searchBar">
                            {{- form('search', 'method': 'get', 'autocomplete': 'off') -}}
                                {# @todo #}
                                <input type="search" class="form-control" placeholder="Search discussion" name="q" id="forum-search-input">
                                <span><i class="zmdi zmdi-search"></i></span>
                            {{- end_form() -}}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</header>

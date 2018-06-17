<header>
    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-lg-10 col-md-12 col-12">
                <div class="row">

                    {# ---- logo ---- #}
                    <div class="col-lg-2 col-md-4 col-5">
                        <div class="logoBox">
                            <a href="{{ base_url }}">
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
                                    <li><a href="#"><i class="zmdi zmdi-comments"></i></a></li>
                                    <li><a href="#"><i class="zmdi zmdi-notifications"></i></a></li>
                                    <li><a href="#"><i class="zmdi zmdi-pin-help"></i></a></li>
                                    {# @todo #}
                                    <li><a href="#"><i class="zmdi zmdi-pin-help"></i></a></li>
                                </ul>

                                <div class="btns-group">
                                    {# @todo #}
                                    <a href="#"><i class="zmdi zmdi-account-add"></i> Register </a>
                                    <a href="#"><i class="zmdi zmdi-github"></i> Log In </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    {# ---- search bar ---- #}
                    <div class="col-lg-5 col-md-12 col-sm-12 col-12  col-md-pull-5">
                        <div class="searchBar">
                            <form>
                                {# @todo #}
                                <input type="search" class="form-control" placeholder="Search discussion">
                                <span><i class="zmdi zmdi-search"></i></span>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</header>

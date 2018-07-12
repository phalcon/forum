<div class="col-lg-10 col-md-12 col-12">
    <div class="row">
        <div class="col-lg-5 col-md-5">
            <div class="footer-box">
                <h5><i class="zmdi zmdi-equalizer"></i>&nbsp;Statistics</h5>

                <p>
                    {# @todo: pluralize 'posts' and 'registered users' #}
                    Our users have posted a total of <span>{{ threads_count }}</span>&nbsp;posts<br>
                    We have <span>{{ users_count }}</span>&nbsp;registered users<br>
                    The newest member is <span><a href="/user/{{ last_user.id }}/{{ last_user.login }}">{{ last_user.name }}</a></span>


                </p>
            </div>
        </div>
        <div class="col-lg-7 col-md-7">
            <div class="footer-box">
                <h5><i class="zmdi zmdi-comments"></i>&nbsp;Latest Threads</h5>

                    {%- for last_thread in last_threads -%}
                        <p itemscope itemtype="http://schema.org/Question">
                            [<a href="/category/{{ last_thread.category_id }}/{{ last_thread.category_slug }}">{{ last_thread.category_name }}</a>]:
                            <span itemprop="name">
                                <a href="/discussion/{{ last_thread.post_id }}/{{ last_thread.post_slug }}">{{ last_thread.post_title|e }}</a>
                            </span>
                            by&nbsp;
                            <span class="footer-author" itemprop="author">
                                <a href="/user/{{ last_thread.user_id }}/{{ last_thread.user_login }}">{{ last_thread.user_name }}</a>
                            </span>
                        </p>
                    {%- endfor -%}
            </div>
        </div>
    </div>
</div>

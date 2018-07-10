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

                <p>
                    [Installation]: <span>Phalcon installation on ubuntu 16.04 LTS with XAMPP server</span> by Mateo Agudelo<br>
                    [General]: <span>Clearing form field</span> by Hoet<br>
                    [Developer Tools]: <span>Issue with Rewrite Rules</span> by Marcelo Pavan
                </p>
            </div>
        </div>
    </div>
</div>

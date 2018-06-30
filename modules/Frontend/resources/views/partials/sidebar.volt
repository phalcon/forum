<div class="col-lg-2 col-md-3">
    <div class="leftMenu">
        <div class="leftMain">
            <ul>
                <li class="{%- if action_name == 'new' -%}active{%- endif -%}">
                    {{- link_to('discussions/new', '<span><i class="zmdi zmdi-comment-alt-text"></i></span>All Discussions') -}}
                </li>
                <li class="hot {% if action_name == 'hot' -%}active{%- endif -%}">
                    {{- link_to('discussions/hot', '<span><i class="zmdi zmdi-fire"></i></span>Popular') -}}
                </li>
                <li class="{%- if action_name == 'unanswered' -%}active{%- endif -%}">
                    {{- link_to('discussions/unanswered', '<span><i class="zmdi zmdi-spinner"></i></span>Unanswered') -}}
                </li>

                {%- if (not(user_id is empty)) -%}
                    <li class="{%- if action_name == 'my' -%}active{%- endif -%}">
                        {{- link_to('discussions/my', '<span><i class="zmdi zmdi-comments"></i></span>My Discussions') -}}
                    </li>
                    <li class="{%- if action_name == 'answers' -%}active{%- endif -%}">
                        {{- link_to('discussions/answers', '<span><i class="zmdi zmdi-comment-more"></i></span>My Answers') -}}
                    </li>
                {%- endif -%}
            </ul>
        </div>
        <div class="menuLinks">
            <ul>
                <li class="active"><a href="javascript:void(0);" data-tag="begin"><span></span> Beginners</a></li>
                <li><a href="javascript:void(0);" data-tag="orm"><span></span> ORM</a></li>
                <li><a href="javascript:void(0);" data-tag="general"><span></span> General</a></li>
                <li><a href="javascript:void(0);" data-tag="mvc"><span></span> MVC</a></li>
                <li><a href="javascript:void(0);" data-tag="database"><span></span> Database</a></li>
                <li><a href="javascript:void(0);" data-tag="volt"><span></span> Volt</a></li>
                <li><a href="javascript:void(0);" data-tag="install"><span></span> Installation</a></li>
                <li><a href="javascript:void(0);" data-tag="cache"><span></span> Cache</a></li>
                <li><a href="javascript:void(0);" data-tag="offtopic"><span></span> Offtopic</a></li>
                <li><a href="javascript:void(0);" data-tag="config"><span></span> Configuration</a></li>
                <li><a href="javascript:void(0);" data-tag="security"><span></span> Security</a></li>
                <li><a href="javascript:void(0);" data-tag="micro"><span></span> Micro</a></li>
                <li><a href="javascript:void(0);" data-tag="odm"><span></span> ODM</a></li>
            </ul>
        </div>
    </div>
</div>

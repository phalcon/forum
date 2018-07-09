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
        <div class="sidebar-links">
            <ul>
                {%- for category in categories -%}
                    <li>
                        {%- set cssClass = 'category-' ~ category.getId() ~ '-label'  -%}

                        {{- link_to(
                            'category/' ~ category.getId() ~ '/' ~ category.getSlug(),
                            '<span class="' ~ cssClass ~ '"></span>' ~ category.getName(),
                            'data-tag': category.getSlug()
                        ) -}}
                    </li>
                {%- endfor -%}
            </ul>
        </div>
    </div>
</div>

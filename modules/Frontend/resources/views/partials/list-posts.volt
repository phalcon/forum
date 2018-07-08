<div id="begin" class="forum-block activeBox">
    <div class="desktop-leftMenu">
        <div class="createBox">
            <span>Sort by</span>
            <select class="form-control">
                <option value="">Date Created</option>
                <option value="">Date Created2</option>
                <option value="">Date Created3</option>
                <option value="">Date Created4</option>
            </select>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="topic-list">
        {%- if posts|length -%}
            {%- for topic in posts -%}
                <div class="topic-item">
                    <div class="topic-head">
                        <div class="imgHolder">
                            <a href="{{ url("user/" ~ topic.p.user.id ~ "/" ~ topic.p.user.login) }}" title="{{ topic.p.user.login }}">
                                {{ image(gravatar(topic.p.user.email), 'class': 'img-fluid', 'alt': topic.p.user.name) }}
                            </a>
                        </div>
                        <div class="headRight">
                            <a href="/discussion/{{ topic.p.id }}/{{ topic.p.slug }}" class="discussion-head">
                                <h3 class="discussion-title" itemprop="name">
                                    {{ topic.p.title|e }}
                                </h3>
                            </a>
                            <ul>
                                {%- set category_class = 'category-label category-' ~ topic.p.categoryId ~ '-label'  -%}
                                <li>
                                    <a href="/category/{{ topic.p.categoryId ~ '/' ~ topic.p.category.slug }}">
                                        <span class="{{ category_class }}"></span>{{ topic.p.category.name }}
                                    </a>
                                </li>
                                <li>
                                    <time itemprop="dateCreated" datetime="{{ date('c', topic.p.createdAt) }}">
                                        <i class="zmdi zmdi-calendar-alt"></i> May 17
                                    </time>
                                </li>
                                <li><a href="#"><i class="zmdi zmdi-time"></i> 7d ago</a></li>
                                <li><a href="#"><i class="zmdi zmdi-comment"></i> 68 replies</a></li>
                                <li><a href="#"><i class="zmdi zmdi-eye"></i> 2934 views</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="form-detail">
                        <div class="imgHolder" style="width: 26px; height: 52px"><!-- todo --></div>
                        <div class="topic-excerpt">
                            <p>{{ topic.p.content|teaser }}</p>
                        </div>
                    </div>
                </div>
            {%- endfor -%}
        {%- else -%}
            <div class="jumbotron" style="margin: 1em 0">
                <h1 class="display-4">There! Caught up.</h1>
                <p class="lead">
                    There are no new posts here. Set your mind to something new.
                </p>
            </div>
        {%- endif -%}
    </div>
    <div class="clearfix"></div>
    <div class="pageNav">
        <ul class="pagination">
            <li class="page-item"><a class="page-link" href="#"><i
                        class="zmdi zmdi-chevron-left"></i></a></li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">4</a></li>
            <li class="page-item"><a class="page-link" href="#">5</a></li>
            <li class="page-item"><a class="page-link" href="#">6</a></li>
            <li class="page-item"><a class="page-link" href="#">7</a></li>
            <li class="page-item"><a class="page-link" href="#">8</a></li>
            <li class="page-item"><a class="page-link" href="#">9</a></li>
            <li class="page-item"><a class="page-link" href="#">10</a></li>
            <li class="page-item"><a class="page-link" href="#"><i class="zmdi zmdi-chevron-right"></i></a>
            </li>
        </ul>
    </div>
</div>

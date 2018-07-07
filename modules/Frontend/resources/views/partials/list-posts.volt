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
                            <a href="{{ url("user/" ~ topic.p.user.getId() ~ "/" ~ topic.p.user.getLogin()) }}" title="{{ topic.p.user.getLogin() }}">
                                {{ image(gravatar(topic.p.user.getEmail()), 'class': 'img-fluid', 'alt': topic.p.user.getName()) }}
                            </a>
                        </div>
                        <div class="headRight">
                            <a href="/discussion/{{ topic.p.getId() }}/{{ topic.p.getSlug() }}" class="discussion-head">
                                <h3 class="discussion-title" itemprop="name">
                                    {{ topic.p.getTitle()|e }}
                                </h3>
                            </a>
                            <ul>
                                {%- set cssClass = 'category-' ~ topic.p.getCategoryId() ~ '-label'  -%}
                                <li><a href="#"><span class="category-label {{ cssClass }}"></span>{{ topic.p.category.getName() }}</a></li>
                                <li>
                                    <time itemprop="dateCreated" datetime="{{ date('c', topic.p.getCreatedAt()) }}">
                                        <i class="zmdi zmdi-calendar-alt"></i> May 17
                                    </time>
                                </li>
                                <li><a href="#"><i class="zmdi zmdi-time"></i> 7d ago</a></li>
                                <li><a href="#"><i class="zmdi zmdi-comment"></i> 68 replies</a></li>
                                <li><a href="#"><i class="zmdi zmdi-eye"></i> 2934 views</a></li>
                            </ul>
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

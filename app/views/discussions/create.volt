{{ content() }}

<div class="container start-discussion">

    <h1>Start a Discussion</h1>

    <div class="row">
        <div class="col-md-1 remove-image hidden-xs" align="right">
            <img src="https://secure.gravatar.com/avatar/{{ session.get('identity-gravatar') }}?s=48&amp;r=pg&amp;d=identicon"
                 class="img-rounded">
        </div>
        <div class="col-md-11">

             <div class="row">

                <div class="col-md-9">

                    <div class="bs-callout bs-callout-info">
                        <h4>Creating a new Post</h4>

                        <p> {{ config.site.project }} is an open source project and a volunteer effort.
                            Help us make this a great place for discussion and collaboration. Please spend some time browsing the
                            topics here
                            before replying or starting your own, and you'll have a better chance of meeting others who share
                            your interests or have had similar problems. If you want to report any bug related to {{ config.site.project }} or
                            suggest a new feature, please post it on <a href="{{ config.site.repo }}">Github</a> issues.</p>
                    </div>

                    {% if firstTime %}
                        <div class="bs-callout bs-callout-warning">
                            <h4>Your first post</h4>

                            <p>
                                You're about to create your first post in the forum.
                                Please take a few minutes to read {{ link_to('help/create-post', 'some recommendations') }}
                                to help others understand your problem and increase your chances of getting good answers
                                that help you solve it more effectively.
                            </p>
                        </div>
                    {% endif %}

                    <form method="post" autocomplete="off" role="form">
                        {{ hidden_field(security.getTokenKey(), "value": security.getToken()) }}

                        <div class="form-group">
                            <label>Title</label>
                            {{ text_field("title", "placeholder": "Title", "class": "form-control") }}
                        </div>

                        <div class="form-group">
                            <label>Category</label>
                            {{ select("categoryId", categories, 'using': ['id', 'name'], 'useEmpty': true, 'emptyText': 'Choose a category...', "class": "form-control") }}
                        </div>

                        <div class="form-group">

                            <ul class="nav nav-tabs preview-nav">
                                <li class="active"><a href="#" onclick="return false">Write</a></li>
                                <li><a href="#" onclick="return false">Preview</a></li>
                                <li class="pull-right">{{ link_to('help/markdown', 'Help', 'target': '_blank') }}</li>
                            </ul>

                            <div id="comment-box">
                                <div class="form-group">
                                    {{ text_area("content", "rows": 15, "placeholder": "Leave the content", "class": "form-control") }}
                                </div>
                            </div>

                            <div id="preview-box" style="display:none"></div>
                        </div>

                        <div class="pull-left">
                            {{ link_to('', 'Back to discussions') }}
                        </div>
                        <div class="pull-right">
                            <button type="submit" class="btn btn-sm btn-success">Submit Discussion</button>
                        </div>

                    </form>

                </div>

                <div class="col-md-3">
                    <div id="recommended-posts-create">
                        <strong>Suggested Posts</strong>
                        <div id="recommended-posts-create-content">There are no suggested posts</div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

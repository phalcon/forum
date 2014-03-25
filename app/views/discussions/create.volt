{{ content() }}

<div class="container start-discussion">

    <h1>Start a Discussion</h1>

    <div class="row">
        <div class="col-md-1 remove-image hidden-xs" align="right">
            <img src="https://secure.gravatar.com/avatar/{{ session.get('identity-gravatar') }}?s=48&amp;r=pg&amp;d=identicon"
                 class="img-rounded">
        </div>
        <div class="col-md-11">

            <div class="bs-callout bs-callout-info">
                <h4>Creating a new Post</h4>

                <p>Help us make this a great place for discussion and collaboration. Please spend some time browsing the
                    topics here
                    before replying or starting your own, and you’ll have a better chance of meeting others who share
                    your interests or have had similar problems. If you want to report any bug related to Phalcon or
                    suggest a new feature, please post it on <a href="https://github.com/phalcon/cphalcon/issues">Github</a> issues.</p>
            </div>

            <form method="post" autocomplete="off" role="form">

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
                        <li class="pull-right">{{ link_to('help/markdown', 'Help', 'parent': '_new') }}</li>
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
    </div>
</div>


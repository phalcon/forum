{{ content() }}

<div class="start-discussion container">

    <ol class="breadcrumb">
        <li>{{ link_to('', 'Home') }}</a></li>
        <li>{{ link_to('category/' ~ post.category.id ~ '/' ~ post.category.slug, post.category.name) }}</a></li>
    </ol>

    <h1>Edit Discussion: {{ post.title|e }}</h1>

    <div class="row">
        <div class="col-md-1 remove-image" align="right">
            <img src="https://secure.gravatar.com/avatar/{{ session.get('identity-gravatar') }}?s=48&amp;r=pg&amp;d=identicon"
                 class="img-rounded">
        </div>
        <div class="col-md-10">
            <form method="post" autocomplete="off" role="form">
                {{ hidden_field(security.getTokenKey(), "value": security.getToken()) }}

                <div class="form-group">
                    {{ hidden_field("id") }}
                </div>

                <div class="form-group">
                    {{ text_field("title", "placeholder": "Title", "class": "form-control") }}
                </div>

                <div class="form-group">
                    {{ select("categoryId", categories, 'using': ['id', 'name'], "class": "form-control") }}
                </div>

                <ul class="nav nav-tabs preview-nav">
                    <li class="active"><a href="#" onclick="return false">Write</a></li>
                    <li><a href="#" onclick="return false">Preview</a></li>
                    <li class="pull-right">{{ link_to('help/markdown', 'Help', 'parent': '_new') }}</li>
                </ul>

                <div id="comment-box">
                    {{ text_area("content", "rows": 15, "placeholder": "Leave the content", "class": "form-control") }}
                </div>
                <div id="preview-box" style="display:none"></div>

                <div class="pull-left">
                    {{ link_to('discussion/' ~ post.id ~ '/' ~ post.slug , 'Cancel') }}
                </div>
                <div class="pull-right">
                    <button type="submit" class="btn btn-success">Save</button>
                </div>

            </form>
        </div>
    </div>
</div>

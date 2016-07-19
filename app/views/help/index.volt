<div class="help-container">
    <div class="row">
        {% include 'partials/breadcrumbs.volt' %}

        <div class="col-md-12 help-head">
            <h1>Find out more about…</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 help-category-box">
            <h3>Asking</h3>
            <ul class="nav">
                <li>{{ link_to('help/create-post', 'How do I ask a good question?', 'title': 'What topics can I ask about here?') }}</li>
            </ul>
        </div>
        <div class="col-md-4 help-category-box">
            <h3>Badges</h3>
            <ul class="nav">
                <li>{{ link_to('help/badges', 'What are badges?', 'title': 'View a full list of badges you can earn') }}</li>
            </ul>
        </div>
        <div class="col-md-4 help-category-box">
            <h3>Reputation & Moderation</h3>
            <ul class="nav">
                <li>{{ link_to('help/karma', 'How Karma & Reputation works?') }}</li>
                <li>{{ link_to('help/moderators', 'Posts & Comments Moderation') }}</li>
            </ul>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 help-category-box">
            <h3>About</h3>
            <ul class="nav">
                <li>{{ link_to('help/stats', 'Forum Statistics') }}</li>
                <li>{{ link_to('help/about', 'About ' ~ config.site.software) }}</li>
            </ul>
        </div>
        <div class="col-md-4 help-category-box">
            <h3>Answering</h3>
            <ul class="nav">
                <li>{{ link_to('help/markdown', 'How to use Markdown?') }}</li>
                <li>{{ link_to('help/voting', 'How does the feedback system work?') }}</li>
            </ul>
        </div>
    </div>
</div>

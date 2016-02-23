<div class="help-container">
    <div class="row">
        {% include 'partials/breadcrumbs.volt' %}
        <div class="col-md-8 col-md-offset-2 help-head">
            <h1>About Phosphorum</h1>
            <section>
                <p>
                    Phosphorum is an engine for building flexible, clear and fast forums.
                    It is used by:
                </p>
                <ul>
                    <li>{{ link_to('https://forum.phalconphp.com/', 'Phalcon Framework Forum', false) }}</li>
                    <li>{{ link_to('https://forum.zephir-lang.com/', 'Zephir Language Forum', false) }}</li>
                </ul>
                <p>
                    You can adapt it to your own needs or improve it if you want.
                    If you want to improve this forum please submit a
                    {{ link_to('https://help.github.com/articles/creating-a-pull-request', 'pull request', false) }}
                    {{ link_to('https://github.com/phalcon/forum', 'repository', false) }}.
                </p>
                <p>Please write us if you have any feedback.</p>
            </section>
        </div>
    </div>
</div>

{{ content() }}

<div class="row">
    <div class="col-md-12">
        <div class="error-v4">
            {{- partial('partials/error/header', ['code': code, 'message': message]) -}}

            {{- partial('partials/error/debug', ['error': error]) -}}

            <div class="col-md-12 text-center">
                <p class="lead">
                    Sorry for the inconvenience but we&rsquo;re performing some maintenance at the moment.
                    If you need to you can always contact us at
                    <a href="{{ 'mailto:' ~ support }}">{{ support }}</a>,
                    otherwise we&rsquo;ll be back online shortly!
                </p>

                <p class="text-right"><em>&mdash; The {{ config.site.project }} Team</em></p>
            </div>
        </div>
    </div>
</div>

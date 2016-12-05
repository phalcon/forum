{{ content() }}

<div class="row">
    <div class="col-md-12">
        <div class="error-v4">
            {{- partial('partials/error/header', ['code': code, 'message': message]) -}}

            {{- partial('partials/error/debug', ['error': error]) -}}

            <div class="col-md-12 text-center">
                <p class="lead">
                    Sorry for the inconvenience but something is not quite right at the moment.
                    We hope to solve it shortly. If you need to you can always contact us at
                    <a href="{{ 'mailto:' ~ support }}">{{ support }}</a>,
                    otherwise please check back in a few minutes!
                </p>

                <p class="text-right"><em>&mdash; The {{ config.site.project }} Team</em></p>
            </div>
        </div>
    </div>
</div>

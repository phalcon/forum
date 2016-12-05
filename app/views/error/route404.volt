{{ content() }}

<div class="row">
    <div class="col-md-12">
        <div class="error-v4">
            {{- partial('partials/error/header', ['code': code, 'message': message]) -}}

            {{- partial('partials/error/debug', ['error': error]) -}}

            <div class="col-md-12 text-center">
                <p class="lead">
                    Are you sure that this page should be here?
                    If so, please let us know:
                    <a href="{{ 'mailto:' ~ support }}">{{ support }}</a>
                </p>
                <p>
                    <a class="btn btn-primary" href="/" style="color: #fff;">Back to main page</a>
                </p>
            </div>
        </div>
    </div>
</div>

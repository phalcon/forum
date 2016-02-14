{{ content() }}

<div class="row">
    <div class="col-md-10 col-md-offset-1">
        <div class="error-v4">
            <h1>{{ code }}</h1>
            <span class="sorry">{{ message }}</span>
            {% if debug %}
                <div class="row text-left">
                    <p>
                        Error <br>in file <code>{{ error.file() }}</code>, at line <code>{{ error.line() }}</code>
                    </p>
                    {% if error.isException() %}
                        <pre>{{ error.exception().getTraceAsString() }}</pre>
                    {% endif %}
                </div>
            {% endif %}
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <p class="lead">
                        We hope to solve it shortly.
                        Please check back in a few minutes. If you continue seeing this error please contact us at
                        <a href="mailto:team@phalconphp.com">{{ config.mail.fromEmail }}</a>
                    </p>
                    <p class="text-center">
                        <a class="btn btn-primary" href="/" style="color: #fff;">Back to main page</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

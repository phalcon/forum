{{ content() }}

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="error-v4">
            <div class="col-md-12 text-center error-banner">
                <h1>{{ code }}</h1>
                <span class="sorry">{{ message }}</span>
            </div>
            {% if debug %}
                <div class="col-md-12 error-debug">
                    <p>
                        Error <br>in file <code>{{ error.file() }}</code>, at line <code>{{ error.line() }}</code>
                    </p>
                    {% if error.isException() %}
                        <pre>{{ error.exception().getTraceAsString() }}</pre>
                    {% endif %}
                </div>
            {% endif %}
            <div class="col-md-12 text-center">
                <p class="lead">
                    We hope to solve it shortly.
                    Please check back in a few minutes. If you continue seeing this error please contact us at
                    <a href="{{ 'mailto:' ~ config.mail.fromEmail }}">{{ config.mail.fromEmail }}</a>
                </p>
                <p>
                    <a class="btn btn-primary" href="/" style="color: #fff;">Back to main page</a>
                </p>
            </div>
        </div>
    </div>
</div>

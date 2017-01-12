{%- if debug === true -%}
    <div class="col-md-12 error-debug">
        <p>
            Error [{{ error.type() }}]: {{ error.message() }} <br>
            File: <code>{{ error.file() }}</code><br>
            Line: <code>{{ error.line() }}</code>
        </p>
        {%- if error.isException() -%}
            <pre>{{ error.exception().getTraceAsString() }}</pre>
        {%- endif -%}
    </div>
{%- endif -%}

<div class="row">
    <div class="col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">
        <h5 class="text-danger">Poll results:</h5>
        <hr>
        {%- cache "poll-votes-" ~ post.id -%}
            {%- for option in post.pollOptions -%}
                {%- if result[option.id] is defined -%}
                    {%- set amount = result[option.id] -%}
                {% else %}
                    {%- set amount = 0 -%}
                {%- endif -%}
                {{ option.title|e }} ({{ amount ~ '%' }})
                <div class="progress progress-striped active user-poll-section">
                    <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="{{ amount }}" aria-valuemin="0" aria-valuemax="100" style="{{ 'width:' ~ amount ~ '%' }}">
                        <span class="sr-only">{{ amount }}% voted</span>
                    </div>
                </div>
            {%- endfor -%}
        {%- endcache -%}
    </div>
</div>

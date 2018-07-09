{%- extends "templates/base.volt" -%}

{% block content %}
    <section class="content-sec">
        <div class="container">
            <div class="row">
                {% include 'include/sidebar' with ['categories': categories] %}

                {{ content() }}
            </div>
        </div>
    </section>
{% endblock %}

{%  block footer %}
    {%  include "include/footer" with [
        'application_short_description': config.application.shortDescription
    ] %}
{%  endblock %}

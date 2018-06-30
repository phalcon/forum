{%- extends "templates/base.volt" -%}

{% block content %}
    <section class="content-sec">
        <div class="container">
            <div class="row">
                {% include 'partials/sidebar.volt' %}

                {{ content() }}
            </div>
        </div>
    </section>
{% endblock %}

{{ content() }}

{% include 'partials/flash-banner.volt' %}

<div class="create-category">
    <h1>Create a Category</h1>

    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-9">
                    <div class="bs-callout bs-callout-info">
                        <h4>Creating a new category</h4>

                        <p> {{ config.site.project }} is an open source project and a volunteer effort.
                            Help us make this a great place for discussion and collaboration.
                            Please make sure that there are no categories with similar meaning.
                            So it would be convenient if we give a small description for the new category.
                            Please do not use HTML or any other markup to describe the category.
                            If you want to report any bug related to {{ config.site.project }} or
                            suggest a new feature, please post it on <a href="{{ config.site.repo }}">Github</a> issues.</p>
                    </div>

                    <form method="post" autocomplete="off" role="form">
                        {{ hidden_field(security.getPrefixedTokenKey('create-category'), "value": security.getPrefixedToken('create-category')) }}

                        <div class="form-group {% if errors is defined and not(errors['name'] is empty) %}has-error{% endif %}">
                            <label>Name</label>
                            {{ text_field("name",  "placeholder": "Category name", "class": "form-control", "required": "required") }}
                            {% if errors is defined and not(errors["name"] is empty) %}
                                <span class="help-block">{{ join('<br>', errors["name"]) }}</span>
                            {% endif %}
                        </div>

                        <div class="form-group {% if errors is defined and not(errors['description'] is empty) %}has-error{% endif %}">
                            <label>Description</label>
                            {{ text_area("description", "rows": 15, "placeholder": "Short category description", "class": "form-control no-editor") }}
                            {% if errors is defined and not(errors["description"] is empty) %}
                                <span class="help-block">{{ join('<br>', errors["description"]) }}</span>
                            {% endif %}
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="no_bounty" id="no_bounty" value="Y"> Disable bounties
                            </label>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="no_digest" id="no_digest" value="Y"> Disable weekly digest
                            </label>
                        </div>

                        <div class="pull-left">
                            {{ link_to('', 'Back to discussions') }}
                        </div>

                        <div class="pull-right">
                            <button type="submit" class="btn btn-sm btn-success">Save Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

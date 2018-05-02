<div class="form-group extra-parameters-group">
    <a class="show-extra-param" onclick="showExtraParam()">Show extra parameters</a>
    <a class="hide-extra-param" onclick="hideExtraParam()" style="display:none">Hide extra parameters</a>

    <div class="extra-params-fields" style="display:none">
        <fieldset>
            <legend class="extra-param-legend">
                Extra parameters
            </legend>
            <div class="form-group">
                {%- for name,data in extraParams -%}
                    <label class="extra-param-label">{{ data['description'] }}</label>

                    {%- if data['field'] is 'input' -%}
                        <input class="form-control" type="{{data['type']}}" value="{{data['value']}}" name="extra[{{name}}]">
                    {%- endif -%}
                {%- endfor -%}
            </div>
        </fieldset>
    </div>
</div>

<div class="col-md-8 col-md-offset-2 text-center">
    {%- if pager.haveToPaginate() -%}
        {{- pager.getLayout() -}}
    {%- endif -%}
</div>

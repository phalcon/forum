<div class="pagination-bar">
    {%- if pager.haveToPaginate() -%}
        {{- pager.getLayout() -}}
    {%- endif -%}
</div>

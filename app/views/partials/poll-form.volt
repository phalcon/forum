{%- set hasPoll = post is defined and post.hasPoll() -%}
{%- set isStartVoting = post is defined and post.isStartVoting() -%}

{%- if hasPoll -%}
    {%- set collapseClass = "in", buttonId = "remove", buttonLabel = "Remove poll", buttonClass = "danger" -%}
{%- else -%}
    {%- set collapseClass = "", buttonId = "create", buttonLabel = "Attach a poll", buttonClass = "primary" -%}
{%- endif -%}
<div class="row" style="margin-top: 1em; margin-bottom: 1em">
    <div class="col-md-12 text-right">
        <a class="btn" role="button" data-toggle="collapse" href="#collapseOptions" aria-expanded="false" aria-controls="collapseOptions">
            More options...
        </a>
        <div class="collapse {{- collapseClass -}}" id="collapseOptions">
            <div class="post-option-header text-left">
                {%- if hasPoll and isStartVoting -%}
                    <div class="alert alert-warning" role="alert">
                        <button class="btn btn-sm btn-danger disabled" type="button" id="remove-poll">
                            Remove poll
                        </button>
                        &nbsp;The poll is in read-only mode. The voting for the poll was started. To create a new poll, you must create a new post.
                    </div>
                {%- else -%}
                    <button class="btn btn-sm btn-{{- buttonClass -}}" type="button" id="{{- buttonId -}}-poll">
                        {{- buttonLabel -}}
                    </button>
                {%- endif -%}
            </div>
            <figure id="options-box" {%- if hasPoll -%}class="post-option-body"{%- endif -%}>
                {%- if hasPoll -%}
                    {%- for i, option in post.pollOptions -%}
                        <div class="form-group" style="min-height: 34px;">
                            <label class="col-sm-2 control-label" for="option{{- option.id -}}" {%- if isStartVoting -%}style="color: #777"{%- endif -%}>Name</label>
                            <div class="col-sm-{%- if i < 2 -%}10{%- else -%}9{%- endif -%}">
                                <input type="text" id="option{{- option.id -}}" name="pollOptions[]" placeholder="Enter an option here" maxlength="64" required="required" class="form-control" value="{{- option.title -}}" {%- if isStartVoting -%}readonly="readonly"{%- endif -%}>
                            </div>
                            {%- if i > 2 -%}
                                <div class="col-sm-1">
                                    {{ link_to('#', '<span class="glyphicon glyphicon-remove-circle"></span>',  'class': 'del-poll-option', 'title': 'Delete option', 'onclick': 'return false') }}
                                </div>
                            {%- endif -%}
                        </div>
                    {%- endfor -%}
                    {%- if not(isStartVoting) and optionsCount < 10 -%}
                        <div class="form-group" style="min-height: 34px;">
                            {{ link_to('#', '+ Add option',  'class': 'btn btn-sm btn-primary add-poll-option', 'onclick': 'return false') }}
                        </div>
                    {%- endif -%}
                {%- endif -%}
            </figure>
        </div>
    </div>
</div>

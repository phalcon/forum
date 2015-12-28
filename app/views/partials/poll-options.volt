<div class="row">
    {%- cache "poll-options-" ~ post.id -%}
        {%- for i, option in post.pollOptions -%}
            <div class="radio">
                <label>
                    <input type="radio" name="pollOption" class="pollOption" data-id="{{ option.id }}" value="{{ option.id }}">
                    <strong class="poll-idx">{{ chr(i + 65) ~ '.' }}</strong>&nbsp;
                    {{- option.title -}}
                </label>
            </div>
        {%- endfor -%}
        <div class="col-md-12">
            <div class="col-md-6">
                <div align="right" class="posts-buttons">
                    <a data-id="{{ post.id }}" class="btn btn-success btn-sm vote-poll disabled" onclick="return false" href="#">
                        <span class="glyphicon glyphicon-ok"></span>&nbsp;Vote
                    </a>
                </div>
            </div>
        </div>
    {%- endcache -%}

</div>

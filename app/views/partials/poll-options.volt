<div class="row">
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
                {%- if currentUser -%}
                <a data-id="{{ post.id }}" class="btn btn-success btn-sm vote-poll disabled" onclick="return false" href="#">
                    <span class="glyphicon glyphicon-ok"></span>&nbsp;Vote
                </a>
                {%- else -%}
                    <p class="text-center">You must log in first to vote</p>
                {%- endif -%}
            </div>
        </div>
    </div>
</div>

{%- set bounty = post.getBounty() -%}

<div class="bs-callout bs-callout-info">
    <h4>Bounty available!</h4>
    {%- if bounty['type'] == "old" -%}
        <p>
            It has been a while and this question still does not have any answers.
            Answer this question and get additional <span class="label label-info">+{{ bounty['value'] }}</span>
            points of karma/reputation if the original poster accepts your reply as correct answer
        </p>
    {%- elseif bounty['type'] == "fast-reply" -%}
        <p>
            This post has recently posted.
            Answer this question and get additional <span class="label label-info">+{{ bounty['value'] }}</span>
            points of karma/reputation if the original poster accepts your reply as correct answer
        </p>
    {%- endif -%}
</div>

{%- if post.users_id == currentUser or moderator == 'Y' -%}
    {{ link_to('edit/discussion/' ~ post.id, '<span class="glyphicon glyphicon-pencil"></span>&nbsp;Edit', 'class': 'btn btn-default btn-xs btn-edit-post') }}
    {{ link_to('delete/discussion/' ~ post.id ~ '?' ~ tokenKey ~ '=' ~ token, '<span class="glyphicon glyphicon-remove"></span>&nbsp;Delete', 'class': 'btn btn-default btn-xs btn-delete-post') }}&nbsp;
{%- endif %}

{%- if moderator == 'Y' -%}
    {%- if post.sticked == 'N' -%}
        {{ link_to('stick/discussion/' ~ post.id ~ '?' ~ tokenKey ~ '=' ~ token, '<span class="glyphicon glyphicon-pushpin"></span>&nbsp;Stick', 'class': 'btn btn-default btn-xs btn-edit-post') }}
    {%- else -%}
        {{ link_to('unstick/discussion/' ~ post.id ~ '?' ~ tokenKey ~ '=' ~ token, '<span class="glyphicon glyphicon-pushpin"></span>&nbsp;Unstick', 'class': 'btn btn-default btn-xs btn-edit-post') }}
    {%- endif %}
{%- endif %}

{%- if currentUser -%}
  {% if post.isSubscribed(currentUser) %}
      {{ link_to('unsubscribe/discussion/' ~ post.id ~ '?' ~ tokenKey ~ '=' ~ token, '<span class="glyphicon glyphicon glyphicon-eye-close"></span>&nbsp;Unsubscribe', "class": "btn btn-default btn-xs") }}
  {% else %}
      {{ link_to('subscribe/discussion/' ~ post.id ~ '?' ~ tokenKey ~ '=' ~ token, '<span class="glyphicon glyphicon-eye-open"></span>&nbsp;Subscribe', "class": "btn btn-default btn-xs") }}
  {% endif %}
  <a href="#" onclick="return false" class="btn btn-danger btn-xs vote-post-down" data-id="{{ post.id }}">
      <span class="glyphicon glyphicon-thumbs-down"></span>
      {{- post.votes_down -}}
  </a>
  <a href="#" onclick="return false" class="btn btn-success btn-xs vote-post-up" data-id="{{ post.id }}">
      <span class="glyphicon glyphicon-thumbs-up"></span>
      {{ post.votes_up }}
  </a>
{%- else -%}
    <a href="#" onclick="return false" class="btn btn-danger btn-xs">
        <span class="glyphicon glyphicon-thumbs-down"></span>
        {%- if post.votes_down -%}
            <span itemprop="downvoteCount">{{ post.votes_down }}</span>
        {%- else -%}
            {{ post.votes_down }}
        {%- endif -%}
    </a>
    <a href="#" onclick="return false" class="btn btn-success btn-xs">
        <span class="glyphicon glyphicon-thumbs-up"></span>
        {%- if post.votes_up -%}
            <span itemprop="upvoteCount">{{ post.votes_up }}</span>
        {%- else -%}
            {{- post.votes_up -}}
        {%- endif -%}
    </a>
{%- endif -%}

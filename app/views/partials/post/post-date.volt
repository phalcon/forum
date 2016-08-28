<div class="posts-date hidden-xs" align="right">
    {% if is_edited > 0 %}
        <span class="action-date action-edit" data-id="{{ post.id }}" data-toggle="modal" data-target="#historyModal">
            edited <span>{{ post.getHumanEditedAt() }}</span>
        </span><br/>
    {% endif %}
    <a name="C{{ post.id }}" href="#C{{ post.id }}">
        <time class="action-date">{{ post.getHumanCreatedAt() }}</time>
    </a>
</div>

<div class="modal fade" id="replyModal" tabindex="-1" role="dialog" aria-labelledby="replyModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <form method="post" autocomplete="off" role="form">
            {{ hidden_field(tokenKey, "value": token) }}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="replyModalLabel">Add Reply</h4>
                </div>

                <div class="modal-body" id="errorBody">
                    <ul class="nav nav-tabs preview-nav">
                        <li class="active"><a href="#" onclick="return false">Comment</a></li>
                        <li><a href="#" onclick="return false">Preview</a></li>
                        <li class="pull-right">{{ link_to('help/markdown', 'Help', 'parent': '_new') }}</li>
                    </ul>
                    <div>
                        <div id="reply-comment-box">
                            {{- hidden_field('id', 'value': post.id) -}}
                            {{- hidden_field('reply-id') -}}
                            <div id="comment-textarea"></div>
                        </div>
                        <div id="preview-box" style="display:none"></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <input type="submit" class="btn btn-success" value="Add Reply"/>
                </div>
            </div>
        </form>
    </div>
</div>

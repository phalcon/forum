{{ content() }}

<div class="row profile">
    <div class="col-md-3">
        <div class="profile-sidebar" itemscope itemtype="http://schema.org/Person">
            <div class="profile-avatar">
                {{ image(avatar, 'class': 'img-responsive', 'itemprop': 'image') }}
            </div>
            <div class="profile-title">
                <div class="profile-title-name">
                    <h1>
                        <span class="user-name" itemprop="name">{{ user.name|e }}</span>
                        <span class="user-login" itemprop="additionalName">{{ user.login }}</span>
                    </h1>
                </div>
            </div>
            <div class="profile-buttons">
                <!-- todo -->
            </div>
            <div class="profile-info">
                <ul class="nav">
                    <li>
                        <span class="octicon octicon-clock"></span>&nbsp;<span>Joined {{ date('M d, y', user.created_at) }}</span>
                    </li>
                    <li>
                        <span class="octicon octicon-gist"></span>&nbsp;<span>Posts {{ numberPosts }}</span>
                    </li>
                    <li>
                        <span class="octicon octicon-comment-discussion"></span>&nbsp;<span>Replies {{ numberReplies }}</span>
                    </li>

                    <li>
                        <span class="octicon octicon-thumbsup"></span>&nbsp;<span>Votes available {{ user.votes }}</span>
                    </li>

                    <li>
                        <span class="octicon octicon-thumbsup"></span>&nbsp;<span>Voting Points <b>{{ user.votes_points }}/50</b></span>
                    </li>

                    <li>
                        <span class="octicon octicon-octoface"></span>&nbsp;<span><a href="https://github.com/{{ user.login }}">Github Profile</a></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="profile-content">
            <div class="row">

                <div class="col-md-12">
                    <form method="post" role="form">
                        {{ hidden_field(security.getPrefixedTokenKey('settings'), "value": security.getPrefixedToken('settings')) }}
                        <fieldset>
                            <legend>
                                Notification Settings
                            </legend>
                            <div class="form-group">
                                <label for="notifications">E-Mail Notifications</label>
                                {{ select_static('notifications', [
                                'N': 'Never receive an e-mail notification',
                                'Y': 'Receive e-mail notifications from all new threads and comments',
                                'P': 'When someone replies to a discussion that I started or replied to'
                                ], 'class': 'form-control') }}
                            </div>
                            <div class="form-group">
                                <label>Weekly Digest</label>
                                <div class="radio">
                                    <label class="radio-inline">
                                        <input type="radio" name="digest" id="digest_y" value="Y" {% if subscribed %}checked{% endif %}> Yes
                                    </label>
                                </div>
                                <div class="radio">
                                    <label class="radio-inline">
                                        <input type="radio" name="digest" id="digest_n" value="N" {% if not subscribed %}checked{% endif %}> No
                                    </label>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>
                                Timezone Settings
                            </legend>
                            <div class="form-group">
                                <label for="timezone">Timezone</label>
                                {{ select_static('timezone', timezones, 'class': 'form-control') }}
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>
                                Appearance
                            </legend>
                            <div class="form-group">
                                <div class="form-group">
                                    <label for="digest">Code Highlight Theme</label>
                                    {{ select_static('theme', [
                                    'D': 'Dark',
                                    'L': 'Light'
                                    ], 'class': 'form-control') }}
                                </div>
                            </div>
                        </fieldset>
                        <div class="form-group">
                            <a href="https://en.gravatar.com/">Change your avatar at Gravatar</a>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-success" value="Save">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

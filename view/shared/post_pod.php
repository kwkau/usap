

<div class="usap-pod" data-mgcid="{{=post.magic_id}}" data-target="{{=post.target}}" data-id="{{=post.user_prof.user_id}}">
    <div class="usap-pod-header">
        <div class="user-pic"><img src="{{=post.user_prof.profile_pic_thumb}}"/></div>

        <div class="username"><span><?=$this->htmlAnchor("profile","{{=post.user_prof.full_name}}","{{=post.user_prof.user_id}}")?></span> <span class="glyph">{{?post.target == 'general'}}&#xf0ac;{{??post.target == 'friend'}}&#xf007;{{?}}</span></div>
        <div class="flag">
            <div class="flag-ico">
                <span class="glyph">&#xf078;</span>
            </div>
            <div class="post-menu">
                <ul>
                    <li class="p-flag">Flag</li>
                    <li class="p-bkmrk">Bookmark</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="usap-pod-main-cont">
        <div class="text-content">
            {{=post.post_text}}
        </div>
        {{if(post.pic_url){ }}
        <div class="pic-content">
            <img src="{{=post.pic_url}}"/>
        </div>
        {{ } }}
    </div>

    <div class="ussap-pod-footer">
        <div class="comments-cont">

            <div class="comments-cont-header">
                <!--post smileys-->
                <div class="cch-box comment-tag">
                    <!--check if the user has liked the post already-->
                    {{?post.smileys.length==0}}{{var smiley=null,unsmiley=null,klass=null;}}{{?}}
                    {{~post.smileys : smly}}
                    {{?parseInt(smly.user_prof.user_id) == parseInt(Global.get_item("id"))}}
                    {{ var smiley =false,unsmiley=true, klass="liked";}}
                    {{?}}
                    {{~}}
                    <div class="smiley pull-left {{=klass||''}}" data-smiley="{{=smiley||''}}" data-unsmiley="{{=unsmiley||''}}"><span class="glyph">&#xf118; </span> <span class="smiley-num">{{=post.smileys.length == 0?"":post.smileys.length}}</span></div>

                </div>
                <div class="cch-box comment-num">
                    <!--comment number-->
                    <span class="glyph">&#xf086;</span> <span> view <span class="cch-tab-text" data-sw-increm="comment_num"> {{=post.comments==0?"":post.comments}}</span></span><!--<span data-sw-show="cmnt_num:false"> <span class="glyph fa-spin"> &#xf185;</span></span>-->
                </div>
                <div class="cch-box pull-right comment-time">
                    <!--forum time-->
                    <span class="cch-tab-text"> {{=post.created_at}}</span> <span class="glyph"> &#xf017; </span>
                </div>
                {{smiley=null;unsmiley=null;klass=null;}}
            </div>

            <!-- comment pod will be the container for all comments on all posts /-->
            <div class="comment-pod-wrapper" data-sw-editable="post_comments comment">
                {{? it[0].post_text == undefined }}
                <div class="comment-pod" data-comment="{{=comment.magic_id}}" data-uid="{{=comment.user_prof.user_id}}">
                    <div class="cp-header">
                        <div class="user-pro">
                            <img src="{{=comment.user_prof.profile_pic_thumb}}"/>
                            <?=$this->htmlAnchor("profile","{{=comment.user_prof.full_name}}","{{=comment.user_prof.user_id}}")?>
                        </div>
                    </div>
                    <div class="cp-body">
                        <p>
                            {{=comment.text}}
                        </p>
                    </div>
                    <div class="cp-footer">
                        <!--check if the user has liked the comment already-->
                        {{?comment.smileys.length == 0}}{{smiley=null;unsmiley=null;klass=null;}}{{?}}
                        {{~comment.smileys : sm}}
                        {{?parseInt(sm.user_prof.user_id) == parseInt(Global.get_item("id"))}}
                        {{ var smiley =false,unsmiley=true, klass="liked";}}
                        {{?}}
                        {{~}}
                        <div class="smiley pull-left {{=klass||''}}" data-smiley="{{=smiley||''}}" data-unsmiley="{{=unsmiley||''}}"><span class="glyph">&#xf118; </span> <span class="smiley-num">{{=comment.smileys.length==0?"":comment.smileys.length}}</span></div>
                        <div class="time pull-right"> <span>{{=comment.created_at}}</span> <span class="glyph">&#xf017; </span></div>
                    </div>
                </div>
                <!--reset variables for the next comment-->
                {{smiley=null;unsmiley=null;klass=null;}}
                {{?}}
            </div>
            <div class="comment-field">
                <textarea class="comment-area" data-instance-id="{{=post.instance_id}}" data-model-name="{{=post.parent_name}}" name="post_comments" placeholder="Comment (press enter to send)....."></textarea>
            </div>
        </div>

    </div>
</div>

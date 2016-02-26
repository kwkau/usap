<!--
                    The time.js file contains a class called TimeSpan??. The TimeSpan?? object can be used to determine the difference between two dates (days, hours, minutes, seconds, milliseconds).

                    Example

                    var future = new Date().add({months: 5, days: 4, hours: 3, minutes: 2, seconds: 1}); var now = new Date();

                    var span = new TimeSpan??(future - now);

                    console.log("Days:", span.getDays()); console.log("Hours:", span.getHours()); console.log("Minutes:", span.getMinutes()); console.log("Seconds:", span.getSeconds());

                    Hope this helps.
                -->

<div class="usap-pod" data-mgcid="{{=forum.magic_id}}" data-type="{{=forum.type}}" data-id="{{=forum.user_prof.user_id}}">
    <div class="usap-pod-header">
        <div class="user-pic"><img src="{{=forum.user_prof.profile_pic_thumb}}" /></div>
        <div class="username"><span><?=$this->htmlAnchor("profile","{{=forum.user_prof.full_name}}","{{=forum.user_prof.user_id}}")?></span> <span class="glyph">{{?forum.type == 'general'}}&#xf0ac;{{??forum.type == 'friend'}}&#xf007;{{?}}</span></div>
        <div class="flag">
            <div class="flag-ico">
                <span class="glyph">&#xf078;</span>
            </div>
            <div class="post-menu">
                <ul>
                    <li class="f-flag">Flag</li>
                    <li class="f-bkmrk">Bookmark</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="usap-pod-main-cont">
        <div class="text-content">
            {{=forum.topic}}
        </div>
    </div>

    <div class="ussap-pod-footer">
        <div class="comments-cont">

            <div class="comments-cont-header">
                <div class="cch-box comment-num">
                    <!--comment number-->
                    <span class="glyph">&#xf086;</span> <span> view <span class="cch-tab-text" data-sw-increm="comment_num"> {{=forum.comments==0?"":forum.comments}}</span></span><!--<span data-sw-show="cmnt_num:false"> <span class="glyph fa-spin"> &#xf185;</span></span>-->
                </div>
                <div class="cch-box comment-tag">
                    <!--tag name-->
                    <span class="glyph fa-flip-horizontal"> &#xf02b;  </span> <span class="cch-tab-text"> {{=forum.tag}}</span>
                </div>
                <div class="cch-box pull-right comment-time">
                    <!--forum time-->
                    <span class="cch-tab-text"> {{=forum.created_at}}</span> <span class="glyph"> &#xf017; </span>
                </div>
            </div>

            <!-- comment pod will be the container for all comments on all posts /-->
            <div class="comment-pod-wrapper" data-sw-editable="forum_comments comment">
                {{? it[0].topic == undefined }}
                <!--check if the user has liked the comment already-->
                {{?comment.smileys.length==0}}{{var smiley=null,unsmiley=null,klass=null;}}{{?}}
                {{~comment.smileys : sm}}
                {{?parseInt(sm.user_prof.user_id) == parseInt(Global.get_item("id"))}}
                {{ var smiley =false,unsmiley=true, klass="liked";}}
                {{?}}
                {{~}}
                <div class="comment-pod" data-comment="{{=comment.magic_id}}" data-smiley="{{=smiley||''}}" data-unsmiley="{{=unsmiley||''}}" data-uid="{{=comment.user_prof.user_id}}">
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
                        <div class="smiley pull-left {{=klass||''}}"><span class="glyph">&#xf118; </span> <span class="smiley-num">{{=comment.smileys.length==0?"":comment.smileys.length}}</span></div>
                        <div class="time pull-right"> <span>{{=comment.created_at}}</span> <span class="glyph">&#xf017; </span></div>
                    </div>
                </div>
                <!--reset variables for the next comment-->
                {{smiley=null;unsmiley=null;klass=null;}}
                {{?}}
            </div>
            <div class="comment-field">
                <textarea class="comment-area" data-instance-id="{{=forum.instance_id}}" data-model-name="{{=forum.parent_name}}" name="forum_comments" placeholder="Comment (press enter to send)....."></textarea>
            </div>
        </div>

    </div>
</div>
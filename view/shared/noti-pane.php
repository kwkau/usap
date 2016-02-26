<div class="tab-pane" id="Notifications">
    <div class="col-md-3 noti-menu">
        <ul>
            <li class="bar"><a href="#user_noti" data-toggle="tab" aria-expanded="true"> Your
                    Notifications</a></li>
            <li><a href="#department_noti" data-toggle="tab"> Department Notifications</a></li>
            <li><a href="#group_noti" data-toggle="tab"> Group Notifications</a></li>
        </ul>
    </div>

    <div id="noti-wrapper" class="col-md-9">
        <div class="col-md-1"></div>
        <div class="tab-content col-md-10">

            <!--user notifications-->
            <div id="user_noti" class="tab-pane active" data-sw-model="UserNoti_mdl noti">
                <div class="noti-pod" data-tom="{{=noti.target_object_magic_id}}"
                     data-top="{{=noti.target_object_type}}" data-target="{{=noti.target_id}}" data-mgcid="{{=noti.magic_id}}" data-type="{{=noti.type}}">
                    <div class="noti-pod-header">
                        <div class="noti-user-pic">
                            <img src="{{=noti.perp_prof.profile_pic_thumb}}"/>

                            <div class="noti-username">
                                <span><a href="javascript:">{{=noti.perp_prof.full_name}}</a></span>
                                <span class="glyph">&#xf111;</span>
                            </div>
                            <span class="glyph noti-close close-item pull-right">&#xf00d;</span>
                        </div>

                    </div>
                    <div class="noti-pod-body">
                        {{=noti.noti_text}}
                    </div>
                    <div class="noti-object-wrapper">
                        <div class="col-md-1"></div>
                        <div class="col-md-10 noti-object" data-sw-container="Post_mdl:Forum_mdl">

                        </div>
                        <div class="col-md-1"></div>
                    </div>

                    <div class="noti-pod-footer">
                        {{?noti.target_object_type != "friend-request"}}
                        <div class="show-container"><span class="glyph">&#xf078;</span></div>
                        {{??}}
                        <div class="friend-req" data-perp="{{=noti.perp_prof.user_id}}">
                            <div class="col-md-6"><button id="friend-req-accept" type="button" class="btn btn-success">Accept</button></div>
                            <div class="col-md-6"><button id="friend-req-reject" type="button" class="btn btn-danger">Reject</button></div>
                        </div>
                        {{?}}
                    </div>
                </div>
            </div>

            <!--department notifications-->
            <div class="tab-pane" id="department_noti" data-sw-model="DepNoti_mdl noti">
                <h3>department_noti</h3>
            </div>

            <!--group notifications-->
            <div class="tab-pane" id="group_noti" data-sw-model="GroupNoti_mdl noti">
                <h3>group_noti</h3>
            </div>


        </div>
        <div class="col-md-1"></div>
    </div>
</div>
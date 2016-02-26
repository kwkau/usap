<div class="tab-pane col-md-12" id="Search" style="padding:2em 0em;">

    <!--user results-->
    <div id="user-results" class="col-md-6 mem-list">
        <div class="list-group">
            <div class="list-group-item active">
                Users
            </div>
            <div data-sw-model="SearchUser_mdl prof">
                <div class="list-group-item row">
                    <div class="mem-pic col-md-2">
                        <img src="{{=prof.profile_pic_thumb}}" alt="" class="img-circle">
                    </div>
                    <div class="mem-name col-md-10">
                        <div class="col-md-10">
                            <?=$this->htmlAnchor('profile',"{{=prof.full_name}}","{{=prof.user_id}}")?>
                        </div>
                    </div>
                </div>


            </div>

        </div>
    </div>



    <!--group results-->
    <div id="group-results" class="col-md-6">
        <div class="list-group">
            <div class="list-group-item active">
                Groups
            </div>
            <div data-sw-model="SearchGrp_mdl grp">
                <div class="list-group-item row">
                    <div class="mem-pic col-md-2">
                        <img src="http://localhost/usap/public/images/groups-def-icon.png " alt="" class="img-circle"/>
                    </div>
                    <div class="mem-name col-md-10">
                        <div class="col-md-10"><?=$this->htmlAnchor('group',"{{=grp.group_name}}","{{=grp.group_name}}")?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
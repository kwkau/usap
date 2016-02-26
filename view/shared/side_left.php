<aside class="col-lg-2 col-md-2 col-sm-2 sidebar">

    <?php
        if($this->page != "group"){
       ?>
            <!--user details-->
            <div class="user-box" data-sw-model="User_mdl val">
                <div id="profile_pic" class="">
                    <img src="{{=val.profile_pic_thumb}}" class="img-circle img-thumbnail"/>
                </div>
                <div class="user-info">
                    <div id="username" class="user-tab" data-user="{{=val.full_name}}:{{=val.id}}">
                        <?=$this->htmlAnchor("profile","{{=val.full_name}}")?>
                    </div>
                    <div id="department" class="user-tab">
                        <?=$this->htmlAnchor("department","{{=val.department.name}}","{{=val.department.name}}")?>
                    </div>
                </div>

            </div>
    <?
        }elseif($this->page == "group"){
            ?>
            <!--group details-->
            <div class="user-box" data-sw-model="GroupSide_mdl val">
                <div id="profile_pic" class="">
                    <img src=" http://localhost/usap/public/images/groups-def-icon.png" class="img-circle img-thumbnail"/>
                </div>
                <div class="user-info">
                    <div id="username" class="user-tab" data-grp="{{=val.name}}:{{=val.id}}">
                        <a href="javascript:">{{=val.name}}</a>
                    </div>
                    <div id="department" class="user-tab">
                       Admin: <?= $this->htmlAnchor("profile/{{=val.admin.user_id}}","{{=val.admin.full_name}}")?>
                    </div>
                </div>

            </div>
    <?
        }
    ?>




    <!--sidebar buttons-->
    <div id="mission-control">
        <ul>
            <li data-tab="Notifications">
                <a href="javascript:">
                    <div class="lb-pod bar1">
                        <span class="glyphicons glyph">&#xf0eb;</span>
                        <span class="text">Notifications
                            <span id="noti-num" class="badge badgeright" data-sw-model="notiNum_mdl val">
                                {{ if(val.num != 0) { }}
                                    {{=val.num}}
                                {{ } else { }}
                                    {{='none'}}
                                {{ } }}
                            </span>
                        </span>
                    </div>
                </a>
            </li>
            <li data-tab="Friends">
                <a href="javascript:">
                    <div class="lb-pod bar2">
                        <span class="glyphicons glyph">&#xf1ae;</span>
                        <span class="text">Friends
                            <span id="frnd-num" class="badge badgeright" data-sw-model="frndNum_mdl val">
                                {{ if (val.num != 0) { }}
                                    {{=val.num}}
                                {{ } else { }}
                                    {{='none'}}
                                {{ } }}
                            </span>
                        </span>
                    </div>
                </a>
            </li>
            <li data-tab="Upload">
                <a href="javascript:">
                    <div class="lb-pod bar3"><span class="glyphicons glyph">&#xf093;</span><span class="text">Uploads</span></div></a>
            </li>
            <li data-tab="Groups">
                <a href="javascript:">
                    <div class="lb-pod bar4">
                        <span class="glyphicons glyph">&#xf0c0;</span>
                        <span class="text">Groups
                            <span class="badge badgeright" data-sw-model="grpNum_mdl val">
                                {{ if (val.num != 0) { }}
                                    {{=val.num}}
                                {{ } else { }}
                                    {{='none'}}
                                {{ } }}
                            </span>
                        </span>
                    </div>
                </a>

            </li>
            <li data-tab="Bookmarks">
                <a href="javascript:"><div class="lb-pod bar5"><span class="glyphicons glyph">&#xf02e;</span><span class="text">Bookmarks</span></div></a>
            </li>
        </ul>
    </div>



</aside>
<div class="grp-creation padtop20 row">
    <div class="col-md-3"></div>
    <!--Group creation form-->
    <div class="col-md-6">
        <form  class="grp-creation-form form-inline" action="<?=HOST_URL?>/group/creation" method="post">
            <div class="grp-name form-group">
                <div class="input-group col-lg-12 col-md-12">
                    <input type="text" name="grp_name" class="form-control" placeholder="Group Name"/>
                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-success btn-block">Create</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
    <div class="col-md-3"></div>

</div>

<div class="grp-list row">
    <div class="col-md-3"></div>

    <!--Group list-->
    <div class="col-md-6">
        <div class="list-group padtop">
            <div class="list-group-item active">
                Groups
            </div>
            <div class="grp-list-item" data-sw-model="Groups_mdl grp">
                <a href="<?=HOST_URL?>/group/{{=grp.name}}" class="list-group-item">
                    <div class="grp-pic pull-left">
                        <?=$this->htmlImg("groups-def-icon.png",array("img-circle"))?>
                    </div>
                    <div class="grp-name pull-right">
                        <p>{{=grp.name}}</p>
                    </div>

                </a>
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>

</div>
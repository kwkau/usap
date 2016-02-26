<?=$this->htmlLink('group.css','stylesheet')?>

    <div class="col-lg-8 col-md-8 col-sm-8 content padtop20">
        <div class="tabbable" id="tabs-183532">
            <?=$this->shared("tab-nav")?>
            <div class="tab-content">
                <div id="forum-pane" class="tab-pane active">
                    <!--spinner-->
                    <div class="row" data-sw-show="frm_spinner:true">
                        <div class="col-md-5"></div>
                        <div class="col-md-2"><span class="glyph spinner fa-spin">&#xf185;</span></div>
                        <div class="col-md-5"></div>
                    </div>

                    <div class="container-fluid">
                        <div class="col-md-1"></div>
                        <div class="col-md-10 padtop">
                            <div class="well panel panel-default">
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-4 text-center">
                                            <?=$this->htmlImg("groups-def-icon.png",array("img-circle","center-block","img-thumbnail","img-responsive"))?>
                                        </div>
                                        <!--/col-->
                                        <div class="col-xs-12 col-sm-8">
                                            <h2><?=$this->viewBag["group"]->name?></h2>
                                            <p><strong>Administrator: </strong><?=$this->viewBag["group"]->admin->full_name?></p>
                                            <p><strong>Bio: </strong>  <?=$this->viewBag["group"]->description?></p>
                                            <p><strong>Email: </strong> adminmail@gmail.com </p>
                                        </div>
                                        <!--/col-->
                                        <div class="clearfix"></div>
                                        <div class="col-xs-12 col-sm-6 text-center green">
                                            <h2><?=count($this->viewBag["group_members"])?></h2>
                                            <p><small>Member(s)</small></p>
                                        </div>
                                        <!--/col-->


                                        <div class="col-xs-12 col-sm-6 text-center blue">
                                            <h2><?=$this->viewBag["forum_count"] > 100? "100+" : $this->viewBag["forum_count"];?></h2>
                                            <p><small>Active forums</small></p>
                                        </div>
                                        <!--/col-->

                                        <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                            <div class="btn-group" role="group">
                                                <button type="button" class="join btn btn-success">Join+</button>
                                            </div>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-primary">Forums</button>
                                            </div>
                                        </div>

                                        <div class="col-md-12 padtop20">
                                            <div class="mem-list container-fluid">
                                                <div class="list-group">
                                                    <div class="list-group-item active">
                                                        Members
                                                    </div>
                                                    <?php
                                                        echo "
                                                            <div class=\"list-group-item\">
                                                                <div class=\"mem-pic\">
                                                                   <img src=\"{$this->viewBag["group"]->admin->profile_pic_thumb}\" alt=\"\" class=\"img-circle\">
                                                                </div>
                                                                <div class=\"mem-name\">
                                                                    <p class=\"center\">{$this->htmlAnchor('profile',$this->viewBag["group"]->admin->full_name,$this->viewBag["group"]->admin->user_id)} <span class=\"blue pull-right\">Admin</span></p>
                                                                </div>
                                                            </div>
                                                            ";
                                                        foreach ($this->viewBag["group_members"] as $mem) {
                                                            if($mem->mem_prof->user_id == $this->viewBag["group"]->admin->user_id)continue;
                                                            echo "
                                                                <div class=\"list-group-item\">
                                                                    <div class=\"mem-pic pull-left\">
                                                                       <img src=\"{$mem->mem_prof->profile_pic_thumb}\" alt=\"\" class=\"img-circle\">
                                                                    </div>
                                                                    <div class=\"mem-name pull-right\">
                                                                        <p class=\"center\">{$this->htmlAnchor('profile',$mem->mem_prof->full_name,$mem->mem_prof->user_id)}</p>
                                                                    </div>
                                                                </div>
                                                            ";
                                                        }
                                                    ?>
                                                </div>

                                            </div>
                                        </div>
                                        <!--/row-->
                                    </div>
                                    <!--/panel-body-->
                                </div>
                                <!--/panel-->
                            </div>
                            <!--/col-->


                        </div>
                        <div class="col-md-1"></div>
                    </div>

                </div>


                <div class="tab-pane" id="Friends">
                    <!--Friend pane content-->
                    <?=$this->shared("friend-pane")?>
                </div>

                <div class="tab-pane" id="Bookmarks">
                    <h2>BookMarks will be ready soon</h2>
                </div>

                <div class="tab-pane" id="Groups">
                    <!--Group pane content-->
                    <?=$this->shared("group-pane")?>
                </div>

                <!--Notifications pane-->
                <?=$this->shared("noti-pane")?>
            </div>


        </div>
    </div>

<?= $this->htmlScript("controllers/usap.group.controller.js") ?>

<?= $this->htmlScript("models/forum.model.js") ?>
<?= $this->htmlScript("models/post.model.js") ?>
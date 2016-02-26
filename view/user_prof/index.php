<?=$this->htmlLink('profile.css','stylesheet')?>
<div class="col-lg-8 col-md-8 col-sm-8 content" xmlns="http://www.w3.org/1999/html">
    <div class="tabbable" id="tabs-183532">
        <!--tab navigation-->
        <?=$this->shared("tab-nav")?>

        <!--tab content-->
        <div class="tab-content">
            <div id="profile-pane" class="tab-pane active">

                <!--profile-->
                <div class="col-lg-1 col-md-1 col-sm-1"></div>
                <div class="col-lg-10 col-md-10 col-sm-10">
                    <br/>
                    <div class="well panel">
                        <div class="panel-body">
                            <div class="row profile-details">
                                <div class="col-xs-12 col-sm-4 text-center">
                                        <div class="profile-pic">
                                            <div class="center-block">
                                                <img src="<?=$this->viewBag["user_prof"]->profile_pic?>" alt=" " class="img-circle img-thumbnail img-responsive"/>
                                            </div>
                                        </div>
                                </div>
                                <!--/col-->
                                <div class="col-xs-12 col-sm-8">
                                    <h2><?=$this->viewBag["user_prof"]->full_name?></h2>
                                    <p><strong>First Name: </strong> <span class="text-capitalize" data-prop="first_name"><?=$this->viewBag["user_prof"]->first_name?></span></p>
                                    <p><strong>Last Name: </strong> <span class="text-capitalize" data-prop="last_name"><?=$this->viewBag["user_prof"]->last_name?> </span></p>


                                    <p><strong>User type: </strong> <span class="text-capitalize" data-prop="category"><?=$this->viewBag["user_prof"]->type?> </span> </p>
                                    <p><strong>Faculty: </strong> <span><?=$this->viewBag["user_prof"]->department->school?> </span> </p>
                                    <p><strong>Department: </strong> <span><?=$this->viewBag["user_prof"]->department->name?></span> </p>
                                    <p><strong>Programme: </strong> <span data-prop="programme"><?=$this->viewBag["user_prof"]->programme?></span></p>

                                    <p><strong>Year Completing: </strong> <span><?=$this->viewBag["user_prof"]->year_complete?></p>
                                </div>


                                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                    <div class="btn-group" role="group">
                                        <div class="col-xs-12 text-center">
                                            <h3 class="green"><?=count($this->viewBag["friends"])?> Friend(s)</h3>
                                        </div>
                                        <button id="friend-request" type="button" class="btn btn-success" data-uid="<?=$this->viewBag["user_prof"]->user_id?>">Send Request</button>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <div class="col-xs-12 text-center">
                                            <h3 class="blue"><?=count($this->viewBag["groups"])?></h3>
                                        </div>
                                        <button type="button" class="btn btn-primary">Groups</button>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <div class="col-xs-12 text-center">
                                            <h3 class="red">43</h3>
                                        </div>
                                        <button type="button" class="btn btn-danger">Forums</button>
                                    </div>
                                </div>



                                <div class="row padtop35">
                                    <!--display the friend list of the user-->
                                    <div class="col-md-6">
                                        <div class="list-group">
                                            <div class="list-group-item active">Friends</div>
                                            <?
                                                foreach ($this->viewBag["friends"] as $friend){
                                                    echo "<div class='list-group-item'>{$this->htmlAnchor("profile",$friend->user_prof->full_name,$friend->user_prof->user_id)}</div>";
                                                }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="list-group">
                                            <div class="list-group-item active">
                                                Groups
                                            </div>
                                            <?
                                                foreach ($this->viewBag["groups"] as $grp) {
                                                    echo "<div class='list-group-item'>{$this->htmlAnchor("group",$grp->name,$grp->name)}</div>";
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--/row-->
                        </div>
                        <!--/panel-body-->
                    </div>
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1"></div>


            </div>

            <!--Notifications pane-->
            <?=$this->shared("noti-pane")?>

        </div>
    </div>
</div>
<?=$this->htmlScript("controllers/usap.profile.controller.js","text/javascript")?>
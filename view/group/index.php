<?=$this->htmlLink('group.css','stylesheet')?>

<div class="col-lg-8 col-md-8 col-sm-8 content">
    <div class="tabbable" id="tabs-183532">
        <?=$this->shared("tab-nav")?>
        <div class="tab-content">
            <div id="forum-pane" class="tab-pane active">
                <!--Forums Start-->
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="create-form">
                            <form action="" method="post" data-sw-sync="Forum_form_mdl">
                                <div class="form-group">
                                    <textarea name="topic" class="form-control" placeholder="Type your forum topic...."></textarea>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <input list="forum-tag" placeholder="&#xf02b; Tag your forum" value=""
                                               name="tag" class="form-control" type="text"/>
                                        <datalist id="forum-tag">
                                            <option value="Physics"></option>
                                            <option value="Computer Science"></option>
                                            <option value="Chemistry"></option>
                                            <option value="Science"></option>
                                            <option value="Arts"></option>
                                            <option value="General"></option>
                                            <option value="Political"></option>
                                            <option value="Biology"></option>
                                            <option value="Mathematics"></option>
                                            <option value="Programing"></option>
                                            <option value="Music"></option>
                                            <option value="Engineering"></option>
                                            <option value="I.T."></option>
                                            <option value="Spiritual"></option>
                                        </datalist>
                                    </div>
                                </div>
                                <button id="forum-submit" class="btn btn-primary" type="submit">Create</button>

                            </form>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>

                <!--spinner-->
                <div class="row" data-sw-show="frm_spinner:true">
                    <div class="col-md-5"></div>
                    <div class="col-md-2"><span class="glyph spinner fa-spin">&#xf185;</span></div>
                    <div class="col-md-5"></div>
                </div>


                <!--forums-->
                <div class="col-lg-2 col-md-2 col-sm-2"></div>
                <div class="col-lg-8 col-md-8 col-sm-8">
                    <div id="usap-pod-wrapper" data-sw-model="Forum_mdl forum">
                        <?= $this->shared("forum_pod"); ?>
                    </div>
                </div>
                <div class="col-lg-2 col-md-2 col-sm-2"></div>


            </div>


            <div id="post-pane" class="tab-pane">
                <!--Post start-->
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="create-form">
                            <form method="post" data-sw-sync="Post_form_mdl" enctype="multipart/form-data">
                                <div class="form-group">
                                    <textarea name="post_text" class="form-control"
                                              placeholder="Type your Post text...."></textarea>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="post-pic-upload btn btn-default">
                                            <div class="pic-upld-box"><span class="glyph">&#xf083; </span> &nbsp; <span
                                                    class="upld-text">Upload a picture</span></div>
                                            <input class="pic-file" name="post-pic" type="file" accept="image/*"/>
                                        </div>
                                    </div>
                                </div>
                                <button id="post-submit" class="btn btn-primary" type="submit">Create</button>
                                <div class="row" data-sw-show="upld_spinner:false">
                                    <span class="glyph spinner fa-spin">&#xf185;</span>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-3"></div>
                </div>
                <!--spinner-->
                <div class="row" data-sw-show = "post_spinner:false">
                    <div class="col-md-5"></div>
                    <div class="col-md-2"><span class="glyph spinner fa-spin">&#xf185;</span></div>
                    <div class="col-md-5"></div>
                </div>

                <div class="row">
                    <!--forums-->
                    <div class="col-lg-3 col-md-3 col-sm-3"></div>
                    <div class="col-lg-6 col-md-6 col-sm-6">
                        <div id="usap-pod-wrapper" data-sw-model="Post_mdl post"> <!---->
                            <?=$this->shared("post_pod");?>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-3"></div>
                </div>

            </div>

            <div class="tab-pane" id="Friends">
                <!--Friend pane content-->
                <?=$this->shared("friend-pane")?>
            </div>

            <?=$this->shared("bookmark-pane")?>

            <div class="tab-pane" id="Groups">
                <!--Group pane content-->
                <?=$this->shared("group-pane")?>
            </div>

            <?=$this->shared("search-pane")?>


            <?=$this->shared("upload-pane")?>

            <!--Notifications pane-->
            <?=$this->shared("noti-pane")?>
        </div>


    </div>
</div>

<?= $this->htmlScript("controllers/usap.group.controller.js") ?>

<?= $this->htmlScript("models/forum.model.js") ?>
<?= $this->htmlScript("models/post.model.js") ?>
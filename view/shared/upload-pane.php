<div class="tab-pane col-md-12" id="Upload">
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4">

            <form id="upload-form" action="<?= HOST_URL ?>/upload" method="post" enctype="multipart/form-data">
                <div class="file form-control">
                    <input type="file" name="file"/>
                </div>
                <button class="btn btn-primary btn-block" type="submit">Upload</button>
                <input type="hidden" name="tag" value="<?= $this->viewBag["tag"] ?>"/>
                <input id="magc_id" type="hidden" name="magic_id" value="user"/>
            </form>
        </div>
        <div class="col-md-4"></div>
    </div>

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <div class="upld-wrapper" data-sw-model="Upload_mdl upld">
                {{? upld.file_type.search(/pdf/) == 0}}{{var img = 'pdf2.png';}}{{?? upld.file_type.search(/img/) == 0
                }}{{ var img = 'pic.png';}}{{?? upld.file_type.search(/w/) == 0}}{{var img = 'word.png';}}{{??
                upld.file_type.search(/pp/) == 0 }}{{var img = 'ppt.png';}}{{?}}
                <?php
                if ($this->viewBag["tag"] == 'user') {
                    ?>
                    <!--user upload pod-->
                    <div class="upld-pod list-group-item" data-mgcid="{{=upld.magic_id}}">
                        <div class="l-cont">
                            <div class="f-type">
                                <?= $this->htmlIMG("{{=img}}"); ?>
                            </div>
                            <div class="f-det">
                                <p>{{=upld.file_name}}</p>
                            </div>
                        </div>
                        <div class="r-cont">
                            <div class="size">
                                <span>Size: {{=upld.file_size/1000}}KB</span>
                                <!--download link-->
                                <span><a href="{{=upld.file_url}}" class="glyph" target="_blank" title="Download File">
                                        &#xf019;</a></span>
                                <!--share link-->
                                <span><a href=":javascript" class="glyph" title="Share with friends">&#xf045;</a></span>
                            </div>
                            <div class="date pull-right">
                                {{=upld.created_at}}
                            </div>
                        </div>
                    </div>
                <?php
                } else {
                    ?>
                    <!--non user upload pod-->
                    <div class="upld-pod list-group-item">
                        <div class="l-cont">
                            <div class="f-type">
                                <?= $this->htmlIMG("{{=img}}"); ?>
                            </div>
                            <div class="f-det">
                                <p>Uploaded
                                    by: <?= $this->htmlAnchor('profile', '{{=upld.user_prof.full_name}}', '{{=upld.user_prof.user_id}}') ?></p>

                                <p>File: {{=upld.file_name}}</p>
                            </div>
                        </div>
                        <div class="r-cont">
                            <div class="size">
                                <span>Size: {{=upld.file_size/1000}}KB</span>
                                <!--download link-->
                                <span><a href="{{=upld.file_url}}" class="glyph" target="_blank" title="Download File">
                                        &#xf019;</a></span>
                            </div>
                            <div class="date pull-right">
                                {{=upld.created_at}}
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
        <div class="col-md-2"></div>
    </div>
</div>
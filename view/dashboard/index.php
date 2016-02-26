<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html;" charset="UTF-8"/>
    <meta name="description" content="This is an Academic social platform to enable students share and learn from each other"/>
    <meta name="author" content="Kwaku Appiah-Kubby Osei Kofi Oware Jerome Davor Gammeli"/>
    <title><?= $this->viewBag['title']?></title>

    <title>MainPage</title>

    <!-- page styles -->
    <?=$this->htmlLink('shared.css','stylesheet')?>
    <?=$this->htmlLink('bootstrap.css','stylesheet')?>
    <?=$this->htmlLink('dashboard.css','stylesheet')?>
    <?=$this->htmlScript('jquery-1.10.2.min.js','text/javascript')?>
    <?=$this->htmlScript('sswap-lib.js')?>

</head>
<body>

<div class="container">
    <div class="navbar navbar-inverse navbar-fixed-top nav-color colorful" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"><span class="glyphicon glyphicon-user"></span> Administrator</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">

            <ul class="nav navbar-nav navbar-right">
                <li><?=$this->htmlAnchor("admin_signout","Sign Out")?>
                </li>
            </ul>
        </div>

        <ul></ul>

    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Flagged Content</h3>
                </div>
                <div class="panel-body" >
                    <div>
                        <h3>Flagged Posts</h3>
                        <div data-sw-model="Post_mdl post">
                            <?=$this->shared("post_pod");?>
                        </div>
                    </div>
                    <div>
                        <h3>Flagged Forums</h3>
                        <div data-sw-model="Forum_mdl forum">
                            <?= $this->shared("forum_pod"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="panel-title input-group col-md-12">
                        <input type="text" class="search-query form-control" placeholder="Search Users" />
                        <span class="input-group-btn">
                            <button id="srch" class="btn btn-danger" type="button">
                                <span class="glyph">&#xf002;</span>
                            </button>
                        </span>
                    </div>
                </div>
                <div id="user-results" class="panel-body">
                    <div class="list-group">
                        <div data-sw-model="SearchUser_mdl prof">
                            <div class="list-group-item row" data-id="{{=prof.user_id}}">
                                <div class="mem-pic col-md-2">
                                    <img src="{{=prof.profile_pic_thumb}}" alt="" class="img-circle">
                                </div>
                                <div class="mem-name col-md-10">
                                    <div class="col-md-10">
                                        <p class="center"><?=$this->htmlAnchor('profile',"{{=prof.full_name}}","{{=prof.user_id}}")?></p>
                                        <p>Index Number: {{=prof.index_number}}</p>
                                    </div>
                                    <div class="col-md-2">
                                        <!--Black List Button-->
                                        {{?prof.black_list == 0}}
                                        <button type="button" class="del-btn btn btn-danger pull-right">Black List</button>
                                        {{?}}

                                        <!--Restore Button-->
                                        {{?prof.black_list == 1}}
                                        <button type="button" class="res-btn btn btn-success pull-right">Restore</button>
                                        {{?}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?=$this->htmlScript('shared.js')?>
<?=$this->htmlScript('app/app_services.js')?>
<?= $this->htmlScript("models/forum.model.js", "text/javascript") ?>
<?= $this->htmlScript("models/post.model.js", "text/javascript") ?>
<?=$this->htmlScript('controllers/usap.dashboard.controller.js','text/javascript')?>
</body>
</html>
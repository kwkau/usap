<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html;" charset="UTF-8"/>
    <meta name="description" content="This is an Academic social platform to enable students share and learn from each other"/>
    <meta name="author" content="Kwaku Appiah-Kubby Osei Kofi Oware Jerome Davor Gammeli"/>

    <title>Admin Login</title>

    <!-- page styles -->
    <?=$this->htmlLink('bootstrap.css','stylesheet')?>
    <?=$this->htmlLink('shared.css','stylesheet')?>
    <?=$this->htmlLink('dashboard.css','stylesheet')?>
    <?=$this->htmlLink('admin.css','stylesheet')?>
    <?=$this->htmlScript('jquery-1.10.2.min.js','text/javascript')?>
    <?=$this->htmlScript('sswap-lib.js')?>
</head>
<body>

<div class="container">

    <div class="col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">

        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="panel-title text-center adminfont">Administrator</div>
            </div>

            <div class="panel-body2 login-margin" >

                <form id="login-form" action="<?=HOST_URL?>/dashboard/login" method="POST">

                    <div class="input-group">
                        <span class="input-group-addon glyphicon glyphicon-user"></span>
                        <input id="user" type="text" class="form-control" name="username" value="" placeholder="Username"/>
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon glyphicon glyphicon-lock"></span>
                        <input id="password" type="password" class="form-control" name="password-hash" placeholder="Password"/>
                    </div>

                    <div class="form-group">

                        <div class="col-sm-12 controls">
                            <button type="submit" class="btn btn-primary btn-block"><i class="glyphicon glyphicon-log-in"></i> Log in</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>
<?=$this->htmlScript('controllers/admin.login.js','text/javascript')?>
</body>
</html>
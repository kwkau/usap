
<!DOCTYPE html>
<html lang="en" id="usap">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta http-equiv="Content-Type" content="text/html;" charset="UTF-8"/>
    <meta name="description" content="This is an Academic social platform to enable students share and learn from each other"/>
    <meta name="author" content="Kwaku Appiah-Kubby Osei Kofi Oware Jerome Davor Gammeli"/>
    <title><?=$this->viewBag['title']?></title>
    <!-- page styles -->
    <?=$this->htmlLink('bootstrap.css','stylesheet')?>
    <?=$this->htmlLink('shared.css','stylesheet')?>
    <?=$this->htmlLink('errors.css','stylesheet')?>
    <?=$this->htmlScript('jquery-1.10.2.min.js','text/javascript')?>
    <?=$this->htmlScript('sswap-lib.js')?>
</head>
<body>
<!--page header-->
<div id=header-cont class="row">
    <?=$this->shared('header');?>
</div>


<!--main content-->
<div class="row error-cont">
    <div class="col-md-2"></div>
    <div class="col-md-8" data-sw-model="Error_mdl err">

        <div class="error-pod">
            <div class="pod-header">
                <div class="er-target-info">
                    <div class="socket-name">Socket Name: {{=err.socket_name}}</div>
                    <div class="message">Message: {{=err.message}}</div>
                    <div class="line-number">Line: {{=err.line_number}}</div>
                    <div class="date">Time: {{=err.created_at}}</div>
                </div>
            </div>
            <div class="pod-cont">
                <div class="col-md-6">
                    <h3>Stack Trace</h3>
                    <div class="cont">
                        {{=err.stack_trace}}
                    </div>
                </div>
                <div class="col-md-6">
                    <h3>Code</h3>
                    <div class="cont">
                        {{=err.code}}
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-md-2"></div>
</div>



<!-- page scripts -->
<?=$this->htmlScript('shared.js')?>
<?=$this->htmlScript('app/app_services.js')?>
<?=$this->htmlScript('bootstrap.min.js')?>
<?=$this->htmlScript('controllers/error.controller.js')?>
<?=$this->htmlScript('models/error.model.js')?>
</body>
</html>
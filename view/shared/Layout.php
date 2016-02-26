
    <!DOCTYPE html>
    <html lang="en" id="usap">
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <meta http-equiv="Content-Type" content="text/html;" charset="UTF-8"/>
            <meta name="description" content="This is an Academic social platform to enable students share and learn from each other"/>
            <meta name="author" content="Kwaku Appiah-Kubby Osei Kofi Oware Jerome Davor Gammeli"/>
            <title><?= $this->viewBag['title']?></title>
            <!-- page styles -->
            <?=$this->htmlLink('bootstrap.css','stylesheet')?>
            <?=$this->htmlLink('shared.css','stylesheet')?>
            <?=$this->htmlScript('jquery-1.10.2.min.js','text/javascript')?>
            <?=$this->htmlScript('sswap-lib.js')?>
        </head>
        <body>
            <!--page header-->
            <div id=header-cont class="row">
                    <?=$this->shared('header');?>
            </div>


            <!--main content-->
            <div class="row">
                    <!--side content-->
                    <?
                        if($this->page != "login")$this->shared('side_left');
                    ?>

                    <!--content-->
                    <?
                        $this->layout_body($this->page);
                    ?>
            </div>



            <!-- page scripts -->
            <?=$this->htmlScript('shared.js')?>
            <?=$this->htmlScript('app/app_services.js')?>
            <?=$this->htmlScript("models/user.model.js")?>
            <?=$this->htmlScript("models/group.model.js")?>
            <?=$this->htmlScript('bootstrap.min.js')?>
            <?=$this->htmlScript("models/noti.model.js")?>
        </body>
    </html>
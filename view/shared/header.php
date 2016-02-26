

<?
    if($this->page == "login"){
        //display login nav bar content
        $home = "";
        $dynamo=
            '
            ';
    }else{
        $home = $this->htmlAnchor("dropzone",'<span>&#xf015;</span>');
        $dynamo=
            '
            <div class="collapse navbar-collapse navbar-right" id="bs-WDM-navbar-collapse-1">
            <!--Collapse Navbar for Small Screens-->
            <ul class="nav navbar-nav header-icons">
                <li>
                    <div class="search">
                        <div class="dropdown-toggle search-btn" data-tab="Search" data-toggle="dropdown" role="button" aria-expanded="false">
                            <span class="glyph">&#xf002;</span>
                        </div>
                        <input type="text" class="form-control input-md" maxlength="64" placeholder="Search"/>
                    </div>
                </li>

                <li>
                <!--comments -->
                    <div class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyph">&#xf01c;</span></div>
                    <ul class="dropdown-box dropdown-menu" role="menu">
                        <li>
                            <div>messages</div>
                        </li>
                    </ul>
                </li>

                <li>
                    <div class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><span class="glyph">&#xf013;</span></div>
                    <ul class="dropdown-box dropdown-menu" role="menu">
                        <li>
                            <div>'.$this->htmlAnchor("logout","Logout").'</div>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
            ';
    }
?>


    <nav class="navbar navbar-inverse navbar-static-top" role="navigation">
        <div class="container-fluid">
            <div class="left-menu">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".sidebar">
                        <span class="glyph"> &#xf142;</span>
                    </button>


                <div class="ussap">
                    usap
                </div>

            </div>
            <div  class="glyph home">
                <?=$home?>
            </div>

            <div class="center-menu">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-WDM-navbar-collapse-1">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>



            <?=$dynamo?>
        </div>
    </nav>

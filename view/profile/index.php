
<?=$this->htmlLink('profile.css','stylesheet')?>
<div class="col-lg-8 col-md-8 col-sm-8 content" xmlns="http://www.w3.org/1999/html">
    <div class="tabbable" id="tabs-183532">
        <?=$this->shared("tab-nav")?>
        <div class="tab-content">
            <div id="profile-pane" class="tab-pane active">

                <!--profile-->
                <div class="col-lg-1 col-md-1 col-sm-1"></div>
                <div class="col-lg-10 col-md-10 col-sm-10">
                    <br/>
                    <div class="well panel">
                        <!--spinner-->
                        <!--
                        <div class="col-md-12" data-sw-show="spinner:true">
                            <div class="col-md-2"></div>
                            <div class="col-md-8"><span class="glyph spinner fa-spin">&#xf185;</span></div>
                            <div class="col-md-2"></div>
                        </div>-->
                        <div class="panel-body">
                            <div class="row profile-details" data-sw-model="Profile_mdl prof">
                                <div class="col-xs-12 col-sm-4 text-center">
                                    <form action="<?=HOST_URL?>/profile/uploads" method="post" enctype="multipart/form-data">
                                        <div class="profile-pic">
                                            <div class="center-block">
                                                <img src="{{=prof.profile_pic}}" alt=" " class="img-circle img-thumbnail img-responsive"/>
                                                <input name="profile_pic" class="center-block img-responsive" alt="Choose a new profile picture" type="file" accept="image/jpeg"/>
                                            </div>
                                        </div>

                                        <p>Upload a different photo...</p>
                                        <p>
                                            <button class="btn btn-primary" type="submit">Upload</button>
                                        </p>
                                    </form>

                                </div>
                                <!--/col-->
                                <div class="col-xs-12 col-sm-8">
                                    <h2>{{=prof.full_name}}</h2>
                                    <p><strong>First Name: </strong> <span class="text-capitalize" data-prop="first_name">{{=prof.first_name}}</span> <span class="glyph edit">&#xf044;</span></p>
                                    <p><strong>Last Name: </strong> <span class="text-capitalize" data-prop="last_name">{{=prof.last_name}}</span> <span class="glyph edit">&#xf044;</span></p>

                                    <p><strong>Telephone: </strong> <span data-prop="phonenumber">{{=prof.phone||"Enter a contact number"}}</span> <span class="glyph edit">&#xf044;</span></p>
                                    <p><strong>Email: </strong> <span data-prop="email_address">{{=prof.email_address}}</span> <span class="glyph edit">&#xf044;</span></p>
                                </div>


                                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-success"></button>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-primary"></button>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-danger"></button>
                                    </div>
                                </div>

                                <div class="row">
                                    <h3>Education</h3>
                                    <p><strong>You are a: </strong> <span class="text-capitalize" data-prop="category">{{=prof.type}}</span> </p>
                                    <p><strong>Faculty: </strong> <span>{{=prof.department.school}}</span> </p>
                                    <p><strong>Department: </strong> <span>{{=prof.department.name}}</span> </p>
                                    <p><strong>Index Number: </strong> <span class="text-uppercase">{{=prof.index_number}}</span></p>
                                    <p><strong>Programme: </strong> <span data-prop="programme">{{=prof.programme||'What is your programme?'}}</span> <span class="glyph edit">&#xf044;</span></p>
                                    <p><strong>Year Start: </strong> <span data-prop="year_start">{{=prof.year_start||'Enter your start year'}}</span> <span class="glyph edit">&#xf044;</span></p>
                                    <p><strong>Year Completing: </strong> <span data-prop="year_complete">{{=prof.year_complete||'Enter your completion year'}}</span> <span class="glyph edit">&#xf044;</span></p>
                                </div>

                                <div class="row">
                                    <h3>Personal</h3>
                                    <p><strong>About Me: </strong> <span data-prop="about_me">{{=prof.about_me||"Write something about yourself"}}</span>  <span class="glyph edit">&#xf044;</span></p>
                                    <p><strong>Gender: </strong> <span>{{=prof.gender == "M"? "Male":"Female"}}</span> </p>
                                    <p><strong>Hobbies: </strong> <span data-prop="hobbies">{{=prof.hobbies||"Any Hobbies?"}}</span> <span class="glyph edit">&#xf044;</span></p>

                                </div>

                            </div>
                            <!--/row-->
                        </div>
                        <!--/panel-body-->
                    </div>
                </div>
                <div class="col-lg-1 col-md-1 col-sm-1"></div>


            </div>




                <!--Notifications-->
                <?=$this->shared("noti-pane")?>

            <div class="tab-pane" id="panel-3">
                <div class="container-fluid">


                </div>

            </div>


            <div class="tab-pane" id="panel-4">

            </div>
        </div>
    </div>
</div>

    <?=$this->htmlScript("controllers/usap.profile.controller.js","text/javascript")?>
    <?=$this->htmlScript("models/profile.model.js","text/javascript")?>
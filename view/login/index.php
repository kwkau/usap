

<?=$this->htmlLink("login.css","stylesheet")?>

<div class="col-md-12">

    <!--login form-->
    <div >
        <div >
            <div class="col-sm-6 lft">

                <div class="account-wall">
                    <h1 class="text-center login-title">Sign in to USAP</h1>
                    <?=$this->htmlIMG("loginicon1.png")?>
                    <form class="form-signin" id="login-form" action="<?=HOST_URL?>/login/user_login" method="post">
                        <div class="control-group">
                            <div class="controls">
                                <input type="text" class="form-control" name="username" placeholder="<?=isset($this->viewBag["username_err"])? 'Invalid Email, please check your Email':'Email'?>" required/>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <input type="password" class="form-control" name="raw-password" placeholder="<?=isset($this->viewBag["password_err"])? 'Invalid Password, please check your Password':'Password'?>" required />
                                <input type="hidden" name="password-hash" value=""/>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <button class="btn btn-lg btn-primary btn-block" type="submit">
                                    Sign in
                                </button>
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <label class="checkbox">
                                    <input class="usap-checkbox" type="checkbox" value="checked" name="remember_me"/>Remember me
                                </label>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>

    <!--registration form-->
    <div >
        <div class="col-sm-6 ">

            <div class="account-wall rht">
                <h1 class="text-center login-title">Register and join USAP</h1>
                <form id="reg-form" class="form-signin" action="<?=HOST_URL?>/login/register"  method="post">
                    <input type="text" class="form-control" name="first_name" placeholder="First Name" required/>

                    <input type="text" class="form-control" name="last_name" placeholder="Last Name" required/>

                    <input type="text" class="form-control" name="user_email" placeholder="Email" required/>

                    <input type="password" class="form-control" name="first_password" placeholder="Password" required />

                    <input type="password" class="form-control" name="second_password" placeholder="Confirm Password" required/>

                    <input type="hidden" class="form-control" value="" name="reg-password-hash" />

                    <select id="cat" class="form-control" name="category" required>
                        <option value="">Category</option>
                        <option value="student">Student</option>
                        <option value="lecturer">Lecturer</option>
                        <option value="alumni">Alumni</option>
                    </select>

                    <select class="form-control" name="department" required>
                        <option value="">Department</option>
                        <?foreach($this->viewBag["departments"] as $department){
                            echo "\n<option value=\"{$department->name}\">{$department->name}</option>";
                        }?>
                    </select>

                    <input type="text" class="form-control" name="index_number" data-sw-show="index:true" placeholder="Index Number"/>
                    <input type="text" class="form-control" name="token" data-sw-show="token:false" placeholder="Lecturer Token"/>

                    <select class="form-control" name="gender" required>
                        <option value="">Gender</option>
                        <option value="M">Male</option>
                        <option value="F">Female</option>
                    </select>

                    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign Up</button>
                </form>
            </div>

        </div>
    </div>

</div>

<?=$this->htmlScript("controllers/ussap.login.controller.js","text/javascript")?>

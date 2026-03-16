<!DOCTYPE html>
<html>

    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Admin | Login</title>

        <link href="<?= ADMIN_ASSETS_PATH ?>assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?= ADMIN_ASSETS_PATH ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet">

        <link href="<?= ADMIN_ASSETS_PATH ?>assets/css/animate.css" rel="stylesheet">
        <link href="<?= ADMIN_ASSETS_PATH ?>assets/css/style.css" rel="stylesheet">
        <style>
            body{
                background: url(<?= ADMIN_ASSETS_PATH ?>assets/images/authentication-bg.svg);
                background-size: contain;
                background-position: center;
                min-height: 100vh;
            }
            .customLogin{
                width: 400px;
                background: #e3e5ef;
                padding: 10px 20px;
                border-radius: 10px;
                margin-top: 20px;
            }
            .customLogo{
                margin-top:90px;
            }
            .customLogin h2{
                margin:0px;
            }
        </style>

    </head>

    <body class="gray-bg">
        <div class="customLogo">
            <img src="<?= ADMIN_ASSETS_PATH ?>assets/images/logo.png" style="width:290px;margin:auto;display:block"/>
        </div>
        <div class="middle-box text-center loginscreen animated fadeInDown customLogin" style="width:350px">
            <div>
                <h2>Admin Login</h2>
                <?php if (!empty($this->session->flashdata('login_success_message'))) { ?>
                    <div class="alert alert-success fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        <strong> Success!</strong> <?= $this->session->flashdata('login_success_message') ?>
                    </div>
                <?php } ?>
                <?php if (!empty($this->session->flashdata('login_error_message'))) { ?>
                    <div class="alert alert-danger fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        <strong>Failed!</strong> <?= $this->session->flashdata('login_error_message') ?>
                    </div>
                <?php }
                ?>

                <form class="m-t" role="form" action="<?php echo base_url(); ?>admin/login/admin_login" method="post">
                    <div class="form-group">
                        <input type="text" class="form-control"  name="email" id="email" placeholder="Email">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Password">

                        <input type="hidden"  name="login_type" value="admin">
                    </div>
                    <!-- <div class="form-group">
                     <label style="float:left">Select Login Type</label> 
                      
                            <select name="login_type" class="form-control">
                                <option value="admin" selected="">Super Admin</option>
                                <option value="subadmin">Sub Admin</option>
                            </select>
                      
                    </div> -->
                    <button type="submit" class="btn btn-primary block full-width m-b" id="btn_login">Login</button>

                <!-- <a href="<?php echo base_url(); ?>admin/forgotpassword"><small>Forgot password?</small></a> --> 
                <!--<p class="text-muted text-center"><small>Do not have an account?</small></p>
                <a class="btn btn-sm btn-white btn-block" href="register.html">Create an account</a>-->
                </form>
                <p class="m-t"> <small>Doctto &copy; <?php echo date('Y'); ?></small> </p>
                

            </div>
        </div>

        <!-- Mainly scripts -->
        <script src="<?= ADMIN_ASSETS_PATH ?>assets/js/jquery-2.1.1.js"></script>
        <script src="<?= ADMIN_ASSETS_PATH ?>assets/js/bootstrap.min.js"></script>

    </body>

</html>

<script src="<?= ADMIN_ASSETS_PATH ?>assets/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript">



  $('#btn_login').click(function(){
        $('.error').remove();
            var errr=0;
      if($('#email').val()=='')
      {
         $('#email').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Email</span>');
         $('#email').focus();
         return false;
      }
      else if(!validateEmail($('#email').val())) 
      { 
        $('#email').after('<span class="error" style="color:red">Invalid Email Address</span>');
        $('#email').focus();
        return false;
      } 
      else if($('#password').val()=='')
      {
         $('#password').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Password</span>');
         $('#password').focus();
         return false;
      }
      
       

 });

  function validateEmail($email) 
{
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if( !emailReg.test( $email) ) {
      return false;
    } 
    else
    {
        return true;
    }
}
</script>

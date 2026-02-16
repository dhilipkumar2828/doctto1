<div class="row">
    <div class="col-lg-12">
        <div class="ibox-title">
               
                <div class="ibox-tools">
                    <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                </div>
            </div>

      <div class="col-lg-6">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= $title ?></h5>
                
            </div>
             <?php if (!empty($this->session->flashdata('success_message'))) { ?>
                        <div class="alert alert-success fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong> Success!</strong> <?= $this->session->flashdata('success_message') ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($this->session->flashdata('error_message'))) { ?>
                        <div class="alert alert-danger fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>Failed!</strong> <?= $this->session->flashdata('error_message') ?>
                        </div>
                    <?php }
                    ?>
            <div class="ibox-content">
                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/settings/update">
                    <div class="form-group">
                        <label class="col-sm-5 control-label">Username : </label>
                        <div class="col-sm-7">
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo $admin->name; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label">Email : </label>
                        <div class="col-sm-7">
                            <input type="text" id="email" name="email" class="form-control" value="<?php echo $admin->email; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 control-label">Phone : </label>
                        <div class="col-sm-7">
                            <input type="text" id="mobile" name="mobile" class="form-control" value="<?php echo $admin->mobile; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 control-label">Social Share Title : </label>
                        <div class="col-sm-7">
                            <input type="text" id="share_title" name="share_title" class="form-control" value="<?php echo $admin->share_title; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label">Play store Link ( User App ) : </label>
                        <div class="col-sm-7">
                            <input type="text" id="playstore_userlink" name="playstore_userlink" class="form-control" value="<?php echo $admin->playstore_userlink; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-6 control-label">Play store Link ( Vendor App ) : </label>
                        <div class="col-sm-6">
                            <input type="text" id="playstore_vendorlink" name="playstore_vendorlink" class="form-control" value="<?php echo $admin->playstore_vendorlink; ?>">
                        </div>
                    </div>
                   <!--  <div class="form-group">
                        <label class="col-sm-2 control-label">Play store Link ( Delivery Boy App ) : </label>
                        <div class="col-sm-10">
                            <input type="text" id="playstore_dblink" name="playstore_dblink" class="form-control" value="<?php echo $admin->playstore_dblink; ?>">
                        </div>
                    </div> -->
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit" id="btn_profile"> <i class="fa fa-plus-circle"></i> Update</button>
                        </div>
                    </div>
                </form>
            </div>


             
            


        </div>
      </div>
      <div class="col-lg-6">
         <div class="ibox-title">
                <h5>Change Password</h5>
                <div class="ibox-tools">

                </div>
            </div>

        <div class="ibox-content">
               <?php if (!empty($this->session->flashdata('success_message1'))) { ?>
                        <div class="alert alert-success fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong> Success!</strong> <?= $this->session->flashdata('success_message1') ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($this->session->flashdata('error_message1'))) { ?>
                        <div class="alert alert-danger fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>Failed!</strong> <?= $this->session->flashdata('error_message1') ?>
                        </div>
                    <?php }
                    ?>
                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/settings/changePassword">
                    <div class="form-group">
                        <label class="col-sm-5 control-label">Old Password : </label>
                        <div class="col-sm-7">
                            <input type="text" id="oldpassword" name="oldpassword" class="form-control" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-5 control-label">New Password : </label>
                        <div class="col-sm-7">
                            <input type="text" id="newpassword" name="newpassword" class="form-control" value="">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-5 control-label">Confirm Password : </label>
                        <div class="col-sm-7">
                            <input type="text" id="confirm_password" name="confirm_password" class="form-control" value="">
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit" id="btn_changePassword"> <i class="fa fa-plus-circle"></i> Update</button>
                        </div>
                    </div>
                </form>
            </div>
      </div>






      <!-- <div class="col-lg-6">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Bonus Coins</h5>
                <div class="ibox-tools">

                </div>
            </div>
             <?php if (!empty($this->session->flashdata('bonussuccess_message'))) { ?>
                        <div class="alert alert-success fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong> Success!</strong> <?= $this->session->flashdata('bonussuccess_message') ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($this->session->flashdata('bonuserror_message'))) { ?>
                        <div class="alert alert-danger fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>Failed!</strong> <?= $this->session->flashdata('bonuserror_message') ?>
                        </div>
                    <?php }
                    ?>
            <div class="ibox-content">
                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/settings/updatebonucoins">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Minimum Order Amount : </label>
                        <div class="col-sm-10">
                            <input type="text" id="order_amount" name="order_amount" class="form-control" value="<?php echo $admin->order_amount; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Coins : </label>
                        <div class="col-sm-10">
                            <input type="text" id="coins" name="coins" class="form-control" value="<?php echo $admin->coins; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Coins per Amount : </label>
                        <div class="col-sm-10">
                            <input type="text" id="coinperamount" name="coinperamount" class="form-control" value="<?php echo $admin->coinperamount; ?>">
                        </div>
                    </div>

                    
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit" id="btn_coins"> <i class="fa fa-plus-circle"></i> Update</button>
                        </div>
                    </div>
                </form>
            </div>


             
            


        </div>
      </div> -->



      <!-- <div class="col-lg-6">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Orders Distance & Bid</h5>
                <div class="ibox-tools">

                </div>
            </div>
             <?php if (!empty($this->session->flashdata('distsuccess_message'))) { ?>
                        <div class="alert alert-success fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong> Success!</strong> <?= $this->session->flashdata('distsuccess_message') ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($this->session->flashdata('disterror_message'))) { ?>
                        <div class="alert alert-danger fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>Failed!</strong> <?= $this->session->flashdata('disterror_message') ?>
                        </div>
                    <?php }
                    ?>
            <div class="ibox-content">
                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/settings/updateradius">
                        
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Distance : </label>
                        <div class="col-sm-10">
                            <input type="text" id="distance" name="distance" class="form-control" value="<?php echo $admin->distance; ?>">
                        </div>
                    </div>

                     <div class="form-group">
                        <label class="col-sm-2 control-label">Bid Status : </label>
                        <div class="col-sm-10">
                            <select id="bid_show_status" name="bid_show_status" class="form-control">
                                <option value="1" <?php if($admin->bid_show_status==1){ echo 'selected="selected"'; }?>>SHOW</option>
                                <option value="0" <?php if($admin->bid_show_status==0){ echo 'selected="selected"'; }?>>HIDE</option>
                            </select>
                        </div>
                    </div>

                    
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit" id="btn_distance"> <i class="fa fa-plus-circle"></i> Update Distance</button>
                        </div>
                    </div>
                </form>
            </div>


             
            


        </div>
      </div>
 -->


      <!-- <div class="col-lg-6">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>User Referal Settings</h5>
                <div class="ibox-tools">

                </div>
            </div>
             <?php if (!empty($this->session->flashdata('referallsuccess_message'))) { ?>
                        <div class="alert alert-success fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong> Success!</strong> <?= $this->session->flashdata('referallsuccess_message') ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($this->session->flashdata('referallerror_message'))) { ?>
                        <div class="alert alert-danger fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>Failed!</strong> <?= $this->session->flashdata('referallerror_message') ?>
                        </div>
                    <?php }
                    ?>
            <div class="ibox-content">
                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/settings/updatereferral">
                    
                    <div class="form-group">
                        <label class="col-sm-6 control-label">User Registration Coins : </label>
                        <div class="col-sm-6">
                            <input type="text" id="user_register_coins" name="user_register_coins" class="form-control" value="<?php echo $admin->registration_coins; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-6 control-label">User Refaral ( First Order Complete ) : </label>
                        <div class="col-sm-6">
                            <input type="text" id="user_refferal_coins" name="user_refferal_coins" class="form-control" value="<?php echo $admin->first_order_coins; ?>">
                        </div>
                    </div>

                    
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-10 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit" id="btn_refferal"> <i class="fa fa-plus-circle"></i> Update</button>
                        </div>
                    </div>
                </form>
            </div>


             
            


        </div>
      </div> -->



    </div>
</div>


<script type="text/javascript">

$('#btn_refferal').click(function(){
        $('.error').remove();
            var errr=0;
      if($('#user_register_coins').val()=='')
      {
         $('#user_register_coins').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter User Registration Coins</span>');
         $('#user_register_coins').focus();
         return false;
      }
      else if($('#user_refferal_coins').val()=='')
      {
         $('#user_refferal_coins').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter User Refaral</span>');
         $('#user_refferal_coins').focus();
         return false;
      }
 });

  $('#btn_distance').click(function(){
        $('.error').remove();
            var errr=0;
      if($('#distance').val()=='')
      {
         $('#distance').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Distance</span>');
         $('#distance').focus();
         return false;
      }
 });

  $('#btn_coins').click(function(){
        $('.error').remove();
            var errr=0;
      if($('#order_amount').val()=='')
      {
         $('#order_amount').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Minimum Order Amount</span>');
         $('#order_amount').focus();
         return false;
      }
      else if($('#coins').val()=='')
      {
         $('#coins').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Coins</span>');
         $('#coins').focus();
         return false;
      } 
      else if($('#coinperamount').val()=='')
      {
         $('#coinperamount').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Coin per amount</span>');
         $('#coinperamount').focus();
         return false;
      } 
 });


  $('#btn_profile').click(function(){
        $('.error').remove();
            var errr=0;
            var ph = $('#mobile').val();
      if($('#name').val()=='')
      {
         $('#name').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Name</span>');
         $('#name').focus();
         return false;
      }
      else if($('#email').val()=='')
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
      else if($('#mobile').val()=='')
      {
         $('#mobile').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Mobile</span>');
         $('#mobile').focus();
         return false;
      }
       else if(ph.length!=10)
      {
         $('#mobile').after('<span class="error" style="color:red">Enter Valid 10 digit Phone Number</span>');
         $('#mobile').focus();
         return false;
      }  
 });



  $('#btn_changePassword').click(function(){
        $('.error').remove();
            var errr=0;
        var newpassword = $('#newpassword').val().length;
        //alert(newpassword);
      if($('#oldpassword').val()=='')
      {
         $('#oldpassword').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Old Password</span>');
         $('#oldpassword').focus();
         return false;
      }
      else if($('#newpassword').val()=='')
      {
         $('#newpassword').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter New Password</span>');
         $('#newpassword').focus();
         return false;
      }
      else if(newpassword<6)
      {
         $('#newpassword').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Please enter Minimum 6 digits</span>');
         $('#newpassword').focus();
         return false;
      }
      else if($('#confirm_password').val()=='')
      {
         $('#confirm_password').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter New Confirm Password</span>');
         $('#confirm_password').focus();
         return false;
      }
      else if($('#newpassword').val()!=$('#confirm_password').val())
      {
         $('#confirm_password').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Password Mismatched</span>');
         $('#confirm_password').focus();
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
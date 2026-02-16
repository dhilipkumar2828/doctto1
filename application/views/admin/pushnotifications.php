<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= $title ?></h5>
                <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/pushnotifications">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                    </div>
            </div>
            

            <div class="ibox-tools" style="float: left;">

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
                </div>
            <div class="ibox-content">
                <form method="post" id="formid" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/pushnotifications/insert">
                      
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Select Type</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="user_type" onchange="getStatus(this.value)">
                                <option value="all">Select All Users</option>
                                <option value="individual">Select Individual User</option>
                            </select>
                        </div>
                    </div>

                      <div class="form-group" id="showdiv" style="display: none;">
                        <label class="col-sm-2 control-label">Select User</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="user_id" name="user_id">
                                <option value="">Select User</option>
                                <?php
                                $user = $this->db->query("select * from users");
                                $result = $user->result();
                                foreach ($result as $vm) {
                                    ?>
                                    <option value="<?php echo $vm->id; ?>"><?php echo $vm->first_name." ".$vm->last_name; ?></option>
                                    <?php
                                } 
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Title</label>
                        <div class="col-sm-10">
                            <input type="text" id="title" name="title" class="form-control" required="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10">
                            <textarea rows="10" cols="10" id="description" name="description" class="form-control" required></textarea>
                        </div>
                    </div>
                     
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                          
                            <button class="btn btn-primary" type="submit" id="btn_category"> <i class="fa fa-plus-circle"></i> Add</button>
                         
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">



  function getStatus(val)
  {
    if(val=='all')
    {
      document.getElementById("showdiv").style.display = "none";
    }
    else
    {
      document.getElementById("showdiv").style.display = "block";
    }
  }


$('form#formid').submit(function(){
    $(this).find(':input[type=submit]').prop('disabled', true);
});

  $('#btn_category').click(function(){
        $('.error').remove();
            var errr=0;

           

      /*if($('#user_id').val()=='')
      {
         $('#user_id').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select UserId</span>');
         $('#user_id').focus();
         return false;
      }
      else*/ if($('#title').val()=='')
      {
         $('#title').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Title</span>');
         $('#title').focus();
         return false;
      }
      else if($('#description').val()=='')
      {
         $('#description').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Description</span>');
         $('#description').focus();
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
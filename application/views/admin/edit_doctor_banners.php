<div class="row">

    <div class="col-lg-12">

        <div class="ibox float-e-margins">

            <div class="ibox-title">

                <h5><?= $title ?></h5>

                <div class="ibox-tools">

                  <a href="<?= base_url() ?>admin/doctor_banners">
                            <button class="btn btn-primary">BACK</button>
                        </a>

                </div>
                 <?php if (!empty($this->session->flashdata('error_message'))) { ?>
                        <div class="alert alert-danger fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>Failed!</strong> <?= $this->session->flashdata('error_message') ?>
                        </div>
                    <?php }
                    ?>

            </div>


            <div class="ibox-content">

                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/doctor_banners/update">
                    <input type="hidden" name="id" value="<?=$banners->id?>">
                   <div class="form-group">
                        <label class="col-sm-2 control-label">Title</label>
                        <div class="col-sm-10">
                            <input type="text" name="title" id="title" class="form-control" value="<?=$banners->title?>">
                        </div>
                    </div>

                   
                    <?php

                    if ($banners->app_image) {

                        ?>

                        <div class="form-group">

                            <label class="col-sm-2 control-label">Preview</label>

                            <div class="col-sm-10">

                                <img width="200px" src="<?= base_url() ?>uploads/doctor_banners/<?= $banners->app_image ?> "/>

                            </div>

                        </div>

                        <?php

                    }

                    ?>

                    <div class="form-group">



                        <label class="col-sm-2 control-label">App Image</label>

                        <div class="col-sm-10">

                            <input type="file" name="appimage" class="form-control">

                             <span class="help-block m-b-none" style="color:red;">App Image Width : 500px and height : 500px</span>

                        </div>

                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Banner Type</label>
                        <div class="col-sm-10">
                            <select name="banner_type" id="banner_type" onchange="check_type(this)" class="form-control">
                                <option value="">-- Select banner type -- </option>
                                <option value="category" <?=$banners->banner_type=="category"?"selected":""?>>Symptoms</option>
                                <option value="doctor" <?=$banners->banner_type=="doctor"?"selected":""?>>Doctors</option>
                            </select>
                           
                        </div>
                    </div>

                    <div class="form-group" style="display: <?=$banners->banner_type=="category"?"block":"none"?>;" id="category_section">
                        <label class="col-sm-2 control-label">Symptoms</label>
                        <div class="col-sm-10">
                            <select name="category_id" id="category_id" class="form-control">
                                <option value="">-- Select Symptoms -- </option>
                                <?php
                                    foreach ($categories as $row) {
                                        ?>
                                            <option value="<?=$row->id?>" <?=$row->id==$banners->category_id?"selected":""?>><?=$row->name?></option>
                                        <?php
                                    }
                                ?>
                                
                                
                            </select>
                           
                        </div>
                    </div>

                    <div class="form-group" style="display: <?=$banners->banner_type=="doctor"?"block":"none"?>;" id="doctor_section">
                        <label class="col-sm-2 control-label">Doctors</label>
                        <div class="col-sm-10">
                            <select name="doctor_id" id="doctor_id" class="form-control">
                                <option value="">-- Select Doctor -- </option>
                                <?php
                                    foreach ($doctors as $row) {
                                        ?>
                                            <option value="<?=$row->id?>" <?=$row->id==$banners->doctor_id?"selected":""?>><?=$row->doctor_name?></option>
                                        <?php
                                    }
                                ?>
                                
                                
                            </select>
                           
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="status" name="status">
                                    <option value="">Select Status</option>
                                    <option value="1" <?php if($banners->status==1){ echo "selected='selected'"; }?>>Active</option>
                                    <option value="0" <?php if($banners->status==0){ echo "selected='selected'"; }?>>InActive</option>
                            </select>
                        </div>
                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">

                        <div class="col-sm-4 col-sm-offset-2">

                            <button class="btn btn-primary" id="btn_category" type="submit"> <i class="fa fa-floppy-o"></i> Update</button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


<script type="text/javascript">

    function check_type(ele){
        var type = ele.value;
        if(type=="category"){
            $("#category_section").show();
            $("#doctor_section").hide();
        }
        else if(type=="doctor"){
             $("#doctor_section").show();
            $("#category_section").hide();
        }
  }

  $('#btn_category').click(function(){
        $('.error').remove();
            var errr=0;
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


$('#btn_banner').click(function(){
        $('.error').remove();
            var errr=0;
      if($('#title').val()=='')
      {
         $('#title').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Title</span>');
         $('#title').focus();
         return false;
      }
      else if($('#appimage').val()=='')
      {
         $('#appimage').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select App Image</span>');
         $('#appimage').focus();
         return false;
      }
      else{
        if($("#banner_type").val()==""){
            $('#banner_type').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Banner Type</span>');
             $('#banner_type').focus();
             return false;
        }
        else if($("#banner_type").val()=="category"){
            if($('#category_id').val()==""){
                $('#category_id').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Category</span>');
                 $('#category_id').focus();
                 return false;
            }
            
        }
        else if($("#banner_type").val()=="doctor"){
            if($('#doctor_id').val()==""){
                $('#doctor_id').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Doctor</span>');
                 $('#doctor_id').focus();
                 return false;
            }
            
        }
      }
     
 });
</script>
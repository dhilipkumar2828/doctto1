<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= $page_title ?></h5>
                <div class="ibox-tools">
                   <a href="<?= base_url() ?>admin/ads">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                </div>
            </div>
            <div class="ibox-content">
                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/ads/insert">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Title</label>
                        <div class="col-sm-10">
                            <input type="text" name="title" id="title" class="form-control">
                        </div>
                    </div>

                   
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Image</label>
                        <div class="col-sm-10">
                            <input type="file" name="appimage" id="appimage" class="form-control" required>
                             <span class="help-block m-b-none" style="color:red;">App Image Width : 900px and height : 400px</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Banner Type</label>
                        <div class="col-sm-10">
                            <select name="banner_type" id="banner_type" onchange="check_type(this)" class="form-control">
                                <option value="">-- Select banner type -- </option>
                                <option value="category">Symptoms</option>
                                <option value="doctor">Doctors</option>
                            </select>
                           
                        </div>
                    </div>

                    <div class="form-group" style="display: none;" id="category_section">
                        <label class="col-sm-2 control-label">Symptoms</label>
                        <div class="col-sm-10">
                            <select name="category_id" id="category_id" class="form-control">
                                <option value="">-- Select Symptoms -- </option>
                                <?php
                                    foreach ($categories as $row) {
                                        ?>
                                            <option value="<?=$row->id?>"><?=$row->name?></option>
                                        <?php
                                    }
                                ?>
                                
                                
                            </select>
                           
                        </div>
                    </div>

                    <div class="form-group" style="display: none;" id="doctor_section">
                        <label class="col-sm-2 control-label">Doctors</label>
                        <div class="col-sm-10">
                            <select name="doctor_id" id="doctor_id" class="form-control">
                                <option value="">-- Select Doctor -- </option>
                                <?php
                                    foreach ($doctors as $row) {
                                        ?>
                                            <option value="<?=$row->id?>"><?=$row->doctor_name?></option>
                                        <?php
                                    }
                                ?>
                                
                                
                            </select>
                           
                        </div>
                    </div>

                  


                  

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" id="btn_banner" type="submit"> <i class="fa fa-plus-circle"></i> Add</button>
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
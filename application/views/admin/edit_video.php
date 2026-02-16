<div class="row">

    <div class="col-lg-12">

        <div class="ibox float-e-margins">

            <div class="ibox-title">

                <h5><?= $title ?></h5>

                <div class="ibox-tools">

                  <a href="<?= base_url() ?>admin/videos">
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

                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/videos/update">

                    <div class="form-group">

                        <label class="col-sm-2 control-label">Video URL</label>

                        <div class="col-sm-10">

                            <input type="hidden" name="id" value="<?= $video->id; ?>" class="form-control">

                            <input type="text" id="name" name="link" value="<?= $video->link; ?>" class="form-control">

                        </div>

                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="status" name="status">
                                    <option value="">Select Status</option>
                                    <option value="1" <?php if($video->status==1){ echo "selected='selected'"; }?>>Active</option>
                                    <option value="0" <?php if($video->status==0){ echo "selected='selected'"; }?>>In Active</option>
                            </select>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label">Priority</label>
                        <div class="col-sm-10">
                            <input type="number" id="priority" name="priority" class="form-control" value="<?php echo $video->priority;?>">
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

  $('#btn_category').click(function(){
        $('.error').remove();
            var errr=0;
      if($('#link').val()=='')
      {
         $('#link').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Video Link</span>');
         $('#link').focus();
         return false;
      }
      
      else if($('#status').val()=='')
      {
         $('#status').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Status</span>');
         $('#status').focus();
         return false;
      }
      else if($('#priority').val()=='')
      {
         $('#priority').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Priority</span>');
         $('#priority').focus();
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
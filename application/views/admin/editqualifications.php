<div class="row">

    <div class="col-lg-12">

        <div class="ibox float-e-margins">

            <div class="ibox-title">

                <h5><?= $title ?></h5>

                <div class="ibox-tools">
                    <a href="<?= base_url() ?>admin/qualifications">
                            <button class="btn btn-primary">BACK</button>
                        </a>


                </div>

            </div>

            <div class="ibox-content">

                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/qualifications/update">
                    
                 
                  <input  type="hidden" name="id"  value="<?=$data->id?>">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Qualification Name</label>
                        <div class="col-sm-10">
                            <input  type="text" name="name" id="name" value="<?=$data->name?>" class="form-control"
                                placeholder="Enter Specilaist In">
                        </div>
                    </div>

                    
                             
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="status" name="status">
                                <option value="" selected="">Select Status</option>
                                    <option value="1" <?=$data->status==1?"selected":""?>>Active</option>
                                   <option value="0"  <?=$data->status==0?"selected":""?>>InActive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">

                        <div class="col-sm-4 col-sm-offset-2">

                            <button class="btn btn-primary" id="btn_banner" type="submit"> <i class="fa fa-floppy-o"></i> Update</button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<script type="text/javascript">
    function getStatus(typ)
    {
        if(typ=='products'){
            document.getElementById("show_shops").style.display = "none";
            document.getElementById("show_products").style.display = "block";
        }
        else if(typ=='shops'){
            document.getElementById("show_shops").style.display = "block";
            document.getElementById("show_products").style.display = "none";
        }
    }
</script>


<script type="text/javascript">

  
 $('#btn_banner').click(function(){
        $('.error').remove();
            var errr=0;
      if($('#name').val()=='')
      {
         $('#name').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Title</span>');
         $('#name').focus();
         return false;
      }
    
      else if($('#status').val()=='')
      {
         $('#status').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Status</span>');
         $('#status').focus();
         return false;
      }
 });
   

</script>
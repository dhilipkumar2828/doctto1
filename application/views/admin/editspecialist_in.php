<div class="row">

    <div class="col-lg-12">

        <div class="ibox float-e-margins">

            <div class="ibox-title">

                <h5><?= $title ?></h5>

                <div class="ibox-tools">
                    <a href="<?= base_url() ?>admin/specialist_in">
                            <button class="btn btn-primary">BACK</button>
                        </a>


                </div>

            </div>

            <div class="ibox-content">

                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/specialist_in/update">
                    
                    
                     <div class="form-group">
                        <label class="col-sm-2 control-label">Select Specialisation</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="specialisation_id" id="specialisation_id" required="">
                                <option value="">Select Specialisation</option>
                                <?php
                                foreach ($specialisation as $value) { 
                                    ?>
                                     <option value="<?php echo $value->id; ?>" <?=$value->id==$specialist_in->specialisation_id?"selected":""?>><?php echo $value->name; ?></option> 
                                <?php } ?>  
                            </select>
                        </div>
                    </div>

                           <div class="form-group">
                        <label class="col-sm-2 control-label">Specialist In</label>
                        <div class="col-sm-10">
                            <input  type="text" name="specialist_in" id="specialist_in" value="<?=$specialist_in->specialist_in?>" class="form-control"
                                placeholder="Enter Specilaist In">
                        </div>
                    </div>

                    
                             
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="status" name="status">
                                <option value="" selected="">Select Status</option>
                                    <option value="active" <?=$specialist_in->status=="active"?"selected":""?>>Active</option>
                                   <option value="inactive"  <?=$specialist_in->status=="inactive"?"selected":""?>>InActive</option>
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
<div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5><?= $title ?></h5> 
                            <div class="ibox-tools">
                                <a href="<?= base_url() ?>admin/vendors_shops/manage_locations?shop_id=<?php echo $shop_id;?>"> 
                                    <button class="btn btn-primary">BACK</button>
                                </a>
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
                        </div> 
                        <div class="ibox-content">
                            <form method="post" class="form-horizontal" action="<?= base_url() ?>admin/pincodes/insert">

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Select State</label> 
                                    <div class="col-sm-10">
                                        <select class="form-control" name="state_id" id="state_id">
                                            <option value="">Select State</option>
                                            <?php 
                                                $qry = $this->db->query("select * from states"); 
                                                $stte_row = $qry->result();
                                                foreach ($stte_row as $value) {
                                            ?>
                                            <option value="<?php echo $value->id; ?>"><?php echo $value->state_name; ?></option>
                                        <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Select City</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="city_id" id="city_id">
                                          <option value="">Select City</option>
                                            
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Pincode</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="pincode" id="pincode" class="form-control" placeholder="Enter Pincode">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Status</label>
                                    <div class="col-sm-10">
                                      <select name="status" id="status" class="form-control">
                                          <option value="1">Active</option>
                                          <option value="0">Inactive</option>
                                      </select>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                <div class="form-group">
                                    <div class="col-sm-4 col-sm-offset-2">                                       
                                        <button class="btn btn-primary" id="add_city" type="submit">Save</button>   
                                    </div>
                                </div>
                            </form>                       
                        </div>
                    </div>
                </div>
            </div>

             <script type="text/javascript">

  
  $('#add_city').click(function(){ 
        $('.error').remove();
            var errr=0;
            
      if($('#state_id').val()=='')
      {
         $('#state_id').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select State</span>');
         $('#state_id').focus();
         return false;
      }
      else if($('#city_id').val()=='')
      {
         $('#city_id').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select City</span>');
         $('#city_id').focus();
         return false;
      }
      else if($('#pincode').val()=='')
      {
         $('#pincode').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Pincode</span>');
         $('#pincode').focus();
         return false;
      }
  
 });



  $(document).ready(function(){
           $('#state_id').change(function(){
            var state_id = $('#state_id').val();
            if(state_id != '')
            {
               $.ajax({
                url:"<?php echo base_url(); ?>admin/locations/getcities",
                method:"POST",
                data:{state_id:state_id},
                success:function(data)
                {
                  //alert(JSON.stringify(data));
                 $('#city_id').html(data);
                }
               });
            }
            
           });

           
        });


</script>
<div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5><?= $title ?></h5>
                            <div class="ibox-tools">
                               
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form method="post" class="form-horizontal" action="<?= base_url() ?>admin/locations/insert/<?php echo $shop_id; ?>">

                              <input type="hidden" name="vendor_id"  placeholder="<?php echo $shop_id; ?>">

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
                                    <label class="col-sm-2 control-label">Select Pincode</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="pincode_id" id="pincode_id">
                                          <option value="">Select Pincode</option>
                                            
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Area</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="area" id="area" class="form-control" placeholder="Enter Area">
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
      else if($('#pincode_id').val()=='')
      {
         $('#pincode_id').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Pincode</span>');
         $('#pincode_id').focus();
         return false;
      }
      else if($('#area').val()=='')
      {
         $('#area').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Area</span>');
         $('#area').focus();
         return false;
      }
  
 });



  $(document).ready(function(){
           $('#state_id').change(function(){
            var state_id = $('#state_id').val();
            var shop_id ='<?php echo $shop_id; ?>';
            if(state_id != '')
            {
               $.ajax({
                url:"<?php echo base_url(); ?>admin/locations/getcities",
                method:"POST",
                data:{state_id:state_id,shop_id:shop_id},
                success:function(data)
                {
                  //alert(JSON.stringify(data));
                 $('#city_id').html(data);
                }
               });
            }
            
           });

            $('#city_id').change(function(){
            var city_id = $('#city_id').val();
            var shop_id = '<?php echo $shop_id; ?>';
            if(city_id != '')
            {
               $.ajax({
                url:"<?php echo base_url(); ?>admin/locations/getpincodes",
                method:"POST",
                data:{city_id:city_id,shop_id:shop_id},
                success:function(data)
                {
                  //alert(JSON.stringify(data));
                 $('#pincode_id').html(data);
                }
               });
            }
            
           });

           
        });


</script>
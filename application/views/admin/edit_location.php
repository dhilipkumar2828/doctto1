<div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5><?= $title ?></h5>
                            <div class="ibox-tools">
                               
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form method="post" class="form-horizontal" action="<?= base_url() ?>admin/locations/update/<?php echo $shop_id; ?>">
                              <input type="hidden" name="lid" value="<?php echo $location->id;?>">
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
                                            <option value="<?php echo $value->id; ?>" <?php if($value->id==$location->state_id){ echo "selected='selected'"; }?>><?php echo $value->state_name; ?></option>
                                        <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Select City</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="city_id" id="city_id">
                                          <option value="">Select City</option>

                                            <?php 
                                                $cit = $this->db->query("select * from cities where state_id='".$location->state_id."'"); 
                                                $cities = $cit->result();
                                                foreach ($cities as $value) {
                                            ?>
                                            <option value="<?php echo $value->id; ?>" <?php if($value->id==$location->city_id){ echo "selected='selected'"; }?>><?php echo $value->city_name; ?></option>
                                        <?php } ?>
                                            
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Select Pincode</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" name="pincode_id" id="pincode_id">
                                          <option value="">Select City</option>

                                            <?php 
                                                $cit = $this->db->query("select * from pincodes where city_id='".$location->city_id."'"); 
                                                $cities = $cit->result();
                                                foreach ($cities as $value) {
                                            ?>
                                            <option value="<?php echo $value->pincode; ?>" <?php if($value->pincode==$location->pincode){ echo "selected='selected'"; }?>><?php echo $value->pincode; ?></option>
                                        <?php } ?>
                                            
                                        </select>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Area</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="area" id="area" class="form-control" placeholder="Enter Area" value="<?php echo $location->area;?>">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Status</label>
                                    <div class="col-sm-10">
                                      <select name="status" id="status" class="form-control">
                                          <option value="1" <?php if($location->status==1){ echo "selected='selected'"; } ?>>Active</option>
                                          <option value="0" <?php if($location->status==0){ echo "selected='selected'"; } ?>>Inactive</option>
                                      </select>
                                    </div>
                                </div>
                                
                                <div class="hr-line-dashed"></div>
                                <div class="form-group">
                                    <div class="col-sm-4 col-sm-offset-2">                                       
                                        <button class="btn btn-primary" id="add_city" type="submit">Update</button>
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
      else if($('#location').val()=='')
      {
         $('#location').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Location</span>');
         $('#location').focus();
         return false;
      }
  
 });



  $(document).ready(function(){
           $('#state_id').change(function(){
            var state_id = $('#state_id').val();
            if(state_id != '')
            {
               $.ajax({
                url:"<?php echo base_url(); ?>vendors/locations/getcities",
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


            $('#city_id').change(function(){
            var city_id = $('#city_id').val();
            if(city_id != '')
            {
               $.ajax({
                url:"<?php echo base_url(); ?>vendors/locations/getpincodes",
                method:"POST",
                data:{city_id:city_id},
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
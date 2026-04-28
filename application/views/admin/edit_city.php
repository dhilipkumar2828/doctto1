<div class="row">
                <div class="col-lg-12">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5><?= $title ?></h5>
                            <div class="ibox-tools">
                                <a href="<?= base_url() ?>admin/cities">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <form method="post" class="form-horizontal" action="<?= base_url() ?>admin/cities/update">

                                
                                 <input type="hidden" name="cid"  value="<?php echo $city_row->id;?>">

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
                                            <option value="<?php echo $value->id; ?>" <?php if($value->id==$city_row->state_id){ echo "selected='selected'"; }?>><?php echo $value->state_name; ?></option>
                                        <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">City Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="city_name" id="city_name" class="form-control" placeholder="Example: City Name" value="<?php echo $city_row->city_name;?>">
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
      else if($('#city_name').val()=='')
      {
         $('#city_name').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter City Name</span>');
         $('#city_name').focus();
         return false;
      }
  
 });

</script>
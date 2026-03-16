<div class="row">

    <div class="col-lg-12">

        <div class="ibox float-e-margins">

            <div class="ibox-title">

                <h5><?= $title ?></h5>

                <div class="ibox-tools">

                  <a href="<?= base_url() ?>admin/doctor_sub_categories">
                            <button class="btn btn-primary">BACK</button>
                        </a>

                </div>

            </div>

            <div class="ibox-content">

                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/doctor_sub_categories/update">

                    <div class="form-group">

                        <label class="col-sm-2 control-label">Category Name</label>

                        <div class="col-sm-10">

                            <select class="form-control" name="cat_id" required >

                                <?php

                                foreach ($categories as $cat) {

                                    ?>

                                    <option value="<?= $cat->id ?>"

                                            <?= $sub_category->cat_id == $cat->id ? 'selected' : '' ?>><?= $cat->name ?></option>

                                            <?php

                                        }

                                        ?>



                            </select>

                        </div>

                    </div>

                    <div class="form-group">

                        <label class="col-sm-2 control-label">Sub Category Name</label>

                        <div class="col-sm-10">

                            <input type="hidden" name="sub_cat_id" value="<?= $sub_category->id ?>" class="form-control">

                            <input type="text" id="sub_category_name" name="sub_category_name" value="<?= $sub_category->sub_category_name ?>" class="form-control">

                        </div>

                    </div>

                    <!-- <div class="form-group">
                        <label class="col-sm-2 control-label">Brands</label>
                        <div class="col-sm-10">
                          <input type="text" id="brand" name="brand" class="form-control" value="<?php echo $sub_category->brand; ?>" required> -->

                            <!-- <select class="form-control" name="brand" required>
                            <?php 
                            $brand = $this->db->query("select * from doctor_sub_categories");
                            $brands = $brand->result();
                            foreach ($brands as $brnd) { ?>
                                    <option value="<?= $brnd->id ?>" <?php if($brnd->id==$sub_category->brand){ echo "selected='selected'"; } ?>><?= $brnd->brand_name; ?></option>
                                    <?php
                                }

                                ?>
                            </select> -->
                        <!-- </div>
                    </div> -->


                 

                    <div class="form-group">

                        <label class="col-sm-2 control-label">Status</label>

                        <div class="col-sm-10" style="margin-top: 6px;">

                            <label class='text-success'>

                                <input type="radio"

                                       name="status"

                                       required="required"

                                       value="1"

                                       data-msg-required="This Status is required" <?= $sub_category->status == 1 ? 'checked' : '' ?>/> Active

                            </label> &nbsp;&nbsp;

                            <label class='text-danger'>

                                <input type="radio"

                                       name="status"

                                       required="required"

                                       value="0"

                                       data-msg-required="This Status is required" <?= $sub_category->status == 0 ? 'checked' : '' ?>/> InActive

                            </label>

                        </div>

                    </div>

                    <div class="hr-line-dashed"></div>

                    <div class="form-group">

                        <div class="col-sm-4 col-sm-offset-2">

                            <button class="btn btn-primary" id="btn_subcategory" type="submit"> <i class="fa fa-plus-circle"></i> Update</button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

<script type="text/javascript">

  $('#btn_subcategory').click(function(){
        $('.error').remove();
            var errr=0;
      if($('#sub_category_name').val()=='')
      {
         $('#sub_category_name').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Sub Category Name</span>');
         $('#sub_category_name').focus();
         return false;
      }
      else if($('#description').val()=='')
      {
         $('#description').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Description</span>');
         $('#description').focus();
         return false;
      }
      else if($('#app_image').val()=='')
      {
         $('#app_image').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select App Image</span>');
         $('#app_image').focus();
         return false;
      }
      else if($('#status').val()=='')
      {
         $('#status').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Status</span>');
         $('#status').focus();
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
<style>
    .shop_title{
        font-size:17px !important;
        color: #f39c5a;
    }
    .bd-example-modal-lg .modal-dialog{
        display: table;
        position: relative;
        margin: 0 auto;
        top: calc(50% - 24px);
    }

    .bd-example-modal-lg .modal-dialog .modal-content{
        /*        background-color: transparent;
                border: none;*/
    }
</style>

<div class="row">
    <div class="col-lg-12">

        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5 class="shop_title"><?= $title ?> - Edit Product</h5>
                <div class="ibox-tools"></div>

                <a href="<?= base_url() ?>admin/products" style="float: right;">
                            <button class="btn btn-primary">Back</button>
                        </a>
            </div>
            <div class="ibox-content" style="background-color: #f3f3f3;">

                <form method="post" class="form-horizontal" enctype="multipart/form-data" action="<?= base_url() ?>admin/inactive_products/update_product/">
                    <input type="hidden" name="shop_id" class="form-control" value="<?= $shop_id ?>">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Product Name: *</label>
                        <div class="col-sm-10">
                          <input type="hidden" name="pid" class="form-control" value="<?php echo $products->id; ?>" >
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo $products->name; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Product Telugu Name: *</label>
                        <div class="col-sm-10">
                          
                            <input type="text" id="product_title_telugu" name="product_title_telugu" class="form-control" value="<?php echo $products->product_title_telugu; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Category Name: *</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="cat_id" id="category" onchange="getSubcategories(this.value)">
                                <option value="">Select Category</option>
                                <?php
                                foreach ($categories as $cat) {
                                    ?>
                                    <option value="<?= $cat->id ?>" <?php if($cat->id==$products->cat_id){ echo "selected='selected'"; }?>><?= $cat->category_name ?></option>
                                    <?php
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sub Category: *</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="sub_categories" name="sub_cat_id" >
                              <option value="">Select Sub Category</option>
                                <?php
                                foreach ($subcategories as $subcat) {
                                    ?>
                                    <option value="<?= $subcat->id ?>" <?php if($subcat->id==$products->sub_cat_id){ echo "selected='selected'"; }?>><?= $subcat->sub_category_name ?></option>
                                    <?php
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Product Description: *</label>
                        <div class="col-sm-10">
                            <textarea rows="10" cols="40" class="form-control" name="description" id="description"><?php echo $products->descp; ?></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Key Features: *</label>
                        <div class="col-sm-10">
                            <textarea rows="10" cols="40" class="form-control" name="key_features" ><?php echo $products->key_features; ?></textarea>
                        </div>
                    </div>

                    

                    <!-- <div class="form-group">
                        <label class="col-sm-2 control-label">Start Selling Date: *</label>
                        <div class="col-sm-10">
                           <input type="text" class="form-control" data-date-format="mm/dd/yyyy" name="selling_date" id="datepicker" value="<?php echo $products->selling_date; ?>">
                        </div>
                    </div> -->



                   
                    <!-- <div class="form-group">
                        <label class="col-sm-2 control-label">Tags : *</label>
                        <div class="col-sm-10">
                            <textarea rows="5" cols="40" class="form-control" name="product_tags" id="product_tags"><?php echo $products->product_tags; ?></textarea>
                        </div>
                    </div>  -->


                    <div class="form-group">
                        <label class="col-sm-2 control-label">Select Product Tags : </label>
                                    <?php
                                      $tags_list = explode(",", $products->product_tags);

                                     $tag = $this->db->query("select * from tags");
                                          $tags = $tag->result();
                                          foreach ($tags as $value) { ?>
                                            <div style="float: left; margin-right: 10px;" >
                                    <b style="float: left;margin-right: 10px;"><?php echo $value->title; ?> : </b> <input type="checkbox" name="product_tags[]" style="height: 15px;" id="product_tags" <?php if(in_array($value->title, $tags_list)){ echo 'checked="checked"'; }?> value="<?php echo $value->title; ?>">
                                    </div>
                                  <?php } ?>
                    </div>

                   <!--  <div class="form-group">
                        <label class="col-sm-2 control-label">Meta Tag Title: *</label>
                        <div class="col-sm-10">
                            <textarea rows="5" cols="40" class="form-control" name="meta_tag_title" id="meta_tag_title"><?php echo $products->meta_tag_title; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Meta Tag Description: *</label>
                        <div class="col-sm-10">
                            <textarea rows="5" cols="40" class="form-control" name="meta_tag_description" id="meta_tag_description"><?php echo $products->meta_tag_description; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Meta Tag Keywords: *</label>
                        <div class="col-sm-10">
                            <textarea rows="5" cols="40" class="form-control" name="meta_tag_keywords" id="meta_tag_keywords"><?php echo $products->meta_tag_keywords; ?></textarea>
                        </div>
                    </div> -->

                     <div class="form-group">
                        <label class="col-sm-2 control-label">Brands : *</label>
                        <div class="col-sm-10" >
                            <input type="text" name="brand" id="brand" class="form-control" value="<?php echo $products->brand; ?>">

                            <!-- <select name="brand" id="brand" class="form-control js-example-basic-multiple">
                                    <option value="">Select Brand</option>
                                    <?php $tag = $this->db->query("select * from attr_brands");
                                          $tags = $tag->result();
                                          foreach ($tags as $value) { ?>
                                    <option value="<?php echo $value->id; ?>"><?php echo $value->brand_name; ?></option>
                                  <?php } ?>
                            </select> -->
                        </div>
                    </div> 


                    <!-- <div class="form-group">
                        <label class="col-sm-2 control-label">Brands : *</label>
                        <div class="col-sm-10" >
                            <select name="brand" id="brand" class="form-control js-example-basic-multiple">
                                    <option value="">Select Brand</option>
                                    <?php $tag = $this->db->query("select * from attr_brands");
                                          $tags = $tag->result();
                                          foreach ($tags as $value) { ?>
                                    <option value="<?php echo $value->id; ?>" <?php if($value->id==$products->brand){ echo 'selected="selected"'; }?>><?php echo $value->brand_name; ?></option>
                                  <?php } ?>
                            </select>
                        </div>
                    </div>  -->

                      
                  <!--   <div class="form-group">
                        <label class="col-sm-2 control-label">Cancel Available</label>
                        <div class="col-sm-10" style="margin-top: 6px;">
                            <select class="form-control" name="cancel_status" id="cancel_status">
                                    <option value="">Select Cancel Available</option>
                                    <option value="yes" <?php if($products->cancel_status=='yes'){  echo 'selected="selected"'; }?>>Yes</option>
                                    <option value="no" <?php if($products->cancel_status=='no'){  echo 'selected="selected"'; }?>>No</option>
                            </select>
                        </div>
                    </div>
 -->

                    <!-- <div class="form-group">
                        <label class="col-sm-2 control-label">Return Available</label>
                        <div class="col-sm-10" style="margin-top: 6px;">
                            <select class="form-control" name="return_status" id="return_status">
                                    <option value="">Select Return ( or ) Exchange Policy</option>
                                    <option value="yes" <?php if($products->return_status=='yes'){  echo 'selected="selected"'; }?>>Yes</option>
                                    <option value="no" <?php if($products->return_status=='no'){  echo 'selected="selected"'; }?>>No</option>
                            </select>
                        </div>
                    </div>
 -->
                    
                        <!-- <?php if($products->cancel_status=='yes'){?>
                            <div id="noof_days">
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Return Number of Days</label>
                                    <div class="col-sm-6">
                                      <input type="text" name="return_noof_days" id="return_noof_days" class="form-control" value="<?php echo $products->return_noof_days; ?>">
                                    </div>
                                </div>
                            </div>
                    <?php } ?> -->

                  <!--   <div class="form-group">
                        <label class="col-sm-2 control-label">Deal Product</label>
                        <div class="col-sm-10" style="margin-top: 6px;">
                            <select class="form-control" name="deal_product" id="deal_product">
                                    <option value="">Select Deal Product Status</option>
                                    <option value="yes" <?php if($products->top_deal=='yes'){  echo 'selected="selected"'; }?>>Yes</option>
                                    <option value="no" <?php if($products->top_deal=='no'){  echo 'selected="selected"'; }?>>No</option>
                            </select>
                        </div>
                    </div>
                         -->
                    

<!-- 
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Manage Stock</label>
                        <div class="col-sm-10" style="margin-top: 6px;">
                            <select class="form-control" name="manage_stock" id="manage_stock">
                                    <option value="">Select Manage Stock</option>
                                    <option value="yes" <?php if($products->manage_stock=='yes'){  echo 'selected="selected"'; }?>>Yes</option>
                                    <option value="no" <?php if($products->manage_stock=='no'){  echo 'selected="selected"'; }?>>No</option>
                            </select>
                        </div>
                    </div> -->
                    
                    

                    <!-- <div class="form-group">
                        <label class="col-sm-2 control-label">Select Variant Product</label>
                        <div class="col-sm-10" style="margin-top: 6px;">
                            <select class="form-control" name="variant_product" id="variant_product" onchange="getVariantProduct(this.value)">
                                    <option value="">Select Status</option>
                                    <?php if($products->variant_product=='no'){ ?>
                                    <option value="no" <?php if($products->variant_product=='no'){  echo 'selected="selected"'; } ?>>No</option>
                                    <?php }else{ ?>
                                      <option value="yes" <?php if($products->variant_product=='yes'){  echo 'selected="selected"'; }?>>Yes</option>
                                      <option value="no" <?php if($products->variant_product=='no'){  echo 'selected="selected"'; } ?>>No</option> 
                                  <?php } ?>
                            </select>
                        </div>
                    </div>
 -->
                <?php if($products->variant_product=='no'){ ?>

                    <!-- <div id="variantProduct" >
                        <?php $qry = $this->db->query("select * from link_variant where product_id='".$products->id."'");
                              $report = $qry->row();
                               ?>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Price</label>
                            <div class="col-sm-6">
                                <input type="hidden" name="vid" class="form-control" value="<?php echo $report->id; ?>" >
                              <input type="text" name="price" id="price" class="form-control" value="<?php echo $report->price; ?>" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Sale Price</label>
                            <div class="col-sm-6">
                              <input type="text" name="saleprice" id="saleprice" class="form-control" value="<?php echo $report->saleprice; ?>" >
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Stock</label>
                            <div class="col-sm-6">
                              <input type="text" name="stock" id="stock" class="form-control" value="<?php echo $report->stock; ?>" >
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">Upload Product Images</label>
                            <div class="col-sm-6">
                              <input type="file" name="images[]" id="images" multiple="" class="form-control">
                              <p style="color: red;">Select multiple Images <small>( Click Ctrl + Select )</small></p>
                            </div>
                        </div>

                         <div class="form-group" style="margin-left: 250px;">
                        <div class="row">
                            <?php
                            $qry = $this->db->query("select * from product_images where product_id='".$products->id."' and variant_id='".$report->id."'"); 
                                  $result = $qry->result();
                                  foreach ($result as $value) 
                                  {
                               ?>
                            <div style="float: left;">
                                  <a  href="<?= base_url() ?>admin/products/deleteProductImageInEdit/<?php echo $value->id; ?>/<?php echo $products->id; ?>/<?php echo $value->variant_id; ?>" style="color: red;"><i class="fa fa-trash"></i></a>
                                <img src="<?php echo base_url(); ?>uploads/products/<?php echo $value->image; ?>" style="width: 100px; height: 100px;"/>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    </div> -->
                  <?php } ?>

                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Stock Status</label>
                        <div class="col-sm-10" style="margin-top: 6px;">
                            <select class="form-control" name="availabile_stock_status" id="manage_stock">
                                    <option value="">Select Manage Stock</option>
                                    <option value="available" <?php if($products->availabile_stock_status=='available'){  echo 'selected="selected"'; }?>>Available</option>
                                    <option value="sold" <?php if($products->availabile_stock_status=='sold'){  echo 'selected="selected"'; }?>>SOLD OUT</option>
                            </select>
                        </div>
                    </div>
                
                    
                     <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10" style="margin-top: 6px;">
                            <select class="form-control" name="status" id="status">
                                    <option value="">Select Status</option>
                                    <option value="1" <?php if($products->status==1){  echo 'selected="selected"'; }?>>Active</option>
                                    <option value="0" <?php if($products->status==0){  echo 'selected="selected"'; }?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Select Item Type*</label>
                        <div class="col-sm-10" style="margin-top: 6px;">
                            <select class="form-control" name="product_type" id="product_type">
                                    <option value="">Select Select Item Type</option>
                                    <option value="veg" <?php if($products->status==1){  echo 'selected="selected"'; }?>>Veg</option>
                                    <option value="non_veg" <?php if($products->status==0){  echo 'selected="selected"'; }?>>Non-veg</option>
                                     <option value="not_applicable" <?php if($products->status==0){  echo 'selected="selected"'; }?>>Not-Applicable</option>
                            </select>
                        </div>
                    </div>
                    

                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit" id="btn_product" >Save</button>
                        </div>
                    </div>
                    <div class="modal fade bd-example-modal-lg" data-backdrop="static" data-keyboard="false" tabindex="-1">
                        <div class="modal-dialog modal-sm">
                            <div class="modal-content" style="width: 108px;color:#f37a20;text-align: center;margin:auto;">
                                <span class="fa fa-spinner fa-spin fa-3x" style="font-size:80px;"></span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

</div>


<script type="text/javascript">
function getVariantProduct(value)
    {
       if(value=='no')
       {
        document.getElementById("variantProduct").style.display = "block";
       }
       else if(value=='yes')
       {
        document.getElementById("variantProduct").style.display = "none";
       }
    }

  function getProduct(value)
  {
    if(value=='yes')
    {
         document.getElementById("showtax").style.display = "block";
    }
    else
    {
        document.getElementById("showtax").style.display = "none";
    }
  }

  $(document).ready(function () {
      var val = '<?php echo $products->manage_stock; ?>';
    if(val==1)
    {
      document.getElementById("displaystock").style.display = "block";
    }
    else
    {
      document.getElementById("displaystock").style.display = "none";
    }
  });

  function getProducts(value)
  {

    if(value==1)
    {
      document.getElementById("displaystock").style.display = "block";
    }
    else
    {
      document.getElementById("displaystock").style.display = "none";
    }
  }


</script>
<script type="text/javascript">

    /*function getPrice(price)
    {
         $('.error').remove();
            var errr=0;
        var mrp = $('#mrp').val();
        if(mrp<price)
        {
                $('#mrp').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Please enter Less than the mrp price</span>');
                 $('#mrp').focus();
                 return false;
        }
        else
        {
           
        }
    }*/

    $('#btn_product').click(function(){
        $('.error').remove();
            var errr=0;
      if($('#name').val()=='')
      {
         $('#name').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Product Name</span>');
         $('#name').focus();
         return false;
      }
     else if($('#category').val()=='')
      {
         $('#category').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Category</span>');
         $('#category').focus();
         return false;
      }
      else if($('#sub_categories').val()=='')
      {
         $('#sub_categories').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Sub Category</span>');
         $('#sub_categories').focus();
         return false;
      }
     
      else if($('#description').val()=='')
      {
         $('#description').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Product Description</span>');
         $('#description').focus();
         return false;
      }

      else if($('#datepicker').val()=='')
      {
         $('#datepicker').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Selling Date</span>');
         $('#datepicker').focus();
         return false;
      }
      else if($('#product_tags').val()=='')
      {
         $('#product_tags').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Product Tag</span>');
         $('#product_tags').focus();
         return false;
      }
      /*else if($('#brand').val()=='')
      {
         $('#brand').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Brand</span>');
         $('#brand').focus();
         return false;
      }*/
      else if($('#meta_tag_title').val()=='')
      {
         $('#meta_tag_title').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Meta Tag Title</span>');
         $('#meta_tag_title').focus();
         return false;
      }
      else if($('#meta_tag_description').val()=='')
      {
         $('#meta_tag_description').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Meta Tag Description</span>');
         $('#meta_tag_description').focus();
         return false;
      }
      else if($('#meta_tag_keywords').val()=='')
      {
         $('#meta_tag_keywords').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Meta Tag Keywords</span>');
         $('#meta_tag_keywords').focus();
         return false;
      }

      else if($('#tax_class').val()=='')
      {
         $('#tax_class').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Tax</span>');
         $('#tax_class').focus();
         return false;
      }
      else if($('#cancel_status').val()=='')
      {
         $('#cancel_status').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Cancel Available</span>');
         $('#cancel_status').focus();
         return false;
      }
      else if($('#deal_product').val()=='')
      {
         $('#deal_product').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Deal Product</span>');
         $('#deal_product').focus();
         return false;
      }
      
      else if($('#manage_stock').val()=='')
      {
         $('#manage_stock').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Manage Stock</span>');
         $('#manage_stock').focus();
         return false;
      }
      else if($('#status').val()=='')
      {
         $('#status').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Status</span>');
         $('#status').focus();
         return false;
      }
      

 });


 function getSubcategories(cid)
    {
      if(cid != '')
      {
         $.ajax({
          url:"<?php echo base_url(); ?>admin/products/loadSubcategories",
          method:"POST",
          data:{cid:cid},
          success:function(data)
          {
           $('#sub_categories').html(data);
          }
         });
      }
    }
    /*$(document).ready(function () {

        var defaultCatId = $('#category').val();
        loadSubcategories(defaultCatId);

        $('#category').on('change', function () {
            var cat_id = $('#category').val();

            loadSubcategories(cat_id);
            loadFilterGroups(cat_id);
        });

        function loadSubcategories(cat_id) {
            //alert(city);
            // $('.modal').modal('show');
            if(cat_id!='')
            {
              $.get("<?= base_url() ?>api/admin_ajax/vendors/get_sub_categories", "cat_id=" + cat_id,
                    function (response, status, http) {
                        //$('.modal').modal('hide');
                        $('#sub_category').html(response);
                    }, "html");
            }
            
        }

        function loadFilterGroups(cat_id) {
            $.get("<?= base_url() ?>api/admin_ajax/vendors/get_filter_groups", "cat_id=" + cat_id,
                    function (response, status, http) {
                        //$('.modal').modal('hide');
                        $('#filtergroups_container').html(response);
                    }, "html");
        }





    });*/
</script>

<link href="<?= ADMIN_ASSETS_PATH ?>assets/js/jquery-ui.css" rel="stylesheet" type="text/css"/>
  <script src="<?= ADMIN_ASSETS_PATH ?>assets/js/jquery.min.js"></script>
  <script src="<?= ADMIN_ASSETS_PATH ?>assets/js/jquery-ui.min.js"></script>
<script>

  $(document).ready(function() {
    $('#datepicker').datepicker();
     $('#datepicker').datepicker('setDate', '<?php echo date('m/d/Y'); ?>');
  });

  </script>
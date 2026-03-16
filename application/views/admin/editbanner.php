<div class="row">

    <div class="col-lg-12">

        <div class="ibox float-e-margins">

            <div class="ibox-title">

                <h5><?= $title ?></h5>

                <div class="ibox-tools">
                    <a href="<?= base_url() ?>admin/banners">
                            <button class="btn btn-primary">BACK</button>
                        </a>


                </div>

            </div>

            <div class="ibox-content">

                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/banners/update">

                    <div class="form-group">

                        <label class="col-sm-2 control-label">Title</label>

                        <div class="col-sm-10">

                            <input type="hidden" name="id" value="<?= $banners->id; ?>" class="form-control" >

                            <input type="text" name="title" id="title" value="<?= $banners->title; ?>" class="form-control" >

                        </div>

                    </div>



                   

                     <?php

                    if ($banners->app_image) {

                        ?>

                        <div class="form-group">

                            <label class="col-sm-2 control-label">Preview</label>

                            <div class="col-sm-10">

                                <img width="200px" src="<?= base_url() ?>uploads/banners/<?= $banners->app_image ?> "/>

                            </div>

                        </div>

                        <?php

                    }

                    ?>

                    <div class="form-group">



                        <label class="col-sm-2 control-label">App Image</label>

                        <div class="col-sm-10">

                            <input type="file" name="appimage" class="form-control">

                        </div>

                    </div>

                   <div class="form-group">
                        <label class="col-sm-2 control-label">Pincode</label>
                        <div class="col-sm-10">
                            <select name="pincode" id="pincode" class="form-control js-example-basic-multiple">
                                <?php $pincode_qry = $this->db->query("select * from pincodes");
                                      $pincode_result = $pincode_qry->result();
                                      foreach ($pincode_result as $value) 
                                      { ?>
                                <option value="<?php echo $value->pincode; ?>" <?php if($banners->pincode==$value->pincode){ echo 'selected="selected"'; }?>><?php echo $value->pincode; ?></option>
                                <?php } ?>
                            </select>
                           
                        </div>
                    </div>
                    <div class="form-group">
                                <label class="col-sm-2 control-label">Select Type: *</label><br>
                                <div class="col-sm-10">
                                <label>
                                    <input type="radio" name="type" id="type" onclick="getStatus('product')" value="product" <?php if($banners->type=='product'){  echo 'checked="checked"'; } ?>> Products 

                                </label> &nbsp;&nbsp;

                                <label><input type="radio" name="type" id="type" onclick="getStatus('external_url')" value="external_url" <?php if($banners->type=='external_url'){  echo 'checked="checked"'; } ?>> External Link
                                </label> &nbsp;&nbsp;
                            </div>
                    </div>

                    <?php if($banners->type=='external_url'){ ?>
                        <div class="form-group" id="show_link">
                            <label class="col-sm-2 control-label">URL</label>
                            <div class="col-sm-10">
                                <input type="text" name="url" id="url" value="<?= $banners->url; ?>" class="form-control">
                            </div>
                        </div>

                         <div class="form-group" id="show_products" style="display: none;">
                        <label class="col-sm-2 control-label">Products</label>
                        <div class="col-sm-10">
                            <select name="product_id" id="product_id" class="form-control">
                                <?php $pro = $this->db->query("select * from products");
                                      $products = $pro->result();
                                      foreach ($products as $product) 
                                      { ?>
                                    <option value="<?php echo $product->id; ?>" <?php if($banners->product_id==$product->id){ echo 'selected="selected"'; }?>><?php echo $product->name; ?></option>
                                <?php } ?>
                            </select>
                           
                        </div>
                    </div>
                    <?php }else if($banners->type=='product'){ ?>
                        <div class="form-group" id="show_link" style="display: none;">
                            <label class="col-sm-2 control-label">URL</label>
                            <div class="col-sm-10">
                                <input type="text" name="url" id="url" value="<?= $banners->url; ?>" class="form-control">
                            </div>
                        </div>

                        <div class="form-group" id="show_products" >
                        <label class="col-sm-2 control-label">Products</label>
                        <div class="col-sm-10">
                            <select name="product_id" id="product_id" class="form-control">
                                <?php $pro = $this->db->query("select * from products");
                                      $products = $pro->result();
                                      foreach ($products as $product) 
                                      { ?>
                                    <option value="<?php echo $product->id; ?>" <?php if($banners->product_id==$product->id){ echo 'selected="selected"'; }?>><?php echo $product->name; ?></option>
                                <?php } ?>
                            </select>
                           
                        </div>
                    </div>
                    <?php } ?>

                    
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
        if(typ=='product'){
            document.getElementById("show_link").style.display = "none";
            document.getElementById("show_products").style.display = "block";
        }
        else if(typ=='external_url'){
            document.getElementById("show_link").style.display = "block";
            document.getElementById("show_products").style.display = "none";
        }
        
    }
</script>


<script type="text/javascript">

  
  $('#btn_banner').click(function(){
        $('.error').remove();
            var errr=0;
      if($('#title').val()=='')
      {
         $('#title').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Title</span>');
         $('#title').focus();
         return false;
      }
 });
   

</script>
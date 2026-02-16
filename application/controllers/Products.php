<style>
    .product_image{
        width: 100px;
        height: 100px;
        object-fit: scale-down;
        border-radius: 10px;
        margin: 0px 5px;
        border: 1px solid #edecec;
    }
    .shop_title{
        font-size:17px !important;
        color: #f39c5a;
    }
    .mangeImagesGrid{
        border: 1px solid #ebe9e9;
    }
    .manageImagesGridImgView{
        width:100%;
        /*border: 1px solid #e5e5e5;*/
        margin-bottom: 4px;
    }
    .previewImage{
        padding: 3px;
        border: 1px solid #ccc;
        margin:4px;
        width:30%;
        float:left;
    }
</style>
<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">

        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">

                    <h5 class="shop_title"><?= $title ?>'s Products</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>vendors/products/add_product?shop_id=<?= $_SESSION['vendors']['vendor_id'] ?>">
                            <button class="btn btn-primary">+ Add Product</button>
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

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Sub Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Images</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($products as $pr) {
                                    ?>
                                    <tr class="gradeX">
                                        <td><?= $i ?></td>
                                        <td>

                                            <img class="product_image" align="left" src="<?= DEFAULT_IMAGE_PATH ?>" title="Product image">
                                            <?= $pr->name ?>
                                            <p><b>SKU:</b> <?= $pr->sku ?></p>
                                        </td>
                                        <td><?= $pr->category_name ?></td>
                                        <td><?= $pr->sub_category_name ?></td>
                                        <td>
                                            &#8377;<?= $pr->price ?>
                                        </td>
                                        <td>
                                            <!--<button class="btn btn-xs btn-primary StockLogsModal" data-id="<?= $pr->id; ?>">Update Stock - 0</button>-->
                                            <!--<p>Stock - <?= $pr->stock ? $pr->stock : 0 ?></p>-->
                                            <button class="btn btn-xs btn-warning ShowPricesModal" data-id="<?= $pr->id; ?>">Update Price</button>


                                        </td>
                                        <td>

                                            <a href="<?= base_url() ?>vendors/products/product_images/<?php echo $pr->id; ?>">
                                                <button class="btn btn-xs btn-success upload_images"><i class="fa fa-picture-o"></i> Upload Images</button>
                                            </a>

                                            <?php $c=$this->db->query("select * from product_images where product_id='".$pr->id."'"); 
                                                    $num_rows = $c->num_rows(); ?>
                                            <p>Images - <?php echo $num_rows; ?></p>

                                        </td>
                                        <td>
                                            <a href="<?= base_url() ?>vendors/products/edit/<?php echo $pr->id; ?>">
                                                <button class="btn btn-xs btn-info"><i class="fa fa-pencil-square-o"></i>Edit</button>
                                            </a>
                                            <button class="btn btn-xs btn-danger delete_product" data-id="<?= $pr->id; ?>"><i class="fa fa-trash-o"></i> Delete</button>
                                        </td>

                                            <!--Start Upload Images Modal-->
                                                
                                                            <!-- <div class="modal fade" id="exampleModal<?php echo $pr->id; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                              <div class="modal-dialog" role="document">
                                                                <div class="modal-content">
                                                                  <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLabel"> Manage Product Images</h5>
                                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                      <span aria-hidden="true">&times;</span>
                                                                    </button>
                                                                  </div>
                                                                  <form method="post" class="form-horizontal" enctype="multipart/form-data" action="<?= base_url() ?>vendors/products/uploadImages/">
                                                                  <div class="modal-body" id="manageImagesContainer">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="row">
                                                                                    <?php 
                                                                                    $qry = $this->db->query("select * from product_images where product_id='".$pr->id."'"); 
                                                                                          $result = $qry->result();
                                                                                          foreach ($result as $value) 
                                                                                          {
                                                                                       ?>
                                                                                    <div class="col-md-4 mangeImagesGrid">
                                                                                          <a onclick="deleteImage(<?php echo $value->id; ?>)" style="color: red;"><i class="fa fa-trash"></i></a>
                                                                                        <img src="https://colormoon.in/rocketwheel/uploads/products/<?php echo $value->image; ?>" class="manageImagesGridImgView"/>
                                                                                    </div>
                                                                                    <?php } ?>

                                                                                    

                                                                                </div>



                                                                            </div>
                                                                            
                                                                                <div class="col-md-6">
                                                                                    <input type="hidden" name="pid" value="<?= $pr->id; ?>">
                                                                                    <div id="file-list-display" style="max-height:300px;overflow-y: scroll"></div>
                                                                                    <input type="file" name="image" id="browseimages" style="margin: 20% auto 10px auto;">
                                                                                    
                                                                                </div>
                                                                            
                                                                        </div>

                                                                    
                                                                  </div>

                                                                  <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                                    <button class="btn btn-primary" >Upload</button>
                                                                  </div>
                                                                  </form>
                                                                </div>
                                                              </div>
                                                            </div>
 -->                                            <!--Close Upload Images Modal-->
                                                        
                                    </tr>
                                    <?php
                                    $i++;
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>S.NO</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Sub Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Images</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Logs Modal -->
<div id="StockLogsModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="classInfo" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    ×
                </button>

                <h4 class="modal-title" id="classModalLabel">
                    Manage Stock of product
                </h4>



            </div>
            <div class="modal-body" id="manageStockContainer">
                <div style="float:right;margin-top: -10px;margin-bottom: 10px;" >
                    <button class="btn btn-xs btn-primary" id="add_stock">Add Stock</button>&nbsp;&nbsp;
                    <button class="btn btn-xs btn-danger" id="remove_stock">Remove Stock</button>
                </div>
                <table id="classTable" class="table table-bordered">
                    <thead>
                        <tr>
                            <td>S.NO</td>
                            <td>Stock</td>
                            <td>Type</td>
                            <td>Note</td>
                            <td>Balance</td>
                        </tr>
                    </thead>
                    <tbody id="stock_logs">

                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!--Update Stock Modal-->
<div class="modal fade" id="updatestockModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog cascading-modal modal-avatar modal-sm" role="document">
        Content
        <div class="modal-content">

            Body
            <div class="modal-body mb-1">

                <h5 class="mt-1 mb-2">Update Stock</h5>

                <div class="md-form ml-0 mr-0">
                    <input type="text" id="product_stock" class="form-control form-control-sm validate ml-0">
                </div>
                <br>

                <div class="text-center mt-4">
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>&nbsp;&nbsp;
                    <button class="btn btn-primary" id="submit_stock">Update</button>
                </div>
            </div>

        </div>

    </div>
</div>



<!-- Update Prices Modal-->
<style>
    #UpdatePricesModal .form-control{
        margin-bottom: 10px;
    }
</style>
<div id="UpdatePricesModal" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="classInfo" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    ×
                </button>
                <h4 class="modal-title" id="classModalLabel">
                    Add/Update Prices
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">SKU: *</label>
                                        <input type="text" name="sku" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Size: *</label>
                                        <select class="form-control" required>
                                            <option>select option</option>
                                            <option>L</option>
                                            <option>XL</option>
                                            <option>XXL</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Color: *</label>
                                        <select class="form-control" required>
                                            <option>select option</option>
                                            <option>Red</option>
                                            <option>Green</option>
                                            <option>Yellow</option>
                                            <option>Blue</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">MRP: *</label>
                                        <input type="text" name="mrp" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Sale Price: *</label>
                                        <input type="text" name="sale_price" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Manage Stock: *</label>
                                        <select class="form-control" required>
                                            <option>select option</option>
                                            <option>Yes</option>
                                            <option>No</option>

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Stock: *</label>
                                        <input type="text" name="stock" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label">Admin Commission: *</label>
                                        <input type="text" name="admin_commission" class="form-control" value="10" disabled required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <button class="btn btn-primary" style="margin-top: 21px;">Save</button>
                                    </div>
                                </div>
                            </div>
                            <br>

                        </div>
                        <br>


                    </div>
                    <div class="col-md-4">
                        <div id="file-list-display" style="max-height:300px;overflow-y: scroll"></div>
                        <input type="file" multiple id="browseimages" style="margin: 20% auto 10px auto;">
                        <button class="btn btn-primary" style="margin: 20px auto;display: block;width: 50%;">Upload</button>

                    </div>
                </div>
                <div class="row">
                    <table id="classTable" class="table table-bordered">
                        <thead>
                            <tr>
                                <td>S.NO</td>
                                <td>SKU</td>
                                <td>Price</td>
                                <td>Stock</td>
                                <td>Stock Visibility</td>
                                <td>Admin Comm (%)</td>
                                <td>Images</td>
                                <td>Action</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#1</td>
                                <td>123456</td>
                                <td>
                                    <p>Mrp: 1500</p>
                                    <p>Sale: 1299</p>
                                </td>
                                <td>10</td>
                                <td>Yes</td>
                                <td>10%</td>
                                <td><button class="btn btn-xs btn-primary">Images</button></td>
                                <td>
                                    <button class="btn btn-xs btn-primary">Edit</button>
                                    <button class="btn btn-xs btn-primary">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    function deleteImage(img_id)
    {

                if(img_id != '')
                {
                   $.ajax({
                    url:"<?php echo base_url(); ?>vendors/products/deleteImage",
                    method:"POST",
                    data:{img_id:img_id},
                    success:function(data)
                    {

                        alert(JSON.stringify(data));
                        alert("Image delted");
                        //var location = '<?= base_url() ?>vendors/products';
                        //console.log(location);
                        //window.location = location;
                        $('#UploadImagesModal').modal('show');
                     //$('#image_id').show("Image delted");
                    }
                   });
                }
    }
    $(document).ready(function () {

        $('#backNavigation').click(function (e) {
            console.log("log something");
//            window.history.back();
            return false;
        });


        $('.delete_product').on('click', function () {
            var product_id = $(this).attr('data-id');
            console.log(product_id);
            var confirm = window.confirm('Are you sure, want to delete this product ?');
            if (confirm) {
                var location = '<?= base_url() ?>vendors/products/delete_product?product_id=' + product_id;
                console.log(location);
                window.location = location;
            } else {
                console.log('not confirmed');
            }
        })

        $('.StockLogsModal').on('click', function () {
            var product_id = $(this).attr('data-id');
            console.log(product_id);
            $('#StockLogsModal').modal('show');

            $.post("<?= base_url() ?>api/admin_ajax/vendors/get_stock",
                    {
                        product_id: product_id
                    },
                    function (response, status) {
                        console.log("Data: " + response + "\nStatus: " + status);
                        console.log(JSON.stringify(response));

                        if (response['status'] == 'valid') {
//                            $('#manageStockContainer').show();
                            console.log(response['data']);
                            $('#stock_logs').html();
                            var tbody = '';
                            var i = 1;
                            response['data'].forEach(function (obj) {
                                console.log(obj);
                                tbody += ` <tr>
                                        <td>` + i + `</td>
                                        <td>` + obj.stock + `</td>
                                        <td>` + obj.type + `</td>
                                        <td>` + obj.note + `</td>
                                        <td>` + obj.balance + `</td>
                                    </tr>`;
                                i++;

                            });
                            $('#stock_logs').html(tbody);
                        } else {
//                            $('#manageStockContainer').hide();
                        }
                    });
//            $('#product_stock').val(stock);
//            $('#updatestockModal').modal('show')
        });

        $('.upload_images').on('click', function () {
            $('#UploadImagesModal').modal('show');
        });

        var imagesPreview = function (input, placeToInsertImagePreview) {

            if (input.files) {
                var filesAmount = input.files.length;
                for (i = 0; i < filesAmount; i++) {
                    var reader = new FileReader();
                    reader.onload = function (event) {
                        console.log(event);
                        var div = document.createElement('div');
                        div.setAttribute('class', 'previewImage');

                        var img = document.createElement('img');
                        img.src = event.target.result;
                        img.setAttribute('width', '50%');
                        div.appendChild(img);


                        var fileDisplayEl = document.createElement('p');
                        fileDisplayEl.innerHTML = event.target.fileName;
                        div.appendChild(fileDisplayEl);

                        placeToInsertImagePreview.appendChild(div);
                    };
                    console.log(filesAmount);
                    reader.fileName = input.files[i].name;
                    reader.readAsDataURL(input.files[i]);
                }
            }
        };

        $('#browseimages').on('change', function () {
            var filelistdisplay = document.getElementById('file-list-display');
            imagesPreview(this, filelistdisplay);
        });


        $('.ShowPricesModal').on('click', function () {
//            alert('hello');
            $('#UpdatePricesModal').modal('show');
        })






    });
</script>

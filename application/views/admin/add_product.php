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
                <h5 class="shop_title"><?= $title ?> - Add Product</h5>
                <div class="ibox-tools"></div>
            </div>
            <div class="ibox-content" style="background-color: #f3f3f3;">
                <form method="post" class="form-horizontal" enctype="multipart/form-data" action="<?= base_url() ?>admin/products/insert_product/">
                    <input type="hidden" name="shop_id" class="form-control" value="<?= $shop_id ?>" required>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Name: *</label>
                        <div class="col-sm-10">
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Category Name: *</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="cat_id" id="category" required>
                                <option value="">Select Category</option>
                                <?php
                                foreach ($categories as $cat) {
                                    ?>
                                    <option value="<?= $cat->id ?>"><?= $cat->category_name ?></option>
                                    <?php
                                }
                                ?>

                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sub Category: *</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="sub_category" name="sub_cat_id" required>


                            </select>
                        </div>
                    </div>
                    <div id="filtergroups_container">

                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">MRP (&nbsp; &#8377;&nbsp;): *</label>
                        <div class="col-sm-10">
                            <input type="text" name="mrp" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sale Price (&nbsp;&#8377;&nbsp;): *</label>
                        <div class="col-sm-10">
                            <input type="text" name="price" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">SKU:  *</label>
                        <div class="col-sm-10">
                            <input type="text" name="sku" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tax Class: *</label>
                        <div class="col-sm-10">
                            <!--<input type="text" name="tax_class" class="form-control" required>-->
                            <select class="form-control" name="tax_class" required>
                                <option value="taxable_goods">Taxable Goods</option>
                                <option value="incl_tax">Inclusive Tax</option>
                            </select>
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Product Information: *</label>
                        <div class="col-sm-10">
                            <textarea rows="10" cols="40" class="form-control" name="description" required></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Meta Tag Title: *</label>
                        <div class="col-sm-10">
                            <textarea rows="5" cols="40" class="form-control" name="meta_tag_title" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Meta Tag Description: *</label>
                        <div class="col-sm-10">
                            <textarea rows="5" cols="40" class="form-control" name="meta_tag_description" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Meta Tag Keywords: *</label>
                        <div class="col-sm-10">
                            <textarea rows="5" cols="40" class="form-control" name="meta_tag_keywords" required></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Product Tags: *</label>
                        <div class="col-sm-10">
                            <input type="text" name="product_tags" class="form-control" required>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10" style="margin-top: 6px;">
                            <label class='text-success'>
                                <input type="radio"
                                       name="status"
                                       required="required"
                                       data-msg-required="This Status is required" value="1" /> Active
                            </label> &nbsp;&nbsp;
                            <label class='text-danger'>
                                <input type="radio"
                                       name="status"
                                       required="required"
                                       data-msg-required="This Status is required" value="0" /> InActive
                            </label>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit">Save</button>
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
    $(document).ready(function () {

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
            $.get("<?= base_url() ?>api/admin_ajax/admin/get_sub_categories", "cat_id=" + cat_id,
                    function (response, status, http) {
                        //$('.modal').modal('hide');
                        $('#sub_category').html(response);
                    }, "html");
        }

        function loadFilterGroups(cat_id) {
            $.get("<?= base_url() ?>api/admin_ajax/admin/get_filter_groups", "cat_id=" + cat_id,
                    function (response, status, http) {
                        //$('.modal').modal('hide');
                        $('#filtergroups_container').html(response);
                    }, "html");
        }





    });
</script>

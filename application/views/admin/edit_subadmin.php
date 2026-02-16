<div class="row">

                <div class="col-lg-12">

                    <div class="ibox float-e-margins">

                        <div class="ibox-title">

                            <h5><?= $title ?></h5>

                            <div class="ibox-tools">
                                <a href="<?= base_url() ?>admin/sub_admin">
                            <button class="btn btn-primary">BACK</button>
                        </a>

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

                        </div>

                        <div class="ibox-content">
                            <form method="post" class="form-horizontal" action="<?= base_url() ?>admin/sub_admin/update">

                                <div class="form-group">

                                    <label class="col-sm-2 control-label">User Name</label>

                                    <div class="col-sm-10">
                                         <input type="hidden" name="admin_id" class="form-control" value="<?php echo $sub_admin_row_data->id; ?>">

                                        <input type="text" name="username" id="username" placeholder="User Name " class="form-control" value="<?php echo $sub_admin_row_data->username; ?>">

                                    </div>

                                </div>
                                <!-- <div class="form-group">

                                    <label class="col-sm-2 control-label">Password</label>

                                    <div class="col-sm-10">

                                        <input type="text" name="password" id="password" placeholder="Password" class="form-control">

                                    </div>

                                </div> -->

                                <div class="form-group">

                                    <label class="col-sm-2 control-label">Login Status</label>
                                    <div class="col-sm-10">
                                        <select name="login_status" id="login_status" class="form-control">
                                            <option value="1" <?php if($sub_admin_row_data->login_status==1){ echo "selected='selected'"; }?>>Active</option>
                                            <option value="0" <?php if($sub_admin_row_data->login_status==0){ echo "selected='selected'"; }?>>In Active</option>
                                        </select>
                                    </div>
                                </div>

            
                <div  class="form-group"> 
                <label class="col-sm-2 control-label">User Permissions</label>

                <?php 
                 $userpermissions  = $sub_admin_row_data->permissions; 
                 $permissions = explode(",", $userpermissions);
                ?>
                    <div class="col-sm-10">
                        <div  class="form-group col-md-12">
                        <input type="checkbox" id="dashboard" name="user_permission[]" value="dashboard" <?php if (in_array("dashboard", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="dashboard"> Dashboard</label>
                        </div>
                         
                    
                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="state" name="user_permission[]" value="state" <?php if (in_array("state", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="state"> State</label>
                    </div>
                        <div  class="form-group col-md-12"> 
                            <div  class="form-group col-md-1">
                            </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="add_state" name="user_permission[]" value="add_state" <?php if (in_array("add_state", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_state"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_state" name="user_permission[]" value="edit_state" <?php if (in_array("edit_state", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_state"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_state" name="user_permission[]" value="delete_state" <?php if (in_array("delete_state", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_state"> Delete</label>
                                    </div>
                        </div>
                                

                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="city" name="user_permission[]" value="city" <?php if (in_array("city", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="city"> City</label>
                        </div>
                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_city" name="user_permission[]" value="add_city" <?php if (in_array("add_city", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_city"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_city" name="user_permission[]" value="edit_city" <?php if (in_array("edit_city", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_city"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_city" name="user_permission[]" value="delete_city" <?php if (in_array("delete_city", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_city"> Delete</label>
                                    </div>
                        </div>



                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="pincode" name="user_permission[]" value="pincode" <?php if (in_array("pincode", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="pincode"> Pincode</label>
                        </div>
                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_pincode" name="user_permission[]" value="add_pincode" <?php if (in_array("add_pincode", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_pincode"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_pincode" name="user_permission[]" value="edit_pincode" <?php if (in_array("edit_pincode", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_pincode"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_pincode" name="user_permission[]" value="delete_pincode" <?php if (in_array("delete_pincode", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_pincode"> Delete</label>
                                    </div>
                        </div>

                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="area" name="user_permission[]" value="area" <?php if (in_array("delete_pincode", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="area"> Area</label>
                        </div>
                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_area" name="user_permission[]" value="add_area" <?php if (in_array("add_area", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_area"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_area" name="user_permission[]" value="edit_area" <?php if (in_array("edit_area", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_area"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_area" name="user_permission[]" value="delete_area" <?php if (in_array("delete_area", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_area"> Delete</label>
                                    </div>
                        </div>

                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="banners" name="user_permission[]" value="banners" <?php if (in_array("delete_area", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="banners"> Banners</label>
                        </div>
                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_banner" name="user_permission[]" value="add_banner" <?php if (in_array("add_banner", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_banner"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_banner" name="user_permission[]" value="edit_banner" <?php if (in_array("edit_banner", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_banner"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_banner" name="user_permission[]" value="delete_banner" <?php if (in_array("delete_banner", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_banner"> Delete</label>
                                    </div>
                        </div>

                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="categories" name="user_permission[]" value="categories" <?php if (in_array("categories", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="categories"> Categories</label>
                        </div>

                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_category" name="user_permission[]" value="add_category" <?php if (in_array("add_category", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_category"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_category" name="user_permission[]" value="edit_category" <?php if (in_array("edit_category", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_category"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_category" name="user_permission[]" value="delete_category" <?php if (in_array("delete_category", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_category"> Delete</label>
                                    </div>
                        </div>

                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="sub_categories" name="user_permission[]" value="sub_categories" <?php if (in_array("sub_categories", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="sub_categories"> Sub Categories</label>
                        </div>

                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_sub_categories" name="user_permission[]" value="add_sub_categories" <?php if (in_array("add_sub_categories", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_sub_categories"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_sub_categories" name="user_permission[]" value="edit_sub_categories" <?php if (in_array("edit_sub_categories", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_sub_categories"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_sub_categories" name="user_permission[]" value="delete_sub_categories" <?php if (in_array("delete_sub_categories", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_sub_categories"> Delete</label>
                                    </div>
                        </div>


                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="attribute_types" name="user_permission[]" value="attribute_types" <?php if (in_array("attribute_types", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="attribute_types"> Attribute Types</label>
                        </div>
                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_attribute_types" name="user_permission[]" value="add_attribute_types" <?php if (in_array("add_attribute_types", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_attribute_types"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_attribute_types" name="user_permission[]" value="edit_attribute_types" <?php if (in_array("edit_attribute_types", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_attribute_types"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_attribute_types" name="user_permission[]" value="delete_attribute_types" <?php if (in_array("delete_attribute_types", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_attribute_types"> Delete</label>
                                    </div>
                        </div>


                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="manage_attributes" name="user_permission[]" value="manage_attributes" <?php if (in_array("delete_attribute_types", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="manage_attributes"> Manage Attributes</label>
                        </div>
                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_manage_attributes" name="user_permission[]" value="add_manage_attributes" <?php if (in_array("add_manage_attributes", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_manage_attributes"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_manage_attributes" name="user_permission[]" value="edit_manage_attributes" <?php if (in_array("edit_manage_attributes", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_manage_attributes"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_manage_attributes" name="user_permission[]" value="delete_manage_attributes" <?php if (in_array("delete_manage_attributes", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_manage_attributes"> Delete</label>
                                    </div>
                        </div>


                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="brands" name="user_permission[]" value="brands" <?php if (in_array("brands", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="brands"> Brands</label>
                        </div>

                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_brands" name="user_permission[]" value="add_brands" <?php if (in_array("add_brands", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_brands"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_brands" name="user_permission[]" value="edit_brands" <?php if (in_array("edit_brands", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_brands"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_brands" name="user_permission[]" value="delete_brands" <?php if (in_array("delete_brands", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_brands"> Delete</label>
                                    </div>
                        </div>


                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="tags" name="user_permission[]" value="tags" <?php if (in_array("tags", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="tags"> Tags</label>
                        </div>
                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_tags" name="user_permission[]" value="add_tags" <?php if (in_array("add_tags", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_tags"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_tags" name="user_permission[]" value="edit_tags" <?php if (in_array("edit_tags", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_tags"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_tags" name="user_permission[]" value="delete_tags" <?php if (in_array("delete_tags", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_tags"> Delete</label>
                                    </div>
                        </div>


                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="sub_admins" name="user_permission[]" value="sub_admins" <?php if (in_array("sub_admins", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="sub_admins"> Sub Admins</label>
                        </div>

                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_sub_admins" name="user_permission[]" value="add_sub_admins" <?php if (in_array("add_sub_admins", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_sub_admins"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_sub_admins" name="user_permission[]" value="edit_sub_admins" <?php if (in_array("edit_sub_admins", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_sub_admins"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_sub_admins" name="user_permission[]" value="delete_sub_admins" <?php if (in_array("delete_sub_admins", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_sub_admins"> Delete</label>
                                    </div>
                        </div>


                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="active_products" name="user_permission[]" value="active_products" <?php if (in_array("active_products", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="active_products"> Active Products</label>
                        </div>

                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="status_active_products" name="user_permission[]" value="status_active_products" <?php if (in_array("status_active_products", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="status_active_products"> Status</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_active_products" name="user_permission[]" value="edit_active_products" <?php if (in_array("edit_active_products", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_active_products"> Edit</label>
                                    </div>
                                    
                        </div>


                        <div  class="form-group col-md-12"> 
                        
                        <input type="checkbox" id="inactive_products" name="user_permission[]" value="inactive_products" <?php if (in_array("inactive_products", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="inactive_products"> Inactive Products</label>
                        </div>
                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="status_inactive_products" name="user_permission[]" value="status_inactive_products" <?php if (in_array("status_inactive_products", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="status_inactive_products"> Status</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_inactive_products" name="user_permission[]" value="edit_inactive_products" <?php if (in_array("edit_inactive_products", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_inactive_products"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_inactive_products" name="user_permission[]" value="delete_inactive_products" <?php if (in_array("delete_inactive_products", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_inactive_products"> Edit</label>
                                    </div>
                                    
                        </div>

                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="coupons" name="user_permission[]" value="coupons" <?php if (in_array("inactive_products", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="coupons"> Coupon Codes</label>
                        </div>

                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_coupons" name="user_permission[]" value="add_coupons" <?php if (in_array("add_coupons", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_coupons"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_coupons" name="user_permission[]" value="edit_coupons" <?php if (in_array("edit_coupons", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_coupons"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_coupons" name="user_permission[]" value="delete_coupons" <?php if (in_array("delete_coupons", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_coupons"> Delete</label>
                                    </div>
                        </div>


                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="cash_coupons" name="user_permission[]" value="cash_coupons" <?php if (in_array("cash_coupons", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="cash_coupons"> Cash Codes</label>
                        </div>

                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_cash_coupons" name="user_permission[]" value="add_cash_coupons" <?php if (in_array("add_cash_coupons", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_cash_coupons"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_cash_coupons" name="user_permission[]" value="edit_cash_coupons" <?php if (in_array("edit_cash_coupons", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_cash_coupons"> Edit</label>
                                    </div>
                                    <!-- <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_cash_coupons" name="user_permission[]" value="delete_cash_coupons" <?php if (in_array("delete_cash_coupons", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_cash_coupons"> Delete</label>
                                    </div> -->
                        </div>


                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="vendor_banners" name="user_permission[]" value="vendor_banners" <?php if (in_array("vendor_banners", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="vendor_banners"> Vendor Banners</label>
                        </div>

                         <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="status_vendor_banners" name="user_permission[]" value="status_vendor_banners" <?php if (in_array("status_vendor_banners", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="status_vendor_banners"> Status</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_vendor_banners" name="user_permission[]" value="edit_vendor_banners" <?php if (in_array("edit_vendor_banners", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_vendor_banners"> Delet</label>
                                    </div>
                                    
                        </div>



                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="orders" name="user_permission[]" value="orders" <?php if (in_array("orders", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="orders"> Orders</label>
                        </div>


                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="active_vendors" name="user_permission[]" value="active_vendors" <?php if (in_array("active_vendors", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="active_vendors"> Active Vendors</label>
                        </div>

                         <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_active_vendors" name="user_permission[]" value="add_active_vendors" <?php if (in_array("add_active_vendors", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_active_vendors"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_active_vendors" name="user_permission[]" value="edit_active_vendors" <?php if (in_array("edit_active_vendors", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_active_vendors"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-4">
                                        <input type="checkbox" id="category_active_vendors" name="user_permission[]" value="category_active_vendors" <?php if (in_array("category_active_vendors", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="category_active_vendors"> Manage Categories</label>
                                    </div>
                                    <div  class="form-group col-md-3">
                                        <input type="checkbox" id="vendor_active_vendors" name="user_permission[]" value="vendor_active_vendors" <?php if (in_array("vendor_active_vendors", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="vendor_active_vendors"> Manage Vendor</label>
                                    </div>
                        </div>



                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="inactive_vendors" name="user_permission[]" value="inactive_vendors" <?php if (in_array("vendor_active_vendors", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="inactive_vendors"> Inactive Vendors</label>
                        </div>

                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-1">
                                        <input type="checkbox" id="add_inactive_vendors" name="user_permission[]" value="add_inactive_vendors" <?php if (in_array("add_inactive_vendors", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_inactive_vendors"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="edit_inactive_vendors" name="user_permission[]" value="edit_inactive_vendors" <?php if (in_array("edit_inactive_vendors", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="edit_inactive_vendors"> Edit</label>
                                    </div>
                                    <div  class="form-group col-md-3">
                                        <input type="checkbox" id="category_inactive_vendors" name="user_permission[]" value="category_inactive_vendors" <?php if (in_array("category_inactive_vendors", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="category_inactive_vendors"> Manage Categories</label>
                                    </div>
                                    <div  class="form-group col-md-3">
                                        <input type="checkbox" id="vendor_inactive_vendors" name="user_permission[]" value="vendor_inactive_vendors" <?php if (in_array("vendor_inactive_vendors", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="vendor_inactive_vendors"> Manage Vendor</label>
                                    </div>
                                     <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_inactive_vendors" name="user_permission[]" value="delete_inactive_vendors" <?php if (in_array("delete_inactive_vendors", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_inactive_vendors"> Delete</label>
                                    </div>
                        </div>


                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="delivery_boys" name="user_permission[]" value="delivery_boys" <?php if (in_array("delivery_boys", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="delivery_boys"> Delivery Boys</label>
                        </div>

                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="add_delivery_boys" name="user_permission[]" value="add_delivery_boys" <?php if (in_array("add_delivery_boys", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="add_delivery_boys"> Add</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_delivery_boys" name="user_permission[]" value="delete_delivery_boys" <?php if (in_array("delete_delivery_boys", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_delivery_boys"> Delete</label>
                                    </div>
                        </div>

                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="settlements" name="user_permission[]" value="settlements" <?php if (in_array("settlements", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="settlements"> Settlements</label>
                        </div>

                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="users" name="user_permission[]" value="users" <?php if (in_array("users", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="users"> Users</label>
                        </div>
                        <div class="form-group col-md-12"> 
                                    <div class="form-group col-md-1">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <input type="checkbox" id="view_users" name="user_permission[]" value="view_users" <?php if (in_array("view_users", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="view_users"> View</label>
                                    </div>
                                    <div  class="form-group col-md-2">
                                        <input type="checkbox" id="delete_users" name="user_permission[]" value="delete_users" <?php if (in_array("delete_users", $permissions)){ echo "checked='checked'"; } ?>>
                                        <label for="delete_users"> Delete</label>
                                    </div>
                        </div>

                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="become_a_vendors" name="user_permission[]" value="become_a_vendors" <?php if (in_array("become_a_vendors", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="become_a_vendors"> Become a vendors</label>
                        </div>


                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="notifications" name="user_permission[]" value="notifications" <?php if (in_array("notifications", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="notifications"> Notifications</label>
                        </div>

                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="push_notifications" name="user_permission[]" value="push_notifications" <?php if (in_array("push_notifications", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="push_notifications"> Push Notifications</label>
                        </div>

                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="transactions" name="user_permission[]" value="transactions" <?php if (in_array("transactions", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="transactions"> Transactions</label>
                        </div>

                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="cms_pages" name="user_permission[]" value="cms_pages" <?php if (in_array("cms_pages", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="cms_pages"> CMS Pages</label>
                        </div>

                        <div  class="form-group col-md-12"> 
                        <input type="checkbox" id="settings" name="user_permission[]" value="settings" <?php if (in_array("settings", $permissions)){ echo "checked='checked'"; } ?>>
                        <label for="settings"> Settings</label>
                        </div>





                    
                        
                    </div>
                </div>
               



                                <div class="hr-line-dashed"></div>

                                <div class="form-group">

                                    <div class="col-sm-4 col-sm-offset-2">                                       

                                        <button class="btn btn-primary" id="add_subadmin" type="submit">Save</button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

            <script type="text/javascript">

  
  $('#add_subadmin').click(function(){
        $('.error').remove();
            var errr=0;

      if($('#username').val()=='')
      {
         $('#username').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Username</span>');
         $('#username').focus();
         return false;
      }
      else if($('#password').val()=='')
      {
         $('#password').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Password</span>');
         $('#password').focus();
         return false;
      }
  
 });

</script>

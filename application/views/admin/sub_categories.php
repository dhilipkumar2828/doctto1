<style>
    .cat_image{
        width: 100px;
        height: 100px;
        object-fit: scale-down;
    }
</style>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Sub Categories</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                        <?php 
                        $user_type = $_SESSION['admin_login']['user_type']; 
                        if($user_type=='subadmin'){ 
                                $admin_id = $_SESSION['admin_login']['id']; 
                                $adm_qry = $this->db->query("select * from sub_admin where id='".$admin_id."'");
                                $adm_row=$adm_qry->row();

                                $userpermissions  = $adm_row->permissions; 
                                $permissions = explode(",", $userpermissions);
                        if (in_array("add_sub_categories", $permissions)){ ?>
                        <a href="<?= base_url() ?>admin/sub_categories/add">
                            <button class="btn btn-primary">+ Add Sub Category</button>
                        </a>
                        <?php } }else{ ?>
                            <a href="<?= base_url() ?>admin/sub_categories/add">
                            <button class="btn btn-primary">+ Add Sub Category</button>
                        </a>
                        <?php } ?>
                    </div>
                </div>
                <div class="ibox-content">

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

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Parent Category</th>
                                    <th>Sub Category</th>
                                   
                                    <!-- <th>Brand</th> -->
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($sub_categories as $subcat) {
                                    ?>
                                    <tr class="gradeX">
                                        <td><?= $i ?></td>
                                        <td><?= $subcat->category_name ?></td>
                                        <td><?= $subcat->sub_category_name ?></td>
                                        
                                        <!-- <td>
                                             <?php echo $subcat->brand; ?>
                                        </td> -->
                                        <td>
                                            <?php 
                                            if($subcat->status==1){?>
                                                <button title="This operation is disabled in demo !" disabled="" class="btn btn-xs btn-success">
                                                Active
                                            </button>
                                            <?php }else{ ?>
                                                <button title="This operation is disabled in demo !" disabled="" class="btn btn-xs btn-danger">
                                                In Active
                                            </button>
                                            <?php } ?>
                                        </td>
                                        <td>
                                             <?php
                                        if($user_type=='subadmin'){}else{ ?>
                                            <a href="<?= base_url() ?>admin/sub_categories/edit_subcategory/<?= $subcat->id ?>">
                                                <button title="Disabled" class="btn btn-xs btn-primary">
                                                    Edit
                                                </button>
                                            </a>
                                            <a href="<?= base_url() ?>admin/sub_categories/delete/<?= $subcat->id ?>">
                                                <button title="Delete Sub Catgories" class="btn btn-xs btn-danger">
                                                    Delete
                                                </button>
                                            </a>

                                             <?php } ?>
                                        </td>


                                    </tr>
                                    <?php
                                    $i++;
                                }
                                ?>
                            </tbody>
                            <!-- <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Details</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot> -->
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

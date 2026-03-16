<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>States</h5>
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
                        if (in_array("add_sub_admins", $permissions)){ ?>
                       <a href="<?= base_url() ?>admin/sub_admin/add">
                            <button class="btn btn-primary">+ Add Sub Admin </button>
                        </a>
                        <?php } }else{ ?>
                            <a href="<?= base_url() ?>admin/sub_admin/add">
                            <button class="btn btn-primary">+ Add Sub Admin </button>
                        </a>
                        <?php } ?>
                        
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

                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>User Name</th> 
                                <th>Status</th> 
                                <th>Permissions</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($subadmins as $st) {
                                ?>
                                <tr class="gradeX">
                                    <td><?= $i ?></td>
                                    <td><?= $st->username; ?></td>
                                    <td>
                                        <?php echo $st->login_status == 1 ? 'Active' : 'Inactive';
                                        ?>
                                    </td> 
                                    <td><?= $st->permissions; ?></td>
                                    <td>

                                        <?php
                                        if($user_type=='subadmin'){ 
                                         if (in_array("edit_sub_admins", $permissions)){ ?>
                                             <a href="<?= base_url() ?>admin/tags/add/<?= $tag->id ?>">
                                            <button class="btn btn-xs btn-primary">
                                                Edit
                                            </button>
                                        </a>
                                             <?php } if (in_array("delete_sub_admins", $permissions)){ ?>
                                                <a href="<?= base_url() ?>admin/tags/delete/<?= $tag->id; ?>">
                                            <button class="btn btn-xs btn-danger delete_product" data-id="<?= $v['id']; ?>" onclick="if(!confirm('Are you sure you want to delete this row?')) return false;"><i class="fa fa-trash-o"></i> Delete</button></a>
                                            
                                             <?php } }else{ ?>
                                                
                                            <a href="<?= base_url() ?>admin/sub_admin/edit/<?= $st->id ?>"><button class="btn btn-xs btn-primary">Edit</button></a>

                                            <a href="<?= base_url() ?>admin/sub_admin/delete/<?= $st->id; ?>"><button class="btn btn-xs btn-danger" onclick="if(!confirm('Are you sure you want to delete this State?')) return false;">Delete</button></a>

                                             <?php } ?>



                                          
                                    </td>

                                </tr>
                                <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</div>


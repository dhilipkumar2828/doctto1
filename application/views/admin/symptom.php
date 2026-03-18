<style>
    .cat_image{
        width: 100px;
        height: 100px;
        object-fit: scale-down;
        border-radius: 10px;
        margin: 0px 5px;
    }
</style>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?php echo $title; ?></h5>
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
                        if (in_array("add_doctor_category", $permissions)){ ?>
                        
                        <a href="<?= base_url() ?>admin/symptom/add">
                            <button class="btn btn-primary">+ Add <?php echo $title; ?></button>
                        </a>
                        <?php } }else{ ?>
                            
                        <a href="<?= base_url() ?>admin/symptom/add">
                            <button class="btn btn-primary">+ Add <?php echo $title; ?></button>
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

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Image</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($symptoms as $v) {
                                    ?>
                                    <tr class="gradeX">
                                        <td><?= $i ?></td>
                                        <td><?= $v->name ?></td>
                                        <td>
                                            <?php if($v->image!=''){ ?>
                                            <a href="<?= base_url() ?>uploads/doctor_categories/<?= $v->image ?>" target="_blank"><img class="cat_image" align="left" src="<?= base_url() ?>uploads/doctor_categories/<?= $v->image ?>" title="category image"></a>
                                            <?php } ?></td>
                                        <td>
                                            <?php 
                                            if($v->status==1){?>
                                                <button title="This operation is disabled in demo !" disabled="" class="btn btn-xs btn-success">
                                                Active
                                            </button>
                                            <?php }else{ ?>
                                                <button title="This operation is disabled in demo !" disabled="" class="btn btn-xs btn-danger">
                                                In Active
                                            </button>
                                            <?php } ?>
                                            
                                        </td>

                                        <td><?php echo $v->priority; ?></td>
                                        <td><?php
                                        if($user_type=='subadmin'){ 
                                         if (in_array("edit_category", $permissions)){ ?>

                                            <a href="<?= base_url() ?>admin/symptom/edit/<?= $v->id ?>">
                                                <button title="This operation is disabled in demo !" class="btn btn-xs btn-primary">
                                                    Edit
                                                </button>
                                            </a>
                                            <?php } if (in_array("delete_category", $permissions)){ ?>
                                            <a href="<?= base_url() ?>admin/symptom/delete/<?= $v->id ?>">
                                                <button title="Delete Category" class="btn btn-xs btn-danger" onclick="if(!confirm('Are you sure you want to delete this Symptom?')) return false;">
                                                    Delete
                                                </button>
                                            </a>
                                            <?php } }else{ ?>
                                                <a href="<?= base_url() ?>admin/symptom/edit/<?= $v->id ?>">
                                                <button title="This operation is disabled in demo !" class="btn btn-xs btn-primary">
                                                    Edit
                                                </button>
                                            </a>

                                            <a href="<?= base_url() ?>admin/symptom/delete/<?= $v->id ?>">
                                                <button title="Delete Category" class="btn btn-xs btn-danger" onclick="if(!confirm('Are you sure you want to delete this Symptom?')) return false;">
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
                            
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

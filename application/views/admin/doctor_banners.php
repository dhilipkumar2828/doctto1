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
                    <h5>Banners</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>

                        <?php 
                        $user_type = $_SESSION['admin_login']['user_type']; 
                        if($user_type=='subadmin'){}else{ ?>
                             <a href="<?= base_url() ?>admin/doctor_banners/add">
                            <button class="btn btn-primary">+ Add Banner</button>
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
                                     <th>Title</th>
                                     <th>Image</th>
                                     <th>Banner Type</th>
                                     <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($banners as $v) {
                                    ?>
                                    <tr class="gradeX">
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $v->title; ?></td>
                                        <td>
                                            <img class="cat_image" align="left" src="<?php echo base_url(); ?>uploads/doctor_banners/<?php echo $v->app_image; ?>" title="Banner image">
                                        </td>
                                        <td><?php if($v->banner_type=='category'){ echo "Symptom"; }else{ echo $v->banner_type; }?></td>
                                       
                                        <td>
                                            <?php
                                        if($user_type=='subadmin'){}else{ ?>
                                            <a href="<?= base_url() ?>admin/doctor_banners/edit/<?= $v->id ?>">
                                                <button title="This operation is disabled in demo !" class="btn btn-xs btn-primary">
                                                    Edit
                                                </button>
                                            </a>
                                            <a href="<?= base_url() ?>admin/doctor_banners/delete/<?= $v->id ?>">
                                                <button title="Delete Category" class="btn btn-xs btn-danger">
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

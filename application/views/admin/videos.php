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
                        if (in_array("add_videos", $permissions)){ ?>
                        <a href="<?= base_url() ?>admin/videos/add">
                            <button class="btn btn-primary">+ Add <?php echo $title1; ?></button>
                        </a>
                        <?php } }else{ ?>
                            <a href="<?= base_url() ?>admin/videos/add">
                            <button class="btn btn-primary">+ Add <?php echo $title1; ?></button>
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
                                    <th>Video</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($videos as $video) {
                                    ?>
                                    <tr class="gradeX">
                                        <td><?= $i ?></td>
                                        <td>
                                            <iframe width="220" height="100"
                                            src="https://www.youtube.com/embed/tgbNymZ7vqY">
                                            </iframe>
                                        </td>
                                        <td><?=$video->priority?></td>
                                        <td>
                                            <?php 
                                            if($video->status==1){?>
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
                                            <a href="<?= base_url() ?>admin/videos/edit_video/<?= $video->id ?>">
                                                <button title="Disabled" class="btn btn-xs btn-primary">
                                                    Edit
                                                </button>
                                            </a>
                                            <a onclick="return confirm('Are you sure?')" href="<?= base_url() ?>admin/videos/delete/<?= $video->id ?>">
                                                <button title="Delete Video" class="btn btn-xs btn-danger">
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

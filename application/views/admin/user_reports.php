<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>User Reports</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
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
                    <table  class="table table-striped table-bordered table-hover dataTables-example" >
                        <thead>
                            <tr>
                                <th>#</th>
<!--                                <th>User_id</th>-->
                                <th>Name</th>
                                <th>Mobile</th>
                                <th>Subject</th>
                                <th>Message</th>
                                <th>Image</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(count($user_report)>0)
                            {   
                                $i=1;
                            foreach($user_report as $user){
                                ?>
                                <tr>
                                <td><?php echo $i;?></td>
<!--                                <td><?php echo $user->user_id; ?></td>-->
                                <td><?php echo $user->fullname; ?></td>
                                <td><?php echo $user->mobile; ?></td>
                                <td><?php echo $user->subject; ?></td>
                                <td><?php echo $user->message; ?></td>
                                <td><?php
                                    $ex= explode(",",$user->images);
                                    foreach ($ex as $value) 
                                    { ?>
                                        <img src="<?= base_url(); ?>uploads/users/<?= $value; ?>" style="height: 100px; width: 100px;">
                                    <?php } ?></td>
                                <td><?php echo date('Y-m-d',strtotime($user->created_date)); ?>
                                        </td>
                               <td>
                                <?php if($user->status==0){ ?>
                                <a href="<?php echo base_url(); ?>admin/User_reports/complete/<?php echo $user->id; ?>"><button class="btn btn-xs btn-info" onclick="if(!confirm('Do you want to completed this report ?')) return false;"></i> Complete </button> </a>
                                    <?php }else if($user->status==1){ ?>
                                        <a class="btn btn-xs btn-success">Issue Completed</a>
                                    <?php } ?>
                            </td>
                            </tr>
                            <?php $i++; } }else{ ?>
                            <tr>
                                <td colspan="8" style="text-align: center">
                                    <h4>No data Found</h4>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</div>


<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Users</h5>
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
                                <th>Image</th>
                                <th>Full Name</th>
                                <th>Gender</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <!--<th>City</th>-->
                                <!--<th>State</th>-->
                                <!--<th>Pincode</th>-->
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(count($users)>0)
                            {
                                $i=1;
                            foreach($users as $user){ ?>
                                <tr>
                                <td><?php echo $i;?></td>
                                <td><?php if($user->image!=''){ ?><a href="<?php echo base_url(); ?>/uploads/users/<?php echo $user->image; ?>" target="_blank"><img src="<?php echo base_url(); ?>/uploads/users/<?php echo $user->image; ?>" style="width: 80px; height: 80px;"></a><?php } ?></td>
                                <td><?php echo $user->first_name; ?></td>
                                 <td><?php echo $user->gender; ?></td>
                                <td><?php echo $user->email; ?></td>
                                <td><?php echo $user->phone; ?></td>
                                <td><?php if($user->otp_status==0){ echo "Not Verified";}else{ echo "Verified"; } ?></td>
                                <td>

                                        <?php
                                        $user_type = $_SESSION['admin_login']['user_type']; 

                                        if($user_type=='subadmin'){
                                            
                                        }else{ ?>
                                           <a href="<?= base_url() ?>/admin/users/delete/<?php echo $user->id; ?>"  onclick="if(!confirm('Are you sure you want to delete this user?')) return false;" >
                                                <button title="Delete User" class="btn btn-xs btn-danger">
                                                    Delete
                                                </button>
                                            </a>
                                            <a href="<?= base_url() ?>/admin/users/view/<?php echo $user->id; ?>"><button title="View User" class="btn btn-xs btn-success">View</button></a>

                                             <?php } ?>


                                    
                                        </td>
                            </tr>
                            <?php $i++; } }else{ ?>
                            <tr>
                                <td colspan="8" style="text-align: center">
                                    <h4>No Orders Found</h4>
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


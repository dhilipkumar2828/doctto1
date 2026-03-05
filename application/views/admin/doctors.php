<style>

    .shop_image{

        width: 100px;

        height: 100px;

        object-fit: scale-down;

        margin-right:5px;

        border-radius: 10px;

        border: 1px solid #efeded;

    }

    .shop_title{

        font-size:17px !important;

        color: #f39c5a;

    }

</style>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">

        

        <div class="col-lg-12">

            <div class="ibox float-e-margins">



                <div class="ibox-title">

                    <h5 class="shop_title">Doctors </h5>

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
                        if (in_array("add_coupons", $permissions)){ ?>
                        <a href="<?= base_url() ?>admin/doctors/add">
                            <button class="btn btn-primary">+ Add Doctor</button>
                        </a>
                        <?php } }else{ ?>
                            
                         <a href="<?= base_url() ?>admin/doctors/add">
                            <button class="btn btn-primary">+ Add Doctor</button>
                        </a>
                        <?php } ?>




                       

                    </div>

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

                <div class="ibox-content">

                   

                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr class="gradeX">
                                <th>S.No</th>
                                <th>Hospital Name</th>
                                <th>Hospital Image</th>
                                <th>Doctor Name</th>
                                <th>Doctor Image</th>
                                <th>State</th>
                                <th>City</th>
                                <th>Pincode</th>
                                <th>Designation</th>
                                <th>Mobile Number</th>
                                <th>Address</th>
                                <th>Experience</th>
                                <th>Voice Call Consultation Fee</th>
                                <th>Video Call Consultation Fee</th>
                                <th>Chat Consultation Fee</th>
                                <th>About</th>
                                <th>Youtube ChannelID</th>
                                <th>Tags</th>
                                <th>Doctor Show Status</th>
                                <th>Doctor Login Status</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ks = 1;
                            foreach ($doctors as $v) {
                                ?>
                                <tr class="gradeX">
                                    
                                    <td>#<?= $ks; ?></td>
                                    <td><?= $v->hospital_name; ?></td>
                                    <td>
                                        <?php if($v->hospital_image!=''){ ?>
                                        <img src="<?php echo base_url(); ?>uploads/doctors/<?= $v->hospital_image; ?>" style="width: 60px; height: 60px;">
                                        <?php } ?>
                                    </td>
                                    <td><?= $v->doctor_name; ?></td>
                                    <td>
                                        <?php if($v->doctor_image!=''){ ?>
                                        <img src="<?php echo base_url(); ?>uploads/doctors/<?= $v->doctor_image; ?>" style="width: 60px; height: 60px;">
                                        <?php } ?>
                                    </td>
                                    
                                    <td><?php
                                     $qry1 = $this->db->query("select * from states where id='".$v->state."'");
                                     $state_row = $qry1->row();
                                    echo (isset($state_row) && isset($state_row->state_name)) ? $state_row->state_name : 'N/A'; ?>
                                        
                                    </td>

                                    <td><?php
                                    $qry2 = $this->db->query("select * from cities where id='".$v->city."'");
                                     $city_row = $qry2->row();
                                     echo (isset($city_row) && isset($city_row->city_name)) ? $city_row->city_name : 'N/A';?>
                                     </td>

                                    <td><?php
                                    $qry3 = $this->db->query("select * from pincodes where id='".$v->pincode."'");
                                    $pin_row = $qry3->row();
                                    echo (isset($pin_row) && isset($pin_row->pincode)) ? $pin_row->pincode : 'N/A';?></td>
                                    <td>
                                        <?php
                                            $desg = explode(',',$v->designations );
                                            for($i=0; $i<count($desg); $i++){
                                                $designation_row = $this->db->get_where('designations',['id'=>$desg[$i]])->row();
                                                $designation = !empty($designation_row) ? $designation_row->name : 'N/A';
                                                ?>
                                                    <span class="badge"><?=$designation?></span>
                                                <?php
                                            }
                                        ?>
                                        
                                    <td><?= $v->mobile_number; ?></td>
                                    <td><?= $v->address; ?></td>
                                    <td><?= $v->experience; ?></td>
                                    <td><?= $v->voice_call; ?></td>
                                    <td><?= $v->video_call; ?></td>
                                    <td><?= $v->chat_price; ?></td> 
                                    <td><?= $v->aboutus; ?></td>
                                    <td><?= $v->youtube_channel_id; ?></td>
                                    <td><?= $v->tags; ?></td>
                                    <td><?= $v->doctor_show_status; ?></td>
                                    <td><?= $v->doctor_login_status; ?></td>
                                    <td><?= $v->created_date; ?></td>

                                    <td class="text-left">
                                <a href="<?=base_url()?>admin/doctors/edit/<?=$v->id?>" class="btn btn-sm btn-circle btn-success"><i class="fa fa-edit"></i></a>

                               <a href="<?=base_url()?>admin/doctors/delete/<?=$v->id?>"> <button type="button" class="btn btn-sm btn-circle btn-danger delete_item" value="<?=$v->id?>"><i class="fa fa-trash"></i></button></a>


                                <a href="<?= base_url() ?>admin/doctors/manage_doctor_symptoms/<?= $v->id ?>">
                                            <button title="categories" class="btn btn-xs btn-success">
                                                Manage Symptoms
                                            </button>
                                        </a>
                                           <a href="<?= base_url() ?>admin/doctors/doctor_bank_details/<?= $v->id ?>">
                                            <button title="categories" class="btn btn-xs btn-success">
                                                Bank Details
                                            </button>
                                        </a>


                                </td>

                                </tr>

                                <?php

                                    $ks++;
                            }

                            ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>





</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#myTable').DataTable();
        //Delete Confirmation Script starts here
        $(document).on("click",'.delete_item',function(){
            var del_id = $(this).val();
            swal({
                title: "Are you sure?",
                text: "You want to Delete entire data related to this!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            },
            function(){
                window.location= "<?=base_url()?>admin/doctors/delete/" + del_id;
            });
        });
        //Delete Confirmation Script ends here
    });
</script>



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

                    <h5 class="shop_title">Doctors Appointment </h5>

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
                        <!--<a href="<?= base_url() ?>admin/doctors_appointments/add">-->
                        <!--    <button class="btn btn-primary">+ Add Appointment</button>-->
                        <!--</a>-->
                        <?php } }else{ ?>
                            
                        <!-- <a href="<?= base_url() ?>admin/doctors_appointments/add">-->
                        <!--    <button class="btn btn-primary">+ Add Appointment</button> -->
                        <!--</a>-->
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
                                <th>AppointmentID</th>
                                <th>User Name</th>
                                <th>Doctor Name</th>
                                <th>Date</th>
                                <th>Time Slot Name</th>
                                <th>Time Slot Value</th>
                                <th>Pateint Name</th>
                                <th>Patient Mobile Number</th>
                                <th>Patient Age</th>
                                <th>Patient Gender</th>
                                <th>Patient Visiting Purpose</th>
                                <th>Consoltation Fee</th>
                                <th>Doctor Status</th>
                                <th>Created Date</th>
                                 <th>Action</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ks = 1;
                            foreach ($tomorrow_data as $v) {
                                ?>
                                <tr class="gradeX">
                                    
                                    <td><?= $ks; ?></td>
                                    <td><?= $v->id?></td>
                                    <td><?php
                                     $qry = $this->db->query("select * from users where id='".$v->patient_id."'");
                                     $users_row = $qry->row();
                                     echo !empty($users_row) ? $users_row->first_name : ''; ?>      
                                    </td>
                                    <td><?php
                                     $qry = $this->db->query("select * from doctors where id='".$v->doctor_id."'");
                                     $doctors_row = $qry->row();
                                     echo !empty($doctors_row) ? $doctors_row->doctor_name : ''; ?>      
                                    </td>
                                    <td><?= $v->date?></td>
                                    <td><?= $v->time_slot_name?></td>
                                    <td><?= $v->time_slot_value?></td>                                       
                                    <td><?= $v->patient_name; ?></td>
                                    <td><?= $v->patient_mobile; ?></td>
                                    <td><?= $v->patient_age; ?></td>
                                    <td><?= $v->patient_gender?></td>
                                    <td><?= $v->patient_visiting_purpose?></td>
                                    <td><?= $v->consultation_fee?></td>
                                    <td>
                                        <?php 
                                            if($v->doctor_status=='reject')
                                            { ?>
                                                    <p><b>Status:</b> <?= $v->doctor_status; ?></p>
                                                    <p><b>Rejected By:</b> <?= $v->rejected_by; ?></p>
                                                    <p><b>Reason:</b> <?= $v->reason?></p>
                                                    <?php if($v->comments!=''){ ?><p><b>Comment:</b> <?= $v->comments; ?><?php } ?></p>
                                            <?php }else{
                                                echo $v->doctor_status;
                                            }
                                        ?>
                                    </td>
                                    <td><?= $v->created_date; ?></td>

                                     <td class="text-left">
                                        <a href="<?= base_url() ?>admin/doctors_appointments/eprescription/<?= $v->id ?>">
                                            <button title="categories" class="btn btn-xs btn-success">
                                                Prescriptions
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
                window.location= "<?=base_url()?>admin/doctors_appointments/delete/" + del_id;
            });
        });
        //Delete Confirmation Script ends here
    });
</script>



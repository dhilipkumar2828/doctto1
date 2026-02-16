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
                
                  <div class="col-md-12">

                        <div class="col-md-3">
                            <div class="widget style1 blue-bg">
                                <a style="color: #FFF;" href="<?php echo base_url(); ?>admin/doctors_appointments/appointment_status?status=active"><div class="row">
                                 
                                    <div class="col-xs-12 text-center">
                                        <span>Active Appointments</span> 
                                        <?php $active = $this->db->where('doctor_status','active')->count_all_results('doctor_appointments'); ?>
                                        <h2 class="font-bold"><?= $active ? $active : 0 ?></h2>
                                    </div>
                                </div></a>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="widget style1 navy-bg">
                                <a style="color: #FFF;" href="<?php echo base_url(); ?>admin/doctors_appointments/appointment_status?status=accept"><div class="row">
                                    <div class="col-xs-12 text-center">
                                        <span>Accept Appointments</span>
                                        <?php $accept = $this->db->where('doctor_status','accept')->count_all_results('doctor_appointments'); ?>
                                        <h2 class="font-bold"><?= $accept ? $accept : 0 ?></h2>
                                    </div>
                                </div></a>
                            </div>
                        </div>
                        
                         <div class="col-md-3">
                            <div class="widget style1 navy-bg">
                                <a style="color: #FFF;" href="<?php echo base_url(); ?>admin/doctors_appointments/appointment_status?status=reject"><div class="row">
                                    <div class="col-xs-12 text-center">
                                        <span>Rejected Appointments</span>
                                        <?php $reject = $this->db->where('doctor_status','reject')->count_all_results('doctor_appointments'); ?>
                                        <h2 class="font-bold"><?= $reject ? $reject : 0 ?></h2>
                                    </div>
                                </div></a>
                            </div>
                        </div>
                        
                         <div class="col-md-3">
                            <div class="widget style1 navy-bg">
                                <a style="color: #FFF;" href="<?php echo base_url(); ?>admin/doctors_appointments/appointment_status?status=completed"><div class="row">
                                    <div class="col-xs-12 text-center">
                                        <span>Completed Appointments</span>
                                       <?php $completed_ap = $this->db->where('doctor_status','completed')->count_all_results('doctor_appointments'); ?> 
                                        <h2 class="font-bold"><?= $completed_ap ? $completed_ap : 0 ?></h2>
                                    </div>
                                </div></a>
                            </div>
                        </div>
                    
                    </div>

                <div class="ibox-content">

                       <div class="row">
                    <form method="post" class="" enctype="multipart/form-data" action="<?php echo base_url(); ?>admin/doctors_appointments/searchorderdate">
                        <div class="col-md-2">
                            <label>From Date :</label>
                            <input type="date" onchange="setTodate(this)" class="form-control" name="start_date" value="<?php  if ($start_date != '') { echo $start_date; }
                            ?>" required="" oninvalid="this.setCustomValidity('Please Select From Date')">
                        </div>
                        <div class="col-md-2">
                            <label>To Date : </label>
                            <input type="date" class="form-control" id="to-date" name="end_date" value="<?php if ($end_date != '') { echo $end_date;  } ?>" 
                            required="" oninvalid="this.setCustomValidity('Please Select To Date')">
                        </div>
                        <div class="col-md-4">
                       <input type="submit" class="btn btn-success" style="margin-top: 26px;" value="Get Data">
                       <a href="<?= base_url() ?>admin/doctors_appointments" style="margin-top: 26px;" class="btn btn-danger"><i class="fa fa-recycle"></i> Reset </a> 
                        </div>
                    </form>                         
                </div>

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
                                <th>Patient Name</th>
                                <th>Patient Mobile Number</th>
                                <th>Patient Age</th>
                                <th>Patient Gender</th>
                                <th>Patient Visiting Purpose</th>
                                <th>Appointment Type</th>
                                <th>Consultation Fee</th>
                                <th>Doctor Status</th> 
                                <th>Created Date</th>
                                 <th>Action</th> 
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ks = 1;
                            foreach ($appointment as $v) {
                                ?>
                                <tr class="gradeX">
                                    
                                    <td><?= $ks; ?></td>
                                    <td><?= $v->id?></td>
                                    <td><?php
                                     $qry = $this->db->query("select * from users where id='".$v->patient_id."'");
                                     $users_row = $qry->row();
                                     echo $users_row->first_name; ?>      
                                    </td>
                                    <td><?php
                                     $qry = $this->db->query("select * from doctors where id='".$v->doctor_id."'");
                                     $doctors_row = $qry->row();
                                     echo $doctors_row->doctor_name; ?>      
                                    </td>
                                    <td><?= $v->date?></td>
                                    <td><?= $v->time_slot_name?></td>
                                    <td><?= $v->time_slot_value?></td>                                       
                                    <td><?= $v->patient_name; ?></td>
                                    <td><?= $v->patient_mobile; ?></td>
                                    <td><?= $v->patient_age; ?></td>
                                    <td><?= $v->patient_gender?></td>
                                    <td><?= $v->patient_visiting_purpose?></td>   
                                    <td><?= $v->appointment_type?></td>
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
<script>
    function setTodate(ele) {
        var from_date = ele.value;
        $("#to-date").attr('min', from_date);
}
</script>



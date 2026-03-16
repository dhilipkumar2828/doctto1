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

                    <h5 class="shop_title">Doctors Payments </h5>

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
                

        

                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr class="gradeX">
                                <th>S.No</th>
                                <th>AppointmentID</th>
                                <th>Doctor Name</th>
                                <th>Patient Name</th> 
                                <th>Date & Time</th>
                                <th>Consultation Fee</th>
                                <th>Appointment Type</th>
                                <th>Doctor Status</th>
                                <th>Invoice</th>
                                
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
                                     $qry = $this->db->query("select * from doctors where id='".$v->doctor_id."'");
                                     $doctors_row = $qry->row();
                                     echo (!empty($doctors_row) && isset($doctors_row->doctor_name)) ? $doctors_row->doctor_name : 'N/A'; ?>      
                                    </td>
                                    <td><?= $v->patient_name; ?></td>
                                    <td><?= date('d M, Y , h:i A',strtotime($v->created_date))?></td>  
                                    <td><?= $v->consultation_fee?></td>
                                    <td><?= $v->appointment_type?></td>
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
                                    <td><a href="<?= base_url() ?>admin/doctor_payments/view_invoice/<?= $v->id ?>?source=<?= isset($v->source) ? $v->source : 'offline' ?>" target="_blank">  
                            <button class="btn btn-primary">+ view</button>
                        </a></td>

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



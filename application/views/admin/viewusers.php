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

                    <h5 class="shop_title">User Payment Details</h5>

                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/users">
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

                    <table class="table table-striped">
                      
                        <tr>
                            <td>Name</td>
                            <td><?php echo $users->first_name."".$users->last_name;?></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><?php echo $users->email;?></td>
                        </tr>
                         <tr>
                            <td>Phone</td>
                            <td><?php echo $users->phone;?></td>
                        </tr>
                        <tr>
                            <td>User Status</td>
                            <td><?php if($users->otp_status==0){ echo "Not Verified";}else{ echo "Verified"; } ?></td>
                        </tr>
                    </table>
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr class="gradeX">
                                <th>S.No</th>
                                <th>AppointmentID</th>
                                <th>User Name</th>
                                <th>Doctor Name</th>
                                <th>User Mobile Number</th>
                                <th>Appointment Type</th>
                                <th>Consultation Fee</th>
                                <th>Payment Time</th> 
                                
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
                                     echo !empty($users_row) ? $users_row->first_name : ''; ?>      
                                    </td>
                                    <td><?php
                                     $qry = $this->db->query("select * from doctors where id='".$v->doctor_id."'");
                                     $doctors_row = $qry->row();
                                     echo !empty($doctors_row) ? $doctors_row->doctor_name : ''; ?>      
                                    </td>
                                    <td><?= $v->patient_mobile; ?></td>
                                    <td><?= $v->appointment_type; ?></td>
                                    <td><?= $v->consultation_fee?></td> 
                                    <td><?= $v->created_date; ?></td>
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



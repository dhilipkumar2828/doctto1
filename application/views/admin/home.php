<div class="row">

    <div class="col-lg-12">

        <div class="ibox float-e-margins">

            <div class="ibox-title">

                <h5>My Dashboard</h5>

            </div>

            <div class="ibox-content">

                <div class="row">

                    <div class="col-md-12">

                        <h2>Users Statistics</h2><hr>

                    </div>
                    <div class="col-md-12">

                        <div class="col-md-3">
                            <a href="<?php echo base_url(); ?>admin/users">
                                <div class="widget style1 blue-bg">
                                    <div class="row">
                                       <div class="col-xs-12 text-center">
                                            <span>Users </span>
                                            <h2 class="font-bold"><?= $active_total_users ?></h2>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        
                       

                    
                    </div>

                </div>



                



                 <div class="row">

                    <div class="col-md-12">

                        <h2>Doctors Statistics</h2><hr>

                    </div>
                    <div class="col-md-12">

                        <div class="col-md-3">
                            <div class="widget style1 blue-bg">
                                <a style="color: #FFF;" href="<?php echo base_url(); ?>admin/doctors/active"><div class="row">
                                    <?php $active_doctors = $this->db->where(array('doctor_show_status'=>'active'))->get('doctors')->num_rows(); ?>
                                    <div class="col-xs-12 text-center">
                                        <span>Active Doctors</span>
                                        <h2 class="font-bold"><?= $active_doctors ?></h2>
                                    </div>
                                </div></a>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="widget style1 navy-bg">
                                <a style="color: #FFF;" href="<?php echo base_url(); ?>admin/doctors/inactive"><div class="row">
                            <?php $inactive_doctors = $this->db->where(array('doctor_show_status'=>'inactive'))->get('doctors')->num_rows(); ?>
                                    <div class="col-xs-12 text-center">
                                        <span>Inactive Doctors</span>
                                        <h2 class="font-bold"><?= $inactive_doctors ?></h2>
                                    </div>
                                </div></a>
                            </div>
                        </div>

                 
                    
                    </div>

                </div>

                    <div class="row">

                    <div class="col-md-12">

                        <h2>Doctors Bookings And Payments</h2><hr>

                    </div>
                    <div class="col-md-12">


                   <div class="col-md-3">
                            <div class="widget style1 navy-bg">
                            <?php 
                                $offline_count = $this->db->get('doctor_appointments')->num_rows(); 
                                $online_count = $this->db->where('payment_status', 'completed')->get('online_doctor_appointments')->num_rows();
                                $appointment_count = $offline_count + $online_count;
                            ?>
                                <a style="color: #FFF;" href="<?php echo base_url(); ?>admin/doctors_appointments"><div class="row">
                                    <div class="col-xs-12 text-center">
                                        <span>Doctor Appointments</span>
                                        <h2 class="font-bold"><?= $appointment_count ?></h2>
                                    </div>
                                </div></a>
                            </div>
                        </div>

                
                        
                        <div class="col-md-3">
                            <div class="widget style1 navy-bg">
                            <?php 
                                $offline_count = $this->db->get('doctor_appointments')->num_rows(); 
                                $online_count = $this->db->where('payment_status', 'completed')->get('online_doctor_appointments')->num_rows();
                                $total_payments = $offline_count + $online_count;
                            ?>
                                <a style="color: #FFF;" href="<?php echo base_url(); ?>admin/doctor_payments"><div class="row">
                                    <div class="col-xs-12 text-center">
                                        <span>Total Payments</span>
                                        <h2 class="font-bold"><?= $total_payments ?></h2>
                                    </div>
                                </div></a>
                            </div>
                        </div>
                    
                    </div>

                </div>



                 <div class="row" >

                    <div class="col-md-12">

                        <h2> Reports</h2><hr>

                    </div>

                    <div class="col-md-4">
                        <a href="<?php echo base_url(); ?>admin/appointment_daily_reports">
                        <div class="widget style1 navy-bg">

                            <div class="row">

                                <div class="col-xs-12 text-center">

                                    <span>Reports </span>
                                    <?php $appointments = $this->db->where(array('doctor_status'=>'completed'))->get('doctor_appointments')->num_rows(); ?>
                                    <h2 class="font-bold"><?php echo $appointments; ?></h2>

                                </div>

                            </div>

                        </div></a>

                    </div>

                </div>

                



            </div>

        </div>

    </div>

</div>







</div>

<script type="text/javascript">
    $(document).ready(function () {
        setInterval(function () {
            $("#here").load(window.location.href + " #here");
            //alert("hi");
        }, 5000);
    });
</script>


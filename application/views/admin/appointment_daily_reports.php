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

                    <h5 class="shop_title">Reports</h5>

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

                <div class="ibox-content">

                    <form method="post" class="form-horizontal" action="<?php echo base_url(); ?>admin/appointment_daily_reports/datewiseReport">
                        <label>From Date</label>
                        <input type="date" name="start_date" value="<?php if(isset($start_date)){ echo $start_date; }?>" required="">
                        <label>To Date</label>
                        <input type="date" name="end_date" value="<?php if(isset($end_date)){ echo $end_date; }?>" required="">
                        <input type="submit" class="btn btn-primary" value="GET">

                        <a href="<?= base_url() ?>admin/appointment_daily_reports" class="btn btn-danger"><i class="fa fa-recycle"></i> Reset </a> 
                    </form>

                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Doctor ID</th>
                                <th>Doctor Name</th>
                                <th>Appointment ID</th>
                                <th>Consultation Fees</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($appointment_commission as $value) {
                            
                                $vendor = $this->db->where(array('id'=>$value->doctor_id))->get('doctors')->row();
                                if(!empty($vendor)){
                                ?>
                                <tr class="gradeX">
                                        
                                <td><?php echo $i;?></td>
                                <td><?php echo $vendor->id; ?></td>
                                <td><?php echo $vendor->hospital_name." ( <small> ".$vendor->doctor_name." </small> )"; ?></td>
                                <td><?php echo $value->id; ?></td>                            
                                <td><?php  echo $value->consultation_fee; ?></td>
                                <td><?php echo date("d-m-Y",strtotime($value->date)); ?></td>
                                </tr>
                                <?php
                                    $i++;
                                }
                            }

                            ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>





</div>




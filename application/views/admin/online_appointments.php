<?php
$start_date = isset($start_date) ? $start_date : '';
$end_date = isset($end_date) ? $end_date : '';
$completed = isset($completed) ? $completed : 0;
$pending = isset($pending) ? $pending : 0;
$failed = isset($failed) ? $failed : 0;
$appointment = isset($appointment) ? $appointment : array();
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5 style="font-size:17px !important; color: #f39c5a;">Online Appointments (Mobile)</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                    </div>
                </div>

                <div class="col-md-12" style="margin-top: 20px;">
                    <div class="col-md-4">
                        <div class="widget style1 blue-bg">
                            <div class="row">
                                <div class="col-xs-12 text-center">
                                    <span>Completed Payments</span>
                                    <h2 class="font-bold"><?= $completed ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="widget style1 yellow-bg">
                            <div class="row">
                                <div class="col-xs-12 text-center">
                                    <span>Pending Payments</span>
                                    <h2 class="font-bold"><?= $pending ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="widget style1 red-bg">
                            <div class="row">
                                <div class="col-xs-12 text-center">
                                    <span>Failed Payments</span>
                                    <h2 class="font-bold"><?= $failed ?></h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ibox-content">
                    <div class="row" style="margin-bottom: 20px;">
                        <form method="post" action="<?php echo base_url(); ?>admin/doctors_appointments/search_online">
                            <div class="col-md-3">
                                <label>From Date :</label>
                                <input type="date" class="form-control" name="start_date" value="<?php echo $start_date; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label>To Date : </label>
                                <input type="date" class="form-control" name="end_date" value="<?php echo $end_date; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <input type="submit" class="btn btn-success" style="margin-top: 26px;" value="Filter">
                                <a href="<?= base_url() ?>admin/doctors_appointments/online" style="margin-top: 26px;" class="btn btn-danger">Reset</a>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Appointment ID</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Date/Time</th>
                                    <th>Patient Details</th>
                                    <th>Payment Status</th>
                                    <th>Amount</th>
                                    <th>Transaction ID</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if($appointment): ?>
                                    <?php $i=1; foreach($appointment as $v): ?>
                                        <tr>
                                            <td><?= $i++ ?></td>
                                            <td><?= $v->id ?></td>
                                            <td>
                                                <strong><?= $v->user_name ?></strong><br>
                                                <small>ID: <?= $v->patient_id ?></small>
                                            </td>
                                            <td>
                                                <strong><?= $v->doctor_name ?></strong><br>
                                                <small><?= $v->hospital_name ?></small>
                                            </td>
                                            <td>
                                                <?= $v->date ?><br>
                                                <small><?= $v->time_slot_name ?> (<?= $v->time_slot_value ?>)</small>
                                            </td>
                                            <td>
                                                <strong><?= $v->patient_name ?></strong> (<?= $v->patient_age ?>/<?= $v->patient_gender ?>)<br>
                                                <?= $v->patient_mobile ?><br>
                                                <small>Purpose: <?= $v->patient_visiting_purpose ?></small>
                                            </td>
                                            <td>
                                                <?php if($v->payment_status == 'completed'): ?>
                                                    <span class="label label-primary">Completed</span>
                                                <?php elseif($v->payment_status == 'pending'): ?>
                                                    <span class="label label-warning">Pending</span>
                                                <?php else: ?>
                                                    <span class="label label-danger"><?= ucfirst($v->payment_status) ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>₹<?= number_format($v->consultation_fee, 2) ?></td>
                                            <td><?= $v->phonepe_transaction_id ?></td>
                                            <td><?= $v->created_date ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

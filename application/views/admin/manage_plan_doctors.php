<?php
// Ensure $plan is an object for consistent property access
if (isset($plan) && is_array($plan)) {
    $plan = (object) $plan;
}
if (!isset($plan) || !is_object($plan)) {
    $plan = new stdClass();
}

$assigned_doctors = isset($assigned_doctors) && is_array($assigned_doctors) ? $assigned_doctors : [];
$all_plans = isset($all_plans) && is_array($all_plans) ? $all_plans : [];
$available_doctors = isset($available_doctors) && is_array($available_doctors) ? $available_doctors : [];
$current_plan_id = isset($current_plan_id) ? $current_plan_id : NULL;
?>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Manage Doctors for <?= $current_plan_id ? "Plan: " . (isset($plan->name) ? $plan->name : '') : "All Subscription Plans" ?></h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/subscription_plans">
                            <button class="btn btn-primary btn-sm">BACK</button>
                        </a>
                        <a href="<?= base_url() ?>admin/subscription_plans/add_doctors">
                            <button class="btn btn-primary btn-sm">
                                <i class="fa fa-plus"></i> Add Doctor
                            </button>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    <?php if ($current_plan_id): ?>
                    <div class="row m-b-md">
                        <div class="col-md-12">
                            <div class="p-xs b-r-sm bg-muted">
                                <strong>Plan Details:</strong> 
                                <span class="m-l-sm"><i class="fa fa-info-circle"></i> <?= isset($plan->description) ? $plan->description : '' ?></span>
                                <span class="m-l-lg"><i class="fa fa-user-md"></i> Limit: <?= count($assigned_doctors) ?> / <?= isset($plan->max_doctors_allowed) ? $plan->max_doctors_allowed : 0 ?></span>
                                <span class="m-l-lg text-primary"><strong>₹<?= isset($plan->price) ? number_format($plan->price, 2) : '0.00' ?> / Month</strong></span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($this->session->flashdata('success_message'))) { ?>
                        <div class="alert alert-success fade in alert-dismissable">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>Success!</strong> <?= $this->session->flashdata('success_message') ?>
                        </div>
                    <?php } ?>

                    <?php if (!empty($this->session->flashdata('error_message'))) { ?>
                        <div class="alert alert-danger fade in alert-dismissable">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>Error!</strong> <?= $this->session->flashdata('error_message') ?>
                        </div>
                    <?php } ?>

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Doctor / Title</th>
                                    <th>Banner</th>
                                    <th>Plan Name</th>
                                    <th>Hospital Name</th>
                                    <th>Mobile Number</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($assigned_doctors): ?>
                                    <?php $i = 1; foreach ($assigned_doctors as $doctor): ?>
                                        <tr class="gradeX">
                                            <td><?= $i++ ?></td>
                                            <td>
                                                <strong><?= isset($doctor->doctor_name) ? $doctor->doctor_name : '' ?></strong>
                                                <?php if(!empty($doctor->title)): ?>
                                                    <br><small class="text-muted"><?= $doctor->title ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if(!empty($doctor->app_image)): ?>
                                                    <img class="cat_image" src="<?= base_url() ?>uploads/doctor_banners/<?= $doctor->app_image ?>" alt="Banner">
                                                <?php else: ?>
                                                    <span class="text-muted small">No Banner</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><span class="label label-primary"><?= isset($doctor->plan_name) ? $doctor->plan_name : '' ?></span></td>
                                            <td><?= isset($doctor->hospital_name) ? $doctor->hospital_name : '' ?></td>
                                            <td><?= isset($doctor->mobile_number) ? $doctor->mobile_number : '' ?></td>
                                            <td>
                                                <a href="<?= base_url() ?>admin/subscription_plans/remove_doctor_from_manage/<?= isset($doctor->plan_id) ? $doctor->plan_id : '' ?>/<?= isset($doctor->doctor_id) ? $doctor->doctor_id : '' ?>" 
                                                   class="btn btn-xs btn-danger" 
                                                   onclick="return confirm('Are you sure you want to remove this doctor from this plan?')">
                                                    <i class="fa fa-trash"></i> Remove
                                                </a>
                                            </td>
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
<style>
    .cat_image{
        width: 80px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid #ddd;
    }
</style>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.dataTables-example').DataTable({
        pageLength: 10,
        responsive: true,
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'excel', title: 'Plan Doctors'},
            {extend: 'pdf', title: 'Plan Doctors'},
            {extend: 'print',
                customize: function (win){
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            }
        ]
    });

    // Initialize Select2 for modal
    $('#addDoctorModal').on('shown.bs.modal', function () {
        $('.select2-modal').select2({
            dropdownParent: $('#addDoctorModal'),
            width: '100%'
        });
    });

    // Auto-select plan based on doctor subscription (optional helper)
    $('.select2-modal').on('change', function() {
        var planId = $(this).find(':selected').data('plan-id');
        if (planId && !$('#plan-selection-container').hasClass('hide')) {
            $('#target-plan-select').val(planId);
        }
    });
});
</script>

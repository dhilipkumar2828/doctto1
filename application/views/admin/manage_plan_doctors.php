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
                    <h5>Manage Doctors</h5>
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
                                    <th>Plan Name</th>
                                    <th>Doctors</th>
                                    <th>Banner Image</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($assigned_doctors): 
                                    $grouped_plans = [];
                                    foreach ($assigned_doctors as $doctor) {
                                        if (!isset($grouped_plans[$doctor->plan_id])) {
                                            $grouped_plans[$doctor->plan_id] = [
                                                'plan_name' => $doctor->plan_name,
                                                'app_image' => $doctor->app_image,
                                                'doctors' => []
                                            ];
                                        }
                                        $grouped_plans[$doctor->plan_id]['doctors'][] = $doctor->doctor_name;
                                    }
                                    
                                    $i = 1; 
                                    foreach ($grouped_plans as $pid => $gp): 
                                ?>
                                        <tr class="gradeX">
                                            <td><?= $i++ ?></td>
                                            <td><strong><?= $gp['plan_name'] ?></strong></td>
                                            <td>
                                                <div class="doctors-list">
                                                    <?php foreach($gp['doctors'] as $dname): ?>
                                                        <span class="label label-info m-r-xs"><?= $dname ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if(!empty($gp['app_image'])): ?>
                                                    <img class="cat_image" src="<?= base_url() ?>uploads/doctor_banners/<?= $gp['app_image'] ?>" alt="Banner">
                                                <?php else: ?>
                                                    <span class="text-muted small">No Image</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url() ?>admin/subscription_plans/edit_plan_doctors/<?= $pid ?>" 
                                                   class="btn btn-xs btn-primary">
                                                    <i class="fa fa-pencil"></i> Edit Doctors
                                                </a>
                                                <a href="<?= base_url() ?>admin/subscription_plans/remove_all_doctors/<?= $pid ?>" 
                                                   class="btn btn-xs btn-danger" 
                                                   onclick="return confirm('Are you sure you want to remove ALL doctors from this plan?')">
                                                    <i class="fa fa-trash"></i> Delete
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
        destroy: true,
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

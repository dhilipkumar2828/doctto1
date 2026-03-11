<style>
    .doctor-card {
        background-color: #f8f9fa;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }
    .doctor-card:hover {
        border-color: #1ab394;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .doctor-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .plan-header {
        background: linear-gradient(135deg, #1ab394 0%, #128a71 100%);
        color: white;
        padding: 20px;
        border-radius: 5px 5px 0 0;
        margin-bottom: 20px;
    }
    .plan-header h2 {
        margin-top: 0;
        font-weight: bold;
    }
    .limit-badge {
        font-size: 14px;
        background: rgba(255,255,255,0.2);
        padding: 5px 12px;
        border-radius: 20px;
    }
</style>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Manage Doctors for Plan</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/subscriptions">
                            <button class="btn btn-primary btn-xs">BACK TO PLANS</button>
                        </a>
                    </div>
                </div>

                <div class="ibox-content" style="padding-bottom: 0; border-bottom: none;">
                    <div class="tabs-container">
                        <ul class="nav nav-tabs">
                            <?php if(!empty($all_plans)) { 
                                foreach($all_plans as $ap) { ?>
                                    <li class="<?= ($ap->id == $plan->id) ? 'active' : '' ?>">
                                        <a href="<?= base_url() ?>admin/subscription_plans/manage_doctors/<?= $ap->id ?>">
                                            <i class="fa fa-stethoscope"></i> <?= $ap->name ?>
                                        </a>
                                    </li>
                            <?php } } ?>
                        </ul>
                    </div>
                </div>

                <div class="ibox-content" style="border-top: none;">
                    <div class="plan-header">
                        <div class="row">
                            <div class="col-md-8">
                                <h2><?= $plan->name ?></h2>
                                <p><?= $plan->description ?></p>
                            </div>
                            <div class="col-md-4 text-right">
                                <span class="limit-badge">
                                    <i class="fa fa-user-md"></i> Max Doctors Allowed: <?= $plan->max_doctors_allowed ?> Doctors
                                </span>
                                <h3 style="margin-top: 10px;">₹<?= number_format($plan->price, 2) ?> / Month</h3>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($this->session->flashdata('success_message'))) { ?>
                        <div class="alert alert-success fade in alert-dismissable">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>Success!</strong> <?= $this->session->flashdata('success_message') ?>
                        </div>
                    <?php } ?>

                    <!-- <?php if (!empty($this->session->flashdata('error_message'))) { ?>
                        <div class="alert alert-danger fade in alert-dismissable">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>Error!</strong> <?= $this->session->flashdata('error_message') ?>
                        </div>
                    <?php } ?> -->

                    <div class="row">
                        <!-- Assign Doctor Section -->
                        <div class="col-md-5">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5>Add Doctor to Plan</h5>
                                </div>
                                <div class="ibox-content">
                                    <?php if ($available_doctors): ?>
                                        <?php if (count($assigned_doctors) >= $plan->max_doctors_allowed): ?>
                                            <div class="alert alert-warning">
                                                <i class="fa fa-warning"></i> Max doctor limit reached for this plan.
                                            </div>
                                        <?php else: ?>
                                            <form method="post" action="<?= base_url() ?>admin/subscription_plans/assign_doctor_from_manage">
                                                <input type="hidden" name="plan_id" value="<?= $plan->id ?>">
                                                <div class="form-group">
                                                    <label>Select Doctor</label>
                                                    <select name="doctor_id" class="form-control select2" required>
                                                        <option value="">Search/Select Doctor</option>
                                                        <?php foreach ($available_doctors as $doctor): ?>
                                                            <option value="<?= $doctor->id ?>">
                                                                <?= $doctor->doctor_name ?> (<?= $doctor->hospital_name ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <button type="submit" class="btn btn-primary block full-width m-b">Assign Doctor</button>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <p class="text-muted">No more active doctors available to assign.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Assigned Doctors List -->
                        <div class="col-md-7">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5>Currently Assigned (<?= count($assigned_doctors) ?> / <?= $plan->max_doctors_allowed ?>)</h5>
                                </div>
                                <div class="ibox-content">
                                    <?php if ($assigned_doctors): ?>
                                        <div id="assigned-doctors-list">
                                            <?php foreach ($assigned_doctors as $doctor): ?>
                                                <div class="doctor-card">
                                                    <div class="doctor-info">
                                                        <div>
                                                            <h4 style="margin: 0;"><?= $doctor->doctor_name ?></h4>
                                                            <small class="text-muted">
                                                                <i class="fa fa-hospital-o"></i> <?= $doctor->hospital_name ?> | 
                                                                <i class="fa fa-phone"></i> <?= $doctor->mobile_number ?>
                                                            </small>
                                                        </div>
                                                        <div>
                                                            <a href="<?= base_url() ?>admin/subscription_plans/remove_doctor_from_manage/<?= $plan->id ?>/<?= $doctor->doctor_id ?>" 
                                                               class="btn btn-sm btn-outline btn-danger" 
                                                               onclick="return confirm('Are you sure you want to remove this doctor from this plan?')">
                                                                <i class="fa fa-trash"></i> Remove
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center p-lg">
                                            <i class="fa fa-user-md fa-3x text-muted"></i>
                                            <p class="m-t-sm">No doctors assigned to this plan yet.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    $('.select2').select2({
        width: '100%'
    });
});
</script>

<style>
    .status-badge {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-active {
        background-color: #d4edda;
        color: #155724;
    }
    .status-expired {
        background-color: #f8d7da;
        color: #721c24;
    }
    .status-cancelled {
        background-color: #fff3cd;
        color: #856404;
    }
    .status-pending {
        background-color: #cce5ff;
        color: #004085;
    }
    .filter-section {
        background-color: #f8f9fa;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }
</style>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= $page_title ?></h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                    </div>
                </div>

                <div class="ibox-content" style="padding-bottom: 0;">
                    <div class="btn-group" style="margin-bottom: 10px;">
                        <a href="<?= base_url() ?>admin/doctor_subscriptions?type=doctor" class="btn btn-<?= ($selected_type == 'doctor') ? 'primary' : 'default' ?>">Doctors</a>
                        <a href="<?= base_url() ?>admin/doctor_subscriptions?type=customer" class="btn btn-<?= ($selected_type == 'customer') ? 'primary' : 'default' ?>">Customers</a>
                    </div>
                </div>

                <?php if ($success = $this->session->flashdata('success_message')) { ?>
                    <div class="alert alert-success fade in alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        <strong>Success!</strong> <?= $success ?>
                    </div>
                    <?php $this->session->unset_userdata('success_message'); ?>
                <?php } ?>

                <?php if (!empty($this->session->flashdata('error_message'))) { ?>
                    <div class="alert alert-danger fade in alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        <strong>Failed!</strong> <?= $this->session->flashdata('error_message') ?>
                    </div>
                <?php } ?>

                <!-- Filter Section -->
                <div class="filter-section">
                    <form method="get" action="<?= base_url() ?>admin/doctor_subscriptions" class="form-inline">
                        <input type="hidden" name="type" value="<?= $selected_type ?>">
                        
                        <?php if($selected_type == 'doctor'): ?>
                        <div class="form-group" style="margin-right: 15px;">
                            <label style="margin-right: 5px;">Doctor:</label>
                            <select name="doctor_id" class="form-control">
                                <option value="">All Doctors</option>
                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= $doctor->id ?>" <?= ($filter_doctor_id == $doctor->id) ? 'selected' : '' ?>>
                                        <?= $doctor->doctor_name ?> - <?= $doctor->hospital_name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php else: ?>
                        <div class="form-group" style="margin-right: 15px;">
                            <label style="margin-right: 5px;">Customer:</label>
                            <select name="user_id" class="form-control">
                                <option value="">All Customers</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user->id ?>" <?= ($filter_user_id == $user->id) ? 'selected' : '' ?>>
                                        <?= $user->first_name ?> <?= $user->last_name ?> (<?= $user->phone ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="form-group" style="margin-right: 15px;">
                            <label style="margin-right: 5px;">Status:</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="active" <?= ($filter_status == 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="expired" <?= ($filter_status == 'expired') ? 'selected' : '' ?>>Expired</option>
                                <option value="cancelled" <?= ($filter_status == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="<?= base_url() ?>admin/doctor_subscriptions?type=<?= $selected_type ?>" class="btn btn-default">Clear</a>
                    </form>
                </div>

                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th><?= ($selected_type == 'doctor') ? 'Doctor' : 'Customer' ?></th>
                                    <th>Plan</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <?php if($selected_type == 'doctor'): ?>
                                    <th>Auto Renew</th>
                                    <?php else: ?>
                                    <th>Cons. Used</th>
                                    <?php endif; ?>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($subscriptions): ?>
                                    <?php foreach ($subscriptions as $subscription): ?>
                                        <tr>
                                            <td><?= $subscription->id ?></td>
                                            <td>
                                                <?php if($selected_type == 'doctor'): ?>
                                                    <strong><?= $subscription->doctor_name ?></strong><br>
                                                    <small><?= $subscription->hospital_name ?></small><br>
                                                    <small><?= $subscription->mobile_number ?></small>
                                                <?php else: ?>
                                                    <strong><?= $subscription->first_name ?? '' ?> <?= $subscription->last_name ?? '' ?></strong><br>
                                                    <small><?= $subscription->phone ?? '' ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?= $subscription->plan_name ?? 'N/A' ?></strong><br>
                                                <small>₹<?= number_format((float)($subscription->plan_price ?? 0), 2) ?></small>
                                            </td>
                                            <td><?= ($selected_type == 'doctor') ? (!empty($subscription->start_at) ? date('d M Y', strtotime($subscription->start_at)) : '-') : (!empty($subscription->start_date) ? date('d M Y', strtotime($subscription->start_date)) : '-') ?></td>
                                            <td><?= ($selected_type == 'doctor') ? (!empty($subscription->end_at) ? date('d M Y', strtotime($subscription->end_at)) : '-') : (!empty($subscription->end_date) ? date('d M Y', strtotime($subscription->end_date)) : '-') ?></td>
                                            <td>
                                                <span class="status-badge status-<?= $subscription->status ?? 'unknown' ?>"><?= ucfirst($subscription->status ?? 'unknown') ?></span>
                                            </td>
                                            <td>
                                                <?php if($selected_type == 'doctor'): ?>
                                                    <?= ($subscription->auto_renew == 1) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>' ?>
                                                <?php else: ?>
                                                    <?= isset($subscription->consultations_used) ? $subscription->consultations_used : '0' ?> / <?= isset($subscription->consultations_remaining) ? $subscription->consultations_remaining : '0' ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <?php if($selected_type == 'doctor'): ?>
                                                    <a href="<?= base_url() ?>admin/doctor_subscriptions/edit/<?= $subscription->id ?>" class="btn btn-xs btn-primary" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <?php endif; ?>
                                                    
                                                    <a href="<?= base_url() ?>admin/doctor_subscriptions/changeStatus/<?= $subscription->id ?>/cancelled?type=<?= $selected_type ?>" 
                                                       class="btn btn-xs btn-danger" title="Cancel"
                                                       onclick="return confirm('Are you sure you want to cancel this subscription?')">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No subscriptions found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

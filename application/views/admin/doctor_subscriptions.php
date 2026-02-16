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
                    <h5>Doctor Subscriptions</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                        <a href="<?= base_url() ?>admin/doctor_subscriptions/add">
                            <button class="btn btn-primary">+ Add Subscription</button>
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
                        <strong>Failed!</strong> <?= $this->session->flashdata('error_message') ?>
                    </div>
                <?php } ?>

                <!-- Filter Section -->
                <div class="filter-section">
                    <form method="get" action="<?= base_url() ?>admin/doctor_subscriptions" class="form-inline">
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
                        <div class="form-group" style="margin-right: 15px;">
                            <label style="margin-right: 5px;">Status:</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="active" <?= ($filter_status == 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="expired" <?= ($filter_status == 'expired') ? 'selected' : '' ?>>Expired</option>
                                <option value="cancelled" <?= ($filter_status == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                <option value="pending" <?= ($filter_status == 'pending') ? 'selected' : '' ?>>Pending</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Filter</button>
                        <a href="<?= base_url() ?>admin/doctor_subscriptions" class="btn btn-default">Clear</a>
                    </form>
                </div>

                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Doctor</th>
                                    <th>Plan</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Status</th>
                                    <th>Auto Renew</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($subscriptions): ?>
                                    <?php foreach ($subscriptions as $subscription): ?>
                                        <tr>
                                            <td><?= $subscription->id ?></td>
                                            <td>
                                                <strong><?= $subscription->doctor_name ?></strong><br>
                                                <small><?= $subscription->hospital_name ?></small><br>
                                                <small><?= $subscription->mobile_number ?></small>
                                            </td>
                                            <td>
                                                <strong><?= $subscription->plan_name ?></strong><br>
                                                <small>₹<?= number_format($subscription->plan_price, 2) ?></small>
                                            </td>
                                            <td><?= date('d M Y', strtotime($subscription->start_at)) ?></td>
                                            <td><?= date('d M Y', strtotime($subscription->end_at)) ?></td>
                                            <td>
                                                <?php if ($subscription->status == 'active'): ?>
                                                    <span class="status-badge status-active">Active</span>
                                                <?php elseif ($subscription->status == 'expired'): ?>
                                                    <span class="status-badge status-expired">Expired</span>
                                                <?php elseif ($subscription->status == 'cancelled'): ?>
                                                    <span class="status-badge status-cancelled">Cancelled</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-pending">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($subscription->auto_renew == 1): ?>
                                                    <span class="badge badge-success">Yes</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">No</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url() ?>admin/doctor_subscriptions/edit/<?= $subscription->id ?>" 
                                                       class="btn btn-xs btn-primary" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    
                                                    <?php if ($subscription->status == 'active'): ?>
                                                        <a href="<?= base_url() ?>admin/doctor_subscriptions/changeStatus/<?= $subscription->id ?>/expired" 
                                                           class="btn btn-xs btn-warning" title="Mark Expired"
                                                           onclick="return confirm('Are you sure you want to mark this subscription as expired?')">
                                                            <i class="fa fa-clock-o"></i>
                                                        </a>
                                                    <?php elseif ($subscription->status == 'pending'): ?>
                                                        <a href="<?= base_url() ?>admin/doctor_subscriptions/changeStatus/<?= $subscription->id ?>/active" 
                                                           class="btn btn-xs btn-success" title="Activate"
                                                           onclick="return confirm('Are you sure you want to activate this subscription?')">
                                                            <i class="fa fa-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <a href="<?= base_url() ?>admin/doctor_subscriptions/changeStatus/<?= $subscription->id ?>/cancelled" 
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
                                        <td colspan="8" class="text-center">No doctor subscriptions found</td>
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

<script>
$(document).ready(function() {
    // Destroy existing DataTable if it exists to prevent reinitialization
    if ($.fn.DataTable.isDataTable('.dataTables-example')) {
        $('.dataTables-example').DataTable().destroy();
    }
    
    // Initialize DataTable with safe configuration
    $('.dataTables-example').DataTable({
        pageLength: 25,
        responsive: true,
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'excel', title: 'Doctor Subscriptions'},
            {extend: 'pdf', title: 'Doctor Subscriptions'},
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
});
</script>

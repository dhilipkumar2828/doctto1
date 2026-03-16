<style>
    .plan_image {
        width: 100px;
        height: 100px;
        object-fit: scale-down;
        margin-right: 5px;
        border-radius: 10px;
        border: 1px solid #efeded;
    }
    .plan_title {
        font-size: 17px !important;
        color: #f39c5a;
    }
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
    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5 class="plan_title">Subscription Plans</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                        <a href="<?= base_url() ?>admin/subscription_plans/add">
                            <button class="btn btn-primary">+ Add Plan</button>
                        </a>
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

                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Plan Name</th>
                                    <th>Description</th>
                                    <th>Price (₹)</th>
                                    <th>Duration (Days)</th>
                                    <th>Max Doctors</th>
                                    <th>Assigned Doctors</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($plans): ?>
                                    <?php foreach ($plans as $plan): ?>
                                        <tr>
                                            <td><?= $plan->id ?></td>
                                            <td>
                                                <strong><?= $plan->name ?></strong>
                                            </td>
                                            <td>
                                                <?= strlen($plan->description) > 50 ? substr($plan->description, 0, 50) . '...' : $plan->description ?>
                                            </td>
                                            <td>₹<?= number_format($plan->price, 2) ?></td>
                                            <td><?= $plan->duration_days ?> days</td>
                                            <td><?= $plan->max_doctors_allowed ?></td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?= $plan->assigned_doctors_count ? $plan->assigned_doctors_count : 0 ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($plan->is_active == 1): ?>
                                                    <span class="status-badge status-active">Active</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-inactive">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url() ?>admin/subscription_plans/edit/<?= $plan->id ?>" 
                                                       class="btn btn-xs btn-primary" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    
                                                    <?php if ($plan->is_active == 1): ?>
                                                        <a href="<?= base_url() ?>admin/subscription_plans/changeStatus/<?= $plan->id ?>/0" 
                                                           class="btn btn-xs btn-warning" title="Deactivate"
                                                           onclick="return confirm('Are you sure you want to deactivate this plan?')">
                                                            <i class="fa fa-ban"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?= base_url() ?>admin/subscription_plans/changeStatus/<?= $plan->id ?>/1" 
                                                           class="btn btn-xs btn-success" title="Activate"
                                                           onclick="return confirm('Are you sure you want to activate this plan?')">
                                                            <i class="fa fa-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No subscription plans found</td>
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
        destroy: true,
        pageLength: 25,
        responsive: true,
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'excel', title: 'Subscription Plans'},
            {extend: 'pdf', title: 'Subscription Plans'},
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

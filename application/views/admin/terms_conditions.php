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
                    <h5>Terms & Conditions Management</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/terms_conditions/add" class="btn btn-primary btn-xs">
                            <i class="fa fa-plus"></i> Add New Terms
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" class="form-inline">
                                <div class="form-group mr-2">
                                    <select name="plan_type" class="form-control">
                                        <option value="">All Plan Types</option>
                                        <option value="user" <?= ($filter_plan_type == 'user') ? 'selected' : '' ?>>User Subscriptions</option>
                                        <option value="doctor" <?= ($filter_plan_type == 'doctor') ? 'selected' : '' ?>>Doctor Subscriptions</option>
                                        <option value="both" <?= ($filter_plan_type == 'both') ? 'selected' : '' ?>>Both</option>
                                    </select>
                                </div>
                                <div class="form-group mr-2">
                                    <select name="status" class="form-control">
                                        <option value="">All Status</option>
                                        <option value="1" <?= ($filter_status == '1') ? 'selected' : '' ?>>Active</option>
                                        <option value="0" <?= ($filter_status == '0') ? 'selected' : '' ?>>Inactive</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="<?= base_url() ?>admin/terms_conditions" class="btn btn-secondary ml-2">Clear</a>
                            </form>
                        </div>
                    </div>

                    <!-- Terms List -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Plan Type</th>
                                    <th>Version</th>
                                    <th>Effective Date</th>
                                    <th>Sections</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($terms): ?>
                                    <?php foreach ($terms as $term): ?>
                                        <tr>
                                            <td><?= $term->id ?></td>
                                            <td>
                                                <strong><?= $term->title ?></strong>
                                                <?php if ($term->subscription_plan_id): ?>
                                                    <br><small class="text-muted">Plan ID: <?= $term->subscription_plan_id ?></small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $term->plan_type == 'user' ? 'info' : ($term->plan_type == 'doctor' ? 'success' : 'warning') ?>">
                                                    <?= ucfirst($term->plan_type) ?>
                                                </span>
                                            </td>
                                            <td><?= $term->version ?></td>
                                            <td><?= date('d M Y', strtotime($term->effective_date)) ?></td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?= $term->sections_count ?: 0 ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($term->is_active == 1): ?>
                                                    <span class="status-badge status-active">Active</span>
                                                <?php else: ?>
                                                    <span class="status-badge status-inactive">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d M Y', strtotime($term->created_at)) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="<?= base_url() ?>admin/terms_conditions/view/<?= $term->id ?>" 
                                                       class="btn btn-xs btn-info" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    
                                                    <a href="<?= base_url() ?>admin/terms_conditions/sections/<?= $term->id ?>" 
                                                       class="btn btn-xs btn-warning" title="Manage Sections">
                                                        <i class="fa fa-list"></i>
                                                    </a>
                                                    
                                                    <a href="<?= base_url() ?>admin/terms_conditions/edit/<?= $term->id ?>" 
                                                       class="btn btn-xs btn-primary" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    
                                                    <?php if ($term->is_active == 1): ?>
                                                        <a href="<?= base_url() ?>admin/terms_conditions/changeStatus/<?= $term->id ?>/inactive" 
                                                           class="btn btn-xs btn-warning" title="Deactivate"
                                                           onclick="return confirm('Are you sure you want to deactivate these terms?')">
                                                            <i class="fa fa-ban"></i>
                                                        </a>
                                                    <?php else: ?>
                                                        <a href="<?= base_url() ?>admin/terms_conditions/changeStatus/<?= $term->id ?>/active" 
                                                           class="btn btn-xs btn-success" title="Activate"
                                                           onclick="return confirm('Are you sure you want to activate these terms?')">
                                                            <i class="fa fa-check"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    
                                                    <a href="<?= base_url() ?>admin/terms_conditions/delete/<?= $term->id ?>" 
                                                       class="btn btn-xs btn-danger" title="Delete"
                                                       onclick="return confirm('Are you sure you want to delete these terms? This will also delete all associated sections.')">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No terms and conditions found</td>
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
            {extend: 'excel', title: 'Terms & Conditions'},
            {extend: 'pdf', title: 'Terms & Conditions'},
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

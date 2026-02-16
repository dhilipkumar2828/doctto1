<div class="wrapper border-bottom white-bg page-heading">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-10">
                <h2 style="margin-bottom: 20px; padding-top: 20px;">Subscription Plans Management</h2>
            </div>
            <div class="col-lg-2">
                <div class="title-action" style="padding-top: 20px;">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createModal">
                        <i class="fa fa-plus"></i> Create New Plan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <?php 
        // Manually retrieve and clear to prevent "stuck" messages
        $success_msg = $this->session->flashdata('success');
        if (!$success_msg) {
            $success_msg = $this->session->tempdata('success');
        }
        
        if ($success_msg): 
            // Aggressively clear it from the session immediately
            unset($_SESSION['success']);
    ?>
        <div class="alert alert-success alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <?= $success_msg ?>
        </div>
    <?php endif; ?>

    <!-- Create Plan Modal -->
    <div class="modal inmodal" id="createModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content animated bounceInRight">
                <div class="modal-header" style="padding: 15px;">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" style="font-size: 20px;">Create New <?= ucfirst($selected_type) ?> Plan</h4>
                </div>
                <form action="<?= base_url() ?>admin/subscriptions/create" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="type" value="<?= $selected_type ?>">
                        <div class="form-group">
                            <label>Plan Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Premium Plan" required>
                        </div>
                        <div class="form-group">
                            <label>Price (₹)</label>
                            <input type="number" name="price" step="0.01" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="form-group">
                            <label>Duration (Days)</label>
                            <input type="number" name="duration_days" value="30" class="form-control" required>
                        </div>
                        <?php if($selected_type == 'customer'): ?>
                        <div class="form-group">
                            <label>Max Doctors Allowed</label>
                            <input type="number" name="max_doctors_allowed" value="1" class="form-control">
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label>Perks / Description (One per line)</label>
                            <textarea name="<?= $selected_type == 'doctor' ? 'perks' : 'description' ?>" class="form-control" rows="5" placeholder="Feature 1&#10;Feature 2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Create Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Select Subscription Category</h5>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-4">
                            <form method="get" id="typeForm">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label>Plan Type</label>
                                    <select name="type" class="form-control" onchange="this.form.submit()">
                                        <option value="customer" <?= $selected_type == 'customer' ? 'selected' : '' ?>>Customer Subscription</option>
                                        <option value="doctor" <?= $selected_type == 'doctor' ? 'selected' : '' ?>>Doctor Subscription</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="display: flex; flex-wrap: wrap;">
        <?php foreach ($plans as $plan): ?>
            <div class="col-lg-4" style="display: flex; flex-direction: column; margin-bottom: 20px;">
                <div class="ibox" style="width: 100%; display: flex; flex-direction: column; height: 100%; margin-bottom: 0;">
                    <div class="ibox-title">
                        <span class="label label-primary pull-right">Monthly</span>
                        <h5><?= $plan->name ?></h5>
                    </div>
                    <div class="ibox-content" style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <h1 class="no-margins">₹<?= $plan->price ?></h1>
                            <div class="stat-percent font-bold text-navy">30 Days</div>
                            <small>Price per month</small>
                            
                            <div class="m-t-md">
                                <h5>Features / Perks</h5>
                                <ul class="list-unstyled">
                                    <?php 
                                        $perks = isset($plan->perks) ? $plan->perks : (isset($plan->description) ? $plan->description : '');
                                        $perk_list = explode("\n", $perks);
                                        foreach($perk_list as $pl): if(trim($pl)):
                                    ?>
                                        <li><i class="fa fa-check text-navy"></i> <?= trim($pl) ?></li>
                                    <?php endif; endforeach; ?>
                                </ul>
                            </div>
                        </div>

                        <div class="m-t-md">
                            <div class="row">
                                <div class="col-xs-6">
                                    <button type="button" class="btn btn-primary btn-block btn-outline" data-toggle="modal" data-target="#editModal<?= $plan->id ?>">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                </div>
                                <div class="col-xs-6">
                                    <a href="<?= base_url() ?>admin/subscriptions/delete/<?= $plan->id ?>?type=<?= $selected_type ?>" class="btn btn-danger btn-block btn-outline" onclick="return confirm('Are you sure you want to delete this plan?')">
                                        <i class="fa fa-trash"></i> Delete
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Modal -->
            <div class="modal inmodal" id="editModal<?= $plan->id ?>" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content animated bounceInRight">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title">Edit <?= $plan->name ?></h4>
                        </div>
                        <form action="<?= base_url() ?>admin/subscriptions/update" method="post">
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $plan->id ?>">
                                <input type="hidden" name="type" value="<?= $selected_type ?>">
                                <div class="form-group">
                                    <label>Price (₹)</label>
                                    <input type="number" name="price" value="<?= $plan->price ?>" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label>Perks / Description (One per line)</label>
                                    <textarea name="<?= $selected_type == 'doctor' ? 'perks' : 'description' ?>" class="form-control" rows="10"><?= $perks ?></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    // Auto-hide alerts after 3 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 3000);
</script>

<link href="<?= base_url() ?>admin_assets/assets/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
<div class="wrapper border-bottom white-bg page-heading">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-lg-9">
                <h2 style="font-weight: 800; color: #1c84c6; margin: 0; padding-top: 25px;">
                    <i class="fa fa-cubes"></i> SUBSCRIPTION PLANS
                </h2>
                <p class="text-muted" style="margin-bottom: 25px;">Manage and configure your service subscription tiers with ease.</p>
            </div>
            <div class="col-lg-3 text-right">
                <div class="title-action" style="padding-top: 20px;">
                    <button type="button" class="btn btn-primary" style="padding: 12px 25px; border-radius: 50px; font-weight: 700; box-shadow: 0 4px 15px rgba(28, 132, 198, 0.3);" data-toggle="modal" data-target="#createModal">
                        <i class="fa fa-plus-circle"></i> CREATE NEW PLAN
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .wrapper-content {
        background-color: #f8f9fa;
        min-height: 100vh;
    }
    .subscription-card {
        background: #fff;
        border-radius: 16px !important;
        border: none !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05) !important;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
        overflow: hidden;
    }
    .subscription-card:hover {
        transform: translateY(-10px) !important;
        box-shadow: 0 20px 40px rgba(0,0,0,0.12) !important;
    }
    .plan-header {
        padding: 15px 20px !important;
        border-bottom: 1px solid #f1f1f1 !important;
        position: relative;
    }
    .plan-price-section {
        padding: 20px 20px !important;
        background: #fff;
    }
    .perk-item {
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #676a6c;
        font-size: 13px;
    }
    .perk-item i {
        color: #1ab394;
        font-size: 14px;
    }
    .btn-action {
        border-radius: 8px !important;
        padding: 8px 15px !important;
        font-weight: 600 !important;
        font-size: 12px !important;
        transition: all 0.2s;
    }
    .card-footer {
        padding: 15px !important;
        background: #fafafb;
        border-top: 1px solid #eee;
    }
    
    /* Plan Specific Colors */
    .card-classic .plan-title { color: #23c6c8; }
    .card-advanced .plan-title { color: #f8ac59; }
    .card-popular .plan-title { color: #1c84c6; }
    
    .pricing-value {
        font-size: 38px;
        font-weight: 800;
        letter-spacing: -1px;
        margin: 0;
    }

</style>

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
                        <div id="create_plan_name_dropdown_div">
                            <div class="form-group">
                                <label>Plan Name</label>
                                <select name="name" id="create_plan_name_dropdown" class="form-control" required>
                                    <option value="">Select Plan</option>
                                    <option value="Classic Plan">Classic Plan</option>
                                    <option value="Advanced Plan">Advanced Plan</option>
                                    <option value="Popular Plan">Popular Plan</option>
                                    <option value="others">Others</option>
                                </select>
                            </div>
                        </div>

                        <div id="create_plan_name_input_div" style="display:none;">
                            <div class="form-group">
                                <label>Plan Name</label>
                                <div class="input-group">
                                    <input type="text" id="create_plan_name_input" class="form-control" placeholder="e.g. Custom Plan">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-warning" onclick="toggleCreatePlanInput(false)">Back</button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Price (₹)</label>
                            <input type="number" name="price" step="0.01" class="form-control" placeholder="0.00" required>
                        </div>
                        <div class="form-group">
                            <label>Duration (Days)</label>
                            <input type="number" name="duration_days" class="form-control" placeholder="e.g. 1, 30, 365" required>
                        </div>
                        <?php if($selected_type == 'doctor'): ?>
                        <div class="form-group">
                            <label>Call Chat Number</label>
                            <input type="text" name="call_chat" class="form-control" placeholder="e.g. +91 9876543210">
                        </div>
                        <div class="form-group">
                            <label>Whatsapp Chat Number</label>
                            <input type="text" name="whatsapp_chat" class="form-control" placeholder="e.g. +91 9876543210">
                        </div>
                        <?php endif; ?>
                        <?php if($selected_type == 'customer'): ?>
                        <div class="form-group">
                            <label>Max Doctors Allowed</label>
                            <input type="number" name="max_doctors_allowed" value="1" class="form-control">
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="is_active" class="form-control" required>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
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
                                    <div class="input-group">
                                        <select name="type" class="form-control" onchange="this.form.submit()">
                                            <option value="customer" <?= $selected_type == 'customer' ? 'selected' : '' ?>>Customer Subscription</option>
                                            <option value="doctor" <?= $selected_type == 'doctor' ? 'selected' : '' ?>>Doctor Subscription</option>
                                        </select>
                                        <span class="input-group-btn">
                                            <a href="<?= base_url() ?>admin/subscriptions" class="btn btn-warning">
                                                <i class="fa fa-refresh"></i> Reset
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row" style="display: flex; flex-wrap: wrap;">
        <?php foreach ($plans as $plan): 
            $plan_class = '';
            if (stripos($plan->name, 'classic') !== false) $plan_class = 'card-classic';
            elseif (stripos($plan->name, 'advanced') !== false) $plan_class = 'card-advanced';
            elseif (stripos($plan->name, 'popular') !== false) $plan_class = 'card-popular';
        ?>
            <div class="col-lg-4" style="margin-bottom: 40px;">
                <div class="ibox subscription-card h-100 <?= $plan_class ?>" style="display: flex; flex-direction: column; height: 100%; position: relative;">

                    
                    <div class="plan-header" style="display: flex; justify-content: space-between; align-items: center; padding: 15px 15px !important;">
                        <div style="flex: 0 0 auto;">
                            <?php if($plan->is_active == 1): ?>
                                <span class="label" style="background: rgba(26, 179, 148, 0.1); color: #1ab394; border: 1px solid rgba(26, 179, 148, 0.2); border-radius: 20px; padding: 3px 10px; font-weight: 700; font-size: 9px; letter-spacing: 0.5px;">
                                    <i class="fa fa-check"></i> ACTIVE
                                </span>
                            <?php else: ?>
                                <span class="label" style="background: rgba(237, 85, 101, 0.1); color: #ed5565; border: 1px solid rgba(237, 85, 101, 0.2); border-radius: 20px; padding: 3px 10px; font-weight: 700; font-size: 9px; letter-spacing: 0.5px;">
                                    <i class="fa fa-times"></i> INACTIVE
                                </span>
                            <?php endif; ?>
                        </div>

                        <div style="flex: 1; text-align: center; padding: 0 10px;">
                            <h2 class="plan-title" style="margin: 0; font-weight: 800; text-transform: uppercase; font-size: 15px; letter-spacing: 0.5px;">
                                <?= $plan->name ?>
                            </h2>
                        </div>
                        
                        <div style="flex: 0 0 auto;">
                            <span class="label" style="background: #23c6c8; color: #fff; border-radius: 20px; padding: 3px 10px; font-weight: 800; font-size: 9px; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(35, 198, 200, 0.1);">
                                <i class="fa fa-clock-o"></i> <?= $plan->duration_days ?> <?= ($plan->duration_days == 1) ? 'DAY' : 'DAYS' ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="plan-price-section text-center" style="border-bottom: 1px solid #f1f1f1; background: #fafafa; padding: 25px 20px !important;">
                        <h1 class="pricing-value" style="color: #1a1a1a; margin: 0;">
                            ₹<?= number_format($plan->price, 0) ?>
                            <span style="font-size: 20px; color: #999; font-weight: 400; vertical-align: middle; margin-left: 5px;">
                                / <?= ($plan->duration_days == 30) ? 'month' : $plan->duration_days . (($plan->duration_days == 1) ? ' day' : ' days') ?>
                            </span>
                        </h1>
                        <div style="text-transform: uppercase; font-size: 11px; font-weight: 800; color: #444; letter-spacing: 1px;">
                            BILLED EVERY <span style="color: #1c84c6;"><?= $plan->duration_days ?> <?= ($plan->duration_days == 1) ? 'DAY' : 'DAYS' ?></span>
                        </div>
                    </div>

                    <div class="ibox-content" style="flex: 1; padding: 20px 25px;">
                        <h5 style="color: #bbb; text-transform: uppercase; font-size: 10px; font-weight: 800; margin-bottom: 15px; letter-spacing: 1px;">Features Included</h5>
                        <div class="perk-list">
                            <?php if($selected_type == 'customer' && !empty($plan->max_doctors_allowed)): ?>
                                <div class="perk-item">
                                    <i class="fa fa-user-md" style="color: #1c84c6;"></i>
                                    <span><strong><?= $plan->max_doctors_allowed ?></strong> <?= $plan->max_doctors_allowed > 1 ? 'Doctors' : 'Doctor' ?> / Month</span>
                                </div>
                            <?php endif; ?>
                            <?php 
                                $perks = isset($plan->perks) ? $plan->perks : (isset($plan->description) ? $plan->description : '');
                                $perk_list = explode("\n", $perks);
                                foreach($perk_list as $pl): if(trim($pl)):
                            ?>
                                <div class="perk-item">
                                    <i class="fa fa-dot-circle-o" style="color: #1ab394; opacity: 0.6;"></i>
                                    <span><?= trim($pl) ?></span>
                                </div>
                            <?php endif; endforeach; ?>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-xs-6">
                                <button type="button" class="btn btn-primary btn-block btn-action" data-toggle="modal" data-target="#editModal<?= $plan->id ?>">
                                    <i class="fa fa-pencil"></i> EDIT PLAN
                                </button>
                            </div>
                            <div class="col-xs-6">
                                <a href="javascript:void(0);" 
                                   class="btn btn-default btn-block btn-action delete-plan" 
                                   style="color: #ed5565; border-color: #fcecec; background: #fffafb;" 
                                   data-url="<?= base_url() ?>admin/subscriptions/delete/<?= $plan->id ?>?type=<?= $selected_type ?>"
                                   data-name="<?= $plan->name ?>">
                                    <i class="fa fa-trash"></i> DELETE
                                </a>
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
                                <?php 
                                $dropdown_plans = ['Classic Plan', 'Advanced Plan', 'Popular Plan'];
                                $is_custom_name = !in_array($plan->name, $dropdown_plans);
                                ?>
                                <div id="edit_plan_name_dropdown_div_<?= $plan->id ?>" style="<?= $is_custom_name ? 'display:none;' : '' ?>">
                                    <div class="form-group">
                                        <label>Plan Name</label>
                                        <select name="<?= $is_custom_name ? '' : 'name' ?>" id="edit_plan_name_dropdown_<?= $plan->id ?>" class="form-control edit-plan-dropdown" data-id="<?= $plan->id ?>" <?= $is_custom_name ? '' : 'required' ?>>
                                            <option value="">Select Plan</option>
                                            <option value="Classic Plan" <?= ($plan->name == 'Classic Plan') ? 'selected' : '' ?>>Classic Plan</option>
                                            <option value="Advanced Plan" <?= ($plan->name == 'Advanced Plan') ? 'selected' : '' ?>>Advanced Plan</option>
                                            <option value="Popular Plan" <?= ($plan->name == 'Popular Plan') ? 'selected' : '' ?>>Popular Plan</option>
                                            <option value="others">Others</option>
                                        </select>
                                    </div>
                                </div>

                                <div id="edit_plan_name_input_div_<?= $plan->id ?>" style="<?= $is_custom_name ? '' : 'display:none;' ?>">
                                    <div class="form-group">
                                        <label>Plan Name</label>
                                        <div class="input-group">
                                            <input type="text" name="<?= $is_custom_name ? 'name' : '' ?>" id="edit_plan_name_input_<?= $plan->id ?>" class="form-control" value="<?= $plan->name ?>" <?= $is_custom_name ? 'required' : '' ?>>
                                            <span class="input-group-btn">
                                                <button type="button" class="btn btn-warning" onclick="toggleEditPlanInput(<?= $plan->id ?>, false)">Back</button>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Price (₹)</label>
                                    <input type="number" name="price" value="<?= $plan->price ?>" class="form-control" required>
                                </div>

                                <div class="form-group">
                                    <label>Duration (Days)</label>
                                    <input type="number" name="duration_days" value="<?= $plan->duration_days ?>" class="form-control" required>
                                </div>

                                <?php if($selected_type == 'doctor'): ?>
                                <div class="form-group">
                                    <label>Call Chat Number</label>
                                    <input type="text" name="call_chat" value="<?= isset($plan->call_chat) ? $plan->call_chat : '' ?>" class="form-control" placeholder="e.g. +91 9876543210">
                                </div>
                                <div class="form-group">
                                    <label>Whatsapp Chat Number</label>
                                    <input type="text" name="whatsapp_chat" value="<?= isset($plan->whatsapp_chat) ? $plan->whatsapp_chat : '' ?>" class="form-control" placeholder="e.g. +91 9876543210">
                                </div>
                                <?php endif; ?>

                                <?php if($selected_type == 'customer'): ?>
                                <div class="form-group">
                                    <label>Max Doctors Allowed</label>
                                    <input type="number" name="max_doctors_allowed" value="<?= $plan->max_doctors_allowed ?>" class="form-control">
                                </div>
                                <?php endif; ?>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="is_active" class="form-control" required>
                                        <option value="1" <?= $plan->is_active == 1 ? 'selected' : '' ?>>Active</option>
                                        <option value="0" <?= $plan->is_active == 0 ? 'selected' : '' ?>>Inactive</option>
                                    </select>
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

<!-- Sweet alert -->
<script src="<?= base_url() ?>admin_assets/assets/js/plugins/sweetalert/sweetalert.min.js"></script>

<script>
    // Auto-hide alerts after 3 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 3000);

    // Create Modal Toggle
    $('#create_plan_name_dropdown').on('change', function() {
        if ($(this).val() === 'others') {
            toggleCreatePlanInput(true);
        }
    });

    function toggleCreatePlanInput(showInput) {
        if (showInput) {
            $('#create_plan_name_dropdown_div').hide();
            $('#create_plan_name_input_div').show();
            $('#create_plan_name_input').attr('name', 'name').prop('required', true).val('').focus();
            $('#create_plan_name_dropdown').removeAttr('name').prop('required', false);
        } else {
            $('#create_plan_name_input_div').hide();
            $('#create_plan_name_dropdown_div').show();
            $('#create_plan_name_dropdown').attr('name', 'name').prop('required', true).val('');
            $('#create_plan_name_input').removeAttr('name').prop('required', false);
        }
    }

    // Edit Modal Toggle
    $('.edit-plan-dropdown').on('change', function() {
        var id = $(this).data('id');
        if ($(this).val() === 'others') {
            toggleEditPlanInput(id, true);
        }
    });

    function toggleEditPlanInput(id, showInput) {
        if (showInput) {
            $('#edit_plan_name_dropdown_div_' + id).hide();
            $('#edit_plan_name_input_div_' + id).show();
            $('#edit_plan_name_input_' + id).attr('name', 'name').prop('required', true).val('').focus();
            $('#edit_plan_name_dropdown_' + id).removeAttr('name').prop('required', false);
        } else {
            $('#edit_plan_name_input_div_' + id).hide();
            $('#edit_plan_name_dropdown_div_' + id).show();
            $('#edit_plan_name_dropdown_' + id).attr('name', 'name').prop('required', true).val('');
            $('#edit_plan_name_input_' + id).removeAttr('name').prop('required', false);
        }
    }
    // SweetAlert Delete Confirmation
    $('.delete-plan').on('click', function() {
        var deleteUrl = $(this).data('url');
        var planName = $(this).data('name');
        
        swal({
            title: "Delete Plan?",
            text: "Are you sure you want to delete '" + planName + "'? This action cannot be undone!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes, delete it!",
            cancelButtonText: "No, cancel plx!",
            closeOnConfirm: false,
            closeOnCancel: true
        }, function(isConfirm) {
            if (isConfirm) {
                window.location.href = deleteUrl;
            }
        });
    });
</script>

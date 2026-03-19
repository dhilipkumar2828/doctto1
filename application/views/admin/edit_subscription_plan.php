<style>
    .feature-row {
        background-color: #f8f9fa;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }
    .remove-feature {
        color: #dc3545;
        cursor: pointer;
    }
    .doctor-card {
        background-color: #f8f9fa;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }
    .doctor-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
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
                    <h5>Edit Subscription Plan</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/subscription_plans">
                            <button class="btn btn-primary">BACK</button>
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

                <div class="ibox-content">
                    <form method="post" action="<?= base_url() ?>admin/subscription_plans/update" class="form-horizontal">
                        <input type="hidden" name="plan_id" value="<?= $plan->id ?>">
                        
                        <?php 
                        $dropdown_plans = ['Classic Plan', 'Advanced Plan', 'Popular Plan'];
                        $is_custom_name = !in_array($plan->name, $dropdown_plans);
                        ?>

                        <div class="form-group" id="plan_name_dropdown_div" style="<?= $is_custom_name ? 'display:none;' : '' ?>">
                            <label class="col-sm-2 control-label">Plan Name *</label>
                            <div class="col-sm-10">
                                <select name="<?= $is_custom_name ? '' : 'name' ?>" id="plan_name_dropdown" class="form-control" <?= $is_custom_name ? '' : 'required' ?>>
                                    <option value="">Select Plan</option>
                                    <option value="Classic Plan" <?= ($plan->name == 'Classic Plan') ? 'selected' : '' ?>>Classic Plan</option>
                                    <option value="Advanced Plan" <?= ($plan->name == 'Advanced Plan') ? 'selected' : '' ?>>Advanced Plan</option>
                                    <option value="Popular Plan" <?= ($plan->name == 'Popular Plan') ? 'selected' : '' ?>>Popular Plan</option>
                                    <option value="others">Others</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="plan_name_input_div" style="<?= $is_custom_name ? '' : 'display:none;' ?>">
                            <label class="col-sm-2 control-label">Plan Name *</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" name="<?= $is_custom_name ? 'name' : '' ?>" id="plan_name_input" class="form-control" value="<?= $plan->name ?>" <?= $is_custom_name ? 'required' : '' ?>>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-warning" id="back_to_dropdown">Back to List</button>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Description *</label>
                            <div class="col-sm-10">
                                <textarea name="description" class="form-control" rows="3" required><?= $plan->description ?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Price (₹) *</label>
                            <div class="col-sm-10">
                                <input type="number" name="price" class="form-control" step="0.01" min="0" value="<?= $plan->price ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Duration (Days) *</label>
                            <div class="col-sm-10">
                                <input type="number" name="duration_days" class="form-control" min="1" value="<?= $plan->duration_days ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Max Doctors Allowed *</label>
                            <div class="col-sm-10">
                                <input type="number" name="max_doctors_allowed" class="form-control" min="1" value="<?= $plan->max_doctors_allowed ?>" required>
                                <small class="text-muted">Currently assigned: <?= count($assigned_doctors) ?> doctors</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Plan Features</label>
                            <div class="col-sm-10">
                                <div id="features-container">
                                    <?php if ($plan_features): ?>
                                        <?php foreach ($plan_features as $feature): ?>
                                            <div class="feature-row">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label>Consultation Type</label>
                                                        <select name="consultation_fees[]" class="form-control" required>
                                                            <option value="">Select Consultation Type</option>
                                                            <?php foreach ($consultation_fees as $fee): ?>
                                                                <option value="<?= $fee->id ?>" <?= ($fee->id == $feature->consultation_fees_id) ? 'selected' : '' ?>>
                                                                    <?= $fee->title ?> (₹<?= $fee->price ?>)
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Limit Count *</label>
                                                        <input type="number" name="limit_counts[]" class="form-control" min="1" value="<?= $feature->limit_count ?>" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Rollover</label>
                                                        <div style="margin-top: 25px;">
                                                            <input type="checkbox" name="rollover[]" value="1" <?= ($feature->rollover == 1) ? 'checked' : '' ?>> Allow rollover to next month
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label>&nbsp;</label>
                                                        <div style="margin-top: 25px;">
                                                            <i class="fa fa-trash remove-feature" onclick="removeFeature(this)"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="feature-row">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label>Consultation Type</label>
                                                    <select name="consultation_fees[]" class="form-control" required>
                                                        <option value="">Select Consultation Type</option>
                                                        <?php foreach ($consultation_fees as $fee): ?>
                                                            <option value="<?= $fee->id ?>">
                                                                <?= $fee->title ?> (₹<?= $fee->price ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Limit Count *</label>
                                                    <input type="number" name="limit_counts[]" class="form-control" min="1" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label>Rollover</label>
                                                    <div style="margin-top: 25px;">
                                                        <input type="checkbox" name="rollover[]" value="1"> Allow rollover to next month
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <label>&nbsp;</label>
                                                    <div style="margin-top: 25px;">
                                                        <i class="fa fa-trash remove-feature" onclick="removeFeature(this)"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="btn btn-success btn-sm" onclick="addFeature()">
                                    <i class="fa fa-plus"></i> Add Feature
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">Update Plan</button>
                                <a href="<?= base_url() ?>admin/subscription_plans" class="btn btn-white">Cancel</a>
                            </div>
                        </div>
                    <                    <!-- Doctor Assignment Section -->
                    <hr>
                    <div class="row">
                        <div class="col-sm-12">
                            <h3 class="m-t-none m-b">Doctor Assignment</h3>
                            
                            <!-- Form to Add More Doctors to this Plan -->
                            <div class="ibox float-e-margins" style="border: 1px solid #e7eaec; padding: 15px; background: #fafafa;">
                                <form method="post" action="<?= base_url() ?>admin/subscription_plans/assign_doctor_from_manage" enctype="multipart/form-data">
                                    <input type="hidden" name="plan_id" value="<?= $plan->id ?>">
                                    <input type="hidden" name="redirect_url" value="admin/subscription_plans/edit/<?= $plan->id ?>">
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group" style="margin-bottom: 10px !important;">
                                                <label class="control-label">Select Doctors (Multi-select) *</label>
                                                <select name="doctor_ids[]" class="form-control select2-multiple" multiple="multiple" required>
                                                    <?php foreach ($available_doctors as $doctor): ?>
                                                        <option value="<?= $doctor->id ?>">
                                                            <?= $doctor->doctor_name ?> (<?= $doctor->hospital_name ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" style="margin-bottom: 10px !important;">
                                                <label class="control-label">Title/Subtitle</label>
                                                <input type="text" name="title" class="form-control" placeholder="e.g. Specialists">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group" style="margin-bottom: 10px !important;">
                                                <label class="control-label">App Image *</label>
                                                <input type="file" name="appimage" class="form-control" required>
                                                <small class="text-danger">Rec: 900x400px</small>
                                            </div>
                                        </div>
                                        <div class="col-md-2" style="padding-top: 25px;">
                                            <button type="submit" class="btn btn-success btn-block"><i class="fa fa-plus"></i> Assign</button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- List of Currently Assigned Doctors -->
                            <div class="table-responsive m-t-md">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Doctor Name</th>
                                            <th>Banner Image</th>
                                            <th>Title</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($assigned_doctors): ?>
                                            <?php foreach ($assigned_doctors as $doctor): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= $doctor->doctor_name ?></strong><br>
                                                        <small class="text-muted"><?= $doctor->hospital_name ?></small>
                                                    </td>
                                                    <td>
                                                        <?php if(!empty($doctor->app_image)): ?>
                                                            <img src="<?= base_url() ?>uploads/doctor_banners/<?= $doctor->app_image ?>" style="height: 40px; border-radius: 4px;">
                                                        <?php else: ?>
                                                            <span class="text-muted">No Image</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= $doctor->title ?></td>
                                                    <td>
                                                        <a href="<?= base_url() ?>admin/subscription_plans/edit_plan_doctors/<?= $plan->id ?>" 
                                                           class="btn btn-xs btn-primary">
                                                            <i class="fa fa-pencil"></i> Edit
                                                         </a>
                                                        <a href="<?= base_url() ?>admin/subscription_plans/remove_doctor/<?= $plan->id ?>/<?= $doctor->doctor_id ?>" 
                                                           class="btn btn-xs btn-danger" 
                                                           onclick="return confirm('Are you sure you want to remove this doctor from this plan?')">
                                                            <i class="fa fa-trash"></i> Remove
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr><td colspan="4" class="text-center">No doctors assigned yet.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>  </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
function addFeature() {
    const container = document.getElementById('features-container');
    const featureRow = document.querySelector('.feature-row').cloneNode(true);
    
    // Clear the values
    featureRow.querySelector('select').value = '';
    featureRow.querySelector('input[type="number"]').value = '';
    featureRow.querySelector('input[type="checkbox"]').checked = false;
    
    container.appendChild(featureRow);
}

function removeFeature(element) {
    const featureRows = document.querySelectorAll('.feature-row');
    if (featureRows.length > 1) {
        element.closest('.feature-row').remove();
    } else {
        alert('At least one feature is required.');
    }
}

// Form validation and Plan Name toggle
$(document).ready(function() {
    // Initialize Select2
    $('.select2-multiple').select2({
        placeholder: "Select Doctors",
        width: '100%'
    });

    $('#plan_name_dropdown').on('change', function() {
        if ($(this).val() === 'others') {
            $('#plan_name_dropdown_div').hide();
            $('#plan_name_input_div').show();
            $('#plan_name_input').attr('name', 'name').prop('required', true).val('').focus();
            $('#plan_name_dropdown').removeAttr('name').prop('required', false);
        }
    });

    $('#back_to_dropdown').on('click', function() {
        $('#plan_name_input_div').hide();
        $('#plan_name_dropdown_div').show();
        $('#plan_name_dropdown').attr('name', 'name').prop('required', true).val('');
        $('#plan_name_input').removeAttr('name').prop('required', false);
    });

    // Handle main form submit only
    $('form:not([action*="assign_doctor_from_manage"])').on('submit', function(e) {
        let isValid = true;
        
        // Check if at least one feature is selected
        const consultationFees = $('select[name="consultation_fees[]"]');
        const limitCounts = $('input[name="limit_counts[]"]');
        
        if (consultationFees.length > 0) {
            consultationFees.each(function(index) {
                if (!$(this).val()) {
                    alert('Please select consultation type for all features.');
                    isValid = false;
                    return false;
                }
            });
        }
        
        if (isValid && limitCounts.length > 0) {
            limitCounts.each(function(index) {
                if (!$(this).val() || $(this).val() < 1) {
                    alert('Please enter valid limit count (minimum 1) for all features.');
                    isValid = false;
                    return false;
                }
            });
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>

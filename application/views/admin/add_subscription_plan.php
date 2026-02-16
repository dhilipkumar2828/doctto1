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
    .add-feature {
        color: #28a745;
        cursor: pointer;
    }
</style>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Add Subscription Plan</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/subscription_plans">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                    </div>
                </div>

                <?php if (!empty($this->session->flashdata('error_message'))) { ?>
                    <div class="alert alert-danger fade in alert-dismissable">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                        <strong>Error!</strong> <?= $this->session->flashdata('error_message') ?>
                    </div>
                <?php } ?>

                <div class="ibox-content">
                    <form method="post" action="<?= base_url() ?>admin/subscription_plans/insert" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Plan Name *</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Description *</label>
                            <div class="col-sm-10">
                                <textarea name="description" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Price (₹) *</label>
                            <div class="col-sm-10">
                                <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Duration (Days) *</label>
                            <div class="col-sm-10">
                                <input type="number" name="duration_days" class="form-control" min="1" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Max Doctors Allowed *</label>
                            <div class="col-sm-10">
                                <input type="number" name="max_doctors_allowed" class="form-control" min="1" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Plan Features</label>
                            <div class="col-sm-10">
                                <div id="features-container">
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
                                </div>
                                <button type="button" class="btn btn-success btn-sm" onclick="addFeature()">
                                    <i class="fa fa-plus"></i> Add Feature
                                </button>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">Save Plan</button>
                                <a href="<?= base_url() ?>admin/subscription_plans" class="btn btn-white">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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

// Form validation
$(document).ready(function() {
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Check if at least one feature is selected
        const consultationFees = $('select[name="consultation_fees[]"]');
        const limitCounts = $('input[name="limit_counts[]"]');
        
        consultationFees.each(function(index) {
            if (!$(this).val()) {
                alert('Please select consultation type for all features.');
                isValid = false;
                return false;
            }
        });
        
        limitCounts.each(function(index) {
            if (!$(this).val() || $(this).val() < 1) {
                alert('Please enter valid limit count (minimum 1) for all features.');
                isValid = false;
                return false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Add New Terms & Conditions</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/terms_conditions" class="btn btn-primary btn-xs">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <form method="POST" action="<?= base_url() ?>admin/terms_conditions/insert" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Title <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="title" class="form-control" required 
                                       placeholder="Enter terms title (e.g., User Subscription Terms & Conditions)">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Plan Type <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="plan_type" class="form-control" required>
                                    <option value="">Select Plan Type</option>
                                    <?php foreach ($plan_types as $key => $value): ?>
                                        <option value="<?= $key ?>"><?= $value ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Subscription Plan ID</label>
                            <div class="col-sm-10">
                                <input type="number" name="subscription_plan_id" class="form-control" 
                                       placeholder="Leave empty for general terms">
                                <small class="text-muted">Optional: Specific plan ID if these terms apply to a particular plan</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Version <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="version" class="form-control" required 
                                       placeholder="e.g., 1.0, 2.1, etc.">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Effective Date <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="date" name="effective_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Content <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea name="content" class="form-control" rows="8" required 
                                          placeholder="Enter the main terms and conditions content..."></textarea>
                                <small class="text-muted">You can add detailed sections after creating the main terms</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Status</label>
                            <div class="col-sm-10">
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="is_active" value="1" checked> Active
                                </label>
                                <small class="text-muted">Uncheck to make inactive</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Save Terms & Conditions
                                </button>
                                <a href="<?= base_url() ?>admin/terms_conditions" class="btn btn-default">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Set default effective date to today
    if (!$('input[name="effective_date"]').val()) {
        $('input[name="effective_date"]').val(new Date().toISOString().split('T')[0]);
    }
});
</script>

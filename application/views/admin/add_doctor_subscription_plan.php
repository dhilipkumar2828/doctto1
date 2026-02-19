<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Add Doctor Subscription Plan</h5>
                <div class="ibox-tools">
                    <a class="collapse-link">
                        <i class="fa fa-chevron-up"></i>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <?php if($this->session->flashdata('success_message')): ?>
                    <div class="alert alert-success alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <?php echo $this->session->flashdata('success_message'); ?>
                    </div>
                <?php endif; ?>
                
                <?php if($this->session->flashdata('error_message')): ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <?php echo $this->session->flashdata('error_message'); ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?php echo base_url(); ?>admin/doctor_subscription_plans/insert" class="form-horizontal">
                    <div class="form-group" id="plan_name_dropdown_div">
                        <label class="col-sm-2 control-label">Plan Name <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select name="name" id="plan_name_dropdown" class="form-control" required>
                                <option value="">Select Plan</option>
                                <option value="Classic Plan">Classic Plan</option>
                                <option value="Advanced Plan">Advanced Plan</option>
                                <option value="Popular Plan">Popular Plan</option>
                                <option value="others">Others</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group" id="plan_name_input_div" style="display:none;">
                        <label class="col-sm-2 control-label">Plan Name <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="input-group">
                                <input type="text" id="plan_name_input" class="form-control">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-warning" id="back_to_dropdown">Back to List</button>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10">
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Price (₹) <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Duration (Days) <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="number" name="duration_days" class="form-control" min="1" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Perks</label>
                        <div class="col-sm-10">
                            <textarea name="perks" class="form-control" rows="6" placeholder="Enter plan perks and benefits (one per line)"></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <select name="is_active" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">Add Plan</button>
                            <a href="<?php echo base_url(); ?>admin/doctor_subscription_plans" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
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
});
</script>

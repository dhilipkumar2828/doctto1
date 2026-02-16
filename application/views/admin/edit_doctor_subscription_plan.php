<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>Edit Doctor Subscription Plan</h5>
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

                <form method="post" action="<?php echo base_url(); ?>admin/doctor_subscription_plans/update" class="form-horizontal">
                    <input type="hidden" name="plan_id" value="<?php echo $plan->id; ?>">
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Plan Name <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" name="name" class="form-control" value="<?php echo $plan->name; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10">
                            <textarea name="description" class="form-control" rows="3"><?php echo $plan->description; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Price (₹) <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="number" name="price" class="form-control" step="0.01" min="0" value="<?php echo $plan->price; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Duration (Days) <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="number" name="duration_days" class="form-control" min="1" value="<?php echo $plan->duration_days; ?>" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Perks</label>
                        <div class="col-sm-10">
                            <textarea name="perks" class="form-control" rows="6" placeholder="Enter plan perks and benefits (one per line)"><?php echo str_replace(['\r\n', '\n', '\r'], "\n", $plan->perks); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <select name="is_active" class="form-control">
                                <option value="1" <?php echo ($plan->is_active == 1) ? 'selected' : ''; ?>>Active</option>
                                <option value="0" <?php echo ($plan->is_active == 0) ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">Update Plan</button>
                            <a href="<?php echo base_url(); ?>admin/doctor_subscription_plans" class="btn btn-default">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Add Doctor Subscription</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/doctor_subscriptions">
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
                    <form method="post" action="<?= base_url() ?>admin/doctor_subscriptions/insert" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Doctor *</label>
                            <div class="col-sm-10">
                                <select name="doctor_id" class="form-control" required>
                                    <option value="">Select Doctor</option>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <option value="<?= $doctor->id ?>">
                                            <?= $doctor->doctor_name ?> - <?= $doctor->hospital_name ?> (<?= $doctor->mobile_number ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Subscription Plan *</label>
                            <div class="col-sm-10">
                                <select name="doctor_subscription_plan_id" class="form-control" required>
                                    <option value="">Select Plan</option>
                                    <?php foreach ($plans as $plan): ?>
                                        <option value="<?= $plan->id ?>">
                                            <?= $plan->name ?> - ₹<?= number_format($plan->price, 2) ?> (<?= $plan->duration_days ?> days)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Start Date *</label>
                            <div class="col-sm-10">
                                <input type="datetime-local" name="start_at" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Status *</label>
                            <div class="col-sm-10">
                                <select name="status" class="form-control" required>
                                    <option value="pending">Pending</option>
                                    <option value="active">Active</option>
                                    <option value="expired">Expired</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Auto Renew</label>
                            <div class="col-sm-10">
                                <input type="checkbox" name="auto_renew" value="1"> Enable auto renewal
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">PhonePe Agreement ID</label>
                            <div class="col-sm-10">
                                <input type="text" name="phonepe_agreement_id" class="form-control" placeholder="Optional">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary" type="submit">Save Subscription</button>
                                <a href="<?= base_url() ?>admin/doctor_subscriptions" class="btn btn-white">Cancel</a>
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
    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;
        
        // Check if doctor is selected
        const doctorId = $('select[name="doctor_id"]').val();
        if (!doctorId) {
            alert('Please select a doctor.');
            isValid = false;
        }
        
        // Check if plan is selected
        const planId = $('select[name="doctor_subscription_plan_id"]').val();
        if (!planId) {
            alert('Please select a subscription plan.');
            isValid = false;
        }
        
        // Check if start date is selected
        const startDate = $('input[name="start_at"]').val();
        if (!startDate) {
            alert('Please select a start date.');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>

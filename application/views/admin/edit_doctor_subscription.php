<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= $page_title ?></h5>
                <div class="ibox-tools">
                    <a href="<?= base_url() ?>admin/doctor_subscriptions">
                        <button class="btn btn-primary">BACK</button>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <?php if ($this->session->flashdata('success_message')): ?>
                    <div class="alert alert-success">
                        <?= $this->session->flashdata('success_message') ?>
                    </div>
                <?php endif; ?>

                <?php if ($this->session->flashdata('error_message')): ?>
                    <div class="alert alert-danger">
                        <?= $this->session->flashdata('error_message') ?>
                    </div>
                <?php endif; ?>

                <form class="form-horizontal" method="post" action="<?= base_url() ?>admin/doctor_subscriptions/update">
                    <input type="hidden" name="subscription_id" value="<?= $subscription->id ?>">
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Doctor *</label>
                        <div class="col-sm-10">
                            <select name="doctor_id" class="form-control" required>
                                <option value="">Select Doctor</option>
                                <?php foreach ($doctors as $doctor): ?>
                                    <option value="<?= $doctor->id ?>" <?= ($doctor->id == $subscription->doctor_id) ? 'selected' : '' ?>>
                                        <?= $doctor->doctor_name ?> (<?= $doctor->hospital_name ?>)
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
                                    <option value="<?= $plan->id ?>" <?= ($plan->id == $subscription->doctor_subscription_plan_id) ? 'selected' : '' ?>>
                                        <?= $plan->name ?> (₹<?= number_format($plan->price, 2) ?> - <?= $plan->duration_days ?> days)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Start Date *</label>
                        <div class="col-sm-10">
                            <input type="datetime-local" name="start_at" class="form-control" 
                                   value="<?= date('Y-m-d\TH:i', strtotime($subscription->start_at)) ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">End Date</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="<?= date('Y-m-d H:i:s', strtotime($subscription->end_at)) ?>" readonly>
                            <small class="text-muted">Automatically calculated based on plan duration</small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status *</label>
                        <div class="col-sm-10">
                            <select name="status" class="form-control" required>
                                <option value="">Select Status</option>
                                <option value="active" <?= ($subscription->status == 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="expired" <?= ($subscription->status == 'expired') ? 'selected' : '' ?>>Expired</option>
                                <option value="cancelled" <?= ($subscription->status == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
                                <option value="pending" <?= ($subscription->status == 'pending') ? 'selected' : '' ?>>Pending</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Auto Renew</label>
                        <div class="col-sm-10">
                            <div style="margin-top: 8px;">
                                <input type="checkbox" name="auto_renew" value="1" <?= ($subscription->auto_renew == 1) ? 'checked' : '' ?>>
                                Enable automatic renewal
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">PhonePe Agreement ID</label>
                        <div class="col-sm-10">
                            <input type="text" name="phonepe_agreement_id" class="form-control" 
                                   value="<?= $subscription->phonepe_agreement_id ?>" placeholder="Enter PhonePe agreement ID">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit">Update Subscription</button>
                            <a href="<?= base_url() ?>admin/doctor_subscriptions" class="btn btn-white">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Update end date when plan or start date changes
    $('select[name="doctor_subscription_plan_id"], input[name="start_at"]').on('change', function() {
        updateEndDate();
    });

    function updateEndDate() {
        var planId = $('select[name="doctor_subscription_plan_id"]').val();
        var startDate = $('input[name="start_at"]').val();
        
        if (planId && startDate) {
            // Get plan duration from selected option text
            var planText = $('select[name="doctor_subscription_plan_id"] option:selected').text();
            var durationMatch = planText.match(/(\d+) days/);
            
            if (durationMatch) {
                var durationDays = parseInt(durationMatch[1]);
                var start = new Date(startDate);
                var end = new Date(start.getTime() + (durationDays * 24 * 60 * 60 * 1000));
                
                var endDateStr = end.toISOString().slice(0, 16).replace('T', ' ');
                $('input[name="end_at"]').val(endDateStr);
            }
        }
    }
    });
</script>

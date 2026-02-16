<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Add Doctor to Top 10</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/doctor_top_listings?month_key=<?= $month_key ?>">
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
                    <div class="alert alert-info">
                        <strong>Month:</strong> <?= date('F Y', strtotime($month_key . '-01')) ?><br>
                        <strong>Current Count:</strong> <?= $current_count ?> / 10 doctors
                    </div>

                    <?php if ($current_count >= 10): ?>
                        <div class="alert alert-warning">
                            <strong>Maximum Limit Reached!</strong> You cannot add more than 10 doctors to the Top 10 list for this month.
                        </div>
                    <?php else: ?>
                        <form method="post" action="<?= base_url() ?>admin/doctor_top_listings/insert" class="form-horizontal">
                            <input type="hidden" name="month_key" value="<?= $month_key ?>">
                            
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Doctor *</label>
                                <div class="col-sm-10">
                                    <select name="doctor_id" class="form-control" required>
                                        <option value="">Select Doctor</option>
                                        <?php foreach ($available_doctors as $doctor): ?>
                                            <option value="<?= $doctor->id ?>">
                                                <?= $doctor->doctor_name ?> - <?= $doctor->hospital_name ?> (<?= $doctor->plan_name ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="text-muted">Only doctors with active subscriptions are available</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Position</label>
                                <div class="col-sm-10">
                                    <select name="position" class="form-control">
                                        <option value="">Auto-assign (Recommended)</option>
                                        <?php for ($i = 1; $i <= 10; $i++): ?>
                                            <option value="<?= $i ?>"><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <small class="text-muted">Leave empty to auto-assign next available position, or choose specific position</small>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Note</label>
                                <div class="col-sm-10">
                                    <textarea name="note" class="form-control" rows="3" placeholder="Optional note about this doctor's placement"></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-primary" type="submit">Add to Top 10</button>
                                    <a href="<?= base_url() ?>admin/doctor_top_listings?month_key=<?= $month_key ?>" class="btn btn-white">Cancel</a>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
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
        
        // Check if position is selected
        const position = $('select[name="position"]').val();
        if (!position) {
            alert('Please select a position.');
            isValid = false;
        }
        
        // Check if position is within valid range
        if (position < 1 || position > 10) {
            alert('Position must be between 1 and 10.');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>

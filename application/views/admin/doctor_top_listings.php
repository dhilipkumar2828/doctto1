<style>
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
    .filter-section {
        background-color: #f8f9fa;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }
    .position-badge {
        background-color: #007bff;
        color: white;
        padding: 5px 10px;
        border-radius: 50%;
        font-weight: bold;
        font-size: 14px;
    }
    .doctor-card {
        background-color: #f8f9fa;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #dee2e6;
    }
    .doctor-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Doctor Top 10 Listings</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                        <a href="<?= base_url() ?>admin/doctor_top_listings/add?month_key=<?= $month_key ?>">
                            <button class="btn btn-primary">+ Add Doctor</button>
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
                        <strong>Failed!</strong> <?= $this->session->flashdata('error_message') ?>
                    </div>
                <?php } ?>

                <!-- Filter Section -->
                <div class="filter-section">
                    <form method="get" action="<?= base_url() ?>admin/doctor_top_listings" class="form-inline">
                        <div class="form-group" style="margin-right: 15px;">
                            <label style="margin-right: 5px;">Month:</label>
                            <input type="month" name="month_key" class="form-control" value="<?= $month_key ?>" onchange="this.form.submit()">
                        </div>
                        <button type="submit" class="btn btn-primary">View</button>
                    </form>
                </div>

                <div class="ibox-content">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Top 10 Doctors for <?= date('F Y', strtotime($month_key . '-01')) ?></h4>
                            
                            <?php if ($listings): ?>
                                <form method="post" action="<?= base_url() ?>admin/doctor_top_listings/update_order">
                                    <input type="hidden" name="month_key" value="<?= $month_key ?>">
                                    
                                    <?php foreach ($listings as $listing): ?>
                                        <div class="doctor-card">
                                            <div class="doctor-info">
                                                <div>
                                                    <span class="position-badge"><?= $listing->position ?></span>
                                                    <strong style="margin-left: 10px;"><?= $listing->doctor_name ?></strong>
                                                    <br>
                                                    <small style="margin-left: 45px;"><?= $listing->hospital_name ?> | <?= $listing->mobile_number ?></small>
                                                    <?php if ($listing->plan_name): ?>
                                                        <br>
                                                        <small style="margin-left: 45px; color: #007bff;">Plan: <?= $listing->plan_name ?></small>
                                                    <?php endif; ?>
                                                    <?php if ($listing->note): ?>
                                                        <br>
                                                        <small style="margin-left: 45px; color: #6c757d;">Note: <?= $listing->note ?></small>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <input type="number" name="positions[<?= $listing->id ?>]" 
                                                           value="<?= $listing->position ?>" 
                                                           min="1" max="10" 
                                                           style="width: 60px; text-align: center; margin-right: 10px;">
                                                    <a href="<?= base_url() ?>admin/doctor_top_listings/edit/<?= $listing->id ?>" 
                                                       class="btn btn-xs btn-primary" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <a href="<?= base_url() ?>admin/doctor_top_listings/delete/<?= $listing->id ?>" 
                                                       class="btn btn-xs btn-danger" title="Remove"
                                                       onclick="return confirm('Are you sure you want to remove this doctor from Top 10?')">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">Update Positions</button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    No doctors found in Top 10 for <?= date('F Y', strtotime($month_key . '-01')) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-4">
                            <h4>Available Doctors</h4>
                            <small class="text-muted">Doctors with active subscriptions who can be added to Top 10</small>
                            
                            <?php if ($available_doctors): ?>
                                <?php foreach ($available_doctors as $doctor): ?>
                                    <div class="doctor-card">
                                        <div>
                                            <strong><?= $doctor->doctor_name ?></strong><br>
                                            <small><?= $doctor->hospital_name ?> | <?= $doctor->mobile_number ?></small><br>
                                            <small style="color: #007bff;">Plan: <?= $doctor->plan_name ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    No available doctors found. All doctors with active subscriptions are already in the Top 10 list.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Auto-submit form when month changes
    $('input[name="month_key"]').on('change', function() {
        $(this).closest('form').submit();
    });
});
</script>

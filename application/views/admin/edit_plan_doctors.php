<style>
    /* Select2 Inspinia Fixes */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #e5e6e7 !important;
        border-radius: 1px !important;
        min-height: 34px !important;
        padding: 0 5px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #1ab394 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #1ab394 !important;
        border: 1px solid #18a689 !important;
        color: #ffffff !important;
    }

    /* Label Alignment */
    .form-horizontal .control-label {
        padding-top: 7px !important;
        font-weight: 600;
    }

    .form-group {
        margin-bottom: 25px !important;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= $page_title?></h5>
                <div class="ibox-tools">
                    <a href="<?= base_url()?>admin/subscription_plans/manage_doctors/<?= $plan->id ?>">
                        <button class="btn btn-primary">BACK</button>
                    </a>
                </div>
            </div>
            
            <?php if (!empty($this->session->flashdata('success_message'))) { ?>
                <div class="alert alert-success fade in alert-dismissable" style="margin: 20px;">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    <strong>Success!</strong> <?= $this->session->flashdata('success_message') ?>
                </div>
            <?php } ?>

            <?php if (!empty($this->session->flashdata('error_message'))) { ?>
                <div class="alert alert-danger fade in alert-dismissable" style="margin: 20px;">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    <strong>Error!</strong> <?= $this->session->flashdata('error_message') ?>
                </div>
            <?php } ?>

            <div class="ibox-content">
                <form method="post" class="form-horizontal" enctype="multipart/form-data" action="<?= base_url()?>admin/subscription_plans/update_plan_doctors_bulk">
                    
                    <input type="hidden" name="plan_id" value="<?= $plan->id ?>">

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Plan Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" value="<?= $plan->name ?>" disabled>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Selected Doctors</label>
                        <div class="col-sm-10">
                            <select name="doctor_ids[]" id="doctor_ids" class="form-control select2-multiple" multiple="multiple" style="width:100%;">
                                <?php foreach ($all_doctors as $doctor): ?>
                                    <option value="<?= $doctor->id ?>" <?= in_array($doctor->id, $assigned_doctor_ids) ? 'selected' : '' ?>>
                                        <?= $doctor->doctor_name ?> (<?= $doctor->hospital_name ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-block m-b-none text-muted"><small>Add or remove doctors from this plan by selecting/deselecting them.</small></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Banner Image</label>
                        <div class="col-sm-10">
                            <?php if(!empty($current_image)): ?>
                                <img src="<?= base_url() ?>uploads/doctor_banners/<?= $current_image ?>" style="width: 200px; margin-bottom: 10px; border-radius: 4px; border: 1px solid #ddd;">
                                <br>
                            <?php endif; ?>
                            <input type="file" name="appimage" id="appimage" class="form-control">
                            <span class="help-block m-b-none text-danger"><small>Recommended: 900x400px. Leave empty to keep current image for the whole set.</small></span>
                        </div>
                    </div>

                    <div class="form-group form-actions-row">
                        <div class="col-sm-10 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit"> <i class="fa fa-save"></i> Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.select2-multiple').select2({
            placeholder: "Search and select doctors...",
            allowClear: true,
            width: '100%'
        });
    });
</script>

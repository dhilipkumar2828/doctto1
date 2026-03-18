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

    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        display: flex !important;
        flex-wrap: wrap;
        padding: 0 !important;
        margin: 0 !important;
        list-style: none;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #1ab394 !important;
        border: 1px solid #18a689 !important;
        color: #ffffff !important;
        margin: 4px 5px 4px 0 !important;
        padding: 2px 8px !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff !important;
        margin-right: 5px !important;
    }

    .select2-container--default .select2-search--inline .select2-search__field {
        margin: 0 !important;
        height: 32px !important;
        line-height: 32px !important;
        padding-left: 5px !important;
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
                <h5>
                    <?= $page_title?>
                </h5>
                <div class="ibox-tools">
                    <a href="<?= base_url()?>admin/subscription_plans/manage_doctors">
                        <button class="btn btn-primary">BACK</button>
                    </a>
                </div>
            </div>
            <div class="ibox-content">
                <form method="post" class="form-horizontal" enctype="multipart/form-data"
                    action="<?= base_url()?>admin/subscription_plans/assign_doctor_from_manage">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Select Plan</label>
                        <div class="col-sm-10">
                            <select name="plan_id" id="plan_id" class="form-control" required>
                                <option value="">-- Select Plan --</option>
                                <?php foreach ($all_plans as $ap): ?>
                                <option value="<?= $ap->id?>">
                                    <?= $ap->name?>
                                </option>
                                <?php
endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group m-t-md">
                        <label class="col-sm-2 control-label">Select Doctors</label>
                        <div class="col-sm-10">
                            <select name="doctor_ids[]" id="doctor_ids" class="form-control select2-multiple"
                                multiple="multiple" style="width:100%;" required>
                                <?php foreach ($available_doctors as $doctor): ?>
                                <option value="<?= $doctor->id?>" data-plan-id="<?= $doctor->plan_id?>">
                                    <?= $doctor->doctor_name?> (
                                    <?= $doctor->hospital_name?>) - Plan:
                                    <?= $doctor->plan_name?>
                                </option>
                                <?php
endforeach; ?>
                            </select>
                            <span class="help-block m-b-none text-muted"><small>You can select multiple doctors from the
                                    list.</small></span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Image</label>
                        <div class="col-sm-10">
                            <input type="file" name="appimage" id="appimage" class="form-control">
                            <span class="help-block m-b-none text-danger"><small>Recommended: 900x400px</small></span>
                        </div>
                    </div>

                    <div class="form-group form-actions-row">
                        <div class="col-sm-10 col-sm-offset-2">
                            <button class="btn btn-primary" id="btn_save" type="submit"> <i class="fa fa-plus"></i> Add
                                Doctor(s)</button>
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

        // Auto-fill plan choice based on first doctor selected (helper)
        $('#doctor_ids').on('change', function () {
            var selected = $(this).select2('data');
            if (selected.length > 0 && $('#plan_id').val() == "") {
                var firstDoctorId = selected[0].id;
                var planId = $('#doctor_ids option[value="' + firstDoctorId + '"]').data('plan-id');
                if (planId) {
                    $('#plan_id').val(planId);
                }
            }
        });

        $('#btn_save').click(function (e) {
            $('.error').remove();
            if ($('#plan_id').val() == '') {
                $('#plan_id').after('<span class="error" style="color:red;font-size: 14px; display:block; margin-top:5px;">Select Plan</span>');
                $('#plan_id').focus();
                return false;
            }
            if ($('#doctor_ids').val() == null || $('#doctor_ids').val().length == 0) {
                $('.select2-container').after('<span class="error" style="color:red;font-size: 14px; display:block; margin-top:5px;">Select at least one doctor</span>');
                return false;
            }
        });
    });
</script>
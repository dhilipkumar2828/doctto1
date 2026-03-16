<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= $page_title ?></h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/terms_conditions" class="btn btn-primary btn-xs">
                            <i class="fa fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <?php if($this->session->flashdata('error_message')): ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?= $this->session->flashdata('error_message') ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url() ?>admin/terms_conditions/update/<?= $terms->id ?>" class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Title <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="title" value="<?= set_value('title', $terms->title) ?>" class="form-control" required>
                                <?= form_error('title', '<span class="text-danger">', '</span>') ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Plan Type <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select name="plan_type" class="form-control" required>
                                    <?php foreach($plan_types as $key => $label): ?>
                                        <option value="<?= $key ?>" <?= ($terms->plan_type == $key) ? 'selected' : '' ?>>
                                            <?= $label ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?= form_error('plan_type', '<span class="text-danger">', '</span>') ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Subscription Plan ID</label>
                            <div class="col-sm-10">
                                <input type="number" name="subscription_plan_id" value="<?= set_value('subscription_plan_id', $terms->subscription_plan_id) ?>" class="form-control" placeholder="Leave empty for all plans">
                                <small class="text-muted">Leave empty if these terms apply to all plans of this type</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Version <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" name="version" value="<?= set_value('version', $terms->version) ?>" class="form-control" required>
                                <?= form_error('version', '<span class="text-danger">', '</span>') ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Effective Date <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="date" name="effective_date" value="<?= set_value('effective_date', $terms->effective_date) ?>" class="form-control" required>
                                <?= form_error('effective_date', '<span class="text-danger">', '</span>') ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Content <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <textarea name="content" rows="10" class="form-control" required><?= set_value('content', $terms->content) ?></textarea>
                                <?= form_error('content', '<span class="text-danger">', '</span>') ?>
                                <small class="text-muted">You can organize content into sections after saving</small>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Status</label>
                            <div class="col-sm-10">
                                <label class="checkbox-inline">
                                    <input type="checkbox" name="is_active" value="1" <?= ($terms->is_active == 1) ? 'checked' : '' ?>>
                                    Active
                                </label>
                                <small class="text-muted">Uncheck to make inactive</small>
                            </div>
                        </div>

                        <div class="hr-line-dashed"></div>

                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a href="<?= base_url() ?>admin/terms_conditions" class="btn btn-white">Cancel</a>
                                <button class="btn btn-primary" type="submit">Update Terms & Conditions</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

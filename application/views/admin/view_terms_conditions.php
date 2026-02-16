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
                        <a href="<?= base_url() ?>admin/terms_conditions/edit/<?= $terms->id ?>" class="btn btn-warning btn-xs">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="<?= base_url() ?>admin/terms_conditions/sections/<?= $terms->id ?>" class="btn btn-info btn-xs">
                            <i class="fa fa-list"></i> Manage Sections
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <?php if($this->session->flashdata('success_message')): ?>
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?= $this->session->flashdata('success_message') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if($this->session->flashdata('error_message')): ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?= $this->session->flashdata('error_message') ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><?= $terms->title ?></h3>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Plan Type:</strong> 
                                                <span class="label label-<?= $terms->plan_type == 'user' ? 'primary' : ($terms->plan_type == 'doctor' ? 'success' : 'info') ?>">
                                                    <?= ucfirst($terms->plan_type) ?> Subscriptions
                                                </span>
                                            </p>
                                            <p><strong>Version:</strong> <?= $terms->version ?></p>
                                            <p><strong>Effective Date:</strong> <?= date('F j, Y', strtotime($terms->effective_date)) ?></p>
                                            <p><strong>Status:</strong> 
                                                <span class="label label-<?= $terms->is_active ? 'success' : 'danger' ?>">
                                                    <?= $terms->is_active ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Created:</strong> <?= date('F j, Y g:i A', strtotime($terms->created_at)) ?></p>
                                            <p><strong>Last Updated:</strong> <?= date('F j, Y g:i A', strtotime($terms->updated_at)) ?></p>
                                            <?php if($terms->subscription_plan_id): ?>
                                                <p><strong>Plan ID:</strong> <?= $terms->subscription_plan_id ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="terms-content">
                                        <h4>Terms Content:</h4>
                                        <div class="well" style="white-space: pre-line; font-family: inherit;">
                                            <?= htmlspecialchars($terms->content) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Quick Actions</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="list-group">
                                        <a href="<?= base_url() ?>admin/terms_conditions/edit/<?= $terms->id ?>" class="list-group-item">
                                            <i class="fa fa-edit"></i> Edit Terms
                                        </a>
                                        <a href="<?= base_url() ?>admin/terms_conditions/sections/<?= $terms->id ?>" class="list-group-item">
                                            <i class="fa fa-list"></i> Manage Sections
                                        </a>
                                        <?php if($terms->is_active): ?>
                                            <a href="<?= base_url() ?>admin/terms_conditions/changeStatus/<?= $terms->id ?>/0" class="list-group-item text-warning">
                                                <i class="fa fa-pause"></i> Deactivate
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url() ?>admin/terms_conditions/changeStatus/<?= $terms->id ?>/1" class="list-group-item text-success">
                                                <i class="fa fa-play"></i> Activate
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= base_url() ?>admin/terms_conditions" class="list-group-item">
                                            <i class="fa fa-arrow-left"></i> Back to List
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Statistics</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row text-center">
                                        <div class="col-xs-6">
                                            <div class="huge"><?= $sections_count ?></div>
                                            <div>Sections</div>
                                        </div>
                                        <div class="col-xs-6">
                                            <div class="huge"><?= $acceptance_count ?></div>
                                            <div>Acceptances</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if(!empty($sections)): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Terms Sections</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th width="25%">Section Title</th>
                                                    <th width="50%">Content</th>
                                                    <th width="10%">Order</th>
                                                    <th width="10%">Required</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($sections as $section): ?>
                                                <tr>
                                                    <td><?= $section->id ?></td>
                                                    <td><strong><?= htmlspecialchars($section->section_title) ?></strong></td>
                                                    <td><?= htmlspecialchars(substr($section->section_content, 0, 100)) ?>...</td>
                                                    <td><?= $section->section_order ?></td>
                                                    <td>
                                                        <span class="label label-<?= $section->is_required ? 'success' : 'default' ?>">
                                                            <?= $section->is_required ? 'Yes' : 'No' ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

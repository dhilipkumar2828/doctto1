<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Manage Terms Sections: <?= $terms->title ?></h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/terms_conditions" class="btn btn-primary btn-xs">
                            <i class="fa fa-arrow-left"></i> Back to Terms
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <!-- Terms Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Plan Type:</strong> 
                            <span class="badge badge-<?= $terms->plan_type == 'user' ? 'info' : ($terms->plan_type == 'doctor' ? 'success' : 'warning') ?>">
                                <?= ucfirst($terms->plan_type) ?>
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong>Version:</strong> <?= $terms->version ?> | 
                            <strong>Effective:</strong> <?= date('d M Y', strtotime($terms->effective_date)) ?>
                        </div>
                    </div>

                    <!-- Add New Section -->
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Add New Section</h5>
                        </div>
                        <div class="ibox-content">
                            <form method="POST" action="<?= base_url() ?>admin/terms_conditions/addSection" class="form-horizontal">
                                <input type="hidden" name="terms_id" value="<?= $terms->id ?>">
                                
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Section Title <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" name="section_title" class="form-control" required 
                                               placeholder="e.g., 1. Subscription Terms">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Section Content <span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <textarea name="section_content" class="form-control" rows="4" required 
                                                  placeholder="Enter section content..."></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Order</label>
                                    <div class="col-sm-4">
                                        <input type="number" name="section_order" class="form-control" 
                                               value="<?= count($sections) + 1 ?>" min="1">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="checkbox-inline">
                                            <input type="checkbox" name="is_required" value="1" checked> Required Section
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fa fa-plus"></i> Add Section
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Sections List -->
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Current Sections (<?= count($sections) ?>)</h5>
                        </div>
                        <div class="ibox-content">
                            <?php if ($sections): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered">
                                        <thead>
                                            <tr>
                                                <th width="5%">Order</th>
                                                <th width="25%">Section Title</th>
                                                <th width="50%">Content</th>
                                                <th width="10%">Required</th>
                                                <th width="10%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sectionsTable">
                                            <?php foreach ($sections as $section): ?>
                                                <tr data-section-id="<?= $section->id ?>">
                                                    <td>
                                                        <span class="order-number"><?= $section->section_order ?></span>
                                                    </td>
                                                    <td>
                                                        <strong class="section-title"><?= $section->section_title ?></strong>
                                                    </td>
                                                    <td>
                                                        <div class="section-content">
                                                            <?= nl2br(htmlspecialchars($section->section_content)) ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if ($section->is_required): ?>
                                                            <span class="badge badge-success">Yes</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-secondary">No</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-xs btn-primary edit-section" 
                                                                    data-section='<?= json_encode($section) ?>'>
                                                                <i class="fa fa-edit"></i>
                                                            </button>
                                                            <a href="<?= base_url() ?>admin/terms_conditions/deleteSection/<?= $section->id ?>/<?= $terms->id ?>" 
                                                               class="btn btn-xs btn-danger"
                                                               onclick="return confirm('Are you sure you want to delete this section?')">
                                                                <i class="fa fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center text-muted">
                                    <p>No sections added yet. Add your first section above.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Section Modal -->
<div class="modal fade" id="editSectionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit Section</h4>
            </div>
            <form method="POST" action="<?= base_url() ?>admin/terms_conditions/updateSection">
                <div class="modal-body">
                    <input type="hidden" name="section_id" id="edit_section_id">
                    <input type="hidden" name="terms_id" value="<?= $terms->id ?>">
                    
                    <div class="form-group">
                        <label>Section Title <span class="text-danger">*</span></label>
                        <input type="text" name="section_title" id="edit_section_title" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Section Content <span class="text-danger">*</span></label>
                        <textarea name="section_content" id="edit_section_content" class="form-control" rows="6" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Order</label>
                                <input type="number" name="section_order" id="edit_section_order" class="form-control" min="1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <label class="checkbox-inline">
                                        <input type="checkbox" name="is_required" id="edit_is_required" value="1"> Required Section
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Section</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Edit section functionality
    $('.edit-section').click(function() {
        var section = $(this).data('section');
        
        $('#edit_section_id').val(section.id);
        $('#edit_section_title').val(section.section_title);
        $('#edit_section_content').val(section.section_content);
        $('#edit_section_order').val(section.section_order);
        $('#edit_is_required').prop('checked', section.is_required == 1);
        
        $('#editSectionModal').modal('show');
    });
});
</script>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>
                    <?= $title?>
                </h5>
                <div class="ibox-tools">

                    <a href="<?= base_url()?>admin/subscription_terms">
                        <button class="btn btn-primary">BACK</button>
                    </a>

                </div>
            </div>
            <div class="ibox-content">
                <form method="post" class="form-horizontal" enctype="multipart/form-data"
                    action="<?= base_url()?>admin/subscription_terms/insert">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Title</label>
                        <div class="col-sm-10">
                            <input type="text" id="title" name="title" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Description</label>
                        <div class="col-sm-10">
                            <textarea id="description" name="description" class="ck_editor_txt"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="status" name="status">
                                <option value="1">Active</option>
                                <option value="0">InActive</option>
                            </select>
                        </div>
                    </div>


                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" type="submit" id="btn_category"> <i
                                    class="fa fa-plus-circle"></i> Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $('#btn_category').click(function () {
        $('.error').remove();
        
        // Sync CKEditor data to textarea
        if (typeof editors !== 'undefined') {
            for (var id in editors) {
                if (editors.hasOwnProperty(id)) {
                    var content = editors[id].getData().trim();
                    $('#' + id).val(content);
                }
            }
        }

        var title = $('#title').val().trim();
        var description = $('#description').val().trim();
        var status = $('#status').val();

        if (title == '') {
            $('#title').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Title</span>');
            $('#title').focus();
            return false;
        }
        else if (description == '' || description == '<p>&nbsp;</p>' || description == '<p></p>') {
            // Place error after the editor UI if possible
            var editorElement = $('#description').next('.ck-editor');
            if (editorElement.length) {
                editorElement.after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;display:block;">Enter Description</span>');
            } else {
                $('#description').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;display:block;">Enter Description</span>');
            }
            return false;
        }
        else if (status == '') {
            $('#status').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Status</span>');
            $('#status').focus();
            return false;
        }
    });
</script>

<script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
<script>
    var editors = {};
    $(document).ready(function() {
        var allEditors = document.querySelectorAll('.ck_editor_txt');
        for (var i = 0; i < allEditors.length; ++i) {
            ClassicEditor.create(allEditors[i]).then(editor => {
                editors[editor.sourceElement.id] = editor;
            }).catch(error => {
                console.error(error);
            });
        }
    });
</script>

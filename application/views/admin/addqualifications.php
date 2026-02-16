<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= $title ?></h5>
                <div class="ibox-tools">
                   <a href="<?= base_url() ?>admin/qualifications">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                </div>
            </div>
            <div class="ibox-content">
                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/qualifications/insert">
                     
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Qualification Name</label>
                        <div class="col-sm-10">
                            <input  type="text" name="name" id="name" class="form-control"
                                placeholder="Enter Qualification Name">
                        </div>
                    </div>

                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="status" name="status">
                                <option value="" selected="">Select Status</option>
                                    <option value="1" >Active</option>
                                   <option value="0">InActive</option>
                            </select>
                        </div>
                    </div>

                    

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" id="add_city" type="submit"> <i class="fa fa-plus-circle"></i> Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">

  
  $('#add_city').click(function () {
        $('.error').remove();
        var errr = 0;

        if ($('#name').val() == '') {
            $('#name').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Qualification</span>');
            $('#name').focus();
            return false;
        }
       
        else if ($('#status').val() == '') {
            $('#status').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Status</span>');
            $('#status').focus();
            return false;
        }
      
    });



    // $(document).ready(function () {
    //     $('#specialisation_id').change(function () {
    //         var specialisation = $('#specialisation_id').val();
    //         if (specialisation_id != '') {
    //             $.ajax({
    //                 url: "<?php echo base_url(); ?>admin/specilaist_in/getspecialist_in",
    //                 method: "POST",
    //                 data: { specialisation_id: specialisation_id },
    //                 success: function (data) {
    //                     //alert(JSON.stringify(data));
    //                     $('#specialist_in').html(data);
    //                 }
    //             });
    //         }

    //     });


    // });


</script>
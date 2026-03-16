<style>

    .shop_title{

        font-size:17px !important;

        color: #f39c5a;

    }

</style>





<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">

        <div class="col-lg-12">

            <div class="ibox float-e-margins">

                <div class="ibox-title">

                   <!--  <h5 class="shop_title">Manage Categories - <?= $shop_name ?></h5> -->

                    <div class="ibox-tools">



                    </div>

                </div>

                <?php if (!empty($this->session->flashdata('success_message'))) { ?>

                    <div class="alert alert-success fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>

                        <?= $this->session->flashdata('success_message') ?>

                    </div>

                <?php } ?>

                <?php if (!empty($this->session->flashdata('error_message'))) { ?>

                    <div class="alert alert-danger fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>

                        <?= $this->session->flashdata('error_message') ?>

                    </div>

                <?php }
                ?>

                <div class="ibox-content">

                    <form method="post" class="form-horizontal" enctype="multipart/form-data" action="<?= base_url() ?>admin/doctors/update_symptoms/"

                          style="background: #f4f4f5;padding: 10px;border-radius: 10px;">

                        <h3>Update Symptoms</h3>
                        
                        <input type="hidden" name="id" value="<?= $doctor_symptoms->id ?>">
                        <input type="hidden" name="doctor_id" value="<?= $doctor_symptoms->doctor_id ?>">

                        <div class="form-group">

                            <div class="col-sm-3">
                                <label class="control-label">Symptom: *</label>

                                <select class="form-control js-example-basic-multiple" name="symptom_id" id="symptom_id" required onchange="getSubSymptoms(this.value)">
                                    <option value="">Select Symptom</option>
                                    <?php
                                    foreach ($symptom as $sym) {
                                        if(!in_array($sym->id,$doctor_data_symptom_ids)) {
                                        ?>
                                        <option value="<?= $sym->id ?>" <?= ($sym->id == $doctor_symptoms->symptom_id) ? "selected" : ""?>><?= $sym->name ?></option>
                                        <?php } } ?>
                                </select>
                            </div>

                           
                            
                            <div class="col-sm-3">

                                <label for="status">Status</label>
                                 <select name="status" class="form-control" required="">                                           
                                 <option value="1" <?= ($doctor_symptoms->status ==1) ? "selected" : "" ?>>active</option>
                                 <option value="0" <?= ($doctor_symptoms->status ==0) ? "selected" : "" ?>>In-active</option>
                                </select> 

                            </div>

                            <div class="col-sm-3">

                                <button class="btn btn-primary" type="submit" style="margin-top:25px">

                                    Update

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>





</div>


<script type="text/javascript">
    function getSubSymptoms(cid)
    {
        if (cid != '')
        {
            $.ajax({
                url: "<?php echo base_url(); ?>admin/vendors_shops/loadSubSymptoms",
                method: "POST",
                data: {cid: cid},
                success: function (data)
                {
                    $('#sub_symptoms').html(data);
                }
            });
        }
    }
</script>
<script type="text/javascript">

    $(document).ready(function () {

        console.log('loaded');

        $('.edit_admin_comission').on('click', function () {

            console.log($(this).attr('data-admin_com'));

            var id = $(this).attr('data-id');
            var cat_id = $(this).attr('data-cat-id');

            var subcategory_ids = $(this).attr('data-subcategory-ids');

            var admin_com = $(this).attr('data-admin_com');

            var gst = $(this).attr('data-gst');

            var status = $(this).attr('data-status');


            $('#shop_category').val(cat_id);

            $('#shop_category').val(cat_id);

            $('#admin_comm_value').val(admin_com);

            $('#admin_gst_value').val(gst);

            if (status === '1') {

                $("input[name='status'][value='1']").attr("checked", true);

            } else {

                $("input[name='status'][value='0']").attr("checked", true);

            }

        });





        $('.delete_admin_comission').on('click', function () {

            var admin_com_id = $(this).attr('data-id');
            var doctor_id = $(this).val();
            console.log(admin_com_id);

            var confirm = window.confirm('Are you sure, want to delete this admin comission ?');

            if (confirm) {

                var location = '<?= base_url() ?>admin/doctors/delete_commission?id=' + admin_com_id + "&doctor_id=" + doctor_id;

                console.log(location);

                window.location = location;

            } else {

                console.log('not confirmed');

            }

        });











    });



</script>
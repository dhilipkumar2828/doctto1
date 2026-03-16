<div class="ibox-content">
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

                            <form method="post" class="form-horizontal" enctype="multipart/form-data" action="<?= base_url() ?>admin/doctors/update_comission"

                          style="background: #f4f4f5;padding: 10px;border-radius: 10px;">

                        <h3>Update Admin Comission</h3>

                        <button onclick="history.back()" class="btn btn-primary" type="button" style="margin-top:25px">

                                    Back

                                </button>
                        <input type="hidden" name="id" value="<?=$com->id?>">
                        <input type="hidden" name="doctor_id" value="<?=$doctor_id?>">
                        <div class="form-group">



                            <div class="col-sm-3">
                                <label class="control-label">Category: *</label>
                                
                                <select class="form-control js-example-basic-multiple" name="cat_id" id="shop_category" required >
                                    <option value="">Select Category</option>
                                    <?php
                                    foreach ($categories as $cat) {
                                        ?>
                                        <option value="<?= $cat->id ?>"  <?=$cat->id == $com->cat_id?"selected":""?>><?= $cat->category_name ?></option>
                                <?php } ?>
                                </select>
                            </div>

                         

                            <!-- <div class="col-sm-3">

                                <label class="control-label">Admin Comission: *</label>

                                <input type="text" name="admin_comission" id="admin_comm_value" class="form-control" required value="<?=$com->admin_comission?>">

                            </div>

                            <div class="col-sm-3">

                                <label class="control-label">GST: *</label>

                                <input type="text" name="gst" id="admin_gst_value" class="form-control" required value="<?=$com->gst?>">

                            </div>

                            <div class="col-sm-3">

                                <label for="room_type_name">Status</label>
                                 <select name="status" class="form-control" required="">                                           
                                 <option value="1" <?=$cat->status ==1?"selected":""?>>active</option>
                                 <option value="0" <?=$cat->status ==0?"selected":""?>>In-active</option>
                                </select> 

                            </div> -->

                     




                            <div class="col-sm-3">

                                <button class="btn btn-primary" type="submit" style="margin-top:25px">

                                    Update

                                </button>

                            </div>

                        </div>





                    </form>
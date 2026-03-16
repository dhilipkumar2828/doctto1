<div class="row">

                <div class="col-lg-12">

                    <div class="ibox float-e-margins">

                        <div class="ibox-title">

                            <h5><?= $title ?></h5>

                            <div class="ibox-tools">
                                <a href="<?= base_url() ?>admin/states">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                               

                            </div>

                        </div>

                        <div class="ibox-content">

                            <form method="post" class="form-horizontal" action="<?= base_url() ?>admin/states/update">

                                <div class="form-group">

                                    <label class="col-sm-2 control-label">State Name</label>

                                    <div class="col-sm-10">
                                        <input type="hidden" name="sid" value="<?php echo $states->id; ?>">
                                        <input type="text" name="state_name" placeholder="Example: Andhra Pradesh " class="form-control" required value="<?php echo $states->state_name; ?>">

                                    </div>

                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group">

                                    <div class="col-sm-4 col-sm-offset-2">                                       

                                        <button class="btn btn-primary" type="submit">Update</button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </div>
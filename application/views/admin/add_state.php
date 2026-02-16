<div class="row">

                <div class="col-lg-12">

                    <div class="ibox float-e-margins">

                        <div class="ibox-title">

                            <h5><?= $title ?></h5>

                            <div class="ibox-tools">
                                <a href="<?= base_url() ?>admin/states">
                            <button class="btn btn-primary">BACK</button>
                        </a>

                                <?php if (!empty($this->session->flashdata('success_message'))) { ?>
                        <div class="alert alert-success fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong> Success!</strong> <?= $this->session->flashdata('success_message') ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($this->session->flashdata('error_message'))) { ?>
                        <div class="alert alert-danger fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>Failed!</strong> <?= $this->session->flashdata('error_message') ?>
                        </div>
                    <?php }
                    ?>

                            </div>

                        </div>

                        <div class="ibox-content">

                            <form method="post" class="form-horizontal" action="<?= base_url() ?>admin/states/insert">

                                <div class="form-group">

                                    <label class="col-sm-2 control-label">State Name</label>

                                    <div class="col-sm-10">

                                        <input type="text" name="state_name" id="state_name" placeholder="Example: Andhra Pradesh " class="form-control">

                                    </div>

                                </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group">

                                    <div class="col-sm-4 col-sm-offset-2">                                       

                                        <button class="btn btn-primary" id="add_state" type="submit">Save</button>

                                    </div>

                                </div>

                            </form>

                        </div>

                    </div>

                </div>

            </div>

            <script type="text/javascript">

  
  $('#add_state').click(function(){
        $('.error').remove();
            var errr=0;

      if($('#state_name').val()=='')
      {
         $('#state_name').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter State</span>');
         $('#state_name').focus();
         return false;
      }
  
 });

</script>

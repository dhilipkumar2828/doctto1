<style>
    .cat_image{
        width: 100px;
        height: 100px;
        object-fit: scale-down;
        border-radius: 10px;
        margin: 0px 5px;
    }
</style>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Settlements</h5>
                   
                         <div class="ibox-tools">
                            <a href="<?= base_url() ?>admin/dashboard">
                                <button class="btn btn-primary">BACK</button>
                            </a>
                        </div>
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
                <div class="ibox-content">

                    <div class="table-responsive">

                        <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Vendor Details</th>
                                    <th>Admin Details</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($vendor_requests as $v) {
                                    ?>
                                    <tr class="gradeX">
                                        <td><?php echo $i; ?></td>
                                        <td>
                                            <p><b>Shop Name :</b><?php
                                            $qry = $this->db->query("select * from vendor_shop where id='".$v->vendor_id."'");
                                            $vend = $qry->row();
                                             echo $vend->shop_name; ?></p>
                                             <p><b>Vendor Amount :</b><?php echo $v->vendor_amount; ?></p>
                                             <p><b>Requested Amount :</b><?php echo $v->request_amount; ?></p>
                                             <p><b>Description :</b><?php echo $v->description; ?></p>
                                             <p><b>Created Date :</b><?php echo date("d-m-Y",$v->created_at); ?></p>
                                         </td>
                                          <td>
                                            <?php if($v->mode_payment=='online'){?>
                                                 <p><b>Payment Mode :</b><?php echo $v->mode_payment; ?></p>
                                                 <p><b>TransactionID :</b><?php echo $v->transaction_id; ?></p>
                                                 <p><b>Image :</b> <img src="<?php echo base_url()."uploads/payments/".$v->image; ?>" style="width: 60px; height: 60px;"></p>
                                            <?php }else{?>
                                                <p><b>Payment Mode :</b><?php echo $v->mode_payment; ?></p>
                                                <p><b>Sender Name :</b><?php echo $v->sender_name; ?></p>
                                                <p><b>Receiver Name :</b><?php echo $v->receiver_name; ?></p>
                                            <?php } ?>
                                            <p><b>Description :</b><?php echo $v->admin_description; ?></p>
                                            <p><b>Payment Date :</b><?php echo date("d-m-Y",$v->updated_at); ?></p>
                                          </td>
                                          <td><?php if($v->status==0){ ?>
                                            <span style="color: red;"><?php echo "Pending"; ?></span>
                                             <?php }else if($v->status==1){ ?> 

                                                <span style="color: green;"><?php echo "Payment Completed"; ?></span>
                                            <?php } ?>
                                            </td>
                                        <td>
                                            <?php if($v->status==0){ ?>
                                            <a href="<?= base_url() ?>admin/settlements/add/<?= $v->id ?>">
                                                <button class="btn btn-primary">
                                                    Settlement
                                                </button>
                                            </a>
                                        <?php }else{ ?>
                                            <button disabled="" class="btn btn-primary">
                                                    Settlement
                                                </button>
                                       <?php } ?>
                                        </td>


                                    </tr>
                                    <?php
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

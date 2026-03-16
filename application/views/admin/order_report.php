<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?php echo $title; ?></h5>
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

                     <form method="post" class="form-horizontal" enctype="multipart/form-data" action="<?php echo base_url(); ?>admin/order_reports/datewiseReport">
                        <label>Start Date</label>
                        <input type="date" name="start_date" value="<?php if($start_date!=''){ echo $start_date; }?>" required="">
                        <label>End Date</label>
                        <input type="date" name="end_date" value="<?php if($end_date!=''){ echo $end_date; }?>" required="">
                        <input type="submit" value="GET">
                    </form>

                    <table class="table table-striped table-bordered table-hover dataTables-example" >
                        <thead>
                            
                            <tr>
                                <th>#</th>
                                <th>Order ID</th>
                                
                                <th>Order Amount</th>
                                <th>Vendor Commission</th>
                                <th>Admin Comission</th>
                                <th>Delivery Details</th>
                                <th>GST</th>
                                <th>Created Date</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i=1;
                            if(count($orders)>0)
                            {
                            foreach($orders as $ord){
                                
                                ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                
                                <td><?php echo $ord->id; ?></td>
                                
                                <td><?php echo $ord->total_price; ?></td>
                                <td><?php echo $ord->vendor_commission; ?></td>
                                
                                 <td><?php echo $ord->admin_commission; ?></td>
                               
                                <td> <?php echo $ord->deliveryboy_commission; ?></td>
                                <td> <?php echo $ord->gst; ?></td>
                                <td><?php echo date("d M,Y",$ord->created_at);?></td>
                                
                                
                                <td>
                                        <a href="<?php echo base_url(); ?>admin/orders/orderDetails/<?php echo $ord->session_id; ?>">
                                                <button class="btn btn-xs btn-info"><i class="fa fa-eye" aria-hidden="true"></i>  View </button></a>  
                                        </td>
                            </tr>
                            <?php $i++; } }else{?>
                            <tr>
                                <td colspan="8" style="text-align: center">
                                    <h4>No Orders Found</h4>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
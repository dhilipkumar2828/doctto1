<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Notifications</h5>
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
                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Order ID</th>
                                
                                <th>Message</th>
                                <th>Sender Name</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(count($transactions)>0)
                            {
                                $i=1;
                            foreach($transactions as $v){ 

                                /*print_r($v);*/
                                ?>
                                <tr>
                                    <td><?php echo $i;?></td>
                                    <td> <?php echo $v->order_id; ?><br><a href="<?php echo base_url(); ?>admin/orders/delivery/<?php echo $v->order_id; ?>">Assign Delivery Boy</a></td>
                                    <td><?php echo $v->message; ?></td>
                                    <td><?php

                                    if($v->vendor_id!=0)
                                    {
                                        $qry = $this->db->query("select * from vendor_shop where id='".$v->vendor_id."'");
                                        $row = $qry->row();

                                        echo "<b>Vendor :</b>".$row->shop_name;
                                    }
                                    else 
                                    {
                                        $qry1 = $this->db->query("select * from users where id='".$v->user_id."'");
                                        $row1 = $qry1->row();
                                        echo "<b>User : </b>".$row1->first_name." ".$row1->last_name;
                                    } ?></td>
                                <td><?php echo $v->created_date;?></td>>

                                </tr>
                                <!-- <tr>
                                <td><?php echo $i;?></td>
                                <td><?php
                                $or = $this->db->query("select * from orders where session_id='".$v->order_id."'");
                                        $ords = $or->row(); 
                                        if($v->vendor_id==0  || $ords->order_status==6)
                                        {
                                        ?><?php echo $ords->id; ?>
                                         <?php }else {?>
                                        <a href="<?php echo base_url(); ?>admin/orders/delivery/<?php echo $ords->session_id; ?>"><?php echo $ords->id ?></a>
                                        <?php } ?></td>
                                
                                <td><?php echo $v->message; ?></td>
                                <td><?php

                                    if($v->vendor_id!=0)
                                    {
                                        $qry = $this->db->query("select * from vendor_shop where id='".$v->vendor_id."'");
                                        $row = $qry->row();

                                        echo "<b>Vendor :</b>".$row->shop_name;
                                    }
                                    else 
                                    {
                                        $qry1 = $this->db->query("select * from users where id='".$v->user_id."'");
                                        $row1 = $qry1->row();
                                        echo "<b>User : </b>".$row1->first_name." ".$row1->last_name;
                                    } ?></td>
                                <td><?php echo date("d M,Y h:i A",strtotime($v->created_date));?></td>
                            </tr> -->
                            <?php $i++; } }else{ ?>
                            <tr>
                                <td colspan="8" style="text-align: center">
                                    <h4>No Notifications Found</h4>
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


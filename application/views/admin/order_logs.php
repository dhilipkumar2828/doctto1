<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Order Logs</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/orders">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                    </div>
x
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


                    <table class="table table-striped table-bordered table-hover dataTables-example" >
                        <thead>
                            
                            <tr>
                                <th>S.No</th>
                                <th>Activity</th>
                                <th>Date & Time</th>
                                <th>By</th>
                                <th>Who</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i=1;
                            if(count($orderlogs)>0)
                            {
                            foreach($orderlogs as $ord){
                               /*$user = $this->db->query("select * from users where id='".$ord->sender_id."'");
                               $users = $user->row();*/

                                /* $ven = $this->db->query("select * from vendor_shop where id='".$ord->vendor_id."'");
                                $vendor = $ven->row();*/

                                if($ord->action_person==0)
                                {
                                    $users = $this->db->where(array('id'=>$ord->receiver_id))->get('users')->row();
                                   
                                    $who = "Customer ";
                                    $by = $users->first_name." ".$users->last_name;
                                    $by1 = $users->phone;
                                }
                                else if($ord->action_person==1)
                                {
                                    $ven = $this->db->query("select * from vendor_shop where id='".$ord->sender_id."'");
                                    $vendor = $ven->row();
                                    $who = "Vendor ";
                                    $by = $vendor->owner_name;
                                    $by1= $vendor->mobile;
                                }
                                else if($ord->action_person==2)
                                {
                                    $del = $this->db->query("select * from admin where id=1");
                                    $delivery = $del->row();
                                    $who = "Admin ";
                                    $by=$delivery->username;
                                    $by1=$delivery->mobile;
                                }
                                

                            ?>
                            <tr>
                                <td><?php echo $i; ?></td>
                                 <td><?php echo $ord->order_status; ?></td>
                                 <td><?php echo date("d-m-Y @ h:i A",$ord->created_at);?></td>
                                 <td><p><?php echo $by; ?></p><p><?php echo $by1; ?></p></td> 
                                 <td><?php echo $who; ?></td>
                                                               
                                
                                
                            </tr>
                            <?php $i++; } }else{?>
                            <tr>
                                <td colspan="8" style="text-align: center">
                                    <h4>No Logs Found</h4>
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
<script type="text/javascript">
    
    /*function updateStatus(value,order_id)
    {
            if(value != '')
            {
             $.ajax({
              url:"<?php echo base_url(); ?>/admin/orders/changeStatus",
              method:"POST",
              data:{value:value,order_id:order_id},
              success:function(data)
              {
               if(data=='success')
               {
                alert("status changed successfully");
                window.location.href = "<?php echo base_url(); ?>vendors/orders";
               }
              }
             });
            }
    }*/

</script> 



<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Promotional Notifications</h5>
                        <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>

                        <a href="<?= base_url() ?>admin/pushnotifications/send">
                            <button class="btn btn-primary">Send Notification</button>
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
                                <th>User Type</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>User Name</th>
                                <th>Created Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if(count($push_notifications)>0)
                            {
                                $i=1;
                            foreach($push_notifications as $v){ 

                                $qry = $this->db->query("select * from users where id='".$v->user_id."'");
                                $user_row = $qry->row();
                                ?>
                                <tr>
                                    <td><?php echo $i;?></td>
                                    <td> <?php echo $v->select_user_type; ?></td>
                                    <td><?php echo $v->title; ?></td>
                                    <td><?php echo $v->description; ?></td>
                                    <td><?php if($v->user_id==0){}else{ echo $user_row->first_name." ".$user_row->last_name; }  ?></td>
                                    
                                    
                                    
                                <td><?php echo date("d M,Y h:i A",strtotime($v->created_date));?></td>

                                </tr>
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


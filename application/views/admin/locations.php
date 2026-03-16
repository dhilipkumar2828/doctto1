<div class="wrapper wrapper-content animated fadeInRight">

    <div class="row">

        <div class="col-lg-12">

            <div class="ibox float-e-margins">

                <div class="ibox-title">

                    <h5>Area</h5>

                    <div class="ibox-tools">
                        <?php 
                        $user_type = $_SESSION['admin_login']['user_type']; 
                        if($user_type=='subadmin'){ 
                                $admin_id = $_SESSION['admin_login']['id']; 
                                $adm_qry = $this->db->query("select * from sub_admin where id='".$admin_id."'");
                                $adm_row=$adm_qry->row();

                                $userpermissions  = $adm_row->permissions; 
                                $permissions = explode(",", $userpermissions);
                        if (in_array("add_area", $permissions)){ ?>
                        <a href="<?= base_url() ?>admin/locations/add">
                            <button class="btn btn-primary">+ Add Area</button>
                        </a>
                         <?php } }else{ ?>
                            <a href="<?= base_url() ?>admin/locations/add">
                            <button class="btn btn-primary">+ Add Area</button>
                        </a>
                    <?php } ?>

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
                                <th>State</th>
                                <th>City</th>
                                <th>Pincode</th>
                                <th>Area</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php

                            $i = 1;

                            foreach ($locations as $loc) {
                                ?>
                                <tr class="gradeX">
                                    <td><?= $i ?></td>
                                    <td><?php
                                        $stat = $this->db->query("select * from states where id='".$loc->state_id."'");
                                        $states = $stat->row();
                                    echo !empty($states) ? $states->state_name : 'N/A'; ?></td>
                                     <td><?php
                                        $cit = $this->db->query("select * from cities where id='".$loc->city_id."'");
                                        $cities = $cit->row();
                                    echo !empty($cities) ? $cities->city_name : 'N/A'; ?></td>


                                    <td><?= $loc->pincode; ?></td>
                                    <td><?= $loc->area; ?></td>
                                    <td><?php if($loc->status==1){echo "Active"; }else if($loc->status==0){ echo "Inactive"; } ?></td>
                                    <td>

                                         <?php
                                        if($user_type=='subadmin'){ 
                                         if (in_array("edit_area", $permissions)){ ?>

                                        <a href="<?= base_url() ?>admin/locations/edit/<?= $loc->id ?>"><button class="btn btn-xs btn-primary">Edit</button></a>
                                    <?php } if (in_array("delete_area", $permissions)){ ?>
                                        <a href="<?= base_url() ?>admin/locations/delete/<?= $loc->id; ?>"><button class="btn btn-xs btn-danger" onclick="if(!confirm('Are you sure you want to delete this Area?')) return false;">Delete</button></a>

                                    <?php }  }else{ ?>

                                        <a href="<?= base_url() ?>admin/locations/edit/<?= $loc->id ?>"><button class="btn btn-xs btn-primary">Edit</button></a>
                                        
                                        <a href="<?= base_url() ?>admin/locations/delete/<?= $loc->id; ?>"><button class="btn btn-xs btn-danger" onclick="if(!confirm('Are you sure you want to delete this Area?')) return false;">Delete</button></a>

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




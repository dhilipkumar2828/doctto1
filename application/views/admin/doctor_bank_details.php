<style>

    .shop_image{

        width: 100px;

        height: 100px; 

        object-fit: scale-down;

        margin-right:5px;

        border-radius: 10px;

        border: 1px solid #efeded;

    }

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

                    <h5 class="shop_title">Doctors </h5>

                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>


                        <?php
                        $user_type = $_SESSION['admin_login']['user_type']; 
                         if($user_type=='subadmin'){ 
                                $admin_id = $_SESSION['admin_login']['id']; 
                                $adm_qry = $this->db->query("select * from sub_admin where id='".$admin_id."'");
                                $adm_row=$adm_qry->row();

                                $userpermissions  = $adm_row->permissions; 
                                $permissions = explode(",", $userpermissions);
                        if (in_array("add_coupons", $permissions)){ ?>
                        <a href="<?= base_url() ?>admin/doctors/add">
                            <button class="btn btn-primary">+ Add Doctor</button>
                        </a>
                        <?php } }else{ ?>
                            
                        <!-- <a href="<?= base_url() ?>admin/doctors/add">-->
                        <!--    <button class="btn btn-primary">+ Add Doctor</button>-->
                        <!--</a>-->
                        <?php } ?>




                       

                    </div>

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

                <div class="ibox-content">

                   

                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr class="gradeX">
                                <th>S.No</th>
                                <th>Doctor Name</th>
                                <th>Bank Name</th>
                                <th>Account Holder Name</th>
                                <th>Account Number</th>
                                <th>IFSC Code</th>
                              
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $ks = 1;
                 
                                ?>
                                <tr class="gradeX">
                                    
                                    <td>#<?= $ks; ?></td>
                                     <td>
                                        <?= $doc_name->doctor_name ?>
                                     </td>
                                    <td><?= $doctor_bank_details->bank_name; ?></td>
                                    <td><?= $doctor_bank_details->account_holder_name; ?></td>
                                    <td><?= $doctor_bank_details->account_number; ?></td>
                                    <td><?= $doctor_bank_details->ifsc_code; ?></td>
                                </tr>

                                <?php

                                    $ks++;
                            

                            ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>





</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#myTable').DataTable();
        //Delete Confirmation Script starts here
        $(document).on("click",'.delete_item',function(){
            var del_id = $(this).val();
            swal({
                title: "Are you sure?",
                text: "You want to Delete entire data related to this!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                closeOnConfirm: false
            },
            function(){
                window.location= "<?=base_url()?>admin/doctors/delete/" + del_id;
            });
        });
        //Delete Confirmation Script ends here
    });
</script>



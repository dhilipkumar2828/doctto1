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

                    <h5 class="shop_title">Admin Login Logs</h5>

                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>

                    </div>

                </div>


                <div class="ibox-content">

                    <table class="table table-striped table-bordered table-hover dataTables-example">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>User type</th>
                                <th>Ip Address</th>
                                <th>City Name</th>
                                <th>Logged in at</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            foreach ($logs as $value) { ?>
                            <tr class="gradeX">
                                <td><?php echo $i++;?></td>
                                <td><?php echo $value->user_type; ?></td>
                                <td><?php echo $value->ip_address; ?></td>
                                <td><?php echo $value->city; ?></td>
                                <td><?php echo date('d-m-Y h:i A',$value->login_time); ?></td>
                            </tr>
                            <?php }?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>





</div>




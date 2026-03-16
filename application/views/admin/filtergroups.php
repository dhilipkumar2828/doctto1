<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= $title ?></h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/filtergroups/add">
                            <button class="btn btn-primary">+ Add Filter Group</button>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">

                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Filter Group Name</th>
                                <th>Group Values</th>
                                <th>Categories</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            if (isset($filtergroups)) {


                                foreach ($filtergroups as $filtergroup) {
                                    ?>
                                    <tr class="gradeX">
                                        <td><?= $i ?></td>
                                        <td><?= $filtergroup->filter_group_name ?>
                                        </td>
                                        <td><?= $filtergroup->group_values ?>
                                        </td>
                                        <td>
                                            <?php
                                            foreach ($filtergroup->categories as $ct) {
                                                ?>
                                                <span><?= $ct->category_name ?>,</span>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            if ($filtergroup->status == 1) {
                                                ?>
                                                <button class="btn btn-xs btn-success">
                                                    Active
                                                </button>
                                                <?php
                                            } else {
                                                ?>
                                                <button class="btn btn-xs btn-danger">
                                                    InActive
                                                </button>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url() ?>admin/filtergroups/add/<?= $filtergroup->id ?>">
                                                <button class="btn btn-xs btn-primary">
                                                    Edit
                                                </button>
                                            </a>
                                        </td>

                                    </tr>
                                    <?php
                                    $i++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


</div>


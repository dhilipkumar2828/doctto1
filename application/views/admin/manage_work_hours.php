<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= $title ?></h5>
                <div class="ibox-tools">

                </div>
            </div>
            <div class="ibox-content">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Shop Name</th>
                            <th>Week Name</th>
                            <th>Is Working Day</th>
                            <th>Open Time</th>
                            <th>Close Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        $i = 1;
                        foreach ($work_hours as $w) {
                            ?>
                            <tr>
                                <td><?= $i ?></td>
                                <td>Tippu</td>
                                <td><?= $w->week_name ?></td>
                                <td>
                                    <div>
                                        <label>
                                            <?= $w->is_working_day ?>
                                        </label>

                                    </div>

                                </td>
                                <td><?= $w->open_time ?></td>
                                <td><?= $w->close_time ?></td>
                                <td>
                                    <div>
                                        <button title="Disabled" disabled="" class="btn btn-xs btn-primary">
                                            Edit
                                        </button>
                                        <a href="#">
                                            <button title="Manage Work Hours" class="btn btn-xs btn-primary">
                                                Delete
                                            </button>
                                        </a>
                                    </div>
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

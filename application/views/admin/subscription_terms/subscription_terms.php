<style>
    .cat_image {
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
                    <h5>Subscription Terms</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url()?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>

                        <a href="<?= base_url()?>admin/subscription_terms/add">
                            <button class="btn btn-primary">+ Add New</button>
                        </a>
                    </div>

                    <?php if (!empty($this->session->flashdata('success_message'))) { ?>
                    <div class="alert alert-success fade in alert-dismissable"><a href="#" class="close"
                            data-dismiss="alert" aria-label="close">×</a>
                        <strong> Success!</strong>
                        <?= $this->session->flashdata('success_message')?>
                    </div>
                    <?php
}?>
                    <?php if (!empty($this->session->flashdata('error_message'))) { ?>
                    <div class="alert alert-danger fade in alert-dismissable"><a href="#" class="close"
                            data-dismiss="alert" aria-label="close">×</a>
                        <strong>Failed!</strong>
                        <?= $this->session->flashdata('error_message')?>
                    </div>
                    <?php
}
?>
                </div>
                <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Title</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
$i = 1;
if (count($content)) {
    foreach ($content as $v) {
?>
                                <tr class="gradeX">
                                    <td>
                                        <?= $i?>
                                    </td>
                                    <td>
                                        <?= $v->title?>
                                    </td>
                                    <td>
                                        <?= $v->description?>
                                    </td>
                                    <td>
                                        <?= ($v->status == 1) ? 'Active' : 'Inactive' ?>
                                    </td>
                                    <td>
                                        <a href="<?= base_url()?>admin/subscription_terms/edit/<?= $v->id?>">
                                            <button class="btn btn-xs btn-primary">
                                                Edit
                                            </button>
                                        </a>
                                        <a href="<?= base_url()?>admin/subscription_terms/delete/<?= $v->id?>" onclick="return confirm('Are you sure you want to delete this?')">
                                                <button class="btn btn-xs btn-danger">
                                                    Delete
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
</div>

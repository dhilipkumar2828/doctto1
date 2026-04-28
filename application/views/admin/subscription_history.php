<style>
    .status-badge {
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-active {
        background-color: #d4edda;
        color: #155724;
    }
    .status-expired {
        background-color: #f8d7da;
        color: #721c24;
    }
    .status-cancelled {
        background-color: #fff3cd;
        color: #856404;
    }
    .status-success {
        background-color: #d4edda;
        color: #155724;
    }
    .status-failed, .status-declined {
        background-color: #f8d7da;
        color: #721c24;
    }
    .status-initiated, .status-pending {
        background-color: #cce5ff;
        color: #004085;
    }
    .type-doctor {
        background-color: #e8f5e9;
        color: #2e7d32;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
    }
    .type-customer {
        background-color: #e3f2fd;
        color: #1565c0;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
    }
</style>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5><?= $page_title ?></h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                    </div>
                </div>

                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Name</th>
                                    <th>Merchant Subscription ID</th>
                                    <th>Transaction ID</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($history): ?>
                                    <?php $sn = 1; foreach ($history as $row): ?>
                                        <tr>
                                            <td><?= $sn++ ?></td>
                                            <td><strong><?= $row->name ?></strong></td>
                                            <td><?= $row->merchant_subscription_id ? $row->merchant_subscription_id : '<small class="text-muted">N/A</small>' ?></td>
                                            <td><?= $row->transaction_id ? $row->transaction_id : '<small class="text-muted">N/A</small>' ?></td>
                                            <td>₹<?= number_format((float)($row->amount ?? 0), 2) ?></td>
                                            <td>
                                                <span class="status-badge status-<?= $row->status ?? 'unknown' ?>"><?= ucfirst($row->status ?? 'unknown') ?></span>
                                            </td>
                                            <td>
                                                <span class="type-<?= str_replace(' ', '-', strtolower($row->type)) ?>"><?= $row->type ?></span>
                                            </td>
                                            <td><?= !empty($row->created_at) ? date('d M Y H:i', strtotime($row->created_at)) : '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No subscription history found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.dataTables-example').DataTable({
        pageLength: 25,
        responsive: true,
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'excel', title: 'Subscription History'},
            {extend: 'pdf', title: 'Subscription History'},
            {extend: 'print',
                customize: function (win){
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            }
        ]
    });
});
</script>

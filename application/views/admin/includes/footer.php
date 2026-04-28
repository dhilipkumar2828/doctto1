<!-- Mainly scripts -->

<script src="<?= ADMIN_ASSETS_PATH ?>assets/js/bootstrap.min.js"></script>
<script src="<?= ADMIN_ASSETS_PATH ?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>
<script src="<?= ADMIN_ASSETS_PATH ?>assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<script src="<?= ADMIN_ASSETS_PATH ?>assets/js/plugins/dataTables/datatables.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= ADMIN_ASSETS_PATH ?>assets/js/inspinia.js"></script>
<script src="<?= ADMIN_ASSETS_PATH ?>assets/js/plugins/pace/pace.min.js"></script>

<script>
    $(document).ready(function () {
        $.fn.dataTable.ext.errMode = 'none';
        $('.dataTables-example').DataTable({
            destroy: true,
            pageLength: 10,
            responsive: true,
            order: [[0, "asc"]],
            dom: '<"html5buttons"B>lTfgitp',
            buttons: [
                            //{extend: 'copy'},
                            {extend: 'csv'},
                            {extend: 'excel', title: 'ExampleFile'},
                           //{extend: 'pdf', title: 'ExampleFile'},

                            {extend: 'print',
                   customize: function (win) {
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


<link href="<?= ADMIN_ASSETS_PATH ?>assets/js/select2.min.css" rel="stylesheet" /> 
<script src="<?= ADMIN_ASSETS_PATH ?>assets/js/select2.min.js"></script>
<script>
        $(document).ready(function() {
           $('.js-example-basic-multiple').select2({
            placeholder : "Select"
           });
        });
</script> 

<script src="<?= ADMIN_ASSETS_PATH ?>assets/js/plugins/toastr/toastr.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "onclick": null,
            "showDuration": "400",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };

        <?php 
            $success = $this->session->flashdata('success_message');
            $error = $this->session->flashdata('error_message');
            $msg = $this->session->flashdata('msg');
            $login_error = $this->session->flashdata('login_error_message');
            
            // Aggressively clear from session superglobal
            $to_clear = ['success_message', 'error_message', 'msg', 'login_error_message'];
            foreach($to_clear as $key) {
                unset($_SESSION[$key]);
                if(isset($_SESSION['__ci_vars'][$key])) {
                    unset($_SESSION['__ci_vars'][$key]);
                }
            }
            
            // Force save session data immediately
            session_write_close();
        ?>

        <?php if ($success): ?>
            toastr.success("<?php echo $success; ?>");
        <?php endif; ?>

        <?php if ($error): ?>
            toastr.error("<?php echo $error; ?>");
        <?php endif; ?>

        <?php if ($msg): ?>
            toastr.info("<?php echo $msg; ?>");
        <?php endif; ?>

        <?php if ($login_error): ?>
            toastr.error("<?php echo $login_error; ?>");
        <?php endif; ?>

        // Automatically hide static alerts after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 3000);
    });
</script>
</body>
</html>

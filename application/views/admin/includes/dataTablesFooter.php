<!-- Mainly scripts -->
            <script src="<?=ADMIN_ASSETS_PATH?>assets/js/jquery-2.1.1.js"></script>
            <script src="<?=ADMIN_ASSETS_PATH?>assets/js/bootstrap.min.js"></script>
            <script src="<?=ADMIN_ASSETS_PATH?>assets/js/plugins/metisMenu/jquery.metisMenu.js"></script>
            <script src="<?=ADMIN_ASSETS_PATH?>assets/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

            <script src="<?=ADMIN_ASSETS_PATH?>assets/js/plugins/dataTables/datatables.min.js"></script>

            <!-- Custom and plugin javascript -->
            <script src="<?=ADMIN_ASSETS_PATH?>assets/js/inspinia.js"></script>
            <script src="<?=ADMIN_ASSETS_PATH?>assets/js/plugins/pace/pace.min.js"></script>

            <!-- Page-Level Scripts -->
            <script>
                $(document).ready(function(){
                    $('.dataTables-example').DataTable({
                        pageLength: 10,
                        responsive: true,
                        dom: '<"html5buttons"B>lTfgitp',
                        buttons: [
                            { extend: 'copy'},
                            {extend: 'csv'},
                            {extend: 'excel', title: 'ExampleFile'},
                            {extend: 'pdf', title: 'ExampleFile'},

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

        </body>

        </html>
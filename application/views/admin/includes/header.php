<!DOCTYPE html>
<html>

    <head>
        <?php $page_name = isset($page_name) ? $page_name : ''; ?>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>Doctto | Dashboard</title>

        <link href="<?= ADMIN_ASSETS_PATH ?>assets/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?= ADMIN_ASSETS_PATH ?>assets/font-awesome/css/font-awesome.css" rel="stylesheet">

        <!-- Toastr style -->
        <link href="<?= ADMIN_ASSETS_PATH ?>assets/css/plugins/toastr/toastr.min.css" rel="stylesheet">

        <!-- Gritter -->
        <link href="<?= ADMIN_ASSETS_PATH ?>assets/js/plugins/gritter/jquery.gritter.css" rel="stylesheet">

        <link href="<?= ADMIN_ASSETS_PATH ?>assets/css/animate.css" rel="stylesheet">
        <link href="<?= ADMIN_ASSETS_PATH ?>assets/css/style.css" rel="stylesheet">
        <link href="<?= ADMIN_ASSETS_PATH ?>assets/css/custom_css.css" rel="stylesheet">
        <link href="<?= ADMIN_ASSETS_PATH ?>assets/css/plugins/dataTables/datatables.min.css" rel="stylesheet">

        <script src="<?= ADMIN_ASSETS_PATH ?>assets/js/jquery-2.1.1.js"></script>

    </head>

    <body>
        <div id="wrapper">
            <nav class="navbar-default navbar-static-side" role="navigation">
                <div class="sidebar-collapse">
                    <!-- style="height: 100vh; overflow-y: scroll;" -->
                    <ul class="nav metismenu" id="side-menu" >
                        <li class="nav-header">
                            <div class="dropdown profile-element">
                                <span>
                                         <!--<img alt="image" class="img-circle" src="<?= ADMIN_ASSETS_PATH ?>assets/img/profile_small.jpg" />-->
                                </span>
                                <a href="<?php echo base_url(); ?>admin/dashboard" >
                                    <span class="clear">
                                        <span class="block m-t-xs">
                                           <span class="nav-label">Doctto</span>

                                        </span>
                                        <span class="text-muted text-xs block">Admin Control Panel <!-- <b class="caret"></b> -->
                                        </span>
                                    </span>
                                </a>
                                <!-- <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                    <li><a href="#">Settings</a></li>
                                    <li><a href="#">Database Backup</a></li>
                                    <li><a href="#">Login Logs</a></li>
                                    <li><a href="#">SMS Gateway Settings</a></li>
                                    <li><a href="#">Change Password</a></li>
                                    <li class="divider"></li>
                                    <li><a href="<?= base_url() ?>admin/logout">Logout</a></li>
                                </ul> -->
                            </div>
                            <div class="logo-element">
                                Doctto
                            </div>
                        </li>

                         <?php 
                        /* $user_type = $_SESSION['admin_login']['user_type']; 
                         if($user_type=='admin'){*/
                ?>

                        <li>
                            <a href="<?= base_url() ?>admin/dashboard"><i class="fa fa-th-large"></i> <span class="nav-label">Dashboard</span></a>
                        </li>
                        <li class="<?php if($page_name == 'subscriptions' || $page_name == 'doctor_subscriptions' || $page_name == 'plan_doctors' || $page_name == 'manage_plan_doctors'){ echo 'active'; } ?>">
                            <a href="#"><i class="fa fa-credit-card"></i> <span class="nav-label">Subscriptions</span><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <li class="<?= $page_name == 'subscriptions' ? 'active' : '' ?>">
                                    <a href="<?= base_url() ?>admin/subscriptions">Subscriptions</a>
                                </li>
                                <li class="<?= $page_name == 'doctor_subscriptions' ? 'active' : '' ?>">
                                    <a href="<?= base_url() ?>admin/doctor_subscriptions">Subscription Plans List</a>
                                </li>
                                <li class="<?php if($page_name == 'plan_doctors' || $page_name == 'manage_plan_doctors'){ echo 'active'; } ?>">
                                    <a href="<?= base_url() ?>admin/subscription_plans/manage_doctors/1">Plan Doctors</a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="<?= base_url() ?>admin/loginLogs"><i class="fa fa-users"></i> <span class="nav-label">Admin Login Logs </span></a>
                        </li>

                     


                        <li class="<?= $page_name == 'users' ? 'active' : '' ?>">
                            <a href="<?= base_url() ?>admin/users"><i class="fa fa-user"></i> <span class="nav-label">Users</span></a>
                        </li>

                       
                        <!--<li>-->
                        <!--    <a href="#"><i class="fa fa-table"></i> <span class="nav-label">Master</span><span class="fa arrow"></span></a>-->
                        <!--    <ul class="nav nav-second-level collapse in">-->
                        <!--       </ul>-->
                        <!--</li>      -->
                                <li class="<?php  if($page_name == 'states' || $page_name == 'cities' || $page_name == 'locations' || $page_name == 'pincodes'){ echo 'active'; } ?>">
                                    <a href="<?= base_url() ?>admin/states"><i class="fa fa-table"></i>Locations
                                        <span class="fa arrow"></span>
                                    </a>
                                    <ul class="nav nav-second-level collapse">
                                            <li  class="<?=  $page_name == 'states' ? 'active' : '' ?>">
                                                <a href="<?= base_url() ?>admin/states">States</a>
                                            </li>
                                            <li class="<?= $page_name == 'cities' ? 'active' : '' ?>">
                                                <a href="<?= base_url() ?>admin/cities">Cities</a>
                                            </li>
                                            <li class="<?= $page_name == 'pincodes' ? 'active' : '' ?>">
                                                <a href="<?= base_url() ?>admin/pincodes">Pincode</a>
                                            </li>
                                            
                                            <!-- <li class="<?= $page_name == 'locations' ? 'active' : '' ?>">
                                                <a href="<?= base_url() ?>admin/locations">Areas</a>
                                            </li> -->
                                    </ul>
                                </li>


                            <!--<li class="<?php  if($page_name == 'prescription' || $page_name == 'handwritten_prescription'){ echo 'active'; } ?>">-->
                            <!--        <a href="<?= base_url() ?>admin/prescription">Prescription-->
                            <!--            <span class="fa arrow"></span>-->
                            <!--        </a>-->
                            <!--        <ul class="nav nav-second-level collapse">-->
                            <!--                <li  class="<?=  $page_name == 'states' ? 'active' : '' ?>">-->
                            <!--                    <a href="<?= base_url() ?>admin/prescription">e-Prescription</a>-->
                            <!--                </li>-->
                                            <!--<li class="<?= $page_name == 'cities' ? 'active' : '' ?>">-->
                                            <!--    <a href="<?= base_url() ?>admin/handwritten_prescription">Handwritten</a>-->
                                            <!--</li>-->
                                           
                                       
                            <!--        </ul>-->
                            <!--    </li>-->
                                
                                
                              
                        

                        


                        

                         <li class="<?php  if($page_name == 'doctors' || $page_name == 'doctor_banners' || $page_name == 'doctor_categories' || $page_name == 'symptom' || $page_name == 'doctor_sub_categories' || $page_name == 'videos' || $page_name == 'ads' || $page_name == 'doctors_appointment' || $page_name == 'doctor_payments' || $page_name == 'doctors_specialisation' || $page_name == 'specialist_in' || $page_name == 'qualifications' ){ echo 'active'; } ?>">
                            <a href="<?= base_url() ?>admin/doctor_banners"><i class="fa fa-table"></i><span class="nav-label">Doctors Master</span>  
                                <span class="fa arrow"></span>
                            </a>
                            <ul class="nav nav-second-level collapse">
                                    <li  class="<?=  $page_name == 'doctor_banners' ? 'active' : '' ?>">
                                        <a href="<?= base_url() ?>admin/doctor_banners">Banners</a>
                                    </li>

                                    <li  class="<?=  $page_name == 'ads' ? 'active' : '' ?>">
                                        <a href="<?= base_url() ?>admin/ads">Ads Management</a>
                                    </li>

                                   <!--  <li class="<?= $page_name == 'doctor_categories' ? 'active' : '' ?>">
                                            <a href="<?= base_url() ?>admin/doctor_categories">Categories</a>
                                    </li> -->

                                    <li class="<?= $page_name == 'symptom' ? 'active' : '' ?>">
                                        <a href="<?= base_url() ?>admin/symptom">Symptom Category</a>
                                    </li>

                                   

                                    <li  class="<?=  $page_name == 'doctors' ? 'active' : '' ?>">
                                        <a href="<?= base_url() ?>admin/doctors">Doctors</a>
                                    </li>

                                    <li  class="<?=  $page_name == 'doctors_appointment' ? 'active' : '' ?>">
                                        <a href="<?= base_url() ?>admin/doctors_appointments">Doctors Appointment</a>
                                    </li>


                                    
                                     <li  class="<?=  $page_name == 'doctor_payments' ? 'active' : '' ?>">
                                        <a href="<?= base_url() ?>admin/doctor_payments">Doctors Payments</a>
                                    </li>
                                    <li  class="<?=  $page_name == 'doctors_specialisation' ? 'active' : '' ?>">
                                        <a href="<?= base_url() ?>admin/doctors_specialisation">Doctors Specialisation</a>
                                    </li>
                                     <li  class="<?=  $page_name == 'specialist_in' ? 'active' : '' ?>"><a href="<?= base_url() ?>admin/specialist_in">Doctors Specialist In</a></li>

                                    <li class="<?= $page_name == 'qualifications' ? 'active' : '' ?>"><a href="<?= base_url() ?>admin/qualifications">Qualification</a></li>

                            </ul>
                        </li>

                        <li class="<?= $page_name == 'payment_invoice' ? 'active' : '' ?>">
                            <a href="<?= base_url() ?>admin/tax_details"><i class="fa fa-user"></i> <span class="nav-label">Payment Tax Invoice Details</span></a>
                        </li>
                        
                        <li class="<?= $page_name == 'welcome_pages' ? 'active' : '' ?>">
                            <a href="<?= base_url() ?>admin/welcome_pages"><i class="fa fa-user"></i> <span class="nav-label">Welcome Pages</span></a>
                        </li>

                     
                        <!--  <li class="<?= $page_name == 'user_reports' ? 'active' : '' ?>">
                            <a href="<?= base_url() ?>admin/user_reports"><i class="fa fa-user"></i> <span class="nav-label">User Reports</span></a>
                        </li>  -->


                        <li class="<?= $page_name == 'content' ? 'active' : '' ?>">
                            <a href="<?= base_url() ?>admin/content"><i class="fa fa-book"></i> <span class="nav-label">CMS Pages</span></a>
                        </li>

                        <li class="<?= $page_name == 'settings' ? 'active' : '' ?>">
                            <a href="<?= base_url() ?>admin/settings"><i class="fa fa-cog"></i> <span class="nav-label">Settings</span></a>
                        </li>

                        <li class="<?= $page_name == 'Dbbackup' ? 'active' : '' ?>">
                            <a href="<?= base_url() ?>admin/Dbbackup"><i class="fa fa-cog"></i> <span class="nav-label">Database Backup</span></a>

                        </li>
                        <!-- <li class="<?= $page_name == 'contactus' ? 'active' : '' ?>">
                            <a href="<?= base_url() ?>admin/contactus"><i class="fa fa-user"></i> <span class="nav-label">User Contact Us</span></a> -->
                    </li>

                    <?php /*}else if($user_type=='subadmin'){}*/ ?>

                </div>
            </nav>

            <div id="page-wrapper" class="gray-bg dashbard-1">
                <div class="row border-bottom">
                    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                        <div class="navbar-header">
                            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                            <!-- <form role="search" class="navbar-form-custom" action="search_results.html">
                                <div class="form-group">
                                    <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
                                </div>
                            </form> -->
                        </div>
                        <ul class="nav navbar-top-links navbar-right">
                          
                            <li>
                                <a href="<?= base_url() ?>admin/logout">
                                    <i class="fa fa-sign-out"></i> Log out
                                </a>
                            </li>
                        </ul>

                    </nav>


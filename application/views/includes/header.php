<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="robots" content="index,follow">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Doctto</title>
        <!--// Boostrap v4 //-->
        <link rel="stylesheet" href="<?=base_url()?>assets/vendor/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?=base_url()?>assets/vendor/css/magnific.popup.min.css">
        <link rel="stylesheet" href="<?=base_url()?>assets/vendor/css/animate.min.css">
        <link rel="stylesheet" href="<?=base_url()?>assets/vendor/css/owl.carousel.min.css">
        <link rel="stylesheet" href="<?=base_url()?>assets/vendor/css/owl.carousel.default.min.css">
        <link rel="stylesheet" href="<?=base_url()?>assets/fonts/font_awesome/css/all.css">
        <link rel="stylesheet" href="<?=base_url()?>assets/fonts/flat_icons/flaticon.css">
        <link rel="stylesheet" href="<?=base_url()?>assets/css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link rel="stylesheet" href="<?=base_url()?>assets/css/f5.css">
        <link rel="icon" href="<?=base_url()?>assets/img/bg/favicon.png" type="image/png" sizes="16x16">
    </head>
    <body data-spy="scroll" data-target="#fixedNavbar">
        <div class="page-wrapper" id="wrapper">
            <header class="header fixed-top" id="header">
                <div id="nav-menu-wrap">
                    <div class="container">
                        <nav class="navbar navbar-expand-lg p-0">
                            <a class="navbar-brand" title="Home" href="index.php">
                                <img src="<?=base_url()?>assets/img/bg/doctlogos.png" alt="Logo White" class="img-fluid logo-transparent logo-img">
                                <img src="<?=base_url()?>assets/img/bg/doctlogos.png" alt="Logo White" class="img-fluid logo-normal">
                            </a>
                            <button class="navbar-toggler" type="button" data-toggle="collapse" onClick="openNav()"
                            aria-controls="fixedNavbar" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="togler-icon-inner">
                                <span class="line-1"></span>
                                <span class="line-2"></span>
                                <span class="line-3"></span>
                            </span>
                            </button>
                            
                            <div id="mySidenav" class="sidenav visible-sm visible-xs">
                                <a href="javascript:void(0)" class="closebtn" onClick="closeNav()">&times;</a>
                                <ul type="none">
                                    <li class="nav-item">
                                        <a class="nav-link menu-link" onclick="closeNav()"href="<?=base_url()?>" data-scroll-nav="1">Home</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link" onclick="closeNav()" href="<?=base_url()?>" data-scroll-nav="2">About</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link" onclick="closeNav()" href="#" data-scroll-nav="3">Features</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link" onclick="closeNav()"href="#" data-scroll-nav="5">Why Choose us</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link"onclick="closeNav()" href="#" data-scroll-nav="4">App Screens</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link"onclick="closeNav()" href="#" data-scroll-nav="7">Contact</a>
                                    </li>
                                    
                                </li>
                            </ul>
                        </div>
                        <div class="collapse navbar-collapse main-menu justify-content-end" id="fixedNavbar">
                            <ul class="navbar-nav">
                                <li class="nav-item">
                                    <a class="nav-link menu-link" href="<?=base_url()?>" data-scroll-nav="1">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link menu-link" href="#" data-scroll-nav="2">About</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link menu-link" href="#" data-scroll-nav="3">Features</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link menu-link" href="#" data-scroll-nav="5">Why Choose us</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link menu-link" href="#" data-scroll-nav="4">App Screens</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link menu-link" href="#" data-scroll-nav="7">Contact</a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </header>

        <ul class="leftTwoBtncontainer d-none d-md-block d-lg-block">
      <li><a href="https://api.whatsapp.com/send?phone=+919490067418&amp;text=Hi" target="_blank" class="whatsappICon"><img src="<?=base_url()?>assets/img/bg/whatsapp.png" alt=""></a></li>
      <br>
      <li><a href="tel:+91 9490067418" class="PhoneappICon"> <img src="<?=base_url()?>assets/img/bg/telephone.png" alt=""> </a></li>
    </ul>
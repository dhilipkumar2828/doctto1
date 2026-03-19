<style>
    .cat_image{
        width: 100px;
        height: 100px;
        object-fit: scale-down;
        border-radius: 10px;
        margin: 0px 5px;
    }
</style>
<style>
  /*.responsive-tabs {
  margin-top:20px;
}

.responsive-tabs-container .tab-content {
  padding:10px 20px;
  border:0px solid #ddd;
  border-top:none;
}

.responsive-tabs-container[class*="accordion-"] .tab-pane {
  margin-bottom: 15px;
}*/

  .responsive-tabs-container[class*="accordion-"] .accordion-link {
    display: none;
    margin-bottom: 10px;
    padding: 10px 15px;
    background-color: #f5f5f5;
    border-radius: 3px;
    border: 1px solid #ddd;
    color: #333;
  }

  /*.responsive-tabs-container[class*="accordion-"] .accordion-link.active {
    border-bottom: medium none;
    border-bottom-left-radius: 0;
    border-bottom-right-radius: 0;
    color: #ff6600;
}*/

  @media (max-width: 767px) {
    .responsive-tabs-container.accordion-xs .nav-tabs {
      display: none;
    }

    .responsive-tabs-container.accordion-xs .accordion-link {
      display: block;
    }

    .responsive-tabs-container[class*="accordion-"] .tab-pane {
      border: 1px solid #ddd;
      border-top: none;
      border-top-left-radius: 0;
      border-top-right-radius: 0;
      border-width: medium 1px 1px;
      margin-bottom: 10px;
      margin-top: -10px;
      padding: 10px 10px 0;
    }
  }

  @media (min-width: 768px) and (max-width: 991px) {
    .responsive-tabs-container.accordion-sm .nav-tabs {
      display: none;
    }

    .responsive-tabs-container.accordion-sm .accordion-link {
      display: block;
    }

    .responsive-tabs-container[class*="accordion-"] .tab-pane {
      border: 1px solid #ddd;
      border-top: none;
      border-top-left-radius: 0;
      border-top-right-radius: 0;
      border-width: medium 1px 1px;
      margin-bottom: 10px;
      margin-top: -10px;
      padding: 10px 10px 0;
    }
  }

  @media (min-width: 992px) and (max-width: 1199px) {
    .responsive-tabs-container.accordion-md .nav-tabs {
      display: none;
    }

    .responsive-tabs-container.accordion-md .accordion-link {
      display: block;
    }
  }

  @media (min-width: 1200px) {
    .responsive-tabs-container.accordion-lg .nav-tabs {
      display: none;
    }

    .responsive-tabs-container.accordion-lg .accordion-link {
      display: block;
    }
  }
</style>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Prescription Page</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url() ?>admin/dashboard">
                            <button class="btn btn-primary">BACK</button>
                        </a>

                        
                              <a href="<?= base_url() ?>admin/doctors_appointments/view_pdf/<?= $val_id ?>" target="_blank">  
                            <button class="btn btn-primary">+ e-Prescription </button>  
                            
                        </a>
                           <a href="<?= base_url() ?>admin/doctors_appointments/labtest_pdf/<?= $val_id ?>" target="_blank">  
                            <button class="btn btn-primary">+ Lab tests </button>  
                            
                        </a>
                         <input type="hidden" name="id" value="<?= $appointment_id->id ?>"> 
                    </div>

                   <!--  <?php if (!empty($this->session->flashdata('success_message'))) { ?>
                        <div class="alert alert-success fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong> Success!</strong> <?= $this->session->flashdata('success_message') ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($this->session->flashdata('error_message'))) { ?>
                        <div class="alert alert-danger fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <strong>Failed!</strong> <?= $this->session->flashdata('error_message') ?>
                        </div>
                    <?php }
                    ?> -->
                </div>
                <!-- <?php include_once('crud_alerts.php') ?> -->
<div class="wrapper wrapper-content animated fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox float-e-margins">
        <div class="ibox-title">
           <?php if(!empty($patient_prescription_id->prescription_type)){ ?>
          <h5>Prescription Type :
            <?php echo $patient_prescription_id->prescription_type; ?>
          </h5>
        <?php }else{ ?>
         <h5 style="color: red;">No Data Found</h5>
        <?php } ?>
          <!--<div class="ibox-tools">-->
          <!--  <a class="btn btn-primary btn-xs" href="<?= base_url() ?>admin/vendors">-->
          <!--    <i class="fa fa-chevron-left" aria-hidden="true"></i>Back-->
          <!--  </a>-->
          <!--</div>-->
        </div>
      <!--   <?php if (!empty($this->session->flashdata('success_message'))) { ?>

          <div class="alert alert-success fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert"
              aria-label="close">×</a>

            <strong> Success!</strong>
            <?= $this->session->flashdata('success_message') ?>

          </div>

        <?php } ?> -->

       <!--  <?php if (!empty($this->session->flashdata('error_message'))) { ?>

          <div class="alert alert-danger fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert"
              aria-label="close">×</a>

            <strong>Failed!</strong>
            <?= $this->session->flashdata('error_message') ?>

          </div>

        <?php }
        ?> -->
        
     <?php   if($patient_prescription_id->prescription_type == 'prescription'){?>
        
        <div class="col-sm-12">

          <ul class="nav nav-tabs responsive-tabs">
            <li class="active"><a href="#eprescription">ePrescription</a></li>
            <li><a href="#manual">Manual Prescription</a></li>
            <li><a href="#lab">Lab Tests</a></li>

          </ul>

          <div class="tab-content">
            <div class="tab-pane active" id="eprescription">
              <div class="wrapper wrapper-content animated fadeInRight">



                  <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                     <th>Medication Name</th>
                                     <th>Dosage</th>
                                     <th>Duration</th>
                                     <th>Repeat</th>
                                     <th>Time Of The Day</th>
                                     <th>To be Taken</th>
                                     <th>Precription Created At</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($eprescription as $ep) {
                                    ?>
                                    <tr class="gradeX">
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $ep->medication_name; ?></td>
                                        <td><?php echo $ep->dosage; ?></td>
                                        <td><?php echo $ep->duration; ?></td>
                                        <td><?php echo $ep->repeat; ?></td>
                                        <td><?php echo $ep->time_of_the_day; ?></td>
                                        <td><?php echo $ep->to_be_taken; ?></td>
                                        <td><?php echo date('d M Y',$ep->prescription_created_at); ?></td>
                                        
                                        <!--<td> -->
                                        <!--    <a href="<?= base_url() ?>admin/prescription/delete/<?= $ep->id ?>">-->
                                        <!--        <button title="Delete Category" class="btn btn-xs btn-danger">-->
                                        <!--            Delete-->
                                        <!--        </button>-->
                                        <!--    </a>-->
                                        <!--</td>-->


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

            <div class="tab-pane" id="manual">
              <div class="ibox-content">
                <div class="table-responsive">
                   <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>#</th>

                                     <th>Manual Prescription</th>
                                     <th>Precription Created At</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                    <tr class="gradeX">
                                        <td><?php echo 1; ?></td>
                                          <td>
                                            <img class="cat_image" align="left" src="<?php echo base_url(); ?>uploads/prescription/<?php echo $manual_prescription->manual_prescription; ?>" >
                                        </td>
                                        <?php if($manual_prescription->manual_prescription){?>
                                        <td><?php echo date('d M Y',$manual_prescription->created_at); ?></td>
                                        <?php } ?>
                                        <!--<td>-->
                                        <!--    <a href="<?= base_url() ?>admin/prescription/delete/<?= $v->id ?>">-->
                                        <!--        <button title="Delete Category" class="btn btn-xs btn-danger">-->
                                        <!--            Delete-->
                                        <!--        </button>-->
                                        <!--    </a>-->
                                        <!--</td>-->
                                    </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                </div>

              </div>
            </div>

            <div class="tab-pane" id="lab">
              <div class="ibox-content">
                <div class="table-responsive">
                   <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                     <th>Lab Tests Name</th>
                                     <th>Test Description</th>
                                     <th>Created At</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($lab_tests as $v) {
                                    ?>
                                    <tr class="gradeX">
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $v->lab_test_name; ?></td>
                                        <td><?php echo $v->lab_test_description; ?></td>
                                        <td><?php echo date('d M Y',$v->lab_test_created_at); ?></td>
                                        <!--<td>-->
                                        <!--     <a href="<?= base_url() ?>admin/doctors_appointments/view_pdf/<?= $val_id ?>" target="_blank">  -->
                                        <!--     <button class="btn btn-primary">+ Generate e-Prescription</button>-->
                                        <!--</td>-->


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
            </div>




          </div>

        </div>
     <?php } else if($patient_prescription_id->prescription_type == 'diagnosis'){?>
         <div class="col-sm-12">

          <ul class="nav nav-tabs responsive-tabs">
            <li class="active"><a href="#chief_complaints">Chief Complaints</a></li>
            <li><a href="#diagnosis">Diagnosis</a></li>
            <li><a href="#advice">Advice</a></li>
            <li><a href="#investigation">Investigation</a></li>
            <li><a href="#follow">Follow Up</a></li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane active" id="chief_complaints">
              <div class="wrapper wrapper-content animated fadeInRight">



                  <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                     <th>Chief Complaints</th>
                                     <th>Created At</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1; ?>
                                    <tr class="gradeX">
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $diagnosis->chief_complaints; ?></td>
                                        <?php if($diagnosis->chief_complaints){?>
                                        <td><?php echo date('d M Y',$diagnosis->created_at); ?></td>
                                        <?php } ?>
                                        <!--<td> -->
                                        <!--    <a href="<?= base_url() ?>admin/prescription/delete/<?= $diagnosis->id ?>">-->
                                        <!--        <button title="Delete Category" class="btn btn-xs btn-danger">-->
                                        <!--            Delete-->
                                        <!--        </button>-->
                                        <!--    </a>-->
                                        <!--</td>-->


                                    </tr>
                            </tbody>
                        </table>
                    </div>

                </div>


              </div>
            </div>

            <div class="tab-pane" id="diagnosis">
              <div class="ibox-content">
                <div class="table-responsive">
                   <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                     <th>Diagnosis</th>
                                     <th>Created At</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1; ?>
                                    <tr class="gradeX">
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $diagnosis->diagnosis; ?></td>
                                        <?php if($diagnosis->diagnosis){?>
                                        <td><?php echo date('d M Y',$diagnosis->created_at); ?></td>
                                        <?php } ?>
                                        <!--<td> -->
                                        <!--    <a href="<?= base_url() ?>admin/prescription/delete/<?= $diagnosis->id ?>">-->
                                        <!--        <button title="Delete Category" class="btn btn-xs btn-danger">-->
                                        <!--            Delete-->
                                        <!--        </button>-->
                                        <!--    </a>-->
                                        <!--</td>-->


                                    </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                </div>

              </div>
            </div>

            <div class="tab-pane" id="advice">
              <div class="ibox-content">
                <div class="table-responsive">
                   <div class="ibox-content">

                    <div class="table-responsive">
                         <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                     <th>Advice</th>
                                     <th>Created At</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1; ?>
                                    <tr class="gradeX">
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $diagnosis->advice; ?></td>
                                        <?php if($diagnosis->advice){?>
                                        <td><?php echo date('d M Y',$diagnosis->created_at); ?></td>
                                        <?php } ?>
                                        <!--<td> -->
                                        <!--    <a href="<?= base_url() ?>admin/prescription/delete/<?= $diagnosis->id ?>">-->
                                        <!--        <button title="Delete Category" class="btn btn-xs btn-danger">-->
                                        <!--            Delete-->
                                        <!--        </button>-->
                                        <!--    </a>-->
                                        <!--</td>-->


                                    </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                </div>
              </div>
            </div>
            
               <div class="tab-pane" id="investigation">
              <div class="ibox-content">
                <div class="table-responsive">
                   <div class="ibox-content">

                    <div class="table-responsive">
                         <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                     <th>Investigation</th>
                                     <th>Created At</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1; ?>
                                    <tr class="gradeX">
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $diagnosis->investigation; ?></td>
                                        <?php if($diagnosis->investigation){?>
                                        <td><?php echo date('d M Y',$diagnosis->created_at); ?></td>
                                        <?php } ?>
                                        <!--<td> -->
                                        <!--    <a href="<?= base_url() ?>admin/prescription/delete/<?= $diagnosis->id ?>">-->
                                        <!--        <button title="Delete Category" class="btn btn-xs btn-danger">-->
                                        <!--            Delete-->
                                        <!--        </button>-->
                                        <!--    </a>-->
                                        <!--</td>-->


                                    </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                </div>
              </div>
            </div>
            
               <div class="tab-pane" id="follow">
              <div class="ibox-content">
                <div class="table-responsive">
                   <div class="ibox-content">

                    <div class="table-responsive">
                             <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                     <th>Follow Up</th>
                                     <th>Created At</th>
                                    <!--<th>Action</th>-->
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $i = 1; ?>
                                    <tr class="gradeX">
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $diagnosis->followup; ?></td>
                                        <?php if($diagnosis->followup){?>
                                        <td><?php echo date('d M Y',$diagnosis->created_at); ?></td>
                                        <?php } ?>
                                        <!--<td> -->
                                        <!--    <a href="<?= base_url() ?>admin/prescription/delete/<?= $diagnosis->id ?>">-->
                                        <!--        <button title="Delete Category" class="btn btn-xs btn-danger">-->
                                        <!--            Delete-->
                                        <!--        </button>-->
                                        <!--    </a>-->
                                        <!--</td>-->


                                    </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                </div>
              </div>
            </div>




          </div>

        </div>
        <?php } ?>
      </div>
    </div>
  </div>
</div>
              
            </div>
        </div>
    </div>
</div>
<script>


  ! function ($) {
    "use strict";
    var a = {
      accordionOn: ["xs"]
    };
    $.fn.responsiveTabs = function (e) {
      var t = $.extend({}, a, e),
        s = "";
      return $.each(t.accordionOn, function (a, e) {
        s += " accordion-" + e
      }), this.each(function () {
        var a = $(this),
          e = a.find("> li > a"),
          t = $(e.first().attr("href")).parent(".tab-content"),
          i = t.children(".tab-pane");
        a.add(t).wrapAll('<div class="responsive-tabs-container" />');
        var n = a.parent(".responsive-tabs-container");
        n.addClass(s), e.each(function (a) {
          var t = $(this),
            s = t.attr("href"),
            i = "",
            n = "",
            r = "";
          t.parent("li").hasClass("active") && (i = " active"), 0 === a && (n = " first"), a === e.length - 1 && (r = " last"), t.clone(!1).addClass("accordion-link" + i + n + r).insertBefore(s)
        });
        var r = t.children(".accordion-link");
        e.on("click", function (a) {
          a.preventDefault();
          var e = $(this),
            s = e.parent("li"),
            n = s.siblings("li"),
            c = e.attr("href"),
            l = t.children('a[href="' + c + '"]');
          s.hasClass("active") || (s.addClass("active"), n.removeClass("active"), i.removeClass("active"), $(c).addClass("active"), r.removeClass("active"), l.addClass("active"))
        }), r.on("click", function (t) {
          t.preventDefault();
          var s = $(this),
            n = s.attr("href"),
            c = a.find('li > a[href="' + n + '"]').parent("li");
          s.hasClass("active") || (r.removeClass("active"), s.addClass("active"), i.removeClass("active"), $(n).addClass("active"), e.parent("li").removeClass("active"), c.addClass("active"))
        })
      })
    }
  }(jQuery);


  $('.responsive-tabs').responsiveTabs({
    accordionOn: ['xs', 'sm']
  });
</script>

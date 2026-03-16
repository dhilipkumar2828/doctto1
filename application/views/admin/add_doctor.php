<style>
    .category_comm_span{
        top: -5px;
        position: relative;
        left: 10px;
    }
    .cat_commission{
        top: -5px;
        position: relative;
        left: 21px;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5><?= $title ?></h5>
                <div class="ibox-tools">
                  <a href="<?= base_url() ?>admin/doctors">
                            <button class="btn btn-primary">BACK</button>
                        </a>
                </div>
            </div>

            <?php if (!empty($this->session->flashdata('error_message'))) { ?>

                    <div class="alert alert-danger fade in alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>

                        <strong>Failed!</strong> <?= $this->session->flashdata('error_message') ?>

                    </div>
                <?php } ?>
                
            <div class="ibox-content test">
                <form method="post" class="form-horizontal" enctype="multipart/form-data" action="<?= base_url() ?>admin/doctors/insert">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Hospital Name</label>
                        <div class="col-sm-10">
                            <input type="text" id="hospital_name" name="hospital_name" class="form-control" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Hospital Image</label>
                        <div class="col-sm-10">
                            <input type="file" id="hospital_image" name="hospital_image" class="form-control"> 
                            <p>Make sure image Width : 500 px & Height: 500 px</p>
                        </div> 

                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Doctor Name</label>
                        <div class="col-sm-10">
                            <input type="text" id="doctor_name" name="doctor_name" class="form-control" value="">
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label">Doctor Image</label>
                        <div class="col-sm-10">
                            <input type="file" id="doctor_image" name="doctor_image" class="form-control">
                            <p>Make sure image Width : 300 px & Height: 300 px</p>
                        </div>

                    </div>     
                    

                    <div class="form-group">
                        <label class="col-sm-2 control-label">States</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="state_id" id="states" onchange="getStateID(this.value)">
                                <option value = "">Select state</option>
                                <?php
                                $stat = $this->db->query("select * from states");
                                $state_list = $stat->result();
                                foreach ($state_list as $st) {
                                    ?>
                                    <option value="<?php echo $st->id; ?>"><?php echo $st->state_name; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">City</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="city_id" id="cities" onchange="getLocations(this.value)">
                                <option value="">Select City</option>
                               

                            </select>
                        </div>
                    </div> 
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Pincodes</label>
                        <div class="col-sm-10">
                           <select class="form-control" name="pincodes"  id="pincodes_data">
                                <option value="">Select Pincode</option>

                            </select>
                           
                            
                        </div>
                    </div>  



                    <div class="form-group">
                        <label class="col-sm-2 control-label">Qualifications</label>
                        <div class="col-sm-10">
                            <select id="designations" name="designations[]" class="form-control js-example-basic-multiple" multiple="multiple">
                                <option value="">Select Designations</option>
                                <?php $designation_qry = $this->db->query("select * from designations where status=1"); 
                                      $designation_result = $designation_qry->result();
                                      foreach ($designation_result as $d_value) 
                                      { ?>
                                <option value="<?php echo $d_value->id; ?>" ><?php echo $d_value->name; ?></option>
                                  <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Specialisation</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="specialisation" id="specialisation">
                                <option  value="">Select Specialisation</option>
                            <?php foreach ($specialisation as $spl) { ?>
                                    <option value="<?= $spl->id ?>"><?= $spl->name ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Specialist In </label>
                        <div class="col-sm-10">
                            <select id="specialist" name="specialist[]" class="form-control js-example-basic-multiple" multiple="multiple">
                                <option value="">Select Specialist In</option>
                                <?php $designation_qry = $this->db->query("select * from specialist_in where status=1"); 
                                      $designation_result = $designation_qry->result();
                                      foreach ($designation_result as $d_value) 
                                      { ?>
                                <option value="<?php echo $d_value->id; ?>" ><?php echo $d_value->specialist_in; ?></option>
                                  <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                   <!--<div class="form-group">-->
                   <!--     <label class="col-sm-2 control-label">Specialist In </label>-->
                   <!--     <div class="col-sm-10">-->
                   <!--         <select class="form-control" name="specialist" id="specialist" required="">-->
                   <!--             <option value="">Select Specialisaton</option>-->

                   <!--         </select>-->
                   <!--     </div>-->
                   <!-- </div>-->
                    
                    
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Digital Signature</label>
                        <div class="col-sm-10">
                            <input type="file" id="image" name="digital_signature" class="form-control">
                            <p>Make sure image Width : 500 px & Height: 500 px</p>
                        </div>

                    </div>
                    
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Doctor License No.</label>
                        <div class="col-sm-10">
                            <input type="text"    onkeypress="return isNumberKey(event)" title="Please enter licence no." id="license" name="license" class="form-control">
                        </div>
                    </div>
                    
                     <div class="form-group">
                        <label class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-10">
                            <input type="text"    onkeyup="getEmail(this.value)" onkeypress="return isNumberKey(event)" title="Please enter email" id="email" name="email" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Mobile Number</label>
                        <div class="col-sm-10">
                            <input type="text"  onkeyup="getMobile(this.value)"  onkeypress="return isNumberKey(event)" title="Please enter exactly 10 digits" id="mobile_number" name="mobile_number" class="form-control">
                        </div>
                    </div>
                    
                   
                    
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Password</label>
                        <div class="col-sm-10">
                            <input type="text" id="password" name="password" class="form-control">
                        </div>
                    </div>

                     <div class="form-group">
                        <label class="col-sm-2 control-label">Address</label>
                        <div class="col-sm-10">
                            <textarea id="address" name="address" class="form-control"></textarea>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label">Experience</label>
                        <div class="col-sm-10">
                            <input type="text" id="experience" name="experience" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Audio Call Consultation Fee</label>
                        <div class="col-sm-10">
                            <input type="text" id="consultant_fee_voice_call" name="consultant_fee_voice_call" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Video Call Consultation Fee</label>
                        <div class="col-sm-10">
                            <input type="text" id="consultant_fee_video_call" name="consultant_fee_video_call" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Chat Consultation Fee</label>
                        <div class="col-sm-10">
                            <input type="text" id="consultant_fee_chat" name="consultant_fee_chat" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">About</label>
                        <div class="col-sm-10">
                            <textarea id="aboutus" name="aboutus" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Youtube Channel ID</label>
                        <div class="col-sm-10">
                            <input type="text" id="youtube_channel_id" name="youtube_channel_id" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Tags</label>
                        <div class="col-sm-10">
                            <input type="text" id="tags" name="tags" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Latitude</label>
                        <div class="col-sm-10">
                            <input type="text" id="latitude" name="latitude" class="form-control">
                            <u><a href="https://www.latlong.net/" target="_blank">Get latitude & longitude</a></u>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Longitude</label>
                        <div class="col-sm-10">
                            <input type="text" id="longitude" name="longitude" class="form-control">
                        </div>
                    </div>
                    
                     <div class="row">
                            <h3 style="text-align: center;">Availability Timings</h3>
                            <div class="form-group">
                                  <label class="col-sm-2 control-label">Morning Start Time</label>
                                  <div class="col-sm-4">
                                      <input type="time" id="morning_start_time" name="morning_start_time" class="form-control">
                                  </div>

                                  <label class="col-sm-2 control-label">Morning End Time</label>
                                <div class="col-sm-4">
                                    <input type="time" id="morning_end_time" name="morning_end_time" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                  <label class="col-sm-2 control-label">Afternoon Start Time</label>
                                  <div class="col-sm-4">
                                      <input type="time" id="afternoon_start_time" name="afternoon_start_time" class="form-control">
                                  </div>

                                  <label class="col-sm-2 control-label">Afternoon End Time</label>
                                <div class="col-sm-4">
                                    <input type="time" id="afternoon_end_time" name="afternoon_end_time" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                  <label class="col-sm-2 control-label">Evening Start Time</label>
                                  <div class="col-sm-4">
                                      <input type="time" id="evening_start_time" name="evening_start_time" class="form-control">
                                  </div>

                                  <label class="col-sm-2 control-label">Evening End Time</label>
                                <div class="col-sm-4">
                                    <input type="time" id="evening_end_time" name="evening_end_time" class="form-control">
                                </div>
                            </div>

                            

                     </div>
                    
     
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Doctor Blue Tick Status</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="bluetick_status" name="bluetick_status">
                                <option value="" selected="">Select Status</option>
                                    <option value="active" >Active</option>
                                   <option value="inactive">InActive</option>
                            </select>
                        </div>
                    </div>
                    
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Doctor Rating </label>
                        <div class="col-sm-10">
                            <select class="form-control" id="doctor_rating" name="doctor_rating">
                                <option value="" selected="">Select Rating</option>
                                   <option value="1" >1-Star</option> 
                                   <option value="2">2-Star</option> 
                                   <option value="3">3-Star</option> 
                                   <option value="4">4-Star</option>
                                   <option value="5">5-Star</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">No.of users reviewed</label>
                        <div class="col-sm-10">
                            <input type="text" id="users_count" name="users_count" class="form-control">
                        </div>
                    </div>
                   
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Doctor Show Status</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="doctor_show_status" name="doctor_show_status">
                                <option value="" selected="">Select Doctor Show Status</option>
                                    <option value="active" >Active</option>
                                   <option value="inactive">InActive</option>
                            </select>
                        </div>
                    </div>

                   <div class="form-group">
                        <label class="col-sm-2 control-label">Doctor Login Status</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="doctor_login_status" name="doctor_login_status">
                                <option value="">Select Doctor Login Status</option>
                                    <option value="active">Active</option>
                                   <option value="inactive">InActive</option>
                            </select>
                        </div>
                    </div>
                    
                   

                    <div class="hr-line-dashed"></div>
                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" id="btn_vendorshops" type="submit">Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<link href="<?= ADMIN_ASSETS_PATH ?>assets/js/select2.min.css" rel="stylesheet" /> 
<script src="<?= ADMIN_ASSETS_PATH ?>assets/js/select2.min.js"></script>
<script>
        $(document).ready(function() {
           $('.js-example-basic-multiple').select2({
            placeholder : "Select"
           });
        });
</script> 
<script type="text/javascript">

  function getEmail(email)
  {
     $('.error').remove();
            var errr=0;
   //var email = $("#email").value();
   
   $.get("<?= base_url() ?>api/admin_ajax/admin/validateEmail", "email=" + email,
    function (response, status, http) {
              var str = response;
              var res = str.split("@");
              //alert(JSON.stringify(res));
              if(res[1]=='success')
              {
                  $('#email').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Already Exist ( Email Address )</span>');
                  $('#email').focus();
                  return false;
              }
              
    }, "html");

  }

  function getMobile(phone)
  {
     $('.error').remove();
            var errr=0;
   $.get("<?= base_url() ?>api/admin_ajax/admin/validateMobile", "phone=" + phone,
    function (response, status, http) {
              var str = response;
              var res = str.split("@");
              //alert(JSON.stringify(res));
              if(res[1]=='success')
              {
                  $('#mobile').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Already Exist ( Mobile Number )</span>');
                  $('#mobile').focus();
                  return false;
              }
              
    }, "html");

  }

  function checkPincode(pincode_id)
  {
     $('.error').remove();
            var errr=0;
   $.get("<?= base_url() ?>api/admin_ajax/admin/validatePincode", "pincode_id=" + pincode_id,
    function (response, status, http) {
              var str = response;
              var res = str.split("@");
              //alert(JSON.stringify(res));
              if(res[1]=='success')
              {
                  $('#pincodes_data').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Pincode already Exist, Please select another one </span>');
                  $('#pincodes_data').focus();
                  return false;
              }
              
    }, "html");
  }

  function isNumberKey(evt)
      {
         var charCode = (evt.which) ? evt.which : event.keyCode
         if (charCode > 31 && (charCode < 48 || charCode > 57))
            return false;

         return true;
      }

  $('#btn_vendorshops').click(function(){
        $('.error').remove();
            var errr=0;
            var FileUploadPath = $('#hospital_image').val();
 var FileSize = document.getElementById("hospital_image").files[0];
 var Extension = FileUploadPath.substring(FileUploadPath.lastIndexOf('.') + 1).toLowerCase();
    var ph = $('#mobile_number').val();
      if($('#hospital_name').val()=='')
      {
         $('#hospital_name').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter hospital Name</span>');
         $('#hospital_name').focus();
         return false;
      }
      else if($('#hospital_image').val()=='') 
      {
         $('#hospital_image').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select hospital Image</span>');
         $('#hospital_image').focus();
         return false;
      }
      else if($('#doctor_name').val()=='')
      {
         $('#doctor_name').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Doctor Name</span>');
         $('#doctor_name').focus();
         return false;
      }
      else if($('#doctor_image').val()=='')
      {
         $('#doctor_image').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Doctor Image</span>');
         $('#doctor_image').focus();
         return false;
      }
        else if($('#consultant_fee_voice_call').val()=='') 
      {
         $('#consultant_fee_voice_call').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter consultant fee for voice call</span>'); 
         $('#consultant_fee_voice_call').focus(); 
         return false;
      } 
      else if($('#consultant_fee_video_call').val()=='')
      {
         $('#consultant_fee_video_call').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter consultant fee for video call</span>');
         $('#consultant_fee_video_call').focus();
         return false;
      }
      
         else if($('#consultant_fee_chat').val()=='')
      {
         $('#consultant_fee_chat').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter consultant fee for Chat</span>');
         $('#consultant_fee_chat').focus();
         return false;
      }
       else if($('#states').val()=='')
      {
         $('#states').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select State</span>');
         $('#states').focus();
         return false;
      }
      else if($('#cities').val()=='')
      {
         $('#cities').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter city</span>');
         $('#cities').focus();
         return false;
      }
        else if($('#pincodes_data').val()=='')
      {
         $('#pincodes_data').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Pincode</span>');
         $('#pincodes_data').focus();
         return false;
      }
      else if($('#cat_id').val()=='')
      {
         $('#cat_id').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select </span>');
         $('#cat_id').focus();
         return false;
      }
        else if($('#image').val()=='') 
      {
         $('#image').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Image</span>');
         $('#image').focus();
         return false;
      }
       else if($('#license').val()=='')
      {
         $('#license').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter License No.</span>');
         $('#license').focus();
         return false;
      }
        else if($('#email').val()=='')
      {
         $('#email').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Email.</span>');
         $('#email').focus();
         return false;
      }
      else if($('#mobile_number').val()=='')
      {
         $('#mobile_number').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Mobile</span>');
         $('#mobile_number').focus();
         return false;
      }
      else if(ph.length!=10)
      {
         $('#mobile_number').after('<span class="error" style="color:red">Enter Valid 10 digit Phone Number</span>');
         $('#mobile_number').focus();
         return false;
      }  
      else if($('#password').val()=='')
      {
         $('#password').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Password</span>');
         $('#password').focus();
         return false;
      }
      
      //  else if($('#designations').val()=='')
      // {
      //    $('#designations').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter designations</span>');
      //    $('#designations').focus();
      //    return false;
      // }
      
      else if($('#experience').val()=='')
      {
         $('#experience').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Experience</span>');
         $('#experience').focus();
         return false;
      }
      
      
      else if($('#address').val()=='')
      {
         $('#address').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Address</span>');
         $('#address').focus();
         return false;
      }
      
      else if($('#aboutus').val()=='')
      {
         $('#aboutus').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter About us</span>');
         $('#aboutus').focus();
         return false;
      }

       else if($('#youtube_channel_id').val()=='')
      {
         $('#youtube_channel_id').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Youtube Channel Id</span>');
         $('#youtube_channel_id').focus();
         return false;
      }
       else if($('#tags').val()=='')
      {
         $('#tags').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Tags</span>');
         $('#tags').focus();
         return false;
      }

      else if($('#morning_start_time').val()=='')
      {
         $('#morning_start_time').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Morming Start Time</span>');
         $('#morning_start_time').focus();
         return false;
      }

      else if($('#morning_end_time').val()=='')
      {
         $('#morning_end_time').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Morning End time</span>');
         $('#morning_end_time').focus();
         return false;
      }

      else if($('#afternoon_start_time').val()=='')
      {
         $('#afternoon_start_time').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Afternoon Start Time</span>');
         $('#afternoon_start_time').focus();
         return false;
      }

      else if($('#afternoon_end_time').val()=='')
      {
         $('#afternoon_end_time').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Afternoon End Time</span>');
         $('#afternoon_end_time').focus();
         return false;
      }

      else if($('#evening_start_time').val()=='')
      {
         $('#evening_start_time').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter evening Start Time</span>');
         $('#evening_start_time').focus();
         return false;
      }

      else if($('#evening_end_time').val()=='')
      {
         $('#evening_end_time').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Evening End Time</span>');
         $('#evening_end_time').focus();
         return false;
      }
      else if($('#bluetick_status').val()=='')
      {
         $('#bluetick_status').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Status</span>');
         $('#bluetick_status').focus();
         return false;
      }
       else if($('#doctor_rating').val()=='')
      {
         $('#doctor_rating').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Rating</span>');
         $('#doctor_rating').focus();
         return false;
      }
        else if($('#users_count').val()=='')
      {
         $('#users_count').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Users Reviewed Count</span>');
         $('#users_count').focus();
         return false;
      }
      else if($('#doctor_show_status').val()=='')
      {
         $('#doctor_show_status').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Doctors Status</span>');
         $('#doctor_show_status').focus();
         return false;
      }

       else if($('#doctor_login_status').val()=='')
      {
         $('#doctor_login_status').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Select Doctor Login Status</span>');
         $('#doctor_login_status').focus();
         return false;
      }
    //   if($('#gst_number').val()=='')
    //   {
    //      $('#gst_number').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Gst Number</span>');
    //      $('#gst_number').focus();
    //      return false;
    //   }
    //   if($('#pan_number').val()=='')
    //   {
    //      $('#pan_number').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Pan Number</span>');
    //      $('#pan_number').focus();
    //      return false;
    //   }


      else if (FileSize.size > 2097152)
      {
              $('#doctor_image').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">File size must under 2mb!</span>');
              $('#doctor_image').focus();
              return false;
      }
      else if (Extension == "png" || Extension == "jpeg" || Extension == "jpg") 
      {
                if (fuData.files && fuData.files[0]) 
                {
                    var reader = new FileReader();
                    reader.onload = function(e) 
                    {
                        $('#shop_logo').attr('src', e.target.result);
                    }
                    reader.readAsDataURL(fuData.files[0]);
                }
      } 
      else 
      {
        $('#hospital_image').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Image only allows file types of PNG , JPG, and JPEG.</span>');
           $('#hospital_image').focus();
           return false;
      }

      

 });

  function validateEmail($email) 
{
    var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    if( !emailReg.test( $email) ) {
      return false;
    } 
    else
    {
        return true;
    }
}
</script>

<link href="https://test.indiasmartlife.com/admin_assets/css/jquery.datetimepicker.css" rel="stylesheet">
<script src="https://test.indiasmartlife.com/admin_assets/js/jquery.datetimepicker.js"></script>
<script type="text/javascript">

  function getStateID(state_id)
          {
             $.get("<?= base_url() ?>api/admin_ajax/admin/get_cities", "state_id=" + state_id,
              function (response, status, http) {
                  //$('.modal').modal('hide');
                  $('#cities').html(response);
              }, "html");
          }

           function getLocations(city_id)
        {
          $.get("<?= base_url() ?>api/admin_ajax/admin/get_city_locations", "city_id=" + city_id,
                    function (response, status, http) {
                        //$('.modal').modal('hide');
                        $('#pincodes_data').html(response);
                    }, "html");
        }

    $(document).ready(function () {

        $('.datepicker').datetimepicker({
            timepicker: false,
            format: 'Y-m-d',
            scrollInput: false
        });
        $(document).on('mousewheel', '.datepicker', function () {
            return false;
        });

        $('.datepickertimepicker').datetimepicker({
            timepicker: true,
            format: 'Y-m-d H:i',
            scrollInput: false
        });
        $(document).on('mousewheel', '.datepickertimepicker', function () {
            return false;
        });


    });
    
    $(document).ready(function () {
        $('#specialisation').change(function () {
            var specialisation = $('#specialisation').val();
            if (specialisation != '') {
                $.ajax({
                    url: "<?php echo base_url(); ?>admin/doctors/getspecialist",
                    method: "POST",
                    data: { specialisation: specialisation },
                    success: function (data) {
                        //alert(JSON.stringify(data));
                        $('#specialist').html(data);
                    }
                });
            }

        });


    });
</script>
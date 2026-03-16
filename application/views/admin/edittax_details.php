<div class="row">

    <div class="col-lg-12">

        <div class="ibox float-e-margins">

            <div class="ibox-title">

                <h5><?= $title ?></h5>

                <div class="ibox-tools">
                    <a href="<?= base_url() ?>admin/tax_details">
                            <button class="btn btn-primary">BACK</button>
                        </a>


                </div>

            </div>

            <div class="ibox-content">

                <form method="post" class="form-horizontal" enctype="multipart/form-data"  action="<?= base_url() ?>admin/tax_details/update"> 
                
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Gst No.</label>
                        <div class="col-sm-10">
                            <input type="text" name="gst" id="gst" placeholder="Enter Gst Number" class="form-control" value="<?=$tax_details->gst?>"> 
                        </div> 
                    </div>
                    
                      <div class="form-group">
                        <label class="col-sm-2 control-label">Pan Number</label>
                        <div class="col-sm-10">
                            <input type="text" name="pan" id="pan" placeholder="Enter Pan Number" class="form-control" value="<?=$tax_details->pan?>">
                        </div>
                    </div>
                    
                       <div class="form-group">
                        <label class="col-sm-2 control-label">Company Address</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" name="address" id="address" placeholder="Address"><?=$tax_details->company_address?></textarea>
                        </div>
                    </div>
                    
                     <div class="form-group">
                        <label class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-10">
                            <input type="text" name="email" id="email" placeholder="Enter Email" class="form-control" value="<?=$tax_details->company_email?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Mobile</label>
                        <div class="col-sm-10">
                            <input type="text" name="mobile" id="mobile" placeholder="Enter Phone Number" class="form-control" value="<?=$tax_details->company_phone?>">
                        </div>
                    </div>
                    
                       <div class="form-group">
                        <label class="col-sm-2 control-label">Website </label>
                        <div class="col-sm-10">
                            <input type="text" name="website" id="website" placeholder="Enter Website Url" class="form-control" value="<?=$tax_details->company_website?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label">Customer Name</label>
                        <div class="col-sm-10">
                            <input type="text" name="cust_name" id="cust_name" placeholder="Enter Name" class="form-control" value="<?=$tax_details->customer_name?>" >
                        </div>
                    </div>
                    
                     
                    
                     <div class="form-group">
                        <label class="col-sm-2 control-label">Customer Email</label>
                        <div class="col-sm-10">
                            <input type="text" name="cust_email" id="cust_email" placeholder="Enter Email" class="form-control" value="<?=$tax_details->customer_email?>" >
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Customer Phone</label>
                        <div class="col-sm-10">
                            <input type="text" name="cust_phone" id="cust_phone" placeholder="Enter Number" class="form-control" value="<?=$tax_details->customer_phone?>" >
                        </div>
                    </div>
                    
                     <div class="form-group">
                        <label class="col-sm-2 control-label">Customer Place</label>
                        <div class="col-sm-10">
                            <input type="text" name="cust_place" id="cust_place" placeholder="Enter Place" class="form-control" value="<?=$tax_details->customer_place?>" >  
                        </div>
                    </div>
                    
                     <div class="form-group">
                        <label class="col-sm-2 control-label">Customer Address</label>
                        <div class="col-sm-10">
                          <textarea class="form-control" name="cust_address" id="cust_address" placeholder="Address">value="<?=$tax_details->customer_address?>"</textarea>
                        </div>
                    </div>
                    
                    
                    <!--  <div class="form-group">-->
                    <!--    <label class="col-sm-2 control-label">Bank Name</label>-->
                    <!--    <div class="col-sm-10">-->
                    <!--        <input type="text" name="bank_name" id="bank_name" placeholder="Enter Bank Number" class="form-control" value="<?=$tax_details->bank_name?>" > -->
                    <!--    </div>-->
                    <!--</div>-->
                    
                    <!--     <div class="form-group">-->
                    <!--    <label class="col-sm-2 control-label">Account Number</label>-->
                    <!--    <div class="col-sm-10">-->
                    <!--        <input type="text" name="account_number" id="account_number" placeholder="Enter Account Number" class="form-control" value="<?=$tax_details->account_number?>" > -->
                    <!--    </div>-->
                    <!--</div>-->
                    
                    <!--     <div class="form-group">-->
                    <!--    <label class="col-sm-2 control-label">IFSC Code</label>-->
                    <!--    <div class="col-sm-10">-->
                    <!--        <input type="text" name="ifsc" id="ifsc" placeholder="Enter Code" class="form-control" value="<?=$tax_details->ifsc_code?>"> -->
                    <!--    </div>-->
                    <!--</div>-->
                    
                    <!--     <div class="form-group">-->
                    <!--    <label class="col-sm-2 control-label">Branch</label>-->
                    <!--    <div class="col-sm-10">-->
                    <!--        <input type="text" name="branch" id="branch" placeholder="Enter Branch" class="form-control" value="<?=$tax_details->branch?>">  -->
                    <!--    </div>-->
                    <!--</div>-->
                 
                    


                    

                    <!-- <?php if ($tax_details->qr_code) { ?>-->

                    <!--    ?>-->

                    <!--    <div class="form-group">-->

                    <!--        <label class="col-sm-2 control-label">Preview</label>-->

                    <!--        <div class="col-sm-10">-->
                                
                    <!--            <img width="200px" src="<?= base_url() ?>uploads/pdf_logo/<?=$tax_details->qr_code?> "/>-->

                    <!--        </div>-->

                    <!--    </div>-->

                    <!--    <?php } ?> -->

               

                    <!--<div class="form-group">-->
                    <!--    <label class="col-sm-2 control-label">Qr Code</label>-->
                    <!--    <div class="col-sm-10">-->
                    <!--        <input type="file" name="qrcode" id="qrcode" class="form-control" >-->
                    <!--         <span class="help-block m-b-none" style="color:red;">App Image Width : 900px and height : 400px</span>-->
                    <!--    </div>-->
                    <!--</div> -->
                    

                    <div class="form-group">
                        <div class="col-sm-4 col-sm-offset-2">
                            <button class="btn btn-primary" id="btn_banner" type="submit"> <i class="fa fa-plus-circle"></i> Update</button> 
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>

</div>

<!--<script type="text/javascript">-->

  
<!--  $('#btn_banner').click(function(){-->
<!--        $('.error').remove();-->
<!--            var errr=0;-->
<!--      if($('#gst').val()=='')-->
<!--      {-->
<!--         $('#gst').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Gst Number</span>');-->
<!--         $('#gst').focus();-->
<!--         return false;-->
<!--      }-->
<!--       else if($('#pan').val()=='')-->
<!--      {-->
<!--         $('#pan').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Pan Number</span>');-->
<!--         $('#pan').focus();-->
<!--         return false;-->
<!--      }-->
<!--      else if($('#address').val()=='')-->
<!--      {-->
<!--         $('#address').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Address</span>'); -->
<!--         $('#address').focus();-->
<!--         return false;-->
<!--      }-->
<!--       else if ($('#email').val() == '' || $('#email').val().trim() == "")-->
<!--        {-->
<!--            $('#email').after('<span class="error" style="color:red;font-size: 14px;margin-left: 17px; position:relative;  bottom: 0px;">Enter Valid email</span>');-->
<!--            $('#email').focus();-->
<!--            return false;-->
<!--        } else if (!validateemail($('#email').val()))-->
<!--        {-->
<!--            $('#email').after('<span class="error" style="color:red; text-align:left; font-size: 14px; position:relative;  bottom: 0px;">Invalid email Address</span>');-->
<!--            $('#email').focus();-->
<!--            return false;-->
<!--        } else if ($('#mobile').val() == '')-->
<!--        {-->
<!--            $('#mobile').after('<span class="error" style="color:red;font-size: 14px; text-align:left; position:relative;  bottom: 0px;">Enter phone Number</span>');-->
<!--            $('#mobile').focus();-->
<!--            return false;-->
<!--        } else if (ph.length != 10)-->
<!--        {-->
<!--            $('#mobile').after('<span class="error" style="color:red; text-align:left; font-size: 14px; position:relative;  bottom: 0px;">Enter Minimum 10 digit Phone No.</span>');-->
<!--            $('#mobile').focus();-->
<!--            return false;-->
<!--        }-->
<!--          else if($('#website').val()=='')-->
<!--      {-->
<!--         $('#website').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Website Url</span>');-->
<!--         $('#website').focus();-->
<!--         return false;-->
<!--      }-->
<!--        else if($('#cust_name').val()=='')-->
<!--      {-->
<!--         $('#cust_name').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Names</span>');-->
<!--         $('#cust_name').focus();-->
<!--         return false;-->
<!--      }-->
<!--      else if ($('#cust_email').val() == '' || $('#cust_email').val().trim() == "")-->
<!--        {-->
<!--            $('#cust_email').after('<span class="error" style="color:red;font-size: 14px;margin-left: 17px; position:relative;  bottom: 0px;">Enter Valid email</span>');-->
<!--            $('#cust_email').focus();-->
<!--            return false;-->
<!--        } else if (!validateemail($('#cust_email').val()))-->
<!--        {-->
<!--            $('#cust_email').after('<span class="error" style="color:red; text-align:left; font-size: 14px; position:relative;  bottom: 0px;">Invalid email Address</span>');-->
<!--            $('#cust_email').focus();-->
<!--            return false;-->
<!--        } else if ($('#cust_mobile').val() == '')-->
<!--        {-->
<!--            $('#cust_mobile').after('<span class="error" style="color:red;font-size: 14px; text-align:left; position:relative;  bottom: 0px;">Enter phone Number</span>');-->
<!--            $('#cust_mobile').focus();-->
<!--            return false;-->
<!--        } else if (ph.length != 10)-->
<!--        {-->
<!--            $('#cust_mobile').after('<span class="error" style="color:red; text-align:left; font-size: 14px; position:relative;  bottom: 0px;">Enter Minimum 10 digit Phone No.</span>');-->
<!--            $('#cust_mobile').focus();-->
<!--            return false;-->
<!--        }-->
<!--           else if($('#cust_place').val()=='')-->
<!--      {-->
<!--         $('#cust_place').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Place</span>');-->
<!--         $('#cust_place').focus();-->
<!--         return false;-->
<!--      }-->
<!--         else if($('#cust_address').val()=='')-->
<!--      {-->
<!--         $('#cust_address').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Address</span>');-->
<!--         $('#cust_address').focus();-->
<!--         return false;-->
<!--      }-->
<!--         else if($('#bank_name').val()=='')-->
<!--      {-->
<!--         $('#bank_name').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Bank Name</span>');-->
<!--         $('#bank_name').focus();-->
<!--         return false;-->
<!--      }-->
<!--         else if($('#account_number').val()=='')-->
<!--      {-->
<!--         $('#account_number').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Account Number</span>');-->
<!--         $('#account_number').focus();-->
<!--         return false;-->
<!--      }-->
<!--         else if($('#ifsc').val()=='')-->
<!--      {-->
<!--         $('#ifsc').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Ifsc Code</span>');-->
<!--         $('#ifsc').focus();-->
<!--         return false;-->
<!--      }-->
<!--         else if($('#branch').val()=='')-->
<!--      {-->
<!--         $('#branch').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Branch</span>'); -->
<!--         $('#branch').focus();-->
<!--         return false;-->
<!--      }-->
      
<!-- });-->
   
<!--    $("#phone1").keypress(function (event) {-->
<!--        return /\d/.test(String.fromCharCode(event.keyCode));-->
<!--    });-->
<!--    $("#name1").keypress(function (e) {-->
<!--        var valid = (e.which >= 65 && e.which <= 90) || (e.which >= 97 && e.which <= 122) || (e.which == 32);-->
<!--        if (!valid) {-->
<!--            e.preventDefault();-->
<!--        }-->
<!--    });-->

<!--    $("#email1").on("blur", function (e) {-->
<!--        var userinput = $(this).val();-->
<!--        var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i-->

<!--        if (!pattern.test(userinput))-->
<!--        {-->
<!--            toastr.error('Invalid email address.')-->
<!--        }-->
<!--    });-->
<!--    function validateemail($email)-->
<!--    {-->
<!--        var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;-->
<!--        if (!emailReg.test($email)) {-->
<!--            return false;-->
<!--        } else-->
<!--        {-->
<!--            return true;-->
<!--        }-->
<!--    }-->
<!--</script>-->

<script type="text/javascript">
    function getStatus(typ)
    {
        if(typ=='products'){
            document.getElementById("show_shops").style.display = "none";
            document.getElementById("show_products").style.display = "block";
        }
        else if(typ=='shops'){
            document.getElementById("show_shops").style.display = "block";
            document.getElementById("show_products").style.display = "none";
        }
    }
</script>


<script type="text/javascript">

  
  $('#btn_banner').click(function(){
        $('.error').remove();
            var errr=0;
      if($('#title').val()=='')
      {
         $('#title').after('<span class="error" style="color:red;font-size: 18px;margin-left: 18px;">Enter Title</span>');
         $('#title').focus();
         return false;
      }
 });
   

</script>
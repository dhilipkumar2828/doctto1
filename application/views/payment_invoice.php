
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
        <title>Doctto Payment Invoice</title>
        <link rel="stylesheet" href="">
        <style>
            body{
                font-size: 14px;
            }

            table tr td{ 
                padding: 5px;
            }

            p{
                font-size: 15px;
            }

            .table-border tr th,
            .table-border tr td{
                border: 1px solid #ccc;
                padding: 4px;
                font-size: 14px;
            }

            .table-border tr th{
                font-weight: 800;
                color: #000;
            }

            .social-media{
                margin: 0px;
                padding: 0px;
                text-align: center;
            }

            .social-media li{
                display: inline-block;
                margin: 0px 5px;
            }

            .social-media li a img{
                width: 35px;
                height: 35px;
            }
        </style>
    </head>
    <body>
        <div style="width:800px; margin: 0px auto; overflow: hidden; font-family: arial; border:1px solid #ccc; padding: 25px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="middle">
                        <table>
                            <tr>                                
                                <td width="630" valign="middle"> 
                                    <table cellspacing="0" cellpadding="10" align="center">
                                        <tr align="center">
                                            <td>
                                                <h6 style="font-size: 20px; color: #276ef1; text-align: left; margin: 0px; margin-bottom: 4px;">TAX INVOICE</h6>
                                                <h5 style="font-size: 20px; color: #000; font-weight: 700; margin: 0px; text-align: left; margin-bottom: 4px;">DOCTTO</h5>

                                                <h6 style="font-size: 16px; color: #000; text-align: left; margin: 0px; margin-bottom: 4px;">GSTIN <?=$tax_details->gst?>      PAN <?=$tax_details->pan?></h6>

                                                <h6 style="font-size: 16px; color: #000; font-weight: 500; text-align: left; margin: 0px; margin-bottom: 4px;"><?=$tax_details->company_address?></h6>

                                                <h6 style="font-size: 16px; color: #000; text-align: left; margin: 0px; font-weight: 500; margin-bottom: 4px;">
                                                    <span style="font-weight: 600;">Mobile: </span><?=$tax_details->company_phone?>&nbsp;&nbsp;&nbsp;&nbsp;<span style="font-weight: 600;">Email: </span><?=$tax_details->company_email?>m</h6>

                                                <h6 style="font-size: 16px; color: #000; text-align: left; margin: 0px; font-weight: 500; margin-bottom: 0px;"><span style="font-weight: 600">Website: </span><?=$tax_details->company_website?></h6>
                                            </td>
                                        </tr>
                                        <!-- <tr align="center">
                                            <td><h6 style="font-size: 14px; color: #000; font-weight: 500; text-align: left; margin: 0px; margin-bottom: 0px;">H NO 224, ROAD NO 10, SRI RAM HILLS BONAKAL ROAD, Khammam, TELANGANA, 507001</h6></td>
                                        </tr> -->
                                    </table>
                                </td>
                                <td width="20"></td>
                                <td width="250" valign="top" align="center">
                                    <h6 style="font-size: 14px; color: #333; font-weight: 600; margin: 0;">ORIGINAL FOR RECIPIENT</h6>
                                    <img src="<?=base_url()?>admin_assets/assets/images/logo1.png" alt="" style="height: 80px; text-align: right; margin: 0px auto; display: block;">
                                </td>
                                <!-- <td width="200" valign="top" align="right">
                                    <img src="qr-code.png" alt="" style="height: 80px;">
                                </td> -->
                            </tr>
                        </table>
                    </td>
                </tr>                               
                <tr>
                    <td valign="top">
                        <table>
                            <tr>
                                <td width="350" valign="top">
                                    <h6 class="lead marginbottom" style="font-size: 18px; color: #000; margin: 0px; margin-bottom: 4px;">Invoice #: <span style="font-weight: 600;">INV-<?=$doctor_id->id?></span></h6>
                                    <h6 style="font-size: 16px; color: #000; margin: 0px; margin-bottom: 4px; font-weight: 600;">Customer Details: </h6>
                                    <h6 style="font-size: 16px; color: #000; margin: 0px; margin-bottom: 4px; font-weight: 700;"><?= $patient->patient_name ?></h6>
                                    <h6 style="font-size: 16px; color: #000; margin: 0px; margin-bottom: 4px; font-weight: 500;">Phone: <?=$patient->patient_mobile?></h6>
                                    <h6 style="font-size: 16px; color: #000; margin: 0px; margin-bottom: 4px; font-weight: 500;"><?=$patient->patient_email?></h6>
                                    <h6 style="font-size: 16px; color: #000; margin: 0px; margin-bottom: 0px; font-weight: 500;">Place of Supply: <?=$tax_details->customer_place?></h6>
                                </td>
                                 <td width="350" valign="top">
                                    <h6 class="lead marginbottom" style="font-size: 18px; color: #000; margin: 0px; margin-bottom: 6px; font-weight: 600;">Invoice Date: <span style="font-weight: 700;"><?=date('d-M-Y', strtotime($patient->date))?></span></h6>
                                    <h6 style="font-size: 16px; color: #000; margin: 0px; font-weight: 500; margin-bottom: 6px;">Billing Address: </h6>
                                    <h6 style="font-size: 16px; color: #000; margin: 0px; font-weight: 500; margin-bottom: 4px; width: 250px;"><?=$tax_details->customer_address?></h6>  
                                </td>
                                <td width="300" valign="top">
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>                
                <tr>
                    <td valign="top">
                        <table width="100%" cellpadding="15" cellspacing="0" class="table-border">
                            <tr>
                                <th style="font-weight: 600; width: 30px; height: 25px; border: inherit; text-align: left; border-top: 2px solid #276ef1; border-bottom: 2px solid #276ef1;">#</th>
                                <th style="font-weight: 600; width: 250px; border: inherit; border-top: 2px solid #276ef1; text-align: left; border-bottom: 2px solid #276ef1;">Item</th>
                                <th style="font-weight: 600; width: 80px; border: inherit; border-top: 2px solid #276ef1; text-align: left; border-bottom: 2px solid #276ef1;">Rate/Item</th>
                                <th style="font-weight: 600; width: 30px; border: inherit; border-top: 2px solid #276ef1; text-align: left; border-bottom: 2px solid #276ef1;">Qty</th>
                                <th style="font-weight: 600; width: 100px; border: inherit; border-top: 2px solid #276ef1; text-align: left; border-bottom: 2px solid #276ef1;">Taxable Value</th>
                                <th style="font-weight: 600; border: inherit; border-top: 2px solid #276ef1; text-align: left; border-bottom: 2px solid #276ef1;">Tax Amount</th>
                                <th style="font-weight: 600; border: inherit; border-top: 2px solid #276ef1; text-align: right; border-bottom: 2px solid #276ef1;">Amount</th>
                            </tr>
                            <tr>
                                <td style="border: inherit; height: 30px; border-bottom: 1px solid #276ef1; text-align: left;">1</td>
                                <td style="border: inherit; height: 30px; border-bottom: 1px solid #276ef1; text-align: left;">Consultation Fee</td>
                                <td style="border: inherit; height: 30px; border-bottom: 1px solid #276ef1; text-align: left;"><?=$doctor_id->consultation_fee?>.00</td>
                                <td style="border: inherit; height: 30px; border-bottom: 1px solid #276ef1; text-align: left;">1.0</td>
                                <td style="border: inherit; height: 30px; border-bottom: 1px solid #276ef1; text-align: left;"><?=$doctor_id->consultation_fee?>.00</td>
                                <td style="border: inherit; height: 30px; border-bottom: 1px solid #276ef1; text-align: left;">0.00 <span>(0.0%)</span></td>
                                <td style="border: inherit; height: 30px; border-bottom: 1px solid #276ef1; text-align: right;"><?=$doctor_id->consultation_fee?>.00</td>
                            </tr>
                            <tr>
                                <td style="border: inherit; height: 30px;"></td>
                                <td style="border: inherit; height: 30px;"></td>
                                <td style="border: inherit; height: 30px;"></td>
                                <td style="border: inherit; height: 30px;"></td>
                                <td style="border: inherit; height: 30px;"></td>
                                <td style="border: inherit; height: 30px; font-weight: 600; border-bottom: 2px solid #8c8c8c;">Taxable Amount</td>
                                <td style="border: inherit; height: 30px; font-weight: 600; border-bottom: 2px solid #8c8c8c;">Rs. <?=$doctor_id->consultation_fee?></td> 
                            </tr>
                            <tr>
                                <td style="border: inherit; height: 30px;"></td>
                                <td style="border: inherit; height: 30px;"></td>
                                <td style="border: inherit; height: 30px;"></td>
                                <td style="border: inherit; height: 30px;"></td>
                                <td style="border: inherit; height: 30px;"></td>
                                <td style="border: inherit; height: 30px; font-weight: 600; font-size: 16px;">Total</td>
                                <td style="border: inherit; height: 30px; font-weight: 600; font-size: 16px;">Rs. <?=$doctor_id->consultation_fee?></td>
                            </tr>
                            <tr>
                                <td colspan="2" style="border: inherit; height: 20px; border-bottom: 2px solid #8c8c8c;">Total Items / Qty : 1 / 1.0</td>
                                <td style="border: inherit; border-bottom: 2px solid #8c8c8c;"></td>
                                <td style="border: inherit; border-bottom: 2px solid #8c8c8c;"></td>
                                <td colspan="3" style="border: inherit; border-bottom: 2px solid #8c8c8c;"></td> 
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <table>
                            <tr>
                                <td width="490">                                    
                                </td>
                                <td width="20"></td>
                                 <td width="490">
                                    <h6 class="text-right" style="font-size: 14px; text-align: right; font-weight: 600; margin-top: -10px;"><img src="<?=base_url()?>admin_assets/assets/images/sucess.png" width="20" style="margin-bottom: -6px;"> Amount Paid</h6>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                
                   <tr>  
                   
                       <td style="font-size: 25px;  text-align: right; padding-right:60px" >
                          
                           <h4>DOCTTO</h4>
                       </td>
                      
                   
                </tr>
                
                        <tr> 
                   
                       <td style="font-size: 20px;  text-align: right;" >
                           <img src="<?php echo base_url(); ?>admin_assets/assets/images/authorised_signature.jpeg" style="width: 200px; height: 50px;"> 
                           <h4>Authorized Signatory :</h4>
                       </td>
                      
                   
                </tr>

                <!--<tr>-->
                <!--    <td valign="top">-->
                <!--        <table>-->
                <!--            <tr>-->
                <!--                <td width="250" valign="top">-->
                <!--                    <img src="<?=base_url()?>uploads/pdf_logo/<?=$tax_details->qr_code?>" width="200">-->
                <!--                </td>-->
                <!--                 <td width="550" valign="top">-->
                <!--                    <h6 style="font-size: 14px; margin: 0px; margin-bottom: 8px; font-weight: 600;">Bank Details:</h6>-->
                <!--                    <h6 style="margin: 0px; margin-bottom: 8px; font-size: 14px; font-weight: 500;">-->
                <!--                        <span style="width:25%; display: inline-block; font-weight: 600; font-size: 14px;">Bank: </span><?=$tax_details->bank_name?></h6>-->
                <!--                    <h6 style="margin: 0px; margin-bottom: 8px; font-size: 14px; font-weight: 500;">-->
                <!--                        <span style="width:25%; display: inline-block; font-weight: 600; font-size: 14px; font-size: 14px; font-weight: 600;">Account #: </span><?=$tax_details->account_number?></h6>-->
                <!--                    <h6 style="margin: 0px; margin-bottom: 8px; font-size: 14px; font-weight: 500;">-->
                <!--                        <span style="width:25%; display: inline-block; font-weight: 600; font-size: 14px;">IFSC: </span><?=$tax_details->ifsc_code?></h6>-->
                <!--                    <h6 style="margin: 0px; margin-bottom: 8px; font-size: 14px; font-weight: 500;">-->
                <!--                        <span style="width:25%; display: inline-block; font-weight: 600; font-size: 14px;">Branch: </span><?=$tax_details->branch?></h6>-->
                <!--                </td>-->
                <!--                <td width="200" valign="top">-->
                <!--                </td>-->
                <!--            </tr>-->
                <!--        </table>-->
                <!--    </td>-->
                <!--</tr>-->
                
            </table>
        </div>
    </body>
</html>
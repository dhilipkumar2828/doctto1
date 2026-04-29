

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Doctto</title>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
        <style>
            body{
                font-size: 14px;
                font-family: 'Montserrat', sans-serif; 

            }

            table tr td{
               padding: 3px 1px;
              font-weight: 500;
            }

            p{
                font-size: 15px;
                color: #474747;
            }

            .table-border tr th,
            .table-border tr td{
                border: 1px solid #ccc;
                padding: 8px;
                font-size: 14px;
            }

            .table-border tr th{
                font-weight: 800;
                color: #000;
            }
        </style>
    </head>
    <body>
        <div style="width:800px; margin: 0px auto; overflow: hidden; font-family: 'Montserrat', sans-serif;
 border:1px solid #ccc; padding: 25px;">
            <table width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td valign="middle">
                        <table>
                            <tr>
                                <td width="650" valign="center" align="left">
                                    <img src="https://doctto.com/admin_assets/assets/images/logo123.png" alt="" style="height: 60px; width: 150px;">
                                </td>
                                
                                <td width="500" valign="right">
                                    <table cellspacing="0" cellpadding="10" align="right">
                                       
                                        <tr>
                                            <td style="font-size: 20px;"><?=$doc_det->doctor_name?></td> 
                                        </tr>
                                         <tr>
                                             
                                            <td style="font-size: 20px;"><?=$desg?></td>   
                                           
                                        </tr>
                                         <tr>
                                            <td style="font-size: 20px;"><?=$spcl?></td>   
                                           
                                        </tr>
                                        <tr>
                                             <td style="font-size: 20px;"><?=$doc_det->hospital_name?></td>
                                        </tr>
                                        <tr>
                                              <td style="font-size: 20px;"><?=$doc_det->address?></td>
                                        </tr>
                                    </table>
                                </td>
                               
                            </tr>
                        </table>
                    </td>
                </tr>
           <tr>  
                    <td colspan="3">
                        <hr style="margin: 0px" />
                    </td>
                </tr>
              
                <tr style="border-bottom: 1px solid #eee;">
                    <td valign="top">
                        <table>
                            <tr>
                                <td width="490" valign="top">
                                    <div style="font-size: 15px; margin-bottom: 5px; text-align: left;">
                                        <p style="font-size: 20px;"><strong>Patient Name :</strong> <span><?= $patient->patient_name ?></span></p>
                                        <p style="font-size: 20px;"><strong>Age/Gender : </strong><span><?= $patient->patient_age ?>/<?= $patient->patient_gender ?></span></p>
                                    </div>
                                </td>
                                <td width="20">&nbsp;</td>
                                 <td width="490" align="right">
                                    <div style="font-size: 15px; margin-bottom: 5px; text-align: right;">
                                        <?php 
                                            $p_date = date('d-m-Y h:i A');
                                            if(!empty($epresp) && !empty($epresp->created_at)){
                                                $p_date = date('d-m-Y h:i A', $epresp->created_at);
                                            } elseif(!empty($diag) && !empty($diag->created_at)){
                                                $p_date = date('d-m-Y h:i A', $diag->created_at);
                                            } elseif(!empty($pres) && !empty($pres->created_at)){
                                                $p_date = date('d-m-Y h:i A', $pres->created_at);
                                            }
                                        ?>
                                        <p style="margin:0px; padding: 0px 0px 5px 0px;"><strong style="font-size: 20px;">Date : <span><?=$p_date?></span></strong></p>
                                      
                                    </div>
                                </td> 
                            </tr>
                        </table>
                    </td>
                </tr>

                 <tr>
                    <td colspan="3">
                        <hr style="margin: 0px" />
                    </td>
                </tr>

                
                 <tr>
                    <td> <p style="margin:0px; padding:0px 0px 5px 0px; font-size: 16px; color: #000; text-decoration: underline;"><strong>Lab Tests</strong></p></td>
                </tr>
                <tr style="text-align: left;">

                    <td valign="top">
                        <table width="100%" cellpadding="15" cellspacing="0">
                            <tr>
                                <th style="padding:10px 0px;">SI.</th>
                                <th style="padding:10px 0px;">Lab Test</th>
                                <th style="padding:10px 0px;">Description</th>
                            </tr>
                            
                              <?php
                            $ks = 1;
                            foreach ($labtest as $v) {
                                ?>
                            <tr style="padding: 8px 0px">
                                <td style="padding: 8px 0px"><?=$ks ?></td>
                                <td style="padding: 8px 0px" align="left">
                                <?=$v->lab_test_name ?>
                                </td>
                                <td style="padding: 8px 0px" align="left"><?=$v->lab_test_description ?></td>
                         
                            </tr>
                                <?php

                                    $ks++; 
                            }

                            ?>
                        </table>
                    </td>
                </tr>

              

                         <tr> 
                       <td colspan="4"  style="text-align: right;">   <?php if($doc_det->digital_signature!=''){ ?>
                           <img src="<?php echo base_url(); ?>uploads/doctors/<?= $doc_det->digital_signature; ?>" style="width: 100px;">     
                          <?php } ?></td>
                </tr>
                 <tr>
                    <td style="font-size: 17px; text-align: right;" colspan="4"><?=$doc_det->doctor_name?></td> 
                    
                   
                </tr>
                 <tr>
                    <td style="font-size: 17px; text-align: right;" colspan="4"><?=$doc_det->doctor_license_no?></td>
                   
                </tr>
                
                
                
               
        
                      
                   
            
            </table>
            
                                   
        </div>
    </body>
</html>
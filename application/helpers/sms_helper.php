<?php if (!defined('BASEPATH'))

   exit('No direct script access allowed');





function send_message($message = "", $mobile_number) {


   $message = urlencode($message);

   $URL = "http://login.smsmoon.com/API/sms.php"; // connecting url 

   $post_fields = ['username' => 'rythufresh', 'password' => 'vizag@123', 'from' => 'INFOSM', 'to' => $mobile_number, 'msg' => $message, 'type' => 1, 'dnd_check' => 0];

   //file_get_contents("http://login.smsmoon.com/API/sms.php?username=colourmoonalerts&password=vizag@123&from=WEBSMS&to=$mobile_number&msg=$message&type=1&dnd_check=0");

   $ch = curl_init();

   curl_setopt($ch, CURLOPT_URL, $URL);

   curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

   curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);

   curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

   curl_exec($ch);

print_r($ch); die;
   return true;

}



function send_sms($num, $msg = 'testsms') {



   $mobilenumbers = $num; //enter Mobile numbers comma seperated



   $message = $msg; //enter Your Message



   $url = "http://login.smsmoon.com/API/unicodesms.php?";



   $message = urlencode($message);



   $ch = curl_init();



   if (!$ch) {



       die("Couldn't initialize a cURL handle");

   }



   $ret = curl_setopt($ch, CURLOPT_URL, $url);



   curl_setopt($ch, CURLOPT_POST, 1);



   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);



   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);



   curl_setopt($ch, CURLOPT_POSTFIELDS, "username=rythufresh&password=vizag@123&from=INFOSM&to=$mobilenumbers&msg=$message&type=1&dnd_check=0&charset=utf-8");



   $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);



   $curlresponse = curl_exec($ch); // execute



   if (curl_errno($ch)) {



       //echo 'curl error : ' . curl_error($ch);

       if (empty($ret)) {



           // some kind of an error happened

           //die(curl_error($ch));



           curl_close($ch); // close cURL handler

       } else {



           //$info = curl_getinfo($ch);

           //print_r($info);



           curl_close($ch); // close cURL handler

       }

   }

}
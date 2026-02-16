<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;
require_once APPPATH . '/libraries/JWT.php';
class Common_model extends CI_model {

 public function auth()
    {
         //JWT Auth middleware
        $headers = $this->input->get_request_header('Authorization');
        $jwt = $this->config->item('jwt'); //secret key for encode and decode
        $token= "token";
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers , $matches)) {
             $token = $matches[1];
            }
        }
      //echo $token;die;
        try {

           $decoded = JWT::decode($token, $jwt['mobile']['secret'], array('HS256'));
           //echo "tet"; die;
          // echo "<pre>"; print_r($decoded);  die;
           if ($decoded->apikey!=$jwt['mobile']['apikey']) 
           {   //echo "test"; die;
               throw new Exception("Unauthorized");
            }
        } catch (Exception $e) {

          //echo 'Message: ' .$e->getMessage(); die;
            $arr = array('error_code' => "invalid", 'message' => "Unauthorized");
           echo json_encode($arr, JSON_PRETTY_PRINT);die;
        }
    }

}

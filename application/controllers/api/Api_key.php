<?php

use \Firebase\JWT\JWT;
class Api_key extends MY_Controller {

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json; charset=utf-8');
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
        header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers,Authorization,Access-Control-Allow-Origin,Access-Control-Allow-Methods");
    }

   
	
	function generate_token()
	{
		if($this->input->get_post('type') && $this->input->get_post('apikey') && $this->input->get_post('pwd'))
		{
			$jwt = $this->config->item('jwt')['mobile'];
			if($this->input->get_post('type')==$jwt['type'] && $this->input->get_post('apikey')==$jwt['apikey'] && $this->input->get_post('pwd')==$jwt['pwd'])
			{
				$token['apikey'] = $jwt['apikey'];
           	 	$date = new DateTime();
            	$token['iat'] = $date->getTimestamp();
            	$token['exp'] = $date->getTimestamp() + 60*60*$jwt['expiration']; //To here is to generate token
            	$token = JWT::encode($token,$jwt['secret'] ); //This is the output token
			
				$result = array('token' => $token);
            $arr = array('error_code' => "valid", 'message' => "Records Found", "data" => $result);
			}
			else
			{
			$arr = array('error_code' => "invalid", 'message' => "No Records Found");	
			}
			
		}
		else
			{
			$arr = array('error_code' => "invalid", 'message' => "No Records Found");	
			}
		echo json_encode($arr, JSON_PRETTY_PRINT);
		
	}
	
	
	
	}
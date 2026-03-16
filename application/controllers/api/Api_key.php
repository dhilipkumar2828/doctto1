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
        // Try getting from POST/GET first, default to raw input stream for JSON
        $type = $this->input->get_post('type');
        $apikey = $this->input->get_post('apikey');
        $pwd = $this->input->get_post('pwd');

        if (empty($type) || empty($apikey) || empty($pwd)) {
            $stream_data = json_decode($this->input->raw_input_stream, true);
            if (!empty($stream_data)) {
                $type = $stream_data['type'] ?? $type;
                $apikey = $stream_data['apikey'] ?? $apikey;
                $pwd = $stream_data['pwd'] ?? $pwd;
            }
        }

        if ($type && $apikey && $pwd) {
            $jwt_config = $this->config->item('jwt')['mobile'];
            if ($type == $jwt_config['type'] && $apikey == $jwt_config['apikey'] && $pwd == $jwt_config['pwd']) {
                $token_payload = array(
                    'apikey' => $jwt_config['apikey'],
                    'iat' => time(),
                    'exp' => time() + (60 * $jwt_config['expiration'])
                );
                
                $token = JWT::encode($token_payload, $jwt_config['secret']);
                
                $arr = array(
                    'error_code' => "valid", 
                    'message' => "Token generated successfully", 
                    'data' => array('token' => $token)
                );
            } else {
                $arr = array('error_code' => "invalid", 'message' => "Invalid API credentials");
            }
        } else {
            $arr = array('error_code' => "invalid", 'message' => "Missing required parameters (type, apikey, pwd)");
        }
        
        echo json_encode($arr, JSON_PRETTY_PRINT);
    }
	
	
	
	}
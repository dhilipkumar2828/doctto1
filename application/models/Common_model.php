<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use \Firebase\JWT\JWT;
require_once APPPATH . '/libraries/JWT.php';
class Common_model extends CI_model {

    public function auth()
    {
        // JWT Auth middleware
        $headers = $this->input->get_request_header('Authorization');
        if (empty($headers) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $headers = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (empty($headers) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        
        $jwt = $this->config->item('jwt'); // secret key for encode and decode
        $token = "";
        if (!empty($headers)) {
            if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
                $token = $matches[1];
            } else {
                // Fallback for cases where "Bearer " prefix might be missing
                $token = $headers;
            }
        }

        // If still empty, check POST/GET for 'token' parameter
        if (empty($token)) {
            $token = $this->input->get_post('token');
        }

        if (empty($token)) {
            $arr = array('error_code' => "invalid", 'message' => "Token Missing");
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        }

        try {
            $decoded = JWT::decode($token, $jwt['mobile']['secret'], array('HS256'));
            if ($decoded->apikey != $jwt['mobile']['apikey']) {
                throw new Exception("Unauthorized");
            }

            // Identify the doctor or user associated with this token in DB
            if (!empty($token)) {
                $doctor = $this->db->get_where('doctors', ['token' => $token])->row();
                if ($doctor) {
                    return (object)['type' => 'doctor', 'id' => $doctor->id];
                }

                $user = $this->db->get_where('users', ['token' => $token])->row();
                if ($user) {
                    return (object)['type' => 'user', 'id' => $user->id];
                }
            }

            // If token is valid JWT but not yet linked to a specific account
            return (object)['type' => 'jwt', 'id' => null];

        } catch (Exception $e) {
            $arr = array('error_code' => "invalid", 'message' => "Unauthorized");
            echo json_encode($arr, JSON_PRETTY_PRINT);
            die;
        }
    }

}

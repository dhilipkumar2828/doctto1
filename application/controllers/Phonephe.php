<?php

class Phonephe extends MY_Controller {

    function __construct() {
        parent::__construct();
        
    }
    
    function paymentview($order_id) {
                $phonepe_data = $this->db->where("id",$order_id)->get("online_doctor_appointments")->row();
                if(!empty($phonepe_data))
                {
                   // print_r($phonepe_data); die;
                            $phone=$phonepe_data->patient_mobile;
                            $grand_total=$phonepe_data->consultation_fee;

                            $in_paise = intval($grand_total * 100);
                            $phn = strval($phone);
                            $uid = $phonepe_data->patient_id;
                            $order_id = $order_id;
                           
                            $con = new stdClass();

                            $con->merchantId = MERCHANT_ID;
                            $con->merchantTransactionId = $order_id;
                            $con->merchantUserId = $uid;
                            $con->amount = $in_paise;
                            $con->redirectUrl = "https://doctto.com/phonephe/post_payment_redirection/" . $order_id;
                            $con->callbackUrl = "https://doctto.com/phonephe/webhook";
                            $con->mobileNumber = $phn;
                            $con->paymentInstrument = new stdClass();
                            $con->paymentInstrument->type = "PAY_PAGE";
                            
                            $encode = json_encode($con);
                            $encoded = base64_encode($encode);
                            $salt_key = SALT_KEY;
                            $salt_index = KEY_INDEX;
                            $string = $encoded . API_END_POINT . $salt_key;
                            $sha256 = hash("sha256", $string);
                            $final_x_header = $sha256 . '###' . $salt_index;
                            
                            $request_json_decode = new stdClass();
                            $request_json_decode->request = $encoded;
                            $request = json_encode($request_json_decode);
                            
                            $curl = curl_init();
                            curl_setopt_array($curl, [
                                CURLOPT_URL => PAY_URL,
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => "",
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 30,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => "POST",
                                CURLOPT_POSTFIELDS => $request,
                                CURLOPT_HTTPHEADER => [
                                    "Content-Type: application/json",
                                    "X-VERIFY: " . $final_x_header,
                                    "accept: application/json"
                                ],
                            ]);

                            $response = curl_exec($curl);
                            //print_r($response); die;
                            $err = curl_error($curl);

                            curl_close($curl);

                            if ($err) {
                                echo "Error #:" . $err;
                            } else {
                                $res = json_decode($response);
                               // print_r($res); die;
                                if ($res->code == 'PAYMENT_INITIATED') {
                                    redirect($res->data->instrumentResponse->redirectInfo->url);
                                } else {
                                    $arr=array('status'=>FALSE,'message'=>"Server might be problem, please try again later");
                                    echo json_encode($arr);
                                }
                            }
                }
                
            
        
    }


    function post_payment_redirection($order_id) {
        
        $response = $this->db->where('pay_transaction_id', $order_id)->get('webhook_response')->row()->json_file;
        $res = json_decode($response);

        if ($res->code == 'PAYMENT_SUCCESS') {
            //Your code here
            $arr = array("status"=>TRUE,"message"=>"Payment Success");
            echo json_encode($arr);
            die;
        } else {
            //Your code here
            $arr = array("status"=>FALSE,"message"=>"Something went wrong, please try again later");
            echo json_encode($arr);
            die;
        }
    }



    function payment_status($order_id) {
        
        
        $response = $this->db->where('pay_transaction_id', $order_id)->get('webhook_response')->row()->json_file;
        $res = json_decode($response);

        if ($res->code == 'PAYMENT_SUCCESS') 
        {
            $row = $this->db->where('id', $order_id)->get('online_doctor_appointments')->row();


                $this->db->where('date',$row->date);
                $this->db->where('time_slot_name',$row->time_slot_name);
                $this->db->where('time_slot_value',$row->time_slot_value);
                $this->db->where('doctor_status!=','reject');
                $data1 = $this->db->count_all_results("doctor_appointments");
                if($data1>0){
                    $arr = array("status"=>FALSE,'message'=>"Slot already Booked");
                    echo json_encode($arr);
                    die;
                }


            $data = array('patient_id'=>$row->patient_id,'doctor_id'=>$row->doctor_id,'date'=>$row->date,'time_slot_name'=>$row->time_slot_name,'time_slot_value'=>$row->time_slot_value,'patient_name'=>$row->patient_name,'patient_mobile'=>$row->patient_mobile,'patient_age'=>$row->patient_age,'patient_gender'=>$row->patient_gender,'patient_visiting_purpose'=>$row->patient_visiting_purpose,'consultation_fee'=>$row->consultation_fee,'appointment_type'=>$row->type);
            $ins = $this->db->insert("doctor_appointments",$data); 
            //echo $this->db->last_query(); die;
            if($ins)
            {

        
            $appointment_id=$this->db->insert_id();
            $message = "You have new appointment and booking no.".$appointment_id."";

            $this->doNotifications($appointment_id,$row->patient_id,$row->doctor_id,$message);


            $message = "Dear ".$row->patient_name." your booking no.".$appointment_id." is successfully placed, awaiting for doctor confirmation. Thanks & Regards...! DOCTTO";
            $this->doNotifications($appointment_id,$row->doctor_id,$row->patient_id,$message);

            $this->db->select("id,first_name");
            $this->db->where("id",$row->patient_id);
            $data_user = $this->db->get("users")->row();
            $first_name = $data_user->first_name; 

 
            $this->db->select("id,doctor_name");
            $this->db->where("id",$row->doctor_id);
            $this->db->where("doctor_show_status",'active');
            $data_doctor = $this->db->get("doctors")->row();
            $doctor_name = $data_doctor->doctor_name;
             //echo $this->db->last_query(); die;
            /*if($time_slot_name=='morning'){
                $time_slot_value1=$time_slot_value." :00 AM";
            }   
            else if($time_slot_name=='afternoon'){
                $time_slot_value1=$time_slot_value." :00 PM";
            } 
            elseif ($time_slot_name=='evening') {
                $time_slot_value1=$time_slot_value." :00 PM";
            }*/ 

            $otp_message = "Dear ".$row->patient_name." your booking no.".$appointment_id." is successfully placed, awaiting for doctor confirmation. Thanks & Regards...! DOCTTO";
            $template_id = '1407168691886113081';

            $this->User->send_message($otp_message,$row->patient_mobile,$template_id);

            //$date=date("d M,Y",strtotime($date));
            //return array('status'=>TRUE,'message'=>'Appointment Success','first_name'=>$first_name,'doctor_name'=>$doctor_name,'patient_name'=>$patient_name,'date'=>$date,'time_slot_value'=>$time_slot_value); 
        

                $arr = array("status"=>TRUE,"message"=>"Appointment Created Successfully");
                echo json_encode($arr);
            }
            
        }
        else 
        {
            //Your code here
            $arr = array("status"=>FALSE,'message'=>"Server might be problem, please try again");
            echo json_encode($arr);
        }
    }


    /**
     * PhonePe Standard Checkout Webhook Handler
     * Handles webhook events: checkout.order.completed, checkout.order.failed, pg.refund.*
     * Verifies Authorization header using SHA256(username:password)
     */
    function webhook() {
        // Get raw payload
        $payload = file_get_contents('php://input');
        
        // Get Authorization header
        $authorization_header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        
        // Verify webhook authorization
        if (!$this->verifyWebhookAuthorization($authorization_header)) {
            log_message('error', 'PhonePe Webhook: Authorization verification failed');
            http_response_code(401);
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            return;
        }
        
        // Parse webhook payload
        $webhook_data = json_decode($payload, true);
        
        if (!$webhook_data || !isset($webhook_data['event']) || !isset($webhook_data['payload'])) {
            log_message('error', 'PhonePe Webhook: Invalid payload structure');
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid payload']);
            return;
        }
        
        $event = $webhook_data['event'];
        $payload_data = $webhook_data['payload'];
        
        // Store webhook for debugging
        $webhook_log = array(
            'pay_transaction_id' => isset($payload_data['merchantOrderId']) ? $payload_data['merchantOrderId'] : (isset($payload_data['merchantRefundId']) ? $payload_data['merchantRefundId'] : 'unknown'),
            'json_file' => $payload,
            'webhook_type' => 'phonepe_standard_checkout',
            'event_type' => $event,
            'created_at' => time()
        );
        $this->db->insert('webhook_response', $webhook_log);
        
        // Process webhook based on event type
        switch ($event) {
            case 'checkout.order.completed':
                $this->handleOrderCompleted($payload_data);
                break;
                
            case 'checkout.order.failed':
                $this->handleOrderFailed($payload_data);
                break;
                
            case 'pg.refund.accepted':
                $this->handleRefundAccepted($payload_data);
                break;
                
            case 'pg.refund.completed':
                $this->handleRefundCompleted($payload_data);
                break;
                
            case 'pg.refund.failed':
                $this->handleRefundFailed($payload_data);
                break;
                
            default:
                log_message('warning', 'PhonePe Webhook: Unknown event type: ' . $event);
                break;
        }
        
        // Return success response
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Webhook processed']);
    }
    
    /**
     * Verify webhook authorization using SHA256(username:password)
     * Authorization header format: SHA256(username:password)
     * PhonePe sends: Authorization: SHA256<hash_value>
     */
    private function verifyWebhookAuthorization($authorization_header) {
        $webhook_username = defined('PHONEPE_WEBHOOK_USERNAME') ? PHONEPE_WEBHOOK_USERNAME : 'doctto_webhook_user';
        $webhook_password = defined('PHONEPE_WEBHOOK_PASSWORD') ? PHONEPE_WEBHOOK_PASSWORD : 'doctto_webhook_pass_2024';
        
        if (empty($authorization_header)) {
            log_message('error', 'PhonePe Webhook: Authorization header is missing');
            return false;
        }
        
        // Generate expected hash: SHA256(username:password)
        $expected_hash = hash('sha256', $webhook_username . ':' . $webhook_password);
        
        // PhonePe sends Authorization header as: SHA256<hash_value>
        // Remove "SHA256" prefix and any whitespace
        $received_hash = str_replace('SHA256', '', $authorization_header);
        $received_hash = trim($received_hash);
        $received_hash = str_replace('<', '', $received_hash);
        $received_hash = str_replace('>', '', $received_hash);
        $received_hash = trim($received_hash);
        
        // Verify hash matches (case-insensitive comparison)
        if (strtolower($received_hash) === strtolower($expected_hash)) {
            return true;
        }
        
        log_message('error', 'PhonePe Webhook: Hash mismatch. Expected: ' . $expected_hash . ', Received: ' . $received_hash);
        return false;
    }
    
    /**
     * Handle checkout.order.completed event
     */
    private function handleOrderCompleted($payload) {
        $merchant_order_id = $payload['merchantOrderId'] ?? null;
        $order_id = $payload['orderId'] ?? null;
        $state = $payload['state'] ?? 'COMPLETED';
        $amount = isset($payload['amount']) ? $payload['amount'] / 100 : 0; // Convert from paisa to rupees
        
        if (!$merchant_order_id) {
            log_message('error', 'PhonePe Webhook: Missing merchantOrderId in order.completed event');
            return;
        }
        
        // Extract merchant order ID format: APT_<appointment_id>_<timestamp>
        // For appointments, extract appointment ID
        if (strpos($merchant_order_id, 'APT_') === 0) {
            $parts = explode('_', $merchant_order_id);
            $appointment_order_id = isset($parts[1]) ? $parts[1] : null;
            
            if ($appointment_order_id) {
                // Update appointment record
                $this->db->where('id', $appointment_order_id);
                $appointment = $this->db->get('online_doctor_appointments')->row();
                
                if ($appointment) {
                    // Check if slot is still available
                    $this->db->where('date', $appointment->date);
                    $this->db->where('time_slot_name', $appointment->time_slot_name);
                    $this->db->where('time_slot_value', $appointment->time_slot_value);
                    $this->db->where('doctor_status!=', 'reject');
                    $existing = $this->db->count_all_results("doctor_appointments");
                    
                    if ($existing == 0) {
                        // Create confirmed appointment
                        $appointment_data = array(
                            'patient_id' => $appointment->patient_id,
                            'doctor_id' => $appointment->doctor_id,
                            'date' => $appointment->date,
                            'time_slot_name' => $appointment->time_slot_name,
                            'time_slot_value' => $appointment->time_slot_value,
                            'patient_name' => $appointment->patient_name,
                            'patient_mobile' => $appointment->patient_mobile,
                            'patient_age' => $appointment->patient_age,
                            'patient_gender' => $appointment->patient_gender,
                            'patient_visiting_purpose' => $appointment->patient_visiting_purpose,
                            'consultation_fee' => $appointment->consultation_fee,
                            'appointment_type' => $appointment->type,
                            'payment_mode' => 'ONLINE',
                            'order_id' => $order_id,
                            'transaction_id' => $merchant_order_id
                        );
                        
                        $this->db->insert("doctor_appointments", $appointment_data);
                        $confirmed_appointment_id = $this->db->insert_id();
                        
                        // Update online appointment with payment status
                        $this->db->where('id', $appointment_order_id);
                        $this->db->update('online_doctor_appointments', array(
                            'phonepe_order_state' => 'COMPLETED',
                            'payment_status' => 'completed',
                            'phonepe_transaction_id' => $merchant_order_id
                        ));
                        
                        // Send notifications
                        $message = "You have new appointment and booking no." . $confirmed_appointment_id . "";
                        $this->doNotifications($confirmed_appointment_id, $appointment->patient_id, $appointment->doctor_id, $message);
                        
                        $message = "Dear " . $appointment->patient_name . " your booking no." . $confirmed_appointment_id . " is successfully placed, awaiting for doctor confirmation. Thanks & Regards...! DOCTTO";
                        $this->doNotifications($confirmed_appointment_id, $appointment->doctor_id, $appointment->patient_id, $message);
                        
                        // Send SMS
                        $otp_message = "Dear " . $appointment->patient_name . " your booking no." . $confirmed_appointment_id . " is successfully placed, awaiting for doctor confirmation. Thanks & Regards...! DOCTTO";
                        $template_id = '1407168691886113081';
                        
                        $this->User->send_message($otp_message, $appointment->patient_mobile, $template_id);
                        
                        log_message('info', 'PhonePe Webhook: Appointment created successfully. ID: ' . $confirmed_appointment_id);
                    } else {
                        log_message('warning', 'PhonePe Webhook: Slot already booked for appointment ID: ' . $appointment_order_id);
                    }
                }
            }
        }
    }
    
    /**
     * Handle checkout.order.failed event
     */
    private function handleOrderFailed($payload) {
        $merchant_order_id = $payload['merchantOrderId'] ?? null;
        $state = $payload['state'] ?? 'FAILED';
        $error_code = $payload['errorCode'] ?? null;
        $detailed_error_code = $payload['detailedErrorCode'] ?? null;
        
        if (!$merchant_order_id) {
            log_message('error', 'PhonePe Webhook: Missing merchantOrderId in order.failed event');
            return;
        }
        
        // Extract appointment ID from merchant order ID
        if (strpos($merchant_order_id, 'APT_') === 0) {
            $parts = explode('_', $merchant_order_id);
            $appointment_order_id = isset($parts[1]) ? $parts[1] : null;
            
            if ($appointment_order_id) {
                // Update appointment with failed status
                $this->db->where('id', $appointment_order_id);
                $this->db->update('online_doctor_appointments', array(
                    'phonepe_order_state' => 'FAILED',
                    'payment_status' => 'failed',
                    'phonepe_transaction_id' => $merchant_order_id
                ));
                
                log_message('info', 'PhonePe Webhook: Payment failed for appointment ID: ' . $appointment_order_id . ', Error: ' . $error_code);
            }
        }
    }
    
    /**
     * Handle pg.refund.accepted event
     */
    private function handleRefundAccepted($payload) {
        $merchant_refund_id = $payload['merchantRefundId'] ?? null;
        $original_merchant_order_id = $payload['originalMerchantOrderId'] ?? null;
        $state = $payload['state'] ?? 'CONFIRMED';
        
        log_message('info', 'PhonePe Webhook: Refund accepted. Refund ID: ' . $merchant_refund_id . ', Order ID: ' . $original_merchant_order_id);
        
        // Update refund status in database if needed
        // This can be implemented based on your refund tracking requirements
    }
    
    /**
     * Handle pg.refund.completed event
     */
    private function handleRefundCompleted($payload) {
        $merchant_refund_id = $payload['merchantRefundId'] ?? null;
        $original_merchant_order_id = $payload['originalMerchantOrderId'] ?? null;
        $state = $payload['state'] ?? 'COMPLETED';
        $amount = isset($payload['amount']) ? $payload['amount'] / 100 : 0;
        
        log_message('info', 'PhonePe Webhook: Refund completed. Refund ID: ' . $merchant_refund_id . ', Order ID: ' . $original_merchant_order_id . ', Amount: ' . $amount);
        
        // Update refund status in database if needed
        // This can be implemented based on your refund tracking requirements
    }
    
    /**
     * Handle pg.refund.failed event
     */
    private function handleRefundFailed($payload) {
        $refund_id = $payload['refundId'] ?? null;
        $original_merchant_order_id = $payload['originalMerchantOrderId'] ?? null;
        $state = $payload['state'] ?? 'FAILED';
        $error_code = $payload['errorCode'] ?? null;
        $detailed_error_code = $payload['detailedErrorCode'] ?? null;
        
        log_message('error', 'PhonePe Webhook: Refund failed. Refund ID: ' . $refund_id . ', Order ID: ' . $original_merchant_order_id . ', Error: ' . $error_code);
        
        // Update refund status in database if needed
        // This can be implemented based on your refund tracking requirements
    }



      function doNotifications($appointment_id,$sender_id,$recieved_id,$message)
    {
        $title = "New Appointment";
        $date=date('Y-m-d');
            $doc_array = array('appointment_id'=>$appointment_id,'sender_id'=>$sender_id,'recieved_id'=>$recieved_id,'message'=>$message,'created_date'=>$date,'created_at'=>time(),'title'=>$title);
            $ins =$this->db->insert("doctor_notifications",$doc_array);
            if($ins)
            {
                return TRUE;
            }
    }

}

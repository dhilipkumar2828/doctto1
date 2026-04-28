<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

//include Rest Controller library
require APPPATH . '/libraries/REST_Controller.php';
// Load the PhonePe SDK via Composer Autoloader
// require_once FCPATH . 'vendor/autoload.php';

use Restserver\Libraries\REST_Controller;
use PhonePe\payments\v2\standardCheckout\StandardCheckoutClient;
use PhonePe\payments\v2\models\request\builders\StandardCheckoutPayRequestBuilder;
use PhonePe\Env;

/**
 * @property Doctors_model $Doctors_model
 * @property CI_DB_query_builder $db
 * @property CI_Input $input
 */
class Doctors extends REST_Controller
{

    public function __construct()
    {
        /* header('Access-Control-Allow-Origin: *');
         header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
         header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
         parent::__construct();*/
        //load user model

        //$this->load->library('email'); 


        header('Access-Control-Allow-Origin: *');
        header('Content-type: application/json; charset=utf-8');
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
        header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers,Authorization,Access-Control-Allow-Origin,Access-Control-Allow-Methods");
        parent::__construct();
        $this->load->model('Doctors_model');
    //   $this->common_model->auth();

    }

    public function patient_prescription_post()
    {
        $appointment_id = $this->post('appointment_id');

        $chk = $this->Doctors_model->prescription($appointment_id);
        if ($chk == 'error') {
            $this->response($chk, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($chk, REST_Controller::HTTP_OK);
        }

    }

    public function validate_location_post()
    {
        $user_id = $this->post('user_id');
        $latitude = $this->post('latitude');
        $longitude = $this->post('longitude');
        $location = $this->post('location');
        $chk = $this->Doctors_model->validateLocation($user_id, $latitude, $longitude, $location);
        if ($chk == 'error') {
            $this->response($chk, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($chk, REST_Controller::HTTP_OK);
        }

    }

    public function getwelcomescreens_post()
    {
        $chk = $this->Doctors_model->getWelcomeScreens();
        if ($chk == 'error') {
            $this->response($chk, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($chk, REST_Controller::HTTP_OK);
        }
    }

    public function notification_count_post()
    {

        $user_id = $this->input->post('user_id');
        $sql = $this->Doctors_model->notificationCount($user_id);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }


    public function user_dashboard_post()
    {
        $latitude = $this->post('latitude');
        $longitude = $this->post('longitude');
        $user_id = $this->post('user_id');
        $cate = $this->Doctors_model->userDashboard($latitude, $longitude, $user_id);
        if ($cate) {
            $this->response($cate, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($cate, REST_Controller::HTTP_OK);
        }

    }


    public function get_categories_post()
    {
        $status = $this->post('status');
        $cate = $this->Doctors_model->categories_list($status);
        if ($cate) {
            $this->response($cate, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($cate, REST_Controller::HTTP_OK);
        }

    }

    public function doctor_videos_post()
    {
        $sql = $this->Doctors_model->doctor_videos_list();
        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function doctors_banners_post()
    {
        $sql = $this->Doctors_model->doctors_banners_list();
        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function getconsultions_post()
    {
        $sql = $this->Doctors_model->getConsultions();
        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    function search_consultions_symptoms_post()
    {
        $keyword = $this->post('keyword');
        $sql = $this->Doctors_model->searchConsultionsSymptoms($keyword);
        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
    }

    public function symptom_wise_doctors_post()
    {
        $symptom_id = $this->post('symptom_id');
        $sql = $this->Doctors_model->symptomWiseDoctors($symptom_id);
        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }


    public function symptom_post()
    {
        $sql = $this->Doctors_model->symptom_list();
        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function sub_symptom_post()
    {
        $symptom_id = $this->post('symptom_id');
        $sql = $this->Doctors_model->sub_symptom_list($symptom_id);
        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function symptom_subsymptom_wise_doctors_post()
    {
        $symptom_id = $this->post('symptom_id');
        $sub_symptom_id = $this->post('sub_symptom_id');
        $sql = $this->Doctors_model->symptomSubsymptomWiseDoctors($symptom_id, $sub_symptom_id);
        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }


    public function available_doctors_post()
    {
        $latitude = $this->post('latitude');
        $longitude = $this->post('longitude');

        $sql = $this->Doctors_model->doctors_list();

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function search_available_doctors_post()
    {
        $keyword = $this->post('keyword');
        //   $latitude = $this->post('latitude');
        //   $longitude = $this->post('longitude');
        $sql = $this->Doctors_model->searchAvailableDoctors($keyword);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function available_doctors_details_post()
    {
        $title = $this->post('title');
        //$designation = $this->post('designation');
        $doctor_id = $this->post('doctor_id');
        $sql = $this->Doctors_model->doctors_full_details_list($title, $doctor_id);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function doctors_details_post()
    {
        $doctor_id = $this->post('doctor_id');
        $sql = $this->Doctors_model->doctors_details($doctor_id);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }


    public function booking_date_post()
    {
        $sql = $this->Doctors_model->date_list();
        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function category_wise_doctors_post()
    {
        $cat_id = $this->post('cat_id');
        $title = $this->post('title');
        $sql = $this->Doctors_model->doctors_page($cat_id, $title);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }




    public function create_doctor_appointments_post()
    {


        //   print_r($created_date);die;
        $patient_id = $this->input->post('patient_id');
        $doctor_id = $this->input->post('doctor_id');
        $date = $this->input->post('date');
        $time_slot_name = $this->input->post('time_slot_name');
        $time_slot_value = $this->input->post('time_slot_value');
        $patient_name = $this->input->post('patient_name');
        $patient_mobile = $this->input->post('patient_mobile');
        $patient_email = $this->input->post('patient_email');
        $patient_age = $this->input->post('patient_age');
        $patient_gender = $this->input->post('patient_gender');
        $patient_visiting_purpose = $this->input->post('patient_visiting_purpose');
        $consultation_fee = $this->input->post('consultation_fee');
        //   $consultation_fees_id = $this->input->post('consultation_fees_id');
        $appointment_type = $this->input->post('appointment_type');


        $sql = $this->Doctors_model->doctor_appointments($patient_id, $doctor_id, $date, $time_slot_name, $time_slot_value, $patient_name, $patient_mobile, $patient_email, $patient_age, $patient_gender, $patient_visiting_purpose, $consultation_fee, $appointment_type, );


        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    function consultation_fees_post()
    {
        $sql = $this->Doctors_model->consultationFees();

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
    }

    function initiate_phonephe_payment_post()
    {
        $total_amount = $this->post('consultation_fee');
        $patient_id = $this->post('patient_id');
        $doctor_id = $this->post('doctor_id');
        $date = $this->post('date');
        $time_slot_name = $this->post('time_slot_name');
        $time_slot_value = $this->post('time_slot_value');
        $patient_name = $this->post('patient_name');
        $patient_mobile = $this->post('patient_mobile');
        $patient_age = $this->post('patient_age');
        $patient_gender = $this->post('patient_gender');
        $patient_visiting_purpose = $this->post('patient_visiting_purpose');
        $type = $this->post('appointment_type');


        // 0. Check for slot availability
        $this->db->where('date', $date);
        $this->db->where('time_slot_name', $time_slot_name);
        $this->db->where('time_slot_value', $time_slot_value);
        $this->db->where('doctor_id', $doctor_id);
        $this->db->where('doctor_status !=', 'reject');
        $existing = $this->db->get("doctor_appointments")->row();
        if ($existing) {
            $this->response(['status' => FALSE, 'message' => 'Slot already Booked'], REST_Controller::HTTP_OK);
            return;
        }

        // 1. Create temporary appointment record to get a unique ID
        $appointment_data = array(
            'patient_id' => $patient_id,
            'doctor_id' => $doctor_id,
            'date' => $date,
            'time_slot_name' => $time_slot_name,
            'time_slot_value' => $time_slot_value,
            'patient_name' => $patient_name,
            'patient_mobile' => $patient_mobile,
            'patient_age' => $patient_age,
            'patient_gender' => $patient_gender,
            'patient_visiting_purpose' => $patient_visiting_purpose,
            'consultation_fee' => $total_amount,
            'type' => $type,
            'status' => 'pending_payment'
        );

        $this->db->insert("online_doctor_appointments", $appointment_data);
        $order_id = $this->db->insert_id();

        if (!$order_id) {
            $this->response(['status' => 'error', 'message' => 'Failed to create appointment record'], REST_Controller::HTTP_OK);
            return;
        }

        $amount_in_paise = intval($total_amount * 100);
        // Generate a unique transaction ID
        $merchant_transaction_id = 'APT_' . $order_id . '_' . time();

        // 3. Log the attempt in payment_logs
        $log_data = [
            'user_id' => $patient_id,
            'plan_id' => $order_id, // Store appointment ID in plan_id field for tracking
            'type' => 'appointment',
            'merchant_transaction_id' => $merchant_transaction_id,
            'amount' => $total_amount,
            'payment_status' => 'pending',
            'provider' => 'phonepe_v2',
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('payment_logs', $log_data);

        try {
            $clientId = (string)PHONEPE_CLIENT_ID;
            $clientSecret = (string)PHONEPE_CLIENT_SECRET;
            $clientVersion = (int)PHONEPE_CLIENT_VERSION;
            $envString = (PHONEPE_MODE == 'PROD') ?Env::PRODUCTION : Env::UAT;

            $client = StandardCheckoutClient::getInstance($clientId, $clientVersion, $clientSecret, $envString);

            // Redirect URL for verification
            $redirect_url = base_url('api/phonepe_hermes/verify_payment/' . $merchant_transaction_id);

            // 6. Build Request Payload
            $payRequest = StandardCheckoutPayRequestBuilder::builder()
                ->merchantOrderId($merchant_transaction_id)
                ->amount($amount_in_paise)
                ->message("Appointment Booking Payment")
                ->redirectUrl($redirect_url)
                ->udf1((string)$patient_id)
                ->udf2((string)$type)
                ->udf3((string)$order_id)
                ->build();

            // 7. Call PhonePe Pay API
            $response = $client->pay($payRequest);

            // Update log with initiation response
            $this->db->where('merchant_transaction_id', $merchant_transaction_id);
            $this->db->update('payment_logs', [
                'request_payload' => json_encode($payRequest),
                'response_payload' => json_encode($response)
            ]);

            // Link transaction ID to appointment
            $this->db->where('id', $order_id);
            $this->db->update('online_doctor_appointments', ['phonepe_transaction_id' => $merchant_transaction_id]);

            if ($response && method_exists($response, 'getRedirectUrl') && $response->getRedirectUrl()) {
                $this->response([
                    'status' => 'success',
                    'message' => 'Payment initiated',
                    'payment_url' => $response->getRedirectUrl(),
                    'redirect_url' => $redirect_url,
                    'merchantTransactionId' => $merchant_transaction_id,
                    'appointment_id' => $order_id
                ], REST_Controller::HTTP_OK);
            }
            else {
                $this->response([
                    'status' => 'error',
                    'message' => 'Invalid response from PhonePe',
                    'details' => json_encode($response)
                ], REST_Controller::HTTP_OK);
            }

        }
        catch (Throwable $e) {
            $this->response([
                'status' => 'error',
                'message' => 'PhonePe V2 Error: ' . $e->getMessage()
            ], REST_Controller::HTTP_OK);
        }
    }


    public function razerpay_orderid_appointment_post()
    {
        $razorpay_keyid = 'rzp_test_kY71FTFw40NENF';
        $razorpay_secret = 'UAHjDNR6V01i358nRzfJowTK';

        $total_amount = $this->post('consultation_fee');

        $final = (int)round($total_amount * 100);

        $data = array(
            'amount' => $final,
            'currency' => 'INR'
        );
        $payload = json_encode($data);
        $ch = curl_init('https://api.razorpay.com/v1/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$razorpay_keyid:$razorpay_secret");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload))
        );
        $result = curl_exec($ch);
        $order_id = json_decode($result)->id;


        $patient_id = $this->input->post('patient_id');
        $doctor_id = $this->input->post('doctor_id');
        $date = $this->input->post('date');
        $time_slot_name = $this->input->post('time_slot_name');
        $time_slot_value = $this->input->post('time_slot_value');
        $patient_name = $this->input->post('patient_name');
        $patient_mobile = $this->input->post('patient_mobile');
        $patient_email = $this->input->post('patient_email');
        $patient_age = $this->input->post('patient_age');
        $patient_gender = $this->input->post('patient_gender');
        $patient_visiting_purpose = $this->input->post('patient_visiting_purpose');
        $type = $this->input->post('appointment_type');
        // $consultation_fee = $this->input->post('consultation_fee');

        $sql = $this->Doctors_model->razerpayOrderidAppointment($patient_id, $doctor_id, $date, $time_slot_name, $time_slot_value, $patient_name, $patient_mobile, $patient_email, $patient_age, $patient_gender, $patient_visiting_purpose, $total_amount, $order_id, $razorpay_keyid, $type);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function online_appointment_post()
    {
        $appointment_id = $this->input->post('appointment_id');
        $razerpay_order_id = $this->input->post('razerpay_order_id');
        $transaction_id = $this->input->post('transaction_id');
        $sql = $this->Doctors_model->online_appointment($appointment_id, $razerpay_order_id, $transaction_id);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
    }


    public function get_doctor_notifications_post()
    {

        $user_id = $this->input->post('user_id');
        $sql = $this->Doctors_model->get_doctor_notifications($user_id);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function update_notifications_post()
    {

        $user_id = $this->input->post('user_id');
        $sql = $this->Doctors_model->updateNotifications($user_id);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function appointment_status_post()
    {

        $user_id = $this->post('user_id');
        //$appointment_id = $this->input->post('appointment_id');
        $appointment_status = $this->post('doctor_status') ? $this->post('doctor_status') : $this->post('status');

        // Map app-side status values to DB status values
        $status_map = array(
            'pending' => 'active', // App sends "pending" → DB stores "active"
            'accepted' => 'accept', // App sends "accepted" → DB stores "accept"
            'cancelled' => 'reject', // App sends "cancelled" → DB stores "reject"
            'canceled' => 'reject', // alternate spelling
        );
        if (isset($status_map[$appointment_status])) {
            $appointment_status = $status_map[$appointment_status];
        }

        $sql = $this->Doctors_model->appointment_status($user_id, $appointment_status);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function appointment_details_post()
    {
        $appointment_id = $this->input->post('appointment_id');
        $user_id = $this->input->post('user_id');
        $sql = $this->Doctors_model->appointment_details($appointment_id, $user_id);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function user_selected_appointment_type_post()
    {
        $doctor_id = $this->input->post('doctor_id');
        $sql = $this->Doctors_model->userSelectedAppointment_type($doctor_id);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }


    public function cancel_appointment_post()
    {
        $patient_id = $this->input->post('patient_id');
        $appointment_id = $this->input->post('appointment_id');
        $reason = $this->input->post('reason');
        $comments = $this->input->post('comments');
        $sql = $this->Doctors_model->appointment_cancel($patient_id, $appointment_id, $reason, $comments);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    public function upload_images_post()
    {
        // $fullname = $this->input->post('fullname');
        // $mobile = $this->input->post('mobile');
        // $subject = $this->input->post('subject');
        // $message = $this->input->post('message');
        //
        $sql = $this->Doctors_model->upload_images();

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
    }

    public function report_issue_post()
    {
        $user_id = $this->input->post('user_id');
        $fullname = $this->input->post('fullname');
        $mobile = $this->input->post('mobile');
        $subject = $this->input->post('subject');
        $message = $this->input->post('message');

        $sql = $this->Doctors_model->report_issue($fullname, $mobile, $subject, $message, $user_id);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
    }

    public function user_contact_post()
    {
        // $email = $this->input->post('email');
        // $mobile = $this->input->post('mobile');

        $sql = $this->Doctors_model->user_contact();

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
    }

    public function timings_post()
    {
        $doctors_id = $this->input->post('doctor_id');
        $date = $this->input->post('date');
        $session = $this->input->post('session');
        $sql = $this->Doctors_model->timings($doctors_id, $date, $session);
        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }

    }

    // ==================== DOCTOR SUBSCRIPTION PAYMENT METHODS ====================

    public function get_doctor_subscription_plans_post()
    {
        $sql = $this->Doctors_model->getDoctorSubscriptionPlans();
        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
    }

    public function initiate_subscription_payment_post()
    {
        // Get raw input for JSON parsing
        $raw_input = $this->input->raw_input_stream;

        // Try to get data from POST first, then from raw input
        $doctor_id = $this->input->post('doctor_id');
        $plan_id = $this->input->post('plan_id');
        $payment_method = $this->input->post('payment_method');

        // If POST data is empty, try to parse JSON from raw input
        if (empty($doctor_id) && !empty($raw_input)) {
            $json_data = json_decode($raw_input, true);
            if ($json_data) {
                $doctor_id = isset($json_data['doctor_id']) ? $json_data['doctor_id'] : null;
                $plan_id = isset($json_data['plan_id']) ? $json_data['plan_id'] : null;
                $payment_method = isset($json_data['payment_method']) ? $json_data['payment_method'] : null;
            }
        }

        // Validate required fields
        if (empty($doctor_id) || empty($plan_id) || empty($payment_method)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Missing required fields: doctor_id, plan_id, and payment_method are required',
                'debug' => [
                    'doctor_id' => $doctor_id,
                    'plan_id' => $plan_id,
                    'payment_method' => $payment_method,
                    'raw_input' => $raw_input
                ]
            ], REST_Controller::HTTP_OK);
            return;
        }

        if ($payment_method == 'phonepe') {
            $sql = $this->Doctors_model->initiatePhonePeSubscriptionPayment($doctor_id, $plan_id);
        }
        else {
            $sql = $this->Doctors_model->initiateRazorpaySubscriptionPayment($doctor_id, $plan_id);
        }

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
    }

    /**
     * Get PhonePe SDK configuration for frontend
     */
    public function get_phonepe_sdk_config_post()
    {
        $this->load->library('PhonePeSDK');

        $config = array(
            'merchantId' => 'M1Y5YWMA86HR',
            'saltKey' => '168028f5-f3cf-40e3-a320-120926e1dcfb',
            'saltIndex' => 1,
            'environment' => 'PRODUCTION' // Change to 'SANDBOX' for testing
        );

        $phonepeSDK = new PhonePeSDK($config);
        $sdkConfig = $phonepeSDK->getSDKConfig();

        $this->response([
            'status' => TRUE,
            'data' => $sdkConfig
        ], REST_Controller::HTTP_OK);
    }

    /**
     * Verify PhonePe payment status
     */
    public function verify_phonepe_payment_post()
    {
        $merchant_transaction_id = $this->input->post('merchant_transaction_id');

        if (empty($merchant_transaction_id)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Merchant transaction ID is required'
            ], REST_Controller::HTTP_OK);
            return;
        }

        $this->load->library('PhonePeSDK');

        $config = array(
            'merchantId' => 'M1Y5YWMA86HR',
            'saltKey' => '168028f5-f3cf-40e3-a320-120926e1dcfb',
            'saltIndex' => 1,
            'environment' => 'PRODUCTION'
        );

        $phonepeSDK = new PhonePeSDK($config);
        $paymentStatus = $phonepeSDK->checkPaymentStatus($merchant_transaction_id);

        if ($paymentStatus) {
            // Process payment result
            $this->Doctors_model->processPhonePeSubscriptionPayment(
                $merchant_transaction_id,
                $paymentStatus['code'],
                $paymentStatus['data']['amount'] / 100
            );

            $this->response([
                'status' => TRUE,
                'data' => $paymentStatus
            ], REST_Controller::HTTP_OK);
        }
        else {
            $this->response([
                'status' => FALSE,
                'message' => 'Unable to verify payment status'
            ], REST_Controller::HTTP_OK);
        }
    }

    /**
     * Verification for PhonePe Subscription Redirect Flow (V2)
     */
    public function verify_subscription_payment_get($merchant_transaction_id)
    {
        $result = $this->Doctors_model->verifyPhonePeSubscriptionPayment($merchant_transaction_id);

        // Redirect back to admin panel or mobile app with result
        // For web:
        $status = $result['status'] ? 'success' : 'failed';
        redirect(base_url('admin/doctor_subscription_plans?payment=' . $status . '&tid=' . $merchant_transaction_id));
    }

    public function subscription_payment_callback_post()
    {
        // Get raw input for JSON parsing
        $raw_input = $this->input->raw_input_stream;

        // Try to get data from POST first, then from raw input
        $payment_method = $this->input->post('payment_method');
        $transaction_id = $this->input->post('transaction_id');
        $payment_status = $this->input->post('payment_status');
        $amount = $this->input->post('amount');

        // If POST data is empty, try to parse JSON from raw input
        if (empty($payment_method) && !empty($raw_input)) {
            $json_data = json_decode($raw_input, true);
            if ($json_data) {
                $payment_method = isset($json_data['payment_method']) ? $json_data['payment_method'] : null;
                $transaction_id = isset($json_data['transaction_id']) ? $json_data['transaction_id'] : null;
                $payment_status = isset($json_data['payment_status']) ? $json_data['payment_status'] : null;
                $amount = isset($json_data['amount']) ? $json_data['amount'] : null;
            }
        }

        // Validate required fields
        if (empty($payment_method) || empty($transaction_id) || empty($payment_status)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Missing required fields: payment_method, transaction_id, and payment_status are required',
                'debug' => [
                    'payment_method' => $payment_method,
                    'transaction_id' => $transaction_id,
                    'payment_status' => $payment_status,
                    'amount' => $amount,
                    'raw_input' => $raw_input
                ]
            ], REST_Controller::HTTP_OK);
            return;
        }

        if ($payment_method == 'phonepe') {
            $sql = $this->Doctors_model->processPhonePeSubscriptionPayment($transaction_id, $payment_status, $amount);
        }
        else {
            $sql = $this->Doctors_model->processRazorpaySubscriptionPayment($transaction_id, $payment_status, $amount);
        }

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
    }

    public function setup_autopay_post()
    {
        $doctor_id = $this->input->post('doctor_id');
        $subscription_id = $this->input->post('subscription_id');
        $autopay_agreement_id = $this->input->post('autopay_agreement_id');

        $sql = $this->Doctors_model->setupDoctorAutopay($doctor_id, $subscription_id, $autopay_agreement_id);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
    }

    public function get_doctor_subscriptions_post()
    {
        $doctor_id = $this->input->post('doctor_id');
        $sql = $this->Doctors_model->getDoctorSubscriptions($doctor_id);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
    }

    public function cancel_doctor_subscription_post()
    {
        $doctor_id = $this->input->post('doctor_id');
        $subscription_id = $this->input->post('subscription_id');

        $sql = $this->Doctors_model->cancelDoctorSubscription($doctor_id, $subscription_id);

        if ($sql) {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
        else {
            $this->response($sql, REST_Controller::HTTP_OK);
        }
    }

    // Get terms and conditions for doctor subscriptions
    public function get_doctor_subscription_terms_post()
    {
        $plan_id = $this->input->post('plan_id');

        $this->load->model('admin/Terms_conditions_model');
        $terms = $this->Terms_conditions_model->get_terms_with_sections('doctor', $plan_id);

        if ($terms) {
            $this->response([
                'status' => TRUE,
                'data' => $terms
            ], REST_Controller::HTTP_OK);
        }
        else {
            $this->response([
                'status' => FALSE,
                'message' => 'No terms and conditions found'
            ], REST_Controller::HTTP_OK);
        }
    }

    // Accept terms and conditions
    public function accept_terms_post()
    {
        // Get raw input for debugging
        $raw_input = $this->input->raw_input_stream;

        // Try to get data from POST first, then from raw input
        $doctor_id = $this->input->post('doctor_id');
        $terms_id = $this->input->post('terms_id');
        $subscription_id = $this->input->post('subscription_id');

        // If POST data is empty, try to parse JSON from raw input
        if (empty($doctor_id) && !empty($raw_input)) {
            $json_data = json_decode($raw_input, true);
            if ($json_data) {
                $doctor_id = isset($json_data['doctor_id']) ? $json_data['doctor_id'] : null;
                $terms_id = isset($json_data['terms_id']) ? $json_data['terms_id'] : null;
                $subscription_id = isset($json_data['subscription_id']) ? $json_data['subscription_id'] : null;
            }
        }

        // Validate required fields
        if (empty($doctor_id) || empty($terms_id)) {
            $this->response([
                'status' => FALSE,
                'message' => 'Missing required fields: doctor_id and terms_id are required',
                'debug' => [
                    'doctor_id' => $doctor_id,
                    'terms_id' => $terms_id,
                    'subscription_id' => $subscription_id,
                    'raw_input' => $raw_input
                ]
            ], REST_Controller::HTTP_OK);
            return;
        }

        $this->load->model('admin/Terms_conditions_model');

        if ($this->Terms_conditions_model->log_terms_acceptance($doctor_id, 'doctor', $terms_id, $subscription_id, $this->input->ip_address(), $this->input->user_agent())) {
            $this->response([
                'status' => TRUE,
                'message' => 'Terms accepted successfully'
            ], REST_Controller::HTTP_OK);
        }
        else {
            $this->response([
                'status' => FALSE,
                'message' => 'Unable to log terms acceptance'
            ], REST_Controller::HTTP_OK);
        }
    }

    // Get doctor subscription status
    public function get_doctor_subscription_status_post()
    {
        // Get raw input for JSON parsing
        $raw_input = $this->input->raw_input_stream;

        // Try to get data from POST first, then from raw input
        $doctor_id = $this->input->post('doctor_id');

        // If POST data is empty, try to parse JSON from raw input
        if (empty($doctor_id) && !empty($raw_input)) {
            $json_data = json_decode($raw_input, true);
            if ($json_data) {
                $doctor_id = isset($json_data['doctor_id']) ? $json_data['doctor_id'] : null;
            }
        }

        if (!$doctor_id) {
            $this->response([
                'status' => FALSE,
                'message' => 'Doctor ID is required',
                'debug' => [
                    'doctor_id' => $doctor_id,
                    'raw_input' => $raw_input
                ]
            ], REST_Controller::HTTP_OK);
            return;
        }

        $this->load->model('admin/doctor_subscriptions_model');
        $subscription = $this->doctor_subscriptions_model->get_active_subscription($doctor_id);

        if ($subscription) {
            $this->response([
                'status' => TRUE,
                'data' => [
                    'has_active_subscription' => true,
                    'subscription' => $subscription
                ]
            ], REST_Controller::HTTP_OK);
        }
        else {
            $this->response([
                'status' => TRUE,
                'data' => [
                    'has_active_subscription' => false,
                    'subscription' => null
                ]
            ], REST_Controller::HTTP_OK);
        }
    }

}



?>
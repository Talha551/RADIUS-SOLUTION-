<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

// Load Composer autoloader if not already loaded
if (!class_exists('TesseractOCR')) {
    require_once FCPATH . 'vendor/autoload.php';
}

use thiagoalessio\TesseractOCR\TesseractOCR;

// Check if Groq PHP library is installed
if (!class_exists('\LucianoTonet\GroqPHP\Groq')) {
    log_message('error', 'Groq PHP library is not installed. Please run: composer require lucianotonet/groq-php');
}

require APPPATH . 'libraries/crontab.php';
require APPPATH . 'libraries/routeros_api.class.php';
require APPPATH . 'libraries/Format.php';


//use phpseclib3\Net\SSH2;
use phpseclib\Net\SSH2;

class Payments extends CI_Controller {

    public function __construct() {
    parent::__construct();
    $this->load->database();
    $this->load->model('api_model');
    }

    public function BillInquiry()
    {
        header('Content-Type: application/json');

        // Support both JSON and form-data POST
        if ($this->input->server('CONTENT_TYPE') === 'application/json') {
            $input = json_decode(file_get_contents('php://input'), true);
            $username = isset($input['username']) ? $input['username'] : '';
            $password = isset($input['password']) ? $input['password'] : '';
            $consumer_number = isset($input['Consumer_number']) ? $input['Consumer_number'] : '';
            $bank_Mnemonic = isset($input['Bank_Mnemonic']) ? $input['Bank_Mnemonic'] : '';
            $reserved = isset($input['Reserved']) ? $input['Reserved'] : '';
        } else {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $consumer_number = $this->input->post('Consumer_number');
            $bank_Mnemonic = $this->input->post('Bank_Mnemonic');
            $reserved = $this->input->post('Reserved');
        }

        if(!isset($username) || !isset($password) || !isset($consumer_number) || 
            $username == "" || $password == "" || $consumer_number == ""){
            log_message('error', 'Failed to get epApiBillInqiry: ' . $consumer_number);
            echo json_encode([
                'response_Code' => '03',
                'Response_Message' => 'Unknown Error / Bad Transaction',
                'status' => ' Unknown Error / Bad Transaction '
            ]);
            return;
        }

        if (
            ($username == "paceapi" && $password == "Pace@9900@957") ||
            ($username == "epaisaapi" && $password == "Pace@8800@717")
        ) {
            $result = $this->api_model->epApiBillInqiry($consumer_number);

            if (!empty($result) && $result->bill_status == 'U' || !empty($result) && $result->bill_status == 'P') {
                log_message('info', 'Success epApiBillInqiry: ' . $consumer_number);

                if($result->bill_status == 'P'){
                    $lastinvoice = $this->api_model->get_eplastinvoice($result->username);
                    echo json_encode([
                            'response_Code' => $result->response_Code,
                            'consumer_Detail' => $result->consumer_Detail,
                            'bill_status' => $result->bill_status,
                            'due_date' => $result->due_date,
                            'amount_within_dueDate' => $result->amount_within_dueDate,
                            'amount_after_dueDate' => $result->amount_after_dueDate,
                            'billing_month' => $result->billing_month,
                            'date_paid' => $lastinvoice->date_paid, 
                            'amount_paid' => $lastinvoice->amount_paid,
                            'tran_auth_Id' => $lastinvoice->transid,
                            'reserved' => 'Bill already paid'
                        ]);
                    return;

                }else{
                    echo json_encode($result);
                    return;
                }

            } elseif(empty($result)) {
                log_message('error', 'Failed to get epApiBillInqiry: ' . $consumer_number);
                echo json_encode([
                    'response_Code' => '01',
                    'Response_Message' => 'CUSTOMER_NOT_FOUND',
                    'status' => 'RESPONSE_CUSTOMER_RELATIONSHIP_NOT_FOUND'
                ]);
                return;

            } elseif($result->bill_status == 'B') {
                log_message('error', 'Failed to get epApiBillInqiryk: ' . $consumer_number);
                echo json_encode([
                    'response_Code' => '02',
                    'Response_Message' => 'Consumer Number Block',
                    'status' => ' Consumer Number Block'
                ]);
                return;

            } else {
                log_message('error', 'Failed to get epApiBillInqiry: ' . $consumer_number);
                echo json_encode([
                    'response_Code' => '03',
                    'Response_Message' => 'Unknown Error / Bad Transaction',
                    'status' => ' Unknown Error / Bad Transaction '
                ]);
                return;
            }

        } else {
            echo json_encode([
                'response_Code' => '04',
                'Response_Message' => 'Invalid Data userid provided wrong',
                'status' => 'Invalid Username & password, Bank Mnemoic'
            ]);
            return;
            
        }
    }

    // Bill Payment API 
    public function BillPayment()
    {
        header('Content-Type: application/json');

        // Support both JSON and form-data POST
        if ($this->input->server('CONTENT_TYPE') === 'application/json') {
            $input = json_decode(file_get_contents('php://input'), true);
            $username = isset($input['username']) ? $input['username'] : '';
            $password = isset($input['password']) ? $input['password'] : '';
            $consumer_number = isset($input['consumer_number']) ? $input['consumer_number'] : '';
            $tran_auth_id = isset($input['tran_auth_id']) ? $input['tran_auth_id'] : '';
            $transaction_amount = isset($input['transaction_amount']) ? $input['transaction_amount'] : '';
            $tran_date = isset($input['tran_date']) ? $input['tran_date'] : '';
            $tran_time = isset($input['tran_time']) ? $input['tran_time'] : '';
            $bank_Mnemonic = isset($input['bank_mnemonic']) ? $input['bank_mnemonic'] : '';
            $reserved = isset($input['Reserved']) ? $input['Reserved'] : '';
        } else {
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $consumer_number = $this->input->post('consumer_number');
            $tran_auth_id = $this->input->post('tran_auth_id');
            $transaction_amount = $this->input->post('transaction_amount');
            $tran_date = $this->input->post('tran_date');
            $tran_time = $this->input->post('tran_time');
            $bank_Mnemonic = $this->input->post('bank_mnemonic');
            $reserved = $this->input->post('Reserved');
        }

        // Validation block
        $invalid = false;
        $invalid_reason = '';
        // username: required, string, max 60
        if (empty($username) || !is_string($username) || strlen($username) > 60) {
            $invalid = true;
            $invalid_reason = 'Invalid username';
        }
        // password: required, string, max 60
        else if (empty($password) || !is_string($password) || strlen($password) > 60) {
            $invalid = true;
            $invalid_reason = 'Invalid password';
        }
        // consumer_number: required, string, max 24 (but 1LINK max 20)
        else if (empty($consumer_number) || !is_string($consumer_number) || strlen($consumer_number) > 24) {
            $invalid = true;
            $invalid_reason = 'Invalid consumer_number';
        }
        // tran_auth_id: required, string, exactly 6 digits
        else if (empty($tran_auth_id) || !preg_match('/^[0-9]{6}$/', $tran_auth_id)) {
            $invalid = true;
            $invalid_reason = 'Invalid tran_auth_id';
        }
        // transaction_amount: required, string, max 12, only digits
        else if (empty($transaction_amount) || !preg_match('/^[0-9]{1,12}$/', $transaction_amount)) {
            $invalid = true;
            $invalid_reason = 'Invalid transaction_amount';
        }
        // tran_date: required, string, exactly 8 digits, valid date YYYYMMDD
        else if (empty($tran_date) || !preg_match('/^[0-9]{8}$/', $tran_date) || !checkdate(substr($tran_date,4,2), substr($tran_date,6,2), substr($tran_date,0,4))) {
            $invalid = true;
            $invalid_reason = 'Invalid tran_date';
        }
        // tran_time: required, string, exactly 6 digits, valid time HHMMSS
        else if (empty($tran_time) || !preg_match('/^[0-9]{6}$/', $tran_time)) {
            $invalid = true;
            $invalid_reason = 'Invalid tran_time';
        }
        else {
            $h = intval(substr($tran_time,0,2));
            $m = intval(substr($tran_time,2,2));
            $s = intval(substr($tran_time,4,2));
            if ($h > 23 || $m > 59 || $s > 59) {
                $invalid = true;
                $invalid_reason = 'Invalid tran_time';
            }
        }
        // bank_mnemonic: required, string, max 8
        if (!$invalid && (empty($bank_Mnemonic) || !is_string($bank_Mnemonic) || strlen($bank_Mnemonic) > 8)) {
            $invalid = true;
            $invalid_reason = 'Invalid bank_mnemonic';
        }
        // reserved: optional, string, max 200
        if (!$invalid && !empty($reserved) && strlen($reserved) > 200) {
            $invalid = true;
            $invalid_reason = 'Invalid reserved';
        }

        if ($invalid) {
            echo json_encode([
                "response_Code" => "04",
                "Response_Message" => "Invalid Data ".$invalid_reason,
                "status" => "Invalid Username & password, Bank Mnemoic"
            ]);
            return;
        }

        if (empty($tran_time)) {
            $tran_time = date("His"); // Format as HHMMSS (e.g. 143800)
        }
        if (empty($bank_Mnemonic)) {
            $bank_Mnemonic = 'BPI';
        }
        if (empty($reserved)) {
            $reserved = 'Bank Payment Auto Recharge';
        }

        if (
            ($username == "paceapi" && $password == "Pace@9900@957") ||
            ($username == "epaisaapi" && $password == "Pace@8800@717")
        ) {

            $paymentInfo = array(
                'apiuser' => $username,
                'apipassword' => $password,
                'consumer_number' => $consumer_number,
                'tran_auth_id' => $tran_auth_id,
                'transaction_amount' => $transaction_amount,
                'tran_date' => $tran_date,
                'tran_time' => $tran_time,
                'bank_mnemonic' => $bank_Mnemonic,
                'reserved' => $reserved
            );

            $this->db->select("A.username, A.payname, B.owner");
            $this->db->from('tbl_userdocs as A');
            $this->db->join('rm_users as B', 'A.username = B.username', 'left');
            $this->db->where('A.payid', $consumer_number);
            $query = $this->db->get();
            $resultUserInfo = $query->row();

            if ($query->num_rows() > 0) {

                $this->db->select("tran_auth_id");
                $this->db->from('tbl_eppayments as A');
                $this->db->where('A.tran_auth_id', $tran_auth_id);
                $queryTransAuthId = $this->db->get();
                $resultTransAuthId = $queryTransAuthId->row();

                if ($queryTransAuthId->num_rows() > 0) {
                    echo json_encode(array(
                        'response_Code' => '03',
                        'Identification_parameter' => 'Duplicate Transaction',
                        'reserved' => 'Duplicate Transaction'
                    ));
                    return;
                } else {
                    //Insert Customer Payment Details to Database
                    $resultPayInsert = $this->api_model->epApiEpPayment($paymentInfo);

                    // UPDATE USER EXPIRTY
                    $date1 = Date("Y-m-d");  // Service Date
                    $date2 = date('Y-m-d', strtotime("+1 month", strtotime($date1)));
                    $updateUserExpiryDate = array('expiration' => $date2);

                    $updateUserExpiry = $this->api_model->updateUserExpiry($consumer_number, $updateUserExpiryDate); // Temprorary Enable User 

                    // Return Response to API
                    echo json_encode(array(
                        'response_Code' => '00',
                        'Identification_parameter' => $resultUserInfo->payname,
                        'reserved' => 'Payment received Thank you'
                    ));
                    return;
                }
            } else {
                echo json_encode(array(
                    'response_Code' => '01',
                    'Identification_parameter' => 'CUSTOMER_NOT_FOUND',
                    'reserved' => 'RESPONSE_CUSTOMER_RELATIONSHIP_NOT_FOUND'
                ));
                return;
            }
        } else {
            echo json_encode(array(
                'response_Code' => '05',
                'Identification_parameter' => 'Invalid API Username and Password',
                'reserved' => 'Invalid API Request'
            ));
            return;
        }
    }

    public function UserInquiry_post()
    {
        
        $username = $this->post("username");
        $password = $this->post("password");
        $ppp_user = $this->post("ppp_user");

        if($username == "paceapi" && $password == "Pace@9900@957"){

            $result = $this->api_model->userInqiryIPPortal($ppp_user);

            if (!empty($result)){
                $this->response($result, REST_Controller::HTTP_OK);
            } else {
                $this->response(array('response_Code' => '01', 
                                    'Response_Message' => 'CUSTOMER_NOT_FOUND', 
                                    'status' => 'RESPONSE_CUSTOMER_RELATIONSHIP_NOT_FOUND'));
            }
        }else {
            $this->response(array('response_Code' => '04', 
                                'Response_Message' => 'Username '.$username.' Wrong username and password', 
                                'status' => 'Invalid Username & password, Bank Mnemoic'));
        }
    }
}
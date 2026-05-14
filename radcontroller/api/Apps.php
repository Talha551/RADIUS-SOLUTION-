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
/**
 * Class : Whatsappapi (WhatsappApiController)
 * WhatsApp API controller to send notifications for expiring users
 * Uses native PHP cURL instead of Guzzle for PHP 5.4 compatibility
 * 
 * Install required packages:
 * sudo yum install tesseract (Centos 7)
 * composer require thiagoalessio/tesseract_ocr:^2.12
 * 
 *      THIS IS WAHTSAPP.cOM NOT IN USE ANYMORE
 */
class Apps extends CI_Controller
{
    //private $api_key = 'nTpSFTanzUI0wcEyO81QE2pJb5N8ZogwssE7jsDZ9330036f';
    //private $instance_id = '51667';
    //private $api_base_url = 'https://waapi.app/api/v1';

    private $api_key = 'PaceTelecom@2025';
    private $api_base_url = 'http://portal.pace-tel.com/api';
    private $instance_id = 'malaysiatest';
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('whatsappapi_model');
        $this->load->model('Services_model');
        $this->load->model('Invoices_model');
        $this->load->model('users_model');
        $this->load->model('Apps_model');
        
        $this->load->helper('url');
        $this->load->library('session');
    }
    
    /**
     * Index function - default entry point
     */
    public function index()
    {
        echo json_encode(array('status' => 'error', 'message' => 'Direct access not allowed'));
    }
    
    /**
     * API function to search user data
     * Accepts: username, PaymentID, mobilenumber
     * Returns: User data from rm_users, tbl_userdocs, and tbl_services
     */
    public function search_user()
    {
        // Set content type to JSON
        header('Content-Type: application/json');
        
        // Check if request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Only POST method is allowed'
            ));
            return;
        }
        
        // Get API key from headers or POST data
        $api_key = $this->input->get_request_header('X-API-Key') ?: $this->input->post('api_key');
        
        // Validate API key
        if (!$this->Apps_model->validate_api_key($api_key)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Invalid API key'
            ));
            return;
        }
        
        // Get required parametersapps
        $username = $this->input->post('username');
        $payment_id = $this->input->post('PaymentID');
        $mobile_number = $this->input->post('mobilenumber');
        
        // Validate required parameters
        if (empty($username) || empty($payment_id) || empty($mobile_number)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Missing required parameters: username, PaymentID, mobilenumber'
            ));
            return;
        }
        
        // Search for user data
        $user_data = $this->Apps_model->search_user_data($username, $mobile_number, $payment_id);
        
        if ($user_data) {
            echo json_encode(array(
                'status' => 'success',
                'data' => $user_data
            ));
        } else {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'No user found with the provided parameters'
            ));
        }
    }
    
    /**
     * API function to get user invoices
     * Accepts: username, fromdate, todate (optional)
     * Returns: List of invoices from tbl_invoices and tbl_services
     */
    public function get_invoices()
    {
        // Set content type to JSON
        header('Content-Type: application/json');
        
        // Check if request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Only POST method is allowed'
            ));
            return;
        }
        
        // Get JSON input
        $json_input = file_get_contents('php://input');
        $post_data = json_decode($json_input, true);
        
        // If JSON parsing failed, try regular POST data
        if (json_last_error() !== JSON_ERROR_NONE) {
            $post_data = $_POST;
        }
        
        // Get API key from headers or POST data
        $api_key = $this->input->get_request_header('X-API-Key');
        if (empty($api_key)) {
            $api_key = isset($post_data['apikey']) ? $post_data['apikey'] : (isset($post_data['api_key']) ? $post_data['api_key'] : null);
        }
        
        // Validate API key
        if (!$this->Apps_model->validate_api_key($api_key)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Invalid API key'
            ));
            return;
        }
        
        // Get required parameters

        $username = $this->input->post('username');
        $from_date = $this->input->post('from_date');
        $to_date = $this->input->post('to_date');
        
        // Validate required parameters
        if (empty($username)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Missing required parameter: username'
            ));
            return;
        }
        
        // Get user invoices
        $invoices = $this->Apps_model->get_user_invoices($username, $from_date, $to_date);
        
        if (!empty($invoices)) {
            echo json_encode(array(
                'status' => 'success',
                'data' => $invoices
            ));
        } else {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'No invoices found for the provided parameters'
            ));
        }
    }
    
    /**
     * API function to get user online status
     * Accepts: username
     * Returns: true/false based on acctstoptime
     */
    public function get_online_status()
    {
        // Set content type to JSON
        header('Content-Type: application/json');
        
        // Check if request method is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Only POST method is allowed'
            ));
            return;
        }
        
        // Get API key from headers or POST data
        $api_key = $this->input->get_request_header('X-API-Key');
        if (empty($api_key)) {
            $api_key = $this->input->post('apikey');
            if (empty($api_key)) {
                $api_key = $this->input->post('api_key');
            }
        }
        
        // Validate API key
        if (!$this->Apps_model->validate_api_key($api_key)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Invalid API key'
            ));
            return;
        }
        
        // Get required parameters
        $username = $this->input->post('username');
        
        // Validate required parameters
        if (empty($username)) {
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Missing required parameter: username'
            ));
            return;
        }
        
        // Get user online status
        $is_online = $this->Apps_model->get_user_online_status($username);
        
        echo json_encode(array(
            'status' => 'success',
            'data' => array(
                'username' => $username,
                'is_online' => $is_online
            )
        ));
    }
    
}

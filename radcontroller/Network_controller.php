<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

class Network_controller extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Network_model');
        $this->load->model('users_model');
        $this->load->model('Other_model');
        $this->isLoggedIn();   
    }

    public function index()
    {
        $this->global['pageTitle'] = 'Network Segments : Dashboard';
        $this->loadViews("dashboard", $this->global, NULL, NULL);
    }

    public function segmentList()
    {
        /*if($this->perm_listservices == 0){
            $this->session->set_flashdata('error', 'You are not allowed to view network segments. Contact Admin to allow permission');
            redirect('dashboard');
        }*/

        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $segmentType = $this->input->post('segmentType');
        $data['searchText'] = $searchText;
        $data['segmentType'] = $segmentType;
        
        $this->load->library('pagination');
        
        $count = $this->Network_model->segmentListingCount($searchText, $segmentType);
        $returns = $this->paginationCompress("segmentList/", $count, 10);
        
        $data['segmentRecords'] = $this->Network_model->segmentListing($searchText, $returns["page"], $returns["segment"], $segmentType);
        $data['segmentTypes'] = array(
            0 => 'Region',
            1 => 'Zone',
            2 => 'Area',
            3 => 'Device',
            4 => 'Optical Unit',
            5 => 'Server',
            6 => 'Other'
        );

        $data['managers'] = $this->users_model->getManagersList();
        
        $this->global['pageTitle'] = 'Network Segments : List';
        $this->loadViews("network/segmentList", $this->global, $data, NULL);
    }

    public function addNewSegment()
    {
        if($this->perm_areaaccess == 0 && $this->session->userdata('name') <> 'admin'){
            $this->session->set_flashdata('error', 'You are not allowed to create network segments(). Contact Admin to allow permission');
            redirect('segmentList');
        }

        $data['segmentTypes'] = array(
            0 => 'Region',
            1 => 'Zone',
            2 => 'Area',
            3 => 'Device',
            4 => 'Optical Unit',
            5 => 'Server',
            6 => 'Other'
        );
        
        $data['managers'] = $this->users_model->getManagersList();

        $data['parentSegments'] = $this->Network_model->getParentSegments();
        
        $this->global['pageTitle'] = 'Network Segments : Add New';
        $this->loadViews("network/addNewSegment", $this->global, $data, NULL);
    }

    function segmentExists($segmentname)
    {
        $segment = $this->Network_model->getSegmentByName($segmentname);
        if($segment) {
            $this->form_validation->set_message('segmentExists', 'Segment name already exists');
            return false;   
        }
        return true;
    }

    public function saveSegment()
    {
        /*if($this->perm_createservices == 0){
            $this->session->set_flashdata('error', 'You are not allowed to create network segments. Contact Admin to allow permission');
            redirect('segmentList');
        }*/

        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('segmentname', 'Segment Name', 'trim|required|max_length[255]|callback_segmentExists');
        $this->form_validation->set_rules('segmenttype', 'Segment Type', 'required|numeric');
        $this->form_validation->set_rules('managername', 'Manager', 'required');
        $this->form_validation->set_rules('activationdate', 'Activation Date', 'required');
        
        if($this->form_validation->run() == FALSE) {
            $this->addNewSegment();
        } else {
            $segmentInfo = array(
                'segmentname' => $this->security->xss_clean($this->input->post('segmentname')),
                'segmenttype' => $this->security->xss_clean($this->input->post('segmenttype')),
                'isgroup' => $this->security->xss_clean($this->input->post('isgroup')) ? 1 : 0,
                'mastersegmentid' => $this->security->xss_clean($this->input->post('mastersegmentid')),
                'activationdate' => $this->security->xss_clean($this->input->post('activationdate')),
                'managername' => $this->security->xss_clean($this->input->post('managername')),
                'details' => $this->security->xss_clean($this->input->post('details'))
            );

            $segmentDetails = $this->input->post('segmentdetails');
            
            $result = $this->Network_model->addNewSegment($segmentInfo, $segmentDetails);
            
            if($result > 0) {
                $this->session->set_flashdata('success', 'New Network Segment created successfully');
            } else {
                $this->session->set_flashdata('error', 'Network Segment creation failed');
            }
            
            redirect('Network_controller/segmentList');
        }
    }

    public function editSegment($segmentId = NULL)
    {
        /*if($this->perm_editservices == 0){
            $this->session->set_flashdata('error', 'You are not allowed to edit network segments. Contact Admin to allow permission');
            redirect('segmentList');
        }*/

        if($segmentId == null) {
            redirect('Network_controller/segmentList');
        }

        $data['segmentInfo'] = $this->Network_model->getSegmentInfo($segmentId);
        $data['segmentDetails'] = $this->Network_model->getSegmentDetails($segmentId);
        
        $data['segmentTypes'] = array(
            0 => 'Region',
            1 => 'Zone',
            2 => 'Area',
            3 => 'Device',
            4 => 'Optical Unit',
            5 => 'Server',
            6 => 'Other'
        );
        
        $data['managers'] = $this->users_model->getManagersList();
        $data['parentSegments'] = $this->Network_model->getParentSegments();

        if($this->session->userdata('name') <> 'admin' && $data['segmentInfo']->managername != $this->session->userdata('name') && $data['segmentInfo']->managername == 'default') {
            $this->session->set_flashdata('error', 'Network Segment not allowed to edit');
            redirect('Network_controller/segmentList');
        }
        
        $this->global['pageTitle'] = 'Network Segments : Edit';
        $this->loadViews("network/editSegment", $this->global, $data, NULL);
    }

    public function updateSegment()
    {
        /*if($this->perm_editservices == 0){
            $this->session->set_flashdata('error', 'You are not allowed to edit network segments. Contact Admin to allow permission');
            redirect('segmentList');
        }*/

        $this->load->library('form_validation');
        
        $segmentId = $this->input->post('segmentid');
        
        //$this->form_validation->set_rules('segmentname', 'Segment Name', 'trim|required|max_length[255]');
        $this->form_validation->set_rules('segmenttype', 'Segment Type', 'required|numeric');
        $this->form_validation->set_rules('managername', 'Manager', 'required');
        $this->form_validation->set_rules('activationdate', 'Activation Date', 'required');
        
        if($this->form_validation->run() == FALSE) {
            $this->editSegment($segmentId);
        } else {
            $segmentInfo = array(
                'segmentname' => $this->security->xss_clean($this->input->post('segmentname')),
                'segmenttype' => $this->security->xss_clean($this->input->post('segmenttype')),
                'isgroup' => $this->security->xss_clean($this->input->post('isgroup')) ? 1 : 0,
                'mastersegmentid' => $this->security->xss_clean($this->input->post('mastersegmentid')),
                'activationdate' => $this->security->xss_clean($this->input->post('activationdate')),
                'managername' => $this->security->xss_clean($this->input->post('managername')),
                'details' => $this->security->xss_clean($this->input->post('details'))
            );

            $segmentDetails = $this->input->post('segmentdetails');
            
            $result = $this->Network_model->updateSegment($segmentInfo, $segmentId, $segmentDetails);
            
            if($result == true) {
                $this->session->set_flashdata('success', 'Network Segment updated successfully');
            } else {
                $this->session->set_flashdata('error', 'Network Segment update failed');
            }
            
            redirect('Network_controller/segmentList');
        }
    }

    public function deleteSegment($segmentId = NULL)
    {
        if($this->perm_editservices == 0){
            echo json_encode(array('status' => 'access'));
        } else {
            $result = $this->Network_model->deleteSegment($segmentId);
            
            if($result > 0) {
                echo json_encode(array('status' => TRUE));
            } else {
                echo json_encode(array('status' => FALSE));
            }
        }
    }

    public function addDefault_Segment_pta(){
        $result = $this->Network_model->addDefault_Segment_pta();
        if($result > 0) {
            $this->session->set_flashdata('success', 'Default Network Segment created successfully');
        } else {
            $this->session->set_flashdata('error', 'Default Network Segment creation failed');
        }
        redirect('Network_controller/segmentList');
    }

    /**
     * Show ACS Device List from GenieACS API
     */
    public function acsDeviceList()
    {
        $segmentId = $this->input->post('segmentId');
        $devices = [];
        $api_error = '';
       
        // Get all server segments for dropdown
        $data['serverSegments'] = $this->Network_model->segmentList(5, 1);
        // Get mapped device names
        $data['mappedDeviceNames'] = $this->Network_model->getMappedDeviceNames();
        
        if ($segmentId) {
            $apiUrl = $this->Network_model->getAcsApiUrl($segmentId);
            
            if ($apiUrl) {
                // Fetch devices from GenieACS API
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $apiUrl . 'devices');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if (curl_errno($ch) || $http_code != 200) {
                    $api_error = 'Unable to fetch device data from ACS server.';
                } else {
                    $devices = json_decode($response, true);
                    if (!is_array($devices)) {
                        $api_error = 'Invalid response from ACS server.';
                        $devices = [];
                    }
                }
                curl_close($ch);
            } else {
                $api_error = 'API URL not found for selected server.';
            }
        }
        
        $data['devices'] = $devices;
        $data['api_error'] = $api_error;
        $data['selectedSegmentId'] = $segmentId;
        
        $this->global['pageTitle'] = 'ACS Device List';
        $this->loadViews('network/acsdevicelist', $this->global, $data, NULL);
    }

    /**
     * AJAX: Get active users for device mapping (autocomplete)
     */
    public function getActiveUsersForDeviceMapAjax() {
        $search = $this->input->get('q');
        $users = $this->Network_model->getActiveUsersForDeviceMap($search);
        $mappedUsernames = $this->Network_model->getMappedUsernames();
        $results = [];
        foreach ($users as $user) {
            if (in_array($user->username, $mappedUsernames)) continue;
            $text = $user->username;
            if (!empty($user->firstname) || !empty($user->lastname)) {
                $text .= ' (' . $user->firstname . ' ' . $user->lastname . ')';
            }
            $results[] = [
                'id' => $user->username,
                'text' => $text
            ];
        }
        echo json_encode(['results' => $results]);
        exit;
    }

    /**
     * AJAX: Map device to user
     */
    public function mapDeviceToUser() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('devicetype', 'Device Type', 'required');
        $this->form_validation->set_rules('devicename', 'Device Name', 'required');
        $this->form_validation->set_rules('username', 'Username', 'required');
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(['success' => false, 'message' => validation_errors()]);
            exit;
        }

        $user = $this->input->post('username');
        $userDetails = $this->users_model->getUserInfo($user);

        $data = [
            'devicetype' => $this->input->post('devicetype'),
            'devicename' => $this->input->post('devicename'),
            'username' => $user,
            'isactive' => 1,
            'masterdeviceid' => 0,
            'mappingdate' => date('Y-m-d'),
            'managername' => $userDetails->owner,
            'details' => $this->input->post('details')
        ];
        $insert_id = $this->Network_model->addNetworkDeviceMap($data);
        if ($insert_id) {
            echo json_encode(['success' => true, 'message' => 'Device mapped successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to map device.']);
        }
        exit;
    }

    /**
     * List mapped devices with search
     */
    public function mappedDeviceList() {
        $search = $this->input->get('search');
        $data['search'] = $search;
        $data['mappedDevices'] = $this->Network_model->getMappedDevices($search);
        $this->global['pageTitle'] = 'Mapped Devices List';
        $this->loadViews('network/mappedDeviceList', $this->global, $data, NULL);
    }

    /**
     * Edit mapped device form
     */
    public function editMappedDevice($deviceid) {
        $data['device'] = $this->Network_model->getMappedDeviceById($deviceid);
        $data['masterDevices'] = $this->Network_model->getAllDevices();
        $this->global['pageTitle'] = 'Edit Mapped Device';
        $this->loadViews('network/editMappedDevice', $this->global, $data, NULL);
    }

    /**
     * AJAX: Get user owner (manager) by username
     */
    public function getUserOwnerAjax() {
        $username = $this->input->get('username');
        $userDetails = $this->users_model->getUserInfo($username);
        $owner = $userDetails && isset($userDetails->owner) ? $userDetails->owner : '';
        echo json_encode(['owner' => $owner]);
        exit;
    }

    /**
     * Update mapped device (POST)
     */
    public function updateMappedDevice() {
        $deviceid = $this->input->post('deviceid');
        $username = $this->input->post('username');
        $userDetails = $this->users_model->getUserInfo($username);
        $managername = $userDetails && isset($userDetails->owner) ? $userDetails->owner : $this->input->post('managername');
        $data = [
            'devicetype' => $this->input->post('devicetype'),
            'devicename' => $this->input->post('devicename'),
            'username' => $username,
            'isactive' => $this->input->post('isactive'),
            'masterdeviceid' => $this->input->post('masterdeviceid'),
            'managername' => $managername,
            'details' => $this->input->post('details'),
            'para1' => $this->input->post('para1'),
            'para2' => $this->input->post('para2')
        ];
        $this->Network_model->updateMappedDevice($deviceid, $data);
        $this->session->set_flashdata('success', 'Mapped device updated successfully.');
        redirect('Network_controller/mappedDeviceList');
    }

    /**
     * Delete mapped device
     */
    public function deleteMappedDevice($deviceid) {
        $this->Network_model->deleteMappedDevice($deviceid);
        $this->session->set_flashdata('success', 'Mapped device deleted successfully.');
        redirect('Network_controller/mappedDeviceList');
    }

    /**
     * Refresh parameters for a specific device
     */
    public function refreshDeviceParameters($deviceid)
    {
        $device = $this->Network_model->getMappedDeviceById($deviceid);
        if ($device) {
            $response = $this->Network_model->refreshPonQualityMonitor($device->devicename);
            $this->session->set_flashdata('success', 'Device parameters refresh initiated. Please wait a few seconds before syncing values.');
        } else {
            $this->session->set_flashdata('error', 'Device not found.');
        }
        redirect('Network_controller/mappedDeviceList');
    }

    /**
     * Sync RX/TX values for all mapped devices from GenieACS
     */
    public function syncRxTxForMappedDevices()
    {
        $this->load->model('Network_model');
        $result = $this->Network_model->syncRxTxForMappedDevicesJob();
        $message = "RX/TX values synced for {$result['updated']} devices.";
        if ($result['inactivated'] > 0) {
            $message .= " {$result['inactivated']} devices marked as inactive due to missing values.";
        }
        if ($this->input->is_ajax_request()) {
            echo json_encode(['success' => true, 'message' => $message]);
            return;
        }
        $this->session->set_flashdata('success', $message);
        redirect('Network_controller/mappedDeviceList');
    }

    /**
     * AJAX: Get RADIUS status for a user (online/offline, start/stop time, terminate cause)
     */
    public function get_radius_status() {
        $username = $this->input->get('username');
        
        // Add debugging
        log_message('debug', 'Getting RADIUS status for username: ' . $username);
        
        $status = $this->Network_model->get_radius_status($username);
        
        // Log the result
        log_message('debug', 'RADIUS status result for ' . $username . ': ' . json_encode($status));
        
        echo json_encode($status);
    }

    /**
     * List all NAS devices
     */
    public function nasList() {
        $managername = $this->session->userdata('name');
        if ($managername !== 'admin') {
            redirect('Network_controller/nasListManager');
            return;
        }
        $this->load->model('Network_model');
        $nasList = $this->Network_model->getNasList();
        $data['nasList'] = $nasList;
        $this->global['pageTitle'] = 'NAS List';
        $this->loadViews('naslistview', $this->global, $data, NULL);
    }

    /**
     * Add new NAS (GET shows form, POST saves)
     */
    public function addNas() {
        $this->load->model('Network_model');
        $this->load->library('form_validation');
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nasname', 'NAS Name', 'required');
            $this->form_validation->set_rules('shortname', 'Short Name', 'required');
            $this->form_validation->set_rules('secret', 'Secret', 'required');
            if ($this->form_validation->run() === TRUE) {
                $data = $this->input->post(NULL, TRUE);
                $this->Network_model->addNas($data);
                $this->session->set_flashdata('success', 'NAS added successfully.');
                redirect('Network_controller/nasList');
            }
        }
        $this->global['pageTitle'] = 'Add NAS';
        $this->loadViews('nasaddedit', $this->global, NULL, NULL);
    }

    /**
     * Edit NAS (GET shows form, POST saves)
     */
    public function editNas($id) {

        $managername = $this->session->userdata('name');
        if ($managername !== 'admin') {
            redirect('Network_controller/nasListManager');
            return;
        }

        $this->load->model('Network_model');
        $this->load->library('form_validation');
        $nas = $this->Network_model->getNasById($id);
        if (!$nas) {
            $this->session->set_flashdata('error', 'NAS not found.');
            redirect('Network_controller/nasList');
        }
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('nasname', 'NAS Name', 'required');
            $this->form_validation->set_rules('shortname', 'Short Name', 'required');
            $this->form_validation->set_rules('secret', 'Secret', 'required');
            if ($this->form_validation->run() === TRUE) {
                $data = $this->input->post(NULL, TRUE);
                $this->Network_model->updateNas($id, $data);
                $this->session->set_flashdata('success', 'NAS updated successfully.');
                redirect('Network_controller/nasList');
            }
        }
        $data['nas'] = $nas;
        $this->global['pageTitle'] = 'Edit NAS';
        $this->loadViews('nasaddedit', $this->global, $data, NULL);
    }

    /**
     * Delete NAS
     */
    public function deleteNas($id) {
        $this->load->model('Network_model');
        $this->Network_model->deleteNas($id);
        $this->session->set_flashdata('success', 'NAS deleted successfully.');
        redirect('Network_controller/nasList');
    }

    /**
     * AJAX: Get online/offline user counts for a NAS
     */
    public function getNasUserCountsAjax() {
        $nasip = $this->input->get('nasip');
        $counts = $this->Network_model->getNasUserCounts($nasip);
        echo json_encode($counts);
    }

    /**
     * AJAX: Get grouped online/offline user counts for a NAS by segment and parameter
     */
    public function getNasSegmentUserStatusAjax() {
        $nasip = $this->input->get('nasip');
        $this->load->model('Network_model');
        $this->db->from('temp_nas_userstatus_cache');
        $this->db->where('nasip', $nasip);
        $this->db->where('segment_id IS NOT NULL', null, false);
        $this->db->order_by('segment_id, parameter');
        $query = $this->db->get();
        $rows = $query->result();
        if (!$rows) {
            echo json_encode([]);
            return;
        }
        // Group by segment_id, then parameter
        $segments = [];
        foreach ($rows as $row) {
            if (!isset($segments[$row->segment_id])) {
                $segments[$row->segment_id] = [
                    'segmentid' => $row->segment_id,
                    'segment_name' => $row->segment_id, // Optionally replace with name lookup
                    'parameters' => []
                ];
            }
            $segments[$row->segment_id]['parameters'][] = [
                'parameter' => $row->parameter,
                'online' => (int)$row->online,
                'offline' => (int)$row->offline
            ];
        }
        // Optionally fetch segment names
        $segment_ids = array_keys($segments);
        if (!empty($segment_ids)) {
            $this->db->select('segmentid, segmentname');
            $this->db->from('tbl_networksegment');
            $this->db->where_in('segmentid', $segment_ids);
            $segQuery = $this->db->get();
            $segNames = [];
            foreach ($segQuery->result() as $seg) {
                $segNames[$seg->segmentid] = $seg->segmentname;
            }
            foreach ($segments as &$seg) {
                if (isset($segNames[$seg['segmentid']])) {
                    $seg['segment_name'] = $segNames[$seg['segmentid']];
                }
            }
        }
        echo json_encode(array_values($segments));
    }

    /**
     * AJAX: Get online/offline/total user count for a NAS (latest session per user, not expired)
     */
    public function getNasOnlineOfflineUserCountAjax() {
        $nasip = $this->input->get('nasip');
        $cache = $this->Network_model->getNasUserStatusCache($nasip);
        if ($cache) {
            echo json_encode([
                'online' => (int)$cache->online,
                'offline' => (int)$cache->offline,
                'total' => (int)$cache->total,
                'last_updated' => $cache->last_updated
            ]);
        } else {
            echo json_encode([
                'online' => 0,
                'offline' => 0,
                'total' => 0,
                'last_updated' => null
            ]);
        }
    }

    /**
     * AJAX: Refresh the NAS/segment/parameter user status cache (recalculate and upsert all)
     */
    public function refreshNasUserStatusCacheAjax() {
        $this->Network_model->refreshAllUserStatusCache();
        echo json_encode([
            'success' => true,
            'updated' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * List NAS/segments for managers (only their own segments)
     */
    public function nasListManager() {
        $managername = $this->session->userdata('name');
        if ($managername === 'admin') {
            redirect('Network_controller/nasList');
            return;
        }
        $this->load->model('Network_model');
        // Get all segments for this manager
        $segments = $this->Network_model->getSegmentsByManager($managername);
        // For each segment, get its parameters
        foreach ($segments as &$segment) {
            $segment->parameters = $this->Network_model->getSegmentDetails($segment->segmentid);
            foreach ($segment->parameters as &$param) {
                $status = $this->Network_model->getParameterUserStatusCache($segment->segmentid, $param->parameter);
                $param->online = $status ? $status->online : 0;
                $param->offline = $status ? $status->offline : 0;
            }
        }
        $data['segmentList'] = $segments;
        $data['managername'] = $managername;
        $this->global['pageTitle'] = 'NAS List (Manager)';
        $this->loadViews('naslistview_manager', $this->global, $data, NULL);
    }

    /**
     * Refresh PonQualityMonitor for all mapped devices
     */
    public function refreshAllPonQuality()
    {
        $this->load->model('Network_model');
        $results = $this->Network_model->refreshPonQualityMonitorForAllDevices();
        $successCount = is_array($results) ? count($results) : 0;
        $this->session->set_flashdata('success', "PON Quality Monitor refresh initiated for $successCount devices. Please wait a few seconds before syncing values.");
        redirect('Network_controller/mappedDeviceList');
    }

    /**
     * User Dashboard - Show online/offline user statistics
     */
    public function userDashboard() {
        if($this->isAdmin() == FALSE && $this->isManager() == FALSE) {
            $this->loadThis();
        } else {
            $managername = $this->session->userdata('name');
            
            // Check if cache is fresh, otherwise use live data

            // Use cached data for faster loading
            $data['overall_stats'] = $this->Network_model->getOverallDashboardStatsFromCache($managername);
            $data['owner_stats'] = $this->Network_model->getUserDashboardStatsFromCache($managername);
            $data['nas_stats'] = $this->Network_model->getOnlineUsersByNasFromCache($managername);
            $data['recent_activity'] = $this->Network_model->getRecentActivityFromCache($managername);
            $data['cache_status'] = 'cached';
            
            $this->global['pageTitle'] = 'User Dashboard : Online/Offline Status';
            $this->loadViews("network/userDashboard", $this->global, $data, NULL);
        }
    }

    /**
     * Disconnect a single expired user
     */
    public function disconnectExpiredUser($username) {
        if($this->isAdmin() == FALSE && $this->isManager() == FALSE) {
            echo json_encode(array('status' => 'error', 'message' => 'Access denied'));
            return;
        }
        
        $result = $this->Network_model->disconnectUser($username);
        echo json_encode($result);
    }

    /**
     * Disconnect all expired users automatically across all managers and NAS devices
     */
    public function disconnectAllExpiredUsers($nasip = null) {
        if($this->isAdmin() == FALSE && $this->isManager() == FALSE) {
            echo json_encode(array('status' => 'error', 'message' => 'Access denied'));
            return;
        }
        
        // Use the automatic function that processes all expired users
        $result = $this->Network_model->disconnectAllExpiredUsersAutomatically();
        echo json_encode($result);
    }

    /**
     * Display expired online users automatically across all managers and NAS devices
     */
    public function expiredOnlineUsers($nasip = null) {
        if($this->isAdmin() == FALSE && $this->isManager() == FALSE) {
            $this->loadThis();
            return;
        }
        
        // Get all expired online users automatically
        $data['expired_users'] = $this->Network_model->getAllExpiredOnlineUsersAutomatically();
        $data['nasip'] = $nasip; // Keep for backward compatibility
        $data['managername'] = $this->session->userdata('name');
        
        $this->global['pageTitle'] = 'Expired Online Users';
        $this->loadViews("network/expiredOnlineUsers", $this->global, $data, NULL);
    }

    /**
     * Search user details from radacct table
     */
    public function searchUserDetails($page = 0) {
        if($this->isAdmin() == FALSE && $this->isManager() == FALSE) {
            $this->loadThis();
            return;
        }
        
        $this->load->library('pagination');
        
        // Get search filters from POST or session
        $filters = array(
            'record_type' => $this->input->post('record_type') ? $this->input->post('record_type') : '0',
            'username' => $this->input->post('username'),
            'owner' => $this->input->post('owner'),
            'expiration_from' => $this->input->post('expiration_from'),
            'expiration_to' => $this->input->post('expiration_to'),
            'address' => $this->input->post('address'),
            'mobile' => $this->input->post('mobile'),
            'taxid' => $this->input->post('taxid')
        );

        // If no POST data, try to get from session (for pagination)
        if ($filters['record_type'] == '0' && empty($filters['username']) && empty($filters['owner']) && 
            empty($filters['expiration_from']) && empty($filters['expiration_to']) && 
            empty($filters['address']) && empty($filters['mobile']) && empty($filters['taxid'])) {
            $filters = $this->session->userdata('search_filters');
        } else {
            // Store filters in session for pagination
            $this->session->set_userdata('search_filters', $filters);
            // Reset page to 0 when new search is performed
            $page = 0;
        }

        // Check if any filter is set
        $hasFilters = false;
        if ($filters) {
            foreach ($filters as $filter) {
                if (!empty($filter)) {
                    $hasFilters = true;
                    break;
                }
            }
        }
        
        // Only perform search if filters are provided
        if ($hasFilters) {
            // Get count for pagination
            $count = $this->Network_model->searchUserDetailsFromRadacctCount($filters);
            $returns = $this->paginationCompress("searchUserDetails/", $count, 20);
            
            // Use the page parameter if provided
            if ($page > 0) {
                $returns["page"] = $page;
                $returns["segment"] = $page;
            }
            
            // Get search results
            $data['searchResults'] = $this->Network_model->searchUserDetailsFromRadacct($filters, $returns["page"], $returns["segment"]);
            $data['pagination'] = $this->pagination->create_links();
        } else {
            // No filters provided, set empty results
            $data['searchResults'] = array();
            $data['pagination'] = '';
        }
        
        $data['filters'] = $filters;
        $data['managers'] = $this->Network_model->getAllManagers();
        
        $this->global['pageTitle'] = 'Search User Details';
        $this->loadViews("network/searchUserDetails", $this->global, $data, NULL);
    }

    /**
     * Clear search filters and redirect to search page
     */
    public function clearSearch() {
        // Clear search filters from session
        $this->session->unset_userdata('search_filters');
        redirect('searchUserDetails');
    }

    /**
     * AJAX: Search usernames for autocomplete
     */
    public function searchUsernamesAjax() {
        $search = $this->input->get('q');
        $users = $this->Network_model->searchUsernames($search);
        
        $results = array();
        foreach ($users as $user) {
            $text = $user->username;
            if (!empty($user->firstname) || !empty($user->lastname)) {
                $text .= ' (' . $user->firstname . ' ' . $user->lastname . ')';
            }
            $results[] = array(
                'id' => $user->username,
                'text' => $text
            );
        }
        
        echo json_encode(array('results' => $results));
        exit;
    }

} 
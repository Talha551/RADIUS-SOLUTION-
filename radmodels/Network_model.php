<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Network_model extends CI_Model
{
    function segmentListingCount($searchText = '', $segmentType = null)
    {
        $this->db->select('BaseTbl.segmentid, BaseTbl.segmentname, BaseTbl.segmenttype, BaseTbl.isgroup, BaseTbl.activationdate, BaseTbl.managername');
        $this->db->from('tbl_networksegment as BaseTbl');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.segmentname LIKE '%".$searchText."%'
                            OR BaseTbl.managername LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        if($this->session->userdata('name') != 'admin') {
            $this->db->where('BaseTbl.managername', $this->session->userdata('name'));
        }
        
        if($segmentType !== null && $segmentType !== '') {
            $this->db->where('BaseTbl.segmenttype', $segmentType);
        }
        
        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function segmentListing($searchText = '', $page, $segment, $segmentType = null)
    {
        $this->db->select('BaseTbl.segmentid, BaseTbl.segmentname, BaseTbl.segmenttype, BaseTbl.isgroup, BaseTbl.activationdate, BaseTbl.managername');
        $this->db->from('tbl_networksegment as BaseTbl');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.segmentname LIKE '%".$searchText."%'
                            OR BaseTbl.managername LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        if($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('BaseTbl.managername', $manager_chain);
        }elseif($this->session->userdata('name') != 'admin') {
            $this->db->where('BaseTbl.managername', $this->session->userdata('name'));
        }

        if($segmentType !== null && $segmentType !== '') {
            $this->db->where('BaseTbl.segmenttype', $segmentType);
        }

        $this->db->order_by('BaseTbl.segmentid', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();        
        return $result;
    }
    function addNewSegment($segmentInfo, $segmentDetails)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_networksegment', $segmentInfo);
        $segmentId = $this->db->insert_id();
        
        if(!empty($segmentDetails)) {
            foreach($segmentDetails as $detail) {
                $detail['segmentid'] = $segmentId;
                $this->db->insert('tbl_networksegmentdetails', $detail);
            }
        }
        
        $this->db->trans_complete();
        
        return $segmentId;
    }

    function getSegmentInfo($segmentId)
    {
        $this->db->select('segmentid, segmentname, segmenttype, isgroup, mastersegmentid, activationdate, managername, details');
        $this->db->from('tbl_networksegment');
        $this->db->where('segmentid', $segmentId);
        $query = $this->db->get();
        
        return $query->row();
    }

    function getSegmentDetails($segmentId)
    {
        $this->db->select('*');
        $this->db->from('tbl_networksegmentdetails');
        $this->db->where('segmentid', $segmentId);
        $query = $this->db->get();
        
        return $query->result();
    }

    function updateSegment($segmentInfo, $segmentId, $segmentDetails)
    {
        $this->db->trans_start();
        $this->db->where('segmentid', $segmentId);
        $this->db->update('tbl_networksegment', $segmentInfo);
        
        // Delete existing details
        $this->db->where('segmentid', $segmentId);
        $this->db->delete('tbl_networksegmentdetails');
        
        // Insert new details
        if(!empty($segmentDetails)) {
            foreach($segmentDetails as $detail) {
                $detail['segmentid'] = $segmentId;
                $this->db->insert('tbl_networksegmentdetails', $detail);
            }
        }
        
        $this->db->trans_complete();
        
        return true;
    }

    function deleteSegment($segmentId)
    {
        $this->db->trans_start();
        
        // Delete details first
        $this->db->where('segmentid', $segmentId);
        $this->db->delete('tbl_networksegmentdetails');
        
        // Delete segment
        $this->db->where('segmentid', $segmentId);
        $this->db->delete('tbl_networksegment');
        
        $this->db->trans_complete();
        
        return true;
    }

    function getParentSegments()
    {
        $this->db->select('segmentid, segmentname');
        $this->db->from('tbl_networksegment');
        $this->db->where('isgroup', 1);
        $this->db->where('managername', $this->session->userdata('name'));
        $this->db->or_where('managername', 'default');
        $query = $this->db->get();
        
        return $query->result();
    }


    function segmentList($segmenttype = 0, $isgroup = 0, $managername = null)
    {
        $this->db->select('*');
        $this->db->from('tbl_networksegment as BaseTbl');
        $this->db->where('segmenttype', $segmenttype);
        $this->db->where('isgroup', $isgroup);

        if($managername != null){
            $this->db->where('managername', $managername);
        }

        $this->db->order_by('BaseTbl.segmentname');

        $query = $this->db->get();
        
        $result = $query->result();        
        return $result;
    }

    function segmentListdetails($segmentid = null)
    {
        $this->db->select('*');
        $this->db->from('tbl_networksegmentdetails as BaseTbl');
        $this->db->where('segmentid', $segmentid);
        $this->db->order_by('BaseTbl.parameter');

        $query = $this->db->get();
        
        $result = $query->result();        
        return $result;
    }

    function addDefault_Segment_pta(){
        $regions = [
            [
                "region_name" => "Main-Region",
                "cities_districts" => ["Main-Sub-Region"]
            ],
            [
                "region_name" => "Central Telecom Region (CTR)",
                "cities_districts" => ["Kasur", "Nankana", "Okara", "Pakpattan", "Sahiwal", "Sheikhupura"]
            ],
            [
                "region_name" => "Northern Telecom Region-I (NTR-I)",
                "cities_districts" => ["Buner", "Charsadda", "Chitral", "Lower Dir", "Malakand", "Mardan", "Nowshera", "Shangla", "Swabi", "Swat", "Upper Dir"]
            ],
            [
                "region_name" => "Southern Telecom Region - I (STR-I)",
                "cities_districts" => ["Badin", "Dadu", "Hyderabad", "Jamshoro", "Mirpur Khas", "Sanghar", "Tharparkar", "Thatta", "Umerkot"]
            ],
            [
                "region_name" => "Faisalabad Telecom Region (FTR)",
                "cities_districts" => ["Faisalabad", "Jhang", "Sargodha", "Toba Tek Singh", "Khushab", "Bhakkar", "Mianwali"]
            ],
            [
                "region_name" => "Multan Telecom Region (MTR)",
                "cities_districts" => ["Bahawalnagar", "Bahawalpur", "D.G. Khan", "Khanewal", "Layyah", "Lodhran", "Multan", "Muzaffargarh", "Rahim Yar Khan", "Rajanpur", "Vehari"]
            ],
            [
                "region_name" => "Gujranwala Telecom Region (GTR)",
                "cities_districts" => ["Gujranwala", "Gujrat", "Hafizabad", "Mandi Bahauddin", "Narowal", "Sialkot"]
            ],
            [
                "region_name" => "Hazara Telecom Region (HTR)",
                "cities_districts" => ["Abbottabad", "Batagram", "Haripur", "Kohistan", "Mansehra"]
            ],
            [
                "region_name" => "Southern Telecom Region V-I (STR V-I)",
                "cities_districts" => ["Ghotki", "Jacobabad", "Kambar Shahdadkot", "Kashmore", "Khairpur", "Larkana", "Naushahro Feroze", "Nawabshah", "Shikarpur", "Sukkur"]
            ],
            [
                "region_name" => "Rawalpindi Telecom Region (RTR)",
                "cities_districts" => ["Attock", "Chakwal", "Jhelum", "Rawalpindi"]
            ],
            [
                "region_name" => "Northern Telecom Region-II-A (NTR-II-A)",
                "cities_districts" => ["Bannu", "D.I. Khan", "Hangu", "Karak", "Kohat", "Lakki Marwat", "Tank"]
            ],
            [
                "region_name" => "Northern Telecom Region-I-B (NTR-I-B)",
                "cities_districts" => ["Buner", "Lower Dir", "Malakand", "Swat", "Shangla", "Upper Dir"]
            ],
            [
                "region_name" => "Western Telecom Region-I-A (WTR-I-A)",
                "cities_districts" => ["Awaran", "Barkhan", "Bolan", "Chagai", "Gawadar", "Jhal Magsi", "Kalat", "Kech", "Khuzdar", "Killa Abdullah", "Lasbela", "Mastung", "Nasirabad", "Pishin", "Quetta"]
            ],
            [
                "region_name" => "Islamabad Telecom Region (ITR)",
                "cities_districts" => ["Islamabad"]
            ],
            [
                "region_name" => "Karachi Telecom Region (KTR)",
                "cities_districts" => ["Karachi"]
            ],
            [
                "region_name" => "Lahore Telecom Region (LTR)",
                "cities_districts" => ["Lahore"]
            ],
            [
                "region_name" => "Muzaffarabad Zonal Office",
                "cities_districts" => ["Muzaffarabad and surrounding areas in AJK"]
            ],
            [
                "region_name" => "Gilgit Zonal Office",
                "cities_districts" => ["Gilgit and surrounding areas in Gilgit-Baltistan"]
            ]
        ];

        $this->db->trans_start();
        $current_date = date('Y-m-d');
        foreach ($regions as $region) {
            // Insert region as group (parent)
            $regionInfo = array(
                'segmentname' => $region['region_name'],
                'segmenttype' => 0,
                'managername' => 'default',
                'isgroup' => 1,
                'activationdate' => $current_date,
                'details' => 'Pakistan Telecom Regions',
                'mastersegmentid' => null
            );
            $this->db->insert('tbl_networksegment', $regionInfo);
            $region_segmentid = $this->db->insert_id();

            // Insert each city/district as group (child of region)
            if (!empty($region['cities_districts'])) {
                foreach ($region['cities_districts'] as $city) {
                    $cityInfo = array(
                        'segmentname' => $city,
                        'segmenttype' => 0,
                        'managername' => 'default',
                        'isgroup' => 1,
                        'activationdate' => $current_date,
                        'details' => 'Pakistan Telecom Regions',
                        'mastersegmentid' => $region_segmentid
                    );
                    $this->db->insert('tbl_networksegment', $cityInfo);
                    $city_segmentid = $this->db->insert_id();

                    // Insert into tbl_networksegmentdetails
                    $detail = array(
                        'segmentid' => $city_segmentid,
                        'parameter' => $city,
                        'parametertype' => 0
                    );
                    $this->db->insert('tbl_networksegmentdetails', $detail);
                }
            }
        }
        $this->db->trans_complete();
        return true;
    }

    function getSegmentByName($segmentname)
    {
        $this->db->select('*');
        $this->db->from('tbl_networksegment');
        $this->db->where('segmentname', $segmentname);
        $query = $this->db->get();  
        return $query->row();
    }

    function getAcsApiUrl($segmentId)
    {
        $this->db->select('B.paravalue1');
        $this->db->from('tbl_networksegment as A');
        $this->db->join('tbl_networksegmentdetails as B', 'A.segmentid = B.segmentid', 'left');
        $this->db->where('A.segmentid', $segmentId);
        $this->db->where('B.parametertype', 9); // API type
        $query = $this->db->get();
        $result = $query->row();
        return $result ? $result->paravalue1 : null;
    }
    
    /**
     * Get active users for device mapping (enableuser=1, acctype=0)
     */
    function getActiveUsersForDeviceMap($search = '') {
        $this->db->select('username, firstname, lastname');
        $this->db->from('rm_users');
        $this->db->where('enableuser', 1);
        $this->db->where('acctype', 0);
        if (!empty($search)) {
            $this->db->like('username', $search);
            $this->db->or_like('firstname', $search);
            $this->db->or_like('lastname', $search);
        }
        $this->db->limit(20); // Limit for autocomplete
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Add a new device mapping to tbl_networkdevicemap
     */
    function addNetworkDeviceMap($data) {
        $this->db->insert('tbl_networkdevicemap', $data);
        return $this->db->insert_id();
    }

    /**
     * Get all mapped device names from tbl_networkdevicemap
     */
    function getMappedDeviceNames() {
        $this->db->select('devicename');
        $this->db->from('tbl_networkdevicemap');
        $query = $this->db->get();
        $result = $query->result();
        return array_map(function($row) { return $row->devicename; }, $result);
    }

    /**
     * Get all mapped usernames from tbl_networkdevicemap
     */
    function getMappedUsernames() {
        $this->db->select('username');
        $this->db->from('tbl_networkdevicemap');
        $query = $this->db->get();
        $result = $query->result();
        return array_map(function($row) { return $row->username; }, $result);
    }

    /**
     * Get mapped devices with optional search (by devicename, username, or details)
     */
    function getMappedDevices($search = '') {
        $this->db->select("deviceid, devicetype, devicename, username, isactive, masterdeviceid, managername, details, para1, para2, lastInform,
            (CASE 
                WHEN lastInform IS NULL THEN 1
                WHEN TIMESTAMPDIFF(MINUTE, lastInform, NOW()) > 15 THEN 1
                ELSE 0
            END) AS is_stale");
        $this->db->from('tbl_networkdevicemap');
        if (!empty($search)) {
            $this->db->like('devicename', $search);
            $this->db->or_like('username', $search);
            $this->db->or_like('details', $search);
        }

        if($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('managername', $manager_chain);
        }elseif($this->session->userdata('name') <> 'admin'){
            $this->db->where('managername', $this->session->userdata('name')); 
        }

        $this->db->order_by('is_stale', 'DESC');
        $this->db->order_by('lastInform', 'ASC'); // Oldest first at the top
        $query = $this->db->get();
        $result = $query->result();
        // Map devicetype and isactive to labels, para1 as RX, para2 as TX
        foreach ($result as $row) {
            $row->devicetype_label = ($row->devicetype == 0) ? 'ONT' : (($row->devicetype == 1) ? 'OLT' : 'Unknown');
            $row->isactive_label = ($row->isactive == 1) ? 'Active' : 'Down';
            $row->rx = $row->para1;
            $row->tx = $row->para2;
            if ($row->masterdeviceid && $row->masterdeviceid != 0) {
                $row->masterdeviceid_label = $this->getDeviceNameById($row->masterdeviceid);
            } else {
                $row->masterdeviceid_label = 'N/A';
            }
        }
        return $result;
    }
    /**
     * Get a single mapped device by deviceid
     */
    function getMappedDeviceById($deviceid) {
        $this->db->select('deviceid, devicetype, devicename, username, isactive, masterdeviceid, managername, details, para1, para2');
        $this->db->from('tbl_networkdevicemap');
        $this->db->where('deviceid', $deviceid);
        $query = $this->db->get();
        return $query->row();
    }
    /**
     * Update a mapped device
     */
    function updateMappedDevice($deviceid, $data) {
        $this->db->where('deviceid', $deviceid);
        return $this->db->update('tbl_networkdevicemap', $data);
    }
    /**
     * Delete a mapped device
     */
    function deleteMappedDevice($deviceid) {
        $this->db->where('deviceid', $deviceid);
        return $this->db->delete('tbl_networkdevicemap');
    }
    /**
     * Get devicename by deviceid (for masterdeviceid label)
     */
    function getDeviceNameById($deviceid) {
        $this->db->select('devicename');
        $this->db->from('tbl_networkdevicemap');
        $this->db->where('deviceid', $deviceid);
        $query = $this->db->get();
        $row = $query->row();
        return $row ? $row->devicename : 'N/A';
    }

    /**
     * Get all devices (deviceid and devicename) for master device selection
     */
    function getAllDevices() {
        $this->db->select('deviceid, devicename');
        $this->db->from('tbl_networkdevicemap');
        $this->db->order_by('devicename', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Fetch RX/TX values from GenieACS API for a device
     */
    public function fetchRxTxFromGenieAcs($deviceId)
    {
        $baseUrl = "http://103.102.159.72:7557";
        $modelType = $this->extractModelType($deviceId);
        $rxProjection = null;
        $txProjection = null;

        // Get segment IDs for the model type
        $this->db->select('segmentid');
        $this->db->from('tbl_networksegmentdetails');
        $this->db->where('paravalue1', $modelType);
        $segmentIdQuery = $this->db->get();
        $segmentIds = array_column($segmentIdQuery->result_array(), 'segmentid');

        if (empty($segmentIds)) {
            // Fallback to Generic
            $this->db->select('segmentid');
            $this->db->from('tbl_networksegmentdetails');
            $this->db->where('paravalue1', 'Generic');
            $segmentIdQuery = $this->db->get();
            $segmentIds = array_column($segmentIdQuery->result_array(), 'segmentid');
        }

        $results = [];
        if (!empty($segmentIds)) {
            $this->db->select('B.parameter, B.paravalue2');
            $this->db->from('tbl_networksegment as A');
            $this->db->join('tbl_networksegmentdetails as B', 'A.segmentid = B.segmentid', 'left');
            $this->db->where('A.segmenttype', 3);
            $this->db->where('B.parametertype', 16);
            $this->db->where('B.paravalue1', 'projection');
            $this->db->where_in('A.segmentid', $segmentIds);
            $query = $this->db->get();
            $results = $query->result();
        }

        foreach ($results as $row) {
            if ($row->parameter === 'RXPower') {
                $rxProjection = $row->paravalue2;
            }
            if ($row->parameter === 'TXPower') {
                $txProjection = $row->paravalue2;
            }
        }

        if (!$rxProjection || !$txProjection) {
            // Fallback to hardcoded if not found
            $rxProjection = 'InternetGatewayDevice.WANDevice.1.X_GponInterafceConfig.RXPower';
            $txProjection = 'InternetGatewayDevice.WANDevice.1.X_GponInterafceConfig.TXPower';
        }

        // First get the lastInform date
        $query = urlencode(json_encode(["_id" => $deviceId]));
        $lastInformUrl = "$baseUrl/devices/?query=$query&projection=_lastInform";
        $ch = curl_init($lastInformUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $lastInformResponse = curl_exec($ch);
        curl_close($ch);
        $lastInformData = json_decode($lastInformResponse, true);
        $lastInform = isset($lastInformData[0]['_lastInform']) ? $lastInformData[0]['_lastInform'] : null;

        // Then get RX/TX values
        $rxUrl = "$baseUrl/devices/?query=$query&projection=$rxProjection";
        $txUrl = "$baseUrl/devices/?query=$query&projection=$txProjection";

        $rx = $this->fetchGenieAcsValue($rxUrl, 'RX');
        $tx = $this->fetchGenieAcsValue($txUrl, 'TX');

        return [
            'rx' => $rx, 
            'tx' => $tx,
            'lastInform' => $lastInform
        ];
    }

    private function extractModelType($deviceId)
    {
        // Example: 00259E-EG8147X6-48575443B85C62A7 => EG8147X6
        $parts = explode('-', $deviceId);
        return isset($parts[1]) ? $parts[1] : 'Generic';
    }

    private function fetchGenieAcsValue($url, $type = 'RX')
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);

        if (!isset($data[0]['InternetGatewayDevice']['WANDevice']['1']['X_GponInterafceConfig'])) {
            return null;
        }
        $config = $data[0]['InternetGatewayDevice']['WANDevice']['1']['X_GponInterafceConfig'];
        if ($type === 'RX' && isset($config['RXPower']['_value'])) {
            return $config['RXPower']['_value'];
        }
        if ($type === 'TX' && isset($config['TXPower']['_value'])) {
            return $config['TXPower']['_value'];
        }
        return null;
    }

    /**
     * Trigger a refresh of PonQualityMonitor for a device in GenieACS
     */
    public function refreshPonQualityMonitor($deviceId)
    {
        // Ensure deviceId is a string
        if (is_array($deviceId)) {
            $deviceId = isset($deviceId['devicename']) ? $deviceId['devicename'] : reset($deviceId);
        }
        // Determine base URL based on context
        if (isset($_SERVER['HTTP_HOST'])) {
            $baseUrl = "http://103.102.159.72:7557"; // original, or you can use a mapping if needed
        } elseif (getenv('APP_HOSTNAME')) {
            // Use environment variable for CLI
            $baseUrl = 'http://' . getenv('APP_HOSTNAME') . ':7557';
        } else {
            $baseUrl = "http://103.102.159.72:7557";
        }
        // First, enable the PON Quality Monitor
        $enableUrl = "$baseUrl/devices/$deviceId/tasks?connection_request";
        $enableData = [
            "name" => "setParameterValues",
            "parameterValues" => [
                ["InternetGatewayDevice.X_HW_PonQualityMonitor.Enable", true]
            ]
        ];
        $ch = curl_init($enableUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($enableData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $enableResponse = curl_exec($ch);
        curl_close($ch);
        // Then refresh the WANDevice object to get updated RX/TX values
        $wanRefreshUrl = "$baseUrl/devices/$deviceId/tasks?connection_request";
        $wanRefreshData = [
            "name" => "refreshObject",
            "objectName" => "InternetGatewayDevice.WANDevice"
        ];
        $ch = curl_init($wanRefreshUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($wanRefreshData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $wanRefreshResponse = curl_exec($ch);
        curl_close($ch);
        // Finally refresh the PON Quality Monitor
        $refreshUrl = "$baseUrl/devices/$deviceId/tasks?connection_request";
        $refreshData = [
            "name" => "refreshObject",
            "objectName" => "InternetGatewayDevice.X_HW_PonQualityMonitor"
        ];
        $ch = curl_init($refreshUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($refreshData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $refreshResponse = curl_exec($ch);
        curl_close($ch);
        // Update lastInform date in database if refresh was successful
        if ($enableResponse && $wanRefreshResponse && $refreshResponse) {
            $this->db->where('devicename', $deviceId);
            $this->db->update('tbl_networkdevicemap', ['lastInform' => date('Y-m-d')]);
        }
        return [
            'enable_response' => $enableResponse,
            'wan_refresh_response' => $wanRefreshResponse,
            'refresh_response' => $refreshResponse
        ];
    }

    /**
     * Sync RX/TX values for all mapped devices from GenieACS
     */
    public function syncRxTxForMappedDevicesJob()
    {
        $devices = $this->getMappedDevices();
        $updated = 0;
        $inactivated = 0;
        foreach ($devices as $device) {
            $rxTx = $this->fetchRxTxFromGenieAcs($device->devicename);
            if (!empty($rxTx['rx']) || !empty($rxTx['tx'])) {
                $this->updateMappedDevice($device->deviceid, [
                    'para1' => $rxTx['rx'],
                    'para2' => $rxTx['tx'],
                    'isactive' => 1,
                    'lastInform' => $rxTx['lastInform'] ? date('Y-m-d H:i:s', strtotime($rxTx['lastInform'])) : null
                ]);
                $updated++;
            } else {
                $this->updateMappedDevice($device->deviceid, [
                    'para1' => 0,
                    'para2' => 0,
                    'isactive' => 0,
                    'lastInform' => null
                ]);
                $inactivated++;
            }
        }
        return ['updated' => $updated, 'inactivated' => $inactivated];
    }

    /**
     * Get RADIUS status for a user (online/offline, start/stop time, terminate cause)
     */
    public function get_radius_status($username) {
        
        // First, check if user has an active session (acctstoptime IS NULL)
        $active_session = $this->db->where('username', $username)
                                  ->where('acctstoptime IS NULL', null, false)
                                  ->order_by('radacctid', 'DESC')
                                  ->get('radacct', 1)
                                  ->row();
        
        if ($active_session) {
            // User is online - return the active session details
            return [
                'online' => true,
                'acctstarttime' => $active_session->acctstarttime,
                'acctstoptime' => $active_session->acctstoptime,
                'acctterminatecause' => $active_session->acctterminatecause
            ];
        }
        
        // User is offline - get the most recent session (which should have acctstoptime set)
        $latest_session = $this->db->where('username', $username)
                                  ->order_by('radacctid', 'DESC')
                                  ->get('radacct', 1)
                                  ->row();
        
        if (!$latest_session) {
            return [
                'online' => false,
                'acctstarttime' => null,
                'acctstoptime' => null,
                'acctterminatecause' => null
            ];
        }
        
        return [
            'online' => false,
            'acctstarttime' => $latest_session->acctstarttime,
            'acctstoptime' => $latest_session->acctstoptime,
            'acctterminatecause' => $latest_session->acctterminatecause
        ];
    }

    /**
     * Get all NAS records with user count (unique users from radacct)
     */
    public function getNasList() {
        $this->db->select('n.*, (
            SELECT COUNT(DISTINCT username) FROM radacct WHERE nasipaddress = n.nasname
        ) as user_count');
        $this->db->from('nas as n');
        $this->db->order_by('n.id', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    /**
     * Get a single NAS by id
     */
    public function getNasById($id) {
        return $this->db->get_where('nas', ['id' => $id])->row();
    }
    /**
     * Add a new NAS
     */
    public function addNas($data) {
        $this->db->trans_start();

        $this->db->insert('nas', $data);
        $nasId = $this->db->insert_id();

        if ($nasId) {
            $existingCount = $this->db->where('nasid', $nasId)->count_all_results('rm_allowednases');

            if ($existingCount === 0) {
                $services = $this->db->select('srvid')->from('rm_services')->get()->result();

                if (!empty($services)) {
                    $rowsToInsert = array();
                    foreach ($services as $service) {
                        $rowsToInsert[] = array(
                            'srvid' => (int) $service->srvid,
                            'nasid' => (int) $nasId,
                        );
                    }
                    if (!empty($rowsToInsert)) {
                        $this->db->insert_batch('rm_allowednases', $rowsToInsert);
                    }
                }
            }
        }

        $this->db->trans_complete();
        return $nasId;
    }
    /**
     * Update NAS by id
     */
    public function updateNas($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('nas', $data);
    }
    /**
     * Delete NAS by id
     */
    public function deleteNas($id) {
        $this->db->where('id', $id);
        return $this->db->delete('nas');
    }

    /**
     * Get online/offline user counts for a NAS by nasipaddress
     */
    public function getNasUserCounts($nasip) {

        $managername = $this->session->userdata('name');

        $online = $this->db->where('nasipaddress', $nasip)
            ->where('acctstoptime IS NULL', null, false)
            ->count_all_results('radacct');
        $offline = $this->db->where('nasipaddress', $nasip)
            ->where('acctstoptime IS NOT NULL', null, false)
            ->count_all_results('radacct');
        return ['online' => $online, 'offline' => $offline];
    }

    /**
     * Get grouped online/offline user counts for a NAS by segment and parameter
     */
    public function getNasSegmentUserStatus($nasip) {
        // Get latest radacct record for each user for this NAS
        $subquery = $this->db->select('MAX(radacctid) as maxid')
            ->from('radacct')
            ->where('nasipaddress', $nasip)
            ->group_by('username')
            ->get_compiled_select();
        $this->db->select('r.username, udocs.segmentid, udocs.parameter, r.acctstoptime');
        $this->db->from('radacct r');
        $this->db->join('tbl_userdocs udocs', 'r.username = udocs.username', 'inner');
        $this->db->join('rm_users u', 'r.username = u.username', 'inner');
        $this->db->where_in('r.radacctid', $subquery, false);
        $this->db->where('u.expiration >=', date('Y-m-d'));
        $query = $this->db->get();
        $rows = $query->result();
        // Group by segmentid and parameter
        $result = [];
        foreach ($rows as $row) {
            $segid = $row->segmentid;
            $param = $row->parameter;
            if (!isset($result[$segid])) {
                $result[$segid] = [];
            }
            if (!isset($result[$segid][$param])) {
                $result[$segid][$param] = ['online'=>0, 'offline'=>0];
            }
            if ($row->acctstoptime === null) {
                $result[$segid][$param]['online']++;
            } else {
                $result[$segid][$param]['offline']++;
            }
        }
        // Get segment names
        $segmentNames = [];
        if (!empty($result)) {
            $this->db->select('segmentid, segmentname');
            $this->db->from('tbl_networksegment');
            $this->db->where_in('segmentid', array_keys($result));
            $segQuery = $this->db->get();
            foreach ($segQuery->result() as $seg) {
                $segmentNames[$seg->segmentid] = $seg->segmentname;
            }
        }
        // Format output
        $output = [];
        foreach ($result as $segid => $params) {
            $segName = isset($segmentNames[$segid]) ? $segmentNames[$segid] : $segid;
            $paramArr = [];
            foreach ($params as $param => $counts) {
                $paramArr[] = [
                    'parameter' => $param,
                    'online' => $counts['online'],
                    'offline' => $counts['offline']
                ];
            }
            $output[] = [
                'segmentid' => $segid,
                'segment_name' => $segName,
                'parameters' => $paramArr
            ];
        }
        return $output;
    }

    /**
     * Get unique user count for a NAS (by nasipaddress), only latest session per user
     */
    public function getNasUniqueUserCount($nasip) {
        $subquery = $this->db->select('MAX(radacctid) as maxid')
            ->from('radacct')
            ->where('nasipaddress', $nasip)
            ->group_by('username')
            ->get_compiled_select();
        $this->db->select('COUNT(*) as cnt');
        $this->db->from('radacct r');
        $this->db->join('rm_users u', 'r.username = u.username', 'inner');
        $this->db->where_in('r.radacctid', $subquery, false);
        $this->db->where('u.expiration >=', date('Y-m-d'));
        $row = $this->db->get()->row();
        return $row ? (int)$row->cnt : 0;
    }

    /**
     * Get online/offline user count for a NAS (by nasipaddress), only latest session per user
     */
    public function getNasOnlineOfflineUserCount($nasip) {
        $subquery = $this->db->select('MAX(radacctid) as maxid')
            ->from('radacct')
            ->where('nasipaddress', $nasip)
            ->group_by('username')
            ->get_compiled_select();
        $this->db->select('r.acctstoptime');
        $this->db->from('radacct r');
        $this->db->join('rm_users u', 'r.username = u.username', 'inner');
        $this->db->where_in('r.radacctid', $subquery, false);
        $this->db->where('u.expiration >=', date('Y-m-d'));

        $query = $this->db->get();
        $online = 0;
        $offline = 0;
        foreach ($query->result() as $row) {
            if ($row->acctstoptime === null) {
                $online++;
            } else {
                $offline++;
            }
        }
        return ['online' => $online, 'offline' => $offline];
    }

    public function getNasUserStatusCache($nasip) {
        return $this->db->get_where('temp_nas_userstatus_cache', [
            'nasip' => $nasip,
            'segment_id' => null,
            'parameter' => null
        ])->row();
    }

    public function getSegmentUserStatusCache($nasip, $segment_id) {
        return $this->db->get_where('temp_nas_userstatus_cache', [
            'nasip' => $nasip,
            'segment_id' => $segment_id,
            'parameter' => null
        ])->result();
    }

    public function getParameterUserStatusCache($segment_id, $parameter) {
        $this->db->select('online, offline');
        $this->db->from('temp_nas_userstatus_cache');
        $this->db->where('segment_id', $segment_id);
        $this->db->where('parameter', $parameter);
        $query = $this->db->get();
        return $query->row();
    }

    // Upsert logic for cache (insert or update)
    public function upsertUserStatusCache($data) {
        $exists = $this->db->get_where('temp_nas_userstatus_cache', [
            'nasip' => $data['nasip'],
            'segment_id' => $data['segment_id'],
            'parameter' => $data['parameter']
        ])->row();
        if ($exists) {
            $this->db->where('id', $exists->id)->update('temp_nas_userstatus_cache', $data);
        } else {
            $this->db->insert('temp_nas_userstatus_cache', $data);
        }
    }

    // Recalculate and upsert all NAS/segment/parameter user status (to be called on-demand or by cron)
    public function refreshAllUserStatusCache() {
        // Clear the cache table
        $this->db->truncate('temp_nas_userstatus_cache');
        // Get all NAS devices
        $nasList = $this->getNasList();
        foreach ($nasList as $nas) {
            $nasip = $nas->nasname;
            // --- NAS-level ---
            $status = $this->getNasOnlineOfflineUserCount($nasip);
            $total = $this->getNasUniqueUserCount($nasip);
            $this->upsertUserStatusCache([
                'nasip' => $nasip,
                'segment_id' => null,
                'parameter' => null,
                'online' => $status['online'],
                'offline' => $status['offline'],
                'total' => $total,
                'last_updated' => date('Y-m-d H:i:s')
            ]);
            // --- Segment/Parameter-level ---
            $segments = $this->getNasSegmentUserStatus($nasip);
            foreach ($segments as $seg) {
                $segment_id = $seg['segmentid'];
                foreach ($seg['parameters'] as $param) {
                    $this->upsertUserStatusCache([
                        'nasip' => $nasip,
                        'segment_id' => $segment_id,
                        'parameter' => $param['parameter'],
                        'online' => $param['online'],
                        'offline' => $param['offline'],
                        'total' => $param['online'] + $param['offline'],
                        'last_updated' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
    }

    // Get all segments for a specific manager
    public function getSegmentsByManager($managername) {
        $this->db->select('*');
        $this->db->from('tbl_networksegment');
        $this->db->where('managername', $managername);
        $this->db->order_by('segmentid', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Refresh PonQualityMonitor for all mapped devices
     */
    public function refreshPonQualityMonitorForAllDevices()
    {
        // Fetch all devicenames from tbl_networkdevicemap
        $this->db->select('devicename');
        $this->db->from('tbl_networkdevicemap');
        $query = $this->db->get();
        $devices = $query->result();

        $results = [];
        foreach ($devices as $device) {
            // Call the existing function for each devicename
            $results[$device->devicename] = $this->refreshPonQualityMonitor($device->devicename);
        }
        return $results;
    }

    /**
     * Get user statistics for dashboard - grouped by owner with online/offline status
     */
    public function getUserDashboardStats($managername = null) {
        $current_date = date('Y-m-d');
        
        // Build the WHERE clause for manager filtering
        $where_clause = "u.acctype = 0";
        if ($managername && $managername !== 'admin') {
            $where_clause .= " AND u.owner = '" . $this->db->escape_str($managername) . "'";
        }
        
        // Use raw SQL to avoid CASE statement escaping issues
        $sql = "
            SELECT 
                u.owner,
                u.username,
                u.expiration,
                CASE 
                    WHEN u.expiration > '{$current_date}' THEN 1 
                    ELSE 0 
                END as is_active,
                CASE 
                    WHEN r.acctstoptime IS NULL THEN 1 
                    ELSE 0 
                END as is_online
            FROM rm_users u
            LEFT JOIN radacct r ON u.username = r.username
            WHERE {$where_clause}
            AND (r.radacctid = (SELECT MAX(radacctid) FROM radacct WHERE username = u.username) OR r.radacctid IS NULL)
        ";
        
        $query = $this->db->query($sql);
        $users = $query->result();
        
        // Group by owner and calculate statistics
        $stats = array();
        foreach ($users as $user) {
            if (!isset($stats[$user->owner])) {
                $stats[$user->owner] = array(
                    'owner' => $user->owner,
                    'active_online' => 0,
                    'active_offline' => 0,
                    'expired_online' => 0,
                    'expired_offline' => 0,
                    'total_active' => 0,
                    'total_expired' => 0,
                    'total_online' => 0,
                    'total_offline' => 0
                );
            }
            
            if ($user->is_active) {
                $stats[$user->owner]['total_active']++;
                if ($user->is_online) {
                    $stats[$user->owner]['active_online']++;
                    $stats[$user->owner]['total_online']++;
                } else {
                    $stats[$user->owner]['active_offline']++;
                    $stats[$user->owner]['total_offline']++;
                }
            } else {
                $stats[$user->owner]['total_expired']++;
                if ($user->is_online) {
                    $stats[$user->owner]['expired_online']++;
                    $stats[$user->owner]['total_online']++;
                } else {
                    $stats[$user->owner]['expired_offline']++;
                    $stats[$user->owner]['total_offline']++;
                }
            }
        }
        
        return array_values($stats);
    }
    
    /**
     * Get online users grouped by NAS with shortname
     */
    public function getOnlineUsersByNas($managername = null) {
        $current_date = date('Y-m-d');
        
        // Build the WHERE clause for manager filtering
        $where_clause = "r.acctstoptime IS NULL AND u.acctype = 0";
        if ($managername && $managername !== 'admin') {
            $where_clause .= " AND u.owner = '" . $this->db->escape_str($managername) . "'";
        }
        
        // Use raw SQL to avoid CASE statement escaping issues
        $sql = "
            SELECT 
                n.shortname,
                n.nasname,
                COUNT(DISTINCT r.username) as online_users,
                COUNT(DISTINCT CASE WHEN u.expiration > '{$current_date}' THEN r.username END) as active_online_users,
                COUNT(DISTINCT CASE WHEN u.expiration <= '{$current_date}' THEN r.username END) as expired_online_users
            FROM radacct r
            INNER JOIN rm_users u ON r.username = u.username
            LEFT JOIN nas n ON r.nasipaddress = n.nasname
            WHERE {$where_clause}
            GROUP BY n.shortname, n.nasname
            ORDER BY online_users DESC
        ";
        
        $query = $this->db->query($sql);
        return $query->result();
    }
    
    /**
     * Get overall dashboard statistics
     */
    public function getOverallDashboardStats($managername = null) {
        $current_date = date('Y-m-d');
        
        // Build the WHERE clause for manager filtering
        $where_clause = "u.acctype = 0";
        if ($managername && $managername !== 'admin') {
            $where_clause .= " AND u.owner = '" . $this->db->escape_str($managername) . "'";
        }
        
        // Use raw SQL to avoid CASE statement escaping issues
        $sql = "
            SELECT 
                COUNT(DISTINCT u.username) as total_users,
                COUNT(DISTINCT CASE WHEN u.expiration > '{$current_date}' THEN u.username END) as total_active_users,
                COUNT(DISTINCT CASE WHEN u.expiration <= '{$current_date}' THEN u.username END) as total_expired_users,
                COUNT(DISTINCT CASE WHEN r.acctstoptime IS NULL THEN r.username END) as total_online_users,
                COUNT(DISTINCT CASE WHEN r.acctstoptime IS NOT NULL THEN r.username END) as total_offline_users,
                COUNT(DISTINCT CASE WHEN u.expiration > '{$current_date}' AND r.acctstoptime IS NULL THEN r.username END) as active_online_users,
                COUNT(DISTINCT CASE WHEN u.expiration > '{$current_date}' AND r.acctstoptime IS NOT NULL THEN r.username END) as active_offline_users,
                COUNT(DISTINCT CASE WHEN u.expiration <= '{$current_date}' AND r.acctstoptime IS NULL THEN r.username END) as expired_online_users,
                COUNT(DISTINCT CASE WHEN u.expiration <= '{$current_date}' AND r.acctstoptime IS NOT NULL THEN r.username END) as expired_offline_users
            FROM rm_users u
            LEFT JOIN radacct r ON u.username = r.username
            WHERE {$where_clause}
            AND (r.radacctid = (SELECT MAX(radacctid) FROM radacct WHERE username = u.username) OR r.radacctid IS NULL)
        ";
        
        $query = $this->db->query($sql);
        return $query->row();
    }
    
    /**
     * Get recent online/offline activity (last 24 hours)
     */
    public function getRecentActivity($managername = null) {
        $last_3_hours = date('Y-m-d H:i:s', strtotime('-5 hours'));
        
        // Build the WHERE clause for manager filtering
        $where_clause = "u.acctype = 0";
        if ($managername && $managername !== 'admin') {
            $where_clause .= " AND u.owner = '" . $this->db->escape_str($managername) . "'";
        }
        
        // Use raw SQL to get distinct records for users who stopped in last 3 hours but are not currently online
        $sql = "
            SELECT 
                r.username,
                u.firstname,
                u.lastname,
                u.owner,
                r.acctstarttime,
                r.acctstoptime,
                r.acctterminatecause,
                r.nasipaddress,
                n.shortname as nas_shortname,
                'Offline' as status
            FROM radacct r
            INNER JOIN rm_users u ON r.username = u.username
            LEFT JOIN nas n ON r.nasipaddress = n.nasname
            WHERE {$where_clause}
            AND r.acctstoptime IS NOT NULL
            AND r.acctstoptime >= '{$last_3_hours}'
            AND r.radacctid = (
                SELECT MAX(radacctid) 
                FROM radacct r2 
                WHERE r2.username = r.username
            )
            AND NOT EXISTS (
                SELECT 1 
                FROM radacct r3 
                WHERE r3.username = r.username 
                AND r3.acctstoptime IS NULL
            )
            ORDER BY r.acctstoptime DESC
            LIMIT 50
        ";
        
        $query = $this->db->query($sql);
        return $query->result();
    }

    /**
     * Get overall dashboard statistics from temporary table
     */
    public function getOverallDashboardStatsFromCache($managername = null) {
        $this->db->select('*');
        $this->db->from('temp_dashboard_overall');

        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }
    
    /**
     * Get user dashboard statistics from temporary table
     */
    public function getUserDashboardStatsFromCache($managername = null) {
        $this->db->select('*');
        $this->db->from('temp_dashboard_owner');
        if ($managername && $managername !== 'admin') {
            $this->db->where('owner', $managername);
        }
        $this->db->order_by('total_active', 'DESC');
        $query = $this->db->get();
        return $query->result_array();
    }
    
    /**
     * Get online users by NAS from temporary table
     */
    public function getOnlineUsersByNasFromCache($managername = null) {
        $this->db->select('*');
        $this->db->from('temp_dashboard_nas');
        $this->db->where('online_users >', 0);
        $this->db->order_by('online_users', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Get recent activity from temporary table
     */
    public function getRecentActivityFromCache($managername = null) {
        $this->db->select('*');
        $this->db->from('temp_dashboard_activity');
        if ($managername && $managername !== 'admin') {
            $this->db->where('owner', $managername);
        }
        $this->db->order_by('acctstarttime', 'DESC');
        $this->db->limit(50);
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Check if cache exists and is fresh (less than 5 minutes old)
     */
    public function isCacheFresh() {
        $this->db->select('created_at');
        $this->db->from('temp_dashboard_overall');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();
        $result = $query->row();
        
        if (!$result) {
            return false;
        }
        
        $cache_time = strtotime($result->created_at);
        $current_time = time();
        $time_diff = $current_time - $cache_time;
        
        // Cache is fresh if less than 5 minutes old
        return $time_diff < 172800;
    }

    /**
     * Get expired online users for a specific NAS
     */
    public function getExpiredOnlineUsers($nasip = null, $managername = null) {
        $current_date = date('Y-m-d');
        
        $this->db->select('r.username, r.framedipaddress, r.nasipaddress, r.acctstarttime, r.acctstoptime, 
                          u.firstname, u.lastname, u.owner, u.expiration, n.shortname as nas_shortname');
        $this->db->from('radacct r');
        $this->db->join('rm_users u', 'r.username = u.username', 'inner');
        $this->db->join('nas n', 'r.nasipaddress = n.nasname', 'left');
        $this->db->where('r.acctstoptime IS NULL'); // Currently online
        $this->db->where('u.expiration <', $current_date); // Expired users
        $this->db->where('u.acctype', 0); // Regular users only
        
        if ($nasip) {
            $this->db->where('r.nasipaddress', $nasip);
        }
        
        if ($managername && $managername !== 'admin') {
            $this->db->where('u.owner', $managername);
        }
        
        $this->db->order_by('u.expiration', 'ASC');
        $query = $this->db->get();
        
        return $query->result();
    }

    /**
     * Get expired online users count for a specific NAS
     */
    public function getExpiredOnlineUsersCount($nasip = null, $managername = null) {
        $current_date = date('Y-m-d');
        
        $this->db->select('COUNT(*) as count');
        $this->db->from('radacct r');
        $this->db->join('rm_users u', 'r.username = u.username', 'inner');
        $this->db->where('r.acctstoptime IS NULL'); // Currently online
        $this->db->where('u.expiration <', $current_date); // Expired users
        $this->db->where('u.acctype', 0); // Regular users only
        
        if ($nasip) {
            $this->db->where('r.nasipaddress', $nasip);
        }
        
        if ($managername && $managername !== 'admin') {
            $this->db->where('u.owner', $managername);
        }
        
        $query = $this->db->get();
        $result = $query->row();
        
        return $result ? $result->count : 0;
    }

    /**
     * Disconnect a single user
     * @param string $username Username to disconnect
     * @return array Status and message
     */
    public function disconnectUser($username) {
        // Get user connection details
        $this->db->select('r.username, r.framedipaddress, r.nasipaddress, r.acctstarttime, r.acctstoptime, 
                          n.secret');
        $this->db->from('radacct r');
        $this->db->join('nas n', 'r.nasipaddress = n.nasname', 'left');
        $this->db->where('r.username', $username);
        $this->db->where('r.acctstoptime IS NULL'); // Currently online
        
        $query = $this->db->get();
        $connectedUser = $query->row();
        
        if (!$connectedUser) {
            return array('status' => 'error', 'message' => 'User not found or not online');
        }
        
        // Execute disconnect command
        $command = "echo User-Name=".$connectedUser->username.",Framed-IP-Address=".$connectedUser->framedipaddress.
                    " | radclient -r 1 ".$connectedUser->nasipaddress.":3799 disconnect ".$connectedUser->secret;
        
        exec($command);
        
        // Update radacct table to mark user as disconnected
        $this->db->query("UPDATE radacct SET acctstoptime = NOW() WHERE username = ? AND acctstoptime IS NULL", array($username));
        
        return array('status' => 'success', 'message' => 'User disconnected successfully');
    }

    /**
     * Disconnect all expired users for a specific NAS
     * @param string|null $nasip NAS IP address (optional)
     * @param string|null $managername Manager name (optional)
     * @return array Status, message, and counts
     */
    public function disconnectAllExpiredUsers($nasip = null, $managername = null) {
        $current_date = date('Y-m-d');
        
        // Get expired online users
        $this->db->select('r.username, r.framedipaddress, r.nasipaddress, r.acctstarttime, r.acctstoptime, 
                          u.firstname, u.lastname, u.owner, u.expiration, n.shortname as nas_shortname, n.secret');
        $this->db->from('radacct r');
        $this->db->join('rm_users u', 'r.username = u.username', 'inner');
        $this->db->join('nas n', 'r.nasipaddress = n.nasname', 'left');
        $this->db->where('r.acctstoptime IS NULL'); // Currently online
        $this->db->where('u.expiration <', $current_date); // Expired users
        $this->db->where('u.acctype', 0); // Regular users only
        
        if ($nasip) {
            $this->db->where('r.nasipaddress', $nasip);
        }
        
        if ($managername && $managername !== 'admin') {
            $this->db->where('u.owner', $managername);
        }
        
        $this->db->order_by('u.expiration', 'ASC');
        $query = $this->db->get();
        $expired_users = $query->result();
        
        $disconnected_count = 0;
        $failed_count = 0;
        
        foreach ($expired_users as $user) {
            // Execute disconnect command
            $command = "echo User-Name=".$user->username.",Framed-IP-Address=".$user->framedipaddress.
                        " | radclient -r 1 ".$user->nasipaddress.":3799 disconnect ".$user->secret;
            
            exec($command);
            
            // Update radacct table to mark user as disconnected
            $this->db->query("UPDATE radacct SET acctstoptime = NOW() WHERE username = ? AND acctstoptime IS NULL", array($user->username));
            
            $disconnected_count++;
        }
        
        return array(
            'status' => 'success', 
            'message' => "Disconnected: $disconnected_count, Failed: $failed_count",
            'disconnected' => $disconnected_count,
            'failed' => $failed_count,
            'total_processed' => count($expired_users)
        );
    }

    /**
     * Get user connection details for disconnection
     * @param string $username Username to get details for
     * @return object|null User connection details
     */
    public function getUserConnectionDetails($username) {
        $this->db->select('r.username, r.framedipaddress, r.nasipaddress, r.acctstarttime, r.acctstoptime, 
                          n.secret');
        $this->db->from('radacct r');
        $this->db->join('nas n', 'r.nasipaddress = n.nasname', 'left');
        $this->db->where('r.username', $username);
        $this->db->where('r.acctstoptime IS NULL'); // Currently online
        
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Update user account status after disconnection
     * @param string $username Username to update
     * @return bool Success status
     */
    public function updateUserAccountAfterDisconnect($username) {
        $this->db->query("UPDATE radacct SET acctstoptime = NOW() WHERE username = ? AND acctstoptime IS NULL", array($username));
        return $this->db->affected_rows() > 0;
    }

    /**
     * Automatically disconnect all expired users who are currently online
     * This function processes ALL expired users across ALL managers and NAS devices
     * @return array Status, message, and counts
     */
    public function disconnectAllExpiredUsersAutomatically() {
        $current_date = date('Y-m-d');
        
        // Get all expired users who are currently online
        $this->db->select('r.username, r.framedipaddress, r.nasipaddress, r.acctstarttime, r.acctstoptime, 
                          u.firstname, u.lastname, u.owner, u.expiration, n.shortname as nas_shortname, n.secret');
        $this->db->from('rm_users u');
        $this->db->join('radacct r', 'u.username = r.username', 'inner');
        $this->db->join('nas n', 'r.nasipaddress = n.nasname', 'left');
        $this->db->where('u.expiration <', $current_date); // Expired users
        $this->db->where('u.acctype', 0); // Regular users only
        $this->db->where('r.acctstoptime IS NULL'); // Currently online
        $this->db->order_by('u.expiration', 'ASC');
        
        $query = $this->db->get();
        $expired_online_users = $query->result();
        
        $disconnected_count = 0;
        $failed_count = 0;
        
        foreach ($expired_online_users as $user) {
            try {
                // Execute disconnect command using NAS IP and user IP from radacct
                $command = "echo User-Name=".$user->username.",Framed-IP-Address=".$user->framedipaddress.
                            " | radclient -r 1 ".$user->nasipaddress.":3799 disconnect ".$user->secret;
                
                exec($command);
                
                // Update radacct table to mark user as disconnected
                $this->db->query("UPDATE radacct SET acctstoptime = NOW() WHERE username = ? AND acctstoptime IS NULL", array($user->username));
                
                $disconnected_count++;
                
                // Log the disconnection
                log_message('info', 'Disconnected expired user: ' . $user->username . ' from NAS: ' . $user->nasipaddress);
                
            } catch (Exception $e) {
                $failed_count++;
                log_message('error', 'Failed to disconnect user: ' . $user->username . ' - Error: ' . $e->getMessage());
            }
        }
        
        return array(
            'status' => 'success', 
            'message' => "Disconnected: $disconnected_count, Failed: $failed_count",
            'disconnected' => $disconnected_count,
            'failed' => $failed_count,
            'total_processed' => count($expired_online_users),
            'total_expired_online' => count($expired_online_users)
        );
    }

    /**
     * Get all expired online users automatically across all managers and NAS devices
     * @return array List of expired online users
     */
    public function getAllExpiredOnlineUsersAutomatically() {
        $current_date = date('Y-m-d');
        
        // Get all expired users who are currently online
        $this->db->select('r.username, r.framedipaddress, r.nasipaddress, r.acctstarttime, r.acctstoptime, 
                          u.firstname, u.lastname, u.owner, u.expiration, n.shortname as nas_shortname');
        $this->db->from('rm_users u');
        $this->db->join('radacct r', 'u.username = r.username', 'inner');
        $this->db->join('nas n', 'r.nasipaddress = n.nasname', 'left');
        $this->db->where('u.expiration <', $current_date); // Expired users
        $this->db->where('u.acctype', 0); // Regular users only
        $this->db->where('r.acctstoptime IS NULL'); // Currently online
        $this->db->order_by('u.expiration', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get count of all expired online users automatically across all managers and NAS devices
     * @return int Count of expired online users
     */
    public function getAllExpiredOnlineUsersCountAutomatically() {
        $current_date = date('Y-m-d');
        
        $this->db->select('COUNT(*) as count');
        $this->db->from('rm_users u');
        $this->db->join('radacct r', 'u.username = r.username', 'inner');
        $this->db->where('u.expiration <', $current_date); // Expired users
        $this->db->where('u.acctype', 0); // Regular users only
        $this->db->where('r.acctstoptime IS NULL'); // Currently online
        
        $query = $this->db->get();
        $result = $query->row();
        
        return $result ? $result->count : 0;
    }

    /**
     * Search users for autocomplete in username field
     * @param string $search Search term
     * @return array Array of usernames
     */
    public function searchUsernames($search) {
        $managername = $this->session->userdata('name');
        $this->db->select('username, firstname, lastname');
        $this->db->from('rm_users');
        $this->db->like('username', $search);
        /*$this->db->like('username', $search);
        $this->db->or_like('firstname', $search);
        $this->db->or_like('lastname', $search);*/

        if($managername <> "admin" && $this->ismaster == 0){
            $this->db->where('owner = ', $managername);
        }
        elseif($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            if (!empty($manager_chain)) {
                $this->db->where_in('owner', $manager_chain);
            }    
        }

        $this->db->limit(20);
        $this->db->order_by('username', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get all managers for owner dropdown
     * @return array Array of managers
     */
    public function getAllManagers() {

        $managername = $this->session->userdata('name');
        $this->db->select('managername, firstname, lastname');
        $this->db->from('rm_managers');
        $this->db->where('managername !=', 'admin');

        if($managername <> "admin" && $this->ismaster == 0){
            $this->db->where('managername = ', $managername);
        }
        elseif($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            if (!empty($manager_chain)) {
                $this->db->where_in('managername', $manager_chain);
            }    
        }

        $this->db->order_by('managername', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Search user details from radacct table based on filters
     * @param array $filters Search filters
     * @param int $page Page number for pagination
     * @param int $segment Segment size for pagination
     * @return array Array of user details
     */
    public function searchUserDetailsFromRadacct($filters = array(), $page = 0, $segment = 10) {
        $this->db->select('r.*, u.firstname, u.lastname, u.owner, u.expiration, u.address, u.mobile, u.taxid, n.shortname as nas_shortname');
        $this->db->from('radacct r');
        $this->db->join('rm_users u', 'r.username = u.username', 'inner');
        $this->db->join('nas n', 'r.nasipaddress = n.nasname', 'left');
        
        // Apply filters
        // Handle record type filter
        if (isset($filters['record_type'])) {
            switch($filters['record_type']) {
                case '1': // Offline
                    $this->db->where('r.acctstoptime IS NOT NULL');
                    break;
                case '2': // Online
                    $this->db->where('r.acctstoptime IS NULL');
                    break;
                case '3': // Distinct All - Latest record per user regardless of status
                    // Use a more efficient approach - group by and order by radacctid DESC
                    $this->db->group_by('r.username');
                    $this->db->order_by('r.radacctid', 'DESC');
                    break;
                case '4': // Distinct Offline - Latest offline record per user
                    $this->db->where('r.acctstoptime IS NOT NULL');
                    $this->db->group_by('r.username');
                    $this->db->order_by('r.radacctid', 'DESC');
                    break;
                case '5': // Distinct Online - Latest online record per user
                    $this->db->where('r.acctstoptime IS NULL');
                    $this->db->group_by('r.username');
                    $this->db->order_by('r.radacctid', 'DESC');
                    break;
                case '0': // All Records (default behavior)
                default:
                    break;
            }
        }

        if (!empty($filters['username'])) {
            $this->db->like('r.username', $filters['username']);
        }
        
        if (!empty($filters['owner'])) {
            $this->db->where('u.owner', $filters['owner']);
        }
        
        if (!empty($filters['expiration_from'])) {
            $this->db->where('u.expiration >=', $filters['expiration_from']);
        }
        
        if (!empty($filters['expiration_to'])) {
            $this->db->where('u.expiration <=', $filters['expiration_to']);
        }
        
        if (!empty($filters['address'])) {
            $this->db->like('u.address', $filters['address']);
        }
        
        if (!empty($filters['mobile'])) {
            $this->db->like('u.mobile', $filters['mobile']);
        }
        
        if (!empty($filters['taxid'])) {
            $this->db->like('u.taxid', $filters['taxid']);
        }
        
        // Apply manager restrictions
        $managername = $this->session->userdata('name');
        if ($managername !== 'admin') {
            if ($this->ismaster > 0) {
                $manager_chain = $this->session->userdata('manager_chain');
                $this->db->where_in('u.owner', $manager_chain);
            } else {
                $this->db->where('u.owner', $managername);
            }
        }
        
        // Order by username ASC and radacctid DESC
        $this->db->order_by('r.username', 'ASC');
        $this->db->order_by('r.radacctid', 'DESC');
        
        // Apply pagination
        if ($page > 0) {
            $this->db->limit($page, $segment);
        } else {
            $this->db->limit($segment);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get count of search results for pagination
     * @param array $filters Search filters
     * @return int Count of results
     */
    public function searchUserDetailsFromRadacctCount($filters = array()) {
        $this->db->select('COUNT(*) as count');
        $this->db->from('radacct r');
        $this->db->join('rm_users u', 'r.username = u.username', 'inner');
        
        // Apply filters
        // Handle record type filter
        if (isset($filters['record_type'])) {
            switch($filters['record_type']) {
                case '1': // Offline
                    $this->db->where('r.acctstoptime IS NOT NULL');
                    break;
                case '2': // Online
                    $this->db->where('r.acctstoptime IS NULL');
                    break;
                case '3': // Distinct All - Count distinct users
                    $this->db->select('COUNT(DISTINCT r.username) as count');
                    break;
                case '4': // Distinct Offline - Count distinct offline users
                    $this->db->select('COUNT(DISTINCT r.username) as count');
                    $this->db->where('r.acctstoptime IS NOT NULL');
                    break;
                case '5': // Distinct Online - Count distinct online users
                    $this->db->select('COUNT(DISTINCT r.username) as count');
                    $this->db->where('r.acctstoptime IS NULL');
                    break;
                case '0': // All Records (default behavior)
                default:
                    break;
            }
        }

        if (!empty($filters['username'])) {
            $this->db->like('r.username', $filters['username']);
        }
        
        if (!empty($filters['owner'])) {
            $this->db->where('u.owner', $filters['owner']);
        }
        
        if (!empty($filters['expiration_from'])) {
            $this->db->where('u.expiration >=', $filters['expiration_from']);
        }
        
        if (!empty($filters['expiration_to'])) {
            $this->db->where('u.expiration <=', $filters['expiration_to']);
        }
        
        if (!empty($filters['address'])) {
            $this->db->like('u.address', $filters['address']);
        }
        
        if (!empty($filters['mobile'])) {
            $this->db->like('u.mobile', $filters['mobile']);
        }
        
        if (!empty($filters['taxid'])) {
            $this->db->like('u.taxid', $filters['taxid']);
        }
        
        // Apply manager restrictions
        $managername = $this->session->userdata('name');
        if ($managername !== 'admin') {
            if ($this->ismaster > 0) {
                $manager_chain = $this->session->userdata('manager_chain');
                $this->db->where_in('u.owner', $manager_chain);
            } else {
                $this->db->where('u.owner', $managername);
            }
        }
        
        $query = $this->db->get();
        $result = $query->row();
        return $result ? $result->count : 0;
    }
} 
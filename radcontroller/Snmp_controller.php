<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

class Snmp_controller extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('snmp_model');
        $this->load->helper('form');
        $this->isLoggedIn();
    }

    public function index()
    {
        $this->global['pageTitle'] = 'SNMP : User Search';
        $data = [];
        $this->loadViews('snmp/user_search', $this->global, $data, NULL);
    }

    public function search_user_snmp()
    {
        $username = $this->input->post('username');
        $result = $this->snmp_model->getUserDetailsBySnmp($username);
        $this->global['pageTitle'] = 'SNMP : User Details';
        $data = ['username' => $username, 'snmp_result' => $result];
        $this->loadViews('snmp/user_search', $this->global, $data, NULL);
    }

    public function get_traffic_mbps()
    {
        $index = $this->input->post('ifIndex');
        $result = $this->snmp_model->getInterfaceTrafficMbps($index);
        echo json_encode($result);
    }

    /**
     * Show live traffic for a user by username (Mbps)
     */
    public function get_usertraffic_mbps($username)
    {
        $result = $this->snmp_model->get_usertraffic_mbps($username);
        $this->global['pageTitle'] = 'SNMP : Live User Traffic';
        $data = ['username' => $username, 'traffic_result' => $result];
        $this->loadViews('snmp/snmplivetraffic', $this->global, $data, NULL);
    }

    /**
     * AJAX endpoint: Return current SNMP counters for a user (for JS polling)
     */
    public function get_user_counters() {
        $username = $this->input->get('username');
        $row = $this->snmp_model->get_user_snmp_counters($username);
        if (!$row) {
            echo json_encode(['error' => 'User not found']);
            return;
        }
        echo json_encode([
            'download_bytes' => $row['download_bytes'],
            'upload_bytes' => $row['upload_bytes'],
            'timestamp' => time(),
            'device_type' => isset($row['device_type']) ? $row['device_type'] : null,
            'username' => $username
        ]);
    }

    /**
     * AJAX: Check if monitoring is available for a username (Mikrotik only)
     */
    public function ajax_check_monitoring() {
        $username = $this->input->get('username');
        $row = $this->snmp_model->get_snmpcache_for_user($username);
        if (!$row) {
            echo json_encode(['monitor' => false]);
            return;
        }
        echo json_encode([
            'monitor' => ((int)$row->device_type === 0 || (int)$row->device_type === 5),
            'device_type' => $row->device_type,
            'session_index' => $row->session_index
        ]);
    }

    /**
     * AJAX: Get PON signal info (RX/TX and lastinform) for a username
     */
    public function get_pon_signal_info() {
        $username = $this->input->get('username');
        
        if (!$username) {
            echo json_encode(['rx'=>'N/A','tx'=>'N/A','lastinform'=>'N/A']);
            return;
        }
        
        $info = $this->snmp_model->get_pon_signal_info($username);
        
        if (!$info) {
            echo json_encode(['rx'=>'N/A','tx'=>'N/A','lastinform'=>'N/A']);
            return;
        }
        
        // Check if the values are actually set and not empty
        $rx = (!empty($info['rx']) && $info['rx'] !== null) ? $info['rx'] : 'N/A';
        $tx = (!empty($info['tx']) && $info['tx'] !== null) ? $info['tx'] : 'N/A';
        $lastinform = (!empty($info['lastinform']) && $info['lastinform'] !== null) ? $info['lastinform'] : 'N/A';
        
        echo json_encode([
            'rx' => $rx,
            'tx' => $tx,
            'lastinform' => $lastinform
        ]);
    }

    /**
     * List all SNMP cache entries (with search)
     */
    public function snmpCacheList() {
        $search = $this->input->get('search');
        $deviceType = $this->input->get('device_type');
        $nasIp = $this->input->get('nas_ip');
        $this->load->model('Snmp_model');
        $this->load->database();
        // For NAS filter dropdown
        $nasList = $this->db->select('nasname, shortname')->from('nas')->get()->result();
        $totalRows = $this->Snmp_model->getSnmpCacheListCount($search, $deviceType, $nasIp);
        // Pagination setup
        $this->load->library('pagination');
        $limit = 20;
        $config['base_url'] = base_url('snmp_controller/snmpCacheList');
        $config['total_rows'] = $totalRows;
        $config['per_page'] = $limit;
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string'] = TRUE;
        $config['full_tag_open'] = '<ul class="pagination pagination-sm m-0 float-right">';
        $config['full_tag_close'] = '</ul>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close'] = '</span></li>';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['attributes'] = array('class' => 'page-link');
        $this->pagination->initialize($config);
        $page = $this->input->get('page') ? (int)$this->input->get('page') : 0;
        $offset = $page;
        $snmpCacheList = $this->Snmp_model->getSnmpCacheList($search, $deviceType, $nasIp, $limit, $offset);
        $pagination_links = $this->pagination->create_links();
        $data = [
            'snmpCacheList' => $snmpCacheList,
            'search' => $search,
            'deviceType' => $deviceType,
            'nasIp' => $nasIp,
            'nasList' => $nasList,
            'totalRows' => $totalRows,
            'limit' => $limit,
            'pagination_links' => $pagination_links
        ];
        $this->global['pageTitle'] = 'SNMP Cache List';
        $this->loadViews('snmpcachelist', $this->global, $data, NULL);
    }

    /**
     * Debug function to test PON signal data retrieval
     */
    public function debug_pon_signal($username = null) {
        if (!$username) {
            $username = $this->input->get('username');
        }
        
        if (!$username) {
            echo "No username provided";
            return;
        }
        
        $this->load->database();
        
        // Check if table exists
        $table_exists = $this->db->table_exists('tbl_networkdevicemap');
        echo "Table exists: " . ($table_exists ? 'YES' : 'NO') . "<br>";
        
        if ($table_exists) {
            // Get table structure
            $fields = $this->db->list_fields('tbl_networkdevicemap');
            echo "Table fields: " . implode(', ', $fields) . "<br>";
            
            // Check for the specific username
            $row = $this->db->get_where('tbl_networkdevicemap', ['username' => $username])->row();
            
            if ($row) {
                echo "Found record for username: $username<br>";
                echo "para1 (RX): " . $row->para1 . "<br>";
                echo "para2 (TX): " . $row->para2 . "<br>";
                echo "lastinform: " . $row->lastinform . "<br>";
            } else {
                echo "No record found for username: $username<br>";
                
                // Show some sample records
                $sample = $this->db->limit(5)->get('tbl_networkdevicemap')->result();
                echo "Sample records:<br>";
                foreach ($sample as $record) {
                    echo "- Username: " . $record->username . ", para1: " . $record->para1 . ", para2: " . $record->para2 . "<br>";
                }
            }
        }
    }
} 
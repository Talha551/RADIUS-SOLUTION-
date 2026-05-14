<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';
require APPPATH . 'libraries/routeros_api.class.php';

class Mikrotik_api extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Mikrotik_model');
        $this->load->helper(array('form', 'url'));
        $this->isLoggedIn();   
    }

    /**
     * Test page to help configure routers
     */
    public function testPage()
    {
        $this->global['pageTitle'] = 'MikroTik Router Test Page';
        
        $data['routers'] = $this->Mikrotik_model->getEnabledRouters();
        $data['allRouters'] = $this->Mikrotik_model->getAllRouters();
        
        // Check if enableapi column exists
        $fields = $this->db->list_fields('nas');
        $data['hasEnableApi'] = in_array('enableapi', $fields);
        
        $this->loadViews("mikrotik/test_page", $this->global, $data, NULL);
    }

    /**
     * Debug function to check available routers
     */
    public function debugRouters()
    {
        echo "<h2>Database Table Structure:</h2>";
        echo "<pre>";
        
        // Check table structure
        $fields = $this->db->list_fields('nas');
        echo "Available fields in 'nas' table:\n";
        print_r($fields);
        echo "\n";
        
        // Check if enableapi column exists
        if (in_array('enableapi', $fields)) {
            echo "✓ 'enableapi' column exists\n";
        } else {
            echo "✗ 'enableapi' column does NOT exist\n";
            echo "You need to add this column to your 'nas' table:\n";
            echo "ALTER TABLE nas ADD COLUMN enableapi TINYINT(1) DEFAULT 0;\n\n";
        }
        
        echo "\n=== All Routers in Database ===\n";
        $allRouters = $this->Mikrotik_model->getAllRouters();
        
        if (empty($allRouters)) {
            echo "No routers found in database!\n";
        } else {
            foreach ($allRouters as $router) {
                echo "ID: " . $router->id . "\n";
                echo "Name: " . $router->shortname . "\n";
                echo "IP: " . $router->nasname . "\n";
                echo "API Username: " . $router->apiusername . "\n";
                echo "API Password: " . $router->apipassword . "\n";
                
                if (isset($router->enableapi)) {
                    echo "Enable API: " . $router->enableapi . "\n";
                } else {
                    echo "Enable API: Column not available\n";
                }
                
                echo "---\n";
            }
        }
        
        echo "\n=== Enabled Routers ===\n";
        $enabledRouters = $this->Mikrotik_model->getEnabledRouters();
        
        if (empty($enabledRouters)) {
            echo "No enabled routers found!\n";
            if (!in_array('enableapi', $fields)) {
                echo "Reason: 'enableapi' column missing from database\n";
                echo "Solution: Run this SQL command:\n";
                echo "ALTER TABLE nas ADD COLUMN enableapi TINYINT(1) DEFAULT 0;\n";
                echo "UPDATE nas SET enableapi = 1 WHERE id = your_router_id;\n";
            } else {
                echo "Reason: No routers have enableapi = 1\n";
                echo "Solution: Update a router with:\n";
                echo "UPDATE nas SET enableapi = 1 WHERE id = your_router_id;\n";
            }
        } else {
            echo "Found " . count($enabledRouters) . " enabled router(s):\n";
            foreach ($enabledRouters as $router) {
                echo "- " . $router->shortname . " (" . $router->nasname . ")\n";
            }
        }
        
        echo "</pre>";
    }

    public function index()
    {
        $this->global['pageTitle'] = 'MikroTik Interface Monitor : Dashboard';
        
        $data['routers'] = $this->Mikrotik_model->getEnabledRouters();
        $data['interfaceTypes'] = $this->Mikrotik_model->getInterfaceTypes();
        
        $this->loadViews("mikrotik/interface_monitor", $this->global, $data, NULL);
    }

    /**
     * Get interface statistics via AJAX
     */
    public function getInterfaceStats()
    {
        // Start output buffering to prevent any HTML output
        ob_start();
        
        // Set JSON content type header
        header('Content-Type: application/json');
        
        try {
            $routerId = $this->input->post('router_id');
            
            if (!$routerId) {
                $response = array('error' => 'Router ID is required');
                echo json_encode($response);
                return;
            }
            
            // Log the request for debugging
            log_message('debug', 'getInterfaceStats called with router_id: ' . $routerId);
            
            $result = $this->Mikrotik_model->getInterfaceStats($routerId);
            
            if (isset($result['error'])) {
                $response = array('error' => $result['error']);
            } else {
                $formattedData = $this->Mikrotik_model->formatInterfaceData($result['data']);
                $response = array('success' => true, 'data' => $formattedData);
            }
            
            // Clear any output buffer content
            ob_clean();
            echo json_encode($response);
            
        } catch (Exception $e) {
            log_message('error', 'Exception in getInterfaceStats: ' . $e->getMessage());
            ob_clean();
            $response = array('error' => 'Server error: ' . $e->getMessage());
            echo json_encode($response);
        } catch (Error $e) {
            log_message('error', 'Error in getInterfaceStats: ' . $e->getMessage());
            ob_clean();
            $response = array('error' => 'Server error: ' . $e->getMessage());
            echo json_encode($response);
        }
        
        // End output buffering
        ob_end_flush();
    }

    /**
     * Get real-time statistics for specific interface
     */
    public function getRealTimeStats($routerId=244)
    {
        $routerId = $this->input->post('router_id');
        $interfaceName = $this->input->post('interface_name');
        
        if (!$routerId) {
            $response = array('error' => 'Router ID is required');
            echo json_encode($response);
            return;
        }
        
        // Use getInterfaceStats instead of getRealTimeStats to get complete interface data
        $result = $this->Mikrotik_model->getInterfaceStats($routerId);
        
        if (isset($result['error'])) {
            $response = array('error' => $result['error']);
        } else {
            // Filter by interface name if specified
            if ($interfaceName && $interfaceName != 'all') {
                $filteredData = array();
                foreach ($result['data'] as $interface) {
                    if (isset($interface['name']) && $interface['name'] == $interfaceName) {
                        $filteredData[] = $interface;
                    }
                }
                $response = array('success' => true, 'data' => $filteredData);
            } else {
                $response = array('success' => true, 'data' => $result['data']);
            }
        }
        
        echo json_encode($response);
    }

    /**
     * Test connection to MikroTik router
     */
    public function testConnection()
    {
        // Set JSON content type header
        header('Content-Type: application/json');
        
        try {
            $routerId = $this->input->post('router_id');
            
            if (!$routerId) {
                $response = array('error' => 'Router ID is required');
                echo json_encode($response);
                return;
            }
            
            $router = $this->Mikrotik_model->getRouterById($routerId);
            
            if (!$router) {
                $response = array('error' => 'Router not found or not enabled');
                echo json_encode($response);
                return;
            }
            
            require_once APPPATH . 'libraries/routeros_api.class.php';
            
            $API = new RouterosAPI();
            $API->debug = false;
            
            try {
                if ($API->connect($router->nasname, $router->apiusername, $router->apipassword)) {
                    $response = array('success' => true, 'message' => 'Connection successful to ' . $router->shortname);
                    $API->disconnect();
                } else {
                    $response = array('error' => 'Failed to connect to router: ' . $router->nasname);
                }
            } catch (Exception $e) {
                $response = array('error' => 'Connection error: ' . $e->getMessage());
            }
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            log_message('error', 'Exception in testConnection: ' . $e->getMessage());
            $response = array('error' => 'Server error: ' . $e->getMessage());
            echo json_encode($response);
        } catch (Error $e) {
            log_message('error', 'Error in testConnection: ' . $e->getMessage());
            $response = array('error' => 'Server error: ' . $e->getMessage());
            echo json_encode($response);
        }
    }

    function connectMikrotik(){
        $username = "u2nasir10";
        $this->load->model('Reports_model');
        //$isUserOnline = $this->Reports_model->checkUserOnlineStatus($username);

        $API = new RouterosAPI();
        $API->debug = true;
        
        if ($API->connect('115.186.148.250', 'admin', 'Khyber@007')) {
            $API->write('/interface/monitor-traffic',false);
            $API->write('=interface=<pppoe-u2nasir3>',false);
            $API->write('=once=');
        
           $READ = $API->read(false);
           $ARRAY = $API->parseResponse($READ);
           print_r($ARRAY);

           //$API->comm("/ip/firewall/mangle/add", array("chain" => "prerouting", "action" => "mark-routing", "new-routing-mark" => "to_WAN3"));

           $API->disconnect();
        }    
    }

    function addMikrotikUser($user = "", $password = ""){
        $API = new RouterosAPI();
        $API->debug = true;

        if ($API->connect('192.168.101.69', 'admin', 'IslamAbad321')) {
            $API->comm("/ppp/secret/add", array(
                "name"     => $user,
                "password" => $password,
                "comment"  => "Voucher Created By API",
                "service"  => "any",
            ));

            $API->disconnect();
        }
    }

    /**
     * Test MikroTik API connection and interface retrieval
     */
    public function testMikrotikAPI()
    {
        $routerId = 244; // Hardcode for testing
        $router = $this->Mikrotik_model->getRouterById($routerId);
        
        if (!$router) {
            echo "Router not found!";
            return;
        }
        
        echo "<h2>Testing MikroTik API Connection</h2>";
        echo "<p><strong>Router:</strong> " . $router->shortname . " (" . $router->nasname . ")</p>";
        echo "<p><strong>API Username:</strong> " . $router->apiusername . "</p>";
        echo "<p><strong>API Password:</strong> " . str_repeat('*', strlen($router->apipassword)) . "</p>";
        
        require_once APPPATH . 'libraries/routeros_api.class.php';
        $API = new RouterosAPI();
        $API->debug = true;
        
        try {
            echo "<h3>Attempting Connection...</h3>";
            
            if ($API->connect($router->nasname, $router->apiusername, $router->apipassword)) {
                echo "<p style='color: green;'>✓ Connection successful!</p>";
                
                echo "<h3>Getting Interface List...</h3>";
                $interfaces = $API->comm('/interface/print');
                echo "<p>Found " . count($interfaces) . " interfaces:</p>";
                echo "<ul>";
                foreach ($interfaces as $interface) {
                    echo "<li>" . $interface['name'] . " (" . $interface['type'] . ")</li>";
                }
                echo "</ul>";
                
                echo "<h3>Getting Interface Statistics...</h3>";
                $stats = $API->comm('/interface/monitor-traffic', array(
                    'interface' => 'all',
                    'once' => ''
                ));
                echo "<p>Found " . count($stats) . " statistics records:</p>";
                echo "<ul>";
                foreach ($stats as $stat) {
                    echo "<li>" . $stat['name'] . " - TX: " . $stat['tx-byte'] . ", RX: " . $stat['rx-byte'] . "</li>";
                }
                echo "</ul>";
                
                $API->disconnect();
                echo "<p style='color: green;'>✓ Test completed successfully!</p>";
                
            } else {
                echo "<p style='color: red;'>✗ Connection failed!</p>";
            }
            
        } catch (Exception $e) {
            echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
        }
    }

    /**
     * Show add VLAN form
     */
    public function addVlanForm()
    {
        $routerId = $this->input->get('router_id');
        $interfaceName = $this->input->get('interface_name');
        
        if (!$routerId || !$interfaceName) {
            $this->session->set_flashdata('error', 'Router ID and Interface name are required');
            redirect('Mikrotik/Mikrotik_api');
        }
        
        // Get router details
        $router = $this->Mikrotik_model->getRouterById($routerId);
        if (!$router) {
            $this->session->set_flashdata('error', 'Router not found or not enabled');
            redirect('Mikrotik/Mikrotik_api');
        }
        
        // Get interface details
        $interfaceResult = $this->Mikrotik_model->getInterfaceDetails($routerId, $interfaceName);
        if (isset($interfaceResult['error'])) {
            $this->session->set_flashdata('error', $interfaceResult['error']);
            redirect('Mikrotik/Mikrotik_api');
        }
        
        $interface = $interfaceResult['data'];
        
        // Prepare data for view
        $data = array(
            'router_id' => $routerId,
            'router_name' => $router->shortname . ' (' . $router->nasname . ')',
            'interface_name' => $interfaceName,
            'interface_type' => isset($interface['type']) ? $interface['type'] : 'Unknown',
            'interface_status' => isset($interface['running']) ? ($interface['running'] == 'true' ? 'Yes' : 'No') : 'Unknown'
        );
        
        $this->global['pageTitle'] = 'Add VLAN Interface';
        $this->loadViews("mikrotik/add_vlan", $this->global, $data, NULL);
    }

    /**
     * Add VLAN interface via AJAX
     */
    public function addVlan()
    {
        // Set JSON content type header
        header('Content-Type: application/json');
        
        try {
            // Validate required fields
            $routerId = $this->input->post('router_id');
            $interfaceName = $this->input->post('interface_name');
            $vlanName = $this->input->post('vlan_name');
            $vlanId = $this->input->post('vlan_id');
            
            if (!$routerId || !$interfaceName || !$vlanName || !$vlanId) {
                $response = array('error' => 'All required fields must be provided');
                echo json_encode($response);
                return;
            }
            
            // Validate VLAN ID range
            if ($vlanId < 1 || $vlanId > 4094) {
                $response = array('error' => 'VLAN ID must be between 1 and 4094');
                echo json_encode($response);
                return;
            }
            
            // Validate VLAN name format
            if (!preg_match('/^[a-zA-Z0-9_-]+$/', $vlanName)) {
                $response = array('error' => 'VLAN name can only contain letters, numbers, hyphens, and underscores');
                echo json_encode($response);
                return;
            }
            
            // Prepare VLAN data
            $vlanData = array(
                'name' => $vlanName,
                'vlan_id' => $vlanId,
                'comment' => $this->input->post('vlan_comment'),
                'mtu' => $this->input->post('vlan_mtu'),
                'arp' => $this->input->post('vlan_arp'),
                'use-service-tag' => $this->input->post('vlan_use_service_tag'),
                'disabled' => $this->input->post('vlan_disabled')
            );
            
            // Remove empty values
            $vlanData = array_filter($vlanData, function($value) {
                return $value !== '' && $value !== null;
            });
            
            // Add VLAN interface
            $result = $this->Mikrotik_model->addVlanInterface($routerId, $interfaceName, $vlanData);
            
            if (isset($result['error'])) {
                $response = array('error' => $result['error']);
            } else {
                $response = array(
                    'success' => true, 
                    'message' => 'VLAN interface "' . $vlanName . '" created successfully on interface "' . $interfaceName . '"'
                );
            }
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            log_message('error', 'Exception in addVlan: ' . $e->getMessage());
            $response = array('error' => 'Server error: ' . $e->getMessage());
            echo json_encode($response);
        } catch (Error $e) {
            log_message('error', 'Error in addVlan: ' . $e->getMessage());
            $response = array('error' => 'Server error: ' . $e->getMessage());
            echo json_encode($response);
        }
    }

    /**
     * Check if VLAN exists (for AJAX validation)
     */
    public function checkVlanExists()
    {
        // Set JSON content type header
        header('Content-Type: application/json');
        
        try {
            $routerId = $this->input->post('router_id');
            $vlanName = $this->input->post('vlan_name');
            $vlanId = $this->input->post('vlan_id');
            
            if (!$routerId || !$vlanName) {
                $response = array('error' => 'Router ID and VLAN name are required');
                echo json_encode($response);
                return;
            }
            
            $result = $this->Mikrotik_model->checkVlanExists($routerId, $vlanName, $vlanId);
            
            if (isset($result['error'])) {
                $response = array('error' => $result['error']);
            } else {
                $response = array(
                    'success' => true,
                    'exists' => !empty($result['exists']),
                    'details' => $result['exists']
                );
            }
            
            echo json_encode($response);
            
        } catch (Exception $e) {
            log_message('error', 'Exception in checkVlanExists: ' . $e->getMessage());
            $response = array('error' => 'Server error: ' . $e->getMessage());
            echo json_encode($response);
        }
    }
}
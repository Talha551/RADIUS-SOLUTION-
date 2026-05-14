<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Mikrotik_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all enabled MikroTik routers from NAS table
     */
    public function getEnabledRouters()
    {
        // First check if enableapi column exists
        $columns = $this->db->list_fields('nas');
        
        if (in_array('enableapi', $columns)) {
            $this->db->select('id, nasname, shortname, ports, apiusername, apipassword, description, enableapi');
            $this->db->from('nas');
            $this->db->where('enableapi', 1);
        } else {
            // If enableapi column doesn't exist, get all routers
            $this->db->select('id, nasname, shortname, ports, apiusername, apipassword, description');
            $this->db->from('nas');
        }
        
        $this->db->order_by('shortname', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get router details by ID
     */
    public function getRouterById($id)
    {
        $columns = $this->db->list_fields('nas');
        
        if (in_array('enableapi', $columns)) {
            $this->db->select('id, nasname, shortname, ports, apiusername, apipassword, description, enableapi');
            $this->db->from('nas');
            $this->db->where('id', $id);
            $this->db->where('enableapi', 1);
        } else {
            $this->db->select('id, nasname, shortname, ports, apiusername, apipassword, description');
            $this->db->from('nas');
            $this->db->where('id', $id);
        }
        
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Get router details by NAS name
     */
    public function getRouterByNasname($nasname)
    {
        $columns = $this->db->list_fields('nas');
        
        if (in_array('enableapi', $columns)) {
            $this->db->select('id, nasname, shortname, ports, apiusername, apipassword, description, enableapi');
            $this->db->from('nas');
            $this->db->where('nasname', $nasname);
            $this->db->where('enableapi', 1);
        } else {
            $this->db->select('id, nasname, shortname, ports, apiusername, apipassword, description');
            $this->db->from('nas');
            $this->db->where('nasname', $nasname);
        }
        
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Get all routers (for debugging)
     */
    public function getAllRouters()
    {
        $columns = $this->db->list_fields('nas');
        
        if (in_array('enableapi', $columns)) {
            $this->db->select('id, nasname, shortname, ports, apiusername, apipassword, description, enableapi');
        } else {
            $this->db->select('id, nasname, shortname, ports, apiusername, apipassword, description');
        }
        
        $this->db->from('nas');
        $this->db->order_by('shortname', 'ASC');
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get all interface types for filtering
     */
    public function getInterfaceTypes()
    {
        return array(
            'ethernet' => 'Ethernet',
            'vlan' => 'VLAN',
            'pppoe' => 'PPPoE',
            'pptp' => 'PPTP',
            'l2tp' => 'L2TP',
            'sstp' => 'SSTP',
            'wireless' => 'Wireless',
            'bridge' => 'Bridge',
            'bond' => 'Bond',
            'loopback' => 'Loopback',
            'gre' => 'GRE',
            'ipip' => 'IPIP',
            'eoip' => 'EOIP',
            'vxlan' => 'VXLAN',
            'other' => 'Other'
        );
    }

    /**
     * Format interface data for display
     */
    public function formatInterfaceData($interfaces)
    {
        $formatted = array();
        
        if (is_array($interfaces)) {
            foreach ($interfaces as $interface) {
                $formatted[] = array(
                    'name' => isset($interface['name']) ? $interface['name'] : '',
                    'type' => isset($interface['type']) ? $interface['type'] : '',
                    'orig-name' => isset($interface['orig-name']) ? $interface['orig-name'] : '',
                    'actual-mtu' => isset($interface['actual-mtu']) ? $interface['actual-mtu'] : '',
                    'l2mtu' => isset($interface['l2mtu']) ? $interface['l2mtu'] : '',
                    // Real-time rates (bits per second) formatted
                    'tx-bps' => isset($interface['tx-bits-per-second']) ? $this->formatBitsPerSecond($interface['tx-bits-per-second']) : '0 bps',
                    'rx-bps' => isset($interface['rx-bits-per-second']) ? $this->formatBitsPerSecond($interface['rx-bits-per-second']) : '0 bps',
                    'tx-byte' => isset($interface['tx-byte']) ? $this->formatBytes($interface['tx-byte']) : '0 B',
                    'rx-byte' => isset($interface['rx-byte']) ? $this->formatBytes($interface['rx-byte']) : '0 B',
                    'tx-packet' => isset($interface['tx-packet']) ? $interface['tx-packet'] : '0',
                    'rx-packet' => isset($interface['rx-packet']) ? $interface['rx-packet'] : '0',
                    'fp-tx-byte' => isset($interface['fp-tx-byte']) ? $this->formatBytes($interface['fp-tx-byte']) : '0 B',
                    'fp-rx-byte' => isset($interface['fp-rx-byte']) ? $this->formatBytes($interface['fp-rx-byte']) : '0 B',
                    'fp-tx-packet' => isset($interface['fp-tx-packet']) ? $interface['fp-tx-packet'] : '0',
                    'fp-rx-packet' => isset($interface['fp-rx-packet']) ? $interface['fp-rx-packet'] : '0',
                    'running' => isset($interface['running']) ? ($interface['running'] == 'true' ? 'Yes' : 'No') : 'No',
                    'disabled' => isset($interface['disabled']) ? ($interface['disabled'] == 'true' ? 'Yes' : 'No') : 'No'
                );
            }
        }
        
        return $formatted;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Format bits per second to human readable (Kbps, Mbps, Gbps)
     */
    private function formatBitsPerSecond($bps, $precision = 2)
    {
        // Ensure numeric
        $num = floatval($bps);
        $units = array('bps', 'Kbps', 'Mbps', 'Gbps', 'Tbps');
        $i = 0;
        while ($num >= 1000 && $i < count($units) - 1) {
            $num /= 1000;
            $i++;
        }
        return round($num, $precision) . ' ' . $units[$i];
    }

    /**
     * Build a map of PPPoE active interface name -> username
     */
    private function getActivePppoeInterfaceUsernameMap($API)
    {
        $map = array();
        try {
            // Request only required fields for speed
            $actives = $API->comm('/ppp/active/print', array('.proplist' => 'name,service,interface'));
            foreach ($actives as $row) {
                if (!isset($row['service']) || $row['service'] !== 'pppoe') {
                    continue;
                }
                $username = isset($row['name']) ? $row['name'] : '';
                $iface = isset($row['interface']) ? $row['interface'] : '';
                if ($iface !== '' && $username !== '') {
                    // Direct map
                    $map[$iface] = $username;
                    // If interface is wrapped like <pppoe-username>, also index by plain extracted name
                    if ($iface[0] === '<' && substr($iface, -1) === '>') {
                        $plain = trim($iface, '<>');
                        $map[$plain] = $username;
                    }
                }
            }
        } catch (Exception $e) {
            log_message('error', 'Error building PPPoE active map: ' . $e->getMessage());
        }
        return $map;
    }

    /**
     * Get PPPoE interface names with usernames
     */
    private function getPPPoEInterfaceNames($API)
    {
        $interfaceNames = array();
        
        try {
            // Get all PPPoE interfaces and their details
            $pppoeInterfaces = $API->comm('/interface/print', array(
                '?type' => 'pppoe-in'
            ));
            
            // Map from active sessions
            $activeMap = $this->getActivePppoeInterfaceUsernameMap($API);
            
            foreach ($pppoeInterfaces as $pppoe) {
                if (!isset($pppoe['name'])) { continue; }
                $interfaceName = $pppoe['name'];
                
                // First try to map via active sessions
                if (isset($activeMap[$interfaceName])) {
                    $interfaceNames[$interfaceName] = $activeMap[$interfaceName];
                    continue;
                }
                if (isset($activeMap['<' . $interfaceName . '>'])) {
                    $interfaceNames[$interfaceName] = $activeMap['<' . $interfaceName . '>'];
                    continue;
                }
                
                // Fallbacks: try parse username from interface naming pattern like <pppoe-username>
                if ($interfaceName && $interfaceName[0] === '<' && substr($interfaceName, -1) === '>') {
                    $interfaceNames[$interfaceName] = trim($interfaceName, '<>');
                    continue;
                }
                
                // Last resort: look up active records filtered by interface
                try {
                    $activeConnections = $API->comm('/ppp/active/print', array(
                        '?interface' => $interfaceName,
                        '.proplist' => 'name'
                    ));
                    if (!empty($activeConnections) && isset($activeConnections[0]['name'])) {
                        $interfaceNames[$interfaceName] = $activeConnections[0]['name'];
                    } else {
                        $interfaceNames[$interfaceName] = $interfaceName; // leave as is
                    }
                } catch (Exception $e) {
                    log_message('error', 'Error getting active PPPoE for interface ' . $interfaceName . ': ' . $e->getMessage());
                    $interfaceNames[$interfaceName] = $interfaceName;
                }
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error getting PPPoE interface names: ' . $e->getMessage());
        }
        
        return $interfaceNames;
    }

    /**
     * Get PPPoE usernames from MikroTik
     */
    private function getPPPoEUsernames($API)
    {
        $usernames = array();
        
        try {
            // Try to get PPPoE secrets (where usernames are stored)
            $secrets = $API->comm('/ppp/secret/print');
            foreach ($secrets as $secret) {
                if (isset($secret['service']) && $secret['service'] == 'pppoe') {
                    $usernames[$secret['name']] = $secret['name'];
                }
            }
            
            // Also try to get active PPPoE connections
            $connections = $API->comm('/ppp/active/print');
            foreach ($connections as $connection) {
                if (isset($connection['service']) && $connection['service'] == 'pppoe') {
                    $usernames[$connection['name']] = $connection['name'];
                }
            }
            
        } catch (Exception $e) {
            log_message('error', 'Error getting PPPoE usernames: ' . $e->getMessage());
        }
        
        return $usernames;
    }

    /**
     * Get interface statistics from MikroTik
     */
    public function getInterfaceStats($routerId)
    {
        try {
            $router = $this->getRouterById($routerId);
            
            if (!$router) {
                return array('error' => 'Router not found or not enabled');
            }

            require_once APPPATH . 'libraries/routeros_api.class.php';
            
            $API = new RouterosAPI();
            $API->debug = false;
            
            // Connect to router
            if ($API->connect($router->nasname, $router->apiusername, $router->apipassword)) {
                
                // Get PPPoE usernames first
                $pppoeUsernames = $this->getPPPoEUsernames($API);
                log_message('debug', 'PPPoE usernames found: ' . json_encode($pppoeUsernames));
                
                // Get detailed PPPoE interface information
                $pppoeDetails = $this->getPPPoEInterfaceNames($API);
                log_message('debug', 'PPPoE interface details found: ' . json_encode($pppoeDetails));
                
                // Get interface list
                $interfaces = $API->comm('/interface/print');
                log_message('debug', 'Interfaces found: ' . count($interfaces));
                
                // Debug: Log first interface structure
                if (!empty($interfaces)) {
                    log_message('debug', 'First interface structure: ' . json_encode($interfaces[0]));
                }
                
                // Get interface statistics
                $stats = $API->comm('/interface/monitor-traffic', array(
                    'interface' => 'all',
                    'once' => ''
                ));
                log_message('debug', 'Stats found: ' . count($stats));
                // Build quick lookup by stat name
                $statsByName = array();
                $logPreview = array();
                foreach ($stats as $idx => $st) {
                    if (isset($st['name'])) {
                        $statsByName[$st['name']] = $st;
                        if ($idx < 5) {
                            $logPreview[] = array('name' => $st['name'], 'tx' => isset($st['tx-bits-per-second']) ? $st['tx-bits-per-second'] : null, 'rx' => isset($st['rx-bits-per-second']) ? $st['rx-bits-per-second'] : null);
                        }
                    }
                }
                if (!empty($logPreview)) {
                    log_message('debug', 'Stats preview (first items): ' . json_encode($logPreview));
                }
                
                // Merge interface info with statistics
                $result = array();
                // Build active mapping once
                $activeMap = $this->getActivePppoeInterfaceUsernameMap($API);
                foreach ($interfaces as $interface) {
                    $interfaceName = $interface['name'];
                    $interfaceStats = null;
                    
                    // Find matching statistics by several strategies
                    if (isset($statsByName[$interfaceName])) {
                        $interfaceStats = $statsByName[$interfaceName];
                    } else {
                        // Try default-name
                        if (isset($interface['default-name']) && isset($statsByName[$interface['default-name']])) {
                            $interfaceStats = $statsByName[$interface['default-name']];
                            log_message('debug', 'Matched stats by default-name: ' . $interfaceName . ' <= ' . $interface['default-name']);
                        } else {
                            // Try removing numeric prefix like 00- or 01-
                            $sanitized = preg_replace('/^[0-9]+-/', '', $interfaceName);
                            if ($sanitized !== $interfaceName && isset($statsByName[$sanitized])) {
                                $interfaceStats = $statsByName[$sanitized];
                                log_message('debug', 'Matched stats by sanitized name: ' . $interfaceName . ' <= ' . $sanitized);
                            }
                        }
                    }
                    
                    // Preserve original name
                    $interface['orig-name'] = $interfaceName;
                    
                    // For PPPoE interfaces, try to get the username
                    if (isset($interface['type']) && $interface['type'] == 'pppoe-in') {
                        // 1) active map direct
                        if (isset($activeMap[$interfaceName])) {
                            $interface['name'] = $activeMap[$interfaceName];
                        } elseif (isset($activeMap['<' . $interfaceName . '>'])) {
                            $interface['name'] = $activeMap['<' . $interfaceName . '>'];
                        } elseif (isset($pppoeDetails[$interfaceName])) { // 2) details map
                            $interface['name'] = $pppoeDetails[$interfaceName];
                        } else { // 3) fallbacks
                            if (isset($interface['user'])) {
                                $interface['name'] = $interface['user'];
                            } elseif (isset($interface['username'])) {
                                $interface['name'] = $interface['username'];
                            } elseif (isset($interface['service-name'])) {
                                $interface['name'] = $interface['service-name'];
                            } elseif ($interfaceName && $interfaceName[0] === '<' && substr($interfaceName, -1) === '>') {
                                $interface['name'] = trim($interfaceName, '<>');
                            }
                        }
                        
                        log_message('debug', 'PPPoE interface mapped: ' . $interfaceName . ' => ' . $interface['name']);
                    }
                    
                    // Merge interface info with stats
                    $result[] = array_merge($interface, $interfaceStats ? $interfaceStats : array());
                }
                
                log_message('debug', 'Final result count: ' . count($result));
                // Fallback: for interfaces with missing/zero bps, try fetching individual monitor-traffic using the same connection (faster)
                $fallbackCount = 0;
                $fallbackLimit = 20; // cap fallbacks to keep refresh fast
                foreach ($result as &$row) {
                    $hasTx = isset($row['tx-bits-per-second']) ? intval($row['tx-bits-per-second']) : 0;
                    $hasRx = isset($row['rx-bits-per-second']) ? intval($row['rx-bits-per-second']) : 0;
                    $isRunning = isset($row['running']) ? ($row['running'] === 'true' || $row['running'] === true || $row['running'] === 'Yes') : null;
                    if ($fallbackCount < $fallbackLimit && ($hasTx === 0 && $hasRx === 0) && isset($row['orig-name']) && ($isRunning === null || $isRunning === true)) {
                        try {
                            $nameCandidates = array($row['orig-name']);
                            if (isset($row['default-name'])) { $nameCandidates[] = $row['default-name']; }
                            $sanitized = preg_replace('/^[0-9]+-/', '', $row['orig-name']);
                            if ($sanitized !== $row['orig-name']) { $nameCandidates[] = $sanitized; }
                            // Try each candidate until we get non-zero or present bps
                            foreach ($nameCandidates as $candidate) {
                                $one = $API->comm('/interface/monitor-traffic', array('interface' => $candidate, 'once' => ''));
                                if (!empty($one) && isset($one[0])) {
                                    $sample = $one[0];
                                    if (isset($sample['tx-bits-per-second']) || isset($sample['rx-bits-per-second'])) {
                                        $row['tx-bits-per-second'] = isset($sample['tx-bits-per-second']) ? $sample['tx-bits-per-second'] : 0;
                                        $row['rx-bits-per-second'] = isset($sample['rx-bits-per-second']) ? $sample['rx-bits-per-second'] : 0;
                                        log_message('debug', 'Per-interface monitor filled bps for ' . $row['orig-name'] . ' via ' . $candidate . ' => TX:' . $row['tx-bits-per-second'] . ' RX:' . $row['rx-bits-per-second']);
                                        $fallbackCount++;
                                        break;
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            log_message('error', 'Per-interface monitor error for ' . $row['orig-name'] . ': ' . $e->getMessage());
                        }
                    }
                }

                // Disconnect after all fallbacks are done
                $API->disconnect();

                return array('success' => true, 'data' => $result);
                
            } else {
                return array('error' => 'Failed to connect to router: ' . $router->nasname);
            }
            
        } catch (Exception $e) {
            log_message('error', 'Exception in getInterfaceStats: ' . $e->getMessage());
            return array('error' => 'API Error: ' . $e->getMessage());
        } catch (Error $e) {
            log_message('error', 'Error in getInterfaceStats: ' . $e->getMessage());
            return array('error' => 'API Error: ' . $e->getMessage());
        }
    }

    /**
     * Get real-time interface statistics
     */
    public function getRealTimeStats($routerId, $interfaceName = 'all')
    {
        $router = $this->getRouterById($routerId);
        
        if (!$router) {
            return array('error' => 'Router not found or not enabled');
        }

        require_once APPPATH . 'libraries/routeros_api.class.php';
        
        $API = new RouterosAPI();
        $API->debug = false;
        
        try {
            if ($API->connect($router->nasname, $router->apiusername, $router->apipassword)) {
                
                $params = array('once' => '');
                if ($interfaceName != 'all') {
                    $params['interface'] = $interfaceName;
                }
                
                $stats = $API->comm('/interface/monitor-traffic', $params);
                
                $API->disconnect();
                return array('success' => true, 'data' => $stats);
                
            } else {
                return array('error' => 'Failed to connect to router');
            }
            
        } catch (Exception $e) {
            return array('error' => 'API Error: ' . $e->getMessage());
        }
    }

    /**
     * Get interface details by name
     */
    public function getInterfaceDetails($routerId, $interfaceName)
    {
        $router = $this->getRouterById($routerId);
        
        if (!$router) {
            return array('error' => 'Router not found or not enabled');
        }

        require_once APPPATH . 'libraries/routeros_api.class.php';
        
        $API = new RouterosAPI();
        $API->debug = false;
        
        try {
            if ($API->connect($router->nasname, $router->apiusername, $router->apipassword)) {
                
                // Get interface details
                $interfaces = $API->comm('/interface/print', array(
                    '?name' => $interfaceName
                ));
                
                $API->disconnect();
                
                if (!empty($interfaces)) {
                    return array('success' => true, 'data' => $interfaces[0]);
                } else {
                    return array('error' => 'Interface not found: ' . $interfaceName);
                }
                
            } else {
                return array('error' => 'Failed to connect to router');
            }
            
        } catch (Exception $e) {
            return array('error' => 'API Error: ' . $e->getMessage());
        }
    }

    /**
     * Check if VLAN already exists
     */
    public function checkVlanExists($routerId, $vlanName, $vlanId = null)
    {
        $router = $this->getRouterById($routerId);
        
        if (!$router) {
            return array('error' => 'Router not found or not enabled');
        }

        require_once APPPATH . 'libraries/routeros_api.class.php';
        
        $API = new RouterosAPI();
        $API->debug = false;
        
        try {
            if ($API->connect($router->nasname, $router->apiusername, $router->apipassword)) {
                
                // Check by VLAN name
                $vlanByName = $API->comm('/interface/vlan/print', array(
                    '?name' => $vlanName
                ));
                
                // Check by VLAN ID if provided
                $vlanById = array();
                if ($vlanId) {
                    $vlanById = $API->comm('/interface/vlan/print', array(
                        '?vlan-id' => $vlanId
                    ));
                }
                
                $API->disconnect();
                
                $exists = array();
                if (!empty($vlanByName)) {
                    $exists['name'] = $vlanByName[0];
                }
                if (!empty($vlanById)) {
                    $exists['id'] = $vlanById[0];
                }
                
                return array('success' => true, 'exists' => $exists);
                
            } else {
                return array('error' => 'Failed to connect to router');
            }
            
        } catch (Exception $e) {
            return array('error' => 'API Error: ' . $e->getMessage());
        }
    }

    /**
     * Add VLAN interface to MikroTik router
     */
    public function addVlanInterface($routerId, $interfaceName, $vlanData)
    {
        $router = $this->getRouterById($routerId);
        
        if (!$router) {
            return array('error' => 'Router not found or not enabled');
        }

        require_once APPPATH . 'libraries/routeros_api.class.php';
        
        $API = new RouterosAPI();
        $API->debug = false;
        
        try {
            if ($API->connect($router->nasname, $router->apiusername, $router->apipassword)) {
                
                // First, verify the parent interface exists
                $parentInterface = $API->comm('/interface/print', array(
                    '?name' => $interfaceName
                ));
                
                if (empty($parentInterface)) {
                    $API->disconnect();
                    return array('error' => 'Parent interface not found: ' . $interfaceName);
                }
                
                // Check if VLAN already exists
                $vlanCheck = $this->checkVlanExists($routerId, $vlanData['name'], $vlanData['vlan_id']);
                if (isset($vlanCheck['exists']) && !empty($vlanCheck['exists'])) {
                    $API->disconnect();
                    return array('error' => 'VLAN already exists with this name or ID');
                }
                
                // Prepare VLAN parameters
                $vlanParams = array(
                    'name' => $vlanData['name'],
                    'vlan-id' => $vlanData['vlan_id'],
                    'interface' => $interfaceName
                );
                
                // Add optional parameters if provided
                if (!empty($vlanData['comment'])) {
                    $vlanParams['comment'] = $vlanData['comment'];
                }
                
                if (!empty($vlanData['mtu'])) {
                    $vlanParams['mtu'] = $vlanData['mtu'];
                }
                
                if (!empty($vlanData['arp'])) {
                    $vlanParams['arp'] = $vlanData['arp'];
                }
                
                if (!empty($vlanData['use-service-tag'])) {
                    $vlanParams['use-service-tag'] = $vlanData['use-service-tag'];
                }
                
                if (!empty($vlanData['disabled'])) {
                    $vlanParams['disabled'] = 'yes';
                }
                
                // Create VLAN interface
                $result = $API->comm('/interface/vlan/add', $vlanParams);
                
                $API->disconnect();
                
                if (isset($result['!trap'])) {
                    return array('error' => 'Failed to create VLAN: ' . $result['!trap'][0]['message']);
                } else {
                    return array('success' => true, 'message' => 'VLAN interface created successfully');
                }
                
            } else {
                return array('error' => 'Failed to connect to router');
            }
            
        } catch (Exception $e) {
            return array('error' => 'API Error: ' . $e->getMessage());
        }
    }

    /**
     * Get existing VLANs for validation
     */
    public function getExistingVlans($routerId)
    {
        $router = $this->getRouterById($routerId);
        
        if (!$router) {
            return array('error' => 'Router not found or not enabled');
        }

        require_once APPPATH . 'libraries/routeros_api.class.php';
        
        $API = new RouterosAPI();
        $API->debug = false;
        
        try {
            if ($API->connect($router->nasname, $router->apiusername, $router->apipassword)) {
                
                $vlans = $API->comm('/interface/vlan/print');
                
                $API->disconnect();
                
                return array('success' => true, 'data' => $vlans);
                
            } else {
                return array('error' => 'Failed to connect to router');
            }
            
        } catch (Exception $e) {
            return array('error' => 'API Error: ' . $e->getMessage());
        }
    }
}

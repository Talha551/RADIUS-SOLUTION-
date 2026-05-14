<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Snmp_model extends CI_Model
{
    private $snmp_host = '123.253.93.158';
    private $snmp_community = 'pace-read';
    private $ifDescr_oid = '1.3.6.1.2.1.2.2.1.2'; // ifDescr
    private $ifMtu_oid = '1.3.6.1.2.1.2.2.1.4';   // ifMtu
    private $ifType_oid = '1.3.6.1.2.1.2.2.1.3';  // ifType
    private $ifAdminStatus_oid = '1.3.6.1.2.1.2.2.1.7'; // ifAdminStatus
    private $ifOperStatus_oid = '1.3.6.1.2.1.2.2.1.8';  // ifOperStatus

    public function getUserDetailsBySnmp($username)
    {
        // Walk the interface descriptions
        $results = @snmp2_walk($this->snmp_host, $this->snmp_community, $this->ifDescr_oid, 1000000, 2);
        if (!$results) return false;
        foreach ($results as $row) {
            if (stripos($row, $username) !== false) {
                // Extract interface index from OID
                if (preg_match('/\.([0-9]+)$/', $row, $matches)) {
                    $index = $matches[1];
                } else {
                    // Try to extract from key (if returned as key=>value)
                    $index = null;
                }
                // If not found, try to get from snmp2_real_walk
                if (!$index) {
                    $walk = @snmp2_real_walk($this->snmp_host, $this->snmp_community, $this->ifDescr_oid, 1000000, 2);
                    foreach ($walk as $oid => $val) {
                        if (stripos($val, $username) !== false && preg_match('/\.([0-9]+)$/', $oid, $m)) {
                            $index = $m[1];
                            break;
                        }
                    }
                }
                if ($index) {
                    // Fetch more details for this interface
                    $mtu = @snmp2_get($this->snmp_host, $this->snmp_community, $this->ifMtu_oid . "." . $index, 1000000, 2);
                    $type = @snmp2_get($this->snmp_host, $this->snmp_community, $this->ifType_oid . "." . $index, 1000000, 2);
                    $adminStatus = @snmp2_get($this->snmp_host, $this->snmp_community, $this->ifAdminStatus_oid . "." . $index, 1000000, 2);
                    $operStatus = @snmp2_get($this->snmp_host, $this->snmp_community, $this->ifOperStatus_oid . "." . $index, 1000000, 2);
                    return [
                        'ifDescr' => $row,
                        'ifIndex' => $index,
                        'ifMtu' => $mtu,
                        'ifType' => $type,
                        'ifAdminStatus' => $adminStatus,
                        'ifOperStatus' => $operStatus
                    ];
                } else {
                    return ['ifDescr' => $row, 'details' => 'Index not found'];
                }
            }
        }
        return false;
    }

    public function getInterfaceTrafficMbps($index)
    {
        // Use snmp2_get for 64-bit counters (HC) for speed
        $ifInOctets_oid = '1.3.6.1.2.1.31.1.1.1.6.' . $index;   // ifHCInOctets (64-bit)
        $ifOutOctets_oid = '1.3.6.1.2.1.31.1.1.1.10.' . $index; // ifHCOutOctets (64-bit)
        $ifDescr_oid = $this->ifDescr_oid . '.' . $index;

        // Get initial values
        $in1_val = @snmp2_get($this->snmp_host, $this->snmp_community, $ifInOctets_oid, 1000000, 2);
        $out1_val = @snmp2_get($this->snmp_host, $this->snmp_community, $ifOutOctets_oid, 1000000, 2);
        $descr = @snmp2_get($this->snmp_host, $this->snmp_community, $ifDescr_oid, 1000000, 2);
        //log_message('debug', 'SNMP OIDs: in=' . $ifInOctets_oid . ', out=' . $ifOutOctets_oid . ', descr=' . $ifDescr_oid);
        //log_message('debug', 'SNMP Values: in1=' . $in1_val . ', out1=' . $out1_val . ', descr=' . $descr);

        //sleep(1);
        // Get values after 1 second
        $in2_val = @snmp2_get($this->snmp_host, $this->snmp_community, $ifInOctets_oid, 1000000, 2);
        $out2_val = @snmp2_get($this->snmp_host, $this->snmp_community, $ifOutOctets_oid, 1000000, 2);
        //log_message('debug', 'SNMP Values: in2=' . $in2_val . ', out2=' . $out2_val);

        $in1 = $this->parseSnmpValue($in1_val);
        $in2 = $this->parseSnmpValue($in2_val);
        $out1 = $this->parseSnmpValue($out1_val);
        $out2 = $this->parseSnmpValue($out2_val);
        $download_bps = max(0, $in2 - $in1);
        $upload_bps = max(0, $out2 - $out1);
        // Swap labels in return array
        return [
            'download_mbps' => round(($upload_bps * 8) / 1000000, 3), // OutOctets is Download
            'upload_mbps' => round(($download_bps * 8) / 1000000, 3), // InOctets is Upload
            'in1' => $in1,
            'in2' => $in2,
            'out1' => $out1,
            'out2' => $out2,
            'ifDescr' => $descr
        ];
    }

    private function findSnmpValueByIndex($walk, $index) {
        if (!is_array($walk)) return 0;
        foreach ($walk as $oid => $val) {
            if (preg_match('/\\.' . $index . '$/', $oid)) {
                return $this->parseSnmpValue($val);
            }
        }
        return 0;
    }

    private function parseSnmpValue($val) {
        // Try to extract a number from Counter64, STRING, or any SNMP value string
        if (preg_match('/Counter64:\s*(\\d+)/', $val, $matches)) {
            return (float)$matches[1];
        }
        if (preg_match('/STRING:\s*\"?(\\d+)\"?/', $val, $matches)) {
            return (float)$matches[1];
        }
        if (preg_match('/(-?\\d+)/', $val, $matches)) {
            return (float)$matches[1];
        }
        return 0;
    }

    /**
     * Flush the SNMP cache table
     */
    public function flushSnmpCache() {
        $this->load->database();
        $this->db->truncate('tbl_snmpcache');
    }

    /**
     * Poll all active users from all SNMP-enabled NAS (Mikrotik & NetElastic), and update tbl_snmpcache
     * Returns summary array for logging/testing
     */
    public function pollActiveUsersFromAllNas()
    {
        $this->load->database();
        $nas_list = $this->db
            ->select('nasname, shortname, ports, community, type')
            ->from('nas')
            ->where('community IS NOT NULL', null, false)
            ->get()->result();

        $mikrotik_oid = '1.3.6.1.4.1.14988.1.1.2.1.1.3';
        $netelastic_oid = 'NETELASTIC-FLEXBNG-SMGR::userName';
        $insert_data = [];
        $summary = ['mikrotik' => 0, 'netelastic' => 0, 'skipped' => 0];
        foreach ($nas_list as $nas) {
            $ip = $nas->nasname;
            $community = $nas->community;
            $port = $nas->ports ? $nas->ports : 161;
            $type = (int)$nas->type;

            echo "Type : ".$type;
            if (empty($ip) || empty($community)) {
                $summary['skipped']++;
                continue;
            }
            if ($type === 0) { // Mikrotik
                // Walk for usernames and IPs
                $name_walk = @snmp2_real_walk($ip, $community, '1.3.6.1.4.1.14988.1.1.2.1.1.2', 1000000, 2);
                $ip_walk   = @snmp2_real_walk($ip, $community, '1.3.6.1.4.1.14988.1.1.2.1.1.3', 1000000, 2);
                // Walk ifDescr to map interface index to ifDescr value
                $ifdescr_walk = @snmp2_real_walk($ip, $community, '1.3.6.1.2.1.2.2.1.2', 1000000, 2);
                $ifdescr_map = [];
                if (is_array($ifdescr_walk)) {
                    foreach ($ifdescr_walk as $oid => $val) {
                        if (preg_match('/\.([0-9]+)$/', $oid, $m)) {
                            $ifdescr_map[$m[1]] = trim($val);
                        }
                    }
                }
                $usernames = [];
                $ips = [];
                if (is_array($name_walk)) {
                    foreach ($name_walk as $oid => $val) {
                        if (preg_match('/\.([0-9]+)$/', $oid, $m)) {
                            $idx = $m[1];
                            $username = trim(preg_replace('/^STRING:|^\s+|\s+$/', '', $val));
                            $usernames[$idx] = $username;
                        }
                    }
                }
                if (is_array($ip_walk)) {
                    foreach ($ip_walk as $oid => $val) {
                        if (preg_match('/\.([0-9]+)$/', $oid, $m)) {
                            $idx = $m[1];
                            $ipaddr = trim(preg_replace('/^IpAddress:|^\s+|\s+$/', '', $val));
                            $ips[$idx] = $ipaddr;
                        }
                    }
                }
                // Save to cache using correct SNMP interface index
                foreach ($usernames as $idx => $username) {
                    $ipaddr = isset($ips[$idx]) ? $ips[$idx] : '';
                    // Remove STRING: prefix, quotes, and angle brackets
                    $username_clean = trim($username);
                    $username_clean = preg_replace('/^STRING:\s*/', '', $username_clean);
                    $username_clean = trim($username_clean, '"<>');
                    // Find SNMP interface index by matching <pppoe-USERNAME> in ifDescr
                    $snmp_ifindex = null;
                    $search = '<' . $username_clean . '>';
                    foreach ($ifdescr_map as $ifidx => $ifdescr) {
                        if (strpos($ifdescr, $search) !== false) {
                            $snmp_ifindex = $ifidx;
                            break;
                        }
                    }
                    if (!empty($username_clean) || (!empty($ipaddr) && $ipaddr !== '0.0.0.0')) {
                        $insert_data[] = [
                            'username'      => $username_clean,
                            'session_index' => $snmp_ifindex ? $snmp_ifindex : null, // Use correct SNMP ifIndex
                            'ipaddress'     => $ipaddr,
                            'device_type'   => 0,
                            'nas_ip'        => $ip,
                            'last_updated'  => date('Y-m-d H:i:s')
                        ];
                        $summary['mikrotik']++;
                    }
                }
            } elseif ($type === 5) { // NetElastic
                log_message('debug', "SNMP NetElastic: polling IP=$ip, community=$community");
                $netelastic_user_oid = '1.3.6.1.4.1.54268.1.1.4.3.1.3';
                $walk = @snmp2_real_walk($ip, $community, $netelastic_user_oid, 1000000, 2);
                //log_message('debug', 'SNMP NetElastic: walk result=' . print_r($walk, true));
                if (is_array($walk)) {
                    foreach ($walk as $oid => $val) {
                        if (preg_match('/\.([0-9]+)$/', $oid, $m)) {
                            $session_index = $m[1];
                            $username = trim($val);
                            $username_clean = preg_replace('/^STRING:\s*/', '', $username);
                            $username_clean = trim($username_clean, '"<>');
                            // Skip if session_index is null or username is empty
                            if (empty($session_index) || empty($username_clean)) {
                                log_message('debug', "SNMP NetElastic: Skipping username $username_clean due to null/empty session_index (OID: $oid)");
                                continue;
                            }
                            $insert_data[] = [
                                'username'      => $username_clean,
                                'session_index' => $session_index, // Use OID index directly
                                'ipaddress'     => '',
                                'device_type'   => 5,
                                'nas_ip'        => $ip,
                                'last_updated'  => date('Y-m-d H:i:s')
                            ];
                            $summary['netelastic']++;
                        }
                    }
                }
            } else {
                $summary['skipped']++;
            }
        }

        // Upsert logic: update if exists, else insert (no transaction block)
        foreach ($insert_data as $row) {
            if (!isset($row['session_index']) || $row['session_index'] === null) {
                continue;
            }
            // Try to update first
            $this->db->where('username', $row['username']);
            $this->db->where('nas_ip', $row['nas_ip']);
            $this->db->where('device_type', $row['device_type']);
            $this->db->update('tbl_snmpcache', [
                'session_index' => $row['session_index'],
                'ipaddress'     => $row['ipaddress'],
                'last_updated'  => $row['last_updated']
            ]);
            if ($this->db->affected_rows() == 0) {
                // If not updated, insert new
            $this->db->insert('tbl_snmpcache', $row);
            }
        }
        return $summary;
    }

    /**
     * Get live traffic (Mbps) for a user by username from tbl_snmpcache and NAS
     */
    public function get_usertraffic_mbps($username) {
        $this->load->database();
        $row = $this->db->order_by('last_updated', 'DESC')->get_where('tbl_snmpcache', ['username' => $username])->row();

        if (!$row) return false;
        $nas_ip = $row->nas_ip;
        $community = '';
        // Get community from nas table (for all device types)
        $nas = $this->db->get_where('nas', ['nasname' => $nas_ip])->row();

        if ($nas) {
            $community = $nas->community;
        }
        //log_message('debug', 'SNMP get_usertraffic_mbps: using community=' . $community . ' for NAS=' . $nas_ip);
        $type = (int)$row->device_type;
        $session_index = $row->session_index;
        $ipaddress = $row->ipaddress;
        $result = [
            'username' => $username,
            'nas_ip' => $nas_ip,
            'device_type' => $type,
            'session_index' => $session_index,
            'ipaddress' => $ipaddress,
            'last_updated' => $row->last_updated
        ];
        if ($type === 0) { // Mikrotik
            // Use 64-bit counters as in getInterfaceTrafficMbps
            $ifInOctets_oid = '1.3.6.1.2.1.31.1.1.1.6.' . $session_index;   // ifHCInOctets (64-bit)
            $ifOutOctets_oid = '1.3.6.1.2.1.31.1.1.1.10.' . $session_index; // ifHCOutOctets (64-bit)
            //log_message('debug', "SNMP get_usertraffic_mbps: OIDs: in=$ifInOctets_oid, out=$ifOutOctets_oid, NAS=$nas_ip, community=$community");
            $in1_val = @snmp2_get($nas_ip, $community, $ifInOctets_oid, 1000000, 2);
            $out1_val = @snmp2_get($nas_ip, $community, $ifOutOctets_oid, 1000000, 2);
            //log_message('debug', "SNMP get_usertraffic_mbps: in1_val=$in1_val, out1_val=$out1_val");
            $in2_val = @snmp2_get($nas_ip, $community, $ifInOctets_oid, 1000000, 2);
            $out2_val = @snmp2_get($nas_ip, $community, $ifOutOctets_oid, 1000000, 2);
            //log_message('debug', "SNMP get_usertraffic_mbps: in2_val=$in2_val, out2_val=$out2_val");
            $in1 = $this->parseSnmpValue($in1_val);
            $in2 = $this->parseSnmpValue($in2_val);
            $out1 = $this->parseSnmpValue($out1_val);
            $out2 = $this->parseSnmpValue($out2_val);
            //log_message('debug', "SNMP get_usertraffic_mbps: parsed in1=$in1, in2=$in2, out1=$out1, out2=$out2");
            $download_bps = max(0, $out2 - $out1);
            $upload_bps = max(0, $in2 - $in1);
            $result['download_mbps'] = round(($download_bps * 8) / 1000000, 3); // OutOctets is Download
            $result['upload_mbps'] = round(($upload_bps * 8) / 1000000, 3);   // InOctets is Upload
            $result['raw_in'] = [$in1, $in2];
            $result['raw_out'] = [$out1, $out2];
        } else if ($type === 5) { // NetElastic
            // NetElastic: Use correct NetElastic MIB OIDs for traffic
            $ifInOctets_oid = '1.3.6.1.4.1.54268.1.1.4.3.1.41.' . $session_index;   // v4UpUnicastBytes (upload)
            $ifOutOctets_oid = '1.3.6.1.4.1.54268.1.1.4.3.1.51.' . $session_index;  // v4DownUnicastBytes (download)
            //log_message('debug', "SNMP get_usertraffic_mbps (NetElastic): OIDs: in=$ifInOctets_oid, out=$ifOutOctets_oid, NAS=$nas_ip, community=$community");
            $in1_val = @snmp2_get($nas_ip, $community, $ifInOctets_oid, 1000000, 2);
            $out1_val = @snmp2_get($nas_ip, $community, $ifOutOctets_oid, 1000000, 2);
            //log_message('debug', "SNMP get_usertraffic_mbps (NetElastic): in1_val=$in1_val, out1_val=$out1_val");
            $in2_val = @snmp2_get($nas_ip, $community, $ifInOctets_oid, 1000000, 2);
            $out2_val = @snmp2_get($nas_ip, $community, $ifOutOctets_oid, 1000000, 2);
            //log_message('debug', "SNMP get_usertraffic_mbps (NetElastic): in2_val=$in2_val, out2_val=$out2_val");
            $in1 = $this->parseSnmpValue($in1_val);
            $in2 = $this->parseSnmpValue($in2_val);
            $out1 = $this->parseSnmpValue($out1_val);
            $out2 = $this->parseSnmpValue($out2_val);
            //log_message('debug', "SNMP get_usertraffic_mbps (NetElastic): parsed in1=$in1, in2=$in2, out1=$out1, out2=$out2");
            $download_bytes = max(0, $out2 - $out1);
            $upload_bytes = max(0, $in2 - $in1);
            $result['download_mbps'] = round($download_bytes / 125000, 3); // NetElastic: bytes/125000 = Mbps
            $result['upload_mbps'] = round($upload_bytes / 125000, 3);
            $result['raw_in'] = [$in1, $in2];
            $result['raw_out'] = [$out1, $out2];
            $result['note'] = 'NetElastic SNMP traffic polling.';
        }
        return $result;
    }

    /**
     * Return current SNMP counters (download_bytes, upload_bytes) for a user (no sleep, for AJAX polling)
     */
    public function get_user_snmp_counters($username) {
        $this->load->database();
        $row = $this->db->order_by('last_updated', 'DESC')->get_where('tbl_snmpcache', ['username' => $username])->row();
        if (!$row) return false;
        $nas_ip = $row->nas_ip;
        $community = '';
        $nas = $this->db->get_where('nas', ['nasname' => $nas_ip])->row();
        if ($nas) $community = $nas->community;
        $type = (int)$row->device_type;
        $session_index = $row->session_index;
        if ($type === 0) { // Mikrotik
            $in_oid = '1.3.6.1.2.1.31.1.1.1.6.' . $session_index;
            $out_oid = '1.3.6.1.2.1.31.1.1.1.10.' . $session_index;
        } else if ($type === 5) { // NetElastic
            $in_oid = '1.3.6.1.4.1.54268.1.1.4.3.1.41.' . $session_index;
            $out_oid = '1.3.6.1.4.1.54268.1.1.4.3.1.51.' . $session_index;
        } else {
            return false;
        }
        $in_val = @snmp2_get($nas_ip, $community, $in_oid, 1000000, 2);
        $out_val = @snmp2_get($nas_ip, $community, $out_oid, 1000000, 2);
        //log_message('debug', "AJAX SNMP get_user_snmp_counters: username=$username, NAS=$nas_ip, in_oid=$in_oid, out_oid=$out_oid, in_val=$in_val, out_val=$out_val");
        return [
            'download_bytes' => $this->parseSnmpValue($out_val),
            'upload_bytes' => $this->parseSnmpValue($in_val),
            'device_type' => $type
        ];
    }

    /**
     * Get the most recent SNMP cache row for a username (exact, pppoe- prefix, or angle brackets)
     */
    public function get_snmpcache_for_user($username) {
        $this->load->database();
        // Try exact match first
        $row = $this->db->order_by('last_updated', 'DESC')
            ->get_where('tbl_snmpcache', ['username' => $username])
            ->row();
        if ($row) {
            if ((int)$row->device_type === 5) return $row; // NetElastic: only exact
            if ((int)$row->device_type === 0) return $row; // Mikrotik: try all
        }
        // For Mikrotik, try with pppoe- prefix and angle brackets
        $row = $this->db->order_by('last_updated', 'DESC')
            ->get_where('tbl_snmpcache', ['username' => 'pppoe-' . $username])
            ->row();
        if ($row && (int)$row->device_type === 0) return $row;
        $row = $this->db->order_by('last_updated', 'DESC')
            ->get_where('tbl_snmpcache', ['username' => '<' . $username . '>'])
            ->row();
        if ($row && (int)$row->device_type === 0) return $row;
        return null;
    }

    /**
     * Get PON signal info (RX/TX and lastinform) for a username from tbl_networkdevicemap
     */
    public function get_pon_signal_info($username) {
        
        // Add some debugging
        log_message('debug', 'Looking for PON signal info for username: ' . $username);
        
        $row = $this->db->get_where('tbl_networkdevicemap', ['username' => $username])->row();
        
        if (!$row) {
            log_message('debug', 'No record found in tbl_networkdevicemap for username: ' . $username);
            return null;
        }
        
        log_message('debug', 'Found record for username: ' . $username . ', para1: ' . $row->para1 . ', para2: ' . $row->para2 . ', lastinform: ' . $row->lastInform);
        
        return [
            'rx' => $row->para1,
            'tx' => $row->para2,
            'lastinform' => $row->lastInform
        ];
    }

    // List all SNMP cache entries, with optional search, device type, nas IP, and pagination
    public function getSnmpCacheList($search = null, $deviceType = null, $nasIp = null, $limit = 20, $offset = 0) {
        $this->load->database();
        $managername = $this->session->userdata ( 'name' );
        $this->db->select('tbl_snmpcache.*, nas.shortname as nas_shortname');
        $this->db->from('tbl_snmpcache');
        $this->db->join('nas', 'tbl_snmpcache.nas_ip = nas.nasname', 'left');
        if ($search) {
            $this->db->group_start();
            $this->db->like('tbl_snmpcache.username', $search);
            $this->db->or_like('tbl_snmpcache.ipaddress', $search);
            $this->db->or_like('tbl_snmpcache.nas_ip', $search);
            $this->db->or_like('nas.shortname', $search);
            $this->db->group_end();
        }
        if ($deviceType !== null && $deviceType !== '') {
            $this->db->where('tbl_snmpcache.device_type', $deviceType);
        }
        if ($nasIp !== null && $nasIp !== '') {
            $this->db->where('tbl_snmpcache.nas_ip', $nasIp);
        }

        if ($managername <> 'admin') {
            $this->db->where('(
                (REPLACE(tbl_snmpcache.username, "pppoe-", "") IN 
                (SELECT username FROM rm_users WHERE owner = "'.$managername.'"))                
            )', null, false);
        }

        $this->db->order_by('tbl_snmpcache.last_updated', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        return $query->result();
    }

    // Get total count for pagination with same filters
    public function getSnmpCacheListCount($search = null, $deviceType = null, $nasIp = null) {
        $managername = $this->session->userdata ( 'name' );
        $this->load->database();
        $this->db->from('tbl_snmpcache');
        $this->db->join('nas', 'tbl_snmpcache.nas_ip = nas.nasname', 'left');
        if ($search) {
            $this->db->group_start();
            $this->db->like('tbl_snmpcache.username', $search);
            $this->db->or_like('tbl_snmpcache.ipaddress', $search);
            $this->db->or_like('tbl_snmpcache.nas_ip', $search);
            $this->db->or_like('nas.shortname', $search);
            $this->db->group_end();
        }
        if ($deviceType !== null && $deviceType !== '') {
            $this->db->where('tbl_snmpcache.device_type', $deviceType);
        }
        if ($nasIp !== null && $nasIp !== '') {
            $this->db->where('tbl_snmpcache.nas_ip', $nasIp);
        }

        if ($managername <> 'admin') {
            $this->db->where('(
                (REPLACE(tbl_snmpcache.username, "pppoe-", "") IN 
                (SELECT username FROM rm_users WHERE owner = "'.$managername.'"))                
            )', null, false);
        }
        
        return $this->db->count_all_results();
    }

    /**
     * Cleanup SNMP cache records older than 7 days
     */
    public function cleanupOldSnmpCache()
    {
        $this->load->database();
        $threshold = date('Y-m-d H:i:s', strtotime('-7 days'));
        $this->db->where('last_updated <', $threshold);
        $this->db->delete('tbl_snmpcache');
    }
} 
<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Wireguard_model extends CI_Model
{
    /**
     * Get all Wireguard servers from network segments (only from tbl_networksegment)
     */
    public function getWireguardServers()
    {
        $this->db->select('*');
        $this->db->from('tbl_networksegment');
        $this->db->where('segmenttype', 5); // Wireguard server type
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get server details by segment ID
     */
    public function getServerDetails($segmentId)
    {
        $this->db->select('*');
        $this->db->from('tbl_networksegmentdetails');
        $this->db->where('segmentid', $segmentId);
        $this->db->where('parametertype', 11);
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Check if server has all required parameters
     */
    public function validateServerParameters($segmentId)
    {
        $required_params = [
            'loginip' => ['type' => 2, 'value' => 'ssh'],
            'loginport' => ['type' => 4, 'value' => 'ssh'],
            'loginuser' => ['type' => 5, 'value' => 'ssh'],
            'loginpassword' => ['type' => 6, 'value' => 'ssh'],
            'servername' => ['type' => 11, 'value' => 'address'],
            'serverpublickey' => ['type' => 11, 'value' => 'publickey'],
            'serverport' => ['type' => 11, 'value' => 'port'],
            'ipv4' => ['type' => 11, 'value' => 'ipv4']
        ];

        $missing_params = [];
        foreach ($required_params as $param => $config) {
            $this->db->where('segmentid', $segmentId);
            $this->db->where('parametertype', $config['type']);
            $this->db->where('paravalue1', $config['value']);
            $query = $this->db->get('tbl_networksegmentdetails');
            
            if ($query->num_rows() == 0) {
                $missing_params[] = $param;
            }
        }

        return $missing_params;
    }

    /**
     * Get available IP addresses from pool
     */
    public function getAvailableIPs($segmentId)
    {
        $this->db->select('paravalue2');
        $this->db->from('tbl_networksegmentdetails');
        $this->db->where('segmentid', $segmentId);
        $this->db->where('parametertype', 11);
        $this->db->where('paravalue1', 'ipv4');
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $ip_pool = $query->row()->paravalue2;
            // TODO: Implement IP pool management logic
            return $ip_pool;
        }
        return null;
    }

    /**
     * Get the next available IP from the pool (highest first, no duplicates, per server/subnet)
     */
    public function getNextAvailableIP($segmentId)
    {
        // Get the pool from server details
        $this->db->select('paravalue2');
        $this->db->from('tbl_networksegmentdetails');
        $this->db->where('segmentid', $segmentId);
        $this->db->where('parametertype', 11);
        $this->db->where('paravalue1', 'ipv4');
        $query = $this->db->get();
        if ($query->num_rows() == 0) return null;

        $pool = $query->row()->paravalue2; // e.g. 10.1.0.1/24 or 10.1.0.0/24

        // Parse subnet
        list($subnet, $mask) = explode('/', $pool);
        $subnet_long = ip2long($subnet);
        $host_bits = 32 - (int)$mask;
        $num_ips = pow(2, $host_bits);

        // Get all assigned IPs (for all users)
        $this->db->select('ipaddress');
        $this->db->from('tbl_wireguardusers');
        $assigned = $this->db->get()->result_array();
        $assigned_ips = array_map(function($row) {
            return trim(explode('/', $row['ipaddress'])[0]);
        }, $assigned);

        // Assign from highest to lowest (skip network and broadcast)
        for ($i = $num_ips - 2; $i > 0; $i--) {
            $ip = long2ip($subnet_long + $i);
            if (!in_array($ip, $assigned_ips)) {
                return $ip . '/' . $mask;
            }
        }
        return null; // No available IP
    }

    /**
     * Add new Wireguard user
     */
    public function addWireguardUser($userData)
    {
        return $this->db->insert('tbl_wireguardusers', $userData);
    }

    /**
     * Get Wireguard user by username
     */
    public function getUserByUsername($username)
    {
        $this->db->where('username', $username);
        $query = $this->db->get('tbl_wireguardusers');
        return $query->row();
    }

    /**
     * Get all Wireguard users
     */
    public function getAllUsers($searchText = '')
    {
        $this->db->select('w.*, r.firstname, r.lastname');
        $this->db->from('tbl_wireguardusers w');
        $this->db->join('rm_users r', 'w.username = r.username', 'left');
        if (!empty($searchText)) {
            $this->db->group_start();
            $this->db->like('w.username', $searchText);
            $this->db->or_like('w.ipaddress', $searchText);
            $this->db->or_like('w.serveripaddress', $searchText);
            $this->db->group_end();
        }
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Generate Wireguard keys
     */
    public function generateWireguardKeys()
    {
        // Generate private key
        $private_key = shell_exec('wg genkey');
        $private_key = trim($private_key);

        // Generate public key from private key
        $public_key = shell_exec('echo ' . escapeshellarg($private_key) . ' | wg pubkey');
        $public_key = trim($public_key);

        return [
            'private_key' => $private_key,
            'public_key' => $public_key
        ];
    }

    /**
     * Generate random port number
     */
    public function generateRandomPort()
    {
        return rand(1024, 65535);
    }

    /**
     * Add peer to local Wireguard server (wg set, runtime only)
     */
    public function addPeerLocal($clientPublicKey, $clientIP)
    {
        $allowedIPs = strpos($clientIP, '/') !== false ? $clientIP : $clientIP . '/32';
        $cmd = "sudo wg set wg0 peer {$clientPublicKey} allowed-ips {$allowedIPs}";
        shell_exec($cmd);
        return true;
    }

    /**
     * Add peer to remote Wireguard server via SSH (wg set, runtime only)
     */
    public function addPeerRemote($clientPublicKey, $clientIP, $ssh_ip, $ssh_port, $ssh_user, $ssh_pass)
    {
        $allowedIPs = strpos($clientIP, '/') !== false ? $clientIP : $clientIP . '/32';
        $cmd = "sshpass -p '{$ssh_pass}' ssh -o StrictHostKeyChecking=no -p {$ssh_port} {$ssh_user}@{$ssh_ip} \"sudo wg set wg0 peer {$clientPublicKey} allowed-ips {$allowedIPs}\"";
        $output = shell_exec($cmd . ' 2>&1');
        if (strpos($output, 'Permission denied') !== false || strpos($output, 'No such file') !== false) {
            return false;
        }
        return true;
    }

    /**
     * Fetch SSH parameters for a server (only return if all are present and non-empty)
     */
    public function getServerSshParams($segmentId)
    {
        $params = ['ip' => null, 'port' => null, 'user' => null, 'password' => null];
        $this->db->where('segmentid', $segmentId);
        $this->db->where_in('parametertype', [2, 4, 5, 6]);
        $query = $this->db->get('tbl_networksegmentdetails');
        foreach ($query->result() as $row) {
            if ($row->parametertype == 2) $params['ip'] = $row->paravalue2;
            if ($row->parametertype == 4) $params['port'] = $row->paravalue2;
            if ($row->parametertype == 5) $params['user'] = $row->paravalue2;
            if ($row->parametertype == 6) $params['password'] = $row->paravalue2;
        }
        // Only return params if all are present and non-empty
        if ($params['ip'] && $params['port'] && $params['user'] && $params['password']) {
            return $params;
        }
        return null;
    }

    /**
     * Auto-detect and add peer (local or remote) using robust SSH param check
     */
    public function addPeerAuto($clientPublicKey, $clientIP, $segmentId)
    {
        $params = $this->getServerSshParams($segmentId);

        if ($params) {
            // All SSH params found, use remote
            return $this->addPeerRemote($clientPublicKey, $clientIP, $params['ip'], $params['port'], $params['user'], $params['password']);
        } else {
            // Use local
            return $this->addPeerLocal($clientPublicKey, $clientIP);
        }
    }
} 
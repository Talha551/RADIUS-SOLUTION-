<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Whatsapp_model extends CI_Model
{
    protected $table = 'tbl_whatsapp_sessions';
    protected $alerts_table = 'tbl_whatsapp_alerts';

    public function get_all_sessions()
    {

        if($this->session->userdata('name') <> 'admin'){
            if($this->ismaster <= 0){
                $this->db->where('managername', $this->session->userdata('name'));
            }else{
                $this->db->where('managername in (select managername from rm_managers  
                    where mastername = "'.$this->session->userdata('name').'")');
            }
        }
        return $this->db->order_by('id', 'DESC')->get($this->table)->result();
    }

    public function get_session($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function add_session($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update_session($id, $data)
    {
        $this->db->where('id', $id)->update($this->table, $data);
        return $this->db->affected_rows();
    }

    public function delete_session($id)
    {
        $this->db->where('id', $id)->delete($this->table);
        return $this->db->affected_rows();
    }

    /**
     * Get active expiration alerts from the database
     */
    public function get_active_expiration_alerts()
    {
        $this->db->from('tbl_whatsapp_alerts');
        $this->db->where('status', 1);
        $this->db->where('alert_type', 1);
        $query = $this->db->get();
        return $query->result();
    }

    // Placeholder for WAHA API sync logic
    public function sync_session_status($id)
    {
        // TODO: Call WAHA API for this session, update status/QR/pairing code
    }

    public function sync_all_sessions_status()
    {
        try {
            $sessions = $this->get_all_sessions();
            $updated_count = 0;
            
            foreach ($sessions as $session) {
                $result = $this->sync_session_status_to_db($session->session_name);
                if ($result !== false) {
                    $updated_count++;
                }
            }
            
            log_message('info', "Synced status for {$updated_count} sessions");
            return $updated_count;
        } catch (Exception $e) {
            log_message('error', 'Error syncing all sessions: ' . $e->getMessage());
            return 0;
        }
    }

    // Helper to call WAHA API
    public function call_waha_api($endpoint, $method = 'GET', $data = null)
    {
        $base_url = 'http://portal.pace-tel.com:3001/api'; // Updated to your WAHA API base URL
        $api_key = 'PaceTelecom@2025';
        $url = rtrim($base_url, '/') . '/' . ltrim($endpoint, '/');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $headers = [
            'Content-Type: application/json',
            'X-API-KEY: ' . $api_key
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            // Always send at least an empty JSON object
            $payload = $data === null ? '{}' : json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        } elseif (strtoupper($method) === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        return [
            'http_code' => $http_code,
            'response' => $response,
            'error' => $error
        ];
    }

    // Fetch QR code as binary PNG, convert to base64 for <img> display using /api/{session}/auth/qr
    public function get_qr_code($session_name = null)
    {
        $base_url = 'http://portal.pace-tel.com:3001/api/';
        $api_key = 'PaceTelecom@2025';
        $session = $session_name ?: 'default';
        $url = rtrim($base_url, '/') . '/' . urlencode($session) . '/auth/qr';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: image/png',
            'X-Api-Key: ' . $api_key
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $base64 = base64_encode($response);
            return 'data:image/png;base64,' . $base64;
        }
        return null;
    }

    // Fetch pairing code using /api/{session}/auth/request-code
    public function get_pairing_code($session_name = null)
    {
        $base_url = 'http://portal.pace-tel.com:3001/api/';
        $api_key = 'PaceTelecom@2025';
        $session = $session_name ?: 'default';
        $url = rtrim($base_url, '/') . '/' . urlencode($session) . '/auth/request-code';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'accept: application/json',
            'X-Api-Key: ' . $api_key
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);
        if (!empty($data['code'])) {
            return $data['code'];
        }
        return null;
    }

    // Fetch all sessions from WAHA API
    public function get_all_waha_sessions()
    {
        $result = $this->call_waha_api('sessions/', 'GET');
        if (in_array($result['http_code'], [200, 201])) {
            $data = json_decode($result['response'], true);
            return is_array($data) ? $data : [];
        }
        return [];
    }

    // Fetch a session by name from WAHA API
    public function get_session_by_name($name)
    {
        $result = $this->call_waha_api('sessions/' . urlencode($name), 'GET');
        if (in_array($result['http_code'], [200, 201])) {
            $data = json_decode($result['response'], true);
            return is_array($data) ? $data : null;
        }
        return null;
    }

    public function get_session_status($session_name = null)
    {
        $session = $session_name ?: 'default';
        $session_details = $this->get_session_by_name($session);

        if ($session_details && isset($session_details['status'])) {
            return $session_details['status'];
        }

        // Fallback if the primary method fails
        return 'UNKNOWN';
    }

    public function get_all_sessions_with_status()
    {
        if($this->session->userdata('name') <> 'admin'){
            if($this->ismaster <= 0){
                $this->db->where('managername', $this->session->userdata('name'));
            }else{
                $this->db->where('managername in (select managername from rm_managers  
                    where mastername = "'.$this->session->userdata('name').'")');
            }
        }
        $sessions = $this->db->order_by('id', 'DESC')->get($this->table)->result();
        $result = array();
        foreach ($sessions as $session) {
            $waha = $this->get_session_by_name($session->session_name);
            if ($waha && isset($waha['status'])) {
                $session->waha_status = $waha['status'];
            } else {
                $session->waha_status = 'STOPPED';
            }
            $result[] = $session;
        }
        return $result;
    }

    public function delete_session_by_name($session_name)
    {
        $this->db->where('session_name', $session_name)->delete($this->table);
        return $this->db->affected_rows();
    }


    /**
     * Sync session status from WAHA API to database
     */
    public function sync_session_status_to_db($session_name)
    {
        try {
            // Get current status from WAHA API
            $waha_status = $this->get_session_status($session_name);
            
            // Get current status from database
            $db_session = $this->get_session_by_name($session_name);
            
            if ($db_session) {
                $old_status = is_object($db_session) ? $db_session->status : $db_session['status'];
                $new_status = $waha_status;
                
                // Update status in database
                $this->db->where('session_name', $session_name);
                $this->db->update('tbl_whatsapp_sessions', [
                    'status' => $new_status,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                // If status changed from STARTING to CONNECTED or WORKING, update alerts
                if ($old_status === 'STARTING' && in_array($new_status, ['CONNECTED', 'WORKING'])) {
                    $this->update_alerts_for_session($session_name);
                }
                
                log_message('info', "Session {$session_name} status updated: {$old_status} -> {$new_status}");
                return $new_status;
            }
            
            return false;
        } catch (Exception $e) {
            log_message('error', 'Error syncing session status: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update alerts table when session becomes active
     */
    private function update_alerts_for_session($session_name)
    {
        try {
            // Get manager name for this session
            $session = $this->get_session_by_name($session_name);
            if (!$session) {
                return false;
            }
            
            $managername = is_object($session) ? $session->managername : $session['managername'];
            
            // Update or create alert entry for this manager
            $existing_alert = $this->db->get_where('tbl_whatsapp_alerts', [
                'managername' => $managername
            ])->row();
            
            if ($existing_alert) {
                // Update existing alert
                $this->db->where('id', $existing_alert->id);
                $this->db->update('tbl_whatsapp_alerts', [
                    'status' => 1,
                    'alert_type' => 1, // Expiration alerts
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {
                // Create new alert entry
                $this->db->insert('tbl_whatsapp_alerts', [
                    'managername' => $managername,
                    'status' => 1,
                    'alert_type' => 1, // Expiration alerts
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            log_message('info', "Alerts updated for manager: {$managername} (session: {$session_name})");
            return true;
        } catch (Exception $e) {
            log_message('error', 'Error updating alerts: ' . $e->getMessage());
            return false;
        }
    }


    // Alert-related functions
    public function get_all_alerts()
    {
        if($this->session->userdata('name') != 'admin') {
            if($this->ismaster <= 0) {
                $this->db->where('managername', $this->session->userdata('name'));
            } else {
                $this->db->where('managername in (select managername from rm_managers 
                    where mastername = "'.$this->session->userdata('name').'")');
            }
        }
        return $this->db->order_by('id', 'DESC')->get($this->alerts_table)->result();
    }

    public function get_alert($id)
    {
        if($this->session->userdata('name') != 'admin') {
            if($this->ismaster <= 0) {
                $this->db->where('managername', $this->session->userdata('name'));
            } else {
                $this->db->where('managername in (select managername from rm_managers 
                    where mastername = "'.$this->session->userdata('name').'")');
            }
        }
        return $this->db->get_where($this->alerts_table, ['id' => $id])->row();
    }

    public function add_alert($data)
    {
        $this->db->insert($this->alerts_table, $data);
        return $this->db->insert_id();
    }

    public function update_alert($id, $data)
    {
        if($this->session->userdata('name') != 'admin') {
            if($this->ismaster <= 0) {
                $this->db->where('managername', $this->session->userdata('name'));
            } else {
                $this->db->where('managername in (select managername from rm_managers 
                    where mastername = "'.$this->session->userdata('name').'")');
            }
        }
        $this->db->where('id', $id)->update($this->alerts_table, $data);
        return $this->db->affected_rows();
    }

    public function delete_alert($id)
    {
        if($this->session->userdata('name') != 'admin') {
            if($this->ismaster <= 0) {
                $this->db->where('managername', $this->session->userdata('name'));
            } else {
                $this->db->where('managername in (select managername from rm_managers 
                    where mastername = "'.$this->session->userdata('name').'")');
            }
        }
        $this->db->where('id', $id)->delete($this->alerts_table);
        return $this->db->affected_rows();
    }

    public function get_whatsapp_logs()
    {
        // Get current user's role and name
        $current_user = $this->session->userdata('name');
        $is_admin = ($current_user === 'admin');
        $is_master = ($this->ismaster > 0);

        $this->db->from('tbl_whatsapp_logs');

        if (!$is_admin) {
            if ($is_master) {
                // It's a master manager, use the chain from the session
                $manager_chain = $this->session->userdata('manager_chain');
                
                if (!empty($manager_chain) && is_array($manager_chain)) {
                    $this->db->where_in('managername', $manager_chain);
                } else {
                    // Fallback to only their own logs if chain is somehow empty
                    $this->db->where('managername', $current_user);
                }
            } else {
                // It's a regular manager, only their logs
                $this->db->where('managername', $current_user);
            }
        }
        // No where clause for admin, so they get all logs

        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function clear_all_messages()
    {
        // Get current user's role and name
        $current_user = $this->session->userdata('name');
        $is_admin = ($current_user === 'admin');
        $is_master = ($this->ismaster > 0);

        if ($is_admin) {
            // Admin can clear all messages
            $this->db->update('tbl_whatsapp_logs', ['messages' => '']);
            return $this->db->affected_rows();
        } elseif ($is_master) {
            // Master manager can clear messages from their sub-managers
            $this->db->select('tbl_whatsapp_logs.id');
            $this->db->from('tbl_whatsapp_logs');
            $this->db->join('rm_managers', 'tbl_whatsapp_logs.managername = rm_managers.managername');
            $this->db->where('rm_managers.mastername', $current_user);
            $this->db->or_where('tbl_whatsapp_logs.managername', $current_user); // Also include their own logs
            $sub_query = $this->db->get_compiled_select();
            
            $this->db->where("id IN ($sub_query)", NULL, FALSE);
            $this->db->update('tbl_whatsapp_logs', ['messages' => '']);
            return $this->db->affected_rows();
        } else {
            // Regular manager can only clear their own messages
            $this->db->where('managername', $current_user);
            $this->db->update('tbl_whatsapp_logs', ['messages' => '']);
            return $this->db->affected_rows();
        }
    }

    public function delete_log($id)
    {
        // Get current user's role and name
        $current_user = $this->session->userdata('name');
        $is_admin = ($current_user === 'admin');
        $is_master = ($this->ismaster > 0);

        if ($is_admin) {
            // Admin can delete any log
            $this->db->where('id', $id)->delete('tbl_whatsapp_logs');
            return $this->db->affected_rows();
        } elseif ($is_master) {
            // Master manager can delete logs from their sub-managers
            $this->db->select('tbl_whatsapp_logs.id');
            $this->db->from('tbl_whatsapp_logs');
            $this->db->join('rm_managers', 'tbl_whatsapp_logs.managername = rm_managers.managername');
            $this->db->where('tbl_whatsapp_logs.id', $id);
            $this->db->group_start();
            $this->db->where('rm_managers.mastername', $current_user);
            $this->db->or_where('tbl_whatsapp_logs.managername', $current_user);
            $this->db->group_end();
            $sub_query = $this->db->get_compiled_select();
            
            $this->db->where("id IN ($sub_query)", NULL, FALSE);
            $this->db->delete('tbl_whatsapp_logs');
            return $this->db->affected_rows();
        } else {
            // Regular manager can only delete their own logs
            $this->db->where('id', $id);
            $this->db->where('managername', $current_user);
            $this->db->delete('tbl_whatsapp_logs');
            return $this->db->affected_rows();
        }
    }

    public function mark_messages_as_read($id)
    {
        // Get current user's role and name
        $current_user = $this->session->userdata('name');
        $is_admin = ($current_user === 'admin');
        $is_master = ($this->ismaster > 0);

        // First get the mobile number for this log
        $log = $this->db->get_where('tbl_whatsapp_logs', ['id' => $id])->row();
        if ($log) {
            $mobile_number = $log->mobile;
            
            if ($is_admin) {
                // Admin can clear messages for any mobile number
                $this->db->where('mobile', $mobile_number)->update('tbl_whatsapp_logs', ['messages' => '']);
                return $this->db->affected_rows();
            } elseif ($is_master) {
                // Master manager can clear messages for mobile numbers in their sub-managers' logs
                $this->db->select('tbl_whatsapp_logs.id');
                $this->db->from('tbl_whatsapp_logs');
                $this->db->join('rm_managers', 'tbl_whatsapp_logs.managername = rm_managers.managername');
                $this->db->where('tbl_whatsapp_logs.mobile', $mobile_number);
                $this->db->group_start();
                $this->db->where('rm_managers.mastername', $current_user);
                $this->db->or_where('tbl_whatsapp_logs.managername', $current_user);
                $this->db->group_end();
                $sub_query = $this->db->get_compiled_select();
                
                $this->db->where("id IN ($sub_query)", NULL, FALSE);
                $this->db->update('tbl_whatsapp_logs', ['messages' => '']);
                return $this->db->affected_rows();
            } else {
                // Regular manager can only clear messages for mobile numbers in their own logs
                $this->db->where('mobile', $mobile_number);
                $this->db->where('managername', $current_user);
                $this->db->update('tbl_whatsapp_logs', ['messages' => '']);
                return $this->db->affected_rows();
            }
        }
        return 0;
    }

    public function get_manager_mobile($managername)
    {
        return $this->db->select('mobile')
                        ->from('rm_managers')
                        ->where('managername', $managername)
                        ->get()
                        ->row()
                        ->mobile;
    }

    public function get_manager_sessions($managername)
    {
        return $this->db->where('managername', $managername)
                        ->get($this->table)
                        ->result();
    }

    /**
     * Finds a user in rm_users by matching a normalized phone number.
     * This function now normalizes numbers to the last 10 digits for a more robust match.
     * e.g., '03349900957', '+923349900957', and '923349900957' will all match.
     *
     * @param string $mobile The mobile number to search for.
     * @return object|null The user record if found, otherwise null.
     */
    public function find_user_by_mobile($mobile)
    {
        // 1. Normalize the incoming number to its last 10 digits.
        $search_suffix = substr(preg_replace('/[^\d]/', '', $mobile), -10);

        // Don't search if the number is invalid.
        if (empty($search_suffix) || strlen($search_suffix) < 10) {
            return null;
        }

        // 2. Build a query that normalizes the 'mobile' column in the same way.
        $this->db->from('rm_users');
        // The SQL function RIGHT(column, 10) is used for efficiency.
        // We strip common non-digit characters before getting the last 10 digits.
        $this->db->where("RIGHT(REPLACE(REPLACE(REPLACE(mobile, '+', ''), '-', ''), ' ', ''), 10) =", $search_suffix);
        
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Finds users in rm_users by matching a normalized phone number.
     * This function now normalizes numbers to the last 10 digits for a more robust match
     * and returns all matching users.
     * e.g., '03349900957', '+923349900957', and '923349900957' will all match.
     *
     * @param string $mobile The mobile number to search for.
     * @return array An array of user objects if found, otherwise an empty array.
     */
    public function find_users_by_mobile($mobile)
    {
        log_message('debug', 'find_users_by_mobile - Searching for mobile: ' . $mobile);
        
        $this->db->select('username, owner');
        $this->db->from('rm_users');
        $this->db->where('mobile', $mobile);
        
        $query = $this->db->get();
        $result = $query->result_array();
        
        log_message('debug', 'find_users_by_mobile - SQL Query: ' . $this->db->last_query());
        log_message('debug', 'find_users_by_mobile - Found users: ' . count($result));
        
        if (!empty($result)) {
            foreach ($result as $user) {
                log_message('debug', 'find_users_by_mobile - User found: ' . $user['username'] . ' (Owner: ' . $user['owner'] . ')');
            }
        }
        
        return $result; // Return all results
    }

    /**
     * Extract mobile number from WAHA payload
     */
    private function extract_mobile_from_payload($payload)
    {
        if (isset($payload['payload']['from'])) {
            return preg_replace('/@c\.us$/', '', $payload['payload']['from']);
        }
        return '';
    }

    /**
     * Logs an incoming message from the WAHA webhook.
     * Handles creating a new log or updating an existing one for all users matching the mobile number.
     * If a user was previously 'unknown' and is now found, it updates the log with the correct user details.
     * @param array $payload The webhook payload from WAHA.
     * @return array An array of log entry IDs that were created or updated.
     */
    public function log_incoming_waha_message($payload)
    {
        try {
            // Extract message details
            $mobile = $this->extract_mobile_from_payload($payload);
            $session_id = isset($payload['session']) ? $payload['session'] : 'unknown';
            $message_body = isset($payload['payload']['body']) ? trim($payload['payload']['body']) : '';
            
            log_message('debug', 'Model processing - Mobile: ' . $mobile);
            log_message('debug', 'Model processing - Session: ' . $session_id);
            log_message('debug', 'Model processing - Message body: "' . $message_body . '"');
            
            // Log if this is a media message being ignored
            if (isset($payload['payload']['hasMedia']) && $payload['payload']['hasMedia'] === true) {
                $media_type = isset($payload['payload']['media']['mimetype']) ? $payload['payload']['media']['mimetype'] : 'unknown';
                log_message('info', "Media message ignored from mobile: {$mobile}, type: {$media_type}, session: {$session_id}");
                return []; // Return empty array for media messages
            }
            
            // Ensure we have a valid text message
            if (empty($message_body)) {
                log_message('info', "Empty message body ignored from mobile: {$mobile}, session: {$session_id}");
                return []; // Return empty array for empty messages
            }
            
            log_message('info', "Processing text message from mobile: {$mobile}, session: {$session_id}, message: " . substr($message_body, 0, 100) . "...");
            
            // Find all users associated with this mobile number
            $users = $this->find_users_by_mobile($mobile);
            
            log_message('debug', 'Model processing - Users found: ' . count($users));
            
            if (empty($users)) {
                log_message('warning', "No users found for mobile: {$mobile}");
                return [];
            }
            
            $log_ids = [];
            
            // Process each user associated with this mobile number
            foreach ($users as $user) {
                $log_id = $this->_create_or_update_log($mobile, $session_id, $user['username'], $user['owner'], $message_body);
                if ($log_id) {
                    $log_ids[] = $log_id;
                }
            }
            
            log_message('debug', 'Model processing - Log IDs created: ' . count($log_ids));
            return $log_ids;
            
        } catch (Exception $e) {
            log_message('error', 'Whatsapp_model::log_incoming_waha_message() - Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Helper function to create or update a single log entry.
     * Checks for an existing log based on mobile and username.
     *
     * @param string $mobile
     * @param string $session_id
     * @param string $username
     * @param string $managername
     * @param string $message_body
     * @return int The ID of the log entry.
     */
    private function _create_or_update_log($mobile, $session_id, $username, $managername, $message_body)
    {
        log_message('debug', '_create_or_update_log - Starting with mobile: ' . $mobile . ', username: ' . $username . ', manager: ' . $managername);
        
        // Check if a log entry already exists for this mobile and username combination
        $existing_log = $this->db->get_where('tbl_whatsapp_logs', [
            'mobile' => $mobile,
            'username' => $username
        ])->row();
        
        log_message('debug', '_create_or_update_log - Existing log found: ' . ($existing_log ? 'YES' : 'NO'));

        if ($existing_log) {
            // Update existing log
            $updated_messages = $existing_log->messages . "\n--------------------\n" . $message_body;
            
            $update_data = [
                'messages' => $updated_messages,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->where('id', $existing_log->id);
            $this->db->update('tbl_whatsapp_logs', $update_data);
            
            log_message('debug', '_create_or_update_log - Updated log ID: ' . $existing_log->id);
            log_message('debug', '_create_or_update_log - Update SQL: ' . $this->db->last_query());
            
            return $existing_log->id;
        } else {
            // Create new log entry
            $insert_data = [
                'mobile' => $mobile,
                'username' => $username,
                'managername' => $managername,
                'waha_session_id' => $session_id,
                'messages' => $message_body,
                'status' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert('tbl_whatsapp_logs', $insert_data);
            $new_log_id = $this->db->insert_id();
            
            log_message('debug', '_create_or_update_log - Created new log ID: ' . $new_log_id);
            log_message('debug', '_create_or_update_log - Insert SQL: ' . $this->db->last_query());
            
            return $new_log_id;
        }
    }

    /**
     * Get users from WhatsApp logs who are expiring within 3 days
     * Only includes users whose managers have active WhatsApp instances
     */
    public function get_registered_expiring_users($default_days = 5)
    {

        $managername = $this->session->userdata('name');

        // Calculate date 3 days from now
        $days_from_now = date('Y-m-d', strtotime('+'.$default_days.' days'));
        
        $this->db->select('
            wl.mobile,
            wl.username,
            wl.username as userId,
            ru.firstName,
            ru.lastName,
            ru.owner as managername,
            tud.payid,
            ru.expiration,
            ws.session_name as instance_id,
            wa.interval,
            wa.duration,
            wa.frequency
        ');

        $this->db->from('tbl_whatsapp_logs wl');
        $this->db->join('rm_users ru', 'wl.username = ru.username', 'inner');
        $this->db->join('tbl_userdocs tud', 'wl.username = tud.username', 'inner');
        $this->db->join('tbl_whatsapp_sessions ws', 'ru.owner = ws.managername', 'inner');
        $this->db->join('tbl_whatsapp_alerts wa', 'wl.managername = wa.managername', 'inner');

        $this->db->where('ru.expiration <=', $days_from_now);
        $this->db->where('ru.expiration >=', date('Y-m-d')); // Not expired yet
        $this->db->where('ws.status', 'STARTING'); // Only active instances
        $this->db->where('wl.mobile IS NOT NULL');
        $this->db->where('wl.mobile !=', '');
        $this->db->where('wa.status', 1);
        $this->db->where('wa.alert_type', 1);

        $manager_chain = $this->session->userdata('manager_chain');
        if (!empty($manager_chain)) {
            $this->db->where_in('ru.owner', $manager_chain);
        }
        else{
            $this->db->where('ru.owner', $this->session->userdata('name'));
        }


        $this->db->group_by('wl.username'); // Avoid duplicates
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_all_expiring_users($managername = null, $default_days = 3)
    {

        if($default_days >= 0 ){
            $days_to = date('Y-m-d', strtotime('+'.$default_days.' days'));
            $days_from = date('Y-m-d');
        }else{
            $days_from = date('Y-m-d', strtotime($default_days.' days'));
            $days_to = date('Y-m-d');
        }
        
        // Calculate date 3 days from now
        //$days_from_now = date('Y-m-d', strtotime('+'.$default_days.' days'));
        
        $this->db->select('
            ru.mobile,
            ru.username,
            ru.username as userId,
            ru.firstName,
            ru.lastName,
            ru.owner as managername,
            tud.payid,
            ru.expiration,
            ws.session_name as instance_id
        ');

        $this->db->from('rm_users ru');
        $this->db->join('tbl_userdocs tud', 'ru.username = tud.username', 'inner');
        $this->db->join('tbl_whatsapp_sessions ws', 'ru.owner = ws.managername', 'inner');

        $this->db->where('ru.expiration <=', $days_to);
        $this->db->where('ru.expiration >=', $days_from);
        //$this->db->where_in('ws.status', ['CONNECTED', 'WORKING']); // Only active instances
        $this->db->where('ru.mobile IS NOT NULL');
        $this->db->where('ru.mobile !=', '');

        if ($managername) {
            $this->db->group_start();
            $this->db->where('ru.owner', $managername);
            $this->db->or_where('ru.owner IN (SELECT managername FROM rm_managers WHERE mastername = "'.$managername.'")', NULL, FALSE);
            $this->db->group_end();
        }

         
        $this->db->group_by('ru.username'); // Avoid duplicates
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_all_expiring_users_baileys($managername = null, $default_days = 3)
    {

        if($default_days >= 0 ){
            $days_to = date('Y-m-d', strtotime('+'.$default_days.' days'));
            $days_from = date('Y-m-d');
        }else{
            $days_from = date('Y-m-d', strtotime($default_days.' days'));
            $days_to = date('Y-m-d');
        }
        
        // Calculate date 3 days from now
        //$days_from_now = date('Y-m-d', strtotime('+'.$default_days.' days'));
        
        $this->db->select('
            ru.mobile,
            ru.username,
            ru.username as userId,
            ru.firstName,
            ru.lastName,
            ru.owner as managername,
            tud.payid,
            ru.expiration,
            ru.owner as instance_id
        ');

        $this->db->from('rm_users ru');
        $this->db->join('tbl_userdocs tud', 'ru.username = tud.username', 'inner');
        //$this->db->join('tbl_whatsapp_sessions ws', 'ru.owner = ws.managername', 'inner');

        $this->db->where('ru.expiration <=', $days_to);
        $this->db->where('ru.expiration >=', $days_from);
        //$this->db->where_in('ws.status', ['CONNECTED', 'WORKING']); // Only active instances
        $this->db->where('ru.mobile IS NOT NULL');
        $this->db->where('ru.mobile !=', '');

        if ($managername) {
            $this->db->group_start();
            $this->db->where('ru.owner', $managername);
            $this->db->or_where('ru.owner IN (SELECT managername FROM rm_managers WHERE mastername = "'.$managername.'")', NULL, FALSE);
            $this->db->group_end();
        }

         
        $this->db->group_by('ru.username'); // Avoid duplicates
        
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_alert_by_id($alert_id)
    {
        $this->db->from('tbl_whatsapp_alerts');
        $this->db->where('id', $alert_id); // Assuming 'alert_id' is the primary key
        $query = $this->db->get();
        return $query->row();
    }


    public function get_newcreated_users($managername = null, $default_days = 1)
    {

        if($default_days >= 0 ){
            $days_to = date('Y-m-d', strtotime('+'.$default_days.' days'));
            $days_from = date('Y-m-d');
        }else{
            $days_from = date('Y-m-d', strtotime($default_days.' days'));
            $days_to = date('Y-m-d');
        }
        
        // Calculate date 3 days from now
        //$days_from_now = date('Y-m-d', strtotime('+'.$default_days.' days'));
        
        $this->db->select('
            ru.mobile,
            ru.username,
            ru.username as userId,
            ru.firstName,
            ru.lastName,
            ru.owner as managername,
            tud.payid,
            ru.expiration,
            ru.createdon,
            ws.session_name as instance_id
        ');

        $this->db->from('rm_users ru');
        $this->db->join('tbl_userdocs tud', 'ru.username = tud.username', 'inner');
        $this->db->join('tbl_whatsapp_sessions ws', 'ru.owner = ws.managername', 'inner');

        $this->db->where('ru.createdon <=', $days_to);
        $this->db->where('ru.createdon >=', $days_from);
        $this->db->where_in('ws.status', ['CONNECTED', 'WORKING']); // Only active instances
        $this->db->where('ru.mobile IS NOT NULL');
        $this->db->where('ru.mobile !=', '');

        if ($managername) {
            $this->db->group_start();
            $this->db->where('ru.owner', $managername);
            $this->db->or_where('ru.owner IN (SELECT managername FROM rm_managers WHERE mastername = "'.$managername.'")', NULL, FALSE);
            $this->db->group_end();
        }

         
        $this->db->group_by('ru.username'); // Avoid duplicates
        
        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * Baileys FastAPI config (no DB persistence for sessions).
     *
     * @return array{base_url: string, api_key: string}
     */
    public function baileys_get_config()
    {
        $this->load->config('baileys', true);
        $base = $this->config->item('baileys_api_base_url', 'baileys');
        $key = $this->config->item('baileys_api_key', 'baileys');
        return [
            'base_url' => rtrim((string) $base, '/'),
            'api_key' => (string) $key,
        ];
    }

    /**
     * Low-level Baileys HTTP call.
     *
     * @param string $path e.g. 'sessions' or 'sessions/foo/status'
     * @param string $method GET|POST|DELETE
     * @param mixed|null $json_body Encoded as JSON for POST
     * @param bool $binary_response If true, body is raw bytes (e.g. PNG)
     * @return array{http_code:int, body:string, json:mixed|null, error:string, curl_error:string}
     */
    public function baileys_request($path, $method = 'GET', $json_body = null, $binary_response = false)
    {
        $cfg = $this->baileys_get_config();
        $url = $cfg['base_url'] . '/' . ltrim($path, '/');
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $headers = [];
        if ($cfg['api_key'] !== '') {
            $headers[] = 'X-API-Key: ' . $cfg['api_key'];
        }
        if (!$binary_response) {
            $headers[] = 'Accept: application/json';
            if ($method === 'POST' || $method === 'PUT' || $method === 'PATCH') {
                $headers[] = 'Content-Type: application/json';
            }
        } else {
            $headers[] = 'Accept: image/png,*/*';
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $m = strtoupper($method);
        if ($m === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json_body === null ? '{}' : json_encode($json_body));
        } elseif ($m === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        } elseif ($m !== 'GET') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $m);
            if ($json_body !== null) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json_body));
            }
        }

        $body = curl_exec($ch);
        $http_code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err = curl_error($ch);
        curl_close($ch);

        $decoded = null;
        if (!$binary_response && $body !== false && $body !== '') {
            $decoded = json_decode($body, true);
        }

        return [
            'http_code' => $http_code,
            'body' => $body === false ? '' : $body,
            'json' => $decoded,
            'error' => $curl_err,
            'curl_error' => $curl_err,
        ];
    }

    public function baileys_list_sessions()
    {
        return $this->baileys_request('sessions', 'GET');
    }

    public function baileys_create_session($session_id)
    {
        return $this->baileys_request('sessions', 'POST', ['session_id' => $session_id]);
    }

    public function baileys_session_status($session_id)
    {
        return $this->baileys_request('sessions/' . rawurlencode($session_id) . '/status', 'GET');
    }

    public function baileys_session_qr_json($session_id)
    {
        return $this->baileys_request('sessions/' . rawurlencode($session_id) . '/qr', 'GET');
    }

    public function baileys_session_qr_png($session_id)
    {
        return $this->baileys_request('sessions/' . rawurlencode($session_id) . '/qr.png', 'GET', null, true);
    }

    public function baileys_delete_session($session_id)
    {
        return $this->baileys_request('sessions/' . rawurlencode($session_id), 'DELETE');
    }

    /**
     * POST /sessions/{id}/send — optional typing simulation for Baileys API.
     *
     * @param array $options Keys: typing (bool), typing_ms (int milliseconds, typically sent with typing true)
     */
    public function baileys_send_text($session_id, $to, $text, array $options = [])
    {
        $payload = ['to' => $to, 'text' => $text];
        if (!empty($options['typing'])) {
            $payload['typing'] = true;
        }
        if (isset($options['typing_ms'])) {
            $ms = (int) $options['typing_ms'];
            if ($ms > 0) {
                $this->load->config('baileys', true);
                $cap = (int) $this->config->item('baileys_typing_ms_max', 'baileys');
                if ($cap < 1) {
                    $cap = 10000;
                }
                $payload['typing_ms'] = max(1, min($cap, $ms));
            }
        }
        return $this->baileys_request(
            'sessions/' . rawurlencode($session_id) . '/send',
            'POST',
            $payload
        );
    }

    public function baileys_poll_messages($session_id, $limit = 50, $clear = false)
    {
        $limit = max(1, min(200, (int) $limit));
        $clear_q = $clear ? 'true' : 'false';
        $q = 'sessions/' . rawurlencode($session_id) . '/messages?limit=' . $limit . '&clear=' . $clear_q;
        return $this->baileys_request($q, 'GET');
    }

    public function baileys_health()
    {
        return $this->baileys_request('health', 'GET');
    }

} 
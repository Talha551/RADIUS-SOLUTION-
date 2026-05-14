<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ImportUsers_model extends CI_Model
{
    public function getServicesByManager($managername)
    {
        $this->db->select('radsrvid, srvname');
        $this->db->from('tbl_services');
        $this->db->where('managername', $managername);
        $query = $this->db->get();
        return $query->result();
    }

    public function logImport($managername, $filename)
    {
        $data = [
            'managername' => $managername,
            'import_type' => 1, // 1 = users
            'importfile' => $filename,
            'status' => 0
        ];
        $this->db->insert('tbl_import_logs', $data);
        return $this->db->insert_id();
    }

    public function updateImportLog($import_id, $status)
    {
        $this->db->where('id', $import_id);
        $this->db->update('tbl_import_logs', ['status' => $status]);
    }

    public function validateCSV($filePath, $owner, $import_id)
    {

        $this->load->model('Invoices_model');
        $this->load->model('users_model');
        $this->load->model('Services_model');

        $handle = fopen($filePath, 'r');
        if (!$handle) {
            return ['status' => 0, 'error_message' => 'Could not open file.'];
        }
        
        $header = fgetcsv($handle);
        $required = ['username','password','firstname','lastname','address','mobile','email','taxid','expiration','service_id'];
        $success_count = 0;
        $error_message = '';
        $row_num = 1;

        $rows_to_process = [];
        $usernames_in_csv = [];

        // PASS 1: VALIDATION
        while (($row = fgetcsv($handle)) !== false) {
            $row_num++;
            
            // Skip empty rows (like a blank line at the end of the file)
            $is_empty_row = true;
            foreach ($row as $val) {
                if (trim((string)$val) !== '') {
                    $is_empty_row = false;
                    break;
                }
            }
            if ($is_empty_row) {
                continue;
            }

            if (count($header) !== count($row)) {
                $error_message .= "Row $row_num: Invalid column count. ";
                continue;
            }
            $data = array_combine($header, $row);
            
            // Extract fields
            $username = isset($data['username']) ? trim($data['username']) : '';
            $password = isset($data['password']) ? trim($data['password']) : '';
            $firstname = isset($data['firstname']) ? trim($data['firstname']) : '';
            $lastname = isset($data['lastname']) ? trim($data['lastname']) : '';
            $address = isset($data['address']) ? trim($data['address']) : '';
            $mobile = isset($data['mobile']) ? trim($data['mobile']) : '';
            $email = isset($data['email']) ? trim($data['email']) : '';
            $taxid = isset($data['taxid']) ? trim($data['taxid']) : '';
            $expiration = isset($data['expiration']) ? trim($data['expiration']) : '';
            $srvid = isset($data['service_id']) ? trim($data['service_id']) : '';
            
            // Clean mobile and taxid from Excel hack (e.g. ="923...")
            $mobile = str_replace(['=', '"'], '', $mobile);
            $taxid = str_replace(['=', '"'], '', $taxid);

            // Required fields
            if (!$username || !$password || !$firstname || !$lastname || !$mobile || !$srvid) {
                $error_message .= "Row $row_num: Missing required fields. ";
                continue;
            }
            // Username validation
            if (preg_match('/\s/', $username) || strlen($username) < 4) {
                $error_message .= "Row $row_num: Invalid username. ";
                continue;
            }
            // Password validation
            if (strlen($password) < 3 || preg_match('/\s/', $password)) {
                $error_message .= "Row $row_num: Invalid password. ";
                continue;
            }
            // Mobile validation (Pakistan format)
            if (!preg_match('/^(0\d{10}|92\d{10})$/', $mobile)) {
                $error_message .= "Row $row_num: Invalid mobile format. ";
                continue;
            }
            // Email validation (optional)
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_message .= "Row $row_num: Invalid email. ";
                continue;
            }

            // Expiration parsing (any format -> Y-m-d)
            if (!$expiration) {
                $expiration = date('Y-m-d');
            } else {
                $timestamp = strtotime($expiration);
                if ($timestamp === false) {
                    $timestamp = strtotime(str_replace('/', '-', $expiration));
                }
                if ($timestamp === false) {
                    $error_message .= "Row $row_num: Invalid expiration date. ";
                    continue;
                }
                $expiration = date('Y-m-d', $timestamp);
            }

            // Check duplicate username in this CSV
            if (in_array($username, $usernames_in_csv)) {
                $error_message .= "Row $row_num: Duplicate username in CSV. ";
                continue;
            }

            // Check username uniqueness in DB
            $exists = $this->db->get_where('rm_users', ['username' => $username])->row();
            if ($exists) {
                $error_message .= "Row $row_num: Username already exists. ";
                continue;
            }

            // Check if service_id exists
            $radsrvid = $this->Services_model->getServiceProfileInfo($srvid);
            if (!$radsrvid) {
                $error_message .= "Row $row_num: Invalid service ID. ";
                continue;
            }

            // Check if service plan price exists for this manager
            $serviceplan_info = $this->Invoices_model->getPackagePrice($owner, $srvid);
            if (!$serviceplan_info) {
                $error_message .= "Row $row_num: Service ID $srvid not assigned to this manager or has no price setup. ";
                continue;
            }

            $usernames_in_csv[] = $username;
            
            $rows_to_process[] = [
                'username' => $username,
                'password' => $password,
                'firstname' => $firstname,
                'lastname' => $lastname,
                'address' => $address,
                'mobile' => $mobile,
                'email' => $email,
                'taxid' => $taxid,
                'expiration' => $expiration,
                'srvid' => $srvid,
                'radsrvname' => $radsrvid->srvname
            ];
        }

        fclose($handle);
        $status = (empty($error_message) && !empty($rows_to_process)) ? 1 : 0;
        return [
            'status' => $status,
            'rows_to_process' => $rows_to_process,
            'error_message' => rtrim($error_message)
        ];
    }

    public function importSingleUser($pdata, $owner)
    {
        $this->load->model('Invoices_model');
        $this->load->model('users_model');
        $this->load->model('Services_model');

        $username = $pdata['username'];
        $password = $pdata['password'];
        $firstname = $pdata['firstname'];
        $lastname = $pdata['lastname'];
        $address = $pdata['address'];
        $mobile = $pdata['mobile'];
        $email = $pdata['email'];
        $taxid = $pdata['taxid'];
        $expiration = $pdata['expiration'];
        $srvid = $pdata['srvid'];
        $radsrvname = $pdata['radsrvname'];

        // Double check username exists just in case
        $exists = $this->db->get_where('rm_users', ['username' => $username])->row();
        if ($exists) {
            return ['success' => false, 'message' => "Username $username already exists."];
        }

        // Insert user
        $userInfo = [
            'username' => $username,
            'password' => md5($password),
            'groupid' => 1,
            'enableuser' => 1,
            'uplimit' => 0,
            'downlimit' => 0,
            'comblimit' => 0,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'address' => $address,
            'mobile' => $mobile,
            'email' => $email,
            'taxid' => $taxid,
            'expiration' => $expiration, // Y-m-d format
            'srvid' => $srvid,
            'createdby' => $owner,
            'owner' => $owner,
            'custattr' => 'Mikrotik-Address-List := ' . $owner,
            'lang' => 'English',
            'gpslat' => 0.00000000000000,
            'gpslong' => 0.00000000000000,
            'usemacauth' => 0,
            'uptimelimit' => 0,
            'ipmodecm' => 0,
            'ipmodecpe' => 0,
            'poolidcm' => 0,
            'poolidcpe' => 0,
            'createdon' => date('Y-m-d'),
            'acctype' => 0,
            'credits' => 0.00,
            'cardfails' => 0,
            'warningsent' => 0,
            'verified' => 0,
            'selfreg' => 0,
            'verifyfails' => 0,
            'verifysentnum' => 0,
            'contractvalid' => '0000-00-00',
            'pswactsmsnum' => 0,
            'alertemail' => 0,
            'alertsms' => 0
        ];

        $radpassword = array('username'=>$username, 'attribute'=>'Cleartext-Password', 'op'=>':=', 'value'=>$password);
        $radsimuse = array('username'=>$username, 'attribute'=>'Simultaneous-Use', 'op'=>':=', 'value'=>'1');
        
        $result = $this->users_model->addNewUser($userInfo, $radpassword, $radsimuse);

        if (!$result) {
            return ['success' => false, 'message' => "Failed to add user $username to rm_users."];
        }

        $userDocsInfo = array('username'=>$username, 'segmentid'=>0, 'parameter'=>'');
        $this->users_model->updateDocsInfo($userDocsInfo, $username);       
        
        // Add or update radusergroup for this user
        $this->Services_model->manageUserRadusergroup($radsrvname, $username, 1);

        // Create an Invoice
        $serviceplan_info = $this->Invoices_model->getPackagePrice($owner, $srvid);
        if ($serviceplan_info) {
            $baseprice = $serviceplan_info->baseprice;
            $today = new DateTime(date('Y-m-d'));
            $exp = new DateTime($expiration);

            // Calculate the number of days difference
            $days = $today->diff($exp)->days;

            // Calculate per day price
            $per_day = $baseprice / 30;

            // Calculate amount
            $amount = round($per_day * $days, 0); 

            if(abs($amount) > 1){
                $invoiceInfo = array('username'=>$username,
                                'srvid'=>$serviceplan_info->srvid, 
                                'managername'=>$this->session->userdata('name'),
                                'createdBy'=>0,
                                'invtype'=>'Recharge',
                                'srvdate'=>date('Y-m-d'),
                                'expdate'=>$expiration,
                                'price'=>$serviceplan_info->baseprice,
                                'amount'=>-$amount,
                                'remarks'=>'User Import Auto Invoice');

                $this->Invoices_model->addRecharge($invoiceInfo);
            }
        }
        
        return ['success' => true, 'message' => "User $username imported successfully."];
    }

    /**
     * Get import logs with role-based access control
     * 
     * @param string $managername Current manager name
     * @param bool $isAdmin Whether user is admin
     * @param bool $isMaster Whether user is master manager
     * @param array $managerChain Array of subordinate manager names
     * @return array Array of import logs
     */
    public function getImportLogs($managername, $isAdmin = false, $isMaster = false, $managerChain = [])
    {
        $this->db->select('il.*');
        $this->db->from('tbl_import_logs il');
        $this->db->order_by('il.created_at', 'DESC');
        
        // Apply role-based filtering
        if (!$isAdmin) {
            if ($isMaster && !empty($managerChain)) {
                // Master can see logs from all subordinates
                $this->db->where_in('il.managername', $managerChain);
            } else {
                // Regular manager can only see their own logs
                $this->db->where('il.managername', $managername);
            }
        }
        // Admin can see all logs (no additional where clause needed)
        
        $query = $this->db->get();
        $results = $query->result();
        
        // Add human-readable names
        foreach ($results as $log) {
            $log->import_type_name = $this->getImportTypeName($log->import_type);
            $log->status_name = $this->getStatusName($log->status);
        }
        
        return $results;
    }

    /**
     * Get import type name
     * 
     * @param int $type Import type ID
     * @return string Import type name
     */
    private function getImportTypeName($type)
    {
        switch ($type) {
            case 1:
                return 'Users';
            case 2:
                return 'Services';
            default:
                return 'Unknown';
        }
    }

    /**
     * Get status name
     * 
     * @param int $status Status ID
     * @return string Status name
     */
    private function getStatusName($status)
    {
        switch ($status) {
            case 1:
                return 'Success';
            case 0:
                return 'Failed';
            default:
                return 'Pending';
        }
    }

    /**
     * Delete import log with role-based access control
     * 
     * @param int $logId Log ID to delete
     * @param string $managername Current manager name
     * @param bool $isAdmin Whether user is admin
     * @param bool $isMaster Whether user is master manager
     * @param array $managerChain Array of subordinate manager names
     * @return bool Success status
     */
    public function deleteImportLog($logId, $managername, $isAdmin = false, $isMaster = false, $managerChain = [])
    {
        // First check if user has permission to delete this log
        $this->db->select('managername');
        $this->db->from('tbl_import_logs');
        $this->db->where('id', $logId);
        $query = $this->db->get();
        
        if ($query->num_rows() == 0) {
            return false; // Log doesn't exist
        }
        
        $log = $query->row();
        
        // Check permissions
        if (!$isAdmin) {
            if ($isMaster && !empty($managerChain)) {
                // Master can delete logs from subordinates
                if (!in_array($log->managername, $managerChain)) {
                    return false; // Not authorized
                }
            } else {
                // Regular manager can only delete their own logs
                if ($log->managername !== $managername) {
                    return false; // Not authorized
                }
            }
        }
        
        // Delete the log
        $this->db->where('id', $logId);
        return $this->db->delete('tbl_import_logs');
    }

    /**
     * Get import log details by ID with role-based access control
     * 
     * @param int $logId Log ID
     * @param string $managername Current manager name
     * @param bool $isAdmin Whether user is admin
     * @param bool $isMaster Whether user is master manager
     * @param array $managerChain Array of subordinate manager names
     * @return object|bool Log object or false if not found/not authorized
     */
    public function getImportLogById($logId, $managername, $isAdmin = false, $isMaster = false, $managerChain = [])
    {
        $this->db->select('il.*');
        $this->db->from('tbl_import_logs il');
        $this->db->where('il.id', $logId);
        
        // Apply role-based filtering
        if (!$isAdmin) {
            if ($isMaster && !empty($managerChain)) {
                // Master can see logs from all subordinates
                $this->db->where_in('il.managername', $managerChain);
            } else {
                // Regular manager can only see their own logs
                $this->db->where('il.managername', $managername);
            }
        }
        
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $log = $query->row();
            $log->import_type_name = $this->getImportTypeName($log->import_type);
            $log->status_name = $this->getStatusName($log->status);
            return $log;
        }
        return false;
    }
} 
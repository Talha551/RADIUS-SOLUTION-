<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Reseller_model extends CI_Model
{

    // Resellers Listing Report

    function resellerListingCount($searchText = '', $managerAllServices, $filterType = 0)
    {
        $this->db->select('BaseTbl.managername, BaseTbl.mastername, BaseTbl.firstname, BaseTbl.lastname, BaseTbl.city,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('rm_managers as BaseTbl');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.managername  LIKE '%".$searchText."%'
                            OR BaseTbl.mastername  LIKE '%".$searchText."%'
                            OR BaseTbl.firstname  LIKE '%".$searchText."%'
                            OR BaseTbl.lastname  LIKE '%".$searchText."%'
                            OR  BaseTbl.city  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        if($filterType == 1) { // Master only
            $this->db->where('BaseTbl.managername in (select mastername from rm_managers)');
        } else if($filterType == 2) { // User Panel only
            $this->db->where('BaseTbl.managername not in (select mastername from rm_managers)');
        }

        $managername = $this->session->userdata ( 'name' );

        if($managerAllServices == 0)
        {
            $this->db->where('BaseTbl.mastername = ', $managername);
        }elseif($managerAllServices == 1 && $managername <> 'admin'){
            $manager_chain = $this->session->userdata('manager_chain');
            if (!empty($manager_chain)) {
                $this->db->where_in('BaseTbl.mastername', $manager_chain);
            }else{
                $this->db->where('BaseTbl.mastername = ', $managername);
            }
        }

        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function resellerListing($searchText = '', $page, $segment, $managerAllServices, $filterType = 0)
    {
        $this->db->query('Set @row_number = 0');
        $this->db->query('Set @active_users = 0');
        $curDate = date("y-m-d");
        $this->db->select('BaseTbl.managername, BaseTbl.mastername, BaseTbl.firstname, BaseTbl.lastname, BaseTbl.city,
                            Accounts.balance as balance,
                            0 as connected,
                            (@row_number:=@row_number + 1) AS serial_number');

        $managername = $this->session->userdata ( 'name' );

        $this->db->from('rm_managers as BaseTbl');
        $this->db->join('rm_users as Users', 'BaseTbl.managername = Users.owner','left');

        $manager_chain = $this->session->userdata('manager_chain');
        $manager_chain_quoted = array_map(function($v) { return "'".$this->db->escape_str($v)."'"; }, $manager_chain);
        $manager_chain_str = implode(',', $manager_chain_quoted);
        if($managername == 'admin'){ $where_clause = ''; }else{ $where_clause = ' where managername IN ('.$manager_chain_str.') '; }

        $this->db->join('(Select managername, sum(amount) as balance from tbl_invoices where managername in 
                        (select managername from rm_managers '.$where_clause.') group by managername) as Accounts',
                        'BaseTbl.managername = Accounts.managername','left');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.managername  LIKE '%".$searchText."%'
                            OR BaseTbl.mastername  LIKE '%".$searchText."%'
                            OR BaseTbl.firstname  LIKE '%".$searchText."%'
                            OR BaseTbl.lastname  LIKE '%".$searchText."%'
                            OR BaseTbl.city  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        // Add filter condition
        if($filterType == 1) { // Master only
            $this->db->where('BaseTbl.managername in (select mastername from rm_managers)');
        } else if($filterType == 2) { // User Panel only
            $this->db->where('BaseTbl.managername not in (select mastername from rm_managers)');
        }

        if($managerAllServices == 0)
        {
            $this->db->where('BaseTbl.mastername = ', $managername);
        }elseif($managerAllServices == 1 && $managername <> 'admin'){
            if (!empty($manager_chain)) {
                $this->db->where_in('BaseTbl.mastername', $manager_chain);
            }else{
                $this->db->where('BaseTbl.mastername = ', $managername);
            }
        }

        if($managername == 'admin'){
            $this->db->order_by('BaseTbl.mastername, BaseTbl.managername');
        }else{
            $this->db->order_by('BaseTbl.managername');
        }
        
        $this->db->group_by('BaseTbl.managername');

        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;
    }

    function getResellerList()
    {
        $managername = $this->session->userdata ( 'name' );

        $this->load->model('login_model');
        $manager_chain = $this->login_model->master_chain($managername);

        $this->db->select('*');
        $this->db->from('rm_managers');
        $this->db->where('managername <>', 'admin');
        if($this->ismaster > 0 && $managername <> 'admin'){
            $this->db->where_in('managername', $manager_chain);
        }elseif($managername <> 'admin'){
            $this->db->where('managername', $managername);
        }
        $query = $this->db->get();

        return $query->result();
    }

    function checkResellerExist($manager = ''){

        $this->db->select('managername');
        $this->db->from("rm_managers");
        $this->db->where('managername', $manager);
        $query = $this->db->get();

        if ($query->num_rows() > 0){
            return true;
        } else {
            return false;
        }

    }

    function checkManager_validity()
    {
        $managername = $this->session->userdata ( 'name' );
        $this->db->select('username');
        $this->db->from("rm_users");
        
        $this->db->where('owner', $managername);

        $query = $this->db->get();

        if ($query->num_rows() > 0){
            return false;
        } else {
            return true;
        }
    }

    function addNewReseller($resellerInfo, $reseller, $masterreseller)
    {
        $this->db->trans_start();
        $this->db->insert('rm_managers', $resellerInfo);

        $query1 = $this->db->query("Insert into rm_allowedmanagers select srvid,'".$reseller."' as managername from rm_allowedmanagers 
                                    where managername = '".$masterreseller."' and srvid in 
                                    (select radsrvid from tbl_services where managername='".$masterreseller."')");

        $query2 = $this->db->query("insert into tbl_services (srvname, radsrvid, managername, baseprice, costprice, saleprice) 
                                    select srvname, radsrvid, '".$reseller."' as managername, baseprice, costprice, saleprice from tbl_services 
                                    where managername = '".$masterreseller."'");
        
        $this->db->insert('tbl_settings', array(
            'stgname' => 'Inactive Days Limit',
            'stgtype' => 'INACTIVE-DAYS-BILL',
            'stgvalue' => 1,
            'managername' => $reseller
        ));
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }

    function getResellerInfo($managername)
    {
        $this->db->select('*');
        $this->db->from("rm_managers");
        $this->db->where('managername', $managername);
        $query = $this->db->get();
        return $query->row();
    }

    function updateCurrentReseller($resellerInfo, $reseller, $masterreseller)
    {
        $this->db->trans_start();
        $this->db->where('managername', $reseller);
        $this->db->update('rm_managers', $resellerInfo);
        $this->db->trans_complete();
        
        //return $insert_id;
        return True;
    }

    /**
     * Get total user count and expired user count for a manager, including all sub-managers recursively.
     * @param string $managername
     * @return array ['total_users' => int, 'expired_users' => int]
     */
    public function getManagerUserCountsRecursive($managername)
    {
        // 1. Get all managers
        $allManagers = $this->db->get('rm_managers')->result();

        // 2. Helper to recursively get all sub-managers
        $getAllSubManagers = function($managername, $allManagers, &$result = []) use (&$getAllSubManagers) {
            foreach ($allManagers as $row) {
                if ($row->mastername == $managername) {
                    $result[] = $row->managername;
                    $getAllSubManagers($row->managername, $allManagers, $result);
                }
            }
            return $result;
        };

        // 3. Get all sub-managers for the given manager
        $subManagers = $getAllSubManagers($managername, $allManagers);
        $allRelevantManagers = array_merge([$managername], $subManagers);

        // 4. Count total users
        $this->db->where_in('owner', $allRelevantManagers);
        $total_users = $this->db->count_all_results('rm_users');

        // 5. Count expired users
        $this->db->where_in('owner', $allRelevantManagers);
        $this->db->where('expiration >', date('Y-m-d'));
        $this->db->where('enableuser', 1);
        $expired_users = $this->db->count_all_results('rm_users');

        return [
            'total_users' => $total_users,
            'expired_users' => $expired_users
        ];
    }

    /**
     * Get user stats for a manager, including all sub-managers recursively.
     * @param string $managername
     * @return array ['active' => int, 'expired_last_month' => int, 'total' => int, 'new_users' => int]
     */
    public function getManagerUserStatsRecursive($managername)
    {
        $allManagers = $this->db->get('rm_managers')->result();
        $getAllSubManagers = function($managername, $allManagers, &$result = []) use (&$getAllSubManagers) {
            foreach ($allManagers as $row) {
                if ($row->mastername == $managername) {
                    $result[] = $row->managername;
                    $getAllSubManagers($row->managername, $allManagers, $result);
                }
            }
            return $result;
        };
        $subManagers = $getAllSubManagers($managername, $allManagers);
        $allRelevantManagers = array_merge([$managername], $subManagers);
        $now = date('Y-m-d H:i:s');
        $monthAgo = date('Y-m-d H:i:s', strtotime('-1 month'));

        // Active users
        $this->db->where_in('owner', $allRelevantManagers);
        $this->db->where('expiration >=', $now);
        $active = $this->db->count_all_results('rm_users');

        // Expired in last month
        $this->db->where_in('owner', $allRelevantManagers);
        $this->db->where('expiration <', $now);
        $this->db->where('expiration >=', $monthAgo);
        $expired_last_month = $this->db->count_all_results('rm_users');

        // Total = active + expired_last_month
        $total = $active + $expired_last_month;

        // New users in last month
        $this->db->where_in('owner', $allRelevantManagers);
        $this->db->where('createdon >=', $monthAgo);
        $new_users = $this->db->count_all_results('rm_users');

        // Online users (from radacct) - batch to avoid regex error
        $this->db->select('username');
        $this->db->where_in('owner', $allRelevantManagers);
        $usernames = $this->db->get('rm_users')->result_array();
        $username_list = array_column($usernames, 'username');

        $online = 0;
        /*$batchSize = 500;
        if (!empty($username_list)) {
            $chunks = array_chunk($username_list, $batchSize);
            foreach ($chunks as $chunk) {
                $this->db->where_in('username', $chunk);
                $this->db->where('acctstoptime IS NULL', null, false);
                $this->db->group_by('username');
                $online += $this->db->count_all_results('radacct');
            }
        }*/

        return [
            'active' => $active,
            'expired_last_month' => $expired_last_month,
            'total' => $total,
            'new_users' => $new_users,
            'online' => $online
        ];
    }

    /**
     * Get the number of online users for a manager and all sub-managers recursively.
     * @param string $managername
     * @return int
     */
    public function getOnlineUserCount($managername)
    {
        $allManagers = $this->db->get('rm_managers')->result();
        $getAllSubManagers = function($managername, $allManagers, &$result = []) use (&$getAllSubManagers) {
            foreach ($allManagers as $row) {
                if ($row->mastername == $managername) {
                    $result[] = $row->managername;
                    $getAllSubManagers($row->managername, $allManagers, $result);
                }
            }
            return $result;
        };
        $subManagers = $getAllSubManagers($managername, $allManagers);
        $allRelevantManagers = array_merge([$managername], $subManagers);
        // Get all usernames for these managers
        $this->db->select('username');
        $this->db->where_in('owner', $allRelevantManagers);
        $usernames = $this->db->get('rm_users')->result_array();
        $username_list = array_column($usernames, 'username');
        $online = 0;
        $batchSize = 500;
        if (!empty($username_list)) {
            $chunks = array_chunk($username_list, $batchSize);
            foreach ($chunks as $chunk) {
                $this->db->where_in('username', $chunk);
                $this->db->where('acctstoptime IS NULL', null, false);
                $this->db->group_by('username');
                $online += $this->db->count_all_results('radacct');
            }
        }
        return $online;
    }

    // Profile Management Functions
    
    /**
     * Get all profile roles
     */
    function getProfileRoles()
    {
        $this->db->select('*');
        $this->db->from('tbl_profileroles');
        $this->db->order_by('roleId', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * Get profile listing count
     */
    function profileListingCount($searchText = '')
    {
        $managername = $this->session->userdata ( 'name' );
        $this->db->select('BaseTbl.*, Roles.role');
        $this->db->from('tbl_profiles as BaseTbl');
        $this->db->join('tbl_profileroles as Roles', 'BaseTbl.roleId = Roles.roleId', 'left');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.profileid LIKE '%".$searchText."%'
                            OR BaseTbl.name LIKE '%".$searchText."%'
                            OR BaseTbl.mobile LIKE '%".$searchText."%'
                            OR Roles.role LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        if($this->ismaster > 0 && $managername <> 'admin'){
            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('managername', $manager_chain);
        }elseif($managername <> 'admin'){
            $this->db->where('managername', $managername);
        }

        $this->db->where('BaseTbl.isDeleted', 0);
        $query = $this->db->get();
        
        return $query->num_rows();
    }

    /**
     * Get profile listing with pagination
     */
    function profileListing($searchText = '', $page, $segment)
    {
        $managername = $this->session->userdata ( 'name' );
        $this->db->select('BaseTbl.*, Roles.role');
        $this->db->from('tbl_profiles as BaseTbl');
        $this->db->join('tbl_profileroles as Roles', 'BaseTbl.roleId = Roles.roleId', 'left');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.profileid LIKE '%".$searchText."%'
                            OR BaseTbl.name LIKE '%".$searchText."%'
                            OR BaseTbl.mobile LIKE '%".$searchText."%'
                            OR Roles.role LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        if($this->ismaster > 0 && $managername <> 'admin'){
            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('managername', $manager_chain);
        }elseif($managername <> 'admin'){
            $this->db->where('managername', $managername);
        }

        $this->db->where('BaseTbl.isDeleted', 0);
        $this->db->order_by('BaseTbl.userId', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        return $query->result();
    }

    /**
     * Check if profile exists
     */
    function checkProfileExist($profileid = '')
    {
        $this->db->select('profileid');
        $this->db->from('tbl_profiles');
        $this->db->where('profileid', $profileid);
        $this->db->where('isDeleted', 0);
        $query = $this->db->get();

        if ($query->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Add new profile
     */
    function addNewProfile($profileInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_profiles', $profileInfo);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
    }

    /**
     * Get profile info by ID
     */
    function getProfileInfo($userId)
    {
        $this->db->select('BaseTbl.*, Roles.role');
        $this->db->from('tbl_profiles as BaseTbl');
        $this->db->join('tbl_profileroles as Roles', 'BaseTbl.roleId = Roles.roleId', 'left');
        $this->db->where('BaseTbl.userId', $userId);
        $this->db->where('BaseTbl.isDeleted', 0);
        $query = $this->db->get();
        return $query->row();
    }

    /**
     * Update profile
     */
    function updateProfile($profileInfo, $userId)
    {
        $this->db->where('userId', $userId);
        $this->db->update('tbl_profiles', $profileInfo);
        
        return TRUE;
    }

    /**
     * Delete profile (soft delete)
     */
    function deleteProfile($userId, $profileInfo)
    {
        $this->db->where('userId', $userId);
        $this->db->update('tbl_profiles', $profileInfo);
        
        return TRUE;
    }

    /**
     * Get profile by profileid
     */
    function getProfileByProfileId($profileid)
    {
        $this->db->select('BaseTbl.*, Roles.role');
        $this->db->from('tbl_profiles as BaseTbl');
        $this->db->join('tbl_profileroles as Roles', 'BaseTbl.roleId = Roles.roleId', 'left');
        $this->db->where('BaseTbl.profileid', $profileid);
        $this->db->where('BaseTbl.isDeleted', 0);
        $query = $this->db->get();
        return $query->row();
    }
}
<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : User_model (User Model)
 * User model class to get to handle user related data 
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class User_model extends CI_Model
{
    /**
     * This function is used to get the user listing count
     * @param string $searchText : This is optional search text
     * @return number $count : This is row count
     */
    function userListingCount($searchText = '')
    {
        $this->db->select('BaseTbl.userId, BaseTbl.email, BaseTbl.name, BaseTbl.mobile, BaseTbl.createdDtm, Role.role');
        $this->db->from('tbl_users as BaseTbl');
        $this->db->join('tbl_roles as Role', 'Role.roleId = BaseTbl.roleId','left');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.email  LIKE '%".$searchText."%'
                            OR  BaseTbl.name  LIKE '%".$searchText."%'
                            OR  BaseTbl.mobile  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $this->db->where('BaseTbl.isDeleted', 0);
        $this->db->where('BaseTbl.roleId !=', 1);
        $query = $this->db->get();
        
        return $query->num_rows();
    }
    
    /**
     * This function is used to get the user listing count
     * @param string $searchText : This is optional search text
     * @param number $page : This is pagination offset
     * @param number $segment : This is pagination limit
     * @return array $result : This is result
     */
    function userListing($searchText = '', $page, $segment)
    {
        $this->db->select('BaseTbl.userId, BaseTbl.email, BaseTbl.name, BaseTbl.mobile, BaseTbl.createdDtm, Role.role');
        $this->db->from('tbl_users as BaseTbl');
        $this->db->join('tbl_roles as Role', 'Role.roleId = BaseTbl.roleId','left');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.email  LIKE '%".$searchText."%'
                            OR  BaseTbl.name  LIKE '%".$searchText."%'
                            OR  BaseTbl.mobile  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $this->db->where('BaseTbl.isDeleted', 0);
        $this->db->where('BaseTbl.roleId !=', 1);
        $this->db->order_by('BaseTbl.userId', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();        
        return $result;
    }
    
    /**
     * This function is used to get the user roles information
     * @return array $result : This is result of the query
     */
    function getUserRoles()
    {
        $this->db->select('roleId, role');
        $this->db->from('tbl_roles');
        $this->db->where('roleId !=', 1);
        $query = $this->db->get();
        
        return $query->result();
    }

    /**
     * This function is used to check whether email id is already exist or not
     * @param {string} $email : This is email id
     * @param {number} $userId : This is user id
     * @return {mixed} $result : This is searched result
     */
    function checkEmailExists($email, $userId = 0)
    {
        $this->db->select("email");
        $this->db->from("tbl_users");
        $this->db->where("email", $email);   
        $this->db->where("isDeleted", 0);
        if($userId != 0){
            $this->db->where("userId !=", $userId);
        }
        $query = $this->db->get();

        return $query->result();
    }
    
    
    /**
     * This function is used to add new user to system
     * @return number $insert_id : This is last inserted id
     */
    function addNewUser($userInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_users', $userInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return $insert_id;
    }
    
    /**
     * This function used to get user information by id
     * @param number $userId : This is user id
     * @return array $result : This is user information
     */
    function getUserInfo($userId)
    {
        $this->db->select('userId, name, email, mobile, roleId');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', 0);
		$this->db->where('roleId !=', 1);
        $this->db->where('userId', $userId);
        $query = $this->db->get();
        
        return $query->row();
    }
    
    
    
    /**
     * This function is used to update the user information
     * @param array $userInfo : This is users updated information
     * @param number $userId : This is user id
     */
    function editUser($userInfo, $userId)
    {
        $this->db->where('userId', $userId);
        $this->db->update('tbl_users', $userInfo);
        
        return TRUE;
    }
    
    
    
    /**
     * This function is used to delete the user information
     * @param number $userId : This is user id
     * @return boolean $result : TRUE / FALSE
     */
    function deleteUser($userId, $userInfo)
    {
        $this->db->where('userId', $userId);
        $this->db->update('tbl_users', $userInfo);
        
        return $this->db->affected_rows();
    }


    /**
     * This function is used to match users password for change password
     * @param number $userId : This is user id
     */
    function matchOldPassword($userId, $oldPassword)
    {
        $this->db->select('userId, password');
        $this->db->where('userId', $userId);        
        $this->db->where('isDeleted', 0);
        $query = $this->db->get('tbl_users');
        
        $user = $query->result();

        if(!empty($user)){
            if(verifyHashedPassword($oldPassword, $user[0]->password)){
                return $user;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }
    
    /**
     * This function is used to change users password
     * @param number $userId : This is user id
     * @param array $userInfo : This is user updation info
     */
    function changePassword($userId, $userInfo)
    {
        $this->db->where('userId', $userId);
        $this->db->where('isDeleted', 0);
        $this->db->update('tbl_users', $userInfo);
        
        return $this->db->affected_rows();
    }


    /**
     * This function is used to get user login history
     * @param number $userId : This is user id
     */
    function loginHistoryCount($userId, $searchText, $fromDate, $toDate)
    {
        $this->db->select('BaseTbl.userId, BaseTbl.sessionData, BaseTbl.machineIp, BaseTbl.userAgent, BaseTbl.agentString, BaseTbl.platform, BaseTbl.createdDtm');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.sessionData LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        if(!empty($fromDate)) {
            $likeCriteria = "DATE_FORMAT(BaseTbl.createdDtm, '%Y-%m-%d' ) >= '".date('Y-m-d', strtotime($fromDate))."'";
            $this->db->where($likeCriteria);
        }
        if(!empty($toDate)) {
            $likeCriteria = "DATE_FORMAT(BaseTbl.createdDtm, '%Y-%m-%d' ) <= '".date('Y-m-d', strtotime($toDate))."'";
            $this->db->where($likeCriteria);
        }
        if($userId >= 1){
            $this->db->where('BaseTbl.userId', $userId);
        }
        $this->db->from('tbl_last_login as BaseTbl');
        $query = $this->db->get();
        
        return $query->num_rows();
    }

    /**
     * This function is used to get user login history
     * @param number $userId : This is user id
     * @param number $page : This is pagination offset
     * @param number $segment : This is pagination limit
     * @return array $result : This is result
     */
    function loginHistory($userId, $searchText, $fromDate, $toDate, $page, $segment)
    {
        $this->db->select('BaseTbl.userId, BaseTbl.sessionData, BaseTbl.machineIp, BaseTbl.userAgent, BaseTbl.agentString, BaseTbl.platform, BaseTbl.createdDtm');
        $this->db->from('tbl_last_login as BaseTbl');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.sessionData  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        if(!empty($fromDate)) {
            $likeCriteria = "DATE_FORMAT(BaseTbl.createdDtm, '%Y-%m-%d' ) >= '".date('Y-m-d', strtotime($fromDate))."'";
            $this->db->where($likeCriteria);
        }
        if(!empty($toDate)) {
            $likeCriteria = "DATE_FORMAT(BaseTbl.createdDtm, '%Y-%m-%d' ) <= '".date('Y-m-d', strtotime($toDate))."'";
            $this->db->where($likeCriteria);
        }
        if($userId >= 1){
            $this->db->where('BaseTbl.userId', $userId);
        }
        $this->db->order_by('BaseTbl.id', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();        
        return $result;
    }

    /**
     * This function used to get user information by id
     * @param number $userId : This is user id
     * @return array $result : This is user information
     */
    function getUserInfoById($userId)
    {
        $this->db->select('userId, name, email, mobile, roleId');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', 0);
        $this->db->where('userId', $userId);
        $query = $this->db->get();
        
        return $query->row();
    }

    /**
     * This function used to get user information by id with role
     * @param number $userId : This is user id
     * @return aray $result : This is user information
     */
    function getUserInfoWithRole($userId)
    {
        $this->db->select('BaseTbl.userId, BaseTbl.email, BaseTbl.name, BaseTbl.mobile, BaseTbl.roleId, Roles.role');
        $this->db->from('tbl_users as BaseTbl');
        $this->db->join('tbl_roles as Roles','Roles.roleId = BaseTbl.roleId');
        $this->db->where('BaseTbl.userId', $userId);
        $this->db->where('BaseTbl.isDeleted', 0);
        $query = $this->db->get();
        
        return $query->row();
    }

    function getUsersNasInfo($username)
    {
        $this->db->select('nasipaddress');
        $this->db->from('radacct');
        $this->db->where('username',  $username);
        $this->db->order_by('radacctid', 'DESC');
        //echo $this->db->count_all_results();
        $query = $this->db->get();
        return $query->row();
    }

    // *********** Settings Data Managemenet *******************

    function settingsListingCount($searchText = '')
    {
        $this->db->select('BaseTbl.stgname, BaseTbl.stgtype, BaseTbl.stgvalue, BaseTbl.managername');
        $this->db->from('tbl_settings as BaseTbl');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.stgname  LIKE '%".$searchText."%'
                            OR  BaseTbl.stgvalue  LIKE '%".$searchText."%'
                            OR  BaseTbl.stgtype  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        $query = $this->db->get();
        
        return $query->num_rows();
    }
    
    function settingsListing($searchText = '', $page, $segment)
    {
        $this->db->select('BaseTbl.stgid, BaseTbl.stgname, BaseTbl.stgtype, BaseTbl.stgvalue, BaseTbl.managername');
        $this->db->from('tbl_settings as BaseTbl');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.stgname  LIKE '%".$searchText."%'
                            OR  BaseTbl.stgvalue  LIKE '%".$searchText."%'
                            OR  BaseTbl.stgtype  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        $managername = $this->session->userdata ( 'name' );

        if($managername <> 'admin'){
            if($this->ismaster > 0){
                $this->db->where('BaseTbl.managername in (Select managername from rm_managers where mastername = "'.$managername.'")');
                $this->db->where('BaseTbl.stgtype = ', 'POSTPAID-MANAGER');
            }
        }

        $this->db->order_by('BaseTbl.stgtype');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();        
        return $result;
    }

    function getSettingsInfo($stgId)
    {
        $this->db->select('stgid, stgname, stgtype, stgvalue, managername');
        $this->db->from('tbl_settings');
        $this->db->where('stgid', $stgId);
        $query = $this->db->get();
        
        return $query->row();
    }

    function addNewSettings($settingsInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_settings', $settingsInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return $insert_id;
    }

    function chkSettingsInfo($stgname, $stgtype, $managername)
    {
        $this->db->select("stgid");
        $this->db->from("tbl_settings");

        if($stgtype == "DEFAULT-ACCOUNT"){
            $this->db->where("stgname", $stgname);
        }else{
            $this->db->where("stgtype", $stgtype);   
            $this->db->where("managername", $managername);
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    function editSettings($settingsInfo, $stgid)
    {
        $this->db->where('stgid', $stgid);
        $this->db->update('tbl_settings', $settingsInfo);
        
        return TRUE;
    }

    // Dashboard - Users Details
    function dashboardCustomerInfo($masterInfo){
        $managername = $this->session->userdata ( 'name' );
        $this->db->select('sum(if(users.expiration >= now(), 1, 0)) as users, 
                            sum(if(users.createdon > now() - interval 30 day, 1, 0)) as created, 
                            sum(if(users.expiration > now() and users.expiration < now() + interval 30 day, 1, 0)) as expin30day, 
                            sum(if(users.expiration > now() - interval 30 day and users.expiration < now(), 1, 0)) as expired,
                            sum(if(users.expiration >= now() and users.expiration <= now() + interval 24 hour, 1, 0)) as expin1day, 
                            sum(if(users.expiration >= now() and users.expiration <= now() + interval 3 day, 1, 0)) as expin3day, 
                            sum(if(users.expiration >= now() - interval 1 day and users.expiration <= now(), 1, 0)) as exp1day, 
                            sum(if(users.expiration >= now() - interval 3 day and users.expiration <= now(), 1, 0)) as exp3day, 
                            sum(if(users.enableuser = 0, 1, 0)) as blocked');
        $this->db->from('rm_users as users');

        if($managername <> 'admin' && $masterInfo > 0){

            $manager_chain = $this->session->userdata('manager_chain');
            
            if (!empty($manager_chain) && is_array($manager_chain)) {
                $this->db->where_in('owner', $manager_chain);
            }

        }
        elseif($managername <> 'admin')
        { 
            $this->db->where('owner = "'.$managername.'"');
        }

        $this->db->where('username not in (select cardnum from rm_cards where active=0)');

        $queryUsers = $this->db->get();
        $results = $queryUsers->row();
        if ($queryUsers->num_rows() > 0){
            return $results;
        } else {
            return false;
        }

    }

    function dashboardCustomerOnlineStatus($masterInfo = ""){
        // Get the manager chain from session
        $manager_chain = $this->session->userdata('manager_chain');
        
        // Use direct SQL query to count online users
        // Join rm_users with radacct to get online users for the manager chain
        $this->db->select('COUNT(DISTINCT radacct.username) as online_count');
        $this->db->from('radacct');
        $this->db->join('rm_users', 'radacct.username = rm_users.username', 'inner');
        $this->db->where('radacct.acctstoptime IS NULL');
        if (!empty($manager_chain)) {
            $this->db->where_in('rm_users.owner', $manager_chain);
        }
        elseif($this->session->userdata('name') <> 'admin'){
            $this->db->where('rm_users.owner', $this->session->userdata('name'));
        }
        
        $query = $this->db->get();
        $result = $query->row();
        
        return $result ? (int)$result->online_count : 0;
    }

    function dashboardBalanceInfo($masterInfo = ""){
        //Total Balance from Table
        $managername = $this->session->userdata ( 'name' );
        $this->db->select('sum(amount) as amount, 
                            sum((if(invtype = "Recharge" and month(createdDtm) = month(curdate()) and year(createdDtm) = year(curdate()), amount,0))) as recharge, 
                            sum((if(invtype = "Credit" and expdate = "0000-00-00 00:00:00" and month(createdDtm) = month(curdate()) and year(createdDtm) = year(curdate()), amount,0))) as refund');
        $this->db->from('tbl_invoices');
        $this->db->where('managername', $managername);
        //$this->db->where('month(createdDtm) = month(curdate()) and year(createdDtm) = year(curdate())');
        //echo $this->db->count_all_results();
        $queryBalance = $this->db->get();
        $result = $queryBalance->row();
        if ($queryBalance->num_rows() > 0){
            return $result;
        } else {
            false;
        }
    }

    function dashboardCostOfSales($masterInfo){
        // Monthly Cost of Sales //
        $managername = $this->session->userdata ( 'name' );
        $this->db->select_sum('costprice');
        $this->db->from('rm_users as A');
        $this->db->join('tbl_services as B', 'A.srvid = B.radsrvid and A.owner = B.managername','left');

        if($masterInfo == 0 and $managername <> "admin"){
            $this->db->where('A.owner', $managername);
            $this->db->where('B.managername', $managername);
        }elseif($masterInfo > 0){
            $this->db->where('A.owner in (Select managername from rm_managers where mastername = "'.$managername.'")');
            $this->db->where('B.managername in (Select managername from rm_managers where mastername = "'.$managername.'")');
        }

        $this->db->where('A.enableuser', 1);
        $this->db->where('expiration >= CURDATE() - INTERVAL 3 DAY');

        $this->db->where('username not in (select cardnum from rm_cards where active=0)');
        //echo $this->db->count_all_results();
        $queryCostOfSales = $this->db->get();
        $result = $queryCostOfSales->row();
        if ($queryCostOfSales->num_rows() > 0){
            return $result;
        } else {
            return false;
        }
    }

    function dashboardEasyPaisaBalance(){
        //Monthly Easy Paisa
        $managername = $this->session->userdata ( 'name' );
        $this->db->select_sum('amount_paid');
        $this->db->from('tbl_eptransaction as A');
        //$this->db->join('tbl_eptransaction as B', 'A.username = B.username','left');

        if($managername <> 'admin'){  
            $this->db->where('A.username in (select username from rm_users where owner = "'.$managername.'")'); 
        }

        $this->db->where('month(A.createdDtm) = month(CURDATE())');
        $this->db->where('year(A.createdDtm) = year(CURDATE())');
        //echo $this->db->count_all_results();
        $query = $this->db->get();
        $results = $query->row();
        //return $results;
        
        if ($query->num_rows() > 0){
            return $results;
        } else {
            return false;
        }
    }


    function dashboard_connectionInfo($username){

        //$date2 = Date("Y-m-d");
        //$date1 = date('Y-m-d', strtotime("-1 month", strtotime($date2)));

        $this->db->select('*');
        $this->db->from('radacct');

        $this->db->where('username', $username);

        $this->db->order_by('radacctid', 'DESC');
        $this->db->limit(5, 1);

        $query = $this->db->get();
        $result = $query->result();
        return $result;

    }

    /**
     * Get settings for a manager
     */
    public function getManagerSettings($managername)
    {
        $this->db->select('stgid, stgtype, stgvalue, stgname');
        $this->db->from('tbl_settings');
        $this->db->where('managername', $managername);
        $query = $this->db->get();
        
        return $query->result();
    }

    /**
     * Add new setting for a manager
     */
    public function settingAddNewManager($settingInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_settings', $settingInfo);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
    }

    /**
     * Delete a manager setting
     */
    public function deleteManagerSetting($stgid)
    {
        $this->db->where('stgid', $stgid);
        $this->db->delete('tbl_settings');
        
        return $this->db->affected_rows() > 0;
    }
}  
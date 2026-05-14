<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Login_model (Login Model)
 * Login model class to get to authenticate user credentials 
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class Login_model extends CI_Model
{

    function loginManager($user, $password)
    {
        $this->db->select('*');
        $this->db->from('rm_managers');
        $this->db->where('managername', $user);

        $query = $this->db->get();
        
        $user = $query->row();
        
        if(!empty($user)){
            //echo md5($password)."-".$user->password;
            if((md5($password)) == ($user->password)){
                return $user;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    function loginProfile($user, $password)
    {
        $this->db->select('A.password as password, A.*, B.perm_allowaccounts');
        $this->db->from('tbl_profiles as A');
        $this->db->where('A.profileid', $user);
        $this->db->join('rm_managers as B', 'B.managername = A.managername');

        $query = $this->db->get();
        
        $user = $query->row();
        
        if(!empty($user)){
            //echo md5($password)."-".$user->password;
            if((md5($password)) == ($user->password)){
                return $user;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    function lisMasterManager($manager)
    {
        $this->db->select('count(managername) as managercount');
        $this->db->from('rm_managers');
        $this->db->where('mastername', $manager);

        $query = $this->db->get();
        
        $submanagers = $query->row();
        
        if(!empty($submanagers)){
            //echo md5($password)."-".$user->password;
            return $submanagers;
        } else {
            return array();
        }
    }

    /**
     * Recursively gets all subordinate managers for a given master manager.
     * This PHP-based recursive approach is compatible with older versions of
     * MySQL/MariaDB that do not support WITH RECURSIVE.
     *
     * @param string $managername The name of the master manager.
     * @return array A list of all subordinate manager names, including the master.
     */
    public function master_chain($managername)
    {
        $all_managers = [];
        $managers_to_check = [$managername]; // Start with the master manager

        // Loop until we have no more managers to check for subordinates
        while (!empty($managers_to_check)) {
            // Add the managers we are about to check to our final list
            $all_managers = array_merge($all_managers, $managers_to_check);

            // Find all direct subordinates of the current list of managers
            $this->db->select('managername');
            $this->db->from('rm_managers');
            $this->db->where_in('mastername', $managers_to_check);
            $query = $this->db->get();

            if (!$query) {
                // Query failed, break to prevent infinite loop
                log_message('error', 'Subordinate query failed in Login_model::master_chain.');
                break;
            }

            $subordinates = $query->result_array();

            // The list for the next iteration is the subordinates we just found
            $managers_to_check = array_column($subordinates, 'managername');
        }
        
        // Return a unique list of all found managers
        return array_unique($all_managers);
    }

    function getSettingInfo($manager, $stgType)
    {
        $this->db->select('stgvalue as status, managername, stgname');
        $this->db->from('tbl_settings');
        $this->db->where('managername', $manager);
        $this->db->where('stgtype', $stgType);

        $query = $this->db->get();
        
        $isaccountmanager = $query->row();
        
        if(!empty($isaccountmanager)){
            //echo md5($password)."-".$user->password;
            return $isaccountmanager;
        } else {
            return array();
        }
    }

    /**
     * This function used to check the login credentials of the user
     * @param string $email : This is email of the user
     * @param string $password : This is encrypted password of the user
     */
    function loginMe($email, $password)
    {

        $this->db->select('BaseTbl.userId, BaseTbl.password, BaseTbl.name, BaseTbl.roleId, Roles.role');
        $this->db->from('tbl_users as BaseTbl');
        $this->db->join('tbl_roles as Roles','Roles.roleId = BaseTbl.roleId');
        $this->db->where('BaseTbl.email', $email);
        $this->db->where('BaseTbl.isDeleted', 0);
        $query = $this->db->get();
        
        $user = $query->row();
        
        if(!empty($user)){
            if(verifyHashedPassword($password, $user->password)){
                return $user;
            } else {
                return array();
            }
        } else {
            return array();
        }
    }

    /**
     * This function used to check email exists or not
     * @param {string} $email : This is users email id
     * @return {boolean} $result : TRUE/FALSE
     */
    function checkEmailExist($email)
    {
        $this->db->select('userId');
        $this->db->where('email', $email);
        $this->db->where('isDeleted', 0);
        $query = $this->db->get('tbl_users');

        if ($query->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }


    /**
     * This function used to insert reset password data
     * @param {array} $data : This is reset password data
     * @return {boolean} $result : TRUE/FALSE
     */
    function resetPasswordUser($data)
    {
        $result = $this->db->insert('tbl_reset_password', $data);

        if($result) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * This function is used to get customer information by email-id for forget password email
     * @param string $email : Email id of customer
     * @return object $result : Information of customer
     */
    function getCustomerInfoByEmail($email)
    {
        $this->db->select('userId, email, name');
        $this->db->from('tbl_users');
        $this->db->where('isDeleted', 0);
        $this->db->where('email', $email);
        $query = $this->db->get();

        return $query->row();
    }

    /**
     * This function used to check correct activation deatails for forget password.
     * @param string $email : Email id of user
     * @param string $activation_id : This is activation string
     */
    function checkActivationDetails($email, $activation_id)
    {
        $this->db->select('id');
        $this->db->from('tbl_reset_password');
        $this->db->where('email', $email);
        $this->db->where('activation_id', $activation_id);
        $query = $this->db->get();
        return $query->num_rows();
    }

    // This function used to create new password by reset link
    function createPasswordUser($email, $password)
    {
        $this->db->where('email', $email);
        $this->db->where('isDeleted', 0);
        $this->db->update('tbl_users', array('password'=>getHashedPassword($password)));
        $this->db->delete('tbl_reset_password', array('email'=>$email));
    }

    /**
     * This function used to save login information of user
     * @param array $loginInfo : This is users login information
     */
    function lastLogin($loginInfo)
    {
        $this->db->trans_start();
        $this->db->insert('ns_last_login', $loginInfo);
        $this->db->trans_complete();
    }

    /**
     * This function is used to get last login info by user id
     * @param number $userId : This is user id
     * @return number $result : This is query result
     */
    function lastLoginInfo($managername)
    {
        $this->db->select('BaseTbl.createdDtm');
        $this->db->where('BaseTbl.managername', $managername);
        $this->db->order_by('BaseTbl.id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('ns_last_login as BaseTbl');

        return $query->row();
    }

    function dashboardCustomerInfo($masterInfo){
        $managername = $this->session->userdata ( 'name' );
        $this->db->select('sum(if(users.expiration >= now(), 1, 0)) as users, 
                            sum(if(users.createdon > now() - interval 30 day, 1, 0)) as created, 
                            sum(if(users.expiration > now() - interval 30 day and users.expiration < now(), 1, 0)) as expired, 
                            sum(if(users.enableuser = 0, 1, 0)) as blocked');
        $this->db->from('rm_users as users');
        $this->db->where('acctype=0');

        if($managername <> 'admin' && $masterInfo > 0){
            $this->db->where('owner in (Select managername from rm_managers where mastername =  "'.$managername.'")');
        }
        elseif($managername <> 'admin')
        { 
            $this->db->where('owner = "'.$managername.'"');
        }

        $queryUsers = $this->db->get();
        $results = $queryUsers->row();
        if ($queryUsers->num_rows() > 0){
            return $results;
        } else {
            return false;
        }

    }

    function dashboardCustomerOnlineStatus($masterInfo){
        // Online Users
        $managername = $this->session->userdata ( 'name' );
        $this->db->select('username');
        $this->db->where('isnull(acctstoptime)=true');
        $this->db->from('radacct');
        if($managername <> 'admin'){
            if($masterInfo > 0){
            $this->db->where("username IN (Select username from rm_users where owner in (Select managername from rm_managers where mastername = '".$managername."'))", null, false);
            }
            else
            { $this->db->where("username IN (Select username from rm_users where owner='".$managername."')", null, false); }
        }
        return $this->db->count_all_results();
    }

    function dashboardBalanceInfo(){
        //Total Balance from Table
        $managername = $this->session->userdata ( 'name' );
        $this->db->select_sum('amount');
        $this->db->from('tbl_invoices');
        $this->db->where('managername', $managername);
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
        $this->db->join('tbl_services as B', 'A.srvid = B.radsrvid','left');
        if($masterInfo < 1){
            $this->db->where('A.owner', $managername);
            $this->db->where('B.managername', $managername);}
        else{
            $this->db->where('A.owner in (Select managername from rm_managers where mastername = "'.$managername.'")');
            $this->db->where('B.managername', $managername);
        }
        $this->db->where('A.enableuser', 1);
        $this->db->where('expiration >= CURDATE() - INTERVAL 7 DAY');
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
        $this->db->from('rm_users as A');
        $this->db->join('tbl_eptransaction as B', 'A.username = B.username','left');

        if($managername <> 'admin'){  $this->db->where('A.owner', $managername); }
        $this->db->where('month(B.createdDtm) = month(CURDATE())');
        $this->db->where('year(B.createdDtm) = year(CURDATE())');
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

    function dashboardInfo($masterInfo){

        $managername = $this->session->userdata ( 'name' );
        /* and users.expiration <= (now() + interval 180 day)
                            and users.expiration >= (now() - interval 30 day) */
        $this->db->select('sum(if(users.expiration >= now(), 1, 0)) as users, 
                            sum(if(users.expiration > now(), 1, 0)) as active, 
                            sum(if(users.expiration <= now(), 1, 0)) as expired, 
                            sum(if(users.enableuser = 0, 0, 1)) as blocked');
        $this->db->from('rm_users as users');
        $this->db->where('acctype=0');

        if($managername <> 'admin' && $masterInfo > 0){
            $this->db->where('owner in (Select managername from rm_managers where mastername =  "'.$managername.'")');
        }
        elseif($managername <> 'admin')
        { 
            $this->db->where('owner = "'.$managername.'"');
        }

        $queryUsers = $this->db->get();
        $result = $queryUsers->row();
        if ($queryUsers->num_rows() > 0){
            $totalusers = $result->users;
            $activeusers = $result->active;
            $expiredusers = $result->expired;
            $blockedusers = $result->blocked;
        } else {
            $totalusers = 0;
            $activeusers = 0;
            $expiredusers = 0;
            $blockedusers = 0;
        }

        // Online Users
        $this->db->select('username');
        $this->db->where('isnull(acctstoptime)=true');
        $this->db->from('radacct');
        if($managername <> 'admin'){
            if($this->ismaster > 0){
            $this->db->where("username IN (Select username from rm_users where owner in (Select managername from rm_managers where mastername = '".$managername."'))", null, false);
            }
            else
            { $this->db->where("username IN (Select username from rm_users where owner='".$managername."')", null, false); }
        }
        $online_customers = $this->db->count_all_results();

        //Total Balance from Table
        $this->db->select_sum('amount');
        $this->db->from('tbl_invoices');
        $this->db->where('managername', $managername);
        //echo $this->db->count_all_results();
        $queryBalance = $this->db->get();
        $result = $queryBalance->row();
        if ($queryBalance->num_rows() > 0){
            echo $result->amount;
        } else {
            echo 0;
        }

        // Monthly Cost of Sales //
        $this->db->select_sum('costprice');
        $this->db->from('rm_users as A');
        $this->db->join('tbl_services as B', 'A.srvid = B.radsrvid','left');
        if($this->ismaster < 1){
            $this->db->where('A.owner', $managername);
            $this->db->where('B.managername', $managername);}
        else{
            $this->db->where('A.owner in (Select managername from rm_managers where mastername = "'.$managername.'")');
            $this->db->where('B.managername', $managername);
        }
        $this->db->where('A.enableuser', 1);
        $this->db->where('expiration >= CURDATE() - INTERVAL 7 DAY');
        //echo $this->db->count_all_results();
        $queryCostOfSales = $this->db->get();
        $result = $queryCostOfSales->row();
        if ($queryCostOfSales->num_rows() > 0){
            echo "Rs. ".$result->costprice;
        } else {
            echo 0;
        }

        //Monthly Easy Paisa
        $this->db->select_sum('amount_paid');
        $this->db->from('rm_users as A');
        $this->db->join('tbl_eptransaction as B', 'A.username = B.username','left');

        if($managername <> 'admin'){  $this->db->where('A.owner', $managername); }
        $this->db->where('month(B.createdDtm) = month(CURDATE())');
        $this->db->where('year(B.createdDtm) = year(CURDATE())');
        //echo $this->db->count_all_results();
        $query = $this->db->get();
        $result = $query->row();

    }
}

?>
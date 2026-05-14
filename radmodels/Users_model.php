<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Users_model extends CI_Model
{

    function subscribersList($searchText = '', $type, $accType = 0, $page, $segment, $orderColumnIndex, $orderDir)
    {

        $this->db->start_cache(); // Start QB caching

        $this->db->select('BaseTbl.username, BaseTbl.enableuser, BaseTbl.expiration, BaseTbl.firstname, BaseTbl.lastname, 
                            BaseTbl.owner, BaseTbl.address, BaseTbl.mobile, BaseTbl.city, BaseTbl.createdon, 
                            Services.srvname as servicename, imgfiles.payid,
                            IF(ISNULL(imgfiles.cnic_file1), 0, IF(imgfiles.cnic_file1="",0,1)) as verified');
        $this->db->from('rm_users as BaseTbl');
        $this->db->join('rm_usergroups as Group', 'BaseTbl.groupid = Group.groupid', 'left');
        $this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.radsrvid and BaseTbl.owner = Services.managername', 'left');
        $this->db->join('tbl_userdocs as imgfiles', 'BaseTbl.username = imgfiles.username', 'left');

        $this->db->where('BaseTbl.acctype', $accType);

        $managername = $this->session->userdata('name');
        if ($managername !== 'admin') {
            if ($this->ismaster > 0) {

                // Build the subquery as a string
                $subQuery = $this->db->select('managername')
                            ->from('rm_managers')
                            ->where('mastername', $managername)
                            ->get_compiled_select();

                // Use the subquery within where_in
                $this->db->where_in('BaseTbl.owner', $subQuery, FALSE);

            } else {
                $this->db->where('BaseTbl.owner', $managername);
            }
        }

        // Simplify type-based conditions here...
        // Apply type-based filters
        $this->applyTypeFilter($type, $accType);

        if (!empty($searchText)) {
            $this->db->group_start();
            $this->db->like('BaseTbl.username', $searchText);
            $this->db->or_like('BaseTbl.firstname', $searchText);
            $this->db->or_like('BaseTbl.lastname', $searchText);
            // Add other fields as needed...
            $this->db->group_end();
        }

        $this->db->stop_cache(); // Stop QB caching

        //echo $this->db->get_compiled_select();

        // First, get the total count of records
        $totalCount = $this->db->count_all_results('rm_users as BaseTbl', false);

        $this->db->flush_cache();

        // Then, apply limit and order and retrieve the actual records
        $this->db->order_by($orderColumnIndex, $orderDir);
        $this->db->order_by('BaseTbl.username');
        $this->db->limit($page, $segment);
        $query = $this->db->get();

        //return $query->result();
        return [
            'totalCount' => $totalCount,
            'records' => $query->result()
        ];
    }

    protected function applyTypeFilter($type, $accType) {

        $curDate = date("Y-m-d");
        $curDate3Days = date('Y-m-d', strtotime('+3 days'));
        
        switch ($type) {
            case 1:
                $this->db->where('BaseTbl.enableuser', 1);
                $this->db->where('BaseTbl.acctype', $accType);
                $this->db ->where('BaseTbl.expiration >', $curDate);
                break;
            case 2:
                // Add cases as per the original logic
                $this->db->where('enableuser=1 and acctype='.$accType.' and expiration <="'.$curDate.'"');
                break;
            case 3:
                // Add cases as per the original logic
                $this->db->where('BaseTbl.enableuser=', 0);
                break;
            case 4:
                // Add cases as per the original logic
                $this->db->where('BaseTbl.acctype', $accType);
                break;
            case 5:
                $this->db->where('BaseTbl.username in (select username from radacct where isnull(acctstoptime)=TRUE)');
                break;
            case 6:
                $this->db->where('BaseTbl.username not in (select username from radacct where isnull(acctstoptime)=TRUE) 
                                    and enableuser=1 and acctype='.$accType.' and expiration >= "'.$curDate.'"');
                break;
            case 7:
                $this->db->where('BaseTbl.username not in (select username from radacct where isnull(acctstoptime)=TRUE) 
                                    and enableuser=1 and acctype='.$accType.' and expiration < "'.$curDate.'"');
                break;
            case 8:
                $this->db->where('BaseTbl.enableuser', 1);
                $this->db->where('BaseTbl.acctype', $accType);
                $this->db->where('BaseTbl.expiration >=', $curDate);
                $this->db->where('BaseTbl.expiration <=', $curDate3Days);
                break;
            case 9:
                $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() and expiration <= now() + interval 1 day');
                break;
            case 10:
                $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() and expiration <= now() + interval 1 day');
                break;
            case 11:
                $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() and expiration <= now() + interval 1 day');
                break;
            case 12:
                $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() - interval 7 day and expiration <= now()');
                break;
        }

    }


    // OLD Methods Replaced by Subscribers Methods
    function userListingCount($searchText = '', $type, $accType = 0)
    {

        $this->db->select('BaseTbl.username, BaseTbl.enableuser, BaseTbl.expiration, BaseTbl.firstname, BaseTbl.lastname, 
                            BaseTbl.mobile, BaseTbl.owner, BaseTbl.city, imgfiles.payid');
        $this->db->from('rm_users as BaseTbl');
        //$this->db->join('rm_usergroups as Group', 'BaseTbl.groupid = Group.groupid','left');
        $this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.radsrvid and BaseTbl.owner = Services.managername','left');
        $this->db->join('tbl_userdocs as imgfiles', 'BaseTbl.username = imgfiles.username','left');

        if(!empty($searchText)) {

            $likeCriteria = "(BaseTbl.username  LIKE '%".$searchText."%'
                            OR  BaseTbl.firstname  LIKE '%".$searchText."%'
                            OR  BaseTbl.lastname  LIKE '%".$searchText."%'
                            OR  BaseTbl.owner  LIKE '%".$searchText."%'
                            OR  BaseTbl.mobile  LIKE '%".$searchText."%'
                            OR  imgfiles.payid  LIKE '%".$searchText."%'
                            OR  Services.srvname LIKE '%".$searchText."%'
                            OR  BaseTbl.address LIKE '%".$searchText."%'
                            OR  BaseTbl.owner  LIKE '%".$searchText."%')";

            $this->db->where($likeCriteria);
        }

        $managername = $this->session->userdata ( 'name' );

        if($managername <> 'admin' && $this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('BaseTbl.owner', $manager_chain);
        }elseif($managername <> 'admin'){
            $this->db->where('BaseTbl.owner', $managername);
        }

        $this->db->where('BaseTbl.acctype', $accType);

        if($type == 0)
            $this->db->where('BaseTbl.enableuser =', 1);

        $curDate = date("y-m-d");
        $curDate3Days = date('Y-m-d', strtotime('+3 days'));
        $curDate1Day = date('Y-m-d', strtotime('+1 days'));

        if($type == 1)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >"'.$curDate.'"');
        
        if($type == 2)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration <="'.$curDate.'"');

        if($type == 3)
            $this->db->where('BaseTbl.enableuser', 0);
        
        if($type == 4)
            $this->db->where('BaseTbl.acctype', $accType);

        if($type == 5)
            $this->db->where('BaseTbl.username in (select username from radacct where isnull(acctstoptime)=TRUE)');

        if($type == 6)
            $this->db->where('BaseTbl.username not in (select username from radacct where isnull(acctstoptime)=TRUE) and enableuser=1 and acctype='.$accType.' and expiration >= "'.$curDate.'"');
        
        if($type == 7)
            $this->db->where('BaseTbl.username not in (select username from radacct where isnull(acctstoptime)=TRUE) and enableuser=1 and acctype='.$accType.' and expiration < "'.$curDate.'"');

        if($type == 8)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() and expiration <= now() + interval 3 day');

        if($type == 9)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() and expiration <= now() + interval 1 day');

        if($type == 10)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() - interval 1 day and expiration <= now()');

        if($type == 11)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() - interval 3 day and expiration <= now()');
        
        if($type == 12)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() - interval 7 day and expiration <= now()');
        
        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function userListing($searchText = '', $type, $accType = 0, $page, $segment)
    {
        $this->db->query('Set @row_number = 0');
        $this->db->select('BaseTbl.username, BaseTbl.enableuser, BaseTbl.expiration, BaseTbl.firstname, BaseTbl.lastname, BaseTbl.owner, BaseTbl.address, 
                            BaseTbl.mobile, BaseTbl.owner, BaseTbl.city, BaseTbl.createdon, Services.srvname as servicename, imgfiles.payid,
                            IF(IsNull(imgfiles.cnic_file1), 0, IF(imgfiles.cnic_file1="",0,1)) as verified,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('rm_users as BaseTbl');
        $this->db->join('rm_usergroups as Group', 'BaseTbl.groupid = Group.groupid','left');
        $this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.radsrvid and BaseTbl.owner = Services.managername','left');
        $this->db->join('tbl_userdocs as imgfiles', 'BaseTbl.username = imgfiles.username','left');
        //$this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.radsrvid','left');

        if(!empty($searchText)) {

            $likeCriteria = "(BaseTbl.username  LIKE '%".$searchText."%'
                            OR  BaseTbl.firstname  LIKE '%".$searchText."%'
                            OR  BaseTbl.lastname  LIKE '%".$searchText."%'
                            OR  BaseTbl.owner  LIKE '%".$searchText."%'
                            OR  BaseTbl.mobile  LIKE '%".$searchText."%'
                            OR  imgfiles.payid  LIKE '%".$searchText."%'
                            OR  Services.srvname LIKE '%".$searchText."%'
                            OR  BaseTbl.address LIKE '%".$searchText."%'
                            OR  BaseTbl.expiration LIKE '%".$searchText."%'
                            OR  BaseTbl.owner  LIKE '%".$searchText."%')";

            $this->db->where($likeCriteria);

        }
        //$this->db->where('BaseTbl.isDeleted', 0);
        //$this->db->where('BaseTbl.roleId !=', 1);
        $this->db->where('BaseTbl.acctype', $accType);

        $managername = $this->session->userdata ( 'name' );

        if($managername <> 'admin' && $this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('BaseTbl.owner', $manager_chain);
        }elseif($managername <> 'admin'){
            $this->db->where('BaseTbl.owner', $managername);
        }

        if($type == 0){

            $this->db->where('BaseTbl.enableuser ', 1);

            if($accType == 2){ // If Prepaid cards then filter it to Only Active Cards
                $this->db->where('BaseTbl.username in (Select cardnum from rm_cards where active=1)');
            }
        }

        $curDate = date("y-m-d");
        $curDate3Days = date('Y-m-d', strtotime('+3 days'));

        if($type == 1)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration > "'.$curDate.'"');
        
        if($type == 2)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration <= "'.$curDate.'"');

        if($type == 3)
            $this->db->where('BaseTbl.enableuser', 0);
        
        if($type == 4)
            $this->db->where('BaseTbl.acctype', $accType);

        if($type == 5)
            $this->db->where('BaseTbl.username in (select username from radacct where isnull(acctstoptime)=TRUE)');

        if($type == 6)
            $this->db->where('BaseTbl.username not in (select username from radacct where isnull(acctstoptime)=TRUE) and enableuser=1 and acctype='.$accType.' and expiration >= "'.$curDate.'"');
        
        if($type == 7)
            $this->db->where('BaseTbl.username not in (select username from radacct where isnull(acctstoptime)=TRUE) and enableuser=1 and acctype='.$accType.' and expiration < "'.$curDate.'"');

        if($type == 8)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() and expiration <= now() + interval 3 day');

        if($type == 9)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() and expiration <= now() + interval 1 day');

        if($type == 10)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() - interval 1 day and expiration <= now()');

        if($type == 11)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() - interval 3 day and expiration <= now()');
        
        if($type == 12)
            $this->db->where('enableuser=1 and acctype='.$accType.' and expiration >= now() - interval 7 day and expiration <= now()');

        if($type == 2 ||$type >= 8 && $type <= 12){
            $this->db->order_by('BaseTbl.expiration', 'DESC');
        }else{
            $this->db->order_by('BaseTbl.createdon', 'DESC');
            $this->db->order_by('BaseTbl.username');
        }

        $this->db->limit($page, $segment);
        $query = $this->db->get();

        $result = $query->result();        
        return $result;
    }

    function getPackages()
    {
        $managername = $this->session->userdata ( 'name' );

        $this->db->select('srvid, srvname, unitprice');
        $this->db->from('rm_services');
        $this->db->where("srvid IN (Select srvid from rm_allowedmanagers where managername='".$managername."')", null, false);
        
        if($managername <> 'admin'){
            $this->db->where("srvid IN (Select radsrvid from tbl_services where managername='".$managername."')", null, false);
        }

        $this->db->order_by('srvname', 'ASC');

        $query = $this->db->get();
        
        return $query->result();
    }

    function getPackagesByManager($managername)
    {

        $this->db->select('srvid, srvname, unitprice');
        $this->db->from('rm_services');
        $this->db->where("srvid IN (Select srvid from rm_allowedmanagers where managername='".$managername."')", null, false);
        
        $this->db->where("srvid IN (Select radsrvid from tbl_services where managername='".$managername."')", null, false);

        $query = $this->db->get();
        
        return $query->result();
    }

    function getResellerPackages()
    {
        $managername = $this->session->userdata ( 'name' );

        $this->db->select('srvid, srvname, radsrvid, costprice as unitprice, saleprice');
        $this->db->from('tbl_services');
        $this->db->where('managername', $managername);
        $query = $this->db->get();
        
        return $query->result();
    }

    function getUserInfo($userId, $accType = 0)
    {

        $this->db->select('A.username, A.password, A.enableuser, A.uplimit, A.downlimit, A.comblimit, A.firstname, A.lastname, A.company, A.mobile,
                        A.address, A.email, A.taxid, A.srvid, B.srvname, A.gpslat, A.gpslong, A.mac, A.usemacauth, 
                        A.expiration, A.uptimelimit, A.owner, A.createdon, A.acctype, A.city, A.state, A.country, A.verified,
                        A.alertemail, A.alertsms, A.phone, A.comment, A.zip, A.staticipcpe, B.downrate, B.uprate,
                        B.trafficunitdl, B.trafficunitul, B.trafficunitcomb,B.dlburstlimit,B.ulburstlimit');
        $this->db->from('rm_users as A');
        $this->db->join('rm_services as B', 'A.srvid = B.srvid','left');
		//$this->db->where('acctype =', $accType); // Removed due to Prepaid Card Types
        $this->db->where('username', $userId);
        $query = $this->db->get();
        
        return $query->row();

    }

    function getRadGetPassword($username){

        $this->db->select('username, attribute, value');
        $this->db->from('radcheck as A');
        $this->db->where('username', $username);
        $this->db->where('attribute = "Cleartext-Password"');
        
        $query = $this->db->get();

        return $query->row();

    }

    function getManagersList()
    {
        $managername = $this->session->userdata ( 'name' );

        $this->load->model('login_model');
        $manager_chain = $this->login_model->master_chain($managername);

        $this->db->select('managername, firstname, lastname');
        $this->db->from('rm_managers');
        $this->db->where('managername <>', 'admin');

        //$manager_chain = $this->session->userdata('manager_chain');
        $manager_chain_quoted = array_map(function($v) { return "'".$this->db->escape_str($v)."'"; }, $manager_chain);
        $manager_chain_str = implode(',', $manager_chain_quoted);

        if($this->ismaster > 0){
            //$this->db->where('mastername', $managername);
            $this->db->where('managername IN ('.$manager_chain_str.')');
        }
        $this->db->order_by('managername', 'ASC');

        $query = $this->db->get();
        
        return $query->result();
    }

    function getManagerInfo($managername)
    {
        $this->db->select('*');
        $this->db->from('rm_managers');
        $this->db->where('managername', $managername);
        
        $query = $this->db->get();
        return $query->row();
    }

    function checkManagerExist($newmanager)
    {
        $this->db->select('managername');
        $this->db->from("rm_managers");
        $this->db->where('managername', $newmanager);

        $query = $this->db->get();

        if ($query->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }


    function addNewUser($userInfo, $radpassword, $radsimuse)
    {
        $this->db->trans_start();
        $this->db->insert('rm_users', $userInfo);
        
        $this->db->insert('radcheck', $radpassword);
        $this->db->insert('radcheck', $radsimuse);
        //$insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        //return $insert_id;
        return True;
    }

    function addNewCard($cardInfo)
    {
        $this->db->trans_start();
        $this->db->insert('rm_cards', $cardInfo);
        //$insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        //return $insert_id;
        return True;
    }

    function editUser($userInfo, $user)
    {

        $this->db->trans_start();
        $this->db->where('username', $user);
        $this->db->update('rm_users', $userInfo);
        $this->db->trans_complete();
        
        return TRUE;
    }

    function editUserPasswordRadCheck($userInfo, $user){
        $this->db->where('username', $user);
        $this->db->where('attribute', 'Cleartext-Password');
        $this->db->update('radcheck', $userInfo);
        
        return TRUE;
    }

    function updateVerifyUser($usersInfo, $user)
    {
        $this->db->where('username', $user);
        $this->db->update('rm_users', $usersInfo);
        
        return TRUE;
    }

    function store_images($usersInfo, $user)
    {
        $this->db->select('username');
        //$this->db->from("tbl_userdocs");
        $this->db->where('username', $user);
        $query = $this->db->get('tbl_userdocs');

        if ($query->num_rows() > 0){

            $this->db->where('username', $user);
            $this->db->update('tbl_userdocs', $usersInfo);
        }
        else
        {
            $this->db->trans_start();
            $this->db->insert('tbl_userdocs', $usersInfo);
            $this->db->trans_complete();
        }
        return True;

    }

    function update_images($usersInfo, $user)
    {
        $this->db->where('username', $user);
        $this->db->update('tbl_userdocs', $usersInfo);
        
        return TRUE;
    }

    function checkUserExist($username)
    {
        $this->db->select('username');
        $this->db->from("rm_users");
        $this->db->where('username', $username);
        $query = $this->db->get();

        if ($query->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }

    function checkUsernameExists($username)
    {
        $this->db->select("username");
        $this->db->from("rm_users");
        $this->db->where("username", $username);   

        $query = $this->db->get();

        return $query->result();
    }

    function getUserDocsInfo($user)
    {

        $this->db->select('username, payid, payname, discount, adjamount, cnic_file1, cnic_file2, cnicno, createdDtm,
        inst_name, inst_box, inst_wifi, inst_fiber, inst_meter, inst_chrg, inst_cost, inst_disc, segmentid, parameter');
        $this->db->from("tbl_userdocs");
        $this->db->where('username', $user);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0){
            return $query->row();
        } else {
            return false;
        }

    }

    function checkDocumentsExists($username)
    {
        $this->db->select('username');
        $this->db->from("tbl_userdocs");
        $this->db->where('username', $username);
        $query = $this->db->get();

        if ($query->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }

    function updateDocsInfo1($user, $usersInfo = ''){
        $this->db->where('username', $user);
        $this->db->update('tbl_userdocs', $usersInfo);
        
        return TRUE;
    }

    function updateDocsInfo($usersInfo, $user)
    {
        $this->db->select('username');
        $this->db->where('username', $user);
        $query = $this->db->get('tbl_userdocs');

        if ($query->num_rows() > 0){

            $this->db->where('username', $user);
            $this->db->update('tbl_userdocs', $usersInfo);
        }
        else
        {
            $this->db->trans_start();
            $this->db->insert('tbl_userdocs', $usersInfo);
            $this->db->trans_complete();
        }

        $this->db->select('payid');
        $this->db->where('username', $user);
        $this->db->where('isnull(payid)=true');
        $query = $this->db->get('tbl_userdocs');

        if ($query->num_rows() > 0){

            // Generate Unique Payment ID
            for ($x = 0; $x <= 1000; $x++) {
                $key = rand(0, 99999);
                $key = str_pad($key, 6, 21, STR_PAD_LEFT);
                //echo $key;
                //exit;
                $this->db->select('username');
                $this->db->where('payid',$key);
                $query = $this->db->get('tbl_userdocs');
                if ($query->num_rows() > 0){
                    continue;
                }
                else
                {
                    $this->db->query('Update tbl_userdocs set payid="'.$key.'" where username="'.$user.'"');
                    break;
                }
            }
        }

        return True;

    }

    function verifyDuplicateFileName($file1, $file2)
    {

        $this->db->select('cnic_file1');
        $this->db->where('cnic_file1', $file1);
        $this->db->where('cnic_file2', $file2);
        $query = $this->db->get('tbl_userdocs');

        if ($query->num_rows() > 0){
            return true;
        }
        else
        {
            return false;
        }

    }

    function export_tocsv($status, $expiry)
    {

        $this->db->select("BaseTbl.username, UserDocs.payid, Services.srvname, BaseTbl.firstname, BaseTbl.lastname, BaseTbl.address,  BaseTbl.city, BaseTbl.mobile, BaseTbl.expiration, BaseTbl.createdon, BaseTbl.owner, Services.costprice, Services.saleprice");
        $this->db->from('rm_users as BaseTbl');
        $this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.radsrvid and BaseTbl.owner = Services.managername','left');
        $this->db->join('tbl_userdocs as UserDocs', 'BaseTbl.username = UserDocs.username','left');
        $this->db->where('BaseTbl.enableuser = ', $status);

        $managername = $this->session->userdata ( 'name' );
        if($managername <> 'admin'){
            $this->db->where('BaseTbl.owner = ', $managername);
        }

        if($expiry == 1)
        {
            $curDate = date("y-m-d");
            $this->db->where('acctype=0 and expiration > "'.$curDate.'"');
        }

        if($expiry == 0)
        {
            $curDate = date("y-m-d");
            $this->db->where('acctype=0 and expiration <= "'.$curDate.'"');
        }

        return $this->db->get();

    }

    // IMPORT USERS LISTS FROM CSV FILE
    function get_importeduserslist() {  // Get List of Un Imported Entries 
        
        $this->db->select("*");
        $this->db->from('tbl_eptransaction as BaseTbl');
        $this->db->where('posted', 0);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return FALSE;
        }
    }


    // User Package Ownership Changed
    function getUserNewServiceID($owner, $username){

        $this->db->select("srvid, radsrvid");
        $this->db->from("tbl_services");
        $this->db->where("managername", $owner);
        $this->db->where("radsrvid IN (select srvid from rm_users where username = '".$username."')");
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();;
        } else {
            return FALSE;
        }
    }


    // Users Group

    function usersGroupCount($searchText = '')
    {
        $this->db->select('BaseTbl.groupid, BaseTbl.groupname, BaseTbl.descr');
        $this->db->from('rm_usergroups as BaseTbl');

        $managername = $this->session->userdata ( 'name' );

        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function usersGroupListing($searchText = '', $page, $segment, $managerAllServices)
    {

        $managername = $this->session->userdata ( 'name' );
        //if($managername <> 'admin'){ $managerwhere = ' managername = "'.$managername.'" and '; }else{ $managerwhere = ""; }

        $this->db->select('BaseTbl.groupid, BaseTbl.groupname, BaseTbl.descr');

        $this->db->from('rm_usergroups as BaseTbl');
        
        $this->db->order_by('BaseTbl.groupname, BaseTbl.descr');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }

    function getUsersGroup()
    {
        //$managername = $this->session->userdata ( 'name' );

        $this->db->select('groupid, groupname, descr');
        $this->db->from('rm_usergroups');
        $this->db->order_by('groupname');
        //$this->db->where('managername', $managername);;
        $query = $this->db->get();
        return $query->result();

    }

    function getManagerDataList()
    {
        //$managername = $this->session->userdata ( 'name' );

        $this->db->select('grpname, grpid');
        $this->db->from('tbl_managergroup');
        $this->db->order_by('grpname');
        //$this->db->where('managername', $managername);;
        $this->db->group_by('grpname');
        $query = $this->db->get();
        
        return $query->result();

    }

    // ****** Managers Group *******//

    function managerGroupCount($searchText = '')
    {
        $this->db->select('BaseTbl.grpid, BaseTbl.grpname, BaseTbl.usergrpid, BaseTbl.managername, BaseTbl.desc');
        $this->db->from('tbl_managergroup as BaseTbl');

        $managername = $this->session->userdata ( 'name' );

        $this->db->join('rm_usergroups as Group', 'BaseTbl.usergrpid = Group.groupid','left');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.grpid  LIKE '%".$searchText."%'
                            OR  BaseTbl.grpname  LIKE '%".$searchText."%'
                            OR  BaseTbl.usergrpid  LIKE '%".$searchText."%'
                            OR  Group.groupname  LIKE '%".$searchText."%'
                            OR  BaseTbl.managername  LIKE '%".$searchText."%'
                            OR  BaseTbl.desc  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function managerGroupListing($searchText = '', $page, $segment, $managerAllServices)
    {

        $managername = $this->session->userdata ( 'name' );
        //if($managername <> 'admin'){ $managerwhere = ' managername = "'.$managername.'" and '; }else{ $managerwhere = ""; }

        $this->db->select('BaseTbl.grpid, concat(BaseTbl.grpname, " (", IF(ISNULL(dynamicnas.nasname)=1, "", dynamicnas.nasname), ")") as grpname, 
                        BaseTbl.usergrpid, Group.groupname, BaseTbl.managername, BaseTbl.desc');
        $this->db->from('tbl_managergroup as BaseTbl');
        $this->db->join('rm_usergroups as Group', 'BaseTbl.usergrpid = Group.groupid','left');
        $this->db->join('nas as dynamicnas', 'BaseTbl.grpname = dynamicnas.shortname','left');

        if(!empty($searchText)) {

            $likeCriteria = "(BaseTbl.grpid  LIKE '%".$searchText."%'
                            OR  BaseTbl.grpname  LIKE '%".$searchText."%'
                            OR  BaseTbl.usergrpid  LIKE '%".$searchText."%'
                            OR  Group.groupname  LIKE '%".$searchText."%'
                            OR  BaseTbl.managername  LIKE '%".$searchText."%'
                            OR  BaseTbl.desc  LIKE '%".$searchText."%')";
                            
            $this->db->where($likeCriteria);

        }
        
        $this->db->order_by('BaseTbl.grpname, BaseTbl.desc');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }

    function addNewManagerGroup($managerGroupInfo){

        $this->db->trans_start();
        $this->db->insert('tbl_managergroup', $managerGroupInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return True;
    }

    function managerGroupExists($managerGroup, $usergroup, $managername)
    {
        $this->db->select("grpname");
        $this->db->from("tbl_managergroup");
        $this->db->where("grpname", $managerGroup);
        $query = $this->db->get();

        return $query->result();
    }

    function updateManagerGroupInfo($groupInfo, $grpid)
    {
        $this->db->where('grpid', $grpid);
        $this->db->update('tbl_managergroup', $groupInfo);
        
        return TRUE;
    }

    function getManagerGroupInfo($grpId)
    {

        $this->db->select('grpid, grpname, usergrpid, Group.groupname as usergroupname, managername, desc, type');
        $this->db->from('tbl_managergroup as BaseTbl');
        $this->db->join('rm_usergroups as Group', 'BaseTbl.usergrpid = Group.groupid','left');

        $this->db->where('grpid', $grpId);
        $query = $this->db->get();
        
        return $query->row();

    }

    function getExistingManagerGroupsUniqueOnly()
    {

        $this->db->select('grpname');
        $this->db->from('tbl_managergroup as BaseTbl');

        $this->db->where('grpid', $grpId);
        $query = $this->db->get();
        
        return $query->row();

    }

    // ****** Prepad Cards ************ //
    function cardsBatchCount($searchText = '')
    {
        $managername = $this->session->userdata ( 'name' );
        $this->db->select('BaseTbl.owner, BaseTbl.series, count(BaseTbl.series) as cards, 
                            count( case when used = "0000-00-00 00:00:00" then 1 end ) as unsold,
                            sum( case when used = "0000-00-00 00:00:00" then 0 else value end) as sold,
                            sum(value) as value, "" as remakrs');
        $this->db->from('rm_cards as BaseTbl');

        if($managername <> 'admin'){
            $this->db->where('BaseTbl.owner = ', $managername);
        }

        $this->db->order_by('BaseTbl.owner, BaseTbl.series');
        $this->db->group_by("series");

        $managername = $this->session->userdata ( 'name' );

        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function cardsBatchListing($searchText = '', $page = 0, $segment = 0, $managerAllServices, $searchText1 = '', $searchText2 = '', $searchText3 = '')
    {

        $managername = $this->session->userdata ( 'name' );
        //if($managername <> 'admin'){ $managerwhere = ' managername = "'.$managername.'" and '; }else{ $managerwhere = ""; }
        
        $wherCondition0 = ($searchText <> '') ? " owner = '".$searchText."' " : "";
        $wherCondition1 = ($searchText1 <> '') ? " and date >= '".$searchText1."' " : "";
        $wherCondition2 = ($searchText2 <> '') ? " and date <= '".$searchText2."' " : "";

        $wherCondition = $wherCondition0.$wherCondition1.$wherCondition2; // Check for Master and Sub Managers
        $whereDateConition = $wherCondition1.$wherCondition2; // Check for Date Conditions

        $whereClause = ($searchText <> '') ? " Where " : "";
        $andClause = ($searchText <> '') ? " and " : "";

        switch ($searchText3) {
            case 1:
                $groupBy = "cards.owner";
                $orderBy = "cards.owner, cards.date desc, cards.series desc";
                break;
            
            case 2:
                $groupBy = "cards.owner, cards.series";
                $orderBy = "cards.owner, cards.date desc, cards.series desc";
                break;

            case 3:
                $groupBy = "cards.owner, cards.series, month(cards.date)";
                $orderBy = "cards.date desc, cards.series desc";
                break;
        
            default:
                $groupBy = "cards.series";
                $orderBy = "cards.date desc, cards.series desc";
        }


        if($managername <> 'admin' && $this->ismaster == 0){

            //$this->db->where('BaseTbl.owner = ', $managername);
            $sql = "SELECT series, date, expiration, owner, price, SUM(total) AS cards, SUM(unsold) AS unsold, SUM(sold) AS sold, sum(value) as value, 
                    '' as remarks, expiretime, timebaseexp, 0 as collection, COUNT(CASE WHEN revoked = 1 THEN 1 END) as revoked  
                    FROM (
                        SELECT series, date, expiration, owner, value as price, COUNT(cardnum) AS total, 0 AS unsold, 0 AS sold, sum(value) as value, expiretime, timebaseexp, revoked 
                        FROM rm_cards where owner = '$managername' 
                        GROUP BY series, owner
                        UNION ALL
                        SELECT series, date, expiration, owner, value as price, 0 AS total, COUNT(cardnum) AS unsold, 0 AS sold, sum(value) as value, expiretime, timebaseexp, revoked 
                        FROM rm_cards AS A
                        WHERE cardnum IN (SELECT username FROM rm_users WHERE expiration = A.expiration AND username = A.cardnum) and
                        owner = '$managername' 
                        GROUP BY series, owner
                        UNION ALL
                        SELECT series, date, expiration, owner, value as price, 0 AS total, 0 AS unsold, COUNT(cardnum) AS sold, COUNT(cardnum)*value as value, expiretime, timebaseexp, revoked 
                        FROM rm_cards AS A 
                        WHERE cardnum IN (SELECT username FROM rm_users WHERE username = A.cardnum) and 
                        owner = '$managername' and active = 1
                        GROUP BY series, owner
                    ) AS cards 
                    GROUP BY series
                    ORDER BY date desc, series desc";

        }else{

            if($managername == 'admin')
            {

                if($searchText <> '' && $wherCondition <> ''){
                    $wherCondition = " (".$wherCondition." OR owner in (select managername from rm_managers where mastername = '$searchText')) $whereDateConition ";
                }

                $sql = "SELECT cards.series, cards.date, cards.expiration, cards.owner, price, SUM(cards.total) AS cards, SUM(cards.unsold) AS unsold, SUM(cards.sold) AS sold, sum(cards.value) as value, 
                        '' as remarks, expiretime, timebaseexp, collections.collection as collection, COUNT(CASE WHEN revoked = 1 THEN 1 END) as revoked   
                        FROM (
                            SELECT series, date, expiration, owner, value as price, COUNT(cardnum) AS total, 0 AS unsold, 0 AS sold, 0 as value, expiretime, timebaseexp, revoked  
                            FROM rm_cards ".$whereClause.$wherCondition."
                            GROUP BY series, owner
                            UNION ALL
                            SELECT series, date, expiration, owner, value as price, 0 AS total, COUNT(cardnum) AS unsold, 0 AS sold, 0 as value, expiretime, timebaseexp, revoked 
                            FROM rm_cards AS A
                            WHERE cardnum IN (SELECT username FROM rm_users WHERE expiration = A.expiration AND username = A.cardnum)
                            ".$andClause.$wherCondition."
                            GROUP BY series, owner
                            UNION ALL
                            SELECT series, date, expiration, owner, value as price, 0 AS total, 0 AS unsold, COUNT(cardnum) AS sold, COUNT(cardnum)*value as value, expiretime, timebaseexp, revoked 
                            FROM rm_cards AS A
                            WHERE cardnum IN (SELECT username FROM rm_users WHERE username = A.cardnum) 
                            and active = 1 ".$andClause.$wherCondition."
                            GROUP BY series, owner
                        ) AS cards 
                            left join 
                        ( select series, sum(amount) as collection from tbl_cardscollection group by series) as collections on cards.series = collections.series 
                        GROUP BY $groupBy
                        ORDER BY $orderBy";

            }elseif($this->ismaster > 0){

                if($searchText <> '' && $wherCondition <> ''){
                    $wherCondition = " (".$wherCondition." OR owner in (select managername from rm_managers where mastername = '$searchText')) $whereDateConition";
                }

                $sql = "SELECT cards.series, date, expiration, owner, price, SUM(total) AS cards, SUM(unsold) AS unsold, SUM(sold) AS sold, sum(value) as value, 
                        '' as remarks, expiretime, timebaseexp, collections.collection as collection, COUNT(CASE WHEN revoked = 1 THEN 1 END) as revoked  
                        FROM (
                            SELECT series, date, expiration, owner, value as price, COUNT(cardnum) AS total, 0 AS unsold, 0 AS sold, 0 as value, expiretime, timebaseexp, revoked 
                            FROM rm_cards where  
                            owner in (select managername from rm_managers where mastername = '$managername') ".$andClause.$wherCondition."
                            GROUP BY series, owner
                            UNION ALL
                            SELECT series, date, expiration, owner, value as price, 0 AS total, COUNT(cardnum) AS unsold, 0 AS sold, 0 as value, expiretime, timebaseexp, revoked 
                            FROM rm_cards AS A
                            WHERE cardnum IN (SELECT username FROM rm_users WHERE expiration = A.expiration AND username = A.cardnum) and  
                            owner in (select managername from rm_managers where mastername = '$managername')
                            ".$andClause.$wherCondition."
                            GROUP BY series, owner
                            UNION ALL
                            SELECT series, date, expiration, owner, value as price, 0 AS total, 0 AS unsold, COUNT(cardnum) AS sold, COUNT(cardnum)*value as value, expiretime, timebaseexp, revoked 
                            FROM rm_cards AS A
                            WHERE cardnum IN (SELECT username FROM rm_users WHERE username = A.cardnum) and  
                            owner in (select managername from rm_managers where mastername = '$managername') and active = 1 
                            ".$andClause.$wherCondition."
                            GROUP BY series, owner
                        ) AS cards 
                         left join 
                        ( select series, sum(amount) as collection from tbl_cardscollection group by series) as collections on cards.series = collections.series 
                        GROUP BY $groupBy
                        ORDER BY $orderBy";

            }

        }

        $query = $this->db->query($sql);
        $result = $query->result();
        return $result;

    }

    function cardsListing($searchText = '', $seriesID)
    {

        $managername = $this->session->userdata ( 'name' );

        //echo $this->ismaster;
        //if($managername <> 'admin'){ $managerwhere = ' managername = "'.$managername.'" and '; }else{ $managerwhere = ""; }

        $this->db->select('A.id, A.cardnum, A.password, A.value, A.expiration as vilidity, A.series, A.date, A.owner, A.used, 
                        A.downlimit, A.uplimit, A.comblimit, A.uptimelimit, A.srvid, A.transid, A.active, B.enableuser, 
                        A.expiretime, A.timebaseexp, A.timebaseonline, B.expiration as expiration');

        $this->db->from('rm_cards as A');
        $this->db->join('rm_users as B', 'A.cardnum = B.username','left');

        $this->db->where('series', $seriesID);

        if($managername <> 'admin' && $this->ismaster <> 1){
            $this->db->where('A.owner = ', $managername);
        }elseif($this->ismaster == 1){
            $this->db->where('A.owner in (select managername from rm_managers where mastername = "'.$managername.'")');
        }

        $this->db->order_by('A.active, A.date', 'DESC');

        $this->db->order_by('B.expiration, A.owner, A.cardnum');

        $query = $this->db->get();
        $result = $query->result();
        return $result;

    }

    function cardsListingSummery($searchText = '', $seriesID)
    {

        $managername = $this->session->userdata ( 'name' );

        //echo $this->ismaster;
        //if($managername <> 'admin'){ $managerwhere = ' managername = "'.$managername.'" and '; }else{ $managerwhere = ""; }

        $this->db->select('A.id, count(A.cardnum) as cardcount, A.password, A.value as value, 
                        A.expiration as vilidity, A.series, A.date, A.owner, A.used, 
                        A.downlimit, A.uplimit, A.comblimit, A.uptimelimit, A.srvid, A.transid, A.active, B.enableuser, 
                        A.expiretime, A.timebaseexp, A.timebaseonline, B.expiration as expiration');

        $this->db->from('rm_cards as A');
        $this->db->join('rm_users as B', 'A.cardnum = B.username','left');

        $this->db->where('series', $seriesID);

        if($managername <> 'admin' && $this->ismaster <> 1){
            $this->db->where('A.owner = ', $managername);
        }elseif($this->ismaster == 1){
            $this->db->where('A.owner in (select managername from rm_managers where mastername = "'.$managername.'")');
        }

        $this->db->group_by('A.active');

        $this->db->order_by('B.expiration, A.owner, A.cardnum', 'DESC');

        $query = $this->db->get();
        $result = $query->result();
        return $result;

    }

    function export_cardstocsv($seriesID)
    {

        $this->db->select('A.cardnum, A.password, A.value, A.expiration, A.series, A.date, A.owner, A.used, A.expiretime, A.timebaseexp');
        
        $this->db->from('rm_cards as A');
        $this->db->where('series', $seriesID);
        $this->db->order_by('A.cardnum');

        return $this->db->get();
    }

    function genCardSeriesNumber(){

        $this->db->select('year(date) as year, max(right(series,4)) as series');
        $this->db->from('rm_cards');
        $this->db->where('year(date) = year(curdate())');
        $this->db->having('year = year(curdate())');
        //$this->db->group_by("series");
            
        $query = $this->db->get();

        return $query->row();
    }

    function getMaxId(){

        $this->db->select_max('id');
        $this->db->from('rm_cards');

        $query = $this->db->get();
        return $query->row();

    }

    function getUserExpiry(){
        
    }

    function validate_user($username){

        $query = $this->db->get_where('rm_users', ['username' => $username]);
        if ($query->num_rows() > 0) {
            $user = $query->row();

            $currentDate = date('Y-m-d H:i:s');
            if($user->expiration <= $currentDate){
                return (object)['mode' => 2];
            }elseif($user->password == MD5("")){
                return (object)['mode' => 0];
            } else {
                // Password required
                return (object)['mode' => 1];
            }
        } else {
            return false;
        }
    }
    function validate_userpassword($username, $password){

        $this->db->select('password');
        $this->db->from('rm_users');
        $this->db->where('username', $username);
        $this->db->where('password', md5($password));

        $query = $this->db->get();
        return $query->row();
    }

    public function revoke_cards_and_disable_users($series, $type) {

        // Update rm_cards table
        if ($type == 1) {
            // Only update where active = 0 for the given series
            $sql_cards = "UPDATE rm_cards 
                        SET revoked = 1 
                        WHERE series = ? AND active = 0";
        } else {
            // Update all cards in the series regardless of active status
            $sql_cards = "UPDATE rm_cards 
                        SET revoked = 1 
                        WHERE series = ?";
        }
        $this->db->query($sql_cards, [$series]);

        // Update rm_users table based on cardnums in rm_cards
        if ($type == 1) {
            // Only update users where active = 0 for the given series
            $sql_users = "UPDATE rm_users 
                        SET enableuser = 0 
                        WHERE username IN (
                            SELECT cardnum FROM rm_cards 
                            WHERE series = ? AND active = 0
                        )";
        } else {
            // Update all users where cardnums match the series
            $sql_users = "UPDATE rm_users 
                        SET enableuser = 0 
                        WHERE username IN (
                            SELECT cardnum FROM rm_cards 
                            WHERE series = ?
                        )";
        }
        $this->db->query($sql_users, [$series]);

        // Return summary of rows affected
        return [
            'cards_updated' => $this->db->affected_rows(), // Updated rows in rm_cards
            'users_updated' => $this->db->affected_rows() // Updated rows in rm_users
        ];

    }

    public function unlock_cards_and_enable_users($series, $type) {

        // Update rm_cards table
        if ($type == 1) {
            // Only update where active = 0 for the given series
            $sql_cards = "UPDATE rm_cards 
                        SET revoked = 0 
                        WHERE series = ? AND active = 0";
        } else {
            // Update all cards in the series regardless of active status
            $sql_cards = "UPDATE rm_cards 
                        SET revoked = 0 
                        WHERE series = ?";
        }
        $this->db->query($sql_cards, [$series]);

        // Update rm_users table based on cardnums in rm_cards
        if ($type == 1) {
            // Only update users where active = 0 for the given series
            $sql_users = "UPDATE rm_users 
                        SET enableuser = 1 
                        WHERE username IN (
                            SELECT cardnum FROM rm_cards 
                            WHERE series = ? AND active = 0
                        )";
        } else {
            // Update all users where cardnums match the series
            $sql_users = "UPDATE rm_users 
                        SET enableuser = 1 
                        WHERE username IN (
                            SELECT cardnum FROM rm_cards 
                            WHERE series = ?
                        )";
        }
        $this->db->query($sql_users, [$series]);

        // Return summary of rows affected
        return [
            'cards_updated' => $this->db->affected_rows(), // Updated rows in rm_cards
            'users_updated' => $this->db->affected_rows() // Updated rows in rm_users
        ];

    }

    /**
     * Get users with missing region (segmentid) or parameter (location)
     * Supports filters and paginationCompress logic
     * @param int $page (limit)
     * @param int $segment (offset)
     * @param array $filters
     * @param bool $countOnly (if true, return only total count)
     * @return array|int
     */
    public function getUsersMissingRegionOrParameter($page = 20, $segment = 0, $filters = [], $countOnly = false) {
        $managername = $this->session->userdata('name');
        $isMaster = isset($this->ismaster) ? $this->ismaster : 0;
        // --- Build query for count ---
        $this->db->from('rm_users as u');
        $this->db->join('tbl_userdocs as d', 'u.username = d.username', 'left');
        $this->db->where('u.enableuser', 1);
        $this->db->group_start();
        $this->db->where('d.segmentid IS NULL');
        $this->db->or_where('d.segmentid', '');
        $this->db->or_where('d.parameter IS NULL');
        $this->db->or_where('d.parameter', '');
        $this->db->group_end();
        if (!empty($filters['manager'])) {
            $this->db->where('u.owner', $filters['manager']);
        } else if ($managername !== 'admin') {
            if ($isMaster > 0) {
                $subQuery = $this->db->select('managername')->from('rm_managers')->where('mastername', $managername)->get_compiled_select();
                $this->db->where_in('u.owner', $subQuery, false);
            } else {
                $this->db->where('u.owner', $managername);
            }
        }
        if (!empty($filters['address'])) {
            $this->db->like('u.address', $filters['address']);
        }
        if (!empty($filters['username'])) {
            $this->db->like('u.username', $filters['username']);
        }
        if (!empty($filters['expiration_from'])) {
            $this->db->where('u.expiration >=', $filters['expiration_from']);
        }
        if (!empty($filters['expiration_to'])) {
            $this->db->where('u.expiration <=', $filters['expiration_to']);
        }
        if (!empty($filters['created_from'])) {
            $this->db->where('u.createdon >=', $filters['created_from']);
        }
        if (!empty($filters['created_to'])) {
            $this->db->where('u.createdon <=', $filters['created_to']);
        }
        $totalCount = $this->db->count_all_results();
        if ($countOnly) {
            return $totalCount;
        }
        // --- Build query for data ---
        $this->db->select('u.username, u.firstname, u.lastname, u.mobile, u.owner, u.expiration, u.createdon, u.address, d.segmentid, d.parameter');
        $this->db->from('rm_users as u');
        $this->db->join('tbl_userdocs as d', 'u.username = d.username', 'left');
        $this->db->where('u.enableuser', 1);
        $this->db->group_start();
        $this->db->where('d.segmentid IS NULL');
        $this->db->or_where('d.segmentid', '');
        $this->db->or_where('d.parameter IS NULL');
        $this->db->or_where('d.parameter', '');
        $this->db->group_end();
        if (!empty($filters['manager'])) {
            $this->db->where('u.owner', $filters['manager']);
        } else if ($managername !== 'admin') {
            if ($isMaster > 0) {
                $subQuery = $this->db->select('managername')->from('rm_managers')->where('mastername', $managername)->get_compiled_select();
                $this->db->where_in('u.owner', $subQuery, false);
            } else {
                $this->db->where('u.owner', $managername);
            }
        }
        if (!empty($filters['address'])) {
            $this->db->like('u.address', $filters['address']);
        }
        if (!empty($filters['username'])) {
            $this->db->like('u.username', $filters['username']);
        }
        if (!empty($filters['expiration_from'])) {
            $this->db->where('u.expiration >=', $filters['expiration_from']);
        }
        if (!empty($filters['expiration_to'])) {
            $this->db->where('u.expiration <=', $filters['expiration_to']);
        }
        if (!empty($filters['created_from'])) {
            $this->db->where('u.createdon >=', $filters['created_from']);
        }
        if (!empty($filters['created_to'])) {
            $this->db->where('u.createdon <=', $filters['created_to']);
        }
        $this->db->order_by('u.username');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        return [
            'totalCount' => $totalCount,
            'records' => $query->result()
        ];
    }

    /**
     * Bulk update region/parameter for users
     * @param array $usernames
     * @param int|null $segmentid
     * @param string|null $parameter
     */
    public function bulkAssignRegionParameter($usernames, $segmentid = null, $parameter = null) {
        if (empty($usernames)) return false;
        $data = [];
        if ($segmentid !== null) $data['segmentid'] = $segmentid;
        if ($parameter !== null) $data['parameter'] = $parameter;
        if (empty($data)) return false;
        $this->db->where_in('username', $usernames);
        $this->db->update('tbl_userdocs', $data);
        return $this->db->affected_rows();
    }

    /**
     * Get online status for a list of usernames (1=online, 0=offline)
     * @param array $usernames
     * @return array username => 1|0
     */
    public function getOnlineStatusForUsers($usernames)
    {
        if (empty($usernames)) return [];
        $this->db->select('username');
        $this->db->from('radacct');
        $this->db->where_in('username', $usernames);
        $this->db->where('acctstoptime IS NULL', null, false);
        $query = $this->db->get();
        $onlineUsers = array_column($query->result_array(), 'username');
        $statuses = [];
        foreach ($usernames as $u) {
            $statuses[$u] = in_array($u, $onlineUsers) ? 1 : 0;
        }
        return $statuses;
    }

    function getAttributes_radcheck($username, $attribute = "Cleartext-Password"){

        $this->db->select('username, attribute, op, value');
        $this->db->from('radcheck as A');
        $this->db->where('username', $username);
        $this->db->where('attribute', $attribute);
        
        $query = $this->db->get();

        return $query->row();

    }
}
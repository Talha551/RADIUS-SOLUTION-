<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Subscribers_model extends CI_Model
{

    function subscribersList($searchText = '', $type, $accType = 0, $owner = "admin", $srvid = 0, $page, $segment, $orderColumnIndex, $orderDir)
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
                //$subQuery = $this->db->select('rm_managers.managername')
                //            ->from('rm_managers')
                //            ->where('mastername', $managername)
                //            ->get_compiled_select();

                // Use the subquery within where_in
                //$this->db->where_in('BaseTbl.owner', $subQuery, FALSE);

                $this->db->where('BaseTbl.owner in (select managername from rm_managers where mastername = "'.$managername.'" 
                                    OR managername = "'.$managername.'")');
                //$this->db->where('BaseTbl.owner', $managername);

            } else {
                $this->db->where('BaseTbl.owner', $managername);
            }
        }

        // Simplify type-based conditions here...
        // Apply type-based filters
        $this->applyTypeFilter($type, $accType, $owner, $srvid);

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
        $this->db->order_by('BaseTbl.createdon', 'DESC');
        $this->db->limit($page, $segment);

        $query = $this->db->get();

        log_message('info', 'DB_INFO - '.$this->db->get_compiled_select());

        //return $query->result();
        return [
            'totalCount' => $totalCount,
            'records' => $query->result()
        ];
    }

    protected function applyTypeFilter($type, $accType, $owner, $srvid) {

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
                $this->db->where('BaseTbl.enableuser=1 and BaseTbl.acctype='.$accType.' and BaseTbl.expiration <="'.$curDate.'"');
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
                $this->db->where('BaseTbl.enableuser=1 and BaseTbl.acctype='.$accType.' and BaseTbl.expiration >= now() and BaseTbl.expiration <= now() + interval 1 day');
                break;
            case 10:
                $this->db->where('BaseTbl.enableuser=1 and BaseTbl.acctype='.$accType.' and BaseTbl.expiration >= now() and BaseTbl.expiration <= now() + interval 1 day');
                break;
            case 11:
                $this->db->where('BaseTbl.enableuser=1 and BaseTbl.acctype='.$accType.' and BaseTbl.expiration >= now() and BaseTbl.expiration <= now() + interval 1 day');
                break;
            case 12:
                $this->db->where('BaseTbl.enableuser=1 and BaseTbl.acctype='.$accType.' and BaseTbl.expiration >= now() - interval 7 day and BaseTbl.expiration <= now()');
                break;
        }

        if($owner <> "admin"){
            $this->db->where('BaseTbl.owner', $owner);
        }

        if($srvid <> 0){
            $this->db->where('Services.srvid', $srvid);
        }

    }


    // OLD Methods Replaced by Subscribers Methods
    function userListingCount($searchText = '', $type, $accType = 0)
    {

        $this->db->select('BaseTbl.username, BaseTbl.enableuser, BaseTbl.expiration, BaseTbl.firstname, BaseTbl.lastname, 
                            BaseTbl.mobile, BaseTbl.owner, BaseTbl.city, imgfiles.payid, 
                            BaseTbl.address, BaseTbl.city');
        $this->db->from('rm_users as BaseTbl');
        $this->db->join('rm_usergroups as Group', 'BaseTbl.groupid = Group.groupid','left');
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
        if($managername <> 'admin'){
            if($this->ismaster > 0){
                $this->db->where('BaseTbl.owner in (Select managername from rm_managers where mastername = "'.$managername.'")');
            }else{
                $this->db->where('BaseTbl.owner = ', $managername); 
            }
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
            $this->db->where('BaseTbl.enableuser=', 0);
        
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
        if($managername <> 'admin'){
            if($this->ismaster > 0){
                $this->db->where('BaseTbl.owner in (Select managername from rm_managers where mastername = "'.$managername.'")');
            }else{
                $this->db->where('BaseTbl.owner = ', $managername); 
            }
        }

        if($type == 0)
            $this->db->where('BaseTbl.enableuser ', 1);

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

    function getResellerPackages($managername = NULL)
    {
        if($managername == NULL){
            $managername = $this->session->userdata ( 'name' );
        }

        $this->db->select('srvid, srvname, radsrvid, costprice as unitprice, saleprice, managername');
        $this->db->from('tbl_services');
        if($managername <> 'admin'){
            $this->db->where('managername', $managername);
        }
        $query = $this->db->get();
        
        return $query->result();
    }

    function getUserInfo($userId, $accType = 0)
    {

        $this->db->select('A.username, A.password, A.enableuser, A.uplimit, A.downlimit, A.comblimit, A.firstname, A.lastname, A.company, A.mobile,
                        A.address, A.city, A.email, A.country, A.state, A.taxid, A.srvid, B.srvname, A.gpslat, A.gpslong, A.mac, A.usemacauth, 
                        A.expiration, A.uptimelimit, A.owner, A.createdon, A.acctype, A.comment, A.phone, A.zip,
                        A.ipmodecm, A.ipmodecpe, A.poolidcm, A.poolidcpe, A.staticipcpe, A.maccm, A.custattr, A.verified, A.selfreg, 
                        A.verifyfails, A.verifysentnum, A.verifymobile, A.contractid, A.contractvalid,
                        A.alertsms, A.alertemail,
                        B.trafficunitdl, B.trafficunitul, B.trafficunitcomb,B.dlburstlimit,B.ulburstlimit,
                        C.srvname, C.baseprice, C.costprice, C.saleprice');

        $this->db->from('rm_users as A');
        $this->db->join('rm_services as B', 'A.srvid = B.srvid','left');
        $this->db->join('tbl_services as C', 'B.srvid = C.radsrvid','left');
		//$this->db->where('acctype =', $accType); // Removed due to Prepaid Card Types
        $this->db->where('username', $userId);
        $query = $this->db->get();
        
        return $query->row();

    }

    function update_subscriber($data)
    {
        $this->db->where('username', $data['username']);
        return $this->db->update('rm_users', $data); // Update user table
    }

    function update_subscriberDocs($data)
    {
        $this->db->where('username', $data['username']);
        return $this->db->update('rm_users', $data); // Update user table
    }

    function getAttributes_radcheck($username, $attribute = "Cleartext-Password"){

        $this->db->select('username, attribute, op, value');
        $this->db->from('radcheck as A');
        $this->db->where('username', $username);
        $this->db->where('attribute', $attribute);
        
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

        $this->db->select('managername, firstname, lastname');
        $this->db->from('rm_managers');
        $this->db->where('managername <>', 'admin');

        if($managername <> 'admin'){
            $this->db->where('mastername', $managername);
        }
        $query = $this->db->get();
        
        return $query->result();
    }

    function getUsersList($owner = "")
    {
        $managername = $this->session->userdata ( 'name' );

        $this->db->select('username, firstname, lastname');
        $this->db->from('rm_users');

        if($managername <> 'admin'){
            $this->db->where('owner', $managername); // This condition is for logged in Manager
        }elseif($owner !== ''){
            $this->db->where('owner', $owner); // if Owner required to filter then this condition will apply
        }
        $query = $this->db->get();
        
        return $query->result();
    }

    function getManagerInfo($managername)
    {
        $this->db->select('managername, perm_listusers, perm_createusers, perm_editusers, perm_edituserspriv, perm_deleteusers, 
                        perm_listinvoices, perm_listmanagers, perm_createmanagers, perm_editmanagers, perm_deletemanagers, perm_listservices,
                        perm_createservices, perm_editservices, perm_deleteservices, perm_listonlineusers, perm_listinvoices,
                        perm_trafficreport, perm_addcredits, perm_negbalance, perm_listallinvoices, perm_showinvtotals, perm_logout,
                        perm_cardsys, perm_editinvoice, perm_allusers, perm_allowdiscount, perm_enwriteoff, perm_accessap, perm_cts, enablemanager,
                        perm_trafficreport, perm_addcredits, perm_allowdiscount, enablemanager, 
                        perm_createservices, perm_editservices');
        $this->db->from('rm_managers');
        $this->db->where('managername', $managername);
        $query = $this->db->get();
        return $query->row();
    }

    function checkManagerExist($manager)
    {
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
        $this->db->where('username', $user);
        $this->db->update('rm_users', $userInfo);
        
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
        inst_name, inst_box, inst_wifi, inst_fiber, inst_meter, inst_chrg, inst_cost, inst_disc, inst_box_sn');
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
        $this->db->or_where('payid', '');
        
        $query = $this->db->get('tbl_userdocs');
        

        if ($query->num_rows() > 0){

            // Generate Unique Payment ID
            for ($x = 0; $x <= 1000; $x++) {
                $key = rand(0, 9999);
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

        $this->db->select("BaseTbl.username, UserDocs.payid, Services.srvname, BaseTbl.firstname, BaseTbl.lastname, BaseTbl.address, BaseTbl.city, BaseTbl.expiration, BaseTbl.createdon, BaseTbl.owner, Services.costprice, Services.saleprice");
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
        $this->db->join('tbl_dynamicnas as dynamicnas', 'BaseTbl.grpname = dynamicnas.shortname','left');

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

    function cardsBatchListing($searchText = '', $page, $segment, $managerAllServices)
    {

        $managername = $this->session->userdata ( 'name' );
        //if($managername <> 'admin'){ $managerwhere = ' managername = "'.$managername.'" and '; }else{ $managerwhere = ""; }

        $sql = "SELECT series, owner, SUM(total) AS cards, SUM(unsold) AS unsold, SUM(sold) AS sold, sum(value) as value, '' as remarks 
                FROM (
                    SELECT series, owner, COUNT(cardnum) AS total, 0 AS unsold, 0 AS sold, sum(value) as value 
                    FROM rm_cards
                    GROUP BY series, owner
                    UNION ALL
                    SELECT series, owner, 0 AS total, COUNT(cardnum) AS unsold, 0 AS sold, sum(value) as value 
                    FROM rm_cards AS A
                    WHERE cardnum IN (SELECT username FROM rm_users WHERE expiration = A.expiration AND username = A.cardnum)
                    GROUP BY series, owner
                    UNION ALL
                    SELECT series, owner, 0 AS total, 0 AS unsold, COUNT(cardnum) AS sold, sum(value) as value 
                    FROM rm_cards AS A
                    WHERE cardnum IN (SELECT username FROM rm_users WHERE expiration <> A.expiration AND username = A.cardnum)
                    GROUP BY series, owner
                ) AS cards
                GROUP BY series, owner
                ORDER BY series, owner";

                $query = $this->db->query($sql);
                $result = $query->result();

                return $result;

    }

    function cardsListing($searchText = '', $seriesID)
    {

        $managername = $this->session->userdata ( 'name' );
        //if($managername <> 'admin'){ $managerwhere = ' managername = "'.$managername.'" and '; }else{ $managerwhere = ""; }

        $this->db->select('A.id, A.cardnum, A.password, A.value, A.expiration, A.series, A.date, A.owner, A.used, 
                        A.downlimit, A.uplimit, A.comblimit, A.uptimelimit, A.srvid, A.transid, A.active, 
                        A.expiretime, A.timebaseexp, A.timebaseonline');

        $this->db->from('rm_cards as A');
        $this->db->where('series', $seriesID);

        if($managername <> 'admin'){
            $this->db->where('A.owner = ', $managername);
        }
        
        $this->db->order_by('A.cardnum');

        $query = $this->db->get();
        $result = $query->result();
        return $result;

    }

    function export_cardstocsv($seriesID)
    {

        $this->db->select('A.cardnum, A.password, A.value, A.expiration, A.series, A.date, A.owner, A.used');
        
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
            // Assume 'password' is a field in your users table. Adjust as necessary.
            if ($user->password == MD5("")) {
                // No password required
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

}
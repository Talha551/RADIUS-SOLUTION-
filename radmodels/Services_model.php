<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Services_model extends CI_Model
{

    function servicesCount($searchText = '', $type, $managerAllServices)
    {
        $this->db->select('BaseTbl.srvname, BaseTbl.radsrvid, BaseTbl.baseprice, BaseTbl.costprice, BaseTbl.saleprice,
                            SrvTbl.srvid, SrvTbl.srvname');
        $this->db->from('tbl_services as BaseTbl');
        $this->db->join('rm_services as SrvTbl', 'BaseTbl.radsrvid = SrvTbl.srvid','left');

        //$this->db->where('UserTbl.enableuser = ', 1);
        //$this->db->where('UserTbl.username in (select username from radacct where YEARWEEK(acctstarttime) > YEARWEEK(NOW() - INTERVAL 2 WEEK))');
        $curDate = date("y-m-d");

        
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.srvname  LIKE '%".$searchText."%'
                            OR  SrvTbl.srvname  LIKE '%".$searchText."%'
                            OR  BaseTbl.managername  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        //$managername = $this->session->userdata ( 'name' );
        //$this->db->where('SrvTbl.owner = ', $managername);
        //$this->db->where('BaseTbl.acctype =', 0);
        $managername = $this->session->userdata ( 'name' );
        //$this->db->where('UsrTbl.owner = ', $managername);
        if($managerAllServices == 0)
        {
            $this->db->where('BaseTbl.managername = ', $managername);
        }elseif($managerAllServices == 1 && $managername <> 'admin'){
            $this->db->where('BaseTbl.managername in (Select managername from rm_managers where mastername="'.$managername.'")');
            $this->db->where('BaseTbl.managername', $type);
            //if(!empty($managerFilter)){ $this->db->where('BaseTbl.managername', $managerFilter); };
        }

        if($managername == 'admin'){
            $this->db->where('BaseTbl.managername', $type);
        }

        $this->db->group_by('BaseTbl.managername, BaseTbl.srvid');

        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function servicesListing($searchText = '', $type, $page, $segment, $managerAllServices)
    {
        $curDate = date("y-m-d");
        $this->db->query('Set @row_number = 0');
        $this->db->select('BaseTbl.srvid, BaseTbl.srvname, BaseTbl.managername, BaseTbl.radsrvid, BaseTbl.baseprice as baseprice, BaseTbl.costprice, BaseTbl.saleprice,
                            SrvTbl.srvname as radsrvname, 
                            (select count(username) as users from rm_users where srvid=BaseTbl.radsrvid and owner=BaseTbl.managername and enableuser=1 and expiration <= "'.$curDate.'") as NoOfUsers, 
                            BaseTbl.costprice * (select count(username) as users from rm_users where srvid=BaseTbl.radsrvid and owner=BaseTbl.managername) as Cost, 
                            BaseTbl.saleprice * (select count(username) as users from rm_users where srvid=BaseTbl.radsrvid and owner=BaseTbl.managername) as Sales,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('tbl_services as BaseTbl');
        $this->db->join('rm_services as SrvTbl', 'BaseTbl.radsrvid = SrvTbl.srvid','left');   
        
        //$this->db->where('UserTbl.enableuser = ', 1);
        //$this->db->where('UserTbl.username in (select username from radacct where YEARWEEK(acctstarttime) > YEARWEEK(NOW() - INTERVAL 10 WEEK))');

        $curDate = date("y-m-d");        

        //if($type == 0)
        //    $this->db->where('UserTbl.acctype=0 and UserTbl.expiration > "'.$curDate.'"');

        //if($type == 1)
        //    $this->db->where('UserTbl.username in (select username from radacct where isnull(acctstoptime)=TRUE)');


        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.srvname  LIKE '%".$searchText."%'
                            OR  SrvTbl.srvname  LIKE '%".$searchText."%'
                            OR  BaseTbl.managername  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        $managername = $this->session->userdata ( 'name' );
        //$this->db->where('UsrTbl.owner = ', $managername);
        if($managername == $type){
            $this->db->where('BaseTbl.managername = ', $type);
        }elseif($managerAllServices == 0)
        {
            $this->db->where('BaseTbl.managername = ', $managername);
        }elseif($managerAllServices == 1 && $managername <> 'admin'){
            $this->db->where('BaseTbl.managername in (Select managername from rm_managers where mastername="'.$managername.'")');
            $this->db->where('BaseTbl.managername', $type);
            //if(!empty($managerFilter)){ $this->db->where('BaseTbl.managername', $managerFilter); };
        }

        if($managername == 'admin'){
            $this->db->where('BaseTbl.managername', $type);
        }

        $this->db->group_by('BaseTbl.managername, BaseTbl.srvid');
        $this->db->order_by('serial_number');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;
    }

    function checkServiceExists($srvname, $manager)
    {
        $this->db->select('srvname');
        $this->db->from("tbl_services");
        $this->db->where('srvname', $srvname);
        $this->db->where('managername', $manager);

        $query = $this->db->get();

        if ($query->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }

    function checkBaseServiceExists($manager, $radsrvid)
    {
        $this->db->select('radsrvid');
        $this->db->from("tbl_services");
        $this->db->where('radsrvid', $radsrvid);
        $this->db->where('managername', $manager);

        if($this->ismaster == 1 && $this->session->userdata ( 'name' ) <> 'admin'){
            $this->db->where('radsrvid in (select radsrvid from tbl_services where managername = "'.$manager.'")');
        }

        $query = $this->db->get();

        if ($query->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }

    function checkMasterServiceExists($manager, $radsrvid)
    {

        $managersInfo = $this->db->get_where('rm_managers', ['managername' => $manager])->row();

        $this->db->select('radsrvid');
        $this->db->from("tbl_services");
        $this->db->where('radsrvid', $radsrvid);

        if(strtoupper($managersInfo->mastername) <> 'NONE' && $managersInfo->mastername <> '0'){
            $this->db->where('managername', $managersInfo->mastername);
            $this->db->where('radsrvid in (select srvid from rm_allowedmanagers where managername = "'.$managersInfo->mastername.'")');
        }

        $this->db->where('radsrvid in (select srvid from rm_allowedmanagers where managername = "'.$manager.'")');

        $query = $this->db->get();

        if (strtoupper($managersInfo->mastername) == 'NONE' || $managersInfo->mastername == '0' || $query->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }

    function addService($serviceInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_services', $serviceInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return True;
    }

    function getServiceInfo($srvid)
    {
        // First get the service information
        $this->db->select('srvid, srvname, radsrvid, managername, baseprice, costprice, saleprice');
        $this->db->from('tbl_services');
        $this->db->where('srvid', $srvid);
        $this->db->order_by('srvname', 'ASC');
        $query = $this->db->get();
        $service = $query->row();

        if (!$service) {
            return null;
        }

        // Function to check if radsrvid exists for a manager
        $checkRadsrvidExists = function($managername, $radsrvid) {
            $this->db->select('srvid');
            $this->db->from('tbl_services');
            $this->db->where('managername', $managername);
            $this->db->where('radsrvid', $radsrvid);
            return $this->db->get()->num_rows() > 0;
        };

        // Function to get all managers in the hierarchy chain
        $getManagerChain = function($managername) {
            $chain = array($managername);
            $currentManager = $managername;
            
            while (true) {
                $this->db->select('mastername');
                $this->db->from('rm_managers');
                $this->db->where('managername', $currentManager);
                $manager = $this->db->get()->row();
                
                if (!$manager || strtoupper($manager->mastername) == 'NONE' || $manager->mastername == '0') {
                    break; // No more masters in chain
                }
                
                $chain[] = $manager->mastername;
                $currentManager = $manager->mastername;
            }
            
            return $chain;
        };

        // Get the entire manager chain
        $managerChain = $getManagerChain($service->managername);
        
        // Check if radsrvid exists for all managers in the chain
        foreach ($managerChain as $manager) {
            if (!$checkRadsrvidExists($manager, $service->radsrvid)) {
                log_message('debug', 'Service validation failed: radsrvid ' . $service->radsrvid . 
                          ' does not exist for manager ' . $manager . ' in the chain');
                return null; // radsrvid doesn't exist for one of the managers in chain
            }
        }

        return $service;
    }

    function editServiceInfo($srvid)
    {
        // First get the service information
        $this->db->select('srvid, srvname, radsrvid, managername, baseprice, costprice, saleprice');
        $this->db->from('tbl_services');
        $this->db->where('srvid', $srvid);
        $this->db->order_by('srvname', 'ASC');
        $query = $this->db->get();
        $service = $query->row();

        return $service;
    }

    function updateService($serviceInfo, $srvid)
    {
        // First, get the current service details to identify the manager
        $current_service = $this->db->get_where('tbl_services', ['srvid' => $srvid])->row();
        
        if ($current_service) {
            // Start transaction
            $this->db->trans_start();
            
            try {
                // Update the main service
                $this->db->where('srvid', $srvid);
                $this->db->update('tbl_services', $serviceInfo);
                
                // If baseprice is being updated and this is a master reseller's service
                if (isset($serviceInfo['baseprice']) && $current_service->managername) {
                    // Get all sub-resellers for this master
                    $this->db->select('managername');
                    $this->db->from('rm_managers');
                    $this->db->where('mastername', $current_service->managername);
                    $sub_resellers = $this->db->get()->result();
                    
                    if ($sub_resellers) {
                        foreach ($sub_resellers as $reseller) {
                            // Update the base price for matching services of sub-resellers
                            $this->db->where('managername', $reseller->managername);
                            $this->db->where('radsrvid', $current_service->radsrvid);
                            
                            // Get current service price for comparison
                            $current_sub_service = $this->db->get_where('tbl_services', [
                                'managername' => $reseller->managername,
                                'radsrvid' => $current_service->radsrvid
                            ])->row();
                            
                            // Only update if new baseprice is higher than current
                            if ($current_sub_service && $serviceInfo['baseprice'] > $current_sub_service->baseprice) {
                                $this->db->where('radsrvid', $current_service->radsrvid);
                                $this->db->where('managername', $reseller->managername);
                                $this->db->update('tbl_services', [
                                    'baseprice' => $serviceInfo['baseprice'],
                                    'costprice' => $serviceInfo['costprice'],
                                    'saleprice' => $serviceInfo['saleprice']
                                ]);
                                
                                // Log the update
                                log_message('debug', 'Updated baseprice for sub-reseller: ' . $reseller->managername . 
                                          ' service: ' . $current_service->srvname . 
                                          ' new baseprice: ' . $serviceInfo['baseprice']);
                            } else {
                                log_message('debug', 'Skipped price update for sub-reseller: ' . $reseller->managername . 
                                          ' service: ' . $current_service->srvname . 
                                          ' current baseprice: ' . $current_sub_service->baseprice . 
                                          ' new baseprice: ' . $serviceInfo['baseprice']);
                            }
                        }
                    }
                }
                
                $this->db->trans_complete();
                
                if ($this->db->trans_status() === FALSE) {
                    log_message('error', 'Failed to update service and sub-reseller services');
                    return FALSE;
                }
                
                return TRUE;
                
            } catch (Exception $e) {
                $this->db->trans_rollback();
                log_message('error', 'Error updating service: ' . $e->getMessage());
                return FALSE;
            }
        }
        
        return FALSE;
    }

    function updateUserServices($radServiceInfo, $radsrvid, $pradsrvid, $manager)
    {

        $this->db->where('srvid', $pradsrvid);
        $this->db->where('owner', $manager);
        $this->db->update('rm_users', $radServiceInfo);
        return $this->db->affected_rows();

    }

    function getServiceDownloadInfo($srvid)
    {
        $this->db->select('B.trafficunitdl, B.trafficunitul, B.trafficunitcomb');
        $this->db->from('tbl_services as A');
        $this->db->join('rm_services as B', 'A.radsrvid = B.srvid','left');
        $this->db->where('A.srvid', $srvid);

        $query = $this->db->get();
        return $query->row();

    }

    function getTrafficData($user)
    {
        $this->db->select('downlimit, uplimit');
        $this->db->select_sum('acctinputoctets');
        $this->db->select_sum('acctoutputoctets');
        $this->db->from('rm_users as A');
        $this->db->join('radacct as B', 'A.username = B.username', 'left');
        $this->db->where('A.username', $user);
        $this->db->group_by('A.username');

        $query = $this->db->get();
        return $query->row();

    }

    function resetDataPlan($user)
    {
        
        //$query1 = $this->db->query('insert into radacctbak select * from radacct where username ="'.$user.'" 
        //                            and radacctid not in (Select radacctid from radacctbak)');

        $query2 = $this->db->query('delete from radacct where username ="'.$user.'"');

    }

    function services_tocsv($status, $expiry)
    {
        $where_clause = "srvid=BaseTbl.radsrvid and owner=BaseTbl.managername";
        $curDate = date("y-m-d");
        if($expiry == 1){
            $where_clause = "srvid=BaseTbl.radsrvid and owner=BaseTbl.managername and acctype=0 and expiration > '".$curDate."'";
        }
        if($expiry == 0){
            $where_clause = "srvid=BaseTbl.radsrvid and owner=BaseTbl.managername and acctype=0 and expiration <= '".$curDate."'";
        }

        $query = "srvname, managername, costprice, saleprice, 
        (select count(username) as users from rm_users where ".$where_clause.") as NoOfUsers,
        BaseTbl.costprice * (select count(username) as users from rm_users where ".$where_clause.") as Cost, 
        BaseTbl.saleprice * (select count(username) as users from rm_users where ".$where_clause.") as Sales";

        //echo $query;
        //exit;

        $this->db->select("srvname, managername, costprice, saleprice, 
                (select count(username) as users from rm_users where ".$where_clause.") as NoOfUsers,
                BaseTbl.costprice * (select count(username) as users from rm_users where ".$where_clause.") as Cost, 
                BaseTbl.saleprice * (select count(username) as users from rm_users where ".$where_clause.") as Sales");

        $this->db->from('tbl_services as BaseTbl');

        $managername = $this->session->userdata ( 'name' );
        if($managername <> 'admin'){
            $this->db->where('BaseTbl.managername = ', $managername);
        }

        return $this->db->get();

    }

    function getDefaultManagerFilter($mastername){
        $this->db->select('managername');
        $this->db->from('rm_managers as A');

        if($mastername <> 'admin'){ 
            $this->db->where('mastername', $mastername);
        }

        $this->db->order_by('managername', 'ASC');

        $query = $this->db->get();
        return $query->row();
    }

    function getPrepaidPackages($managername)
    {
        $this->db->select('srvid, srvname, radsrvid, costprice as unitprice, saleprice');
        $this->db->from('tbl_services');
        
        //if($managername <> "admin"){
            $this->db->where('managername', $managername);
        //}
        $this->db->where('radsrvid in (Select srvid from rm_services where srvtype = 1)');

        $this->db->order_by('srvname', 'ASC');

        $query = $this->db->get();
        
        return $query->result();
    }

    function getPackageDetails($managername, $srvid)
    {
        $this->db->select('srvid, srvname, radsrvid, baseprice, costprice as unitprice, saleprice');
        $this->db->from('tbl_services');
        
        //if($managername <> "admin"){
            $this->db->where('managername', $managername);
        //}
        $this->db->where('radsrvid', $srvid);

        $query = $this->db->get();
        return $query->row();
    }

    /**
     * This function is used to get the service profile listing count
     * @param string $searchText : This is optional search text
     * @return number $count : This is row count
     */
    function spListingCount($searchText = '')
    {
        $this->db->select('BaseTbl.srvid, BaseTbl.srvname, BaseTbl.descr, BaseTbl.downrate, BaseTbl.uprate, BaseTbl.enableservice');
        $this->db->from('rm_services as BaseTbl');
        
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.srvname LIKE '%".$searchText."%'
                            OR BaseTbl.descr LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        
        $managername = $this->session->userdata('name');
        if($managername <> 'admin'){
            $this->db->join('rm_allowedmanagers as AM', 'BaseTbl.srvid = AM.srvid', 'left');
            $this->db->where('AM.managername', $managername);
        }
        
        $query = $this->db->get();
        
        return $query->num_rows();
    }

    /**
     * This function is used to get the service profile listing
     * @param string $searchText : This is optional search text
     * @param number $page : This is pagination offset
     * @param number $segment : This is pagination limit
     * @return array $result : This is result
     */
    function spListing($searchText = '', $page, $segment)
    {
        $this->db->select('BaseTbl.srvid, BaseTbl.srvname, BaseTbl.descr, round(((BaseTbl.downrate/1024)/1024), 0) as downrate, round((BaseTbl.uprate/1024)/1024, 0) as uprate, BaseTbl.enableservice');
        $this->db->from('rm_services as BaseTbl');
        
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.srvname LIKE '%".$searchText."%'
                            OR BaseTbl.descr LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        
        $managername = $this->session->userdata('name');
        if($managername <> 'admin'){
            $this->db->join('rm_allowedmanagers as AM', 'BaseTbl.srvid = AM.srvid', 'left');
            $this->db->where('AM.managername', $managername);
        }
        
        $this->db->order_by('BaseTbl.srvid', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;
    }

    /**
     * This function is used to check whether service profile already exists or not
     * @param string $service : This is service name
     * @param string $manager : This is manager name
     * @return boolean
     */
    function spCheckServiceExists($service, $manager)
    {
        $this->db->select('BaseTbl.srvid');
        $this->db->from('rm_services as BaseTbl');
        $this->db->join('rm_allowedmanagers as AM', 'AM.srvid = BaseTbl.srvid', 'left');
        $this->db->where('BaseTbl.srvname', $service);
        $this->db->where('AM.managername', $manager);
        $query = $this->db->get();
        
        return $query->num_rows() > 0;
    }

    /**
     * This function is used to check whether base service profile already exists or not
     * @param string $manager : This is manager name
     * @param number $srvid : This is service id
     * @return boolean
     */
    function spCheckBaseServiceExists($manager, $srvid)
    {
        $this->db->select('BaseTbl.srvid');
        $this->db->from('rm_services as BaseTbl');
        $this->db->join('rm_allowedmanagers as AM', 'AM.srvid = BaseTbl.srvid', 'left');
        $this->db->where('BaseTbl.srvid', $srvid);
        $this->db->where('AM.managername', $manager);
        $query = $this->db->get();
        
        return $query->num_rows() > 0;
    }

    /**
     * This function is used to add new service profile
     * @param array $serviceInfo : This is service profile information
     * @return number $insert_id : This is last inserted id
     */
    function spAddNewService($serviceInfo)
    {
        // Convert values to integers where needed
        $intFields = array(
            'timeaddmodeexp', 'timeaddmodeonline', 'trafficaddmode',
            'timebaseexp', 'timeunitexp', 'inittimeexp',
            'timebaseonline', 'timeunitonline', 'inittimeonline',
            'trafficunitdl', 'initdl', 'trafficunitul', 'initul',
            'trafficunitcomb', 'inittotal', 'minamount', 'minamountadd', 'addamount'
        );

        foreach ($intFields as $field) {
            if (isset($serviceInfo[$field])) {
                $serviceInfo[$field] = intval($serviceInfo[$field]);
            }
        }

        // Add expiration fields to serviceInfo if not present
        $defaultFields = array(
            'timeaddmodeexp' => isset($serviceInfo['timeaddmodeexp']) ? $serviceInfo['timeaddmodeexp'] : 1,
            'timeaddmodeonline' => isset($serviceInfo['timeaddmodeonline']) ? $serviceInfo['timeaddmodeonline'] : 1,
            'trafficaddmode' => isset($serviceInfo['trafficaddmode']) ? $serviceInfo['trafficaddmode'] : 1,
            'timebaseexp' => isset($serviceInfo['timebaseexp']) ? $serviceInfo['timebaseexp'] : 2,
            'timeunitexp' => isset($serviceInfo['timeunitexp']) ? $serviceInfo['timeunitexp'] : 0,
            'inittimeexp' => isset($serviceInfo['inittimeexp']) ? $serviceInfo['inittimeexp'] : 0,
            'timebaseonline' => isset($serviceInfo['timebaseonline']) ? $serviceInfo['timebaseonline'] : 0,
            'timeunitonline' => isset($serviceInfo['timeunitonline']) ? $serviceInfo['timeunitonline'] : 0,
            'inittimeonline' => isset($serviceInfo['inittimeonline']) ? $serviceInfo['inittimeonline'] : 0,
            'trafficunitdl' => isset($serviceInfo['trafficunitdl']) ? $serviceInfo['trafficunitdl'] : 0,
            'initdl' => isset($serviceInfo['initdl']) ? $serviceInfo['initdl'] : 0,
            'trafficunitul' => isset($serviceInfo['trafficunitul']) ? $serviceInfo['trafficunitul'] : 0,
            'initul' => isset($serviceInfo['initul']) ? $serviceInfo['initul'] : 0,
            'trafficunitcomb' => isset($serviceInfo['trafficunitcomb']) ? $serviceInfo['trafficunitcomb'] : 0,
            'inittotal' => isset($serviceInfo['inittotal']) ? $serviceInfo['inittotal'] : 0,
            'minamount' => isset($serviceInfo['minamount']) ? $serviceInfo['minamount'] : 1,
            'minamountadd' => isset($serviceInfo['minamountadd']) ? $serviceInfo['minamountadd'] : 0,
            'addamount' => isset($serviceInfo['addamount']) ? $serviceInfo['addamount'] : 1
        );

        $serviceInfo = array_merge($defaultFields, $serviceInfo);
        
        $this->db->trans_start();
        $this->db->insert('rm_services', $serviceInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return $insert_id;
    }

    /**
     * This function is used to get service profile information by id
     * @param number $srvid : This is service profile id
     * @return array $result : This is service profile information
     */
    function spGetServiceInfo($srvid)
    {
        $this->db->select('srvid, srvname, descr, (downrate/1024)/1024 as downrate, (uprate/1024)/1024 as uprate, limitdl, limitul, limitcomb, limitexpiration, limituptime, poolname, enableservice, srvtype, enableburst, dlburstlimit, ulburstlimit, dlburstthreshold, ulburstthreshold, dlbursttime, ulbursttime,
        pricecalcdownload, pricecalcupload, pricecalcuptime, monthly, renew, carryover, resetcounters, enaddcredits, unitpricetax, unitpriceaddtax, unitpriceadd,
        timeaddmodeexp, timeaddmodeonline, trafficaddmode, timebaseexp, timeunitexp, inittimeexp, timebaseonline, timeunitonline, inittimeonline, trafficunitdl, initdl, trafficunitul, initul, trafficunitcomb, inittotal, minamount, minamountadd, addamount');
        $this->db->from('rm_services');
        $this->db->where('srvid', $srvid);
        $query = $this->db->get();
        
        return $query->row();
    }

    /**
     * This function is used to get allowed managers for a service profile
     * @param number $srvid : This is service profile id
     * @return array $result : This is allowed managers list
     */
    function spGetAllowedManagers($srvid)
    {
        $this->db->select('managername');
        $this->db->from('rm_allowedmanagers');
        $this->db->where('srvid', $srvid);
        $query = $this->db->get();
        
        $result = array();
        foreach($query->result() as $row) {
            $result[] = $row->managername;
        }
        
        return $result;
    }

    /**
     * This function is used to get allowed NASes for a service profile
     * @param number $srvid : This is service profile id
     * @return array $result : This is allowed NASes list
     */
    function spGetAllowedNases($srvid)
    {
        $this->db->select('nasid');
        $this->db->from('rm_allowednases');
        $this->db->where('srvid', $srvid);
        $query = $this->db->get();
        
        $result = array();
        foreach($query->result() as $row) {
            $result[] = $row->nasid;
        }
        
        return $result;
    }

    /**
     * This function is used to update the service profile information
     * @param array $serviceInfo : This is service profile updated information
     * @param number $srvid : This is service profile id
     * @return boolean $result : TRUE / FALSE
     */
    function spEditService($serviceInfo, $srvid)
    {
        $this->db->where('srvid', $srvid);
        $this->db->update('rm_services', $serviceInfo);
        
        return $this->db->affected_rows() > 0;
    }

    /**
     * This function is used to update allowed managers for a service profile
     * @param number $srvid : This is service profile id
     * @param array $managers : This is allowed managers list
     * @return boolean $result : TRUE / FALSE
     */
    function spUpdateAllowedManagers($srvid, $managers = array())
    {
        // First delete all existing allowed managers for this service profile
        $this->db->where('srvid', $srvid);
        $this->db->delete('rm_allowedmanagers');
        
        // Then insert new allowed managers
        if (!empty($managers)) {
            $data = array();
            foreach ($managers as $manager) {
                $data[] = array(
                    'srvid' => $srvid,
                    'managername' => $manager
                );
            }
            
            return $this->db->insert_batch('rm_allowedmanagers', $data);
        }
        
        return true;
    }

    /**
     * This function is used to update allowed NASes for a service profile
     * @param number $srvid : This is service profile id
     * @param array $nases : This is allowed NASes list
     * @return boolean $result : TRUE / FALSE
     */
    function spUpdateAllowedNases($srvid, $nases)
    {
        // Delete existing allowed NASes
        $this->db->where('srvid', $srvid);
        $this->db->delete('rm_allowednases');
        
        // Add new allowed NASes
        if(!empty($nases)) {
            foreach($nases as $nas) {
                $nasInfo = array(
                    'srvid' => $srvid,
                    'nasid' => $nas
                );
                $this->db->insert('rm_allowednases', $nasInfo);
            }
        }
        
        return true;
    }

    /**
     * This function is used to delete the service profile
     * @param number $srvid : This is service profile id
     * @param array $serviceInfo : This is service profile information
     * @return boolean $result : TRUE / FALSE
     */
    function spDeleteService($srvid, $serviceInfo)
    {
        $this->db->where('srvid', $srvid);
        $this->db->update('rm_services', $serviceInfo);
        
        return $this->db->affected_rows();
    }

    /**
     * This function is used to get the list of NASes
     * @return array $result : This is result
     */
    function getNases()
    {
        $this->db->select('id as nasid, shortname as nasname, nasname as ipaddress');
        $this->db->from('nas');
        $this->db->order_by('shortname', 'ASC');
        $query = $this->db->get();
        
        return $query->result();
    }

    /**
     * This function is used to update the service profile information
     * @param array $serviceInfo : This is service profile updated information
     * @param number $srvid : This is service profile id
     * @return boolean $result : TRUE / FALSE
     */
    function spUpdateService($serviceInfo, $srvid)
    {
        // Convert values to integers where needed
        $intFields = array(
            'timeaddmodeexp', 'timeaddmodeonline', 'trafficaddmode',
            'timebaseexp', 'timeunitexp', 'inittimeexp',
            'timebaseonline', 'timeunitonline', 'inittimeonline',
            'trafficunitdl', 'initdl', 'trafficunitul', 'initul',
            'trafficunitcomb', 'inittotal', 'minamount', 'minamountadd', 'addamount'
        );

        foreach ($intFields as $field) {
            if (isset($serviceInfo[$field])) {
                $serviceInfo[$field] = intval($serviceInfo[$field]);
            }
        }

        // Add expiration fields to serviceInfo if not present
        $defaultFields = array(
            'timeaddmodeexp' => isset($serviceInfo['timeaddmodeexp']) ? $serviceInfo['timeaddmodeexp'] : 1,
            'timeaddmodeonline' => isset($serviceInfo['timeaddmodeonline']) ? $serviceInfo['timeaddmodeonline'] : 1,
            'trafficaddmode' => isset($serviceInfo['trafficaddmode']) ? $serviceInfo['trafficaddmode'] : 1,
            'timebaseexp' => isset($serviceInfo['timebaseexp']) ? $serviceInfo['timebaseexp'] : 2,
            'timeunitexp' => isset($serviceInfo['timeunitexp']) ? $serviceInfo['timeunitexp'] : 0,
            'inittimeexp' => isset($serviceInfo['inittimeexp']) ? $serviceInfo['inittimeexp'] : 0,
            'timebaseonline' => isset($serviceInfo['timebaseonline']) ? $serviceInfo['timebaseonline'] : 0,
            'timeunitonline' => isset($serviceInfo['timeunitonline']) ? $serviceInfo['timeunitonline'] : 0,
            'inittimeonline' => isset($serviceInfo['inittimeonline']) ? $serviceInfo['inittimeonline'] : 0,
            'trafficunitdl' => isset($serviceInfo['trafficunitdl']) ? $serviceInfo['trafficunitdl'] : 0,
            'initdl' => isset($serviceInfo['initdl']) ? $serviceInfo['initdl'] : 0,
            'trafficunitul' => isset($serviceInfo['trafficunitul']) ? $serviceInfo['trafficunitul'] : 0,
            'initul' => isset($serviceInfo['initul']) ? $serviceInfo['initul'] : 0,
            'trafficunitcomb' => isset($serviceInfo['trafficunitcomb']) ? $serviceInfo['trafficunitcomb'] : 0,
            'inittotal' => isset($serviceInfo['inittotal']) ? $serviceInfo['inittotal'] : 0,
            'minamount' => isset($serviceInfo['minamount']) ? $serviceInfo['minamount'] : 1,
            'minamountadd' => isset($serviceInfo['minamountadd']) ? $serviceInfo['minamountadd'] : 0,
            'addamount' => isset($serviceInfo['addamount']) ? $serviceInfo['addamount'] : 1
        );

        $serviceInfo = array_merge($defaultFields, $serviceInfo);
        
        $this->db->where('srvid', $srvid);
        $this->db->update('rm_services', $serviceInfo);
        
        return true;
    }

    /**
     * This function is used to export service profiles to CSV
     * @param number $status : This is status filter
     * @param number $expiry : This is expiry filter
     * @return object $result : This is result
     */
    function spToCSV($status = 0, $expiry = 0)
    {
        $this->db->select('srvid, srvname, descr, downrate, uprate, enableservice');
        $this->db->from('rm_services');
        
        if($status == 1) {
            $this->db->where('enableservice', 1);
        }
        
        $managername = $this->session->userdata('name');
        if($managername <> 'admin'){
            $this->db->join('rm_allowedmanagers as AM', 'rm_services.srvid = AM.srvid', 'left');
            $this->db->where('AM.managername', $managername);
        }
        
        return $this->db->get();
    }

    /**
     * This function is used to get the next available srvid
     * @return number $nextId : This is next available id
     */
    function getNextSrvId()
    {
        $this->db->select_max('srvid');
        $query = $this->db->get('rm_services');
        $result = $query->row();
        
        if(isset($result->srvid)) {
            return $result->srvid + 1;
        } else {
            return 1; // Start with 1 if no records exist
        }
    }

    /**
     * Get all managers
     */
    function getManagers()
    {
        $this->db->select('managername');
        $this->db->from('rm_managers');
        $this->db->order_by('managername', 'ASC');
        $query = $this->db->get();
        
        
        return $query->result();
    }

    /**
     * Get allowed managers for a service profile
     */
    function getAllowedManagers($srvid)
    {
        $this->db->select('managername');
        $this->db->from('rm_allowedmanagers');
        $this->db->where('srvid', $srvid);
        $this->db->order_by('managername', 'ASC');
        $query = $this->db->get();
        
        $managers = array();
        foreach ($query->result() as $row) {
            $managers[] = $row->managername;
        }
        
        return $managers;
    }

    /**
     * This function is used to check if attribute name already exists
     * @param string $attname : This is attribute name
     * @return boolean : True if exists, false otherwise
     */
    function checkAttributeNameExists($attname, $attid = NULL)
    {
        $this->db->select('attid');
        $this->db->from('tbl_attributes');
        $this->db->where('attname', $attname);
        
        // If attid is provided, exclude it from the check (for update operations)
        if ($attid) {
            $this->db->where('attid !=', $attid);
        }
        
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    /**
     * This function is used to get all attributes
     * @param string $searchText : This is optional search text
     * @param number $page : This is pagination offset
     * @param number $segment : This is pagination limit
     * @return array $result : This is result
     */
    function getAttributes($searchText = '', $page = 0, $segment = 0)
    {
        $this->db->select('attid, attname, atttype, descr');
        $this->db->from('tbl_attributes');
        
        if (!empty($searchText)) {
            $likeCriteria = "(attname LIKE '%".$searchText."%' OR descr LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        
        $this->db->order_by('attid', 'DESC');
        
        if ($page != 0 && $segment != 0) {
            $this->db->limit($segment, $page);
        }
        
        $query = $this->db->get();
        return $query->result();
    }

    /**
     * This function is used to get the count of attributes
     * @param string $searchText : This is optional search text
     * @param string $filterType : This is optional type filter
     * @return number $count : This is row count
     */
    function attributesListingCount($searchText = '', $filterType = '')
    {
        $this->db->select('BaseTbl.attid, BaseTbl.attname, BaseTbl.atttype, BaseTbl.descr, BaseTbl.createdDtm');
        $this->db->from('tbl_attributes as BaseTbl');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.attname LIKE '%".$searchText."%'
                            OR BaseTbl.descr LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        if(!empty($filterType)) {
            $this->db->where('BaseTbl.atttype', $filterType);
        }
        $query = $this->db->get();
        
        return $query->num_rows();
    }

    /**
     * This function is used to get the attributes listing
     * @param string $searchText : This is optional search text
     * @param number $page : This is pagination offset
     * @param number $segment : This is pagination limit
     * @param string $filterType : This is optional type filter
     * @return array $result : This is result
     */
    function attributesListing($searchText = '', $page = 0, $segment = 0, $filterType = '')
    {
        $this->db->select('BaseTbl.attid, BaseTbl.attname, BaseTbl.atttype, BaseTbl.descr, BaseTbl.createdDtm');
        $this->db->from('tbl_attributes as BaseTbl');
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.attname LIKE '%".$searchText."%'
                            OR BaseTbl.descr LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        if(!empty($filterType) OR $filterType == 0 && $filterType != NULL) {
            $this->db->where('BaseTbl.atttype', $filterType);
        }

        $this->db->order_by('BaseTbl.attid', 'DESC');

        //if ($page != 0 && $segment != 0) {
            $this->db->limit($page, $segment);
        //}
        
        $query = $this->db->get();
        
        $result = $query->result();        
        return $result;
    }

    /**
     * This function is used to add new attribute to system
     * @param array $attributeInfo : This is attribute information
     * @return number $insert_id : This is last inserted id
     */
    function addAttribute($attributeInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_attributes', $attributeInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return $insert_id;
    }

    /**
     * This function is used to get attribute information by id
     * @param number $attid : This is attribute id
     * @return array $result : This is attribute information
     */
    function getAttributeInfo($attid)
    {
        $this->db->select('attid, attname, atttype, descr');
        $this->db->from('tbl_attributes');
        $this->db->where('attid', $attid);
        $query = $this->db->get();
        
        return $query->row();
    }

    /**
     * This function is used to update the attribute information
     * @param array $attributeInfo : This is attribute updated information
     * @param number $attid : This is attribute id
     * @return boolean $result : TRUE / FALSE
     */
    function updateAttribute($attributeInfo, $attid)
    {
        $this->db->where('attid', $attid);
        $this->db->update('tbl_attributes', $attributeInfo);
        
        return TRUE;
    }

    /**
     * This function is used to delete the attribute information
     * @param number $attid : This is attribute id
     * @return boolean $result : TRUE / FALSE
     */
    function deleteAttribute($attid)
    {
        $this->db->where('attid', $attid);
        $this->db->where('attname NOT IN (Select attribute from radcheck)');
        $this->db->where('attname NOT IN (Select attribute from radreply)');
        $this->db->where('attname NOT IN (Select attribute from radgroupcheck)');
        $this->db->where('attname NOT IN (Select attribute from radgroupreply)');
        $this->db->delete('tbl_attributes');
        
        return $this->db->affected_rows();
    }

    /**
     * This function is used to get attribute list for group type
     * @return array $result : This is result
     */
    function getGroupAttributes()
    {
        $this->db->select('attid, attname, descr');
        $this->db->from('tbl_attributes');
        $this->db->where('atttype', 1); // Attribute-Group type
        $this->db->order_by('attname', 'ASC');
        $query = $this->db->get();
        
        return $query->result();
    }

    /**
     * This function is used to get operator list
     * @return array $result : This is result
     */
    function getOperators()
    {
        $this->db->select('attid, attname, descr');
        $this->db->from('tbl_attributes');
        $this->db->where('atttype', 2); // OP type
        $this->db->order_by('attname', 'ASC');
        $query = $this->db->get();
        
        return $query->result();
    }

    /**
     * This function is used to get radius group reply attributes for a service
     * @param string $srvname : This is service name which is linked to groupname in radgroupreply
     * @return array $result : This is result
     */
    function getRadiusGroupReplyAttributes($srvname)
    {
        $this->db->select('id, groupname, attribute, op, value');
        $this->db->from('radgroupreply');
        $this->db->where('groupname', $srvname);
        $this->db->order_by('id', 'ASC');
        $query = $this->db->get();
        
        return $query->result();
    }

    /**
     * This function is used to add a new radius group reply attribute
     * @param array $attributeInfo : This is radgroupreply information
     * @return number $insert_id : This is last inserted id
     */
    function addRadiusGroupReplyAttribute($attributeInfo)
    {
        $this->db->trans_start();
        $this->db->insert('radgroupreply', $attributeInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return $insert_id;
    }

    /**
     * This function is used to delete a radius group reply attribute
     * @param number $id : This is radgroupreply id
     * @return boolean $result : TRUE / FALSE
     */
    function deleteRadiusGroupReplyAttribute($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('radgroupreply');
        
        return $this->db->affected_rows() > 0;
    }

    function getResellerPackages($managername, $mastername) {

        $this->db->select('ts.srvid, ts.srvname, ts.radsrvid, ts.baseprice, ts.costprice, ts.saleprice, rs.srvname as radsrvname, ts.managername');
        $this->db->from('tbl_services ts');
        $this->db->join('rm_services rs', 'ts.radsrvid = rs.srvid', 'left');
        
        if(strtoupper($mastername) != 'NONE' && !empty($mastername)) {
            $this->db->where('ts.managername', $mastername);
        }

        $this->db->where('ts.radsrvid not in (select radsrvid from tbl_services where managername = "'.$managername.'")');
        
        // Group by radsrvid to get unique records
        $this->db->group_by('ts.managername, ts.radsrvid');
        
        // Order by service name for better presentation
        $this->db->order_by('ts.srvname', 'ASC');
        
        $query = $this->db->get();
        return $query->result();

    }

    /**
     * Add service to allowed managers if not exists
     * @param int $srvid: Service ID
     * @param string $managername: Manager name
     * @return bool: True if added or already exists, False on error
     */
    public function addServiceToAllowedManagers($srvid, $managername) {
        // Check if the combination already exists
        $this->db->where('srvid', $srvid);
        $this->db->where('managername', $managername);
        $query = $this->db->get('rm_allowedmanagers');
        
        if ($query->num_rows() == 0) {
            // Combination doesn't exist, add it
            $data = array(
                'srvid' => $srvid,
                'managername' => $managername
            );
            
            $this->db->insert('rm_allowedmanagers', $data);
            return $this->db->affected_rows() > 0;
        }
        
        // Combination already exists
        return true;
    }

    /**
     * Get all sub-reseller services for a master reseller
     * @param string $mastername
     * @return array
     */
    public function getSubResellerServices($mastername = null, $srvid = null)
    {
        if (!$mastername || !$srvid) {
            return array();
        }
        // Step 1: Get radsrvid for the given srvid
        $query = $this->db->select('radsrvid')->from('tbl_services')->where('srvid', $srvid)->get();
        $row = $query->row();
        if (!$row) {
            return array();
        }
        $radsrvid = $row->radsrvid;
        // Step 2: Get subreseller managernames for the master
        $subresellers = $this->db->select('managername')->from('rm_managers')->where('mastername', $mastername)->get()->result();
        $subreseller_names = array_map(function($r) { return $r->managername; }, $subresellers);
        if (empty($subreseller_names)) {
            return array();
        }
        // Step 3: Get all tbl_services records for subresellers with this radsrvid
        $this->db->select('srv.srvid, srv.srvname, srv.radsrvid, srv.managername, srv.baseprice, srv.costprice, srv.saleprice, rs.srvname as radsrvname');
        $this->db->from('tbl_services as srv');
        $this->db->join('rm_services rs', 'srv.radsrvid = rs.srvid', 'left');
        $this->db->where('srv.radsrvid', $radsrvid);
        $this->db->where_in('srv.managername', $subreseller_names);
        $this->db->order_by('srv.managername, srvname');
        return $this->db->get()->result();
    }

    /**
     * Create or update a record in radusergroup for a user
     * @param string $srvname
     * @param string $username
     * @param int $priority
     * @return array
     */
    public function manageUserRadusergroup($srvname, $username, $priority = 0)
    {
        if (!$srvname || !$username) {
            return ['status' => false, 'error' => 'Missing srvname or username'];
        }
        $this->db->where('username', $username);
        $query = $this->db->get('radusergroup');
        if ($query->num_rows() > 0) {
            // Update
            $this->db->where('username', $username);
            $this->db->update('radusergroup', [
                'groupname' => $srvname,
                'priority' => $priority
            ]);
            return ['status' => true, 'action' => 'updated'];
        } else {
            // Insert
            $this->db->insert('radusergroup', [
                'username' => $username,
                'groupname' => $srvname,
                'priority' => $priority
            ]);
            return ['status' => true, 'action' => 'created', 'id' => $this->db->insert_id()];
        }
    }

    /**
     * Check if service profile exists in rm_services table
     */
    public function checkServiceProfileExists($srvid)
    {
        $this->db->select('srvid');
        $this->db->from('rm_services');
        $this->db->where('srvid', $srvid);
        $query = $this->db->get();
        
        return $query->num_rows() > 0;
    }

    /**
     * Get users that have the specified service assigned
     */
    public function getUsersWithService($srvid)
    {
        $this->db->select('username, firstName, lastName, srvid');
        $this->db->from('rm_users');
        $this->db->where('srvid', $srvid);
        $this->db->where('enableuser', 1); // Only active users
        $query = $this->db->get();
        
        return $query->result();
    }

    /**
     * Get service profile information from rm_services
     */
    public function getServiceProfileInfo($srvid)
    {
        $this->db->select('srvid, srvname');
        $this->db->from('rm_services');
        $this->db->where('srvid', $srvid);
        $query = $this->db->get();
        
        return $query->row();
    }

    /**
     * Delete service from rm_allowedmanagers table
     */
    public function deleteServiceFromAllowedManagers($srvid)
    {
        $this->db->where('srvid', $srvid);
        return $this->db->delete('rm_allowedmanagers');
    }

    /**
     * Delete service from rm_allowednases table
     */
    public function deleteServiceFromAllowedNases($srvid)
    {
        $this->db->where('srvid', $srvid);
        return $this->db->delete('rm_allowednases');
    }

    /**
     * Delete service profile from rm_services table
     */
    public function deleteServiceProfile($srvid)
    {
        $this->db->where('srvid', $srvid);
        return $this->db->delete('rm_services');
    }

    /**
     * Delete service from tbl_services table using radsrvid
     */
    public function deleteServiceFromTblServices($radsrvid)
    {
        $this->db->where('radsrvid', $radsrvid);
        return $this->db->delete('tbl_services');
    }

    /**
     * Check if service exists in tbl_services by srvid
     */
    public function checkServiceExistsById($srvid)
    {
        $this->db->select('srvid');
        $this->db->from('tbl_services');
        $this->db->where('srvid', $srvid);
        $query = $this->db->get();
        
        return $query->num_rows() > 0;
    }

    /**
     * Get users that have the specified service assigned from tbl_services
     * Checks for users where rm_users.srvid = tbl_services.radsrvid AND rm_users.owner = tbl_services.managername
     */
    public function getUsersWithTblService($srvid)
    {
        // First get the service details from tbl_services
        $this->db->select('radsrvid, managername');
        $this->db->from('tbl_services');
        $this->db->where('srvid', $srvid);
        $service = $this->db->get()->row();
        
        if (!$service) {
            return array();
        }
        
        // Now check for users in rm_users with matching conditions
        $this->db->select('username, firstName, lastName, srvid, owner');
        $this->db->from('rm_users');
        $this->db->where('srvid', $service->radsrvid);
        $this->db->where('owner', $service->managername);
        $this->db->where('enableuser', 1); // Only active users
        $query = $this->db->get();
        
        return $query->result();
    }

    /**
     * Delete service from tbl_services table by srvid
     */
    public function deleteTblService($srvid)
    {
        $this->db->where('srvid', $srvid);
        return $this->db->delete('tbl_services');
    }
}
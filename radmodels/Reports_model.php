<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Reports_model extends CI_Model
{
    public $ismaster = 0;
    public function __construct()
    {
        parent::__construct();
        $this->ismaster = $this->session->userdata('ismaster') !== null ? $this->session->userdata('ismaster') : 0;
    }

    function onlineCallsCount($searchText = '')
    {
        $this->db->select('BaseTbl.username, BaseTbl.acctstarttime, BaseTbl.acctsessiontime, BaseTbl.acctoutputoctets, BaseTbl.acctinputoctets,
                        BaseTbl.framedipaddress, BaseTbl.framedipaddress, BaseTbl.callingstationid, BaseTbl.nasipaddress, NasTbl.shortname,
                        NasTbl.description, UsrTbl.firstname, UsrTbl.lastname, UsrTbl.city');
        $this->db->from('radacct as BaseTbl');
        $this->db->join('nas as NasTbl', 'BaseTbl.nasipaddress = NasTbl.nasname','left');
        $this->db->join('rm_users as UsrTbl', 'BaseTbl.username = UsrTbl.username', 'left');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.username  LIKE '%".$searchText."%'
                            OR  UsrTbl.firstname  LIKE '%".$searchText."%'
                            OR  UsrTbl.lastname  LIKE '%".$searchText."%'
                            OR  NasTbl.shortname  LIKE '%".$searchText."%'
                            OR  NasTbl.description  LIKE '%".$searchText."%'
                            OR  UsrTbl.city  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        $managername = $this->session->userdata ( 'name' );
        if($managername <> 'admin'){
            if($this->ismaster < 1)
                { $this->db->where('UsrTbl.owner = ', $managername); }
            else
            { $this->db->where('UsrTbl.owner IN  (Select managername from rm_managers where mastername = "'.$managername.'")'); }
        }

        $this->db->where('isnull(BaseTbl.acctstoptime) = true', null, false);
        //$this->db->where('BaseTbl.acctype =', 0);

        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function onlineCallsListing($searchText = '', $page, $segment)
    {

        $this->db->query('Set @row_number = 0');
        $this->db->select('BaseTbl.username, BaseTbl.acctstarttime, BaseTbl.acctsessiontime, BaseTbl.acctoutputoctets, BaseTbl.acctinputoctets,
                            BaseTbl.framedipaddress, BaseTbl.callingstationid, BaseTbl.nasipaddress, NasTbl.shortname,
                            NasTbl.description, UsrTbl.firstname, UsrTbl.lastname, UsrTbl.city, UsrTbl.owner,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('radacct as BaseTbl');
        $this->db->join('nas as NasTbl', 'BaseTbl.nasipaddress = NasTbl.nasname','left');
        $this->db->join('rm_users as UsrTbl', 'BaseTbl.username = UsrTbl.username', 'left');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.username  LIKE '%".$searchText."%'
                            OR  UsrTbl.firstname  LIKE '%".$searchText."%'
                            OR  UsrTbl.lastname  LIKE '%".$searchText."%'
                            OR  NasTbl.shortname  LIKE '%".$searchText."%'
                            OR  NasTbl.description  LIKE '%".$searchText."%'
                            OR  UsrTbl.city  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        $managername = $this->session->userdata ( 'name' );
        if($managername <> 'admin'){
            if($this->ismaster < 1){ 
                $this->db->where('UsrTbl.owner = ', $managername); 
            }
            else{ 
                $this->db->where('UsrTbl.owner IN  (Select managername from rm_managers where mastername = "'.$managername.'")'); 
            }
        }

        $this->db->where('isnull(BaseTbl.acctstoptime) = true', null, false);

        $this->db->order_by('serial_number, BaseTbl.username');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }

    function disconnectUser($user)
    {

        $this->db->select('username, nasipaddress, nasportid, nasporttype, acctstarttime, acctstoptime, acctauthentic, calledstationid,
                            BaseTbl.framedipaddress, NasTbl.secret');
        $this->db->from('radacct as BaseTbl');
        $this->db->join('nas as NasTbl', 'BaseTbl.nasipaddress = NasTbl.nasname','left');
        $this->db->where('BaseTbl.username', $user);
        $this->db->where('isnull(BaseTbl.acctstoptime) = true', null, false);

        $query = $this->db->get();
        $result = $query->row();
        return $result;

    }

    function disconnectUserAccountUpdate($user){

        $this->db->query("update radacct set acctstoptime = now() where isnull(acctstoptime) = true and username = '".$user."'");

    }

    function lastOnlineUserStatus($user)
    {

        $this->db->select('username, nasipaddress, nasportid, nasporttype, acctstarttime, acctstoptime, acctauthentic, calledstationid,
                            BaseTbl.framedipaddress, NasTbl.secret');
        $this->db->from('radacct as BaseTbl');
        $this->db->join('nas as NasTbl', 'BaseTbl.nasipaddress = NasTbl.nasname','left');
        $this->db->where('BaseTbl.username', $user);
        //$this->db->where('isnull(BaseTbl.acctstoptime) = false', null, false);
        $this->db->where('radacctid in (select max(radacctid) from radacct where username="'.$user.'")');

        $query = $this->db->get();
        $result = $query->row();
        return $result;

    }

    function checkUserOnlineStatus($user)
    {
        $this->db->select('username');
        $this->db->from("radacct");
        $this->db->where('username', $user);
        $this->db->where('isnull(acctstoptime) = true', null, false);
        //$this->db->where('radacctid = 2819111');
        $query = $this->db->get();

        if ($query->num_rows() > 0){
            return TRUE;
        } else {
            return FALSE;
        }
    }


    function RestartSession()
    {

        $this->db->select('username, nasipaddress, nasportid, nasporttype, acctstarttime, acctstoptime, acctauthentic, calledstationid,
                            BaseTbl.framedipaddress, NasTbl.secret');
        $this->db->from('radacct as BaseTbl');
        $this->db->join('nas as NasTbl', 'BaseTbl.nasipaddress = NasTbl.nasname','left');
        $this->db->where('isnull(BaseTbl.acctstoptime) = true', null, false);

        $managername = $this->session->userdata ( 'name' );
        $this->db->where('BaseTbl.username in (select username from rm_users where owner = "'.$managername.'")');

        $query = $this->db->get();
        $result = $query->result();
        return $result;

    }

    function salesReportCount($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '')
    {
        $this->db->select('BaseTbl.username, BaseTbl.amount, BaseTbl.invtype, BaseTbl.createdDtm, BaseTbl.remarks');
        $this->db->from('tbl_invoices as BaseTbl');
        $this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.srvid','left');

        $this->db->where('BaseTbl.managername = ', $searchText);
        $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
        $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
        //$this->db->where('BaseTbl.acctype =', 0);

        if($searchText3 == 0){
            $this->db->where('date(BaseTbl.srvdate) >= ', $searchText1);
            $this->db->where('date(BaseTbl.srvdate) <= ', $searchText2);}
        
        if($searchText3 == 1)
            {$this->db->where('date(BaseTbl.srvdate) >= ', $searchText1);
            $this->db->where('date(BaseTbl.srvdate) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Recharge"');}

        if($searchText3 == 2)
            {$this->db->where('date(BaseTbl.srvdate) >= ', $searchText1);
            $this->db->where('date(BaseTbl.srvdate) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Credit"');}

        if($searchText3 == 3){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);}
        
        if($searchText3 == 4){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Recharge"');}

        if($searchText3 == 5){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Credit"');}

        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function salesReport($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '', $page, $segment)
    {
        if($searchText3 >= 9 && $searchText3 <= 13){
            $condition = '"ALL" as invtype,';
            if($searchText3 == 12){ // Group By MasterManager Condition 
                $condition = "managers.mastername as invtype,";
            }elseif($searchText3 >= 11 && $searchText3 <= 13){ 
                $condition = "Services.managername as invtype,";
            }
            $add_to_qry = 'CONCAT(count(BaseTbl.username),"-"," Users") as username, sum(BaseTbl.amount) as amount, '.
                        $condition.
                        'MAX(BaseTbl.createdDtm) as createdDtm,
                        MAX(IF(BaseTbl.srvdate="0000-00-00", DATE(BaseTbl.createdDtm), BaseTbl.srvdate)) as srvdate, 
                        MAX(IF(BaseTbl.expdate="0000-00-00", DATE(BaseTbl.createdDtm), BaseTbl.expdate)) as expdate, 
                        CONCAT("Package Summery of ", "-", Services.srvname) as remarks, Services.srvname,
                        sum(BaseTbl.price) as costprice, sum(Services.saleprice) as saleprice,
                        "" as eppay,
                        (@row_number:=@row_number + 1) AS serial_number';
        }elseif($searchText3 == 8){
            $add_to_qry = 'BaseTbl.username, sum(BaseTbl.amount) as amount, max(BaseTbl.invtype) as invtype, 
                        min(BaseTbl.createdDtm) as createdDtm,
                        min(IF(BaseTbl.srvdate="0000-00-00", DATE(BaseTbl.createdDtm), BaseTbl.srvdate)) as srvdate, 
                        min(IF(BaseTbl.expdate="0000-00-00", DATE(BaseTbl.createdDtm), BaseTbl.expdate)) as expdate, 
                        min(BaseTbl.remarks) as remarks, min(Services.srvname) as srvname, 
                        sum(BaseTbl.price) as costprice, sum(Services.saleprice) as saleprice,
                        min(EPT.transaction_date) as eppay,
                        (@row_number:=@row_number + 1) AS serial_number';
        }else{
            $add_to_qry = 'BaseTbl.username, BaseTbl.amount, BaseTbl.invtype, BaseTbl.createdDtm,
                        IF(BaseTbl.srvdate="0000-00-00", DATE(BaseTbl.createdDtm), BaseTbl.srvdate) as srvdate, 
                        IF(BaseTbl.expdate="0000-00-00", DATE(BaseTbl.createdDtm), BaseTbl.expdate) as expdate, 
                        BaseTbl.remarks, Services.srvname, 
                        BaseTbl.price as costprice, Services.saleprice,
                        EPT.transaction_date as eppay,
                        (@row_number:=@row_number + 1) AS serial_number';
        }

        $this->db->query('Set @row_number = 0');
        $this->db->select($add_to_qry);
        $this->db->from('tbl_invoices as BaseTbl');
        $this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.srvid','left');
        $this->db->join('tbl_eptransaction as EPT', 'BaseTbl.username = EPT.username 
                        and year(BaseTbl.srvdate) = mid(EPT.transaction_date, 1, 4) 
                        and month(BaseTbl.srvdate) = mid(EPT.transaction_date, 5, 2)', 'left');
        
        if($searchText3 == 12){ // Need join Managers for Master Grouping
            $this->db->join('rm_managers as managers', 'BaseTbl.managername = managers.managername','left');
        }
        
        if($searchText3 >= 9 && $searchText3 <= 12 && $searchText == "admin" && $this->session->userdata ( 'name' ) == "admin"){
            $this->db->where('BaseTbl.managername <> ', $searchText);
        }elseif($searchText3 == 13 and $searchText <> "admin"){
            $this->db->where('BaseTbl.managername <> ', $searchText);
        }else{
            $this->db->where('BaseTbl.managername = ', $searchText);
        }

        if($searchText3 == 0){
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->having('date(srvdate) >= ', $searchText1);
            $this->db->having('date(srvdate) <= ', $searchText2);
        }
        
        if($searchText3 == 1)
        {
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->where('BaseTbl.invtype = "Recharge"');
            $this->db->having('date(srvdate) >= ', $searchText1);
            $this->db->having('date(srvdate) <= ', $searchText2);
        }

        if($searchText3 == 2)            
        {
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->where('BaseTbl.invtype = "Credit"');
            $this->db->having('date(srvdate) >= ', $searchText1);
            $this->db->having('date(srvdate) <= ', $searchText2);
        }

        if($searchText3 == 3){
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
        }
        
        if($searchText3 == 4){
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Recharge"');
        }

        if($searchText3 == 5){
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Credit"');
        }
        
        if($searchText3 == 6){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
        }

        if($searchText3 == 7){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('date(BaseTbl.srvdate) = "0000-00-00"');
            //$this->db->where('BaseTbl.invtype = "Recharge"');
        }

        if($searchText3 == 8){
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Credit"');
            $this->db->group_by('BaseTbl.username');
        }

        if($searchText3 >= 9 && $searchText3 <= 13){ // Report By Package
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->where('BaseTbl.srvid <> ', 0);
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            if($searchText3 == 9){
                $this->db->group_by('Services.srvname');
            }elseif($searchText3 == 10){ // Group by Radius Service Profile ID
                $this->db->group_by('Services.radsrvid');
                $this->db->order_by('Services.srvname', 'ASCE');
            }elseif($searchText3 == 11){
                $this->db->group_by('Services.radsrvid, Services.managername');
                $this->db->order_by('BaseTbl.managername', 'ASCE');
            }elseif($searchText3 == 12){
                $this->db->where('BaseTbl.managername in (select managername from rm_managers where mastername <> "")');
                $this->db->where('BaseTbl.username <> ', $searchText);
                $this->db->group_by('managers.mastername, Services.radsrvid');
                $this->db->order_by('BaseTbl.managername', 'ASCE');
            }elseif($searchText3 == 13){
                $this->db->where('BaseTbl.managername in (select managername from rm_managers where mastername = "'.$searchText.'")');
                $this->db->where('BaseTbl.username <> ', $searchText);
                $this->db->group_by('Services.radsrvid, Services.managername');
                $this->db->order_by('BaseTbl.managername', 'ASCE');
            }
        }

        $this->db->order_by('BaseTbl.createdDtm', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }

    function salesSummery($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '')
    {

        $this->db->query('Set @row_number = 0');
        $this->db->select('A.managername');
        $this->db->select('IF(A.srvdate = "0000-00-00", "Refund-Credit", A.invtype) as type');
        $this->db->select('count(A.username) as Users');
        $this->db->select_sum('A.price');
        $this->db->select_sum('A.amount');
        $this->db->from('tbl_invoices as A');
        $this->db->join('rm_users as B', 'A.managername = B.owner and A.username=B.username','left');

        if($searchText3 == 0 || $searchText3 == 1 || $searchText3 == 2){

            $this->db->where('A.managername = ', $searchText);
            $this->db->where('date(A.srvdate) >= ', $searchText1);
            $this->db->where('date(A.srvdate) <= ', $searchText2);
            $this->db->where('A.invtype', 'Recharge');
            $this->db->where('A.username <> ', $searchText);

            $this->db->or_where('A.managername = ', $searchText);
            $this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);
            $this->db->where('A.invtype', 'Credit');
            $this->db->where('A.username <> ', $searchText);
            //$this->db->where('A.srvid<>0'); // This need to remove Credit Entries by Admin

            $this->db->or_where('A.managername = ', $searchText);
            $this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);
            $this->db->where('A.invtype', 'Debit');
            $this->db->where('A.username <> ', $searchText);

        }else if($searchText3 == 6){
            $this->db->or_where('A.managername = ', $searchText);
            $this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);

        }else if($searchText3 == 7){
            $this->db->where('A.managername = ', $searchText);
            $this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);
            $this->db->where('date(A.srvdate) = "0000-00-00"');

        }else if($searchText3 == 8){
            $this->db->where('A.managername = ', $searchText);
            $this->db->where('A.username <> ', $searchText);
            $this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);
            $this->db->where('A.invtype', 'Credit');

        }else{
            $this->db->where('A.managername = ', $searchText);
            $this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);
            $this->db->where('A.invtype', 'Recharge');
            $this->db->where('A.username <> ', $searchText);
    
            $this->db->or_where('A.managername = ', $searchText);
            $this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);
            $this->db->where('A.invtype', 'Credit');
            $this->db->where('A.username <> ', $searchText);
            //$this->db->where('A.srvid<>0'); // // This need to remove Credit Entries by Admin

            $this->db->or_where('A.managername = ', $searchText);
            $this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);
            $this->db->where('A.invtype', 'Debit');
            $this->db->where('A.username <> ', $searchText);

        }
        
        if($searchText3 == 6){
            //$this->db->group_by(array('A.managername'));
        }else{
            $this->db->group_by(array('A.managername','type'));
        }

        $this->db->order_by('A.managername, type');
        
        $query = $this->db->get();
        $result = $query->result();
        return $result;

    }
    
    // MANAGER SALES REPORT SUMMERY REPORT
    function salesReportManager($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '', $page, $segment)
    {

        //(@refund:=@refund + BaseTbl.amount) AS baseprice
        $this->db->query('Set @refund = 0');
        $this->db->select('BaseTbl.managername, BaseTbl.invtype,
                            IF(BaseTbl.srvdate="0000-00-00", DATE(BaseTbl.createdDtm), BaseTbl.srvdate) as srvdate,
                            sum(TblCredit.credit), sum(TblDebit.recharge), SUM(BaseTbl.price) as costprice,
                            sum(amount) AS balance');
        $this->db->from('tbl_invoices as BaseTbl');
        $this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.srvid','left');
        $this->db->join('(select username, amount as credit from tbl_invoices as A 
                            where A.amount > 0) as TblCredit',
                            'TblCredit.username = BaseTbl.username','left');
        $this->db->join('(select username, amount as recharge from tbl_invoices as A 
                            where A.amount < 0) as TblDebit',
                            'TblDebit.username = BaseTbl.username','left');
        
        //$this->db->where('BaseTbl.managername IN (Select managername from rm_managers where mastername = "'.$searchText.'")');

        if($searchText3 == 0 || $searchText3 == 1 || $searchText3 == 2){

            $this->db->where('date(BaseTbl.srvdate) >= ', $searchText1);
            $this->db->where('date(BaseTbl.srvdate) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype', 'Recharge');
            $this->db->where('BaseTbl.managername IN (Select managername from rm_managers where mastername = "'.$searchText.'")');

            $this->db->or_where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype', 'Credit');
            //$this->db->where('A.srvid<>0'); // This need to remove Credit Entries by Admin
            $this->db->where('BaseTbl.managername IN (Select managername from rm_managers where mastername = "'.$searchText.'")');

        }else{

            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype', 'Recharge');
            $this->db->where('BaseTbl.managername IN (Select managername from rm_managers where mastername = "'.$searchText.'")');
    
            $this->db->or_where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype', 'Credit');
            //$this->db->where('A.srvid<>0'); // // This need to remove Credit Entries by Admin
            $this->db->where('BaseTbl.managername IN (Select managername from rm_managers where mastername = "'.$searchText.'")');

        }

        $this->db->group_by('BaseTbl.managername');
        $this->db->order_by('BaseTbl.managername');
        //$this->db->limit($page, $segment);
        $query = $this->db->get();

        $result = $query->result();

        return $result;

    }

    function salesSummeryManager($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '')
    {

        $this->db->query('Set @row_number = 0');
        $this->db->select('A.managername');
        $this->db->select('IF(A.srvdate = "0000-00-00", "Refund-Credit", A.invtype) as type');
        $this->db->select('count(A.username) as Users');
        $this->db->select_sum('A.price');
        $this->db->select_sum('A.amount');
        $this->db->from('tbl_invoices as A');
        $this->db->join('rm_users as B', 'A.managername = B.owner and A.username=B.username','left');

        if($searchText3 == 0 || $searchText3 == 1 || $searchText3 == 2){

            $this->db->where('A.managername IN (Select managername from rm_managers where mastername = "'.$searchText.'")');
            $this->db->where('date(A.srvdate) >= ', $searchText1);
            $this->db->where('date(A.srvdate) <= ', $searchText2);
            $this->db->where('A.invtype', 'Recharge');

            $this->db->or_where('A.managername IN (Select managername from rm_managers where mastername = "'.$searchText.'")');
            $this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);
            $this->db->where('A.invtype', 'Credit');
            //$this->db->where('A.srvid<>0'); // This need to remove Credit Entries by Admin

        }else{

            $this->db->where('A.managername IN (Select managername from rm_managers where mastername = "'.$searchText.'")');
            $this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);
            $this->db->where('A.invtype', 'Recharge');
    
            $this->db->or_where('A.managername IN (Select managername from rm_managers where mastername = "'.$searchText.'")');
            $this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);
            $this->db->where('A.invtype', 'Credit');
            //$this->db->where('A.srvid<>0'); // // This need to remove Credit Entries by Admin

        }
        
        //$this->db->group_by('A.managername');

        //$this->db->order_by('A.managername');
        
        $query = $this->db->get();
        $result = $query->result();
        return $result;

    }


    // ***** Cost Of Sales Report ********** //
    function costOfSalesReport($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '', $page, $segment)
    {
        $this->db->query('Set @row_number = 0');
        $this->db->select('BaseTbl.username, BaseTbl.amount, BaseTbl.invtype, BaseTbl.createdDtm,
                            BaseTbl.srvdate, BaseTbl.expdate, BaseTbl.remarks, Services.srvname, 
                            BaseTbl.price as costprice, Services.saleprice,
                            (@row_number:=@row_number + 1) AS serial_number');

        $this->db->from('tbl_recharge as BaseTbl');
        $this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.srvid','left');
        
        $this->db->where('BaseTbl.managername = ', $searchText);


        if($searchText3 == 0){
            $this->db->where('date(BaseTbl.srvdate) >= ', $searchText1);
            $this->db->where('date(BaseTbl.srvdate) <= ', $searchText2);}
        
        if($searchText3 == 1)
            {$this->db->where('date(BaseTbl.srvdate) >= ', $searchText1);
            $this->db->where('date(BaseTbl.srvdate) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Recharge"');}

        if($searchText3 == 2)
            {$this->db->where('date(BaseTbl.srvdate) >= ', $searchText1);
            $this->db->where('date(BaseTbl.srvdate) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Credit"');}

        if($searchText3 == 3){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);}
        
        if($searchText3 == 4){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Recharge"');}

        if($searchText3 == 5){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Credit"');}

        $this->db->order_by('BaseTbl.createdDtm', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }

    function costOfSalesReportCount($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '')
    {
        $this->db->select('BaseTbl.username, BaseTbl.amount, BaseTbl.invtype, BaseTbl.createdDtm, BaseTbl.remarks');
        $this->db->from('tbl_recharge as BaseTbl');
        $this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.srvid','left');

        $this->db->where('BaseTbl.managername = ', $searchText);
        $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
        $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
        //$this->db->where('BaseTbl.acctype =', 0);

        if($searchText3 == 0){
            $this->db->where('date(BaseTbl.srvdate) >= ', $searchText1);
            $this->db->where('date(BaseTbl.srvdate) <= ', $searchText2);}
        
        if($searchText3 == 1)
            {$this->db->where('date(BaseTbl.srvdate) >= ', $searchText1);
            $this->db->where('date(BaseTbl.srvdate) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Recharge"');}

        if($searchText3 == 2)
            {$this->db->where('date(BaseTbl.srvdate) >= ', $searchText1);
            $this->db->where('date(BaseTbl.srvdate) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Credit"');}

        if($searchText3 == 3){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);}
        
        if($searchText3 == 4){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Recharge"');}

        if($searchText3 == 5){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Credit"');}

        $query = $this->db->get();
        return $query->num_rows();
    }

    function costOfSalesSummery($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '')
    {

        $this->db->query('Set @row_number = 0');
        $this->db->select('A.managername');
        $this->db->select('count(A.username) as Users');
        $this->db->select_avg('A.price');
        $this->db->select_sum('A.amount');
        $this->db->from('tbl_recharge as A');
        $this->db->join('rm_users as B', 'A.managername = B.owner and A.username=B.username','left');
        
        $this->db->where('A.managername = ', $searchText);
        $this->db->where('date(A.srvdate) >= ', $searchText1);
        $this->db->where('date(A.srvdate) <= ', $searchText2);
        $this->db->where('A.invtype', 'SALES');

        $this->db->or_where('A.managername = ', $searchText);
        $this->db->where('date(A.createdDtm) >= ', $searchText1);
        $this->db->where('date(A.createdDtm) <= ', $searchText2);
        $this->db->where('A.invtype', 'SALES');
        $this->db->where('A.srvid<>0');

        $this->db->group_by('A.managername');

        $this->db->order_by('A.managername');
        
        $query = $this->db->get();
        $result = $query->result();
        return $result;

    }

    // End of Cost Of sales Report Section
    function sendGroupMessage($owner)
    {
        $this->db->select('id, title, subject, txtmsg, status, createdDtm');
        $this->db->from('tbl_smslogs');
        $this->db->where('owner', $owner);

        $query = $this->db->get();
        
        $result = $query->result();
        return $result;
        
    }

    function insertSmsLogs($smslogs)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_smslogs', $smslogs);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return True;
    }

    function smsReportCount($searchText = '', $searchText1 = '', $searchText2 = '')
    {
        $this->db->select('BaseTbl.title, BaseTbl.subject, BaseTbl.txtmsg, BaseTbl.status, BaseTbl.createdDtm');
        $this->db->from('tbl_smslogs as BaseTbl');

        //$this->db->where('BaseTbl.title = ', $searchText);
        //$this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
        //$this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
        //$this->db->where('BaseTbl.acctype =', 0);

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.title  LIKE '%".$searchText."%'
                            AND date(BaseTbl.createdDtm) >= '".$searchText1."'
                            AND date(BaseTbl.createdDtm) <= '".$searchText2."'
                            OR BaseTbl.subject  LIKE '%".$searchText."%'
                            AND date(BaseTbl.createdDtm) >= '".$searchText1."'
                            AND date(BaseTbl.createdDtm) <= '".$searchText2."'
                            OR  BaseTbl.txtmsg  LIKE '%".$searchText."%'
                            AND date(BaseTbl.createdDtm) >= '".$searchText1."'
                            AND date(BaseTbl.createdDtm) <= '".$searchText2."')";

            $this->db->where($likeCriteria);
        }

        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function smsReport($searchText = '', $searchText1 = '', $searchText2 = '', $page, $segment)
    {
        $this->db->query('Set @row_number = 0');
        $this->db->select('BaseTbl.title, BaseTbl.subject, BaseTbl.txtmsg, BaseTbl.status, BaseTbl.createdDtm,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('tbl_smslogs as BaseTbl');
        
        //$this->db->where('BaseTbl.title = ', $searchText);
        //$this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
        //$this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.title  LIKE '%".$searchText."%'
                            AND date(BaseTbl.createdDtm) >= '".$searchText1."'
                            AND date(BaseTbl.createdDtm) <= '".$searchText2."'
                            OR BaseTbl.subject  LIKE '%".$searchText."%'
                            AND date(BaseTbl.createdDtm) >= '".$searchText1."'
                            AND date(BaseTbl.createdDtm) <= '".$searchText2."'
                            OR  BaseTbl.txtmsg  LIKE '%".$searchText."%'
                            AND date(BaseTbl.createdDtm) >= '".$searchText1."'
                            AND date(BaseTbl.createdDtm) <= '".$searchText2."')";

            $this->db->where($likeCriteria);
        }

        $this->db->order_by('BaseTbl.createdDtm', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }

    // Resellers Listing Report

    function resellerListingCount($searchText = '', $page, $segment)
    {

        $this->db->select('BaseTbl.managername, BaseTbl.mastername, BaseTbl.firstname, BaseTbl.lastname, BaseTbl.city,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('rm_managers as BaseTbl');
        
        //$this->db->where('BaseTbl.title = ', $searchText);
        //$this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
        //$this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.managername  LIKE '%".$searchText."%'
                            OR BaseTbl.mastername  LIKE '%".$searchText."%'
                            OR BaseTbl.firstname  LIKE '%".$searchText."%'
                            OR BaseTbl.lastname  LIKE '%".$searchText."%'
                            OR  BaseTbl.city  LIKE '%".$searchText."%')";

            $this->db->where($likeCriteria);
        }

        $query = $this->db->get();
        
        return $query->num_rows();

    }

    function resellerListing($searchText = '', $page, $segment)
    {
        $this->db->query('Set @row_number = 0');
        $this->db->select('BaseTbl.managername, BaseTbl.mastername, BaseTbl.firstname, BaseTbl.lastname, BaseTbl.city,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('rm_managers as BaseTbl');
        
        //$this->db->where('BaseTbl.title = ', $searchText);
        //$this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
        //$this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.managername  LIKE '%".$searchText."%'
                            OR BaseTbl.mastername  LIKE '%".$searchText."%'
                            OR BaseTbl.firstname  LIKE '%".$searchText."%'
                            OR BaseTbl.lastname  LIKE '%".$searchText."%'
                            OR  BaseTbl.city  LIKE '%".$searchText."%')";

            $this->db->where($likeCriteria);
        }

        $this->db->order_by('BaseTbl.createdDtm', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }


    // ************ Generate Easy Paisa Listing ************************

    function easypaisaCount($searchText = '', $managerAllServices)
    {
        $this->db->select('fileid, filename, billingmonth, duedate, managername, createdBy, createdDtm, filepath');
        $this->db->from('tbl_easypaisa as BaseTbl');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.filename  LIKE '%".$searchText."%'
                            OR  SrvTbl.billingmonth  LIKE '%".$searchText."%'
                            OR  BaseTbl.duedate  LIKE '%".$searchText."%')";
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
        }


        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function easypaisaListing($searchText = '', $page, $segment, $managerAllServices)
    {
        $this->db->query('Set @row_number = 0');
        $this->db->select('fileid, filename, billingmonth, duedate, managername, createdBy, createdDtm, filepath,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('tbl_easypaisa as BaseTbl');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.filename  LIKE '%".$searchText."%'
                            OR  SrvTbl.billingmonth  LIKE '%".$searchText."%'
                            OR  BaseTbl.duedate  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        $managername = $this->session->userdata ( 'name' );
        //$this->db->where('UsrTbl.owner = ', $managername);
        if($managerAllServices == 0)
        {
            $this->db->where('BaseTbl.managername = ', $managername);
        }

        $this->db->order_by('BaseTbl.fileid');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;
    }

    function checkEasypaisaFileExists($filename)
    {
        $this->db->select('filename');
        $this->db->from("tbl_easypaisa");
        $this->db->where('filename', $filename);
        $query = $this->db->get();

        if ($query->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }

    function addEasypaisaFile($easypaisaInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_easypaisa', $easypaisaInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return True;
    }

    function easypaisaReport($billingmonth, $duedate){

        $this->load->dbutil();
        $query = $this->db->query("Set @counter=0");
        $query = $this->db->query("Select @counter:=@counter+1 as SNo, payid as Groupid, concat(firstname, ' ', lastname) as GroupName, 
                        '".$billingmonth."' as BillingMonth, LPAD(replace((convert(abs(C.saleprice-A.discount+A.adjamount),CHAR)),'.',''),14,0) as DueDateAmount, 
                        '".$duedate."' as DueDate, LPAD(replace((convert(abs(C.saleprice-A.discount+A.adjamount),CHAR)),'.',''),14,0) as AmountAfterDueDate 
                        from tbl_userdocs as A left join rm_users as B on A.username = B.username 
                        left join tbl_services as C on B.srvid = C.radsrvid where B.owner=C.managername and C.saleprice>1 
                        and A.username in (select username from tbl_userdocs) and B.enableuser=1 and isnull(payid)=false");
        //$query = $this->db->get();
        //$result = $query->result();
        //echo $this->dbutil->csv_from_result($query);
        return $query;
    }

    // Indicators Report
    function indicatorReport($searchText = '', $searchText1 = '', $searchText2 = '')
    {
        $this->db->query('Set @row_number = 0');
        $this->db->select('owner, concat(month(createdon), " / ", year(createdon)) as Month, count(username) as Users, "Success" as status,
                            (Select count(username) from rm_users where owner = BaseTbl.owner and enableuser = 1 and createdon < date_add(BaseTbl.createdon, interval -DAY(BaseTbl.createdon)+1 DAY)) as PrevUsers,
                            (Select count(username) from rm_users where owner = BaseTbl.owner and enableuser = 1 and createdon <= LAST_DAY(BaseTbl.createdon)) as TotalUsers,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('rm_users as BaseTbl');

        $this->db->where('enableuser=1');
        //$this->db->where('month(createdon)>6');
        $this->db->where('year(createdon)=2021');
        $this->db->group_by('month(createdon), owner');

        $this->db->order_by('BaseTbl.owner, BaseTbl.createdon');
        //$this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }


    function easypaisaCollection($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '', $searchText4 = '', $page, $segment)
    {

        if($searchText4 == 0){
            $this->db->query('Set @row_number = 0');
            $this->db->select('BaseTbl.consumer_number, BaseTbl.customer_name, BaseTbl.amount_paid, BaseTbl.transaction_date,
                            BaseTbl.username, BaseTbl.posted, BaseTbl.createdDtm, posted, Users.owner,
                            STR_TO_DATE(CONCAT(left(transaction_date,4), "-",mid(transaction_date,5,2), "-", right(transaction_date,2)), "%Y-%m-%d") as trans_date,
                            (@row_number:=@row_number + 1) AS serial_number');
            $this->db->from('tbl_eptransaction as BaseTbl');
            $this->db->join('rm_users as Users', 'BaseTbl.username = Users.username', 'left');
        }else{
            $this->db->query('Set @row_number = 0');
            $this->db->select('"Summery" as consumer_number, "By Date" as customer_name, sum(BaseTbl.amount_paid) as amount_paid, BaseTbl.transaction_date,
                            count(BaseTbl.username) as username, max(BaseTbl.posted) as posted, Max(BaseTbl.createdDtm) as createdDtm, Users.owner,
                            max(STR_TO_DATE(CONCAT(left(transaction_date,4), "-",mid(transaction_date,5,2), "-", right(transaction_date,2)), "%Y-%m-%d")) as trans_date,
                            (@row_number:=@row_number + 1) AS serial_number');
            $this->db->from('tbl_eptransaction as BaseTbl');
            $this->db->join('rm_users as Users', 'BaseTbl.username = Users.username', 'left');
        }
        
        //echo $this->session->userdata ( 'name' );
        
        if($this->session->userdata ( 'name' ) <> "admin"){
            $this->db->where('Users.owner = ', $searchText);
        }else{
            if($searchText4 == 1 && $searchText <> "admin"){
                $this->db->where('Users.owner = ', $searchText);
            }else if($searchText4 == 0 && $searchText <> "admin"){
                $this->db->where('Users.owner = ', $searchText);
            }
        }


        if($searchText3 == 0){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);}

        if($searchText3 == 1){
            $this->db->having('trans_date >= ', $searchText1);
            $this->db->having('trans_date <= ', $searchText2);}
        
        if($searchText3 == 2)
            {$this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.posted = 0');}
        
        if($searchText3 == 3)
            {$this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.posted = 1');}
        
        if($searchText4 == 1 && $searchText == "admin"){
            $this->db->group_by('BaseTbl.transaction_date');
            $this->db->group_by('Users.owner');
        }else if($searchText4 == 1 || $searchText4 == 2){
            $this->db->group_by('BaseTbl.transaction_date');
        }

        $this->db->order_by('BaseTbl.transaction_date', 'ASCE');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }

    function easypaisaCollectionCount($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '', $searchText4 = '')
    {

        $this->db->query('Set @row_number = 0');
        $this->db->select('BaseTbl.consumer_number, BaseTbl.customer_name, BaseTbl.amount_paid, BaseTbl.transaction_date,
                            BaseTbl.username, BaseTbl.posted, BaseTbl.createdDtm, posted,
                            STR_TO_DATE(CONCAT(left(transaction_date,4), "-",mid(transaction_date,5,2), "-", right(transaction_date,2)), "%Y-%m-%d") as trans_date,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('tbl_eptransaction as BaseTbl');
        $this->db->join('rm_users as Users', 'BaseTbl.username = Users.username', 'left');

        if($this->session->userdata ( 'name' ) <> "admin"){
            $this->db->where('Users.owner = ', $searchText);
        }else{
            if($searchText4 == 0){
                $this->db->where('Users.owner = ', $searchText);
            }
        }

        if($searchText3 == 0){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);}

        if($searchText3 == 1){
            $this->db->having('trans_date >= ', $searchText1);
            $this->db->having('trans_date <= ', $searchText2);}
        
        if($searchText3 == 2)
            {$this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.posted = 0');}
        
        if($searchText3 == 3)
            {$this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.posted = 1');}

        

        $query = $this->db->get();
        return $query->num_rows();
        
    }

    function easypaisaCollectionSummery($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '', $searchText4 = '')
    {

        $this->db->query('Set @row_number = 0');
        $this->db->select('B.owner');
        $this->db->select('count(A.username) as Payments');
        $this->db->select_sum('A.amount_paid');
        $this->db->from('tbl_eptransaction as A');
        $this->db->join('rm_users as B', 'A.username = B.username','left');
        
        //if($this->session->userdata ( 'name' ) <> "admin"){
        //    $this->db->where('B.owner = ', $searchText);
        //}else{
        //    if($searchText4 == 0){
        //        $this->db->where('B.owner = ', $searchText);
        //    }
        //}

        if($this->session->userdata ( 'name' ) <> "admin"){
            $this->db->where('B.owner = ', $searchText);
        }else{
            if($searchText4 == 1 && $searchText <> "admin"){
                $this->db->where('B.owner = ', $searchText);
            }else if($searchText4 == 0 && $searchText <> "admin"){
                $this->db->where('B.owner = ', $searchText);
            }
        }

        //$this->db->where('B.owner = ', $searchText);
        //$this->db->where('date(A.createdDtm) >= ', $searchText1);
        //$this->db->where('date(A.createdDtm) <= ', $searchText2);

        if($searchText3 == 0){
            $this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);}

        if($searchText3 == 1){
            $this->db->where("STR_TO_DATE(A.transaction_date, '%Y%m%d') >= ", $searchText1);
            $this->db->where("STR_TO_DATE(A.transaction_date, '%Y%m%d') <= ", $searchText2);}
        
        if($searchText3 == 2)
            {$this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);
            $this->db->where('A.posted = 0');}
        
        if($searchText3 == 3)
            {$this->db->where('date(A.createdDtm) >= ', $searchText1);
            $this->db->where('date(A.createdDtm) <= ', $searchText2);
            $this->db->where('A.posted = 1');}

        $this->db->group_by('B.owner');

        $this->db->order_by('B.owner');
        
        $query = $this->db->get();
        $result = $query->result();
        return $result;

    }

    // ***************** Package Sales Report ****************** //
    function packageSalesReportCount($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '')
    {
        $this->db->select('BaseTbl.username, BaseTbl.amount, BaseTbl.invtype, BaseTbl.createdDtm, BaseTbl.remarks');
        $this->db->from('tbl_invoices as BaseTbl');
        $this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.srvid','left');

        $this->db->where('BaseTbl.managername = ', $searchText);
        $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
        $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
        //$this->db->where('BaseTbl.acctype =', 0);

        if($searchText3 == 0){
            $this->db->where('date(BaseTbl.srvdate) >= ', $searchText1);
            $this->db->where('date(BaseTbl.srvdate) <= ', $searchText2);}
        
        if($searchText3 == 1)
            {$this->db->where('date(BaseTbl.srvdate) >= ', $searchText1);
            $this->db->where('date(BaseTbl.srvdate) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Recharge"');}

        if($searchText3 == 2)
            {$this->db->where('date(BaseTbl.srvdate) >= ', $searchText1);
            $this->db->where('date(BaseTbl.srvdate) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Credit"');}

        if($searchText3 == 3){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);}
        
        if($searchText3 == 4){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Recharge"');}

        if($searchText3 == 5){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Credit"');}

        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function packageSalesReport($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '', $page, $segment)
    {
        if($searchText3 == 8){
            $add_to_qry = 'BaseTbl.username, sum(BaseTbl.amount) as amount, max(BaseTbl.invtype) as invtype, min(BaseTbl.createdDtm) as createdDtm,
                        min(IF(BaseTbl.srvdate="0000-00-00", DATE(BaseTbl.createdDtm), BaseTbl.srvdate)) as srvdate, 
                        min(IF(BaseTbl.expdate="0000-00-00", DATE(BaseTbl.createdDtm), BaseTbl.expdate)) as expdate, 
                        min(BaseTbl.remarks) as remarks, min(Services.srvname) as srvname, 
                        sum(BaseTbl.price) as costprice, sum(Services.saleprice) as saleprice,
                        min(EPT.transaction_date) as eppay,
                        (@row_number:=@row_number + 1) AS serial_number';
        }else{
            $add_to_qry = 'BaseTbl.username, BaseTbl.amount, BaseTbl.invtype, BaseTbl.createdDtm,
                        IF(BaseTbl.srvdate="0000-00-00", DATE(BaseTbl.createdDtm), BaseTbl.srvdate) as srvdate, 
                        IF(BaseTbl.expdate="0000-00-00", DATE(BaseTbl.createdDtm), BaseTbl.expdate) as expdate, 
                        BaseTbl.remarks, Services.srvname, 
                        BaseTbl.price as costprice, Services.saleprice,
                        EPT.transaction_date as eppay,
                        (@row_number:=@row_number + 1) AS serial_number';
        }

        $this->db->query('Set @row_number = 0');
        $this->db->select($add_to_qry);
        $this->db->from('tbl_invoices as BaseTbl');
        $this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.srvid','left');
        $this->db->join('tbl_eptransaction as EPT', 'BaseTbl.username = EPT.username 
                        and year(BaseTbl.srvdate) = mid(EPT.transaction_date, 1, 4) 
                        and month(BaseTbl.srvdate) = mid(EPT.transaction_date, 5, 2)', 'left');
        
        $this->db->where('BaseTbl.managername = ', $searchText);

        if($searchText3 == 0){
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->having('date(srvdate) >= ', $searchText1);
            $this->db->having('date(srvdate) <= ', $searchText2);
        }
        
        if($searchText3 == 1)
        {
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->where('BaseTbl.invtype = "Recharge"');
            $this->db->having('date(srvdate) >= ', $searchText1);
            $this->db->having('date(srvdate) <= ', $searchText2);
        }

        if($searchText3 == 2)            
        {
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->where('BaseTbl.invtype = "Credit"');
            $this->db->having('date(srvdate) >= ', $searchText1);
            $this->db->having('date(srvdate) <= ', $searchText2);
        }

        if($searchText3 == 3){
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
        }
        
        if($searchText3 == 4){
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Recharge"');
        }

        if($searchText3 == 5){
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Credit"');
        }
        
        if($searchText3 == 6){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
        }

        if($searchText3 == 7){
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('date(BaseTbl.srvdate) = "0000-00-00"');
            //$this->db->where('BaseTbl.invtype = "Recharge"');
        }

        if($searchText3 == 8){
            $this->db->where('BaseTbl.username <> ', $searchText);
            $this->db->where('date(BaseTbl.createdDtm) >= ', $searchText1);
            $this->db->where('date(BaseTbl.createdDtm) <= ', $searchText2);
            $this->db->where('BaseTbl.invtype = "Credit"');
            $this->db->group_by('BaseTbl.username');
        }

        $this->db->order_by('BaseTbl.createdDtm', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }

    // ********** Users Fair Use Policy Report *********//

    function usersFairUsePolicyCount($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = 0, $searchText4 = '')
    {
        if($searchText1 <> "" && $searchText2 <> ""){
            $this->db->select('users.username, users.firstname, users.lastname,users.mobile, users.owner, 
                                Round(sum(radaccount.acctinputoctets)/1073741824, 0) as upload, 
                                Round(sum(radaccount.acctoutputoctets)/1073741824, 0) as download');
            $this->db->from('rm_users as users');
            $this->db->join('radacct as radaccount', 'users.username = radaccount.username','left');
            if(!empty($searchText)) {
                $likeCriteria = "(users.username  LIKE '%".$searchText4."%'
                                OR  users.firstname  LIKE '%".$searchText4."%'
                                OR  users.mobile  LIKE '%".$searchText4."%')";
                $this->db->where($likeCriteria);
            }
            if($this->session->userdata ( 'name' ) <> "admin" && $this->ismaster == 0)
            {
                $this->db->where('owner', $this->session->userdata ( 'name' ));
            }elseif($this->ismaster == 1){
                $this->db->where('owner in (select managername from rm_managers where mastername = "'.$this->session->userdata ( 'name' ).'")');
            }else{
                $this->db->where('owner', $searchText);
            }

            $this->db->where('acctstarttime >=', $searchText1);
            $this->db->where('acctstarttime <=', $searchText2);

            $this->db->group_by('users.username');

            if($searchText3 <> NULL){
                $this->db->having('download > '.$searchText3);
            }

            $query = $this->db->get();
            
            return $query->num_rows();
        }else{ 
            return FALSE; 
        }
    }
    
    function usersFairUsePolicyListing($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = 0, $searchText4 = NULL, $page, $segment)
    {
        if($searchText1 <> "" && $searchText2 <> ""){
            $this->db->select('users.username, users.firstname, users.lastname, users.mobile, users.owner, 
                                Round(sum(radaccount.acctinputoctets)/1073741824, 0) as upload, 
                                Round(sum(radaccount.acctoutputoctets)/1073741824, 0) as download');

            $this->db->from('rm_users as users');
            $this->db->join('radacct as radaccount', 'users.username = radaccount.username','left');

            if(!empty($searchText)) {
                $likeCriteria = "(users.username  LIKE '%".$searchText4."%'
                                OR  users.firstname  LIKE '%".$searchText4."%'
                                OR  users.mobile  LIKE '%".$searchText4."%')";
                $this->db->where($likeCriteria);
            }
            if($this->session->userdata ( 'name' ) <> "admin" && $this->ismaster == 0)
            {
                $this->db->where('owner', $this->session->userdata ( 'name' ));
            }elseif($this->ismaster == 1){
                $this->db->where('owner in (select managername from rm_managers where mastername = "'.$this->session->userdata ( 'name' ).'")');
            }else{
                if($searchText <> ""){
                    $this->db->where('owner', $searchText);
                }
            }
            $this->db->where('acctstarttime >=', $searchText1);
            $this->db->where('acctstarttime <=', $searchText2);
            
            $this->db->group_by('users.username');
            if($searchText3 <> NULL){
                $this->db->having('download > '.$searchText3);
            }

            $this->db->limit($page, $segment);
            $this->db->order_by('download', 'DESC');

            $query = $this->db->get();
            $result = $query->result();
            return $result;

        }else{
            return FALSE;
        }
    }

    function usersFairUsePolicySummery($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '', $searchText4 = '')
    {
        if($searchText1 <> "" && $searchText2 <> ""){
            $this->db->select('users.owner, 
                                Round(sum(radaccount.acctinputoctets)/1073741824, 0) as upload, 
                                Round(sum(radaccount.acctoutputoctets)/1073741824, 0) as download');
            $this->db->from('rm_users as users');
            $this->db->join('radacct as radaccount', 'users.username = radaccount.username','left');
            if(!empty($searchText)) {
                $likeCriteria = "(users.username  LIKE '%".$searchText4."%'
                                OR  users.firstname  LIKE '%".$searchText4."%'
                                OR  users.mobile  LIKE '%".$searchText4."%')";
                $this->db->where($likeCriteria);
            }
            if($this->session->userdata ( 'name' ) <> "admin" && $this->ismaster == 0)
            {
                $this->db->where('owner', $this->session->userdata ( 'name' ));
            }elseif($this->ismaster == 1){
                $this->db->where('owner in (select managername from rm_managers where mastername = "'.$this->session->userdata ( 'name' ).'")');
            }else{
                $this->db->where('owner', $searchText);
            }

            $this->db->where('date(acctstarttime) >=', $searchText1);
            $this->db->where('date(acctstarttime) <=', $searchText2);

            $this->db->group_by('users.owner');
            $query = $this->db->get();

            $result = $query->result();
            return $result;

        }else{
            return FALSE;
        }
    }

    function dashboard_fairuse_alert($ismaster, $managername)
    {

        //log_message('debug', "Job Started: Model dashboard_fairuse_alert: $ismaster, $managername");

        $start = date('Y-m-01', strtotime('-1 month'));
        $end = date('Y-m-t', strtotime('-1 month'));
        $this->db->select('U.username, U.firstname, U.lastname, 
            ROUND(SUM(R.acctinputoctets)/1073741824, 0) as upload, 
            ROUND(SUM(R.acctoutputoctets)/1073741824, 0) as download');
        $this->db->from('rm_users as U');
        $this->db->join('radacct as R', 'U.username = R.username', 'inner');
        $this->db->where('R.acctstarttime >=', $start);
        $this->db->where('R.acctstarttime <=', $end);
        if($managername <> 'admin' && $ismaster == 0){
            $this->db->where('U.owner', $managername);
        }elseif($ismaster == 1){
            $this->db->where('U.owner in (select managername from rm_managers where mastername = "'.$managername.'")');
        }
        $this->db->group_by('U.username');
        $this->db->order_by('SUM(R.acctinputoctets) + SUM(R.acctoutputoctets)', 'DESC');
        $this->db->limit(5);
        $query = $this->db->get();
        return $query->result();

    }

    function dashboard_sales_alert(){

        //$date2 = Date("Y-m-d");
        //$date1 = date('Y-m-d', strtotime("-1 month", strtotime($date2)));

        $this->db->select('salesmonth, sum(recharge) as recharge, sum(credit) as credit, sum(refund) as refund');
        $this->db->from('temp_salessummery');

        if($this->session->userdata ( 'name' ) <> "admin" && $this->ismaster == 0)
        {
            $this->db->where('managername', $this->session->userdata ( 'name' ));
        }elseif($this->ismaster == 1){
            $this->db->where('managername in (select managername from rm_managers where mastername = "'.$this->session->userdata ( 'name' ).'")');
        }

        $this->db->group_by('salesmonth');
        $this->db->where('recharge > 0');
        $this->db->order_by('entrydate', 'ASCE');
        //$this->db->limit(9, 1);

        $query = $this->db->get();
        $result = $query->result();
        return $result;

    }


    /* Radius Report

    <th>S.No.</th>
                        <th>Manager</th>
                        <th>Username</th>
                        <th>NasIP</th>
                        <th>StationID</th>
                        <th>Type</th>
                        <th>Start</th>
                        <th>Stop</th>
                        <th>Session</th>
                        <th>Input</th>
                        <th>Output</th>
                        <th>IP</th> */
    
    function radiusReport($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '', $page, $segment){

        if(!empty($searchText))
            {
                // Check if the manager has a master name
                $this->db->select('COUNT(*) as count');
                $this->db->from('rm_managers');
                $this->db->where('mastername', $searchText);
                $query = $this->db->get();
                $result = $query->row();

                //(@refund:=@refund + BaseTbl.amount) AS baseprice
                $this->db->query('Set @id = 0');
                $this->db->select('A.managername, 
                                    if('.$searchText3.' <> 0, count(subquery.username), subquery.username) as users,  
                                    min(subquery.radacctid) as radacctid,
                                    subquery.nasipaddress, subquery.calledstationid, subquery.servicetype, subquery.acctstarttime, 
                                    subquery.acctstoptime, subquery.acctsessiontime, subquery.acctinputoctets, 
                                    subquery.acctoutputoctets, subquery.framedipaddress');
                $this->db->from('rm_managers as A');
                $this->db->join('rm_users as B', 'A.managername = B.owner', 'left');

                // Subquery to get distinct usernames
            
                $subquery = '(SELECT username, min(radacctid) as radacctid, nasipaddress, calledstationid, servicetype, acctstarttime, acctstoptime,
                                acctsessiontime, acctinputoctets, acctoutputoctets, framedipaddress 
                                FROM radacct AS C where acctstarttime >= "'.$searchText1.'" 
                                and acctstarttime <= "'.$searchText2.'" 
                                and not exists (
                                    select 1 from radacct as CA where C.username = CA.username
                                    and C.acctstarttime < "'.$searchText1.'"
                                )
                                group by username) AS subquery'; 
                

                /*$subquery = "(SELECT ra.*
                                FROM radacct ra
                                INNER JOIN rm_cards rc ON ra.username = rc.cardnum
                                WHERE ra.acctstarttime BETWEEN '".$searchText1."' AND '".$searchText2."'
                                  AND rc.active = 1
                                  AND NOT EXISTS (
                                      SELECT 1
                                      FROM radacct ra2
                                      WHERE ra2.username = ra.username
                                        AND ra2.acctstarttime < '".$searchText1."'
                                  )
                                ) as subquery;";*/

                $this->db->join($subquery, 'B.username = subquery.username', 'left');
                //$this->db->join('radacct as C', 'subquery.username = C.username', 'left');

                $this->db->where('DATE(subquery.acctstarttime) >=', $searchText1);
                $this->db->where('DATE(subquery.acctstarttime) <=', $searchText2);

                if ($result->count > 0) {
                    $this->db->where_in('A.managername', '(SELECT managername FROM rm_managers WHERE rm_managers.mastername = "' . $searchText . '"
                                        or rm_managers.managername = "' . $searchText . '")', FALSE);
                }else{
                    $this->db->where('A.managername', $searchText);
                }

                switch ($searchText3) {

                    case 0:
                        $this->db->group_by(['B.username', 'A.managername']);
                        break;

                    case 1:
                        $this->db->group_by(['subquery.nasipaddress', 'A.managername']);
                        break;

                    case 2:
                        $this->db->group_by(['subquery.calledstationid', 'A.managername']);
                        break;

                    case 3:
                        $this->db->group_by(['subquery.servicetype', 'A.managername']);
                        break;

                    case 4:
                        $this->db->group_by(['month(subquery.acctstarttime)', 'A.managername']);
                        break;

                    case 5:
                        $this->db->group_by(['year(subquery.acctstarttime)', 'A.managername']);
                        break;

                }

                //$this->db->group_by(['subquery.calledstationid', 'A.managername']);
                $this->db->order_by('A.managername');
                
                $query = $this->db->get();

                return $query->result();
        }
    }

    /**
     * Get online/offline user counts per hour for the last 24 hours
     */
    public function getOnlineOfflineByHourLast24h($ismaster, $managername)
    {
        $now = date('Y-m-d H:00:00');
        $yesterday = date('Y-m-d H:00:00', strtotime('-24 hours'));
        $hours = [];
        for ($i = 23; $i >= 0; $i--) {
            $h = date('Y-m-d H:00:00', strtotime("-$i hour", strtotime($now)));
            $hours[$h] = ['online' => 0, 'offline' => 0];
        }
        $online_users = [];
        $this->db->select('radacct.username');
        $this->db->from('radacct as radacct');
        $this->db->join('rm_users', 'radacct.username = rm_users.username', 'inner');
        $this->db->where('acctstoptime IS NULL', null, false);
        $this->db->where('rm_users.expiration >=', $now);
        if($managername <> "admin" && $ismaster == 0){
            $this->db->where('rm_users.owner', $managername);
        }elseif($ismaster == 1){
            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('rm_users.owner', $manager_chain);
        }
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $online_users[$row->username] = true;
        }
        $events = [];
        $this->db->select('radacct.username, acctstarttime, acctstoptime');
        $this->db->from('radacct as radacct');
        $this->db->join('rm_users', 'radacct.username = rm_users.username', 'inner');
        $this->db->where('(
            (acctstarttime >= ' . $this->db->escape($yesterday) . ' AND acctstarttime <= ' . $this->db->escape($now) . ')
            OR (acctstoptime >= ' . $this->db->escape($yesterday) . ' AND acctstoptime <= ' . $this->db->escape($now) . ')
        )', null, false);
        $this->db->where('rm_users.expiration >=', $yesterday);
        if($managername <> "admin" && $ismaster == 0){
            $this->db->where('rm_users.owner', $managername);
        }elseif($ismaster == 1){
            $this->db->where('rm_users.owner in (select managername from rm_managers where mastername = "'.$managername.'")');
        }
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            if ($row->acctstarttime) {
                $h = date('Y-m-d H:00:00', strtotime($row->acctstarttime));
                $events[$h]['start'][] = $row->username;
            }
            if ($row->acctstoptime) {
                $h = date('Y-m-d H:00:00', strtotime($row->acctstoptime));
                $events[$h]['stop'][] = $row->username;
            }
        }
        $hour_keys = array_keys($hours);
        $current_online = $online_users;
        foreach (array_reverse($hour_keys) as $h) {
            $offline_users = [];
            if (!empty($events[$h]['stop'])) {
                foreach ($events[$h]['stop'] as $u) {
                    unset($current_online[$u]);
                    $offline_users[$u] = true;
                }
            }
            if (!empty($events[$h]['start'])) {
                foreach ($events[$h]['start'] as $u) {
                    $current_online[$u] = true;
                }
            }
            $hours[$h]['online'] = count($current_online);
            $hours[$h]['offline'] = count($offline_users);
        }
        return $hours;
    }

    /**
     * Get sales per month for the last 12 months (only valid users), in millions
     */
    public function getSalesByMonthLastYear($ismaster, $managername)
    {
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[date('Y-m', strtotime("-$i months"))] = ['sales' => 0, 'paid' => 0];
        }
        $this->db->select([
            "DATE_FORMAT(BaseTbl.srvdate, '%Y-%m') as month",
            "SUM(CASE WHEN BaseTbl.amount < 0 THEN ABS(BaseTbl.amount) ELSE -BaseTbl.amount END) as sales",
            "SUM(BaseTbl.paid) as paid"
        ]);
        $this->db->from('tbl_invoices as BaseTbl');
        $this->db->join('rm_users as Users', 'BaseTbl.username = Users.username', 'inner');
        $this->db->where('BaseTbl.srvdate >=', date('Y-m-01', strtotime('-11 months')));
        if($managername <> 'admin' && $ismaster == 0){
            $this->db->where('Users.owner', $managername);
        }elseif($ismaster == 1){

            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('Users.owner', $manager_chain);

        }
        $this->db->group_by("month");
        $query = $this->db->get();
        foreach ($query->result() as $row) {
            $month = $row->month;
            if (isset($months[$month])) {
                $months[$month]['sales'] = round($row->sales / 1000000, 2);
                $months[$month]['paid'] = round($row->paid / 1000000, 2);
            }
        }
        return $months;
    }

    public function getTop5ServicePlansLastMonth($ismaster, $managername)
    {
        $start = date('Y-m-01', strtotime('-1 month'));
        $end = date('Y-m-t', strtotime('-1 month'));
        $this->db->select('A.srvname, A.saleprice, ABS(SUM(B.amount)) as sales, SUM(B.paid) as paid');
        $this->db->from('tbl_services as A');
        $this->db->join('tbl_invoices as B', 'A.srvid = B.srvid', 'left');
        $this->db->where('B.srvdate >=', $start);
        $this->db->where('B.srvdate <=', $end);
        if($managername <> 'admin' && $ismaster == 0){
            $this->db->where('A.managername', $managername);
        }elseif($ismaster == 1){
            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('Users.owner', $manager_chain);
        }
        $this->db->group_by('A.srvname');
        $this->db->order_by('sales', 'DESC');
        $this->db->limit(5);
        $query = $this->db->get();
        return $query->result();
    }

}
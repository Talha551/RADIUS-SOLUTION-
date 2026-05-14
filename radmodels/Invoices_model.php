<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Invoices_model extends CI_Model
{

    function invoiceListingCount($searchText = '', $searchUsername = '', $searchRenewDate = '', $searchRenewDateTo = '', $searchManager = '')
    {
        $managername = $this->session->userdata ( 'name' );

        $this->db->select('BaseTbl.username, BaseTbl.amount, BaseTbl.invtype, date(BaseTbl.createdDtm) as createdDtm, BaseTbl.remarks');
        $this->db->from('tbl_invoices as BaseTbl');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.username  LIKE '%".$searchText."%'
                            OR  BaseTbl.amount  LIKE '%".$searchText."%'
                            OR  BaseTbl.invtype  LIKE '%".$searchText."%'
                            OR  BaseTbl.createdDtm  LIKE '%".$searchText."%'
                            OR  BaseTbl.remarks  LIKE '%".$searchText."%'
                            OR  BaseTbl.transid  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        
        if(!empty($searchUsername)) {
            $this->db->where('BaseTbl.username', $searchUsername);
        }

        if(!empty($searchManager)) {
            $this->db->where('BaseTbl.managername', $searchManager);
        }

        if(!empty($searchRenewDate)) {
            $this->db->where('DATE(BaseTbl.createdDtm) >= ', $searchRenewDate);
        }

        if(!empty($searchRenewDateTo)) {
            $this->db->where('DATE(BaseTbl.createdDtm) <= ', $searchRenewDateTo);
        }

        //$this->db->where('BaseTbl.createdDtm >= curdate() - interval 1 month');
        
        if($managername <> "admin" && $this->ismaster == 0){
            $this->db->where('BaseTbl.managername = ', $managername);
        }
        elseif($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            if (!empty($manager_chain)) {
                $this->db->where_in('BaseTbl.managername', $manager_chain);
            }    
        }


        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function invoiceListing($searchText = '', $searchUsername = '', $searchRenewDate = '', $searchRenewDateTo = '', $page, $segment, $searchManager = '')
    {
        $managername = $this->session->userdata ( 'name' );

        $this->db->query('Set @row_number = 0');

        $this->db->select('BaseTbl.username, BaseTbl.amount, BaseTbl.invtype, date(BaseTbl.createdDtm) as createdDtm,
                            BaseTbl.srvdate, BaseTbl.expdate, BaseTbl.remarks, Services.srvname, BaseTbl.managername,
                            BaseTbl.price as costprice, Services.saleprice, paid, crdays, jvid, 
                            "" as eppay,
                            (@row_number:=@row_number + 1) AS serial_number');

        $this->db->from('tbl_invoices as BaseTbl');

        $this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.srvid','left');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.username  LIKE '%".$searchText."%'
                            OR  BaseTbl.amount  LIKE '%".$searchText."%'
                            OR  BaseTbl.invtype  LIKE '%".$searchText."%'
                            OR  BaseTbl.createdDtm  LIKE '%".$searchText."%'
                            OR  BaseTbl.remarks  LIKE '%".$searchText."%'
                            OR  BaseTbl.transid  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        
        if(!empty($searchUsername)) {
            $this->db->where('BaseTbl.username', $searchUsername);
        }

        if(!empty($searchManager)) {
            $this->db->where('BaseTbl.managername', $searchManager);
        }

        if(!empty($searchRenewDate)) {
            $this->db->where('DATE(BaseTbl.createdDtm) >= ', $searchRenewDate);
        }else{
            $this->db->where('DATE(BaseTbl.createdDtm) >= curdate() - interval 1 month', null, false);
        }

        if(!empty($searchRenewDateTo)) {
            $this->db->where('DATE(BaseTbl.createdDtm) <= ', $searchRenewDateTo);
        }else{
            $this->db->where('DATE(BaseTbl.createdDtm) <= curdate()', null, false);
        }

        //$this->db->where('BaseTbl.createdDtm >= curdate() - interval 1 month');
        
        if($managername <> "admin" && $this->ismaster == 0){
            $this->db->where('BaseTbl.managername = ', $managername);
        }
        elseif($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            if (!empty($manager_chain)) {
                $this->db->where_in('BaseTbl.managername', $manager_chain);
            }    
        }

        $this->db->order_by('BaseTbl.transid, BaseTbl.createdDtm', 'DESC');

        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }

    function invoiceListingSummery($searchText = '', $searchUsername = '', $searchRenewDate = '', $searchRenewDateTo = '', $searchManager = '')
    {
        $managername = $this->session->userdata ( 'name' );
        
        // Get overall sums
        $this->db->select_sum('BaseTbl.amount');
        $this->db->select_sum('BaseTbl.paid');
        $this->db->from('tbl_invoices as BaseTbl');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.username  LIKE '%".$searchText."%'
                            OR  BaseTbl.amount  LIKE '%".$searchText."%'
                            OR  BaseTbl.invtype  LIKE '%".$searchText."%'
                            OR  BaseTbl.createdDtm  LIKE '%".$searchText."%'
                            OR  BaseTbl.remarks  LIKE '%".$searchText."%'
                            OR  BaseTbl.transid  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        
        if(!empty($searchUsername)) {
            $this->db->where('BaseTbl.username', $searchUsername);
        }

        if(!empty($searchManager)) {
            $this->db->where('BaseTbl.managername', $searchManager);
        }

        if(!empty($searchRenewDate)) {
            $this->db->where('DATE(BaseTbl.createdDtm) >= ', $searchRenewDate);
        }else{
            $this->db->where('DATE(BaseTbl.createdDtm) >= curdate() - interval 1 month', null, false);
        }

        if(!empty($searchRenewDateTo)) {
            $this->db->where('DATE(BaseTbl.createdDtm) <= ', $searchRenewDateTo);
        }else{
            $this->db->where('DATE(BaseTbl.createdDtm) <= curdate()', null, false);
        }


        //$this->db->where('BaseTbl.createdDtm >= curdate() - interval 1 month');
        
        if($managername <> "admin" && $this->ismaster == 0){
            $this->db->where('BaseTbl.managername = ', $managername);
        }
        elseif($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            if (!empty($manager_chain)) {
                $this->db->where_in('BaseTbl.managername', $manager_chain);
            }    
        }

        $overall_query = $this->db->get();
        $overall_result = $overall_query->row();
        
        // Get sums by invtype
        $this->db->select('BaseTbl.invtype, SUM(BaseTbl.amount) as type_amount');
        $this->db->from('tbl_invoices as BaseTbl');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.username  LIKE '%".$searchText."%'
                            OR  BaseTbl.amount  LIKE '%".$searchText."%'
                            OR  BaseTbl.invtype  LIKE '%".$searchText."%'
                            OR  BaseTbl.createdDtm  LIKE '%".$searchText."%'
                            OR  BaseTbl.remarks  LIKE '%".$searchText."%'
                            OR  BaseTbl.transid  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        
        if(!empty($searchUsername)) {
            $this->db->where('BaseTbl.username', $searchUsername);
        }

        if(!empty($searchManager)) {
            $this->db->where('BaseTbl.managername', $searchManager);
        }

        if(!empty($searchRenewDate)) {
            $this->db->where('DATE(BaseTbl.createdDtm) >= ', $searchRenewDate);
        }else{
            $this->db->where('DATE(BaseTbl.createdDtm) >= curdate() - interval 1 month', null, false);
        }

        if(!empty($searchRenewDateTo)) {
            $this->db->where('DATE(BaseTbl.createdDtm) <= ', $searchRenewDateTo);
        }else{
            $this->db->where('DATE(BaseTbl.createdDtm) <= curdate()', null, false);
        }


        //$this->db->where('BaseTbl.createdDtm >= curdate() - interval 1 month');
        
        if($managername <> "admin" && $this->ismaster == 0){
            $this->db->where('BaseTbl.managername = ', $managername);
        }
        elseif($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            if (!empty($manager_chain)) {
                $this->db->where_in('BaseTbl.managername', $manager_chain);
            }    
        }

        $this->db->group_by('BaseTbl.invtype');
        $type_query = $this->db->get();
        $type_results = $type_query->result();
        
        // Create a single result object with all data
        $result = new stdClass();
        $result->amount = $overall_result->amount ?: 0;
        $result->paid = $overall_result->paid ?: 0;
        
        // Initialize all invtype amounts to 0
        $result->activation_amount = 0;
        $result->credit_amount = 0;
        $result->debit_amount = 0;
        $result->gracedays_amount = 0;
        $result->recharge_amount = 0;
        $result->refund_amount = 0;
        
        // Set the actual values from the grouped query
        foreach($type_results as $type_row) {
            $type_field = strtolower($type_row->invtype) . '_amount';
            if(property_exists($result, $type_field)) {
                $result->$type_field = $type_row->type_amount ?: 0;
            }
        }
        
        return $result;
    }

    function getPackagePrice($manager, $service_id)
    {
        $this->db->select('srvid, radsrvid, managername, baseprice as unitprice, baseprice as baseprice, baseprice as saleprice');
        $this->db->from("tbl_services");
        $this->db->where('radsrvid', $service_id);
        $this->db->where('managername', $manager);

        $query = $this->db->get();

        $result = $query->row();

        if ($query->num_rows() > 0){
            return $result;
        } else {
            return false;
        }
        //foreach($result as $row)
        //{
        //    $output = $row->unitprice;
            //$output = '<input type="text" class="form-control required" id="amount" value="'.$row->unitprice.'" name="amount" maxlength="20">';
        //}
        //return $result;
    }

    function getPackagePriceSrvID($manager, $service_id){
        $this->db->select('baseprice as unitprice, baseprice as saleprice, baseprice as baseprice');
        $this->db->from("tbl_services");
        $this->db->where('srvid', $service_id);
        $this->db->where('managername', $manager);
        $query = $this->db->get();

        $result = $query->row();

        if ($query->num_rows() > 0){
            return $result;
        } else {
            return false;
        }

    }

    function addRecharge($userInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_invoices', $userInfo);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        return $insert_id;
    }

    public function getLastRechargeOrActivationInvoice($username)
    {
        $this->db->select('transid, username, invtype, expdate');
        $this->db->from('tbl_invoices');
        $this->db->where('username', $username);
        $this->db->group_start();
        $this->db->where('invtype', 'Recharge');
        $this->db->or_where('invtype', 'Activation');
        $this->db->group_end();
        $this->db->order_by('transid', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        return $query->row();
    }

    function addCustomerCredit($userInfo){
        
        $this->db->trans_start();
        $this->db->insert('tbl_recharge', $userInfo);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        return True;

    }

    function deleteCustomerCredit($username)
    {

        $this->load->helper('date');
        $dateFormat = "%Y-%m-%d";

        $this->db->trans_start();
        $this->db->where('username', $username);
        $this->db->where('srvdate <= ', mdate($dateFormat));
        $this->db->delete('tbl_recharge');
        $this->db->trans_complete();

        return True;

    }

    function balanceCustomerCredit($username)
    {

        $this->load->helper('date');
        $dateFormat = "%Y-%m-%d";

        $this->db->trans_start();
        $this->db->where('username', $username);
        $this->db->where('srvdate <= ', mdate($dateFormat));
        $this->db->insert('tbl_recharge', "Select usernme,");
        $this->db->trans_complete();

        return True;

    }

    function addDownloadLimit($user, $addTrafficInfo)
    {
        $this->db->where('username', $user);
        $this->db->update('rm_users', $addTrafficInfo);
        
        return TRUE;
    }

    function getRechargeInfo($username, $managername = null, $inactive_days_bill_val = 0)
    {
        if($managername == null){
            $managername = $this->session->userdata('name');
        }

        $gracedays = $this->getManagerSettings_bytype($managername, "GRACE-DAYS-OLD");
        $gracedays = isset($gracedays[0]->stgvalue) ? $gracedays[0]->stgvalue : 0;

        //srvdate >= curdate() - INTERVAL ".$gracedays." DAY and
        
        /*if (!empty($inactive_days_bill_val) && $inactive_days_bill_val > 0) {
            $currentDateLogic = 'if(srvdate >= curdate() - INTERVAL '.$gracedays.' DAY, srvdate, IF(DATE_ADD(expdate, INTERVAL ' . (int)$inactive_days_bill_val . ' DAY) >= curdate(), expdate, curdate()))';
        } else {
            $currentDateLogic = 'if(srvdate >= curdate() - INTERVAL '.$gracedays.' DAY, srvdate, curdate())';
        }

        $rechargeInfo = $this->db->select($currentDateLogic . ' as currentdate, srvdate, expdate, invtype, amount, remarks', FALSE)
            ->from('tbl_invoices')
            ->where('username', $username)
            ->where('invtype', 'Gracedays')
            ->where("transid IN (Select Max(transid) from tbl_invoices where username ='".$username."')", null, false)
            ->get()
            ->row();*/

        $rechargeInfo = '';

        if(empty($rechargeInfo) || $rechargeInfo->invtype <> 'Gracedays'){
            if (!empty($inactive_days_bill_val) && $inactive_days_bill_val > 0) {
                $this->db->select("IF(DATE_ADD(expdate, INTERVAL " . (int)$inactive_days_bill_val . " DAY) >= curdate(), expdate, curdate()) as currentdate, srvdate, expdate, invtype, amount, remarks", FALSE);
            } else {
                $this->db->select('curdate() as currentdate, srvdate, expdate, invtype, amount, remarks');
            }
            $this->db->from("tbl_invoices");
            $this->db->where('username', $username);

            $this->db->group_start();
            $this->db->where('invtype', 'Recharge');
            $this->db->or_where('invtype', 'Activation');
            $this->db->group_end();

            //$this->db->where("expdate IN (Select Max(expdate) from tbl_invoices where expdate<>'0000-00-00' and invtype='Recharge' and username='".$username."')", null, false);
            $this->db->where("transid IN (Select Max(transid) from tbl_invoices where invtype in ('Recharge', 'Activation') and username ='".$username."')", null, false);
            $query = $this->db->get();

            $rechargeInfo = $query->row();

            //print_r($rechargeInfo);
            //exit;
        }

        if (!empty($rechargeInfo)){
            return $rechargeInfo;
        } else {
            return false;
        }

        //foreach($result as $row)
        //{
        //    $output = $row->unitprice;
            //$output = '<input type="text" class="form-control required" id="amount" value="'.$row->unitprice.'" name="amount" maxlength="20">';
        //}
        //return $result;
    }

    function updateExpiry($qryUpdateExpiry, $user, $exp_date = NULL)
    {
        $this->db->where('username', $user);
        $this->db->update('rm_users', $qryUpdateExpiry);
        
        return TRUE;
    }
    
    function checkManagerBalance($managername){
        $this->db->select_sum('amount');
        $this->db->from('tbl_invoices');
        $this->db->where('managername', $managername);

        $query = $this->db->get();
        $result = $query->row();

        if ($query->num_rows() > 0){
            return $result;
        } else {
            return false;
        }
    }

    function getLastExpiry($username)
    {

        //$this->db->select_max('expdate');
        //$this->db->from("tbl_invoices");
        //$this->db->where('username', $username);
        //$this->db->where('invtype', 'Recharge');

        $this->db->select('expiration as expdate');
        $this->db->from("rm_users");
        $this->db->where('username', $username);

        $query = $this->db->get();

        $result = $query->row();

        if ($query->num_rows() > 0){
            return $result;
        } else {
            return false;
        }
    }

    function getUserPackage($userid, $managername)
    {
        $this->db->select('Users.srvid as srvid, Services.srvid as managersrvid, Services.srvname as managersrvname,
                            UserDocs.discount, UserDocs.adjamount, Services.baseprice as costprice, Services.baseprice as saleprice');
        $this->db->from("rm_users as Users");
        $this->db->join('tbl_services as Services', 'Users.srvid = Services.radsrvid','left');
        $this->db->join('tbl_userdocs as UserDocs', 'Users.username = UserDocs.username','left');
        $this->db->where('Users.username', $userid);
        $this->db->where('Services.managername', $managername);

        $query = $this->db->get();
        $result = $query->row();

        if ($query->num_rows() > 0){
            return $result;
        } else {
            return false;
        }
    }

    function getUserPrevPackage($userid, $managername)
    {
        $query = $this->db->query("select A.srvid, B.radsrvid from tbl_invoices as A left join tbl_services as B on A.srvid=B.srvid 
                    where transid in (select max(transid) from tbl_invoices where username ='".$userid."' 
                    and managername = '".$managername."') and A.username ='".$userid."' and B.managername = '".$managername."'");

        $row   = $query->row();

        if ($query->num_rows() > 0){
            return $row;
        }else{
            return false;
        }
    }

    function updateUserExpiryNull($user, $qryUpdateExpiryNull)
    {
        $this->db->where('username', $user);
        $this->db->update('tbl_invoices', $qryUpdateExpiryNull);
        
        return TRUE;
    }

    function getLastSessionDate($username){

        $query = $this->db->query("select username, date(max(acctstoptime)) as expdate from radacct where username ='".$username."' group by username");
        $row   = $query->row();
        if ($query->num_rows() > 0){
            return $row->expdate;
        }

    }

    function getMasterManager($manager){
        $this->db->select('*');
        $this->db->from("rm_managers");
        $this->db->where('managername', $manager);
        $query = $this->db->get();

        $result = $query->row();

        if ($query->num_rows() > 0){
            return $result;
        } else {
            return false;
        }

    }
    public function update_price_by_series($series, $new_price) {

        $managername = $this->session->userdata ( 'name' );
        // Update the value for all records with a specific series
        $this->db->set('value', $new_price);
        $this->db->where('series', $series);

        if($this->ismaster > 0){
            $this->db->where('owner in (select managername from rm_managers where mastername = "'.$managername.'")');
        }

        return $this->db->update('rm_cards');

    }

    /* CARDS COLLECTION */

    // Get unique series from the rm_cards table
    public function get_unique_series() {
        $this->db->distinct();
        $this->db->select('series, owner, date');
        $this->db->order_by('date, series', 'DESC');
        return $this->db->get('rm_cards')->result();
    }

    // Insert new collection record
    public function insert_cardscollection($data) {
        return $this->db->insert('tbl_cardscollection', $data);
    }


    // Count all collection records
    public function get_all_cardscollections_count() {

        return $this->db->count_all('tbl_cardscollection');

    }

    // Fetch all collection records
    public function get_all_cardscollections($page, $segment) {

        $this->db->order_by('TransID', 'DESC');
        $this->db->limit($page, $segment);
        return $this->db->get('tbl_cardscollection')->result();

    }

    // Fetch a specific collection by TransID
    public function get_cardscollection_by_id($TransID) {
        $this->db->where('TransID', $TransID);
        return $this->db->get('tbl_cardscollection')->row();
    }

    // Update collection record
    public function update_cardscollection($TransID, $data) {
        $this->db->where('TransID', $TransID);
        return $this->db->update('tbl_cardscollection', $data);
    }

    function cardsseries_info($series = ''){

        $this->db->select("
            series,
            COUNT(CASE WHEN active = 1 THEN 1 END) AS sold_cards,
            COUNT(CASE WHEN active = 0 THEN 1 END) AS unsold_cards,
            COUNT(*) AS total_cards,
            SUM(CASE WHEN active = 1 THEN value ELSE 0 END) AS sales_value
        ");

        $this->db->where('series', $series);
        $this->db->group_by('series');

        $query = $this->db->get('rm_cards'); // Replace with your actual table name if different
        return $query->row(); // Return a single row

    }

    /**
     * Get all usernames for dropdown
     */
    function getUsernamesList()
    {
        $managername = $this->session->userdata('name');
        
        $this->db->select('username');
        $this->db->from('rm_users');
       
        if($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            if (!empty($manager_chain)) {
                $this->db->where_in('rm_users.owner', $manager_chain);
            }    
        }elseif($managername != "admin"){
            $this->db->where('owner', $managername);
        }
        
        $this->db->order_by('username', 'ASC');
        $query = $this->db->get();
        
        return $query->result();
    }


    function masterCreditRefund($username, $srvid) {
        $current_date = date('Y-m-d');
        
        // Get all future transactions for the user
        $this->db->select('transid, username, srvid, price, amount, invtype, managername, srvdate, expdate, paid, jvid');
        $this->db->from('tbl_invoices');
        $this->db->where('username', $username);
        $this->db->where('srvid', $srvid);
        $this->db->where('expdate >', $current_date);
        $this->db->where('invtype <>', 'Refund');
        $this->db->order_by('transid', 'ASC');
        $transactions = $this->db->get()->result();
        
        if (empty($transactions)) {
            return false;
        }

        //print_r($transactions);
        //exit;
        
        // Group transactions by manager and calculate refunds
        $manager_refunds = array();
        foreach ($transactions as $transaction) {

            $days_total = (strtotime($transaction->expdate) - strtotime($transaction->srvdate)) / (60 * 60 * 24);
            if($transaction->srvdate <= $current_date){
                $days_remaining = (strtotime($transaction->expdate) - strtotime($current_date)) / (60 * 60 * 24);
            }else{
                $days_remaining = (strtotime($transaction->expdate) - strtotime($transaction->srvdate)) / (60 * 60 * 24);
            }
            
            if ($days_total > 0) {
                $daily_rate = $transaction->amount / $days_total;
                $refund_amount = $daily_rate * ($days_remaining - 1);
                
                if (!isset($manager_refunds[$transaction->managername])) {
                    $manager_refunds[$transaction->managername] = array(
                        'total_refund' => 0,
                        'transactions' => array()
                    );
                }
                
                $manager_refunds[$transaction->managername]['total_refund'] += $refund_amount;
                $manager_refunds[$transaction->managername]['transactions'][] = array(
                    'srvid' => $transaction->srvid,
                    'original_amount' => $transaction->amount,
                    'refund_amount' => $refund_amount,
                    'srvdate' => $transaction->srvdate,
                    'expdate' => $transaction->expdate
                );

                $this->db->trans_start();
                $this->db->where('transid', $transaction->transid);
                $this->db->update('tbl_invoices', array('invtype' => 'Refund'));
                $this->db->trans_complete();
                
            }
        }

        //print_r($manager_refunds);
        //exit;
        
        // Start transaction
        $this->db->trans_start();
        
        try {
            // Create reverse entries for each manager
            foreach ($manager_refunds as $managername => $refund_data) {
                $refund_amount = round($refund_data['total_refund'], 2);
                
                // For base manager (level3), it's a credit
                // For other managers (level1, level2), it's a debit
                $invtype = ($refund_amount < 0) ? 'Credit' : 'Debit';
                
                $invoice_data = array(
                    'username' => $username,
                    'srvid' => $refund_data['transactions'][0]['srvid'],
                    'price' => 0.00,
                    'amount' => ($invtype == 'Credit') ? abs($refund_amount ): -$refund_amount,
                    'invtype' => 'Refund',
                    'managername' => $managername,
                    'createdBy' => 0,
                    'srvdate' => $current_date,
                    'expdate' => $current_date,
                    'remarks' => 'Package cancellation refund for remaining days'
                );
                
                $this->db->insert('tbl_invoices', $invoice_data);
            }
            
            $this->db->trans_complete();
            
            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Failed to create refund entries for user: ' . $username);
                return false;
            }
            
            return true;
            
        } catch (Exception $e) {
            $this->db->trans_rollback();
            log_message('error', 'Error in masterCreditRefund: ' . $e->getMessage());
            return false;
        }
    }

    function walletListingCount($searchText = '', $searchFromDate = '', $searchToDate = '')
    {
        $this->db->select('BaseTbl.walletid, BaseTbl.walletname, BaseTbl.managername, BaseTbl.wallettype, 
                            BaseTbl.opdate, BaseTbl.closdate, BaseTbl.opbal, BaseTbl.totdebit, BaseTbl.totcredit, 
                            BaseTbl.closbal');
        $this->db->from('tbl_paywallets as BaseTbl');
        
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.walletname LIKE '%".$searchText."%'
                            OR BaseTbl.managername LIKE '%".$searchText."%'
                            OR BaseTbl.remarks LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        // Add date range filters
        if(!empty($searchFromDate)) {
            $this->db->where('BaseTbl.opdate >=', $searchFromDate);
        }
        if(!empty($searchToDate)) {
            $this->db->where('BaseTbl.opdate <=', $searchToDate);
        }

        $managername = $this->session->userdata ( 'name' );

        if($managername != "admin" && $this->ismaster == 0){
            $this->db->where('BaseTbl.managername', $managername);
        }elseif($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            if (!empty($manager_chain)) {
                $this->db->where_in('BaseTbl.managername', $manager_chain);
            }
        }
        
        $query = $this->db->get();
        return $query->num_rows();
    }

    function walletListing($searchText = '', $page, $segment, $searchFromDate = '', $searchToDate = '')
    {
        $managername = $this->session->userdata ( 'name' );
        
        // Build the base query with dynamic opening balance calculation
        $select_fields = 'BaseTbl.walletid, BaseTbl.walletname, BaseTbl.managername, BaseTbl.wallettype, BaseTbl.opdate, 
                         BaseTbl.closdate, BaseTbl.opbal';
        
        // Add dynamic opening balance calculation when date filter is applied
        if(!empty($searchFromDate)) {
            $select_fields .= ', (BaseTbl.opbal + COALESCE((
                SELECT SUM(InvCol2.paidamount) 
                FROM tbl_invoicecollection InvCol2 
                WHERE InvCol2.walletid = BaseTbl.walletid 
                AND InvCol2.paydate < "'.$searchFromDate.'"
            ), 0) + COALESCE((
                SELECT SUM(
                    CASE jvtype
                        WHEN 0 THEN debit
                        WHEN 1 THEN debit
                        WHEN 2 THEN debit
                        WHEN 3 THEN 0
                        WHEN 4 THEN 0
                        WHEN 5 THEN 0
                        ELSE 0
                    END
                ) - SUM(
                    CASE jvtype
                        WHEN 0 THEN credit
                        WHEN 1 THEN credit
                        WHEN 2 THEN credit
                        WHEN 3 THEN 0
                        WHEN 4 THEN 0
                        WHEN 5 THEN 0
                        ELSE 0
                    END
                )
                FROM tbl_journal Journal2 
                WHERE Journal2.walletid = BaseTbl.walletid 
                AND Journal2.jvdate < "'.$searchFromDate.'"
            ), 0)) as adjusted_opbal';
        } else {
            $select_fields .= ', BaseTbl.opbal as adjusted_opbal';
        }
        
        // Use subqueries for current period transactions to avoid filtering out wallets
        if(!empty($searchFromDate) || !empty($searchToDate)) {
            $collection_conditions = '';
            $journal_conditions = '';
            
            if(!empty($searchFromDate)) {
                $collection_conditions .= ' AND paydate >= "'.$searchFromDate.'"';
                $journal_conditions .= ' AND jvdate >= "'.$searchFromDate.'"';
            }
            if(!empty($searchToDate)) {
                $collection_conditions .= ' AND paydate <= "'.$searchToDate.'"';
                $journal_conditions .= ' AND jvdate <= "'.$searchToDate.'"';
            }
            
            $select_fields .= ', COALESCE((
                SELECT SUM(paidamount) 
                FROM tbl_invoicecollection InvCol3 
                WHERE InvCol3.walletid = BaseTbl.walletid'.$collection_conditions.'
            ), 0) as totdebit';
            
            $select_fields .= ', COALESCE((
                SELECT SUM(
                    CASE jvtype
                        WHEN 0 THEN credit
                        WHEN 1 THEN credit
                        WHEN 2 THEN credit
                        WHEN 3 THEN 0
                        WHEN 4 THEN 0
                        WHEN 5 THEN 0
                        ELSE 0
                    END
                )
                FROM tbl_journal Journal3 
                WHERE Journal3.walletid = BaseTbl.walletid'.$journal_conditions.'
            ), 0) as totcredit';
        } else {
            $select_fields .= ', COALESCE((
                SELECT SUM(paidamount) 
                FROM tbl_invoicecollection InvCol3 
                WHERE InvCol3.walletid = BaseTbl.walletid
            ), 0) as totdebit';
            
            $select_fields .= ', COALESCE((
                SELECT SUM(
                    CASE jvtype
                        WHEN 0 THEN credit
                        WHEN 1 THEN credit
                        WHEN 2 THEN credit
                        WHEN 3 THEN 0
                        WHEN 4 THEN 0
                        WHEN 5 THEN 0
                        ELSE 0
                    END
                )
                FROM tbl_journal Journal3 
                WHERE Journal3.walletid = BaseTbl.walletid
            ), 0) as totcredit';
        }
        
        $select_fields .= ', BaseTbl.remarks';
        
        // Calculate closing balance based on adjusted opening balance
        if(!empty($searchFromDate)) {
            $select_fields .= ', (BaseTbl.opbal + COALESCE((
                SELECT SUM(InvCol2.paidamount) 
                FROM tbl_invoicecollection InvCol2 
                WHERE InvCol2.walletid = BaseTbl.walletid 
                AND InvCol2.paydate < "'.$searchFromDate.'"
            ), 0) + COALESCE((
                SELECT SUM(
                    CASE jvtype
                        WHEN 0 THEN debit
                        WHEN 1 THEN debit
                        WHEN 2 THEN debit
                        WHEN 3 THEN 0
                        WHEN 4 THEN 0
                        WHEN 5 THEN 0
                        ELSE 0
                    END
                ) - SUM(
                    CASE jvtype
                        WHEN 0 THEN credit
                        WHEN 1 THEN credit
                        WHEN 2 THEN credit
                        WHEN 3 THEN 0
                        WHEN 4 THEN 0
                        WHEN 5 THEN 0
                        ELSE 0
                    END
                )
                FROM tbl_journal Journal2 
                WHERE Journal2.walletid = BaseTbl.walletid 
                AND Journal2.jvdate < "'.$searchFromDate.'"
            ), 0)) + COALESCE((
                SELECT SUM(paidamount) 
                FROM tbl_invoicecollection InvCol3 
                WHERE InvCol3.walletid = BaseTbl.walletid'.$collection_conditions.'
            ), 0) - COALESCE((
                SELECT SUM(
                    CASE jvtype
                        WHEN 0 THEN credit
                        WHEN 1 THEN credit
                        WHEN 2 THEN credit
                        WHEN 3 THEN 0
                        WHEN 4 THEN 0
                        WHEN 5 THEN 0
                        ELSE 0
                    END
                )
                FROM tbl_journal Journal3 
                WHERE Journal3.walletid = BaseTbl.walletid'.$journal_conditions.'
            ), 0) as closbal';
        } else {
            $select_fields .= ', BaseTbl.opbal + COALESCE((
                SELECT SUM(paidamount) 
                FROM tbl_invoicecollection InvCol3 
                WHERE InvCol3.walletid = BaseTbl.walletid
            ), 0) - COALESCE((
                SELECT SUM(
                    CASE jvtype
                        WHEN 0 THEN credit
                        WHEN 1 THEN credit
                        WHEN 2 THEN credit
                        WHEN 3 THEN 0
                        WHEN 4 THEN 0
                        WHEN 5 THEN 0
                        ELSE 0
                    END
                )
                FROM tbl_journal Journal3 
                WHERE Journal3.walletid = BaseTbl.walletid
            ), 0) as closbal';
        }
        
        $this->db->select($select_fields);
        $this->db->from('tbl_paywallets as BaseTbl');

        if($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('BaseTbl.managername', $manager_chain);
        }elseif($managername != "admin"){
            $this->db->where('BaseTbl.managername', $managername);
        }

        /*if($managername != "admin" && $this->ismaster == 0){
            $this->db->where('BaseTbl.managername', $managername);
        }elseif($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            if (!empty($manager_chain)) {
                $this->db->where_in('BaseTbl.managername', $manager_chain);
            }
        }*/
        
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.walletname LIKE '%".$searchText."%'
                            OR BaseTbl.managername LIKE '%".$searchText."%'
                            OR BaseTbl.remarks LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        // Remove date range filters for wallet opening date - we want to show all wallets
        // and only filter the transactions, not the wallet creation date

        $this->db->order_by('BaseTbl.walletid', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();        
        return $result;
    }

    function addNewWallet($walletInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_paywallets', $walletInfo);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
        
        return $insert_id;
    }

    function getWalletInfo($walletId)
    {
        $this->db->select('walletid, walletname, managername, wallettype, profileid, opdate, closdate, opbal, totdebit, totcredit, closbal, accdr, acccr, remarks');
        $this->db->from('tbl_paywallets');
        $this->db->where('walletid', $walletId);
        $query = $this->db->get();
        
        return $query->row();
    }

    function editWallet($walletInfo, $walletId)
    {
        $this->db->where('walletid', $walletId);
        $this->db->update('tbl_paywallets', $walletInfo);
        
        return TRUE;
    }

    function checkWalletExists($walletname)
    {
        $this->db->select('walletname');
        $this->db->from('tbl_paywallets');
        $this->db->where('walletname', $walletname);
        $query = $this->db->get();
        
        return $query->row();
    }

    // Add a new invoice collection
    public function invcollection_add($data) {
        $this->db->insert('tbl_invoicecollection', $data);
        return $this->db->insert_id();
    }

    // Get wallet list for select box
    public function invcollection_get_wallets() {
        $this->db->select('walletid, walletname');
        $this->db->from('tbl_paywallets');
        $managername = $this->session->userdata ( 'name' );

        if($managername != "admin" && $this->ismaster == 0){
            $this->db->where('managername', $managername);
        }elseif($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            if (!empty($manager_chain)) {
                $this->db->where_in('tbl_paywallets.managername', $manager_chain);
            }
        }
        $this->db->order_by('walletname', 'ASC');
        return $this->db->get()->result();
    }

    // Get invoices for invoice collection grid
    public function invcollection_get_invoices($filterType, $fromDate = null, $toDate = null, $limit = 5) {

        $this->db->select('transid as invid, username, srvid, price, amount, invtype, managername, 
                          createdDtm as invdate, paid as paid, 
                          srvdate, expdate, remarks');
        $this->db->from('tbl_invoices');
        
        // Filter options
        if ($filterType === 'unpaid') {
            $this->db->where('paid', 0);
        } elseif ($filterType === 'recent') {
            $this->db->where('DATE(createdDtm) >=', date('Y-m-d', strtotime('-1 days')));
        } elseif ($filterType === 'custom' && $fromDate && $toDate) {
            $this->db->where('DATE(createdDtm) >=', $fromDate);
            $this->db->where('DATE(createdDtm) <=', $toDate);
        }

        $managername = $this->session->userdata ( 'name' );

        if($managername != "admin"){
            $this->db->where('managername', $managername);
        }
        
        // Additional filters
        $this->db->where('invtype', 'Recharge'); // Only include recharge invoices
        $this->db->where('amount <', 0); // Ensure these are customer invoices (negative amounts)
        
        // Sorting
        $this->db->order_by('transid, createdDtm', 'DESC');
        $this->db->limit($limit); // Limit to prevent performance issues
        
        return $this->db->get()->result();
    }


    // Associate multiple invoices with a collection
    public function invcollection_add_invoices($invoiceIds, $collectionId, $paidAmounts) {
        if (empty($invoiceIds) || empty($collectionId)) {
            return false;
        }
        
        $this->db->trans_start();
        
        // Update each invoice to reference the collection and set paid amount
        foreach ($invoiceIds as $invoiceId) {
            $paidAmount = isset($paidAmounts[$invoiceId]) ? $paidAmounts[$invoiceId] : 0;
            
            $this->db->where('transid', $invoiceId);
            $this->db->update('tbl_invoices', [
                'jvid' => $collectionId,
                'paid' => $paidAmount
            ]);
        }
        
        // Calculate total amount collected
        $this->db->select_sum('amount');
        $this->db->where_in('transid', $invoiceIds);
        $query = $this->db->get('tbl_invoices');
        $totalAmount = abs($query->row()->amount); // Convert negative to positive
        
        // Update collection with total amount
        $this->db->where('transid', $collectionId);
        $this->db->update('tbl_invoicecollection', [
            'paidamount' => $totalAmount,
            'createdDtm' => date('Y-m-d H:i:s')
        ]);
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }

    // Count all invoice collections with optional filters
    public function invcollection_count($walletid = null, $fromdate = null, $todate = null) {
        $managername = $this->session->userdata('name');
        $this->db->select('c.*, w.walletname, COUNT(i.transid) as invoice_count');
        $this->db->from('tbl_invoicecollection c');
        $this->db->join('tbl_paywallets w', 'w.walletid = c.walletid', 'left');
        $this->db->join('tbl_invoices i', 'i.jvid = c.transid', 'left');

        $this->db->where('i.jvid in (
                            select transid from tbl_invoicecollection 
                        )', null, false);
        
        if (!empty($walletid)) {
            $this->db->where('c.walletid', $walletid);
        }
        if (!empty($fromdate)) {
            $this->db->where('c.paydate >=', $fromdate);
        }
        if (!empty($todate)) {
            $this->db->where('c.paydate <=', $todate);
        }

        if($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('w.managername', $manager_chain);
        }elseif($managername != "admin"){
            $this->db->where('w.managername', $managername);
        }

        $this->db->group_by('c.transid');

        return $this->db->count_all_results();
    }
    
    // Get paginated list of invoice collections with optional filters
    public function invcollection_list($limit, $offset, $walletid = null, $fromdate = null, $todate = null) {

        $managername = $this->session->userdata('name');
        $this->db->select('c.*, w.walletname, COUNT(i.transid) as invoice_count');
        $this->db->from('tbl_invoicecollection c');
        $this->db->join('tbl_paywallets w', 'w.walletid = c.walletid', 'left');
        $this->db->join('tbl_invoices i', 'i.jvid = c.transid', 'left');

        $this->db->where('i.jvid in (
                            select transid from tbl_invoicecollection 
                        )', null, false);
        
        if (!empty($walletid)) {
            $this->db->where('c.walletid', $walletid);
        }
        if (!empty($fromdate)) {
            $this->db->where('c.paydate >=', $fromdate);
        }
        if (!empty($todate)) {
            $this->db->where('c.paydate <=', $todate);
        }

        if($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('w.managername', $manager_chain);
        }elseif($managername != "admin"){
            $this->db->where('w.managername', $managername);
        }

        $this->db->limit($limit, $offset);
        $this->db->group_by('c.transid');
        $this->db->order_by('c.transid', 'DESC');
        return $this->db->get()->result();
    }

    // Get a single collection by ID
    public function get_collection_by_id($id) {
        $this->db->where('transid', $id);
        return $this->db->get('tbl_invoicecollection')->row();
    }

    // Get all invoices linked to a collection
    public function get_invoices_by_collection($collectionId) {
        $this->db->where('jvid', $collectionId);
        return $this->db->get('tbl_invoices')->result();
    }

    // Update collection record
    public function update_collection($id, $data) {
        $this->db->where('transid', $id);
        return $this->db->update('tbl_invoicecollection', $data);
    }

    // Update paid amount for a single invoice
    public function update_invoice_paid_amount($invoiceId, $paidAmount) {
        $this->db->where('transid', $invoiceId);
        return $this->db->update('tbl_invoices', ['paid' => $paidAmount]);
    }

    // Delete a collection and release all associated invoices
    public function delete_collection_and_release_invoices($collectionId) {
        $this->db->trans_start();
        // Release invoices: set jvid to NULL and paid to 0 for all invoices linked to this collection
        $this->db->where('jvid', $collectionId);
        $this->db->update('tbl_invoices', array('jvid' => NULL, 'paid' => 0));
        // Delete the collection
        $this->db->where('transid', $collectionId);
        $this->db->delete('tbl_invoicecollection');
        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    // Activation Ticket: Add main record
    public function activationTicket_add_main($data) {
        $this->db->insert('tbl_activationticketmain', $data);
        return $this->db->insert_id();
    }
    // Activation Ticket: Add detail record
    public function activationTicket_add_detail($data) {
        return $this->db->insert('tbl_activationticketdetails', $data);
    }
    // Activation Ticket: Get main record by ID
    public function activationTicket_get_main($id) {
        $this->db->where('transid', $id);
        return $this->db->get('tbl_activationticketmain')->row();
    }

    public function activationTicket_get_usertype($username, $acttype) {
        $this->db->where('username', $username);
        $this->db->where('acttype', $acttype);
        $this->db->where('actstatus', 1);
        return $this->db->get('tbl_activationticketmain')->row();
    }

    // Activation Ticket: Get all details for a main record
    public function activationTicket_get_details($main_id) {
        $this->db->where('transid', $main_id);
        return $this->db->get('tbl_activationticketdetails')->result();
    }
    // Activation Ticket: Get enabled users for a manager
    public function activationTicket_get_users($managername) {
        $this->db->select('username, firstname, lastname');
        $this->db->from('rm_users');
        
        if($this->ismaster > 0){
            $this->db->where('owner in (select managername from rm_managers where mastername = "'.$managername.'")');
        }else{
            $this->db->where('owner', $managername);
        }

        $this->db->where('enableuser', 1);
        $this->db->where('acctype', 0);
        $this->db->order_by('username', 'ASC');
        return $this->db->get()->result();
    }
    // Activation Ticket: Get all accounts
    public function activationTicket_get_accounts() {
        $this->db->select('acctid, accname');
        $this->db->from('tbl_accounts');
        $this->db->order_by('accname', 'ASC');
        return $this->db->get()->result();
    }
    // Activation Ticket: List all tickets for a manager
    public function activationTicket_list($managername, $acttype = null, $searchText = null) {

        if($this->ismaster > 0){
            $this->db->where('username IN 
                (SELECT username FROM rm_users WHERE owner in (select managername from rm_managers where mastername = "' . $managername . '"))', NULL, FALSE);
        }elseif($managername != "admin"){
            $this->db->where('username in (SELECT username FROM rm_users WHERE owner = "'.$managername.'")', null, false);
        }

        if ($acttype !== null && $acttype !== '') {
            $this->db->where('acttype', $acttype);
        }
        if (!empty($searchText)) {
            $like = "(username LIKE '%".$searchText."%' OR remarks LIKE '%".$searchText."%' OR transid LIKE '%".$searchText."%')";
            $this->db->where($like);
        }

        $this->db->order_by('createdDtm', 'DESC');
        return $this->db->get('tbl_activationticketmain')->result();
    }
    public function activationTicket_list_by_username($username) {
        $this->db->where('username', $username);
        $this->db->order_by('createdDtm', 'DESC');
        return $this->db->get('tbl_activationticketmain')->result();
    }
    // Activation Ticket: Get acttype label
    public function activationTicket_acttype_label($acttype) {
        $labels = array(
            0 => 'USER-INSTALLATION',
            1 => 'USER-ACTIVATION',
            2 => 'USER-ADJUSTMENT',
            3 => 'USER-DISCOUNT'
        );
        return isset($labels[$acttype]) ? $labels[$acttype] : 'UNKNOWN';
    }

    // Update status of activation ticket
    public function update_activation_ticket_status($id, $status) {
        $this->db->where('transid', $id);
        return $this->db->update('tbl_activationticketmain', ['actstatus' => $status]);
    }

    public function update_activation_ticket_invtransid($id, $invtransid) {
        $this->db->where('transid', $id);
        return $this->db->update('tbl_activationticketmain', ['invtransid' => $invtransid]);
    }

    public function delete_activation_ticket_invoice($invtransid) {

        $this->db->trans_start();
        $this->db->where('transid', $invtransid);
        $this->db->delete('tbl_invoices');
        $this->db->trans_complete();
        return $this->db->trans_status();
       
    }

    public function reset__activation_ticket_Expiry($user)
    {
        // Get the latest recharge invoice for the user
        $this->db->select('username, expdate');
        $this->db->from('tbl_invoices');
        $this->db->where('invtype', 'Recharge');
        $this->db->where('username', $user);
        $this->db->where('transid IN (SELECT MAX(transid) FROM tbl_invoices WHERE invtype="Recharge" AND username = "'.$user.'")', NULL, FALSE);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $result = $query->row();
            
            // Update the expiration date in rm_users
            $updateData = array(
                'expiration' => $result->expdate . ' 12:00:00'
            );
            
            $this->db->where('username', $user);
            $this->db->update('rm_users', $updateData);
            
            return true;
        }
        
        return false;
    }

    public function getManagerSettings_bytype($managername, $stgtype)
    {
        $this->db->select('stgid, stgtype, stgvalue, stgname');
        $this->db->from('tbl_settings');
        $this->db->where('managername', $managername);
        $this->db->where('stgtype', $stgtype);
        $query = $this->db->get();
        
        return $query->result();
    }

    public function packagechangeadjustment($params) {

        $username = $params['username'];
        $new_price = $params['price']; // monthly price for user
        $radsrvid = $params['radsrvid']; // the new package radsrvid
        $today = date('Y-m-d');
        $expdate = $params['expdate'];

        // Start a transaction for the entire operation to ensure consistency
        $this->db->trans_start();

        // 1. Get all future Recharge and Credit invoices for this user, with left join to tbl_services to get radsrvid
        $this->db->select('tbl_invoices.*, tbl_services.radsrvid as invoice_radsrvid, 
                        tbl_services.srvname as invoice_srvname');
        $this->db->from('tbl_invoices');
        $this->db->join('tbl_services', 'tbl_invoices.srvid = tbl_services.srvid', 'left');
        $this->db->where('tbl_invoices.username', $username);
        $this->db->group_start();
        $this->db->where('tbl_invoices.invtype', 'Recharge');
        $this->db->or_where('tbl_invoices.invtype', 'Credit');
        $this->db->group_end();
        $this->db->where('tbl_invoices.expdate >', $today);
        $this->db->order_by('tbl_invoices.transid');
        $future_invoices = $this->db->get()->result();

        if (!$future_invoices) {
            $this->db->trans_complete();
            return false;
        }

        $adjusted = false;

        foreach ($future_invoices as $invoice) {
            $srvdate = $invoice->srvdate;
            $invoice_expdate = $invoice->expdate;

            if ($invoice->srvdate > $today) {
                $date1 = new DateTime($invoice->srvdate);
                    
                $this->db->where('transid', $invoice->transid);
                $this->db->delete('tbl_invoices');
                continue;

            }else{
                $date1 = new DateTime($today);
            }

            //$date1 = new DateTime($today);

            $date2 = new DateTime($invoice_expdate);
            $days_remaining = $date1 <= $date2 ? $date1->diff($date2)->days : 0;

            $date1_srv = new DateTime($invoice->srvdate);
            $date2_srv = new DateTime($today);
            $days_served = $date1_srv->diff($date2_srv)->days;

            if ($days_remaining <= 0) continue;

            $expdate = $invoice->expdate;

            if ($invoice->invtype == 'Recharge') {
                // For Recharge invoices, we:
                // 1. Close the existing invoice by setting its expdate to today
                // 2. Create a new invoice for the remaining days with the new package
                
                $old_monthly_price = $invoice->price;
                $old_per_day = $old_monthly_price / 30;
                $new_per_day = $new_price / 30;
                $old_value = $old_per_day * $days_served;
                $new_value = $new_per_day * $days_remaining;
                $difference = round($old_value - $new_value, 2);
                if ($difference == 0) continue;
                
                // Update the existing invoice to expire today
                $updateData = array(
                    'amount' => -$old_value,
                    'expdate' => Date("Y-m-d", strtotime(' +0 day')),
                    'remarks' => 'Package change to '.$invoice->invoice_srvname.' (adjusted on '.
                                    date('Y-m-d').')'.' for '.$days_served.' days',
                );
                $this->db->where('transid', $invoice->transid);
                $this->db->update('tbl_invoices', $updateData);
                $adjusted = true;

                // Insert new invoice for remaining days with new package
                $insertData = array(
                    'username' => $username,
                    'srvid' => $params['srvid'],
                    'price' => $new_price,
                    'amount' => -$new_value,
                    'invtype' => 'Recharge',
                    'managername' => $invoice->managername,
                    'createdBy' => 0,
                    'srvdate' => $today, //($invoice->srvdate > $today) ? $invoice->srvdate : $today
                    'expdate' => $expdate,
                    'remarks' => 'New invoice for service changed from '.$params['prevsrvname'],
                );
                $this->db->insert('tbl_invoices', $insertData);

            } elseif ($invoice->invtype == 'Credit') {
                // For Credit invoices (commissions), we:
                // 1. Get the manager from the invoice
                // 2. Find sub-managers who have this manager as master
                // 3. Calculate commissions for old and new packages
                // 4. Close old commission invoice and create new one
                
                $manager = $invoice->managername;
                
                // Get all sub-managers who have this manager as their master
                $sub_managers = $this->db->select('managername')
                    ->from('rm_managers')
                    ->where('mastername', $manager)
                    ->get()
                    ->result();
                
                // Get baseprices for old service
                $old_manager_base = $this->_getBaseprice($manager, $invoice->invoice_radsrvid);
                
                // Get baseprices for new service
                $new_manager_base = $this->_getBaseprice($manager, $radsrvid);
                
                // Calculate days served and remaining
                $date1 = new DateTime($invoice->srvdate);
                $date2 = new DateTime($today);
                $days_served = $date1->diff($date2)->days;
                
                // Process each sub-manager
                foreach ($sub_managers as $sub) {
                    // Get baseprices for sub-manager
                    $old_sub_base = $this->_getBaseprice($sub->managername, $invoice->invoice_radsrvid);
                    $new_sub_base = $this->_getBaseprice($sub->managername, $radsrvid);
                    
                    // Calculate full month commission for old service
                    // Commission = sub_base - manager_base (sub pays manager the difference)
                    $old_commission = $old_sub_base - $old_manager_base;
                    
                    // Calculate full month commission for new service
                    $new_commission = $new_sub_base - $new_manager_base;
                    
                    // Calculate commission for served days (old service)
                    $served_days_commission = ($old_commission / 30) * $days_served;
                    
                    // Calculate commission for remaining days (new service)
                    $remaining_days_commission = ($new_commission / 30) * $days_remaining;
                    
                    // Skip if no significant adjustment
                    if (abs($served_days_commission) < 0.01 && abs($remaining_days_commission) < 0.01) {
                        continue;
                    }
                    
                    // Find the corresponding Credit invoice for this sub-manager
                    $sub_invoice = $this->db->select('transid')
                        ->from('tbl_invoices')
                        ->where('username', $invoice->username)
                        ->where('managername', $manager)
                        ->where('invtype', 'Credit')
                        ->where('srvdate', $invoice->srvdate)
                        ->where('expdate', $invoice->expdate)
                        ->get()
                        ->row();
                        
                    if ($sub_invoice) {
                        // Update existing credit invoice to expire today
                        $updateData = array(
                            'expdate' => Date("Y-m-d", strtotime(' +0 day')),
                            'amount' => $served_days_commission,
                            'remarks' => 'Commission adj '.$invoice->invoice_srvname.' (adjusted on '.
                            date('Y-m-d').')'.' for '.$days_served.' days',
                        );
                        $this->db->where('transid', $sub_invoice->transid);
                        $this->db->update('tbl_invoices', $updateData);
                        $adjusted = true;

                        // Create new credit invoice for remaining days with new commission
                        $insertData = array(
                            'username' => $username,
                            'srvid' => $params['srvid'],
                            'price' => $new_price,
                            'amount' => $remaining_days_commission,
                            'invtype' => 'Credit',
                            'managername' => $manager,
                            'createdBy' => 0,
                            'srvdate' => date('Y-m-d'),
                            'expdate' => $expdate,
                            'remarks' => 'Commission '.$params['prevsrvname'].' for '.$sub->managername,
                        );
                        $this->db->insert('tbl_invoices', $insertData);
                    }
                }
            }
        }
        
        // Complete the transaction
        $this->db->trans_complete();
        
        return $adjusted;
    }

    // Helper: Get manager chain (from manager up to root)
    private function _getManagerChain($managername) {
        $chain = [];
        while ($managername && strtoupper($managername) !== 'NONE') {
            $chain[] = $managername;
            $row = $this->db->get_where('rm_managers', ['managername' => $managername])->row();
            if (!$row || !$row->mastername || $row->mastername == $managername) break;
            $managername = $row->mastername;
        }
        return $chain;
    }

    // Helper: Get baseprice for a manager and radsrvid
    private function _getBaseprice($managername, $radsrvid) {
        if (!$managername || !$radsrvid) return 0;
        $row = $this->db->get_where('tbl_services', [
            'managername' => $managername,
            'radsrvid' => $radsrvid
        ])->row();
        return $row ? $row->baseprice : 0;
    }

    // Get all invoices for a username (for side panel in invoiceAddNew)
    public function getAllInvoicesByUsername($username) {
        $managername = $this->session->userdata('name');
        $this->db->select('BaseTbl.transid, BaseTbl.username, BaseTbl.srvid, BaseTbl.price, BaseTbl.amount, BaseTbl.invtype, BaseTbl.managername, BaseTbl.createdBy, BaseTbl.srvdate, BaseTbl.expdate, BaseTbl.remarks, BaseTbl.createdDtm, BaseTbl.paid, BaseTbl.crdays, BaseTbl.jvid, Services.srvname');
        $this->db->from('tbl_invoices as BaseTbl');
        $this->db->join('tbl_services as Services', 'BaseTbl.srvid = Services.srvid', 'left');
        $this->db->where('BaseTbl.username', $username);

        if($managername <> "admin")
            $this->db->where('BaseTbl.managername = ', $managername);

        $this->db->limit(5);
        $this->db->order_by('BaseTbl.transid', 'DESC');

        $query = $this->db->get();
        return $query->result();
    }


    // Logic for Master Credit Refund

    function getResellerChain($userDetails, $managername,  $amount, $srvid, $srv_date, $exp_date, $accdr, $acccr, $creditType = 'CR', $drdays = 0) {
        
        $chain = array();
        $currentManager = $managername;
        
        $chain[] = $currentManager;
        while(true) {
            $masterInfo = $this->getMasterManager($currentManager);
            
            if(strtoupper($masterInfo->mastername) == "NONE" || $masterInfo->mastername == $currentManager || $masterInfo->mastername == NULL || $masterInfo->mastername == "" || $masterInfo->mastername == "0") {
                break;
            }
            $chain[] = $masterInfo->mastername;
            $currentManager = $masterInfo->mastername;
        }

        log_message('Info', 'invoice-masterCreditRefund: Chain Info '.json_encode($chain));

        // Process each level in the chain
        foreach($chain as $masterManager) {
            $this->masterInvoicesRefund($userDetails, $masterManager, $amount, $srvid, $srv_date, $exp_date, $accdr, $acccr, $creditType, $drdays);
        }
            
    }

    function masterInvoicesRefund($userDetails, $baseManager, $amount, $srvid, $srv_date, $exp_date, $accdr, $acccr, $creditType = 'CR', $drdays = 0){
        

        $masterInfo = $this->getMasterManager($baseManager);
        
        $divratio = $this->Invoices_model->getManagerSettings_bytype($masterInfo->mastername, "DIV-RATIO");
        if (!empty($divratio)) {
            return;
        }

        if(strtoupper($masterInfo->mastername )<> "NONE" || $masterInfo->mastername <> NULL || $masterInfo->mastername <> "" 
            || $masterInfo->mastername <> 0 )
        {
                

            $getPackageBasePrice = $this->Invoices_model->getPackagePrice($baseManager, $userDetails->srvid);
            // If the base price is greater than or equal to the amount, return
            if($getPackageBasePrice->baseprice > abs($amount)){
                return;
            }
            $getPackagePriceMaster = $this->Invoices_model->getPackagePrice($masterInfo->mastername, $userDetails->srvid);


            if(empty($getPackageBasePrice) || empty($getPackagePriceMaster)){
                return;
            }

            //echo $amount;
            //echo "<br>";

            /*echo "Base Manager: ".$baseManager."<br>";
            echo "Base Manager: ".$masterInfo->mastername."<br>";
            print_r($getPackageBasePrice)."<br> Next Line <br>";
            print_r($getPackagePriceMaster)."<br>";
            exit;*/
            
            if( !empty($getPackagePriceMaster) && $getPackageBasePrice->baseprice > $getPackagePriceMaster->baseprice)
            {

                if($creditType == 'CR')
                {
                $amountToRefund = (abs($getPackageBasePrice->baseprice) - $getPackagePriceMaster->baseprice);
                $amountToRefundDetails = " Price:".abs($getPackageBasePrice->baseprice)." - ".$getPackagePriceMaster->baseprice.
                                            " = ".(abs($getPackageBasePrice->baseprice) - $getPackagePriceMaster->baseprice);
                }elseif($creditType == 'DR' && $drdays > 0){
                    $amountToRefund = (($getPackagePriceMaster->baseprice - $getPackageBasePrice->baseprice) / 30) * $drdays;
                    $amountToRefundDetails = " Reverse Master Credit for ".$drdays." days:".abs($getPackagePriceMaster->baseprice)." - ".$getPackageBasePrice->baseprice.
                                            " = ".(abs($getPackagePriceMaster->baseprice) - $getPackageBasePrice->baseprice);
                }

                $masterRechargeInfo = array('username'=>$userDetails->username,
                                'srvid'=>$srvid, 
                                'managername'=>$masterInfo->mastername,
                                'createdBy'=>0,
                                'invtype'=>'Credit',
                                'srvdate'=>$srv_date,
                                'expdate'=>$exp_date,
                                'price'=>0,
                                'amount'=>$amountToRefund,
                                'remarks'=>"Refund to:".$masterInfo->mastername.
                                            $amountToRefundDetails);

                $masterJVEntry = array('jvdate'=>$srv_date,
                                'acctdr'=>$acccr, 
                                'acctcr'=>$accdr, 
                                'desc'=>"Refund to:".$masterInfo->mastername.
                                            $amountToRefundDetails,
                                'jvtype'=>128,
                                'managername'=>$baseManager,
                                'invinqty'=>1,
                                'invprice'=>$amountToRefund,
                                'invtotal'=>$amountToRefund,
                                'debit'=>$amountToRefund,
                                'credit'=>$amountToRefund,
                                'balance'=>0,
                                'username'=>$baseManager);

                log_message('info', 'invoice-masterCreditRefund: (Master): ' . json_encode($masterRechargeInfo));
                log_message('info', 'invoice-masterCreditRefund (Master): ' . json_encode($masterJVEntry));
            }

            log_message('info', 'invoice-masterCreditRefund:  ' . $getPackageBasePrice->baseprice . 
                        ' ' . $getPackagePriceMaster->baseprice);

            if($getPackageBasePrice->baseprice > $getPackagePriceMaster->baseprice && 
                !empty($masterRechargeInfo)){ // Reverse Credit to Master

                $result = $this->Invoices_model->addRecharge($masterRechargeInfo);
                //$jvresult = $this->Jvs_model->addNewJv($masterJVEntry);
                log_message('info', 'invoice-masterCreditRefund: Invoice Created for Master Refund -> ' . $result);

            }
        
        }

    }

    function get_lastinvoice($username)
    {
        $this->db->select('BaseTbl.transid, BaseTbl.username, BaseTbl.srvid, BaseTbl.price, BaseTbl.amount, 
                        BaseTbl.invtype, BaseTbl.managername, BaseTbl.createdBy, BaseTbl.srvdate, BaseTbl.expdate, 
                        BaseTbl.remarks, BaseTbl.createdDtm, BaseTbl.paid, BaseTbl.crdays, BaseTbl.jvid');
        $this->db->from('tbl_invoices as BaseTbl');
        $this->db->where('BaseTbl.username', $username);
        $this->db->where('ABS(BaseTbl.amount) >', 0);
        $this->db->order_by('BaseTbl.transid', 'DESC');
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

}
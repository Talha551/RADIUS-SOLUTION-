<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Accounts_model extends CI_Model
{

    function accountsCount($searchText = '', $type, $managerAllServices)
    {
        $this->db->select('BaseTbl.acctid, BaseTbl.accname, BaseTbl.accgroup, BaseTbl.managername, 
                            BaseTbl.opqty, BaseTbl.inqty, BaseTbl.outqty, BaseTbl.clqty,
                            BaseTbl.opening, BaseTbl.debit, BaseTbl.credit');
        $this->db->from('tbl_accounts as BaseTbl');
        $this->db->join('tbl_accgroup as Group', 'BaseTbl.accgroup = Group.grpid','left');

        $curDate = date("y-m-d");
        
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.acctid  LIKE '%".$searchText."%'
                            OR  BaseTbl.accname  LIKE '%".$searchText."%'
                            OR  BaseTbl.accgroup  LIKE '%".$searchText."%'
                            OR  BaseTbl.managername  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        $managername = $this->session->userdata ( 'name' );

        if($managername <> 'admin'){
            $this->db->where('BaseTbl.acctid in (select acctdr from tbl_journal where managername = "'.$managername.'"
                            Union All select acctcr from tbl_journal where managername = "'.$managername.'")');
        }

        if(!empty($type)){
            $this->db->where('Group.grpname', $type);
        }

        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function accountsListing($searchText = '', $type, $page, $segment, $managerAllServices)
    {

        $managername = $this->session->userdata ( 'name' );
        $manager_chain = $this->session->userdata('manager_chain');
        $manager_chain_quoted = array_map(function($v) { return "'".$this->db->escape_str($v)."'"; }, $manager_chain);
        $manager_chain_str = implode(',', $manager_chain_quoted);

        if($this->ismaster > 0){
            $managerwhere = ' managername IN ('.$manager_chain_str.') and ';
        }elseif($managername <> 'admin'){
            $managerwhere = ' managername = "'.$managername.'" and ';
        }else{
            $managerwhere = '';
        }
        

        $this->db->query('Set @row_number = 0');
        $this->db->select('BaseTbl.acctid, BaseTbl.accname, BaseTbl.accgroup, BaseTbl.managername, 
                            BaseTbl.opqty, BaseTbl.inqty, BaseTbl.outqty, BaseTbl.clqty,
                            (select sum(debit) from tbl_journal where '.$managerwhere.' acctdr = BaseTbl.acctid and MONTH(jvdate) <= MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and YEAR(jvdate) <= YEAR(CURRENT_DATE - INTERVAL 1 MONTH)) as openingDr, 
                            (select sum(debit) from tbl_journal where '.$managerwhere.' acctcr = BaseTbl.acctid and MONTH(jvdate) <= MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and YEAR(jvdate) <= YEAR(CURRENT_DATE - INTERVAL 1 MONTH)) as openingCr, 
                            (select sum(debit) from tbl_journal where '.$managerwhere.' acctdr = BaseTbl.acctid and MONTH(jvdate) = MONTH(CURRENT_DATE) and YEAR(jvdate) = YEAR(CURRENT_DATE)) as debit, 
                            (select sum(debit) from tbl_journal where '.$managerwhere.' acctcr = BaseTbl.acctid and MONTH(jvdate) = MONTH(CURRENT_DATE) and YEAR(jvdate) = YEAR(CURRENT_DATE)) as credit, 
                            BaseTbl.balance, Group.grpname,
                            (@row_number:=@row_number + 1) AS serial_number');

        $this->db->from('tbl_accounts as BaseTbl');
        $this->db->join('tbl_accgroup as Group', 'BaseTbl.accgroup = Group.grpid','left');
        
        $curDate = date("y-m-d");        

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.acctid  LIKE '%".$searchText."%'
                            OR  BaseTbl.accname  LIKE '%".$searchText."%'
                            OR  BaseTbl.accgroup  LIKE '%".$searchText."%'
                            OR  BaseTbl.managername  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }


        //$this->db->where('UsrTbl.owner = ', $managername);
        //if($managerAllServices == 0)
        //{
        //    $this->db->where('BaseTbl.managername = ', $managername);
        //}

        //if($managername == 'admin'){
        //    $this->db->where('BaseTbl.managername', $type);
        //}

        if($this->ismaster > 0){

            $this->db->where('BaseTbl.acctid in (select acctdr from tbl_journal where managername IN ('.$manager_chain_str.')
                            Union All select acctcr from tbl_journal where managername IN ('.$manager_chain_str.'))');

        }elseif($managername <> 'admin'){

            $this->db->where('BaseTbl.acctid in (select acctdr from tbl_journal where managername = "'.$managername.'"
                            Union All select acctcr from tbl_journal where managername = "'.$managername.'")');
                            
        }

        if(!empty($type)){
            $this->db->where('Group.grpname', $type);
        }

        $this->db->order_by('BaseTbl.accgroup, BaseTbl.accname');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }

    function sumAccountsDrCr($searchText = '', $type, $page, $segment, $managerAllServices){


        $managername = $this->session->userdata ( 'name' );
        $manager_chain = $this->session->userdata('manager_chain');
        $manager_chain_quoted = array_map(function($v) { return "'".$this->db->escape_str($v)."'"; }, $manager_chain);
        $manager_chain_str = implode(',', $manager_chain_quoted);

        if($this->ismaster > 0){
            $managerwhere = ' managername IN ('.$manager_chain_str.') and ';
        }elseif($managername <> 'admin'){
            $managerwhere = ' managername = "'.$managername.'" and ';
        }else{
            $managerwhere = '';
        }

        $this->db->select('(select sum(debit) from tbl_journal where '.$managerwhere.' 
                        MONTH(jvdate) <= MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and YEAR(jvdate) <= YEAR(CURRENT_DATE - INTERVAL 1 MONTH))  as opening, 
                        (select sum(debit) from tbl_journal where '.$managerwhere.' MONTH(jvdate) = MONTH(CURRENT_DATE) and YEAR(jvdate) = YEAR(CURRENT_DATE)) as debit, 
                        (select sum(credit) from tbl_journal where '.$managerwhere.' MONTH(jvdate) = MONTH(CURRENT_DATE) and YEAR(jvdate) = YEAR(CURRENT_DATE)) as credit, 
                        ');
        $this->db->from('tbl_accounts as BaseTbl');

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.acctid  LIKE '%".$searchText."%'
                            OR  BaseTbl.accname  LIKE '%".$searchText."%'
                            OR  BaseTbl.accgroup  LIKE '%".$searchText."%'
                            OR  BaseTbl.managername  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        $query = $this->db->get();
        return $query->row();

    }

    function addNewAccount($accountsInfo){

        $this->db->trans_start();
        $this->db->insert('tbl_accounts', $accountsInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return True;
    }

    function getAccountsGroup()
    {
        $managername = $this->session->userdata ( 'name' );

        $this->db->select('grpid, grpname, managername');
        $this->db->from('tbl_accgroup');
        $this->db->order_by('grpname');
        //$this->db->where('managername', $managername);;
        $query = $this->db->get();
        
        return $query->result();
    }

    function accountNameExists($accname)
    {
        $managername = $this->session->userdata ( 'name' );

        $this->db->select('accname');
        $this->db->from('tbl_accounts');
        $this->db->where('accname', $accname);;
        $query = $this->db->get();
        
        return $query->result();

    }

    function getAccountsInfo($acctid){

        $managername = $this->session->userdata ( 'name' );

        $this->db->select('acctid, accname, accgroup, managername, opqty, inqty, outqty, clqty, opening, debit, credit, balance');
        $this->db->from('tbl_accounts');
        $this->db->where('acctid', $acctid);
        $this->db->order_by('accname');

        $query = $this->db->get();
        return $query->row();

    }

    function updateAccout($accountInfo, $acctid)
    {
        $this->db->where('acctid', $acctid);
        $this->db->update('tbl_accounts', $accountInfo);
        
        return TRUE;
    }

    function getAccountsList($accgroup = NULL, $type = NULL)
    {
        $managername = $this->session->userdata ( 'name' );

        $this->db->select('acctid, concat(accname, " - ", Group.grpname, " ") as accname, accgroup, BaseTbl.managername, Group.grpname');
        $this->db->from('tbl_accounts as BaseTbl');
        $this->db->join('tbl_accgroup as Group', 'BaseTbl.accgroup = Group.grpid','left');

        if($type == 1)
        {
            $this->db->where('grpname = "CASH" OR grpname = "BANK"');
        }else if($type == 0){
            // All ITEMS NO Where Applied
            //$this->db->where('grpname <> "ITEM"');
            $this->db->where('grpname = "EXPENSES"');
        }else if($type == 2){
            $this->db->where('grpname = "INCOME"');
        }elseif($accgroup <> '' OR $accgroup <> NULL)
        { 
            $this->db->where('grpname', $accgroup);
        }

        if($managername <> 'admin'){
            $this->db->where('acctid in (select acctid from tbl_accounts where 
                            managername = "default" OR managername = "'.$managername.'")'); 
        }

        $this->db->order_by('Group.grpname, accname');

        $query = $this->db->get();
        
        return $query->result();
    }

    function getCGSInfo(){

        $managername = $this->session->userdata ( 'name' );

        $this->db->select('acctid, accname, accgroup, managername, opqty, inqty, outqty, clqty, opening, debit, credit, balance');
        $this->db->from('tbl_accounts');
        $this->db->where('accname LIKE "%CGS%"');

        $query = $this->db->get();
        return $query->row();

    }

    // ******  LEDGER REPORT *********************
    function ledgerReportCount($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '', $searchText4 = '')
    {
        $this->db->select('BaseTbl.jvid, BaseTbl.jvdate, BaseTbl.desc, BaseTbl.jvtype, BaseTbl.managername');
        $this->db->from('tbl_journal as BaseTbl');

        $this->db->where('date(BaseTbl.jvdate) >= ', $searchText1);
        $this->db->where('date(BaseTbl.jvdate) <= ', $searchText2);

        $managername = $this->session->userdata ( 'name' );

        // Where for Accounts Debit Side Search
        $this->db->where('date(BaseTbl.jvdate) >= ', $searchText1);
        $this->db->where('date(BaseTbl.jvdate) <= ', $searchText2);
        if($searchText3 <> 0)
            $this->db->where('BaseTbl.acctdr = ', $searchText3);

        if($managername <> 'admin'){
            if($this->ismaster > 0){
                $this->db->where('BaseTbl.managername in (Select managername from rm_managers where mastername = "'.$managername.'")');
            }else{
                $this->db->where('BaseTbl.managername = ', $managername); 
            }
        }
            

        if($searchText4 <> 0)
            $this->db->where('BaseTbl.jvtype = ', $searchText4);

        // OR Where for Accounts Credit Side Search
        $this->db->or_where('date(BaseTbl.jvdate) >= ', $searchText1);
        $this->db->where('date(BaseTbl.jvdate) <= ', $searchText2);
        if($searchText3 <> 0)
            $this->db->where('BaseTbl.acctcr = ', $searchText3);

        if($managername <> 'admin'){
            if($this->ismaster > 0){
                $this->db->where('BaseTbl.managername in (Select managername from rm_managers where mastername = "'.$managername.'")');
            }else{
                $this->db->where('BaseTbl.managername = ', $managername); 
            }
        }

        if($searchText4 <> 0)
            $this->db->where('BaseTbl.jvtype = ', $searchText4);

        $query = $this->db->get();
        return $query->num_rows();

    }

    function ledgerReport($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '', $searchText4 = '', $page, $segment)
    {

        $this->db->query('Set @row_number = 0');
        $this->db->select('BaseTbl.jvid, BaseTbl.acctdr, BaseTbl.acctcr, BaseTbl.jvdate, BaseTbl.desc, BaseTbl.jvtype, 
                            Types.typename, BaseTbl.managername, BaseTbl.debit,
                            (select accgroup from tbl_accounts where acctid = BaseTbl.acctdr) as draccgroup,
                            (select accgroup from tbl_accounts where acctid = BaseTbl.acctcr) as craccgroup,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('tbl_journal as BaseTbl');
        $this->db->join('tbl_jvtypes as Types', 'BaseTbl.jvtype = Types.typeid','left');

        $managername = $this->session->userdata ( 'name' );

        // Where for Accounts Debit Side Search
        $this->db->where('date(BaseTbl.jvdate) >= ', $searchText1);
        $this->db->where('date(BaseTbl.jvdate) <= ', $searchText2);

        if($searchText3 <> 0){
            $this->db->where('BaseTbl.acctdr = ', $searchText3);}
        
        if($managername <> 'admin'){
            if($this->ismaster > 0){
                $this->db->where('BaseTbl.managername = "'.$searchText.'"');
            }else{
                $this->db->where('BaseTbl.managername = ', $managername); 
            }
        }elseif($searchText <> 'admin'){
            $this->db->where('BaseTbl.managername = "'.$searchText.'"');
        }

        if($searchText4 <> 0){
            $this->db->where('BaseTbl.jvtype = ', $searchText4);}

        // OR Where for Accounts Credit Side Search
        $this->db->or_where('date(BaseTbl.jvdate) >= ', $searchText1);
        $this->db->where('date(BaseTbl.jvdate) <= ', $searchText2);

        if($searchText3 <> 0){
            $this->db->where('BaseTbl.acctcr = ', $searchText3);}

        if($managername <> 'admin'){
            if($this->ismaster > 0){
                $this->db->where('BaseTbl.managername = "'.$searchText.'"');
            }else{
                $this->db->where('BaseTbl.managername = ', $managername);
            }
        }elseif($searchText <> 'admin'){
            $this->db->where('BaseTbl.managername = "'.$searchText.'"');
        }

        if($searchText4 <> 0)
            $this->db->where('BaseTbl.jvtype = ', $searchText4);

        // Order By and Limit View
        $this->db->order_by('BaseTbl.jvdate', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();

        $result = $query->result();
        return $result;

    }

    function ledgerSummery($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '', $searchText4 = '')
    {
        $managername = $this->session->userdata ( 'name' );

        if($searchText <> 'admin'){
            $whereClause = 'managername = "'.$searchText.'" and ';
        }else{
            $whereClause = '';
        }

        if($searchText4 <> 0)
            $whereClause = $whereClause.' jvtype = "'.$searchText4.'" and ';

        $this->db->query('Set @row_number = 0');
        $this->db->select('A.managername');
        $this->db->select('count(A.jvid) as Entries');
        $this->db->select('(select sum(debit) from tbl_journal where '.$whereClause.' acctdr = "'.$searchText3.'" and date(jvdate) < "'.$searchText1.'") as openingDr');
        $this->db->select('(select sum(debit) from tbl_journal where '.$whereClause.' acctcr = "'.$searchText3.'" and date(jvdate) < "'.$searchText1.'") as openingCr');
        $this->db->select('(select sum(debit) from tbl_journal where '.$whereClause.' acctdr = "'.$searchText3.'" and date(jvdate) >= "'.$searchText1.'" and date(jvdate) <= "'.$searchText2.'") as debit');
        $this->db->select('(select sum(debit) from tbl_journal where '.$whereClause.' acctcr = "'.$searchText3.'" and date(jvdate) >= "'.$searchText1.'" and date(jvdate) <= "'.$searchText2.'") as credit');
        $this->db->select('(select sum(debit) from tbl_journal where '.$whereClause.' acctdr = "'.$searchText3.'" and date(jvdate) <= "'.$searchText2.'") as closingDr');
        $this->db->select('(select sum(debit) from tbl_journal where '.$whereClause.' acctcr = "'.$searchText3.'" and date(jvdate) <= "'.$searchText2.'") as closingCr');
        $this->db->from('tbl_journal as A');
        //$this->db->join('tbl_accounts as B', 'A.accgroup=B.username','left');

        if($searchText <> 'admin')
        {   $this->db->where('A.managername = ', $searchText);  }

        //$this->db->where('date(A.jvdate) >= ', $searchText1);
        //$this->db->where('date(A.jvdate) <= ', $searchText2);
        //$this->db->where('A.invtype', 'Recharge');

        //$this->db->or_where('A.managername = ', $searchText);
        //$this->db->where('date(A.jvdate) >= ', $searchText1);
        //$this->db->where('date(A.jvdate) <= ', $searchText2);
        //$this->db->where('A.invtype', 'Credit');
        //$this->db->where('A.srvid<>0');

        //if($searchText == 'admin')
        //{   $this->db->group_by('A.managername');   }

        $this->db->order_by('A.managername');
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }

    function CashInHand(){

        $managername = $this->session->userdata ( 'name' );

        if($managername <> 'admin'){
            $whereClause = ' managername = "'.$managername.'" and ';
        }else{
            $whereClause = '';
        }

        // ********  Dashboard CASH Deposit ************ //
        $this->db->query('Set @pcash_debit = (select sum(debit) from tbl_journal where '.$whereClause.' 
                jvdate <= CURRENT_DATE - INTERVAL 30 DAY  
                and acctdr in (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="CASH")))');

        $this->db->query('Set @pcash_credit = (select sum(debit) from tbl_journal where '.$whereClause.' 
                jvdate <= CURRENT_DATE - INTERVAL 30 DAY 
                and acctcr in (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="CASH")))');

        $this->db->query('Set @cash_debit = (select sum(debit) from tbl_journal where '.$whereClause.' acctdr in 
                        (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="CASH")))');

        $this->db->query('Set @cash_credit = (select sum(debit) from tbl_journal where '.$whereClause.' acctcr in 
                        (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="CASH")))');

        
        // ********  Dashboard BANK Deposit ************ //
        $this->db->query('Set @pbank_debit = (select sum(debit) from tbl_journal where '.$whereClause.' 
                jvdate <= CURRENT_DATE - INTERVAL 30 DAY  
                and acctdr in (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="BANK")))');

        $this->db->query('Set @pbank_credit = (select sum(debit) from tbl_journal where '.$whereClause.' 
                jvdate <= CURRENT_DATE - INTERVAL 30 DAY 
                and acctcr in (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="BANK")))');

        $this->db->query('Set @bank_debit = (select sum(debit) from tbl_journal where '.$whereClause.' acctdr in 
                        (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="BANK")))');

        $this->db->query('Set @bank_credit = (select sum(debit) from tbl_journal where '.$whereClause.' acctcr in 
                        (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="BANK")))');  
                        
        // ********  Dashboard Expenses ************ //
        $this->db->query('Set @pexp_debit = (select sum(debit) from tbl_journal where '.$whereClause.' 
                MONTH(jvdate) <= MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and YEAR(jvdate) <= YEAR(CURRENT_DATE - INTERVAL 1 MONTH) 
                and acctdr in (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="EXPENSES")))');

        $this->db->query('Set @pexp_credit = (select sum(debit) from tbl_journal where '.$whereClause.' 
                MONTH(jvdate) <= MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and YEAR(jvdate) <= YEAR(CURRENT_DATE - INTERVAL 1 MONTH) 
                and acctcr in (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="EXPENSES")))');

        $this->db->query('Set @exp_debit = (select sum(debit) from tbl_journal where '.$whereClause.' 
                        MONTH(jvdate) = MONTH(CURRENT_DATE) and YEAR(jvdate) = YEAR(CURRENT_DATE) 
                        and acctdr in (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="EXPENSES")))');

        $this->db->query('Set @exp_credit = (select sum(debit) from tbl_journal where '.$whereClause.' 
                        MONTH(jvdate) = MONTH(CURRENT_DATE) and YEAR(jvdate) = YEAR(CURRENT_DATE) 
                        and acctcr in (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="EXPENSES")))');

        // ********  Dashboard Expenses Previous Month ************ //
        $this->db->query('Set @p_month_exp_debit = (select sum(debit) from tbl_journal where '.$whereClause.' 
                MONTH(jvdate) <= MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and YEAR(jvdate) <= YEAR(CURRENT_DATE - INTERVAL 1 MONTH) 
                and acctdr in (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="EXPENSES")))');

        $this->db->query('Set @p_month_exp_credit = (select sum(debit) from tbl_journal where '.$whereClause.' 
                MONTH(jvdate) <= MONTH(CURRENT_DATE - INTERVAL 1 MONTH) and YEAR(jvdate) <= YEAR(CURRENT_DATE - INTERVAL 1 MONTH)
                and acctcr in (select acctid from tbl_accounts where accgroup in (select grpid from tbl_accgroup where grpname="EXPENSES")))');
        
        // *********** Summery Report ************ //
        $this->db->select('round((ifnull(@cash_debit,0)-ifnull(@cash_credit,0)),0) as CashInHand, round((ifnull(@pcash_debit,0)-ifnull(@pcash_credit,0)),0) as pCashInHand, 
                round((ifnull(@bank_debit,0)-ifnull(@bank_credit,0)),0) as BankBalance, round((ifnull(@pbank_debit,0)-ifnull(@pbank_credit,0)),0) as pBankBalance,
                round((ifnull(@exp_debit,0)-ifnull(@exp_credit,0)),0) as ExpBalance, round((ifnull(@pexp_debit,0)-ifnull(@pexp_credit,0)),0) as pExpBalance,
                round((ifnull(@p_month_exp_debit,0)-ifnull(@p_month_exp_credit,0)),0) as p_month_ExpBalance');

        $query = $this->db->get();
        return $query->row();

    }

    function trialBalance($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '')
    {

        $managername = $this->session->userdata ( 'name' );
        $manager_chain = $this->session->userdata('manager_chain');
        $manager_chain_quoted = array_map(function($v) { return "'".$this->db->escape_str($v)."'"; }, $manager_chain);
        $manager_chain_str = implode(',', $manager_chain_quoted);

        if($this->ismaster > 0){
            $managerFilter = ' and managername IN ('.$manager_chain_str.') ';
        }elseif($searchText <> 'admin'){
            $managerFilter = ' and managername = "'.$managername.'" ';
        }else{
            $managerFilter = '';
        }

        /*if($searchText <> 'admin'){
            $managerFilter = ' and managername = "'.$searchText.'"';
        }else{
            $managerFilter = '';
        }*/

        $whereCluaseOp = $managerFilter.' and MONTH(jvdate) <= MONTH("'.$searchText1.'" - INTERVAL 1 MONTH) and YEAR(jvdate) <= YEAR("'.$searchText1.'" - INTERVAL 1 MONTH) ';
        $whereClauseCur = $managerFilter.' and date(jvdate) >= "'.$searchText1.'" and date(jvdate) <= "'.$searchText2.'"';

        $this->db->query('Set @row_number = 0');
        $this->db->select('BaseTbl.acctid, BaseTbl.accname, BaseTbl.accgroup, Group.grpname,
                            (select sum(debit) from tbl_journal where acctdr = BaseTbl.acctid '.$whereCluaseOp.') as opdebit,
                            (select sum(debit) from tbl_journal where acctcr = BaseTbl.acctid '.$whereCluaseOp.') as opcredit,
                            (select sum(debit) from tbl_journal where acctdr = BaseTbl.acctid '.$whereClauseCur.') as debit,
                            (select sum(debit) from tbl_journal where acctcr = BaseTbl.acctid '.$whereClauseCur.') as credit,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('tbl_accounts as BaseTbl');
        $this->db->join('tbl_accgroup as Group', 'BaseTbl.accgroup = Group.grpid','left');

        if($searchText3 <> 0)
            $this->db->where('BaseTbl.accgroup = ', $searchText3);

        $this->db->group_by('BaseTbl.acctid');

        // Order By and Limit View
        $this->db->order_by('BaseTbl.accgroup', 'DESC');
        $query = $this->db->get();

        $result = $query->result();
        return $result;

    }

    function trialSummery($searchText = '', $searchText1 = '', $searchText2 = '', $searchText3 = '')
    {

        $managername = $this->session->userdata ( 'name' );
        $manager_chain = $this->session->userdata('manager_chain');
        $manager_chain_quoted = array_map(function($v) { return "'".$this->db->escape_str($v)."'"; }, $manager_chain);
        $manager_chain_str = implode(',', $manager_chain_quoted);

        if($this->ismaster > 0){
            $managerFilter = ' where managername IN ('.$manager_chain_str.') ';
        }elseif($searchText <> 'admin'){
            $managerFilter = ' where managername = "'.$managername.'" ';
        }else{
            $managerFilter = '';
        }

        /*if($searchText <> 'admin'){
            $managerFilter = ' where managername = "'.$searchText.'"';
        }else{
            $managerFilter = '';
        }*/

        if(!empty($managerFilter)){ $andCondition = ' and '; }else{ $andCondition = ' where '; }

        $whereClauseOp = $managerFilter.$andCondition.' MONTH(jvdate) <= MONTH("'.$searchText1.'" - INTERVAL 1 MONTH) and YEAR(jvdate) <= YEAR("'.$searchText1.'" - INTERVAL 1 MONTH) ';
        $whereClauseCur = $managerFilter.$andCondition.' date(jvdate) >= "'.$searchText1.'" and date(jvdate) <= "'.$searchText2.'"';

        $this->db->select('count(acctid) as NumberOfAcc,
                            (select sum(debit) from tbl_journal '.$whereClauseOp.') as openingDr,
                            (select sum(debit) from tbl_journal '.$whereClauseOp.') as openingCr,
                            (select sum(debit) from tbl_journal '.$whereClauseCur.') as debit,
                            (select sum(debit) from tbl_journal '.$whereClauseCur.') as credit');
        $this->db->from('tbl_accounts as BaseTbl');

        if($searchText3 <> 0)
            $this->db->where('BaseTbl.accgroup = ', $searchText3);

        
        $query = $this->db->get();
    
        $result = $query->result();
        return $result;

    }

    // *********** Easy Paisa List ****************

    function get_easypaisalist() {  // Get List of Un Posted Entries 
        
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

    function get_easypaisaownerlist() {   // Summery of Owner Credit Un Posted
        
        $this->db->select('owner, transaction_date');
        $this->db->select_sum('amount_paid');
        $this->db->from('tbl_eptransaction as eptrans');
        $this->db->join('rm_users as users', 'eptrans.username = users.username','left');
        $this->db->where('posted', 0);
        $this->db->group_by('owner');
        $this->db->group_by('transaction_date');
        
        $query = $this->db->get();
    
        $result = $query->result_array();
        return $result;

    }

    function get_userfrompayid($payid){

        $this->db->select('username');
        $this->db->from('tbl_userdocs');
        $this->db->where('payid', $payid);

        $query = $this->db->get();
        return $query->row();

    }
 
    function insert_easypaisacsv($data) {
        $this->db->insert('tbl_eptransaction', $data);
    }

    function insert_eptransactioncsv($consumer_number, $customer_name, $amount_paid, $transaction_date, $username) {

        if($consumer_number <> ''){
            $query="insert into tbl_eptransaction (consumer_number,customer_name,amount_paid,transaction_date,username) 
                    values('$consumer_number', '$customer_name', '$amount_paid', '$transaction_date','$username')";
            $this->db->query($query);
        }
    }

    function check_epentryexists($consumer_number, $customer_name, $amount_paid, $transaction_date)
    {
        $this->db->select('id');
        $this->db->from('tbl_eptransaction');
        $this->db->where('consumer_number', $consumer_number);
        $this->db->where('customer_name', $customer_name);
        $this->db->where('amount_paid', $amount_paid);
        $this->db->where('transaction_date', $transaction_date);
        $query = $this->db->get();
        
        return $query->result();
    }

    function ep_addresellercredit(){

        $query="insert into tbl_invoices (username, srvid, price, amount, invtype, managername, createdBy, srvdate, expdate, remarks) 
                select owner as username, 0 as srvid, 0.00 as price, sum(amount_paid) as amount, 
                'Credit' as invtype, owner as managername, 0 as createdBy, 
                CONCAT(MID(transaction_date,1,4), '-', MID(transaction_date,5,2), '-', MID(transaction_date,7,2)) as srvdate, 
                CONCAT(MID(transaction_date,1,4), '-', MID(transaction_date,5,2), '-', MID(transaction_date,7,2)) as expdate, 
                CONCAT('Easy Paisa Credit Entry ', transaction_date) as remarks 
                From rm_users as A left join tbl_eptransaction as B On A.username = B.username where B.posted = 0 group by owner, transaction_date";

        $query1="insert into tbl_invoices (username, srvid, price, amount, invtype, managername, createdBy, srvdate, expdate, remarks) 
                select 'admin' as username, 0 as srvid, 0.00 as price, -sum(amount_paid) as amount, 
                'Credit' as invtype, 'admin' as managername, 0 as createdBy, 
                CONCAT(MID(transaction_date,1,4), '-', MID(transaction_date,5,2), '-', MID(transaction_date,7,2)) as srvdate, 
                CONCAT(MID(transaction_date,1,4), '-', MID(transaction_date,5,2), '-', MID(transaction_date,7,2)) as expdate, 
                CONCAT('Easy Paisa Credit ',owner, '-', transaction_date) as remarks 
                From rm_users as A left join tbl_eptransaction as B On A.username = B.username where B.posted = 0 group by owner, transaction_date";
        
        $query2="update tbl_eptransaction set posted = 1 where posted = 0";

        $this->db->query($query);
        $this->db->query($query1);
        $this->db->query($query2);
        
    }

    public function stockLedgerReport($itemId = null, $fromDate = null, $toDate = null)
    {
        
        $this->db->from('tbl_journal');
        $this->db->where_in('jvtype', [1, 4, 6, 7]); // 1 = Stock IN, 4 = Stock Out, 6 = Stock Transfer IN, 7 = Stock Transfer Out
        if (!empty($itemId)) {
            $this->db->where('(acctdr = ' . (int)$itemId . ' OR acctcr = ' . (int)$itemId . ')');
        }else{
            $this->db->where('acctdr = 0');
        }

        if (!empty($fromDate)) {
            $this->db->where('jvdate >=', $fromDate);
        }
        if (!empty($toDate)) {
            $this->db->where('jvdate <=', $toDate);
        }
        if($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('managername', $manager_chain);
        }elseif($this->session->userdata('name') != 'admin'){
            $this->db->where('managername', $this->session->userdata('name'));
        }

        $this->db->order_by('jvdate', 'ASC');
        $this->db->order_by('jvid', 'ASC');
        $query = $this->db->get();
        return $query->result();
    } 

    /**
     * Get list of wallets for dropdown
     * @return array
     */
    public function getWalletList()
    {
        
        $this->db->select('walletid, walletname');
        $this->db->from('tbl_paywallets');
        $this->db->order_by('walletname', 'ASC');
        if($this->ismaster > 0){
            $manager_chain = $this->session->userdata('manager_chain');
            $this->db->where_in('managername', $manager_chain);
        }elseif($this->session->userdata('name') != 'admin'){
            $this->db->where('managername', $this->session->userdata('name'));
        }
        $query = $this->db->get();
        return $query->result();
        
    }

    /**
     * Get wallet transactions for a given wallet and date range
     * @param int|null $walletid
     * @param string|null $fromDate
     * @param string|null $toDate
     * @return array
     */
    public function walletLedgerReport($walletid = null, $fromDate = null, $toDate = null)
    {
        $whereJournal = 'walletid = ?';
        $whereInvoice = 'walletid = ?';
        $paramsJournal = [$walletid];
        $paramsInvoice = [$walletid];
        if (!empty($fromDate)) {
            $whereJournal .= ' AND jvdate >= ?';
            $paramsJournal[] = $fromDate;
            $whereInvoice .= ' AND paydate >= ?';
            $paramsInvoice[] = $fromDate;
        }
        if (!empty($toDate)) {
            $whereJournal .= ' AND jvdate <= ?';
            $paramsJournal[] = $toDate;
            $whereInvoice .= ' AND paydate <= ?';
            $paramsInvoice[] = $toDate;
        }
        $sql = "(
            SELECT
                jvdate,
                `desc`,
                CASE jvtype
                  WHEN 0 THEN 0
                  WHEN 1 THEN 0
                  WHEN 2 THEN 0
                  WHEN 3 THEN debit
                  WHEN 4 THEN 0
                  WHEN 5 THEN 0
                  ELSE 0
                END as debit,
                CASE jvtype
                  WHEN 0 THEN credit
                  WHEN 1 THEN credit
                  WHEN 2 THEN credit
                  WHEN 3 THEN 0
                  WHEN 4 THEN 0
                  WHEN 5 THEN 0
                  ELSE 0
                END as credit,
                jvtype,
                CASE jvtype
                  WHEN 0 THEN 'EXPENSES'
                  WHEN 1 THEN 'STOCK'
                  WHEN 2 THEN 'DEPOSIT'
                  WHEN 3 THEN 'SALES'
                  WHEN 4 THEN 'ISSUE'
                  WHEN 5 THEN 'RECEIPT'
                  ELSE 'UNKNOWN'
                END as typename
            FROM tbl_journal
            WHERE $whereJournal
        )
        UNION ALL
        (
            SELECT
                paydate as jvdate,
                remarks as `desc`,
                paidamount as debit,
                0 as credit,
                100 as jvtype,
                'COLLECTION' as typename
            FROM tbl_invoicecollection
            WHERE $whereInvoice
        )
        ORDER BY jvdate ASC";
        $params = array_merge($paramsJournal, $paramsInvoice);
        $query = $this->db->query($sql, $params);
        return $query->result();
    }

}
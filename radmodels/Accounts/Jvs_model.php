<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Jvs_model extends CI_Model
{

    // ********* JV List ************** //

    function jvsCount($searchText = '', $type, $managerAllServices)
    {
        $this->db->select('BaseTbl.jvid, BaseTbl.jvdate, BaseTbl.acctdr, BaseTbl.acctcr, BaseTbl.desc, BaseTbl.jvtype, 
                            BaseTbl.managername, BaseTbl.invopqty, BaseTbl.invinqty, BaseTbl.invoutqty,
                            BaseTbl.invclqty, BaseTbl.invprice, BaseTbl.invtotal, BaseTbl.opening, BaseTbl.debit, 
                            BaseTbl.credit, BaseTbl.balance');
        $this->db->from('tbl_journal as BaseTbl');
        //$this->db->join('tbl_accounts as Accounts', 'BaseTbl.acctdr = Accounts.acctid','left');
        $this->db->join('tbl_jvtypes as Types', 'BaseTbl.jvtype = Types.typeid','left');

        $curDate = date("y-m-d");
        
        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.acctdr  LIKE '%".$searchText."%'
                            OR  BaseTbl.acctcr  LIKE '%".$searchText."%'
                            OR  BaseTbl.desc  LIKE '%".$searchText."%'
                            OR  BaseTbl.jvtype  LIKE '%".$searchText."%'
                            OR  BaseTbl.managername  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        $managername = $this->session->userdata ( 'name' );

        if($managername <> 'admin'){
            if($this->ismaster > 0){
                $this->db->where('BaseTbl.managername in (Select managername from rm_managers where mastername = "'.$managername.'")');
            }else{
                $this->db->where('BaseTbl.managername = ', $managername); 
            }
        }

        if($type <> '')
            { $this->db->where('Types.typename', $type); }

        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function jvsListing($searchText = '', $type, $page, $segment, $managerAllServices)
    {
        
        $this->db->query('Set @row_number = 0');
        $this->db->select('BaseTbl.jvid, BaseTbl.jvdate, BaseTbl.acctdr, BaseTbl.acctcr, BaseTbl.desc, BaseTbl.jvtype, 
                            BaseTbl.managername, BaseTbl.invopqty, BaseTbl.invinqty, BaseTbl.invoutqty,
                            BaseTbl.invclqty, BaseTbl.invprice, BaseTbl.invtotal, BaseTbl.opening, BaseTbl.debit, 
                            BaseTbl.credit, BaseTbl.balance,
                            (Select accname from tbl_accounts where acctid=BaseTbl.acctdr) as accnamedr, 
                            (Select accname from tbl_accounts where acctid=BaseTbl.acctcr) as accnamecr, 
                            Types.typename, BaseTbl.username,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('tbl_journal as BaseTbl');
        //$this->db->join('tbl_accounts as Accounts', 'BaseTbl.acctdr = Accounts.acctid','left');
        $this->db->join('tbl_jvtypes as Types', 'BaseTbl.jvtype = Types.typeid','left');
        
        $curDate = date("y-m-d");        

        if(!empty($searchText)) {
            $likeCriteria = "(BaseTbl.acctdr  = '".$searchText."'
                            OR  BaseTbl.acctcr  = '".$searchText."'
                            OR  BaseTbl.desc  LIKE '%".$searchText."%'
                            OR  BaseTbl.jvtype  LIKE '%".$searchText."%'
                            OR  BaseTbl.managername  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        $managername = $this->session->userdata ( 'name' );
        if($managername <> 'admin'){
            if($this->ismaster > 0){
                $this->db->where('BaseTbl.managername = "'.$managername.'" OR BaseTbl.managername in (Select managername from rm_managers where mastername = "'.$managername.'")');
            }else{
                $this->db->where('BaseTbl.managername = ', $managername); 
            }
        }

        if($type <> '')
            { $this->db->where('Types.typename', $type); }

        $this->db->order_by('BaseTbl.jvid', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }

    function addNewJv($jvInfo){

        $this->db->trans_start();
        $this->db->insert('tbl_journal', $jvInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return True;
    }


    function getExpenseInfo($jvid){

        $managername = $this->session->userdata ( 'name' );

        $this->db->select('jvid, jvdate, acctdr, acctcr, desc, jvtype, managername, walletid, invopqty, invinqty, 
                            invoutqty, invclqty, invprice, invtotal, opening, debit, credit, balance, username');
        $this->db->from('tbl_journal');
        $this->db->where('jvid', $jvid);

        $query = $this->db->get();
        return $query->row();

    }

    function getJvTypeCode($jvtype){

        $managername = $this->session->userdata ( 'name' );

        $this->db->select('typeid');
        $this->db->from('tbl_jvtypes');
        $this->db->where('typename', $jvtype);
        $query = $this->db->get();
        return $query->row();

    }

    function getJvTypeList(){

        $managername = $this->session->userdata ( 'name' );

        $this->db->select('typeid, typename');
        $this->db->from('tbl_jvtypes');
        //$this->db->where('typename', $jvtype);

        $query = $this->db->get();
        return $query->result();

    }

    function getStkTypeList(){

        $managername = $this->session->userdata ( 'name' );

        $this->db->select('typeid, typename');
        $this->db->from('tbl_jvtypes');
        $this->db->where('typename IN ("STOCK", "ISSUE", "TRANSFER-IN", "TRANSFER-OUT")');

        $query = $this->db->get();
        return $query->result();

    }

    function updateAccout($accountInfo, $acctid)
    {
        $this->db->where('acctid', $acctid);
        $this->db->update('tbl_accounts', $accountInfo);
        
        return TRUE;
    }

    function updateExpenseEntry($jvInfo, $jvid)
    {
        $this->db->where('jvid', $jvid);
        $this->db->update('tbl_journal', $jvInfo);

        return TRUE;
    }

    function getDepsoitInfo($jvid){

        $managername = $this->session->userdata ( 'name' );

        $this->db->select('jvid, jvdate, acctdr, acctcr, desc, jvtype, managername, walletid, invopqty, invinqty, 
                            invoutqty, invclqty, invprice, invtotal, opening, debit, credit, balance, username');
        $this->db->from('tbl_journal');
        $this->db->where('jvid', $jvid);

        $query = $this->db->get();
        return $query->row();

    }

    function getStockEntryInfo($jvid){

        $managername = $this->session->userdata ( 'name' );

        $this->db->select('jvid, jvdate, acctdr, acctcr, desc, jvtype, managername, invopqty, invinqty, 
                            invoutqty, invclqty, invprice, invtotal, opening, debit, credit, balance, username, walletid');
        $this->db->from('tbl_journal');
        $this->db->where('jvid', $jvid);

        $query = $this->db->get();
        return $query->row();

    }

    function getItemStockInfo($itemId, $itemManagername){

        $managername = $this->session->userdata ( 'name' );

        /*$query = $this->db->query('select acctdr,sum(invinqty) as stock,ifnull((select sum(invinqty) from tbl_journal 
                        where acctcr=A.acctdr and managername=A.managername),0) as Issue from 
                        tbl_journal as A left join tbl_jvtypes as B on A.jvtype=B.typeid 
                        where B.typename = "STOCK" and acctdr="'.$itemId.'" and A.managername = 
                        "'.$itemManagername.'" group by A.acctdr, A.managername');*/
        
        $query = $this->db->query('select 
            SUM(
            CASE jvtype
                  WHEN 1 THEN invinqty
                  WHEN 6 THEN invinqty
                  ELSE 0
                END) as stock,
            SUM(
            CASE jvtype
                  WHEN 4 THEN invinqty
                  WHEN 7 THEN invinqty
                  ELSE 0
                END) as Issue
            FROM tbl_journal as A left join tbl_jvtypes as B on A.jvtype = B.typeid
            where (acctdr="'.$itemId.'" or acctcr="'.$itemId.'") and A.managername = "'.$itemManagername.'" 
            group by A.managername
            ');

        //$query = $this->db->get();
        return $query->row();

    }

    function getPrevItemStockInfo($itemId, $itemManagername){

        $managername = $this->session->userdata ( 'name' );

        $query = $this->db->query('select acctdr,sum(invinqty) as Stock,ifnull((select sum(invinqty) from tbl_journal 
                        where acctcr=A.acctdr and managername=A.managername),0) as Issue from 
                        tbl_journal as A left join tbl_jvtypes as B on A.jvtype=B.typeid 
                        where B.typename = "STOCK" and acctdr="'.$itemId.'" and A.managername = "'.$itemManagername.'" group by A.acctdr, A.managername');

        //$query = $this->db->get();
        return $query->row();

    }

    function getStockListByUser($username) {
        $this->db->select('BaseTbl.jvid, BaseTbl.jvdate, BaseTbl.acctdr, BaseTbl.acctcr, BaseTbl.desc, BaseTbl.jvtype, 
                            BaseTbl.managername, BaseTbl.invopqty, BaseTbl.invinqty, BaseTbl.invoutqty,
                            BaseTbl.invclqty, BaseTbl.invprice, BaseTbl.invtotal, BaseTbl.opening, BaseTbl.debit, 
                            BaseTbl.credit, BaseTbl.balance,
                            (Select accname from tbl_accounts where acctid=BaseTbl.acctdr) as accnamedr, 
                            (Select accname from tbl_accounts where acctid=BaseTbl.acctcr) as accnamecr, 
                            Types.typename');
        $this->db->from('tbl_journal as BaseTbl');
        $this->db->join('tbl_jvtypes as Types', 'BaseTbl.jvtype = Types.typeid','left');
        
        // Filter by username
        $this->db->where('BaseTbl.username', $username);
        
        // Only get stock and issue records
        $this->db->where('(Types.typename = "STOCK" OR Types.typename = "ISSUE")');
        
        $this->db->order_by('BaseTbl.jvdate', 'DESC');
        $query = $this->db->get();
        
        return $query->result();
    }

    function getJvsListByUser($username = NULL, $jvType = NULL) {

        $this->db->select('BaseTbl.jvid, BaseTbl.jvdate, BaseTbl.acctdr, BaseTbl.acctcr, BaseTbl.desc, BaseTbl.jvtype, 
                            BaseTbl.managername, BaseTbl.invopqty, BaseTbl.invinqty, BaseTbl.invoutqty,
                            BaseTbl.invclqty, BaseTbl.invprice, BaseTbl.invtotal, BaseTbl.opening, BaseTbl.debit, 
                            BaseTbl.credit, BaseTbl.balance,
                            (Select accname from tbl_accounts where acctid=BaseTbl.acctdr) as accnamedr, 
                            (Select accname from tbl_accounts where acctid=BaseTbl.acctcr) as accnamecr, 
                            Types.typename');
        $this->db->from('tbl_journal as BaseTbl');
        $this->db->join('tbl_jvtypes as Types', 'BaseTbl.jvtype = Types.typeid','left');
        
        // Filter by username
        $this->db->where('BaseTbl.username', $username);
        
        // Only get stock and issue records
        if($jvType == "STOCK"){
            $this->db->where('(Types.typename = "STOCK" OR Types.typename = "ISSUE")');
        }else{
            $this->db->where('(Types.typename = "SALES" OR Types.typename = "EXPENSES" OR Types.typename = "DEPOSIT")');
        }

        $this->db->order_by('BaseTbl.jvdate, BaseTbl.jvid', 'DESC');
        $query = $this->db->get();
        
        return $query->result();
    }

}
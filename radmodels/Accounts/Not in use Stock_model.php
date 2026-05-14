<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Jvs_model extends CI_Model
{

    // ********* JV List ************** //

    function stockCount($searchText = '', $type, $managerAllServices)
    {
        $this->db->select('BaseTbl.jvid, BaseTbl.acctdr, BaseTbl.acctcr, BaseTbl.desc, BaseTbl.jvtype, 
                            BaseTbl.managername, BaseTbl.invopqty, BaseTbl.invinqty, BaseTbl.invoutqty,
                            BaseTbl.invclqty, BaseTbl.invprice, BaseTbl.invtotal, BaseTbl.opening, BaseTbl.debit, 
                            BaseTbl.credit, BaseTbl.balance');
        $this->db->from('tbl_journal as BaseTbl');

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

        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function stockListing($searchText = '', $type, $page, $segment, $managerAllServices)
    {
        $this->db->query('Set @row_number = 0');
        $this->db->select('BaseTbl.jvid, BaseTbl.acctdr, BaseTbl.acctcr, BaseTbl.desc, BaseTbl.jvtype, 
                            BaseTbl.managername, BaseTbl.invopqty, BaseTbl.invinqty, BaseTbl.invoutqty,
                            BaseTbl.invclqty, BaseTbl.invprice, BaseTbl.invtotal, BaseTbl.opening, BaseTbl.debit, 
                            BaseTbl.credit, BaseTbl.balance, Accounts.accname, Types.typename,
                            (@row_number:=@row_number + 1) AS serial_number');
        $this->db->from('tbl_journal as BaseTbl');
        $this->db->join('tbl_accounts as Accounts', 'BaseTbl.acctdr = Accounts.acctid','left');
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

    function getAccountsList($accgroup)
    {
        $managername = $this->session->userdata ( 'name' );

        $this->db->select('acctid, accname, accgroup, BaseTbl.managername');
        $this->db->from('tbl_accounts as BaseTbl');
        $this->db->join('tbl_accgroup as Group', 'BaseTbl.accgroup = Group.grpid','left');
        $this->db->where('grpname', $accgroup);;
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

    function getExpenseInfo($jvid){

        $managername = $this->session->userdata ( 'name' );

        $this->db->select('jvid, jvdate, acctdr, acctcr, desc, jvtype, managername, invopqty, invinqty, 
                            invoutqty, invclqty, invprice, invtotal, opening, debit, credit, balance');
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

        $this->db->select('jvid, jvdate, acctdr, acctcr, desc, jvtype, managername, invopqty, invinqty, 
                            invoutqty, invclqty, invprice, invtotal, opening, debit, credit, balance');
        $this->db->from('tbl_journal');
        $this->db->where('jvid', $jvid);

        $query = $this->db->get();
        return $query->row();

    }



}
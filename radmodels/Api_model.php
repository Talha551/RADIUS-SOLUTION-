<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Api_model extends CI_Model
{
    function epApiBillInqiry($consumer_number, $reserved = "something, special, send, into, it.")
    {

        $this->db->select("
            A.username,
            '00' as response_Code, concat(A.firstname,' ', A.lastname) as consumer_Detail, 
            CASE    
                WHEN A.enableuser = 0 THEN 'B' 
                WHEN A.expiration > DATE_ADD(CURDATE(), INTERVAL 1 MONTH) THEN 'P' 
                ELSE 'U' 
            END as bill_status, 
            concat(YEAR(CURDATE()), 
            if(month(CURDATE())<10 , CONCAT('0', month(CURDATE())), month(CURDATE())), 
            if(day(CURDATE())<10 , CONCAT('0', day(CURDATE())), day(CURDATE()))) as due_date, 
            concat('+', LPAD(replace((convert(abs(C.saleprice-B.discount+B.adjamount),CHAR)),'.',''),13,0)) as amount_within_dueDate, 
            concat('+', LPAD(replace((convert(abs(C.saleprice-B.discount+B.adjamount),CHAR)),'.',''),13,0)) as amount_after_dueDate,
            concat(DATE_FORMAT(CURDATE(), '%y'), if(month(CURDATE())<10 , CONCAT('0', 
            month(CURDATE())), month(CURDATE()))) as billing_month, 
            '' as date_paid, '' as amount_paid, '' as tran_auth_Id,
            concat('User Expiration date: ', A.expiration) as reserved
        ");

        $this->db->from('rm_users as A');
        $this->db->join('tbl_userdocs as B', 'A.username = B.username','left');
        $this->db->join('tbl_services as C', 'A.srvid = C.radsrvid','left');
        $this->db->where('B.payid', $consumer_number);
        //$this->db->or_where('A.taxid', $consumer_number);

        //$data = $this->db->get_where("rm_users", ['username' => $username])->row_array();
        $query = $this->db->get();
        $result = $query->row();

        return $result;

    }

    function epApiEpPayment($insertquery){
        $this->db->trans_start();
        $this->db->insert('tbl_eppayments', $insertquery);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();
    }

    function updateUserExpiry($payid, $updateUserExpiryDate)
    {
        $this->db->where('username IN (Select username from tbl_userdocs where  payid="'.$payid.'")');
        $this->db->update('rm_users', $updateUserExpiryDate);
        
        return TRUE;
    }


    function userInqiryIPPortal($username)
    {

        $this->db->select("username, firstname, lastname, address, mobile as cotnact, taxid as idenityid,
                            createdon as membersince, enableuser as status
                        ");

        $this->db->from('rm_users as A');
        $this->db->where('A.username', $username);
        //$this->db->or_where('A.taxid', $consumer_number);

        //$data = $this->db->get_where("rm_users", ['username' => $username])->row_array();
        $query = $this->db->get();
        $result = $query->row();

        return $result;

    }

    function epApiBillPayment($insertquery){

        $this->db->select("A.username, A.payname, B.owner");
        $this->db->from('tbl_userdocs as A');
        $this->db->join('rm_users as B', 'A.username = B.username', 'left');
        $this->db->where('A.payid', $consumer_number);
        $query = $this->db->get();
        $resultUserInfo = $query->row();

        if ($query->num_rows() > 0) {

            $this->db->select("tran_auth_id");
            $this->db->from('tbl_eppayments as A');
            $this->db->where('A.tran_auth_id', $tran_auth_id);
            $queryTransAuthId = $this->db->get();
            $resultTransAuthId = $queryTransAuthId->row();

            if ($queryTransAuthId->num_rows() > 0) {
                echo json_encode(array(
                    'response_Code' => '03',
                    'Identification_parameter' => 'Duplicate Transaction',
                    'reserved' => 'Duplicate Transaction'
                ));
                return;
            } else {
                //Insert Customer Payment Details to Database
                $resultPayInsert = $this->api_model->epApiEpPayment($paymentInfo);

                // UPDATE USER EXPIRTY
                $date1 = Date("Y-m-d");  // Service Date
                $date2 = date('Y-m-d', strtotime("+1 month", strtotime($date1)));
                $updateUserExpiryDate = array('expiration' => $date2);

                $updateUserExpiry = $this->api_model->updateUserExpiry($consumer_number, $updateUserExpiryDate); // Temprorary Enable User 

                // Return Response to API
                echo json_encode(array(
                    'response_Code' => '00',
                    'Identification_parameter' => $resultUserInfo->payname,
                    'reserved' => 'Payment received Thank you'
                ));
                return;
            }
        } else {
            echo json_encode(array(
                'response_Code' => '01',
                'Identification_parameter' => 'CUSTOMER_NOT_FOUND',
                'reserved' => 'RESPONSE_CUSTOMER_RELATIONSHIP_NOT_FOUND'
            ));
            return;
        }

    }

    function get_eplastinvoice($username)
    {
        $this->db->select("BaseTbl.transid, BaseTbl.username, BaseTbl.srvid, BaseTbl.price, BaseTbl.amount as amount, 
                        BaseTbl.invtype, BaseTbl.managername, BaseTbl.createdBy, BaseTbl.srvdate, BaseTbl.expdate, 
                        BaseTbl.remarks, BaseTbl.createdDtm, BaseTbl.paid, BaseTbl.crdays, BaseTbl.jvid,
                        concat(YEAR(CURDATE()), 
                        if(month(createdDtm)<10 , CONCAT('0', month(createdDtm)), month(createdDtm)), 
                        if(day(createdDtm)<10 , CONCAT('0', day(createdDtm)), day(createdDtm))) as date_paid, 
                        concat(LPAD(replace((convert(abs(BaseTbl.amount),CHAR)),'.',''),13,0)) as amount_paid,
                        ");
        $this->db->from('tbl_invoices as BaseTbl');
        $this->db->where('BaseTbl.username', $username);
        $this->db->where('ABS(BaseTbl.amount) >', 0);
        //$this->db->where('DATE_FORMAT(BaseTbl.createdDtm, "%Y-%m") =', date('Y-m'));
        $this->db->order_by('BaseTbl.transid', 'DESC');
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    /**
     * API User Management Methods
     */
    function get_api_users($owner = null)
    {
        $this->db->select('*');
        $this->db->from('api_users');
        if ($owner) {
            // Filter users based on logged in manager, optionally showing unassigned
            $this->db->where("owner", $owner);
            $this->db->or_where("owner", "");
            $this->db->or_where("owner IS NULL", null, false);
        }
        $this->db->order_by('created_at', 'DESC');
        $query = $this->db->get();
        return $query->result();
    }

    function get_api_user($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get('api_users');
        return $query->row();
    }

    function get_api_user_by_email($email)
    {
        $this->db->where('email', $email);
        $query = $this->db->get('api_users');
        return $query->row();
    }

    function update_api_user($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update('api_users', $data);
        return $this->db->affected_rows() > 0;
    }

    function insert_api_invoice($data)
    {
        $this->db->insert('api_invoices', $data);
        return $this->db->insert_id();
    }

    function generate_unique_request_code()
    {
        $code = str_pad(rand(10000, 99999), 5, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        $this->db->where('requestcode', $code);
        $query = $this->db->get('api_users');
        
        if ($query->num_rows() > 0) {
            return $this->generate_unique_request_code(); // Recursive call if exists
        }
        
        return $code;
    }

    function get_manager_gracedays($managername)
    {
        $this->db->select('stgvalue');
        $this->db->from('tbl_settings');
        $this->db->where('stgtype', 'GRACE-DAYS-OLD');
        $this->db->where('managername', $managername);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return (int) $query->row()->stgvalue;
        }
        return 0;
    }

}
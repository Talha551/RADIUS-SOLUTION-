<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Apps Model
 * Handles API-related database operations for user data retrieval
 */
class Apps_model extends CI_Model
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Search user data by username, mobile number and payment ID
     * 
     * @param string $username
     * @param string $mobile
     * @param string $payment_id
     * @return array|false
     */
    public function search_user_data($username, $mobile, $payment_id)
    {
        $this->db->select('u.username, u.firstname, u.lastname, u.mobile, u.owner, u.expiration, u.verified, u.srvid');
        $this->db->select('d.cnicno, d.payid, d.inst_name, d.inst_chrg');
        $this->db->select('s.srvname, s.saleprice');
        $this->db->from('rm_users u');
        $this->db->join('tbl_userdocs d', 'u.username = d.username', 'left');
        $this->db->join('tbl_services s', 'u.srvid = s.radsrvid', 'left');
        $this->db->where('u.username', $username);
        $this->db->where('u.mobile', $mobile);
        $this->db->where('d.payid', $payment_id);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->row_array();
        }
        
        return false;
    }
    
    /**
     * Validate API key
     * 
     * @param string $api_key
     * @return bool
     */
    public function validate_api_key($api_key)
    {
        // For now, we'll use the hardcoded key from controller
        // In the future, this could be moved to database
        $valid_key = 'PaceTelecom@2025';
        return ($api_key === $valid_key);
    }
    
    /**
     * Get user invoices by username and date range
     * 
     * @param string $username
     * @param string $from_date (optional)
     * @param string $to_date (optional)
     * @return array
     */
    public function get_user_invoices($username, $from_date = null, $to_date = null)
    {
        $this->db->select('i.username, i.srvdate, i.expdate, i.createdDtm as invdate');
        $this->db->select('s.srvname, s.saleprice');
        $this->db->from('tbl_invoices i');
        $this->db->join('tbl_services s', 'i.srvid = s.srvid', 'left');
        $this->db->where('i.username', $username);
        
        // If date range is provided, filter by srvdate
        if (!empty($from_date) && !empty($to_date)) {
            $this->db->where('i.srvdate >=', $from_date);
            $this->db->where('i.srvdate <=', $to_date);
        } else {
            // If no date range, get last 5 transactions
            $this->db->order_by('i.createdDtm', 'DESC');
            $this->db->limit(5);
        }
        
        $this->db->order_by('i.createdDtm', 'DESC');
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            return $query->result_array();
        }
        
        return array();
    }
    
    /**
     * Get user online status by checking acctstoptime
     * 
     * @param string $username
     * @return bool
     */
    public function get_user_online_status($username)
    {
        $this->db->select('acctstoptime');
        $this->db->from('radacct');
        $this->db->where('username', $username);
        $this->db->where('acctstoptime', null);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            // Return true if acctstoptime is null, false otherwise
            return ($row['acctstoptime'] === null);
        }
        
        return false; // User not found
    }
} 
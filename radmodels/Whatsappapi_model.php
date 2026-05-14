<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Whatsappapi_model - Model for WhatsApp API operations
 */
class Whatsappapi_model extends CI_Model
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    /**
     * Get users whose subscriptions are expiring in the next 24 hours
     * Only select users with warningsent = 0 and limit to 500 users
     * 
     * @return array List of users with expiring subscriptions
     */
    public function get_expiring_users()
    {
        // Calculate date range for expiration (next 24 hours)
        $now = date('Y-m-d H:i:s');
        $next_24_hours = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Query users whose expiration is in the next 24 hours
        // and who have a mobile number and warningsent = 0
        $this->db->select('A.username as userId, A.firstName, A.lastName, A.mobile, A.expiration as expiryDate, B.payid');
        $this->db->from('rm_users as A');
        $this->db->join('tbl_userdocs as B', 'A.username = B.username', 'left');
        $this->db->where('A.expiration >=', $now);
        $this->db->where('A.expiration <=', $next_24_hours);
        $this->db->where('A.mobile IS NOT NULL');
        $this->db->where('B.payid IS NOT NULL');
        $this->db->where('A.mobile !=', '');
        $this->db->where('A.warningsent', 0); // Only select users not yet notified
        $this->db->where('A.owner in ("banigala", "chahtta1", "charsada1", "mardan3", "mardan4", "mardan5", "mardan6", "saidu", "sarderi1")');
        $this->db->limit(200); // Limit to 500 users maximum
        
        $query = $this->db->get();
        
        return $query->result_array();
    }
    
    /**
     * Mark a user as notified about expiration by setting warningsent to 1
     * 
     * @param string $user_id User ID to mark as notified
     * @return bool Success status
     */
    public function mark_user_notified($user_id)
    {
        // Update warningsent field to 1 when user is notified
        $this->db->where('username', $user_id);
        $this->db->update('rm_users', ['warningsent' => 1]);
        
        return $this->db->affected_rows() > 0;
    }
    
    /**
     * Get a single user by ID
     * 
     * @param int $user_id User ID to retrieve
     * @return array User data
     */
    public function get_user_by_id($user_id)
    {
        $this->db->select('username, firstName, lastName, mobile, expiration');
        $this->db->from('rm_users');
        $this->db->where('username', $user_id);
        
        $query = $this->db->get();
        
        return $query->row_array();
    }
    
    /**
     * Reset warningsent field to 0 for users who:
     * 1. Have warningsent = 1
     * 2. AND their subscription has already expired OR expires more than 24 hours in the future
     * 
     * @return int Number of users reset
     */
    public function reset_expired_warnings()
    {
        // Calculate date ranges
        $now = date('Y-m-d H:i:s');
        $next_24_hours = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Build where conditions
        $this->db->where('warningsent', 1); // Currently marked as notified
        
        $this->db->group_start(); // Start of grouped condition
        $this->db->where('expiration <', $now); // Already expired
        $this->db->or_where('expiration >', $next_24_hours); // OR expiration is beyond 24 hours
        $this->db->group_end(); // End of grouped condition
        
        // Update warningsent to 0
        $this->db->update('rm_users', ['warningsent' => 0]);
        
        return $this->db->affected_rows();
    }

    /**
     * Create the payment receipt table if it doesn't exist
     */
    private function create_payment_table_if_not_exists()
    {
        // Check if table exists
        if ($this->db->table_exists('tbl_epautobonus')) {
            return;
        }
        
        // Create the table
        $this->db->query("
            CREATE TABLE `tbl_epautobonus` (
                `transid` int(11) NOT NULL AUTO_INCREMENT,
                `paymentdate` datetime DEFAULT NULL,
                `referenceid` varchar(50) DEFAULT NULL,
                `customername` varchar(100) DEFAULT NULL,
                `payid` varchar(20) DEFAULT NULL,
                `paidby` varchar(100) DEFAULT NULL,
                `contact` varchar(20) DEFAULT NULL,
                `amount` decimal(10,2) DEFAULT NULL,
                `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`transid`),
                KEY `payid_idx` (`payid`),
                KEY `referenceid_idx` (`referenceid`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ");
        
        log_message('info', 'Created tbl_epautobonus table');
    }

    /**
     * Save payment receipt data to the database
     * 
     * @param array $payment_data The payment data extracted from the receipt
     * @return int|bool The inserted ID or false on failure
     */
    public function save_payment_receipt($payment_data)
    {
        // Create table if it doesn't exist
        $this->create_payment_table_if_not_exists();
        
        // Prepare data for insertion
        $insert_data = [
            'payid' => isset($payment_data['payid']) ? $payment_data['payid'] : null
        ];
        
        // Add payment date if available
        if (isset($payment_data['paymentdate']) && !empty($payment_data['paymentdate'])) {
            try {
                $payment_date = date_create_from_format('j F Y h:i A', $payment_data['paymentdate']);
                if ($payment_date) {
                    $insert_data['paymentdate'] = $payment_date->format('Y-m-d H:i:s');
                }
            } catch (Exception $e) {
                log_message('error', 'Error parsing payment date: ' . $e->getMessage());
            }
        }
        
        // Add other available fields
        $fields = ['referenceid', 'customername', 'paidby', 'contact', 'amount'];
        foreach ($fields as $field) {
            if (isset($payment_data[$field]) && !empty($payment_data[$field])) {
                $insert_data[$field] = $payment_data[$field];
            }
        }
        
        // Insert into database
        $this->db->insert('tbl_epautobonus', $insert_data);
        
        if ($this->db->affected_rows() > 0) {
            return $this->db->insert_id();
        }
        
        return false;
    }

    /**
     * Check if a payment with the same reference ID already exists
     * 
     * @param string $reference_id The reference ID to check
     * @return bool True if payment already exists
     */
    public function payment_exists($reference_id)
    {
        if (empty($reference_id)) {
            return false;
        }
        
        $this->db->where('referenceid', $reference_id);
        $query = $this->db->get('tbl_epautobonus');
        
        return $query->num_rows() > 0;
    }

    function getUserInformation($payID)
    {
        $this->db->select('A.username, A.owner, A.srvid as radsrvid, B.srvid as srvid, B.baseprice, B.costprice, B.saleprice, A.expiration as expdate');
        $this->db->from('rm_users as A');
        $this->db->join('tbl_userdocs as C', 'A.username = C.username', 'left');
        $this->db->join('tbl_services as B', 'A.srvid = B.radsrvid and A.owner = B.managername', 'left');
        $this->db->where('C.payid', $payID);


        $query = $this->db->get();
        return $query->row();

    }
} 
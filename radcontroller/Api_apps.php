<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

class Api_apps extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        // Load input library for linter compatibility (CodeIgniter loads it by default, but this helps static analysis)
        if (!isset($this->input)) {
            $this->load->library('input');
        }
        $this->load->model('Invoices_model');
        $this->load->model('Services_model');
        $this->load->model('Reseller_model');
        $this->load->model('users_model');
        $this->load->model('User_model');
        $this->load->model('Accounts/Accounts_model');
        $this->load->model('Accounts/Jvs_model');
        $this->load->model('Api_model'); // Load the API model
        $this->load->helper(array('form', 'url'));
        $this->isLoggedIn();   
    }

    public function index()
    {
        $this->global['pageTitle'] = 'API Users Management';
        
        $role = $this->session->userdata('role');
        $managerName = $this->session->userdata('name');
        $owner = null;
        
        // If the user is not a super admin (role 1) AND their name is not exactly "admin", filter by their owner name
        if ($role != 1 && strtolower($managerName) !== 'admin') {
             $owner = $managerName;
        }
        
        $data['apiUsersRecords'] = $this->Api_model->get_api_users($owner);
        $this->loadViews("apiapps/index", $this->global, $data, NULL);
    }

    public function toggle_active()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status'); // 'true' or 'false'
        
        $is_active = ($status === 'true') ? 1 : 0;
        
        $result = $this->Api_model->update_api_user($id, array('is_active' => $is_active));
        
        if ($result) {
            echo json_encode(array("status" => "success", "message" => "User status updated successfully."));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to update user status."));
        }
    }

    public function generate_request_code()
    {
        $id = $this->input->post('id');
        
        $code = $this->Api_model->generate_unique_request_code();
        
        $result = $this->Api_model->update_api_user($id, array('requestcode' => $code));
        
        if ($result) {
            echo json_encode(array("status" => "success", "code" => $code, "message" => "Request code generated successfully."));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to generate request code."));
        }
    }

    public function approve_owner()
    {
        $id = $this->input->post('id');
        $managerName = $this->session->userdata('name');
        
        $gracedays = $this->Api_model->get_manager_gracedays($managerName);
        
        $result = $this->Api_model->update_api_user($id, array(
            'owner' => $managerName,
            'gracedays' => $gracedays
        ));
        
        if ($result) {
            echo json_encode(array("status" => "success", "owner" => $managerName, "message" => "User approved and assigned successfully."));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to approve user."));
        }
    }

    public function add_credit()
    {
        $id = $this->input->post('id');
        $amount = $this->input->post('amount');
        
        if (!is_numeric($amount) || $amount == 0) {
            echo json_encode(array("status" => "error", "message" => "Amount must be a non-zero number."));
            return;
        }

        // Fetch user data
        $user = $this->Api_model->get_api_user($id);
        if (!$user) {
            echo json_encode(array("status" => "error", "message" => "API User not found."));
            return;
        }

        // Prepare invoice
        $invoiceData = array(
            'username' => $user->email,
            'amount' => $amount,
            'invtype' => 'Credit',
            'owner' => $this->session->userdata('name'),
            'id' => $user->id,
            'srvdate' => date('Y-m-d'),
            'extdate' => date('Y-m-d', strtotime('+1 month')),
            'remarks' => $amount > 0 ? 'Manual Credit Addition' : 'Manual Credit Deduction'
        );

        $result = $this->Api_model->insert_api_invoice($invoiceData);

        if ($result) {
            echo json_encode(array("status" => "success", "message" => "Credit invoice created successfully."));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to create invoice."));
        }
    }

    public function update_request_code_by_email()
    {
        $email = $this->input->post('email');
        $code = $this->input->post('code'); // Can be empty if generating random
        $postOwner = $this->input->post('owner'); 
        
        $user = $this->Api_model->get_api_user_by_email($email);
        
        if (!$user) {
            echo json_encode(array("status" => "error", "message" => "User with this email not found."));
            return;
        }

        $role = $this->session->userdata('role');
        $managerName = $this->session->userdata('name');
        
        $updateData = array();

        // If the user is not a super admin, they can only modify users whose owner is 'pppoe'
        if ($role != 1 && strtolower($managerName) !== 'admin') {
             if (strtolower(trim($user->owner)) !== 'pppoe') {
                 echo json_encode(array("status" => "error", "message" => "Managers can only generate or change request codes for unassigned (pppoe) users."));
                 return;
             }
             // For managers, force the owner to be themselves
             $updateData['owner'] = $managerName;
             $updateData['gracedays'] = $this->Api_model->get_manager_gracedays($managerName);
        } else {
             // For admin, use the provided owner if it's set
             if ($postOwner !== null && trim($postOwner) !== '') {
                 $updateData['owner'] = trim($postOwner);
                 $updateData['gracedays'] = $this->Api_model->get_manager_gracedays(trim($postOwner));
             }
        }
        
        if (empty($code)) {
            $code = $this->Api_model->generate_unique_request_code();
        }
        
        $updateData['requestcode'] = $code;
        
        $result = $this->Api_model->update_api_user($user->id, $updateData);
        
        if ($result) {
            echo json_encode(array("status" => "success", "code" => $code, "message" => "Request code and owner updated successfully."));
        } else {
            echo json_encode(array("status" => "error", "message" => "Failed to update API user."));
        }
    }

}
<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

class Wireguardvpn_controller extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Wireguard_model');
        $this->load->model('users_model');
        $this->load->helper('form');
        $this->isLoggedIn();   
    }

    /**
     * Default page - list all Wireguard users
     */
    public function index()
    {
        $this->global['pageTitle'] = 'Wireguard VPN : Users List';
        $searchText = $this->input->get('searchText');
        $data['searchText'] = $searchText;
        $data['users'] = $this->Wireguard_model->getAllUsers($searchText);
        $this->loadViews("wireguard/list", $this->global, $data, NULL);
    }

    /**
     * Add new Wireguard user
     */
    public function add()
    {
        $data['servers'] = $this->Wireguard_model->getWireguardServers();
        
        if ($this->input->post()) {
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('username', 'Username', 'required');
            $this->form_validation->set_rules('server_id', 'Server', 'required');
            
            if ($this->form_validation->run() == TRUE) {
                $username = $this->security->xss_clean($this->input->post('username'));
                $server_id = $this->security->xss_clean($this->input->post('server_id'));
                
                // Validate server parameters
                $missing_params = $this->Wireguard_model->validateServerParameters($server_id);
                if (!empty($missing_params)) {
                    $this->session->set_flashdata('error', 'Server is missing required parameters: ' . implode(', ', $missing_params));
                    redirect('wireguard/add');
                }
                
                // Generate keys and port
                $keys = $this->Wireguard_model->generateWireguardKeys();
                $port = $this->Wireguard_model->generateRandomPort();
                
                // Get server details
                $server_details = $this->Wireguard_model->getServerDetails($server_id);
                $server_data = [];
                foreach ($server_details as $detail) {
                    $server_data[$detail->parameter] = $detail->paravalue2;
                }
                
                // Get next available IP
                $ip_pool = $this->Wireguard_model->getNextAvailableIP($server_id);
                if (!$ip_pool) {
                    $this->session->set_flashdata('error', 'No available IP in the pool for this server');
                    redirect('wireguard/add');
                }
                
                // Prepare user data
                $userData = [
                    'username' => $username,
                    'privatekey' => $keys['private_key'],
                    'publickey' => $keys['public_key'],
                    'listenport' => $port,
                    'ipaddress' => $ip_pool, // Assigned unique IP
                    'serverpublickey' => $server_data['serverpublickey'],
                    'serverport' => $server_data['serverport'],
                    'serveripaddress' => $server_data['servername']
                ];
                
                // Add user to database
                if ($this->Wireguard_model->addWireguardUser($userData)) {
                    // Add peer to Wireguard server (local or remote)
                    $peerAdded = $this->Wireguard_model->addPeerAuto($userData['publickey'], explode('/', $userData['ipaddress'])[0], $server_id);

                    if ($peerAdded) {
                        $this->session->set_flashdata('success', 'Wireguard user added and peer added to server successfully');
                        redirect('wireguard');
                    } else {
                        $this->session->set_flashdata('error', 'User added, but failed to add peer to Wireguard server. Please check server connectivity and permissions.');
                        // Optionally: $this->Wireguard_model->deleteUser($userData['username']);
                        redirect('wireguard');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Failed to add Wireguard user');
                }
            }
        }
        
        $this->global['pageTitle'] = 'Wireguard VPN : Add New User';
        $this->loadViews("wireguard/add", $this->global, $data, NULL);
    }

    /**
     * Search users for autocomplete
     */
    public function search_users()
    {
        $term = $this->security->xss_clean($this->input->get('term'));
        $users = $this->users_model->searchUsers($term);
        echo json_encode($users);
    }

    /**
     * View user details
     */
    public function view($userId)
    {
        $data['user'] = $this->Wireguard_model->getUserByUsername($userId);
        if (!$data['user']) {
            show_404();
        }
        $this->global['pageTitle'] = 'Wireguard VPN : User Details';
        $this->loadViews("wireguard/view", $this->global, $data, NULL);
    }

    /**
     * Delete user
     */
    public function delete($userId)
    {
        if ($this->Wireguard_model->deleteUser($userId)) {
            $this->session->set_flashdata('success', 'User deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete user');
        }
        redirect('wireguardvpn');
    }
} 
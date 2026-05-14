<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class Reseller_controller extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Reseller_model');
        $this->isLoggedIn();
    }
    
    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->global['pageTitle'] = 'PaceTel : Dashboard';
        
        $this->loadViews("dashboard", $this->global, NULL , NULL);
    }

    // Resellers Listing
    function resellerListing()
    {
        if($this->perm_listmanagers == 0 && $this->session->userdata('login_type') != 'profile')
        {
            $this->session->set_flashdata('error', 'You do not have permission to access this page');
            redirect('dashboard');
        }elseif($this->perm_listmanagers == 1 && $this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(2,3,4))){
            $this->session->set_flashdata('error', 'Profile do not have permission to access this page');
            redirect('dashboard');
        }

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('users_model');
            $managerInfo = $this->users_model->getManagerInfo($this->session->userdata ( 'name' ));
            $managerAllServices = $managerInfo->perm_createservices;
            //$searchText = $this->session->userdata ( 'name' );        
            $searchText = $this->security->xss_clean($this->input->post('searchText'));
            $filterType = $this->input->post('filterType') ? $this->input->post('filterType') : 0;

            $data['searchText'] = $searchText;
            $data['filterType'] = $filterType;
            
            $this->load->library('pagination');
            
            $count = $this->Reseller_model->resellerListingCount($searchText, $managerAllServices, $filterType);

            $returns = $this->paginationCompress ( "resellerListing/", $count, 25 );
            
            $data['resellerListing'] = $this->Reseller_model->resellerListing($searchText, $returns["page"], $returns["segment"], $managerAllServices, $filterType);

            // Attach user stats for each manager
            foreach ($data['resellerListing'] as &$record) {
                $stats = $this->Reseller_model->getManagerUserStatsRecursive($record->managername);
                $record->active = $stats['active'];
                $record->expired_last_month = $stats['expired_last_month'];
                $record->total = $stats['total'];
                $record->new_users = $stats['new_users'];
                $record->online = $stats['online'];
            }
            unset($record);

            $this->global['pageTitle'] = 'Pace-Tel : Reseller Listing';
            
            $this->loadViews("resellerList", $this->global, $data, NULL);
        }
    }

    function resellerAddNew()
    {

        if($this->perm_createmanagers == 0 && $this->session->userdata('login_type') != 'profile')
        {
            $this->session->set_flashdata('error', 'You do not have permission to access this page');
            redirect('dashboard');
        }elseif($this->perm_createmanagers == 1 && $this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(2,3,4))){
            $this->session->set_flashdata('error', 'Profile do not have permission to access this page');
            redirect('dashboard');
        }

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            //$this->load->model('Reseller_model');
            $data['resellers'] = $this->Reseller_model->getResellerList();
            
            $this->global['pageTitle'] = 'PaceTel : Add New Reseller';

            $this->loadViews("resellerAddNew", $this->global, $data, NULL);
        }
    }


    function saveNewReseller()
    {

        //echo "sdkjaskdfjkasdjfkasdfkjs"; 

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('user','Reseller Name','trim|required|max_length[50]|callback_resellernameExists');
            $this->form_validation->set_rules('password','Password','required|max_length[20]');
            $this->form_validation->set_rules('cpassword','Confirm Password','trim|required|matches[password]|max_length[20]');
            //$this->form_validation->set_rules('master','Master','required');

            $this->form_validation->set_rules('fname','First Name','trim|required|max_length[20]');
            $this->form_validation->set_rules('lname','Last Name','trim|required|max_length[20]');
            $this->form_validation->set_rules('address','Address','trim|required|max_length[100]');
            $this->form_validation->set_rules('mobile','Mobile Number','required|min_length[12]');
            $this->form_validation->set_rules('email','Email','trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('cnic','CNIC No.','trim|required|max_length[16]');

            if($this->form_validation->run() == FALSE)
            {
                $this->resellerAddNew();
            }
            else
            {

                $this->load->model('Reseller_model');
                $this->load->model('users_model');

                $user = str_replace(' ', '', strtolower($this->security->xss_clean($this->input->post('user'))));
                $password = $this->input->post('password');

                $fname = ucwords(strtolower($this->security->xss_clean($this->input->post('fname'))));
                $lname = ucwords(strtolower($this->security->xss_clean($this->input->post('lname'))));
                $address = $this->input->post('address');
                $city = ucwords(strtolower($this->security->xss_clean($this->input->post('city'))));
                $mobile = $this->security->xss_clean($this->input->post('mobile'));
                $email = strtolower($this->security->xss_clean($this->input->post('email')));
                $cnic = $this->input->post('cnic');
                if($this->session->userdata ( 'name' ) == 'admin' || $this->ismaster > 0 || $this->perm_createmanagers == 1){
                    $master = strtolower($this->security->xss_clean($this->input->post('master')));
                }else{
                    $master = "NONE";
                }
                //$radsrvid = $this->Services_model->getServiceInfo($srvid);

                // Get permission values from form
                $perm_listusers = $this->input->post('perm_listusers') ? 1 : 0;
                $perm_createusers = $this->input->post('perm_createusers') ? 1 : 0;
                $perm_editusers = $this->input->post('perm_editusers') ? 1 : 0;
                $perm_edituserspriv = $this->input->post('perm_edituserspriv') ? 1 : 0;
                $perm_deleteusers = $this->input->post('perm_deleteusers') ? 1 : 0;
                
                $perm_listmanagers = $this->input->post('perm_listmanagers') ? 1 : 0;
                $perm_createmanagers = $this->input->post('perm_createmanagers') ? 1 : 0;
                $perm_editmanagers = $this->input->post('perm_editmanagers') ? 1 : 0;
                $perm_deletemanagers = $this->input->post('perm_deletemanagers') ? 1 : 0;
                
                $perm_listservices = $this->input->post('perm_listservices') ? 1 : 0;
                $perm_createservices = $this->input->post('perm_createservices') ? 1 : 0;
                $perm_editservices = $this->input->post('perm_editservices') ? 1 : 0;
                $perm_deleteservices = $this->input->post('perm_deleteservices') ? 1 : 0;
                
                $perm_addcredits = $this->input->post('perm_addcredits') ? 1 : 0;
                $perm_negbalance = $this->input->post('perm_negbalance') ? 1 : 0;
                $perm_allowdiscount = $this->input->post('perm_allowdiscount') ? 1 : 0;
                $perm_enwriteoff = $this->input->post('perm_enwriteoff') ? 1 : 0;
                
                $perm_listinvoices = $this->input->post('perm_listinvoices') ? 1 : 0;
                $perm_listallinvoices = $this->input->post('perm_listallinvoices') ? 1 : 0;
                $perm_editinvoice = $this->input->post('perm_editinvoice') ? 1 : 0;
                $perm_showinvtotals = $this->input->post('perm_showinvtotals') ? 1 : 0;
                
                $perm_cardsys = $this->input->post('perm_cardsys') ? 1 : 0;
                $perm_accessap = $this->input->post('perm_accessap') ? 1 : 0;
                $perm_trafficreport = $this->input->post('perm_trafficreport') ? 1 : 0;
                $perm_cts = $this->input->post('perm_cts') ? 1 : 0;
                
                $perm_listonlineusers = $this->input->post('perm_listonlineusers') ? 1 : 0;
                $perm_allusers = $this->input->post('perm_allusers') ? 1 : 0;
                $perm_logout = $this->input->post('perm_logout') ? 1 : 0;

                $perm_allowaccountsadd = $this->input->post('perm_allowaccountsadd') ? 1 : 0;
                $perm_allowaccountssync = $this->input->post('perm_allowaccountssync') ? 1 : 0;
                $perm_forceprofile = $this->input->post('perm_forceprofile') ? 1 : 0;
                $perm_macbinding = $this->input->post('perm_macbinding') ? 1 : 0;
                $perm_hostspotrandomcheck = $this->input->post('perm_hostspotrandomcheck') ? 1 : 0;
                $perm_forceactivation = $this->input->post('perm_forceactivation') ? 1 : 0;
                $perm_allowaccounts = $this->input->post('perm_allowaccounts') ? 1 : 0;
                $perm_fullrefund = $this->input->post('perm_fullrefund') ? 1 : 0;
                $perm_allowdowngrade = $this->input->post('perm_allowdowngrade') ? 1 : 0;
                $perm_networkmanager = $this->input->post('perm_networkmanager') ? 1 : 0;
                $perm_areaaccess = $this->input->post('perm_areaaccess') ? 1 : 0;

                $resellerInfo = array('managername'=>$user, 'password'=>MD5($password), 'mastername'=>$master, 'enablemanager'=> 1,
                                    'firstname'=>$fname, 'lastname'=>$lname, 'address'=>$address, 'city'=>$city,
                                    'mobile'=>$mobile, 'email'=>$email, 'vatid'=>$cnic,
                                    'perm_listusers'=>$perm_listusers, 'perm_listservices'=>$perm_listservices, 
                                    'perm_listonlineusers'=>$perm_listonlineusers, 'perm_listinvoices'=>$perm_listinvoices,
                                    'perm_trafficreport'=>$perm_trafficreport, 'perm_addcredits'=>$perm_addcredits, 
                                    'perm_negbalance'=>$perm_negbalance, 'perm_showinvtotals'=>$perm_showinvtotals,
                                    'perm_createusers'=>$perm_createusers, 'perm_editusers'=>$perm_editusers, 
                                    'perm_edituserspriv'=>$perm_edituserspriv, 'perm_deleteusers'=>$perm_deleteusers,
                                    'perm_listmanagers'=>$perm_listmanagers, 'perm_createmanagers'=>$perm_createmanagers, 
                                    'perm_editmanagers'=>$perm_editmanagers, 'perm_deletemanagers'=>$perm_deletemanagers,
                                    'perm_createservices'=>$perm_createservices, 'perm_editservices'=>$perm_editservices, 
                                    'perm_deleteservices'=>$perm_deleteservices, 'perm_listallinvoices'=>$perm_listallinvoices,
                                    'perm_cardsys'=>$perm_cardsys, 'perm_editinvoice'=>$perm_editinvoice, 
                                    'perm_allusers'=>$perm_allusers, 'perm_allowdiscount'=>$perm_allowdiscount,
                                    'perm_enwriteoff'=>$perm_enwriteoff, 'perm_accessap'=>$perm_accessap,
                                    'perm_cts'=>$perm_cts, 'perm_logout'=>$perm_logout, 
                                    'perm_allowaccountsadd'=>$perm_allowaccountsadd,
                                    'perm_allowaccountssync'=>$perm_allowaccountssync,
                                    'perm_forceprofile'=>$perm_forceprofile,
                                    'perm_macbinding'=>$perm_macbinding,
                                    'perm_hostspotrandomcheck'=>$perm_hostspotrandomcheck,
                                    'perm_forceactivation'=>$perm_forceactivation,
                                    'perm_allowaccounts'=>$perm_allowaccounts,
                                    'perm_fullrefund'=>$perm_fullrefund,
                                    'perm_allowdowngrade'=>$perm_allowdowngrade,
                                    'perm_networkmanager'=>$perm_networkmanager,
                                    'perm_areaaccess'=>$perm_areaaccess,
                                    'lang'=>'English');

                log_message('info', $this->session->userdata ( 'name' ). " details: " . json_encode($resellerInfo));
                //$radpassword = array('username'=>$user, 'attribute'=>'Cleartext-Password', 'op'=>':=', 'value'=>$password);
                //$radsimuse = array('username'=>$user, 'attribute'=>'Simultaneous-Use', 'op'=>':=', 'value'=>'1');

                //$logInfo = 'user: '.$user.'-srvid: '.$radsrvid->radsrvid.'-createdby: '.$this->session->userdata ( 'name' );
                //log_message('info', 'DB_INFO - Created User '.$logInfo);


                if($this->Reseller_model->checkResellerExist($user) == FALSE)
                {

                    if($this->session->userdata('name') == 'admin' ||  
                        $this->Reseller_model->checkManager_validity() == true)
                    {
                        
                        $result = $this->Reseller_model->addNewReseller($resellerInfo, $user, $master);
                    
                        if($result == True)
                        {
                            $this->session->set_flashdata('success', 'New Reseller created successfully');
                        }
                        else
                        {
                            $this->session->set_flashdata('error', 'Reseller creation failed');
                        }

                    }else{
                        $this->session->set_flashdata('error', 'Reseller cannot be created from a User Manager. Move users from reseller then try again.');
                    }
                
                }else{
                    $this->session->set_flashdata('error', 'Reseller already exists or you are not authorized to create a reseller.');
                }

                redirect('resellerListing');

                
            }
        }
    }

    function resellerEditOld($resellerid = NULL)
    {

        if($this->perm_editmanagers == 0 && $this->session->userdata('login_type') != 'profile')
        {
            $this->session->set_flashdata('error', 'You do not have permission to access this page');
            redirect('dashboard');
        }elseif($this->perm_editmanagers == 1 && $this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(2,3,4))){
            $this->session->set_flashdata('error', 'Profile do not have permission to access this page');
            redirect('dashboard');
        }

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            //$this->load->model('Reseller_model');
            $data['resellers'] = $this->Reseller_model->getResellerList();
            $data['resellerInfo'] = $this->Reseller_model->getResellerInfo($resellerid);
            
            $this->global['pageTitle'] = 'PaceTel : Edit Reseller Info';

            $this->loadViews("resellerEditOld", $this->global, $data, NULL);
        }
    }

    function updateCurrentReseller()
    {

        //echo "sdkjaskdfjkasdjfkasdfkjs"; 

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            $user = $this->input->post('user');
            $status = $this->input->post('status');

            $this->form_validation->set_rules('password','Password','max_length[20]');
            $this->form_validation->set_rules('cpassword','Confirm Password','trim|matches[password]|max_length[20]');
            //$this->form_validation->set_rules('master','Master','required');

            $this->form_validation->set_rules('fname','First Name','trim|required|max_length[20]');
            $this->form_validation->set_rules('lname','Last Name','trim|required|max_length[20]');
            $this->form_validation->set_rules('address','Address','trim|required|max_length[100]');
            $this->form_validation->set_rules('mobile','Mobile Number','required|min_length[12]');
            $this->form_validation->set_rules('email','Email','trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('cnic','CNIC No.','trim|required|max_length[16]');

            if($this->form_validation->run() == FALSE)
            {
                $this->resellerAddNew($user);
            }
            else
            {

                $this->load->model('Reseller_model');
                $this->load->model('users_model');

                //$master = strtolower($this->security->xss_clean($this->input->post('master')));
                $password = $this->input->post('password');

                $fname = ucwords(strtolower($this->security->xss_clean($this->input->post('fname'))));
                $lname = ucwords(strtolower($this->security->xss_clean($this->input->post('lname'))));
                $address = $this->input->post('address');
                $city = ucwords(strtolower($this->security->xss_clean($this->input->post('city'))));
                $mobile = $this->security->xss_clean($this->input->post('mobile'));
                $email = strtolower($this->security->xss_clean($this->input->post('email')));
                $cnic = $this->input->post('cnic');

                if($this->session->userdata ( 'name' ) == 'admin' || $this->ismaster > 0 || $this->perm_editmanagers == 1){
                    $master = strtolower($this->security->xss_clean($this->input->post('master')));
                }else{
                    $master = "NONE";
                }

                // Get permission values from form for update
                $perm_listusers = $this->input->post('perm_listusers') ? 1 : 0;
                $perm_createusers = $this->input->post('perm_createusers') ? 1 : 0;
                $perm_editusers = $this->input->post('perm_editusers') ? 1 : 0;
                $perm_edituserspriv = $this->input->post('perm_edituserspriv') ? 1 : 0;
                $perm_deleteusers = $this->input->post('perm_deleteusers') ? 1 : 0;
                
                $perm_listmanagers = $this->input->post('perm_listmanagers') ? 1 : 0;
                $perm_createmanagers = $this->input->post('perm_createmanagers') ? 1 : 0;
                $perm_editmanagers = $this->input->post('perm_editmanagers') ? 1 : 0;
                $perm_deletemanagers = $this->input->post('perm_deletemanagers') ? 1 : 0;
                
                $perm_listservices = $this->input->post('perm_listservices') ? 1 : 0;
                $perm_createservices = $this->input->post('perm_createservices') ? 1 : 0;
                $perm_editservices = $this->input->post('perm_editservices') ? 1 : 0;
                $perm_deleteservices = $this->input->post('perm_deleteservices') ? 1 : 0;
                
                $perm_addcredits = $this->input->post('perm_addcredits') ? 1 : 0;
                $perm_negbalance = $this->input->post('perm_negbalance') ? 1 : 0;
                $perm_allowdiscount = $this->input->post('perm_allowdiscount') ? 1 : 0;
                $perm_enwriteoff = $this->input->post('perm_enwriteoff') ? 1 : 0;
                
                $perm_listinvoices = $this->input->post('perm_listinvoices') ? 1 : 0;
                $perm_listallinvoices = $this->input->post('perm_listallinvoices') ? 1 : 0;
                $perm_editinvoice = $this->input->post('perm_editinvoice') ? 1 : 0;
                $perm_showinvtotals = $this->input->post('perm_showinvtotals') ? 1 : 0;
                
                $perm_cardsys = $this->input->post('perm_cardsys') ? 1 : 0;
                $perm_accessap = $this->input->post('perm_accessap') ? 1 : 0;
                $perm_trafficreport = $this->input->post('perm_trafficreport') ? 1 : 0;
                $perm_cts = $this->input->post('perm_cts') ? 1 : 0;
                
                $perm_listonlineusers = $this->input->post('perm_listonlineusers') ? 1 : 0;
                $perm_allusers = $this->input->post('perm_allusers') ? 1 : 0;
                $perm_logout = $this->input->post('perm_logout') ? 1 : 0;

                $perm_allowaccountsadd = $this->input->post('perm_allowaccountsadd') ? 1 : 0;
                $perm_allowaccountssync = $this->input->post('perm_allowaccountssync') ? 1 : 0;
                $perm_forceprofile = $this->input->post('perm_forceprofile') ? 1 : 0;
                $perm_macbinding = $this->input->post('perm_macbinding') ? 1 : 0;
                $perm_hostspotrandomcheck = $this->input->post('perm_hostspotrandomcheck') ? 1 : 0;
                $perm_forceactivation = $this->input->post('perm_forceactivation') ? 1 : 0; 
                $perm_allowaccounts = $this->input->post('perm_allowaccounts') ? 1 : 0;
                $perm_fullrefund = $this->input->post('perm_fullrefund') ? 1 : 0;
                $perm_allowdowngrade = $this->input->post('perm_allowdowngrade') ? 1 : 0;
                $perm_networkmanager = $this->input->post('perm_networkmanager') ? 1 : 0;
                $perm_areaaccess = $this->input->post('perm_areaaccess') ? 1 : 0;
                if(empty($password)){              
                    $resellerInfo = array('mastername'=>$master, 'enablemanager'=> 1,
                            'firstname'=>$fname, 'lastname'=>$lname, 'address'=>$address, 'city'=>$city,
                            'mobile'=>$mobile, 'email'=>$email, 'vatid'=>$cnic,
                            'perm_listusers'=>$perm_listusers, 'perm_listservices'=>$perm_listservices, 
                            'perm_listonlineusers'=>$perm_listonlineusers, 'perm_listinvoices'=>$perm_listinvoices,
                            'perm_trafficreport'=>$perm_trafficreport, 'perm_addcredits'=>$perm_addcredits, 
                            'perm_negbalance'=>$perm_negbalance, 'perm_showinvtotals'=>$perm_showinvtotals,
                            'perm_createusers'=>$perm_createusers, 'perm_editusers'=>$perm_editusers, 
                            'perm_edituserspriv'=>$perm_edituserspriv, 'perm_deleteusers'=>$perm_deleteusers,
                            'perm_listmanagers'=>$perm_listmanagers, 'perm_createmanagers'=>$perm_createmanagers, 
                            'perm_editmanagers'=>$perm_editmanagers, 'perm_deletemanagers'=>$perm_deletemanagers,
                            'perm_createservices'=>$perm_createservices, 'perm_editservices'=>$perm_editservices, 
                            'perm_deleteservices'=>$perm_deleteservices, 'perm_listallinvoices'=>$perm_listallinvoices,
                            'perm_cardsys'=>$perm_cardsys, 'perm_editinvoice'=>$perm_editinvoice, 
                            'perm_allusers'=>$perm_allusers, 'perm_allowdiscount'=>$perm_allowdiscount,
                            'perm_enwriteoff'=>$perm_enwriteoff, 'perm_accessap'=>$perm_accessap,
                            'perm_cts'=>$perm_cts, 'perm_logout'=>$perm_logout, 
                            'perm_allowaccountsadd'=>$perm_allowaccountsadd,
                            'perm_allowaccountssync'=>$perm_allowaccountssync,
                            'perm_forceprofile'=>$perm_forceprofile,
                            'perm_macbinding'=>$perm_macbinding,
                            'perm_hostspotrandomcheck'=>$perm_hostspotrandomcheck,
                            'perm_forceactivation'=>$perm_forceactivation,
                            'perm_allowaccounts'=>$perm_allowaccounts,
                            'perm_fullrefund'=>$perm_fullrefund,
                            'perm_allowdowngrade'=>$perm_allowdowngrade,
                            'perm_networkmanager'=>$perm_networkmanager,
                            'perm_areaaccess'=>$perm_areaaccess,
                            'lang'=>'English');

                            log_message('info', $this->session->userdata ( 'name' ). " details: " . json_encode($resellerInfo));
                }
                else
                {
                    $resellerInfo = array('password'=>MD5($password), 'mastername'=>$master, 'enablemanager'=> 1,
                            'firstname'=>$fname, 'lastname'=>$lname, 'address'=>$address, 'city'=>$city,
                            'mobile'=>$mobile, 'email'=>$email, 'vatid'=>$cnic,
                            'perm_listusers'=>$perm_listusers, 'perm_listservices'=>$perm_listservices, 
                            'perm_listonlineusers'=>$perm_listonlineusers, 'perm_listinvoices'=>$perm_listinvoices,
                            'perm_trafficreport'=>$perm_trafficreport, 'perm_addcredits'=>$perm_addcredits, 
                            'perm_negbalance'=>$perm_negbalance, 'perm_showinvtotals'=>$perm_showinvtotals,
                            'perm_createusers'=>$perm_createusers, 'perm_editusers'=>$perm_editusers, 
                            'perm_edituserspriv'=>$perm_edituserspriv, 'perm_deleteusers'=>$perm_deleteusers,
                            'perm_listmanagers'=>$perm_listmanagers, 'perm_createmanagers'=>$perm_createmanagers, 
                            'perm_editmanagers'=>$perm_editmanagers, 'perm_deletemanagers'=>$perm_deletemanagers,
                            'perm_createservices'=>$perm_createservices, 'perm_editservices'=>$perm_editservices, 
                            'perm_deleteservices'=>$perm_deleteservices, 'perm_listallinvoices'=>$perm_listallinvoices,
                            'perm_cardsys'=>$perm_cardsys, 'perm_editinvoice'=>$perm_editinvoice, 
                            'perm_allusers'=>$perm_allusers, 'perm_allowdiscount'=>$perm_allowdiscount,
                            'perm_enwriteoff'=>$perm_enwriteoff, 'perm_accessap'=>$perm_accessap,
                            'perm_cts'=>$perm_cts, 'perm_logout'=>$perm_logout, 
                            'perm_allowaccountsadd'=>$perm_allowaccountsadd,
                            'perm_allowaccountssync'=>$perm_allowaccountssync,
                            'perm_forceprofile'=>$perm_forceprofile,
                            'perm_macbinding'=>$perm_macbinding,
                            'perm_hostspotrandomcheck'=>$perm_hostspotrandomcheck,
                            'perm_forceactivation'=>$perm_forceactivation,
                            'perm_allowaccounts'=>$perm_allowaccounts,
                            'perm_fullrefund'=>$perm_fullrefund,
                            'perm_areaaccess'=>$perm_areaaccess,
                            'lang'=>'English');

                            log_message('info', $this->session->userdata ( 'name' ). " details: " . json_encode($resellerInfo));
                }
                //$radpassword = array('username'=>$user, 'attribute'=>'Cleartext-Password', 'op'=>':=', 'value'=>$password);
                //$radsimuse = array('username'=>$user, 'attribute'=>'Simultaneous-Use', 'op'=>':=', 'value'=>'1');

                //$logInfo = 'user: '.$user.'-srvid: '.$radsrvid->radsrvid.'-createdby: '.$this->session->userdata ( 'name' );
                //log_message('info', 'DB_INFO - Created User '.$logInfo);

                $checkManagerExists = $this->users_model->checkManagerExist($user);

                if($checkManagerExists == 1)
                {
                    
                    $result = $this->Reseller_model->updateCurrentReseller($resellerInfo, $user, $master);
                
                    if($result == True)
                    {
                        $this->session->set_flashdata('success', 'New Reseller created successfully');
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Reseller creation failed');
                    }
                
                }else{
                    $this->session->set_flashdata('error', 'Reseller Not exists....');
                }

                redirect('resellerListing');
                
            }
        }
    }

    function resellernameExists($resellername)
    {
        $this->load->model('users_model');
        $userId = $this->name;
        $return = false;

        if(empty($userId)){
            $result = $this->users_model->checkManagerExist($resellername);
        }

        if(empty($result)){ $return = true; }
        else {
            $this->form_validation->set_message('usernameExists', 'The {field} already taken');
            $return = false;
        }

        return $return;
    }

    /**
     * AJAX endpoint to get online user count for a reseller (managername)
     */
    public function getOnlineCount() {
        $managername = $this->input->post('managername');
        $count = 0;
        $success = false;
        if ($managername) {
            $this->load->model('Reseller_model');
            $count = $this->Reseller_model->getOnlineUserCount($managername);
            $success = true;
        }
        echo json_encode(['success' => $success, 'count' => $count]);
        exit;
    }

    // Profile Management Functions
    
    /**
     * Profile listing
     */
    function profileListing()
    {

        if($this->ismaster == 0 && $this->session->userdata('name') <> 'admin'){
            $this->session->set_flashdata('error', 'You do not have permission to access this page');
            redirect('dashboard');
        }

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $searchText = $this->security->xss_clean($this->input->post('searchText'));
            $data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            
            $count = $this->Reseller_model->profileListingCount($searchText);
            $returns = $this->paginationCompress("profileListing/", $count, 25);
            
            $data['profileListing'] = $this->Reseller_model->profileListing($searchText, $returns["page"], $returns["segment"]);
            
            $this->global['pageTitle'] = 'PaceTel : Profile Management';
            
            $this->loadViews("Profiles/profileList", $this->global, $data, NULL);
        }
    }

    /**
     * Add new profile
     */
    function profileAddNew()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $data['roles'] = $this->Reseller_model->getProfileRoles();
            $data['managers'] = $this->Reseller_model->getResellerList();
            $this->global['pageTitle'] = 'PaceTel : Add New Profile';
            $this->loadViews("Profiles/profileAddNew", $this->global, $data, NULL);
        }
    }

    /**
     * Save new profile
     */
    function saveNewProfile()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('profileid','Profile ID','trim|required|max_length[64]|min_length[3]|alpha_dash|callback_profileidExists');
            $this->form_validation->set_rules('password','Password','required|max_length[64]');
            $this->form_validation->set_rules('cpassword','Confirm Password','trim|required|matches[password]|max_length[64]');
            $this->form_validation->set_rules('name','Full Name','trim|required|max_length[128]');
            $this->form_validation->set_rules('mobile','Mobile Number','trim|required|max_length[20]');
            $this->form_validation->set_rules('roleId','Role','required');
            $this->form_validation->set_rules('managername','Manager','required');

            if($this->form_validation->run() == FALSE)
            {
                $this->profileAddNew();
            }
            else
            {
                $profileid = strtolower($this->security->xss_clean($this->input->post('profileid')));
                $password = $this->input->post('password');
                $name = ucwords(strtolower($this->security->xss_clean($this->input->post('name'))));
                $mobile = $this->security->xss_clean($this->input->post('mobile'));
                $roleId = $this->input->post('roleId');
                $managername = $this->input->post('managername');
                $profileInfo = array('profileid'=>$profileid, 'password'=>MD5($password), 'name'=>$name, 
                                   'mobile'=>$mobile, 'roleId'=>$roleId, 'managername'=>$managername, 'isDeleted'=>0, 
                                   'createdDtm'=>date('Y-m-d H:i:s'));
                $result = $this->Reseller_model->addNewProfile($profileInfo);
                if($result > 0)
                {
                    $this->session->set_flashdata('success', 'New Profile created successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Profile creation failed');
                }
                redirect('Reseller_controller/profileListing');
            }
        }
    }

    /**
     * Edit profile
     */
    function profileEditOld($userId = NULL)
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            if($userId == null)
            {
                redirect('profileListing');
            }
            $data['roles'] = $this->Reseller_model->getProfileRoles();
            $data['managers'] = $this->Reseller_model->getResellerList();
            $data['profileInfo'] = $this->Reseller_model->getProfileInfo($userId);
            $this->global['pageTitle'] = 'PaceTel : Edit Profile';
            $this->loadViews("Profiles/profileEditOld", $this->global, $data, NULL);
        }
    }

    /**
     * Update profile
     */
    function updateProfile()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            $userId = $this->input->post('userId');
            $this->form_validation->set_rules('name','Full Name','trim|required|max_length[128]');
            $this->form_validation->set_rules('mobile','Mobile Number','trim|required|max_length[20]');
            $this->form_validation->set_rules('roleId','Role','required');
            $this->form_validation->set_rules('managername','Manager','required');
            $this->form_validation->set_rules('password','Password','max_length[64]');
            $this->form_validation->set_rules('cpassword','Confirm Password','trim|matches[password]|max_length[64]');
            if($this->form_validation->run() == FALSE)
            {
                $this->profileEditOld($userId);
            }
            else
            {
                $name = ucwords(strtolower($this->security->xss_clean($this->input->post('name'))));
                $mobile = $this->security->xss_clean($this->input->post('mobile'));
                $roleId = $this->input->post('roleId');
                $managername = $this->input->post('managername');
                $password = $this->input->post('password');
                $profileInfo = array();
                if(empty($password))
                {
                    $profileInfo = array('name'=>$name, 'mobile'=>$mobile, 'roleId'=>$roleId, 'managername'=>$managername,
                                       'updatedBy'=>$this->vendorId, 'updatedDtm'=>date('Y-m-d H:i:s'));
                }
                else
                {
                    $profileInfo = array('name'=>$name, 'mobile'=>$mobile, 'roleId'=>$roleId, 'managername'=>$managername, 'password'=>MD5($password),
                                       'updatedBy'=>$this->vendorId, 'updatedDtm'=>date('Y-m-d H:i:s'));
                }
                $result = $this->Reseller_model->updateProfile($profileInfo, $userId);
                if($result == true)
                {
                    $this->session->set_flashdata('success', 'Profile updated successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Profile updation failed');
                }
                redirect('Reseller_controller/profileListing');
            }
        }
    }

    /**
     * Delete profile
     */
    function deleteProfile()
    {
        if($this->isAdmin() == FALSE)
        {
            echo(json_encode(array('status'=>'access')));
        }
        else
        {
            $userId = $this->input->post('userId');
            $profileInfo = array('isDeleted'=>1, 'updatedBy'=>$this->vendorId, 'updatedDtm'=>date('Y-m-d H:i:s'));
            
            $result = $this->Reseller_model->deleteProfile($userId, $profileInfo);
            
            if ($result > 0) { echo(json_encode(array('status'=>TRUE))); }
            else { echo(json_encode(array('status'=>FALSE))); }
        }
    }

    /**
     * Check if profileid exists
     */
    function profileidExists($profileid)
    {
        $profileid = $this->input->post('profileid');
        $userId = $this->input->post('userId');
        
        if(empty($userId)){
            $result = $this->Reseller_model->checkProfileExist($profileid);
        } else {
            $result = $this->Reseller_model->checkProfileExist($profileid, $userId);
        }

        if(empty($result)){ return true; }
        else {
            $this->form_validation->set_message('profileidExists', 'The {field} already taken');
            return false;
        }
    }
}

?>
<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';
require APPPATH . 'libraries/routeros_api.class.php';

class Userslist extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('users_model');
        $this->load->model('User_model');
        $this->load->model('services_model');
        $this->load->model('invoices_model');
        $this->load->model('Other_model');
        $this->load->model('Network_model');
        $this->load->helper(array('form', 'url'));
        $this->isLoggedIn();   
    }

    public function index()
    {
        $this->global['pageTitle'] = 'Users List : Dashboard';
        
        $this->loadViews("dashboard", $this->global, NULL , NULL);
    }

    public function dashboardnetwork()
    {
        $this->global['pageTitle'] = 'Network : Dashboard';
        
        $this->loadViews("dashboardnetwork", $this->global, NULL , NULL);
    }
    function userListing($accType = 0)
    {

        if($this->perm_listusers == 0){
            $this->session->set_flashdata('error', 'You are not allowed to view users. Contact Admin to allow permission');
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
            $type = $this->security->xss_clean($this->input->post('searchText1'));
            
            $this->load->library('pagination');
            
            $count = $this->users_model->userListingCount($searchText, $type, $accType);

			$returns = $this->paginationCompress ( "usersListing/".$accType, $count, 20, 3 );
            
            $data['userRecords'] = $this->users_model->userListing($searchText, $type, $accType, $returns["page"], $returns["segment"]);

            $data['type'] = $type;
            $data['accType'] = $accType;

            $this->global['pageTitle'] = 'Pace-Tel : User Listing';
            
            $this->loadViews("userslist", $this->global, $data, NULL);
        }
    }

    function usersAddNew()
    {
        if($this->perm_createusers == 0 && $this->perm_createmanagers == 1){
            $this->session->set_flashdata('error', 'You are not allowed to create users. Contact Admin to allow permission');
            redirect('usersListing');
        }

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {

            $data['packages'] = $this->users_model->getResellerPackages();

            if($this->perm_areaaccess == 0){
                $data['regions'] = $this->Network_model->segmentList(2, 0, $this->session->userdata('name'));
                $data['parameters'] = $this->Network_model->segmentListdetails();
            }else{
                $data['regions'] = $this->Network_model->segmentList(1, 1);
                $data['parameters'] = $this->Network_model->segmentListdetails();
            }
            
            $this->global['pageTitle'] = 'PaceTel : Add New User';

            $this->loadViews("usersAddNew", $this->global, $data, NULL);
        }
    }


    function saveNewUser()
    {

        //echo "sdkjaskdfjkasdjfkasdfkjs"; 

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('user','User Name','trim|required|max_length[50]|min_length[4]|callback_usernameExists');
            $this->form_validation->set_rules('password','Password','required|max_length[20]');
            $this->form_validation->set_rules('cpassword','Confirm Password','trim|required|matches[password]|max_length[20]');
            $this->form_validation->set_rules('service','Service','required');

            $this->form_validation->set_rules('fname','First Name','trim|required|max_length[20]');
            $this->form_validation->set_rules('lname','Last Name','trim|required|max_length[20]');
            $this->form_validation->set_rules('address','Address','trim|required|max_length[100]');
            $this->form_validation->set_rules('mobile','Mobile Number','required|min_length[12]|max_length[12]');
            $this->form_validation->set_rules('email','Email','trim|valid_email|max_length[128]');
            $this->form_validation->set_rules('cnic','CNIC No.','trim|required|max_length[16]');

            if($this->form_validation->run() == FALSE)
            {
                $this->usersAddNew();
            }
            else
            {

                $this->load->model('Services_model');
                $this->load->model('users_model');

                $user = str_replace(' ', '', strtolower($this->security->xss_clean($this->input->post('user'))));
                $password = $this->input->post('password');

                $fname = ucwords(strtolower($this->security->xss_clean($this->input->post('fname'))));
                $lname = ucwords(strtolower($this->security->xss_clean($this->input->post('lname'))));
                $address = $this->input->post('address');
                $mobile = $this->security->xss_clean($this->input->post('mobile'));
                $email = strtolower($this->security->xss_clean($this->input->post('email')));
                //$mobile = $this->security->xss_clean($this->input->post('cnic'));
                $cnic = $this->input->post('cnic');
                $region = $this->input->post('region');
                $parameter = $this->input->post('parameter');
                $srvid = $this->input->post('service');
                $radsrvid = $this->Services_model->getServiceInfo($srvid);

                $radServiceInfo = $this->Services_model->spGetServiceInfo($radsrvid->radsrvid);

                //   Need to map Reseller Packages with DMA Packages ******
                //************************************************************
                //************************************************************
                //************************************************************
                //************************************************************

                if($srvid <> 0 && !empty($radsrvid->radsrvid)){
              
                    $userInfo = array('username'=>$user, 'password'=>MD5($password), 'groupid'=>1, 'enableuser'=> 1,
                                        'uplimit'=>0, 'downlimit'=>0, 'comblimit'=>0,
                                        'firstname'=>$fname, 'lastname'=>$lname, 'address'=>$address,
                                        'mobile'=>$mobile, 'email'=>$email, 'taxid'=>$cnic,
                                        'gpslat'=>0.00000000000000, 'gpslong'=>0.00000000000000,
                                        'usemacauth'=>0, 'expiration'=>date('Y-m-d'), 'uptimelimit'=>0, 'srvid'=>$radsrvid->radsrvid, 
                                        'ipmodecm'=>0, 'ipmodecpe'=>0, 'poolidcm'=>0, 'poolidcpe'=>0,
                                        'createdon'=>date('Y-m-d'), 'acctype'=>0, 'credits'=>0.00, 'cardfails'=>0,
                                        'createdby'=>$managername = $this->session->userdata ( 'name' ),
                                        'owner'=>$managername = $this->session->userdata ( 'name' ),
                                        'warningsent'=>0, 'verified'=>0, 'selfreg'=>0, 'verifyfails'=>0, 'verifysentnum'=>0,
                                        'contractvalid'=>'0000-00-00', 'pswactsmsnum'=>0, 'alertemail'=>0, 'alertsms'=>0,
                                        'custattr'=>'Mikrotik-Address-List := '.$this->session->userdata ( 'name' ).','.'Mikrotik-Address-List := '.$radServiceInfo->srvname,
                                        'lang'=>'English');

                    $radpassword = array('username'=>$user, 'attribute'=>'Cleartext-Password', 'op'=>':=', 'value'=>$password);
                    $radsimuse = array('username'=>$user, 'attribute'=>'Simultaneous-Use', 'op'=>':=', 'value'=>'1');

                    $userDocsInfo = array('username'=>$user, 'segmentid'=>$region, 'parameter'=>$parameter);

                    $logInfo = 'user: '.$user.'-srvid: '.$radsrvid->radsrvid.'-createdby: '.$this->session->userdata ( 'name' );
                    log_message('info', 'DB_INFO - Created User '.$logInfo);


                    if($this->users_model->checkUserExist($user) == FALSE)
                    {
                        
                        $result = $this->users_model->addNewUser($userInfo, $radpassword, $radsimuse);
                        log_message('info', 'rm_users: User Created ' . json_encode($userInfo). ' results status '. $result);

                        $result = $this->users_model->updateDocsInfo($userDocsInfo, $user);
                        log_message('info', 'tbl_userdocs: User Created ' . json_encode($userDocsInfo). ' results status '. $result);
                    
                        // Add or update radusergroup for this user
                        $rm_services = $this->Services_model->getServiceProfileInfo($radsrvid->radsrvid);
                        $srvname = $radsrvid->srvname; // Make sure this is the correct groupname
                        $this->Services_model->manageUserRadusergroup($rm_services->srvname, $user, 1);

                        if($result == True)
                        {
                            $this->session->set_flashdata('success', 'New User created successfully');
                            redirect('usersListing');
                        }
                        else
                        {
                            $this->session->set_flashdata('error', 'User creation failed');
                            redirect('usersListing');
                        }
                    
                    }else{
                        $this->session->set_flashdata('error', 'User already exists....');
                        $this->usersAddNew();
                    }

                }else{

                    $this->session->set_flashdata('error', 'Service not valid, empty or not Allowed');
                    $this->usersAddNew();

                }


                
            }
        }
    }

    function editOld($userId = NULL)
    {
        if($this->perm_editusers == 0 && $this->perm_createmanagers == 1){
            $this->session->set_flashdata('error', 'You are not allowed to edit users. Contact Admin to allow permission');
            redirect('usersListing');
        }

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            if($userId == null)
            {
                redirect('usersListing');
            }
            
            $data['packages'] = $this->users_model->getResellerPackages();
            $data['userInfo'] = $this->users_model->getUserInfo($userId);
            $data['userDocsInfo'] = $this->users_model->getUserDocsInfo($userId);
            $data['managers'] = $this->users_model->getManagersList();

            if($this->perm_areaaccess == 0){
                $data['regions'] = $this->Network_model->segmentList(2, 0, $this->session->userdata('name'));
                $data['parameters'] = $this->Network_model->segmentListdetails();
            }else{
                $data['regions'] = $this->Network_model->segmentList(1, 1);
                $data['parameters'] = $this->Network_model->segmentListdetails();
            }
            
            $this->global['pageTitle'] = 'PaceTel : Edit User';
            
            $this->loadViews("usersEditOld", $this->global, $data, NULL);
        }
    }

    function editUser()
    {

        if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(3, 4))){
            $this->session->set_flashdata('error', 'You are now allowed to edit this user');
            $this->loadViews("dashboard", $this->global, NULL , NULL);
            return;
        }

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {

            $this->load->library('form_validation');
            $user = $this->input->post('user');
            $status = $this->input->post('status');

            //$this->form_validation->set_rules('user','User Name','trim|required|max_length[50]|callback_usernameExists');
            $this->form_validation->set_rules('password','Password','matches[cpassword]|max_length[20]');
            $this->form_validation->set_rules('cpassword','Confirm Password','matches[password]|max_length[20]');

            $this->form_validation->set_rules('manager','Manager Name','required');

            $this->form_validation->set_rules('fname','First Name','trim|required|max_length[20]');
            $this->form_validation->set_rules('lname','Last Name','trim|required|max_length[20]');
            $this->form_validation->set_rules('address','Address','trim|required|max_length[100]');
            $this->form_validation->set_rules('mobile','Mobile Number','required|min_length[12]');
            $this->form_validation->set_rules('email','Email','trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('cnic','First Name','trim|required|max_length[16]');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->editOld($user);
            }
            else
            {
                
                //$user = $this->security->xss_clean($this->input->post('user'));
                $password = $this->input->post('password');

                $fname = ucwords(strtolower($this->security->xss_clean($this->input->post('fname'))));
                $lname = ucwords(strtolower($this->security->xss_clean($this->input->post('lname'))));
                $address = $this->input->post('address');
                $mobile = $this->security->xss_clean($this->input->post('mobile'));
                $email = strtolower($this->security->xss_clean($this->input->post('email')));
                //$mobile = $this->security->xss_clean($this->input->post('cnic'));
                $owner = $this->security->xss_clean($this->input->post('manager'));

                $cnic = $this->input->post('cnic');
                $srvid = $this->input->post('service');

                $region = $this->input->post('region');
                $parameter = $this->input->post('parameter');

                $newServiceID = $this->users_model->getUserNewServiceID($owner, $user);

                if(!$newServiceID){
                    $this->session->set_flashdata('error', 'Destination portal not exists package assigned to user');
                    redirect('usersListing');
                    return false;
                }

                if(empty($password))
                {
                    //$userInfo = array('email'=>$email, 'roleId'=>$roleId, 'name'=>$name,
                    //                'mobile'=>$mobile, 'updatedBy'=>$this->vendorId, 'updatedDtm'=>date('Y-m-d H:i:s'));
                    $userInfo = array('groupid'=>1, 'enableuser'=> $status, 'owner'=>$owner,
                        'firstname'=>$fname, 'lastname'=>$lname, 'address'=>$address,
                        'mobile'=>$mobile, 'email'=>$email, 'taxid'=>$cnic,
                        'gpslat'=>0.00000000000000, 'gpslong'=>0.00000000000000,
                        'usemacauth'=>0, 'uptimelimit'=>0,
                        'custattr'=>'Mikrotik-Address-List := '.$owner,
                        'lang'=>'English');
                }
                else
                {
                    //$userInfo = array('email'=>$email, 'password'=>getHashedPassword($password), 'roleId'=>$roleId,
                    //    'name'=>ucwords($name), 'mobile'=>$mobile, 'updatedBy'=>$this->vendorId, 
                    //    'updatedDtm'=>date('Y-m-d H:i:s'));

                    $userInfo = array('password'=>MD5($password), 'groupid'=>1, 'enableuser'=> $status,
                        'firstname'=>$fname, 'lastname'=>$lname, 'address'=>$address, 'owner'=>$owner,
                        'mobile'=>$mobile, 'email'=>$email, 'taxid'=>$cnic,
                        'gpslat'=>0.00000000000000, 'gpslong'=>0.00000000000000,
                        'usemacauth'=>0, 'uptimelimit'=>0,
                        'custattr'=>'Mikrotik-Address-List := '.$owner,
                        'lang'=>'English');
    
                    $radpassword = array('username'=>$user, 'attribute'=>'Cleartext-Password', 'op'=>':=', 'value'=>$password);
                    $radsimuse = array('username'=>$user, 'attribute'=>'Simultaneous-Use', 'op'=>':=', 'value'=>'1');

                    $radcheckResult = $this->users_model->editUserPasswordRadCheck($radpassword, $user);

                }

                $userDocsInfo = array('username'=>$user, 'segmentid'=>$region, 'parameter'=>$parameter);

                $result = $this->users_model->editUser($userInfo, $user);

                $resultDocs = $this->users_model->updateDocsInfo($userDocsInfo, $user);

                $this->load->model('Reports_model');
                $connectedUser = $this->Reports_model->disconnectUser($user);
                $command = "echo User-Name=".$connectedUser->username.",Framed-IP=".$connectedUser->framedipaddress.
                            " | radclient -x ".$connectedUser->nasipaddress.":3799 disconnect ".$connectedUser->secret;

                $logInfo = 'user:'.$user.'-srvid:'.$srvid.'-editedby:'.$this->session->userdata ( 'name' );
                log_message('info', 'DB_INFO - Edited '.$logInfo);
            
                if($status == 0){
                    exec($command);
                }
                
            
                if($result == true)
                {
                    $this->session->set_flashdata('success', 'User updated successfully ');
                }
                else
                {
                    $this->session->set_flashdata('error', 'User updation failed');
                }
                
                redirect('usersListing');
                
            }
        }
    }

    function usernameExists($username)
    {
        $this->db = $this->load->database('default', TRUE);
        $userId = $this->vendorId;
        $return = false;

        if(empty($userId)){
            $result = $this->users_model->checkUsernameExists($username);
        }

        if(empty($result)){
            $return = true; 
        }
        else {
            $this->form_validation->set_message('usernameExists', 'The {field} already taken');
            $return = false;
        }

        return $return;
    }

    function userDashBoard($userId = NULL)
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            if($userId == null)
            {
                redirect('usersListing');
            }

            $this->load->model('invoices_model');
            $this->load->model('Reports_model');

            $managername = $this->session->userdata ( 'name' );

            $data['radInfo'] = $this->users_model->getRadGetPassword($userId);
            $userInfo = $this->users_model->getUserInfo($userId);
            $data['userInfo'] = $userInfo;
            $data['packageInfo'] = $this->invoices_model->getUserPackage($userId, $userInfo->owner);
            $data['rechargeInfo'] = $this->invoices_model->getRechargeInfo($userId);
            $data['username'] = $userId;

            $data['connectionInfo'] = $this->User_model->dashboard_connectionInfo($userId);

            $data['cardInfo'] = $this->Other_model->get_cardInfo($userId);

            $data['perm_macbinding'] = $this->perm_macbinding;

            //if($this->users_model->checkDocumentsExists($userId) == FALSE)
            //    $data['userDocsInfo'] = $this->users_model->getUserDocsInfo($userId);
            
            $this->global['pageTitle'] = 'PaceTel : User Dashboard';
            
            $this->loadViews("dashboardCust", $this->global, $data, NULL);
        }
    }

    function userLiveTraffic($userId = NULL)
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            if($userId == null)
            {
                redirect('usersListing');
            }

            $data['packages'] = $this->users_model->getResellerPackages();
            $data['userInfo'] = $this->users_model->getUserInfo($userId);
            
            $this->global['pageTitle'] = 'PaceTel : Live Traffic Monitor';
            
            $this->loadViews("userMonitor", $this->global, $data, NULL);
        }
    }


    public function storeImages()
    {
        // Create documents directory if it doesn't exist
        $upload_path = FCPATH . 'uploads/documents/';
        if (!is_dir($upload_path)) {
            mkdir($upload_path, 0755, true);
        }

        $config['upload_path']   = $upload_path;
        $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf'; 
        $config['max_size']      = 2048; // 2MB in kilobytes
        $config['max_width']     = 0; 
        $config['max_height']    = 0;
        $config['encrypt_name']  = TRUE; // For security
        $this->load->library('upload', $config);

        $fullPathImage1 = '';
        $fullPathImage2 = '';
        $upload_error = false;
        $error_message = '';

        // CNIC Image 1 Upload
        if (!empty($_FILES['cnic_image1']['name'])) {
            if (!$this->upload->do_upload('cnic_image1')) {
                $error = $this->upload->display_errors();
                $upload_error = true;
                $error_message .= "Front image: " . $error . " ";
            } else {
                $data = array('upload_data1' => $this->upload->data());
                $fullPathImage1 = 'documents/' . $data['upload_data1']['file_name'];
            }
        }

        // CNIC Image 2 Upload
        if (!empty($_FILES['cnic_image2']['name'])) {
            if (!$this->upload->do_upload('cnic_image2')) {
                $error = $this->upload->display_errors();
                $upload_error = true;
                $error_message .= "Back image: " . $error;
            } else {
                $data = array('upload_data2' => $this->upload->data());
                $fullPathImage2 = 'documents/' . $data['upload_data2']['file_name'];
            }
        }

        $user = $this->input->post('user');

        // Only update if at least one file was uploaded successfully
        if (!empty($fullPathImage1) || !empty($fullPathImage2)) {
            // Update user verification status if front image was uploaded
            if (!empty($fullPathImage1)) {
                $usersInfo = array('comment' => $fullPathImage1, 'verified' => 1);
                $this->users_model->updateVerifyUser($usersInfo, $user);
            }

            // Store image paths in database
            $userInfoImage = array(
                'username' => $user,
                'cnic_file1' => !empty($fullPathImage1) ? $fullPathImage1 : null,
                'cnic_file2' => !empty($fullPathImage2) ? $fullPathImage2 : null,
                'createdby' => $this->session->userdata('name')
            );
            
            $result = $this->users_model->store_images($userInfoImage, $user);

            if ($result == true) {
                if ($upload_error) {
                    $this->session->set_flashdata('warning', 'Some files uploaded with errors: ' . $error_message);
                } else {
                    $this->session->set_flashdata('success', 'Files uploaded successfully');
                }
            } else {
                $this->session->set_flashdata('error', 'Files upload failed');
            }
        } else if ($upload_error) {
            $this->session->set_flashdata('error', 'Files upload failed: ' . $error_message);
        } else {
            $this->session->set_flashdata('info', 'No files were selected for upload');
        }
        
        // Redirect back to the document upload page
        redirect('userslist/docsUpload/0/' . $user);
    }


    function docsUpload($accType = 0, $userId = NULL)
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            if($userId == null)
            {
                redirect('usersListing');
            }
            
            //$data['packages'] = $this->users_model->getPackages();
            $managername = $this->session->userdata ( 'name' );
            $data['managerInfo'] = $this->users_model->getManagerInfo($managername);
            $data['userInfo'] = $this->users_model->getUserInfo($userId, $accType);
            $data['userDocsInfo'] = $this->users_model->getUserDocsInfo($userId);



            // User Stock Issue List

            $this->load->model('users_model');
            $this->load->model('Accounts/Jvs_model');
            $userInfo = $this->users_model->getUserInfo($userId);
            
            $data['stockList'] = $this->Jvs_model->getJvsListByUser($userInfo->username, "STOCK");
            $data['jvsList'] = $this->Jvs_model->getJvsListByUser($userInfo->username, "ACCOUNTS");

            $data['activationList'] = $this->invoices_model->activationTicket_list_by_username($userInfo->username, "STOCK");
            
            $this->global['pageTitle'] = 'Upload : User Docs';
            
            $this->loadViews("docsUpload", $this->global, $data, NULL);
        }
    }

    function updateDocsInfo()
    {

            $this->load->library('form_validation');
            $user = $this->input->post('user');

            $this->form_validation->set_rules('payname','Full Name','trim|required|max_length[20]');
            //$this->form_validation->set_rules('discount','Discount','numeric');
            //$this->form_validation->set_rules('adjamount','Adjustment','numeric');

            if($this->form_validation->run() == FALSE)
            {
                $this->docsUpload($user);
            }
            else
            {
                $payname = ucwords(strtolower($this->security->xss_clean($this->input->post('payname'))));
                $discount = $this->input->post('discount');
                $adjamount = $this->input->post('adjamount');

                $userInfo = array('username'=>$user,'payname'=>$payname, 'discount'=>$discount, 'adjamount'=>$adjamount);

                $result = $this->users_model->updateDocsInfo($userInfo, $user);

                if($result == true)
                {
                    $this->session->set_flashdata('success', 'User settings updated successfully '.$message);
                }
                else
                {
                    $this->session->set_flashdata('error', 'User settings updation failed');
                }
                
                redirect('docsUpload/'.$user);

            }

    }

    // Update Installation Charges
    function updateInstallationInfo()
    {

            $this->load->library('form_validation');
            $user = $this->input->post('user');

            $this->form_validation->set_rules('inst_name','Installation Name','trim|required|max_length[255]');
            //$this->form_validation->set_rules('discount','Discount','numeric');
            //$this->form_validation->set_rules('adjamount','Adjustment','numeric');

            if($this->form_validation->run() == FALSE)
            {
                $this->docsUpload($user);
            }
            else
            {
                $user = $this->input->post('user');
                $inst_name = ucwords(strtolower($this->security->xss_clean($this->input->post('inst_name'))));
                $inst_box = ucwords(strtolower($this->security->xss_clean($this->input->post('inst_box'))));
                $inst_wifi = ucwords(strtolower($this->security->xss_clean($this->input->post('inst_wifi'))));
                $inst_fiber = ucwords(strtolower($this->security->xss_clean($this->input->post('inst_fiber'))));
                $inst_meter = $this->input->post('inst_meter');
                $inst_chrg = $this->input->post('inst_chrg');
                $inst_cost = $this->input->post('inst_cost');
                $inst_disc = $this->input->post('inst_disc');

                $userInfo = array('inst_name'=>$inst_name, 'inst_box'=>$inst_box, 'inst_wifi'=>$inst_wifi, 
                                'inst_fiber'=>$inst_fiber, 'inst_meter'=>$inst_meter, 'inst_chrg'=>$inst_chrg, 'inst_cost'=>$inst_cost, 
                                'inst_disc'=>$inst_disc,);

                $validate_user = $this->users_model->getUserDocsInfo($user);
                if($validate_user > 0){
                    $result = $this->users_model->updateDocsInfo($userInfo, $user);
                    if($result == true)
                    {
                        $this->session->set_flashdata('success', 'User Installation Charges Updated.... '.$message);
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'User Installation info update failed');
                    }
                    redirect('docsUpload/'.$user);
                }else{
                    $this->session->set_flashdata('error', 'Generate payment ID First......');
                    redirect('docsUpload/'.$user);
                }

            }

    }

    function manageUser($userId = NULL)
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            if($userId == null)
            {
                redirect('usersListing');
            }
            
            $data['packages'] = $this->users_model->getResellerPackages();
            $data['userInfo'] = $this->users_model->getUserInfo($userId);
            
            $this->global['pageTitle'] = 'PaceTel : Edit User';
            
            $this->loadViews("usersEditOld", $this->global, $data, NULL);
        }
    }

    function export_tocsv($status = 0, $expiry = 0)
    {

        $file_name = 'users_details_on_'.date('Ymd').'.csv'; 
        header("Content-Description: File Transfer"); 
        header("Content-Disposition: attachment; filename=$file_name"); 
        header("Content-Type: application/csv;");

        // get data 
        $user_data = $this->users_model->export_tocsv($status, $expiry);
   
        // file creation 
        $file = fopen('php://output', 'w');
    
        $header = array("username", "payid", "package", "firstname", "lastname", "address", "city", "mobile", "expiration", "created on", "owner", "costprice", "saleprice"); 
        fputcsv($file, $header);
        foreach ($user_data->result_array() as $key => $value)
        { 
          fputcsv($file, $value); 
        }
        fclose($file); 
        exit; 
    }

    // ***** Quick User Action ****** //
    function userQuickEdit($accType = 0, $searchText1 = NULL, $pageLimit = NULL, $searchText = NULL)
    {
        if($this->perm_editusers == 0 && $this->perm_createmanagers == 1){
            $this->session->set_flashdata('error', 'You are not allowed to recharge users. Contact Admin to allow permission');
            redirect('usersListing');
        }

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            //echo "Search Text1: ".$searchText1."- PageLimit: ".$pageLimit." - AccountType: ".$accType." - SearchText: ".$searchText." ";

            $searchText = $this->security->xss_clean($this->input->post('searchText'));
            $data['searchText'] = $searchText;

            if($searchText1 == NULL){
                $type = $this->security->xss_clean($this->input->post('searchText1'));
                
            }else{
                $type = $searchText1;
            }

            if($pageLimit == NULL){
                $pageLimit = 50;
            }

            $this->load->library('pagination');
            
            $count = $this->users_model->userListingCount($searchText, $type, $accType);

			//$returns = $this->paginationCompress ( "usersMultiEdit/", $count, 50 );
            $returns = $this->paginationCompress ( "userQuickEdit/", $count, $pageLimit, 50 );
            
            $data['userRecords'] = $this->users_model->userListing($searchText, $type, $accType, $returns["page"], $returns["segment"]);

            //print_r($data['userRecords']);
            $data['type'] = $type;

            $this->global['pageTitle'] = 'Pace-Tel : User Listing';
            
            $this->loadViews("usersMultiEdit", $this->global, $data, NULL);
        }
    }

    function userQuickBlock($userId = NULL, $enableuser = NULL)
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {

            if($userId == null)
            {
                redirect('userQuickEdit');
            }

            $this->load->model('login_model');
            $userDetails = $this->users_model->getUserInfo($userId);
            $managername = $this->session->userdata ( 'name' );
            
            if($managername <> 'admin'){
                $managerSettings = $this->login_model->getSettingInfo($managername, 'ALLOW-SMS');
                if(!empty($managerSettings)){
                   $allowManager = $managerSettings->status;
                }else{
                   $allowManager = 0;
                }
            }else{
                $allowManager = 1;
            }
           
            if($enableuser == 0)
            {   
                $userInfo = array('enableuser'=> 0);
                $result = $this->users_model->editUser($userInfo, $userId);

                // ***** Disconnect User ****** //
                $this->load->model('Reports_model');
                $connectedUser = $this->Reports_model->disconnectUser($userId);
                if(!empty($connectedUser)){
                    $command = "echo User-Name=".$connectedUser->username.",Framed-IP=".$connectedUser->framedipaddress.
                                " | radclient -r 1 ".$connectedUser->nasipaddress.":3799 disconnect ".$connectedUser->secret;

                    exec($command);
                }

                // **** Send SMS to User ****** // 
                $messagetext = "Dear ".$userDetails->firstname." ".$userDetails->lastname.", your account blocked due to non payment. Kindly contact our local team for further details";
                //$this->session->set_flashdata('success', $userId.'Status '.$enableuser.' disabled successfully');
            }else{

                $userInfo = array('enableuser'=> 1);
                $result = $this->users_model->editUser($userInfo, $userId);
                $messagetext = "Dear ".$userDetails->firstname." ".$userDetails->lastname.", your account is active now. If you are still facing any difficulty kindly contact our local team.";

            }

            if($allowManager == 1){
                $this->sendsmsgateway($userDetails->mobile, $messagetext, $errorCode);
            }

            echo(json_encode(array('status'=>TRUE)));
        }
    }

    function userTrafficWatch($username)
    {

        $this->load->model('Reports_model');

        $isUserOnline = $this->Reports_model->checkUserOnlineStatus($username);
        //$isUserOnline = TRUE;

        if($isUserOnline == TRUE)
        {

            $connectedUser = $this->Reports_model->disconnectUser($username);
            //$managername = $this->session->userdata('name');
            //$service_id = $this->input->post('service_id');
            //$exp_date = $this->input->post('exp_date');
            //$srv_date = $this->input->post('srv_date');

            //echo $connectedUser->nasipaddress;

            $connection = ssh2_connect($connectedUser->nasipaddress, 9322);

            if(!$connection == false){

                ssh2_auth_password($connection, 'sshroot', 'sshpassword@007');

                //$stream = ssh2_exec($connection, 'ssh admin@203.135.57.66 /queue simple print oid where name=<pppoe-u2nasir3>');
                $stream = ssh2_exec($connection, '/queue simple print oid where name=<pppoe-'.trim($username).'>');
                stream_set_blocking($stream, true);
                $stream_out = ssh2_fetch_stream($stream, SSH2_STREAM_STDIO);
                $myfile = substr(stream_get_contents($stream_out), 53);

                unset($connection);

                //echo "**********************";
                //echo $myfile;

                $oid_array = preg_split("/[\s=]+/", $myfile);
                //print_r($oid_array);

                $oid_bytes_in = trim(rtrim($oid_array[4]));
                $oid_bytes_out = trim(rtrim($oid_array[6]));

                $key_in = substr($oid_bytes_in, 12);
                $key_out = substr($oid_bytes_out, 12);

                $data_in_1 = snmprealwalk($connectedUser->nasipaddress, "pace", $oid_bytes_in);
                $data_out_1 = snmprealwalk($connectedUser->nasipaddress, "pace", $oid_bytes_out);

                //echo "  OID Bytes IN = ".$oid_bytes_in."  END ";
                //sleep(5);

                $value1_in = preg_split("/[-\s:]/", $data_in_1["SNMPv2-SMI::enterprises" . $key_in]);
                $value1_out = preg_split("/[-\s:]/", $data_out_1["SNMPv2-SMI::enterprises" . $key_out]);
                //print_r($value1_in);
                //echo $value1_in[2]." || ".$value1_out[2];

                //$dwnLimit1 = $value1_in[2];
                //$upLimit1 = $value1_out[2];

                //echo " Prev DWN =".$_SESSION['user_dwn_prv']." ValueIN ".$value1_in[2]." Value Out ".$value1_out[2]." ||  ";


                $dwnLimit = round(((((($value1_in[2] - $_SESSION['user_dwn_prv']) * 8) / 1024) / 1024) / 1), 2);
                $upLimit = round(((((($value1_out[2] - $_SESSION['user_up_prv']) * 8) / 1024) / 1024) / 1), 2);

                //echo $_SESSION['user_dwn_prv']." /////// ".$dwnLimit." ------  ";

                $_SESSION['user_dwn_prv'] = $value1_in[2];
                $_SESSION['user_up_prv'] = $value1_out[2];

                $_SESSION['dwn_count'] = $_SESSION['dwn_count'] + 1;

                // **** Session Download ******

                if ($_SESSION['dwn_count_label'] == "") {
                    $_SESSION['dwn_count_label'] = $_SESSION['dwn_count'];
                } else {
                    $_SESSION['dwn_count_label'] = $_SESSION['dwn_count_label'] . ',' . $_SESSION['dwn_count'];
                }

                if ($_SESSION['user_dwn'] == "") {
                    $_SESSION['user_dwn'] = 50;
                } else {
                    $_SESSION['user_dwn'] = $_SESSION['user_dwn'] . ',' . $dwnLimit;
                }

                if ($_SESSION['user_up'] == "") {
                    $_SESSION['user_up'] = 50;
                } else {
                    $_SESSION['user_up'] = $_SESSION['user_up'] . ',' . $upLimit;
                }

                //echo $_SESSION['user_dwn'];
                //echo $_SESSION['user_up'];

                $info = array(['dwnLimit' => $dwnLimit, 'upLimit' => $upLimit]);
                echo json_encode($info);
            }
            else{
                exit;
            }

        }else{
            $this->session->set_flashdata('error', 'User is Offline...');
        }
    }


    // Function Get Live Traffic from Mikrotik 
    function trafficInterfaceMikrotik($username)
    {

        $this->load->model('Reports_model');

        $isUserOnline = $this->Reports_model->checkUserOnlineStatus($username);
        //$isUserOnline = TRUE;

    
        echo "SERVER IP ADDRESS: ".$connectedUser->nasipaddress;
        exit;

        if($isUserOnline == TRUE)
        {

            $connectedUser = $this->Reports_model->disconnectUser($username);

            $API = new RouterosAPI();

            $API->debug = true;
            
            if ($API->connect($connectedUser->nasipaddress, 'admin', 'Khyber@007')) {
                        
                $API->write('/interface/monitor-traffic',false);
                $API->write('=interface=<pppoe-'.trim($username).'>',false);
                $API->write('=once=');
            
            $READ = $API->read(false);
            $ARRAY = $API->parseResponse($READ);
            print_r($ARRAY);

            //$API->comm("/ip/firewall/mangle/add", array("chain" => "prerouting", "action" => "mark-routing", "new-routing-mark" => "to_WAN3"));

            $API->disconnect();
            }
        }
    }


    // Users Group Management
    function usersGroup()
    {
        if($this->perm_listusers == 0){
            $this->session->set_flashdata('error', 'You are not allowed to view user groups. Contact Admin to allow permission');
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

            $data['searchText'] = $searchText;

            //$data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            
            $count = $this->users_model->usersGroupCount($searchText, $managerAllServices);

            $returns = $this->paginationCompress ( "usersGroup/", $count, 100 );
            
            $data['groupListing'] = $this->users_model->usersGroupListing($searchText, $returns["page"], $returns["segment"], $managerAllServices);

            $this->global['pageTitle'] = 'Pace-Tel : Group Listing';
            
            $this->loadViews("usersGroup", $this->global, $data, NULL);
        }
    }

    // Managers Groups Management //

    function managerGroup()
    {
        if($this->perm_listmanagers == 0){
            $this->session->set_flashdata('error', 'You are not allowed to view user groups. Contact Admin to allow permission');
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

            $data['searchText'] = $searchText;

            //$data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            
            $count = $this->users_model->managerGroupCount($searchText, $managerAllServices);

            $returns = $this->paginationCompress ( "managerGroup/", $count, 100 );
            
            $data['groupListing'] = $this->users_model->managerGroupListing($searchText, $returns["page"], $returns["segment"], $managerAllServices);

            //print_r($data['groupListing']);

            $this->global['pageTitle'] = 'Pace-Tel : Group Listing';
            
            $this->loadViews("managerGroup", $this->global, $data, NULL);
        }
    }

    function managerGroupAdd()
    {
        if($this->perm_createmanagers == 0){
            $this->session->set_flashdata('error', 'You are not allowed to create user groups. Contact Admin to allow permission');
            redirect('managerGroup');
        }

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('users_model');

            $managername = $this->session->userdata ( 'name' );
            $managerInfo = $this->users_model->getManagerInfo($managername);

            if($managername == 'admin')
            {
                $this->load->model('users_model');
                $data['managerGroups'] = $this->users_model->getUsersGroup();
                $data['usersGroup'] = $this->users_model->getUsersGroup();
                $data['managerList'] = $this->users_model->getManagersList();
                $data['managerDataList'] = $this->users_model->getManagerDataList();
                $data['nasname'] = $this->Other_model->getNasList();
                $this->global['pageTitle'] = 'Groups : Add New User';
                $this->loadViews("managerGroupNew", $this->global, $data, NULL);
            }else{
                //echo "Not Allowed..........";
                $this->session->set_flashdata('error', 'Operation not allowed........');
                redirect('usersListing');
            }
        }
    }

    function managerGroupSave()
    {

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {

            $this->load->library('form_validation');
            $this->form_validation->set_rules('shortname','NAS Name','trim|max_length[64]');
            $this->form_validation->set_rules('managername','Manager');
            $this->form_validation->set_rules('usergroup','User Group','required');
            $this->form_validation->set_rules('desc','Description','max_length[128]');

            if($this->form_validation->run() == FALSE)
            {
                $this->managerGroupAdd($this->input->post('grpname'));
            }
            else
            {
                $managername = $this->security->xss_clean($this->input->post('managername'));
                if(empty($managername) || $managername == "0"){
                    $managername = $this->session->userdata ( 'name' );
                }

                $grpname = $this->security->xss_clean($this->input->post('shortname'));
                //$grpname = $this->security->xss_clean($this->input->post('browser'));

                $usergroup = $this->security->xss_clean($this->input->post('usergroup'));
                $desc = $this->security->xss_clean($this->input->post('desc'));
              
                $managerGroupInfo = array('grpname'=>$grpname,
                                    'usergrpid'=>$usergroup,
                                    'desc'=>$desc,
                                    'managername'=>$managername);
                                    
                $this->load->model('users_model');

                if(($this->users_model->managerGroupExists($grpname, $usergroup, $managername)) == false)
                {
                    $result = $this->users_model->addNewManagerGroup($managerGroupInfo);
                
                    if($result == True)
                    {
                        $this->session->set_flashdata('success', 'Manager Group Mapping Created successfully');
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Manager Group Mapping creation failed');
                    }
                
                }else{
                    $this->session->set_flashdata('error', 'Manager Group Mapping already exists....');
                }

                redirect('managerGroup');
            }
        }
    }

    function managerGroupEdit($grpId = NULL)
    {
        if($this->perm_editmanagers == 0){
            $this->session->set_flashdata('error', 'You are not allowed to edit user groups. Contact Admin to allow permission');
            redirect('managerGroup');
        } 

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            if($grpId == null)
            {
                redirect('managerGroup');
            }
            
            $data['groupInfo'] = $this->users_model->getManagerGroupInfo($grpId);
            $data['usersGroup'] = $this->users_model->getUsersGroup();
            $data['managerList'] = $this->users_model->getManagersList();
            
            $this->global['pageTitle'] = 'PaceTel : Edit User';
            
            $this->loadViews("managersGroupEdit", $this->global, $data, NULL);
        }
    }

    function managerGroupUpdate()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            $grpid = $this->input->post('grpid');
            
            $this->form_validation->set_rules('grpname','Group Name','required|max_length[128]');
            $this->form_validation->set_rules('usersGroup','User Group','required');
            $this->form_validation->set_rules('desc','Description','max_length[128]');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->managerGroupEdit($grpid);
            }
            else
            {

                $grpid = $this->input->post('grpid');
                $grpname = $this->security->xss_clean($this->input->post('grpname'));
                $usergrpid = $this->security->xss_clean($this->input->post('usersGroup'));
                $desc = $this->security->xss_clean($this->input->post('desc'));
                $managername = $this->security->xss_clean($this->input->post('manager'));

                $managerGroupInfo = array('grpname'=>$grpname, 'usergrpid'=>$usergrpid, 'desc'=>$desc, 'managername'=>$managername);
                
                $result = $this->users_model->updateManagerGroupInfo($managerGroupInfo, $grpid);
                
                if($result == true)
                {
                    $this->session->set_flashdata('success', 'Manager Group updated successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Manager Group updation failed');
                }
                
                redirect('managerGroup');

            }
        }
    }

    // ********** Users Batch List *********** //
    function usersBatchList()
    {
        if($this->perm_cardsys == 0){
            $this->session->set_flashdata('error', 'You are not allowed to view Card system. Contact Admin to allow permission');
            redirect('dashboard');
        }
        
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {

            $managerAllServices = $this->perm_createservices;
            //$searchText = $this->session->userdata ( 'name' );        

            $data['managerList'] = $this->users_model->getManagersList();

            $searchText = $this->security->xss_clean($this->input->post('searchText'));

            $data['searchText'] = $searchText;

            $searchText1 = $this->security->xss_clean($this->input->post('searchText1'));
            $searchText2 = $this->security->xss_clean($this->input->post('searchText2'));
            $searchText3 = $this->security->xss_clean($this->input->post('searchText3'));
            $searchText4 = $this->security->xss_clean($this->input->post('searchText4'));

            $data['searchText1'] = $searchText1;
            $data['searchText2'] = $searchText2;
            $data['searchText3'] = $searchText3;
            $data['searchText4'] = $searchText4;
            $data['manualSyncBatchUrl'] = site_url('other_controller/sync_mikrotik_vouchers');

            //$data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            
            $count = $this->users_model->cardsBatchCount($searchText, $managerAllServices);

            //$returns = $this->paginationCompress ( "serieslist", $count, 10 );
            
            $data['groupListing'] = $this->users_model->cardsBatchListing($searchText, 0, 0, $managerAllServices, $searchText1, $searchText2, $searchText3);

            $this->global['pageTitle'] = 'Pace-Tel : Group Listing';
            
            $this->loadViews("Prepaid/serieslist", $this->global, $data, NULL);
        }
    }

    function usersCardsCleanList($seriesID) // Simple Cards Fetch from Series List for Copy Pase
    {
        if($this->perm_cardsys == 0){
            $this->session->set_flashdata('error', 'You are not allowed to view Card system. Contact Admin to allow permission');
            redirect('dashboard');
        }
        
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('users_model');
            $this->load->library('pagination');

            $searchText = $this->security->xss_clean($this->input->post('searchText'));

            $data['searchText'] = $searchText;
            $data['cardsListing'] = $this->users_model->cardsListing($searchText, $seriesID);
            $data['cardsListingSummery'] = $this->users_model->cardsListingSummery($searchText, $seriesID);
            $data['seriesID'] = $seriesID;
            $data['nasList'] = $this->Other_model->getNasList();
            $data['manualSyncBatchUrl'] = site_url('other_controller/sync_mikrotik_vouchers');

            // Build card -> NAS label map so view can show exactly which NAS each voucher belongs to.
            $data['nasLabelByCardnum'] = [];
            $data['seriesNasLabel'] = 'Unassigned';

            $cardNums = [];
            if (!empty($data['cardsListing'])) {
                foreach ($data['cardsListing'] as $row) {
                    if (isset($row->cardnum) && $row->cardnum !== '') {
                        $cardNums[] = $row->cardnum;
                    }
                }
            }
            $cardNums = array_values(array_unique($cardNums));
            $data['seriesNasId'] = null;

            if (!empty($cardNums)) {
                $rmCardFields = $this->db->list_fields('rm_cards');
                $nasCol = null;
                if (in_array('nasid', $rmCardFields, true)) {
                    $nasCol = 'nasid';
                } elseif (in_array('nas_id', $rmCardFields, true)) {
                    $nasCol = 'nas_id';
                }

                $nasRows = [];
                if ($nasCol !== null) {
                    $this->db->select('c.cardnum, c.' . $nasCol . ' as nas_ref, n.shortname, n.nasname', false);
                    $this->db->from('rm_cards c');
                    $this->db->join('nas n', 'n.id = c.' . $nasCol, 'left', false);
                    $this->db->where_in('c.cardnum', $cardNums);
                    $nasRows = $this->db->get()->result();
                }

                $seriesNasUnique = [];
                foreach ($nasRows as $nr) {
                    $label = 'Unassigned';
                    if (isset($nr->nas_ref) && (int)$nr->nas_ref > 0) {
                        $short = isset($nr->shortname) ? trim((string)$nr->shortname) : '';
                        $ip = isset($nr->nasname) ? trim((string)$nr->nasname) : '';
                        if ($short !== '' && $ip !== '') $label = $short . ' (' . $ip . ')';
                        elseif ($ip !== '') $label = $ip;
                        else $label = 'NAS ' . (int)$nr->nas_ref;
                        $seriesNasUnique[$label] = true;
                        if ($data['seriesNasId'] === null) {
                            $data['seriesNasId'] = (int)$nr->nas_ref;
                        }
                    }
                    $data['nasLabelByCardnum'][$nr->cardnum] = $label;
                }

                if (count($seriesNasUnique) === 1) {
                    $keys = array_keys($seriesNasUnique);
                    $data['seriesNasLabel'] = $keys[0];
                } elseif (count($seriesNasUnique) > 1) {
                    $data['seriesNasLabel'] = 'Mixed NAS';
                }
            }

            if (empty($data['nasList']) || !is_array($data['nasList'])) {
                $data['nasList'] = [];
                $nasRows = $this->db->select('id, shortname, nasname')->get('nas')->result();
                foreach ($nasRows as $nasRow) {
                    $data['nasList'][] = (object) [
                        'id' => isset($nasRow->id) ? (int)$nasRow->id : 0,
                        'shortname' => isset($nasRow->shortname) ? $nasRow->shortname : '',
                        'nasname' => isset($nasRow->nasname) ? $nasRow->nasname : '',
                    ];
                }
            }

            // Build card activity details for UI (active now, activation date, last seen, expiry state).
            $data['cardActivityByCardnum'] = [];
            if (!empty($cardNums)) {
                foreach ($data['cardsListing'] as $row) {
                    $cn = isset($row->cardnum) ? $row->cardnum : null;
                    if (empty($cn)) continue;
                    $exp = isset($row->expiration) ? trim((string)$row->expiration) : '';
                    $expired = false;
                    if ($exp !== '' && $exp !== '0000-00-00') {
                        $expired = ($exp < date('Y-m-d'));
                    }
                    $activeNowFallback = false;
                    if (isset($row->active)) $activeNowFallback = ((int)$row->active === 1);
                    elseif (isset($row->used)) $activeNowFallback = ((int)$row->used === 1);

                    $activatedFallback = null;
                    if (!empty($row->activated_on)) $activatedFallback = $row->activated_on;
                    elseif (!empty($row->activatedon)) $activatedFallback = $row->activatedon;
                    elseif (!empty($row->date)) $activatedFallback = $row->date;

                    $lastActiveFallback = null;
                    if (!empty($row->last_active_on)) $lastActiveFallback = $row->last_active_on;
                    elseif (!empty($row->lastactiveon)) $lastActiveFallback = $row->lastactiveon;
                    elseif (!empty($row->last_seen)) $lastActiveFallback = $row->last_seen;
                    elseif (!empty($row->lastseen)) $lastActiveFallback = $row->lastseen;

                    $sessionFallback = 0;
                    if (isset($row->timebaseonline)) $sessionFallback = (int)$row->timebaseonline;

                    $data['cardActivityByCardnum'][$cn] = [
                        'is_active_now' => $activeNowFallback,
                        'activated_on' => $activatedFallback,
                        'last_active_on' => $lastActiveFallback,
                        'last_stop_on' => null,
                        'session_seconds' => $sessionFallback,
                        'expiry_date' => ($exp !== '' && $exp !== '0000-00-00') ? $exp : null,
                        'is_expired' => $expired,
                    ];
                }

                $hasRadacct = $this->db->query("SHOW TABLES LIKE 'radacct'")->num_rows() > 0;
                if ($hasRadacct) {
                    $chunks = array_chunk($cardNums, 300);
                    foreach ($chunks as $chunk) {
                        if (empty($chunk)) continue;
                        $this->db->select('username, MIN(acctstarttime) AS first_start, MAX(acctstarttime) AS last_start, MAX(acctstoptime) AS last_stop, SUM(IFNULL(acctsessiontime,0)) AS total_seconds, SUM(CASE WHEN acctstoptime IS NULL THEN 1 ELSE 0 END) AS open_sessions', false);
                        $this->db->from('radacct');
                        $this->db->where_in('username', $chunk);
                        $this->db->group_by('username');
                        $radRows = $this->db->get()->result();

                        foreach ($radRows as $rr) {
                            $u = isset($rr->username) ? $rr->username : null;
                            if (empty($u) || !isset($data['cardActivityByCardnum'][$u])) continue;
                            $data['cardActivityByCardnum'][$u]['is_active_now'] = ((int)$rr->open_sessions > 0);
                            $data['cardActivityByCardnum'][$u]['activated_on'] = isset($rr->first_start) ? $rr->first_start : null;
                            $data['cardActivityByCardnum'][$u]['last_active_on'] = isset($rr->last_start) ? $rr->last_start : null;
                            $data['cardActivityByCardnum'][$u]['last_stop_on'] = isset($rr->last_stop) ? $rr->last_stop : null;
                            $data['cardActivityByCardnum'][$u]['session_seconds'] = isset($rr->total_seconds) ? (int)$rr->total_seconds : 0;
                        }
                    }
                }
            }


            $this->global['pageTitle'] = 'Pace-Tel : Cards List';
            
            $this->loadViews("Prepaid/cardslist", $this->global, $data, NULL);
        }
    }

    function export_cardstocsv($seriesID = NULL)
    {

        $file_name = 'cards_details_on_'.date('Ymd').'.csv'; 
        header("Content-Description: File Transfer"); 
        header("Content-Disposition: attachment; filename=$file_name"); 
        header("Content-Type: application/csv;");

        // get data 
        $user_data = $this->users_model->export_cardstocsv($seriesID);
   
        // file creation 
        $file = fopen('php://output', 'w');
    
        $header = array("cardnum", "password", "value", "expiration", "series", "date", "owner", "used"); 
        fputcsv($file, $header);
        foreach ($user_data->result_array() as $key => $value)
        { 
          fputcsv($file, $value); 
        }
        fclose($file); 
        exit; 
    }

    // ******* Generate Prepaid - Users OR Cards *********** //

    function cardsListing($accType = 2) // Only Used to Load The Default Page and Pagination will be used of Function usersListing
    {
        if($this->perm_cardsys == 0){
            $this->session->set_flashdata('error', 'You are not allowed to view users. Contact Admin to allow permission');
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

            $type = $this->security->xss_clean($this->input->post('searchText1'));
            
            $this->load->library('pagination');
            
            $count = $this->users_model->userListingCount($searchText, $type, $accType);

			$returns = $this->paginationCompress ( "usersListing/", $count, 20, 4 );
            
            $data['userRecords'] = $this->users_model->userListing($searchText, $type, $accType, $returns["page"], $returns["segment"]);

            $data['type'] = $type;
            $data['accType'] = $accType;

            $this->global['pageTitle'] = 'Pace-Tel : User Listing';
            
            $this->loadViews("userslist", $this->global, $data, NULL);
        }
    }

    function prepaidGenUsers()
    {

        if($this->perm_cardsys == 0 || $this->perm_createusers == 0){
            $this->session->set_flashdata('error', 'You are not allowed to create users. Contact Admin to allow permission');
            redirect('usersListing');
        }

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('users_model');

            $managername = $this->session->userdata ( 'name' );
            $managerInfo = $this->users_model->getManagerInfo($managername);

            if($managername == 'admin'|| $this->ismaster > 0 || $this->perm_cardsys == 1 && $this->perm_createusers == 1)
            {
                $this->load->model('users_model');
                $data['managerGroups'] = $this->users_model->getUsersGroup();
                $data['usersGroup'] = $this->users_model->getUsersGroup();
                $data['managerList'] = $this->users_model->getManagersList();
                $data['packages'] = $this->users_model->getResellerPackages();
                $this->global['pageTitle'] = 'Groups : Add New User';
                $this->loadViews("Prepaid/genusers", $this->global, $data, NULL);
            }else{
                //echo "Not Allowed..........";
                $this->session->set_flashdata('error', 'Operation not allowed........');
                redirect('Prepaid/genusers');
            }
        }
    }

    function prepaidCreateUsersBatch()
    {

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {

            $this->load->library('form_validation');
            $this->form_validation->set_rules('cardtype','Card Type','required|integer');
            $this->form_validation->set_rules('quantity','Quantity','required|integer');
            $this->form_validation->set_rules('cardvalue','Card Value','required|decimal');
            $this->form_validation->set_rules('expiration','Expiration','required');

            //if($this->form_validation->run() == FALSE)
            //{
                //$this->prepaidGenUsers($this->input->post('grpname'));
            //}
            //else
            //{
                $managername = $this->security->xss_clean($this->input->post('managername'));
                if(empty($managername) || $managername == "0"){
                    $managername = $this->session->userdata ( 'name' );
                }
                
                $quantity = $this->security->xss_clean($this->input->post('quantity'));
                $cardtype = $this->security->xss_clean($this->input->post('cardtype'));
                //$cardvalue = $this->security->xss_clean($this->input->post('cardvalue'));
                $expiration = $this->security->xss_clean($this->input->post('expiration'));
                $prefix = $this->security->xss_clean($this->input->post('prefix'));
                $usergroup = $this->security->xss_clean($this->input->post('usergroup'));
                $pinlength = $this->security->xss_clean($this->input->post('pinlength'));
                $pswlength = $this->security->xss_clean($this->input->post('pswlength'));

                $downlimit = $this->security->xss_clean($this->input->post('downlimit'));
                $uplimit = $this->security->xss_clean($this->input->post('uplimit'));
                $comblimit = $this->security->xss_clean($this->input->post('comblimit'));
                $uptimelimit = $this->security->xss_clean($this->input->post('uptimelimit'));
                $expiremode = $this->security->xss_clean($this->input->post('expiremode'));

                $expiretime = $this->security->xss_clean($this->input->post('expiretime'));
                $timebaseexp = $this->security->xss_clean($this->input->post('timebaseexp'));

                $srvid = $this->security->xss_clean($this->input->post('packages'));
                $cardvalue = $this->services_model->getPackageDetails($managername, $srvid);

                //echo $cardtype."-".$quantity."-".$managername."-".$cardvalue."-".$expiration."-".$prefix."-Group-".$usergroup;
                $cardseriesmax = $this->users_model->genCardSeriesNumber();
                $cardseries = date('Y').'-'.sprintf('%04d', ($cardseriesmax->series+1));

                $cardSeriesPrice = $this->generatePrepaidCards($quantity, $cardtype, $cardvalue, $expiration, $prefix, 
                                        $usergroup, $pinlength, $pswlength, $downlimit, $uplimit, $comblimit, $uptimelimit, $expiremode, 
                                        $expiretime, $timebaseexp, $managername, $srvid, $cardseries);

                $basePrice = $cardSeriesPrice["cardSeriesMasterCost"];
                $unitPrice = $cardSeriesPrice["cardSeriesCost"];
                $salePrice = $cardSeriesPrice["cardSeriesSales"];

                // DISABLED due to reasons to change it for Real Time Voucher Actionvation Invoice //
                //$this->postBulkCardsInvoice($cardSeriesPrice, $managername, $cardseries);

                //echo "Base = ".$basePrice." - Unit = ".$unitPrice." - Sales = ".$salePrice;
                redirect('serieslist');
            //}
        }
    }

    // Generate Cards and Post Users in rm_users Table
    function generatePrepaidCards($quantity, $cardtype, $cardvalue, $expiration, $prefix, $usergroup, $pinlength,
                            $pswlength, $downlimit, $uplimit, $comblimit, $uptimelimit, 
                            $expiremode, $expiretime, $timebaseexp, $managername, $srvid, $cardseries){

            $cardSeriesSales = 0;
            $cardSeriesCost = 0;
            $cardSeriesMasterCost = 0;

            for ($i = 0; $i < $quantity; $i++){

                //echo " || CARD NEW:".$this->getRandomString($pinlength, $prefix);
                $newCardNum = $this->getRandomString($pinlength, $prefix);
                $cardPassword = $this->getRandomString($pswlength);
                $cardNextID = $this->users_model->getMaxId();

                if($this->users_model->checkUserExist($newCardNum) == FALSE){

                    $cardsInfo = array('id'=>$cardNextID->id + 1,
                                        'cardnum'=>$newCardNum,
                                        'password'=>$cardPassword,
                                        'value'=>$cardvalue->saleprice,
                                        'expiration'=>$expiration,
                                        'series'=>$cardseries,
                                        'date'=>date("Y-m-d"),
                                        'owner'=>$managername,
                                        'cardtype'=>0,
                                        'revoked'=>0,
                                        'downlimit'=>$downlimit,
                                        'uplimit'=>$uplimit,
                                        'comblimit'=>$comblimit,
                                        'uptimelimit'=>$uptimelimit,
                                        'srvid'=>$srvid,
                                        'transid'=>$this->getRandomStronString(),
                                        'active'=>0,
                                        'expiretime'=>($expiremode == 1 ? $expiretime : 0 ),
                                        'timebaseexp'=>$timebaseexp,
                                        'timebaseonline'=>0);

                    $cardSeriesSales = $cardSeriesSales + $cardvalue->saleprice;
                    $cardSeriesCost = $cardSeriesCost + $cardvalue->unitprice;
                    $cardSeriesMasterCost = $cardSeriesMasterCost + $cardvalue->baseprice;

                    // Crate New Prepaid User 
                    $userInfo = array('username'=>$newCardNum, 'password'=>MD5($cardPassword), 'groupid'=>$usergroup, 'enableuser'=> 1,
                                        'uplimit'=>$uplimit, 'downlimit'=>$downlimit, 'comblimit'=>$comblimit,
                                        'firstname'=>'Prepaid', 'lastname'=>'Card', 'address'=>'',
                                        'mobile'=>'', 'email'=>'', 'taxid'=>'',
                                        'gpslat'=>0.00000000000000, 'gpslong'=>0.00000000000000,
                                        'usemacauth'=>0, 'expiration'=>$expiration, 'uptimelimit'=>$uptimelimit, 'srvid'=>$srvid, 
                                        'ipmodecm'=>0, 'ipmodecpe'=>0, 'poolidcm'=>0, 'poolidcpe'=>0,
                                        'createdon'=>date('Y-m-d'), 'acctype'=>2, 'credits'=>$cardvalue->saleprice, 'cardfails'=>0,
                                        'createdby'=>$managername,
                                        'owner'=>$managername,
                                        'warningsent'=>0, 'verified'=>1, 'selfreg'=>0, 'verifyfails'=>0, 'verifysentnum'=>0,
                                        'contractvalid'=>'0000-00-00', 'pswactsmsnum'=>0, 'alertemail'=>0, 'alertsms'=>0,
                                        'custattr'=>'Mikrotik-Address-List := '.$managername,
                                        'lang'=>'English');

                    $radpassword = array('username'=>$newCardNum, 'attribute'=>'Cleartext-Password', 'op'=>':=', 'value'=>$cardPassword);
                    $radsimuse = array('username'=>$newCardNum, 'attribute'=>'Simultaneous-Use', 'op'=>':=', 'value'=>'1');

                    $logInfo = 'user: '.$newCardNum.'-srvid: '.$srvid.'-createdby: '.$this->session->userdata ( 'name' );
                    log_message('info', 'DB_INFO - Created Bulk Prepaid User '.$logInfo);


                    if($this->users_model->checkUserExist($newCardNum) == FALSE)
                    {
                        //print_r($cardsInfo);
                        $this->users_model->addNewCard($cardsInfo);
                        $result = $this->users_model->addNewUser($userInfo, $radpassword, $radsimuse);
                    }

                }
            }

            $cardSeriesPrice = array('cardSeriesSales'=>$cardSeriesSales, 'cardSeriesCost'=>$cardSeriesCost, 'cardSeriesMasterCost'=>$cardSeriesMasterCost);
            return $cardSeriesPrice;

    }

    function getRandomString($length = 8, $prefix = '') {

        $characters = '123456789';
        $string = $prefix;
        $length = $length - strlen($prefix);
    
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
    
        return $string;
    } 

    function getRandomStronString($length = 13) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';
    
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
    
        return $string;
    }

    // Prepaid Cards Debit To Reseller
    function postBulkCardsInvoice($cardSeriesPrice, $managername, $cardseries){

        $basePrice = $cardSeriesPrice["cardSeriesMasterCost"];
        $unitPrice = $cardSeriesPrice["cardSeriesCost"];
        $salePrice = $cardSeriesPrice["cardSeriesSales"];

        $invtype = "Recharge";

        //echo "Base = ".$basePrice." - Unit = ".$unitPrice." - Sales = ".$salePrice;
        
        $dt1 = new DateTime();
        $date1 = $dt1->format("Y-m-d");

        $srv_date = $date1;
        $exp_date = $date1;

        // Debit Reseller
        $userInfo = array('username'=>$cardseries,
                        'srvid'=>0, 
                        'managername'=>$managername,
                        'createdBy'=>0,
                        'invtype'=>$invtype,
                        'srvdate'=>$srv_date,
                        'expdate'=>$exp_date,
                        'price'=>0,
                        'amount'=>-$unitPrice,
                        'remarks'=>$cardseries." Bulk Users ");

        if($managername <> $this->session->userdata ( 'name' ) && $managername <> 'admin'){

            $manager_master = $this->session->userdata ( 'name' );
            $masterInfo = array('username'=>$cardseries,
                            'srvid'=>0, 
                            'managername'=>$manager_master,
                            'createdBy'=>0,
                            'invtype'=>$invtype,
                            'srvdate'=>$srv_date,
                            'expdate'=>$exp_date,
                            'price'=>0,
                            'amount'=>$unitPrice-$basePrice,
                            'remarks'=>"Commission Series:".$cardseries." (Sales:".$unitPrice." Cost:".$unitPrice.")");

        }

        $this->load->model('users_model');
        $this->load->model('Invoices_model');

        if(($this->users_model->checkManagerExist($managername)) == true)
        {
            $result = $this->Invoices_model->addRecharge($userInfo);
            if($managername <> $this->session->userdata ( 'name' ) && $managername <> 'admin' && ($unitPrice-$basePrice) > 0){
                $resultmaster = $this->Invoices_model->addRecharge($masterInfo);
            }

            if($result == True)
            {
                $this->session->set_flashdata('success', 'Manager recharged successfully');
            }
            else
            {
                $this->session->set_flashdata('error', 'Manager recharged failed');
            }
        
        }else{
            $this->session->set_flashdata('error', 'Manager not exists....');
        }


    }

    function printCards($seriesID){

        // get data 
        $cardsData = $this->users_model->export_cardstocsv($seriesID);

        $data['cardsData'] = $cardsData->result();

        //print_r($data);

        $this->load->view("/Prepaid/printcards", $data);
    }

    // Revoke Unsed Cards //
    function revoke_returncards($series = ''){

        $effectedrows = $this->users_model->revoke_cards_and_disable_users($series, 1);

        //print_r($data);
        $this->session->set_flashdata('success', 'Cards Revoked '.$effectedrows['cards_updated'].' - Users Blocked '.$effectedrows['users_updated']);
        redirect('serieslist');

    }

    function revoke_allcards($series = ''){

    }

    function unlock_returncards($series = ''){

        $effectedrows = $this->users_model->unlock_cards_and_enable_users($series, 1);

        //print_r($data);
        $this->session->set_flashdata('success', 'Cards Unlocked '.$effectedrows['cards_updated'].' - Users Activated '.$effectedrows['users_updated']);
        redirect('serieslist');

    }


    function getInfoCustomer($nasipaddress, $username){

        if(!empty($username))
        {
            
            $ddnsNasInfo = $this->Other_model->getDDNSMappingInfo($nasipaddress);
            $userInfo = $this->users_model->getUserInfo($username, 2);

            //shortname, nasname, domain, apiuser, apipasswd, apiport
            //echo "Name:".$ddnsNasInfo->shortname."- Nas:".$ddnsNasInfo->nasname."- Domain:".$ddnsNasInfo->domain." User: ".$userInfo->username;

            echo "Trying to find user: ".$username." - ".$nasipaddress."<br>\n</br>";

            //print_r($ddnsNasInfo)."<br>\n</br>";

            //print_r($userInfo)."<br>\n</br>";


            if(!empty($ddnsNasInfo) && !empty($userInfo)){

                //$apiHost = gethostbyname($ddnsNasInfo->domain);
                if(!empty($ddnsNasInfo->domain)){
                    $apiHost = gethostbyname($ddnsNasInfo->domain);
                }else{
                    $apiHost = $ddnsNasInfo->nasname;
                }
                echo "Found host for : ".$nasipaddress." domain:  ".$ddnsNasInfo->domain." IP: ".$apiHost."<br>\n</br>";

                if(empty($apiHost)){
                    $apiHost = $record->nasname;
                }

                // Send request to add Hotspot User
                $this->getCustomerDevice_API($apiHost, $ddnsNasInfo->apiuser, $ddnsNasInfo->apipasswd, $userInfo, $ddnsNasInfo->authmode);
            }

        }
        
    }

    function getCustomerDevice_API($apiHost, $apiuser, $apipasswd, $userInfo, $authmode = 0){

        $API = new RouterosAPI();
        $API->debug = false;

        if ($API->connect($apiHost, $apiuser, $apipasswd)) {

            $leases = $API->comm("/ip/dhcp-server/lease/print");

            $targetIP = '172.16.4.113';
            //echo "Results Are:   >>>>> ";
            //print_r($leases);

            // Flag to check if hostname was found
            $hostnameFound = false;

            // Loop through the leases to find the target IP
            foreach ($leases as $lease) {
                if (isset($lease['address']) && $lease['address'] === $targetIP) {
                    // If IP matches, retrieve and print the hostname
                    $hostname = isset($lease['host-name']) ? $lease['host-name'] : 'No hostname found';
                    echo "Hostname for IP $targetIP: $hostname\n";
                    $hostnameFound = true;
                    break; // Exit the loop once found
                }
            }
        
            // If no hostname was found for the IP
            if (!$hostnameFound) {
                echo "No hostname found for IP $targetIP.\n";
            }

            // Disconnect from the MikroTik API
            $API->disconnect();

        } else {
            echo "Failed to connect to MikroTik API.";
        }


    }

    public function getSegmentDetails()
    {
        $segmentid = $this->input->post('segmentid');
        $this->load->model('Network_model');
        $details = $this->Network_model->segmentListdetails($segmentid);

        $options = '<option value="">Select Area</option>';
        foreach ($details as $detail) {
            $options .= '<option value="' . htmlspecialchars($detail->parameter) . '">' . htmlspecialchars($detail->parameter) . '</option>';
        }
        echo $options;
        exit;
    }

    /**
     * Show users missing region/parameter and allow assignment, with filters and pagination
     */
    public function usersAssignRegion() {
        $this->load->model('Network_model');
        $this->load->model('Users_model');
        $this->load->helper('url');

        $this->load->library('pagination');

        // Filters from GET/POST
        $filters = [
            'manager' => $this->input->get_post('manager'),
            'address' => $this->input->get_post('address'),
            'username' => $this->input->get_post('username'),
            'expiration_from' => $this->input->get_post('expiration_from'),
            'expiration_to' => $this->input->get_post('expiration_to'),
            'created_from' => $this->input->get_post('created_from'),
            'created_to' => $this->input->get_post('created_to'),
        ];

        // Regions/parameters for dropdowns
        if($this->perm_areaaccess == 0){
            $data['regions'] = $this->Network_model->segmentList(2, 0, $this->session->userdata('name'));
            $data['parameters'] = $this->Network_model->segmentListdetails();
        }else{
            $data['regions'] = $this->Network_model->segmentList(1, 1);
            $data['parameters'] = $this->Network_model->segmentListdetails();
        }

        // Manager list for filter (admin/master only)
        $managername = $this->session->userdata('name');
        $isMaster = isset($this->ismaster) ? $this->ismaster : 0;
        if ($managername === 'admin' || $isMaster > 0) {
            $data['managerList'] = $this->Users_model->getManagersList();
        } else {
            $data['managerList'] = [];
        }

        // Get total count for pagination
        $countResult = $this->Users_model->getUsersMissingRegionOrParameter(0, 0, $filters, true); // true for count only
        $count = is_array($countResult) ? $countResult['totalCount'] : $countResult;

        // Use paginationCompress as in userListing
        $returns = $this->paginationCompress("usersAssignRegion", $count, 20);
        $page = $returns["page"];
        $segment = $returns["segment"];


        // Get filtered, paginated users
        $result = $this->Users_model->getUsersMissingRegionOrParameter($page, $segment, $filters);
        $data['users'] = $result['records'];
        $data['pagination'] = $this->pagination->create_links();
        $data['filters'] = $filters;
        $this->global['pageTitle'] = 'Assign Region/Location to Users';
        $this->loadViews('usersAssignRegion', $this->global, $data, NULL);
    }

    /**
     * AJAX: Bulk assign region/parameter to users
     */
    public function bulkAssignRegionParameter() {
        $this->load->model('Users_model');
        $usernames = $this->input->post('usernames');
        $segmentid = $this->input->post('segmentid');
        $parameter = $this->input->post('parameter');
        if (!is_array($usernames) || empty($usernames)) {
            echo json_encode(['success' => false, 'message' => 'No users selected.']);
            return;
        }
        $affected = $this->Users_model->bulkAssignRegionParameter($usernames, $segmentid, $parameter);
        if ($affected) {
            echo json_encode(['success' => true, 'message' => 'Region/Location assigned to selected users.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes made.']);
        }
    }

    /**
     * AJAX: Get online status for a list of usernames
     * POST: usernames[]
     * Returns: {success: true, statuses: {username: 1|0, ...}}
     */
    public function ajaxGetOnlineStatus()
    {
        $usernames = $this->input->post('usernames');
        if (!is_array($usernames) || empty($usernames)) {
            echo json_encode(['success' => false, 'error' => 'No usernames provided']);
            return;
        }
        $this->load->model('Users_model');
        $statuses = $this->Users_model->getOnlineStatusForUsers($usernames);
        echo json_encode(['success' => true, 'statuses' => $statuses]);
    }
}
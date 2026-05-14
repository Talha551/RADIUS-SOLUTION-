<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class User extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->model('login_model');
        $this->load->model('Reports_model');
        $this->isLoggedIn();   
    }
    
    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->global['pageTitle'] = 'PaceTel : Dashboard';
        
        $data = null;
        $this->loadViews("dashboard", $this->global, $data , NULL);
    }

    function getDashBoardInfo(){

        $date2 = Date("Y-m-d");
        $date1 = date('Y-m-d', strtotime("-1 month", strtotime($date2)));

        $managername = $this->session->userdata ( 'name' );
        $masterInfo = $this->login_model->lisMasterManager($managername);
        $data['ismastermanager'] = $masterInfo->managercount;
        $data['customerInfo'] = $this->user_model->dashboardCustomerInfo($masterInfo->managercount);

        $data['customerOnlineInfo'] = $this->user_model->dashboardCustomerOnlineStatus($masterInfo->managercount);

        $data['balanaceInfo'] = $this->user_model->dashboardBalanceInfo($masterInfo->managercount);
        $data['costOfSales'] = $this->user_model->dashboardCostOfSales($masterInfo->managercount);
        $data['paymentInfo'] = $this->user_model->dashboardEasyPaisaBalance();
        //$data['fairusealert'] = $this->Reports_model->dashboard_fairuse_alert();
        $data['salesalert'] = $this->Reports_model->dashboard_sales_alert();

        $data['perm_listusers '] = $this->perm_listusers;
        $data['perm_createusers '] = $this->perm_createusers;
        $data['perm_editusers '] = $this->perm_editusers;
        $data['perm_edituserspriv '] = $this->perm_edituserspriv;
        $data['perm_deleteusers '] = $this->perm_deleteusers;
        $data['perm_listmanagers '] = $this->perm_listmanagers;
        $data['perm_createmanagers '] = $this->perm_createmanagers;
        $data['perm_editmanagers '] = $this->perm_editmanagers;
        $data['perm_deletemanagers '] = $this->perm_deletemanagers;
        $data['perm_listservices '] = $this->perm_listservices;
        $data['perm_createservices '] = $this->perm_createservices;
        $data['perm_editservices '] = $this->perm_editservices;
        $data['perm_deleteservices '] = $this->perm_deleteservices;
        $data['perm_listonlineusers '] = $this->perm_listonlineusers;
        $data['perm_listinvoices '] = $this->perm_listinvoices;
        $data['perm_trafficreport '] = $this->perm_trafficreport;
        $data['perm_addcredits '] = $this->perm_addcredits;
        $data['perm_negbalance '] = $this->perm_negbalance;
        $data['perm_listallinvoices '] = $this->perm_listallinvoices;
        $data['perm_showinvtotals '] = $this->perm_showinvtotals;
        $data['perm_logout '] = $this->perm_logout;
        $data['perm_cardsys '] = $this->perm_cardsys;
        $data['perm_editinvoice '] = $this->perm_editinvoice;
        $data['perm_allusers '] = $this->perm_allusers;
        $data['perm_allowdiscount '] = $this->perm_allowdiscount;
        $data['perm_enwriteoff '] = $this->perm_enwriteoff;
        $data['perm_accessap '] = $this->perm_accessap;
        $data['perm_cts '] = $this->perm_cts;


        return $data;

    }
    
    /**
     * This function is used to load the user list
     */
    function userListing()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {        
            $searchText = $this->security->xss_clean($this->input->post('searchText'));
            $data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            
            $count = $this->user_model->userListingCount($searchText);

			$returns = $this->paginationCompress ( "userListing/", $count, 10 );
            
            $data['userRecords'] = $this->user_model->userListing($searchText, $returns["page"], $returns["segment"]);
            
            $this->global['pageTitle'] = 'PaceTel : User Listing';
            
            $this->loadViews("users", $this->global, $data, NULL);
        }
    }

    /**
     * This function is used to load the add new form
     */
    function addNew()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('user_model');
            $data['roles'] = $this->user_model->getUserRoles();
            
            $this->global['pageTitle'] = 'PaceTel : Add New User';

            $this->loadViews("addNew", $this->global, $data, NULL);
        }
    }

    /**
     * This function is used to check whether email already exist or not
     */
    function checkEmailExists()
    {
        $userId = $this->input->post("userId");
        $email = $this->input->post("email");

        if(empty($userId)){
            $result = $this->user_model->checkEmailExists($email);
        } else {
            $result = $this->user_model->checkEmailExists($email, $userId);
        }

        if(empty($result)){ echo("true"); }
        else { echo("false"); }
    }
    
    /**
     * This function is used to add new user to the system
     */
    function addNewUser()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('fname','Full Name','trim|required|max_length[128]');
            $this->form_validation->set_rules('email','Email','trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('password','Password','required|max_length[20]');
            $this->form_validation->set_rules('cpassword','Confirm Password','trim|required|matches[password]|max_length[20]');
            $this->form_validation->set_rules('role','Role','trim|required|numeric');
            $this->form_validation->set_rules('mobile','Mobile Number','required|min_length[10]');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->addNew();
            }
            else
            {
                $name = ucwords(strtolower($this->security->xss_clean($this->input->post('fname'))));
                $email = strtolower($this->security->xss_clean($this->input->post('email')));
                $password = $this->input->post('password');
                $roleId = $this->input->post('role');
                $mobile = $this->security->xss_clean($this->input->post('mobile'));
                
                $userInfo = array('email'=>$email, 'password'=>getHashedPassword($password), 'roleId'=>$roleId, 'name'=> $name,
                                    'mobile'=>$mobile, 'createdBy'=>$this->vendorId, 'createdDtm'=>date('Y-m-d H:i:s'));
                
                $this->load->model('user_model');
                $result = $this->user_model->addNewUser($userInfo);
                
                
                if($result > 0)
                {
                    $this->session->set_flashdata('success', 'New User created successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'User creation failed');
                }
                
                redirect('addNew');
            }
        }
    }

    
    /**
     * This function is used load user edit information
     * @param number $userId : Optional : This is user id
     */
    function editOld($userId = NULL)
    {
        if($this->isAdmin() == FALSE || $userId == 1)
        {
            $this->loadThis();
        }
        else
        {
            if($userId == null)
            {
                redirect('userListing');
            }
            
            $data['roles'] = $this->user_model->getUserRoles();
            $data['userInfo'] = $this->user_model->getUserInfo($userId);
            
            $this->global['pageTitle'] = 'PaceTel : Edit User';
            
            $this->loadViews("editOld", $this->global, $data, NULL);
        }
    }
    
    
    /**
     * This function is used to edit the user information
     */
    function editUser()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
            $userId = $this->input->post('userId');
            
            $this->form_validation->set_rules('fname','Full Name','trim|required|max_length[128]');
            $this->form_validation->set_rules('email','Email','trim|required|valid_email|max_length[128]');
            $this->form_validation->set_rules('password','Password','matches[cpassword]|max_length[20]');
            $this->form_validation->set_rules('cpassword','Confirm Password','matches[password]|max_length[20]');
            $this->form_validation->set_rules('role','Role','trim|required|numeric');
            $this->form_validation->set_rules('mobile','Mobile Number','required|min_length[10]');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->editOld($userId);
            }
            else
            {
                $name = ucwords(strtolower($this->security->xss_clean($this->input->post('fname'))));
                $email = strtolower($this->security->xss_clean($this->input->post('email')));
                $password = $this->input->post('password');
                $roleId = $this->input->post('role');
                $mobile = $this->security->xss_clean($this->input->post('mobile'));
                
                $userInfo = array();
                
                if(empty($password))
                {
                    $userInfo = array('email'=>$email, 'roleId'=>$roleId, 'name'=>$name,
                                    'mobile'=>$mobile, 'updatedBy'=>$this->vendorId, 'updatedDtm'=>date('Y-m-d H:i:s'));
                }
                else
                {
                    $userInfo = array('email'=>$email, 'password'=>getHashedPassword($password), 'roleId'=>$roleId,
                        'name'=>ucwords($name), 'mobile'=>$mobile, 'updatedBy'=>$this->vendorId, 
                        'updatedDtm'=>date('Y-m-d H:i:s'));
                }
                
                $result = $this->user_model->editUser($userInfo, $userId);
                
                if($result == true)
                {
                    $this->session->set_flashdata('success', 'User updated successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'User updation failed');
                }
                
                redirect('userListing');
            }
        }
    }


    /**
     * This function is used to delete the user using userId
     * @return boolean $result : TRUE / FALSE
     */
    function deleteUser()
    {
        if($this->isAdmin() == FALSE)
        {
            echo(json_encode(array('status'=>'access')));
        }
        else
        {
            $userId = $this->input->post('userId');
            $userInfo = array('isDeleted'=>1,'updatedBy'=>$this->vendorId, 'updatedDtm'=>date('Y-m-d H:i:s'));
            
            $result = $this->user_model->deleteUser($userId, $userInfo);
            
            if ($result > 0) { echo(json_encode(array('status'=>TRUE))); }
            else { echo(json_encode(array('status'=>FALSE))); }
        }
    }
    
    /**
     * Page not found : error 404
     */
    function pageNotFound()
    {
        $this->global['pageTitle'] = 'PaceTel : 404 - Page Not Found';
        
        $this->loadViews("404", $this->global, NULL, NULL);
    }

    /**
     * This function used to show login history
     * @param number $userId : This is user id
     */
    function loginHistoy($userId = NULL)
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $userId = ($userId == NULL ? 0 : $userId);

            $searchText = $this->input->post('searchText');
            $fromDate = $this->input->post('fromDate');
            $toDate = $this->input->post('toDate');

            $data["userInfo"] = $this->user_model->getUserInfoById($userId);

            $data['searchText'] = $searchText;
            $data['fromDate'] = $fromDate;
            $data['toDate'] = $toDate;
            
            $this->load->library('pagination');
            
            $count = $this->user_model->loginHistoryCount($userId, $searchText, $fromDate, $toDate);

            $returns = $this->paginationCompress ( "login-history/".$userId."/", $count, 10, 3);

            $data['userRecords'] = $this->user_model->loginHistory($userId, $searchText, $fromDate, $toDate, $returns["page"], $returns["segment"]);
            
            $this->global['pageTitle'] = 'PaceTel : User Login History';
            
            $this->loadViews("loginHistory", $this->global, $data, NULL);
        }        
    }

    /**
     * This function is used to show users profile
     */
    function profile($active = "details")
    {
        $data["userInfo"] = $this->user_model->getUserInfoWithRole($this->vendorId);
        $data["active"] = $active;
        
        $this->global['pageTitle'] = $active == "details" ? 'PaceTel : My Profile' : 'PaceTel : Change Password';
        $this->loadViews("profile", $this->global, $data, NULL);
    }

    /**
     * This function is used to update the user details
     * @param text $active : This is flag to set the active tab
     */
    function profileUpdate($active = "details")
    {
        $this->load->library('form_validation');
            
        $this->form_validation->set_rules('fname','Full Name','trim|required|max_length[128]');
        $this->form_validation->set_rules('mobile','Mobile Number','required|min_length[10]');
        $this->form_validation->set_rules('email','Email','trim|required|valid_email|max_length[128]|callback_emailExists');        
        
        if($this->form_validation->run() == FALSE)
        {
            $this->profile($active);
        }
        else
        {
            $name = ucwords(strtolower($this->security->xss_clean($this->input->post('fname'))));
            $mobile = $this->security->xss_clean($this->input->post('mobile'));
            $email = strtolower($this->security->xss_clean($this->input->post('email')));
            
            $userInfo = array('name'=>$name, 'email'=>$email, 'mobile'=>$mobile, 'updatedBy'=>$this->vendorId, 'updatedDtm'=>date('Y-m-d H:i:s'));
            
            $result = $this->user_model->editUser($userInfo, $this->vendorId);
            
            if($result == true)
            {
                $this->session->set_userdata('name', $name);
                $this->session->set_flashdata('success', 'Profile updated successfully');
            }
            else
            {
                $this->session->set_flashdata('error', 'Profile updation failed');
            }

            redirect('profile/'.$active);
        }
    }

    /**
     * This function is used to change the password of the user
     * @param text $active : This is flag to set the active tab
     */
    function changePassword($active = "changepass")
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('oldPassword','Old password','required|max_length[20]');
        $this->form_validation->set_rules('newPassword','New password','required|max_length[20]');
        $this->form_validation->set_rules('cNewPassword','Confirm new password','required|matches[newPassword]|max_length[20]');
        
        if($this->form_validation->run() == FALSE)
        {
            $this->profile($active);
        }
        else
        {
            $oldPassword = $this->input->post('oldPassword');
            $newPassword = $this->input->post('newPassword');
            
            $resultPas = $this->user_model->matchOldPassword($this->vendorId, $oldPassword);
            
            if(empty($resultPas))
            {
                $this->session->set_flashdata('nomatch', 'Your old password is not correct');
                redirect('profile/'.$active);
            }
            else
            {
                $usersData = array('password'=>getHashedPassword($newPassword), 'updatedBy'=>$this->vendorId,
                                'updatedDtm'=>date('Y-m-d H:i:s'));
                
                $result = $this->user_model->changePassword($this->vendorId, $usersData);
                
                if($result > 0) { $this->session->set_flashdata('success', 'Password updation successful'); }
                else { $this->session->set_flashdata('error', 'Password updation failed'); }
                
                redirect('profile/'.$active);
            }
        }
    }

    /**
     * This function is used to check whether email already exist or not
     * @param {string} $email : This is users email
     */
    function emailExists($email)
    {
        $userId = $this->vendorId;
        $return = false;

        if(empty($userId)){
            $result = $this->user_model->checkEmailExists($email);
        } else {
            $result = $this->user_model->checkEmailExists($email, $userId);
        }

        if(empty($result)){ $return = true; }
        else {
            $this->form_validation->set_message('emailExists', 'The {field} already taken');
            $return = false;
        }

        return $return;
    }

    /**
     * This function is used to load the Settings list
     */
    function settingListing()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {        
            $searchText = $this->security->xss_clean($this->input->post('searchText'));
            $data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            
            $count = $this->user_model->settingsListingCount($searchText);

			$returns = $this->paginationCompress ( "settings/", $count, 50 );
            
            $data['settingRecords'] = $this->user_model->settingsListing($searchText, $returns["page"], $returns["segment"]);
            
            $this->global['pageTitle'] = 'PaceTel : Setting Listing';
            
            $this->loadViews("settings", $this->global, $data, NULL);
        }
    }

    function settingAddNew()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            if($this->session->userdata ( 'name' ) == "admin"){
                $this->load->model('users_model');
                //$data['roles'] = $this->user_model->getUserRoles();
                $data['managerList'] = $this->users_model->getManagersList();
                
                $this->global['pageTitle'] = 'PaceTel : Add New Setting';

                $this->loadViews("settingsAddNew", $this->global, $data, NULL);
            }else{
                $this->settingListing();
            }
        }
    }

    function settingseditOld($stgId = NULL)
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            if($this->session->userdata ( 'name' ) == "admin" || $this->ismaster > 0){
                //$data['roles'] = $this->user_model->getUserRoles();
                $this->load->model('users_model');
                $data['settingsInfo'] = $this->user_model->getSettingsInfo($stgId);
                $data['managerList'] = $this->users_model->getManagersList();
                $this->global['pageTitle'] = 'PaceTel : Edit Settings';

                $this->loadViews("settingsEditOld", $this->global, $data, NULL);
            }
        }
    }

    /**
     * This function is used to add new Settings to the system
     */
    function saveNewSettings()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {

            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('stgname','Setting Name','trim|required|max_length[128]');
            $this->form_validation->set_rules('stgtype','Type','trim|required|max_length[128]');
            $this->form_validation->set_rules('stgvalue','Setting Value','trim|required|numeric');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->settingAddNew();
            }
            else
            {
                $stgname = $this->security->xss_clean($this->input->post('stgname'));
                $stgtype = $this->security->xss_clean($this->input->post('stgtype'));
                $stgvalue = $this->security->xss_clean($this->input->post('stgvalue'));
                $managername = $this->security->xss_clean($this->input->post('manager'));
                
                $settingsInfo = array('stgname'=>$stgname, 'stgtype'=>$stgtype, 'stgvalue'=>$stgvalue, 'managername'=>$managername);
                
                $this->load->model('user_model');

                $checkEntry = $this->user_model->chkSettingsInfo($stgname, $stgtype, $managername);
                if(empty($checkEntry)){
                    $result = $this->user_model->addNewSettings($settingsInfo);
                    
                    if($result > 0)
                    {
                        $this->session->set_flashdata('success', 'New Setting created successfully');
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Setting creation failed');
                    }
                    
                    redirect('settingsList');
                }else{  
                    $this->session->set_flashdata('Setting Violation', 'The {setting} already exists..'); 
                    $this->settingAddNew();
                }
            }
        }
    }

    function editSettings()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
            $stgid = $this->input->post('stgid');
            
            $this->form_validation->set_rules('stgname','Setting Name','trim|required|max_length[128]');
            $this->form_validation->set_rules('stgtype','Type','trim|required|max_length[128]');
            $this->form_validation->set_rules('stgvalue','Setting Value','trim|required|numeric');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->editOld($userId);
            }
            else
            {
                $stgid = $this->input->post('stgid');
                $stgname = $this->security->xss_clean($this->input->post('stgname'));
                $stgtype = $this->security->xss_clean($this->input->post('stgtype'));
                $stgvalue = $this->security->xss_clean($this->input->post('stgvalue'));
                $managername = $this->security->xss_clean($this->input->post('manager'));
                
                $settingsInfo = array('stgname'=>$stgname, 'stgtype'=>$stgtype, 'stgvalue'=>$stgvalue, 'managername'=>$managername);
                
                $result = $this->user_model->editSettings($settingsInfo, $stgid);
                
                if($result == true)
                {
                    $this->session->set_flashdata('success', 'Settings updated successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Settings updation failed');
                }
                
                redirect('settingsList');
            }
        }
    }

    function dashboardCustomer()
    {
        $this->global['pageTitle'] = 'PaceTel : Dashboard';
        
        $this->loadViews("dashboardCust", $this->global, NULL , NULL);
    }


    /**
     * CONTROL PANEL DMA
     */
    public function loginDMA()
    {

        $this->global['pageTitle'] = 'PaceTel : Dashboard';
        
        $this->loadViews("loginmaster", $this->global, NULL , NULL);

        //$isLoggedIn = $this->session->userdata('isLoggedIn');
        //$this->load->view('loginmaster');
        //redirect("Radiusmanager/admin.php");

    }

    public function profilelist(){
        $this->global['pageTitle'] = 'PaceTel : Profile List';
        
        $this->loadViews("cp_listservies", $this->global, NULL , NULL);

    }

    public function profileadd(){
        $this->global['pageTitle'] = 'PaceTel : Profile Add';
        
        $this->loadViews("cp_newservice", $this->global, NULL , NULL);

    }

    public function naslist(){
        $this->global['pageTitle'] = 'PaceTel : NAS List';
        
        $this->loadViews("cp_listnas", $this->global, NULL , NULL);

    }
    public function nasnew(){
        $this->global['pageTitle'] = 'PaceTel : NAS List';
        
        $this->loadViews("cp_newnas", $this->global, NULL , NULL);

    }

    public function overalltrafficreport(){
        $this->global['pageTitle'] = 'PaceTel : Overall Traffic';
        
        $this->loadViews("cp_overalltrafficreport", $this->global, NULL , NULL);

    }

    public function findtrafficdata(){
        $this->global['pageTitle'] = 'PaceTel : Traffic Data';
        
        $this->loadViews("cp_findtrafficdata", $this->global, NULL , NULL);

    }

    public function trafficsummery(){
        $this->global['pageTitle'] = 'PaceTel : Traffic Summery';
        
        $this->loadViews("cp_trafficsummery", $this->global, NULL , NULL);

    }

    public function systemsettings(){
        $this->global['pageTitle'] = 'PaceTel : System Setting';
        
        $this->loadViews("cp_settings", $this->global, NULL , NULL);

    }

    public function radiusonlineusers(){
        $this->global['pageTitle'] = 'PaceTel : Radius Online Users';
        
        $this->loadViews("cp_radiusonline", $this->global, NULL , NULL);

    }

    public function mainuserscontrol(){
        $this->global['pageTitle'] = 'PaceTel : Main User Control';
        
        $this->loadViews("cp_mainuserscontrol", $this->global, NULL , NULL);

    }

    public function userProfile(){
        $this->global['pageTitle'] = 'PaceTel : Main User Control';
        
        $user = $this->input->post('username');

        $data['username'] = $user;
        $this->loadViews("cp_userprofile", $this->global, $data , NULL);

    }

    public function hotspot(){
        //$this->global['pageTitle'] = 'Pace Radius : Hotspot Connect';
        
        //$this->loadViews("master/hotspot.php", $this->global, NULL , NULL);
        redirect('master/hotspot.php');

    }

    /**
     * Get settings for a manager
     */
    public function getManagerSettings()
    {
        header('Content-Type: application/json');
        
        if (!$this->input->is_ajax_request()) {
            echo json_encode(['status' => false, 'message' => 'Invalid request']);
            return;
        }

        $managername = $this->security->xss_clean($this->input->post('managername'));
        
        if (empty($managername)) {
            echo json_encode(['status' => false, 'message' => 'Manager name is required']);
            return;
        }

        $settings = $this->user_model->getManagerSettings($managername);
        
        echo json_encode([
            'status' => true,
            'settings' => $settings ? $settings : []
        ]);
    }

    /**
     * Add new setting for manager via AJAX
     */
    public function settingAddNewManager()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            log_message('info', "settings-update-manager: ");
            $stgtype = $this->security->xss_clean($this->input->post('stgtype'));
            $stgvalue = $this->security->xss_clean($this->input->post('stgvalue'));
            $stgname = $this->security->xss_clean($this->input->post('stgname'));
            $managername = $this->security->xss_clean($this->input->post('managername'));

            $loggedInUser = $this->session->userdata ( 'name' );

            log_message('info', "settings-update-manager: ".$loggedInUser);
            
            $settingInfo = array(
                'stgtype' => $stgtype,
                'stgvalue' => $stgvalue,
                'managername' => $managername,
                'stgname' => $stgname
            );

            $checkEntry = $this->user_model->chkSettingsInfo('SETTING_' . strtoupper($stgtype), $stgtype, $managername);
            if(empty($checkEntry)){
                
                $result = $this->user_model->settingAddNewManager($settingInfo);
                
                if($result > 0)
                {
                    $response = array(
                        'status' => TRUE,
                        'message' => 'Setting added successfully'
                    );
                    log_message('info', "settings-update-manager: ".$stgtype." added by: ".$this->session->userdata ( 'name' )." for manager: ".$managername);
                }
                else
                {
                    $response = array(
                        'status' => FALSE,
                        'message' => 'Failed to add setting'
                    );
                    log_message('error', "settings-update-manager: ".$stgtype." addition failed by: ".$this->session->userdata ( 'name' )." for manager: ".$managername);
                }

            }else{
                log_message('error', "settings-update-manager: failed to update ".$managername);
                $response = array(
                    'status' => FALSE,
                    'message' => 'Setting already exists'
                );
            }
            
            echo json_encode($response);
        }
    }

    /**
     * Delete manager setting via AJAX
     */
    public function deleteManagerSetting()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $stgid = $this->security->xss_clean($this->input->post('stgid'));
            
            $result = $this->user_model->deleteManagerSetting($stgid);
            
            if($result > 0)
            {
                $response = array(
                    'status' => TRUE,
                    'message' => 'Setting deleted successfully'
                );

                log_message('info', "manager-settings-delete: Manager Settings ID ".$stgid." deleted by: ".$this->session->userdata ( 'name' ));
            }
            else
            {
                $response = array(
                    'status' => FALSE,
                    'message' => 'Failed to delete setting'
                );

                log_message('error', "manager-settings-delete: ".$stgid." deletion failed by: ".$this->session->userdata ( 'name' ));
            }
            
            echo json_encode($response);
        }
    }

    public function ajaxDashboardInfo()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
            return;
        }
        $managername = $this->session->userdata('name');
        // Get domain prefix for cache key
        $domain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (getenv('APP_HOSTNAME') ?: 'localhost');
        $domain_prefix = str_replace('.', '_', $domain);
        $cache_key = $domain_prefix . '_dashboardinfo_' . $managername;
        $this->load->driver('cache', array('adapter' => 'file'));
        $force_refresh = $this->input->get('refresh');
        if ($force_refresh) {
            $data = $this->getDashBoardInfo();
            $data['last_updated'] = date('Y-m-d H:i:s');
            $this->cache->save($cache_key, $data, 300);
        } else {
            $data = $this->cache->get($cache_key);
            if ($data === FALSE) {
                $data = $this->getDashBoardInfo();
                $data['last_updated'] = date('Y-m-d H:i:s');
                $this->cache->save($cache_key, $data, 300);
            }
        }
        echo json_encode($data);
    }

}

?>
<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class : Login (LoginController)
 * Login class to control to authenticate user credentials and starts user's session.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */

/**
 * @property Login_model $login_model
 * @property CI_Form_validation $form_validation
 * @property CI_Session $session
 * @property CI_Security $security
 * @property CI_Input $input
 * @property CI_User_agent $agent
 * @property CI_Loader $load
 */
class Login extends CI_Controller
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model');

    }

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        
        $this->isLoggedIn();

    }
    
    /**
     * This function used to check the user is logged in or not
     */
    function isLoggedIn()
    {
        $isLoggedIn = $this->session->userdata('isLoggedIn');
        
        if(!isset($isLoggedIn) || $isLoggedIn != TRUE)
        {
            $this->load->view('login');
        }
        else
        {
            // Run jobs queue before redirecting to dashboard
            //$this->load->model('Other_model');
            //$this->Other_model->runDueJobs();
            if($this->session->userdata('name') <> 'admin'){
                redirect('/dashboard');
            }else{
                redirect('/Network_controller/userDashboard');
            }
        }
    }

    public function loginManager()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('username', 'User Name', 'required|max_length[128]|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|max_length[32]');

        if($this->form_validation->run() == FALSE)
        {
            $this->index();
        }
        else
        {
            $username = strtolower($this->security->xss_clean($this->input->post('username')));
            $password = $this->input->post('password');
            $result = $this->login_model->loginManager($username, $password);

            if(empty($result)){
                $result = $this->login_model->loginProfile($username, $password);

                if(!empty($result)){
                    $username = $result->managername;
                    $login_type = 'profile';
                }else{
                    log_message('error', 'Invalid login attempt with username '.$username.' from IP: '.$_SERVER['REMOTE_ADDR']);
                    $this->session->set_flashdata('error', 'Username or password mismatch');
                    //$this->index();
                    redirect('/login');
                }
            }else{
                $login_type = 'manager';
            }

            if(!empty($result))
            {

                $lastLogin = $this->login_model->lastLoginInfo($result->managername);
                $submanagers = $this->login_model->lisMasterManager($result->managername);

                //$accountmanager = $this->login_model->getSettingInfo($result->managername, 'ALLOW-ACCOUNTS');
                $smsmanager = $this->login_model->getSettingInfo($result->managername, 'ALLOW-SMS');
                $bulksmsmanager = $this->login_model->getSettingInfo($result->managername, 'ALLOW-BULK-SMS');
                $postpaidmanager = $this->login_model->getSettingInfo($result->managername, 'POSTPAID-MANAGER');

                $manager_chain = array();
                if($submanagers->managercount > 0){
                    $manager_chain = $this->login_model->master_chain($result->managername);
                }

                

                $sessionArray = array('managername'=>$result->managername,                    
                                        'name'=>$result->managername,
                                        'login_type'=>$login_type,
                                        'roleId'=>($login_type == 'profile') ? $result->roleId : 0,
                                        'profileid'=>($login_type == 'profile') ? $result->profileid : $result->managername,
                                        'lastLogin'=> $lastLogin->createdDtm,
                                        'issubmanagers'=> $submanagers->managercount,
                                        'manager_chain' => $manager_chain,
                                        'isaccountmanager'=> $result->perm_allowaccounts,
                                        'issmsmanager'=> $smsmanager->status,
                                        'isbulksmsmanager'=> $bulksmsmanager->status,
                                        'ispostpaidmanager'=> $postpaidmanager->status,
                                        'isLoggedIn' => TRUE
                                );

                $this->session->set_userdata($sessionArray);

                unset($sessionArray['managername'], $sessionArray['isLoggedIn'], $sessionArray['lastLogin']);

                $loginInfo = array("managername"=>$result->managername, "sessionData" => json_encode($sessionArray), "machineIp"=>$_SERVER['REMOTE_ADDR'], "userAgent"=>getBrowserAgent(), "agentString"=>$this->agent->agent_string(), "platform"=>$this->agent->platform());
                log_message('info', 'Login Information with username '.$username.' from IP: '.$_SERVER['REMOTE_ADDR']);
                $this->login_model->lastLogin($loginInfo);

                //redirect('/dashboard');
                //redirect('/Network_controller/userDashboard');

                if($result->managername <> 'admin'){
                    redirect('/dashboard');
                }else{
                    redirect('/Network_controller/userDashboard');
                }
            }
            else
            {
                log_message('error', 'Invalid login attempt with username '.$username.' from IP: '.$_SERVER['REMOTE_ADDR']);
                $this->session->set_flashdata('error', 'Username or password mismatch');
                $this->index();
            }

        }

    }
    
    
    /**
     * This function used to logged in user
     */
    public function loginMe()
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|max_length[128]|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|max_length[32]');
        
        if($this->form_validation->run() == FALSE)
        {
            $this->index();
        }
        else
        {
            $email = strtolower($this->security->xss_clean($this->input->post('email')));
            $password = $this->input->post('password');
            
            $result = $this->login_model->loginMe($email, $password);
            
            if(!empty($result))
            {
                $lastLogin = $this->login_model->lastLoginInfo($result->userId);

                $sessionArray = array('userId'=>$result->userId,                    
                                        'role'=>$result->roleId,
                                        'roleText'=>$result->role,
                                        'name'=>$result->name,
                                        'lastLogin'=> $lastLogin->createdDtm,
                                        'isLoggedIn' => TRUE
                                );

                $this->session->set_userdata($sessionArray);

                unset($sessionArray['userId'], $sessionArray['isLoggedIn'], $sessionArray['lastLogin']);

                $loginInfo = array("userId"=>$result->userId, "sessionData" => json_encode($sessionArray), "machineIp"=>$_SERVER['REMOTE_ADDR'], "userAgent"=>getBrowserAgent(), "agentString"=>$this->agent->agent_string(), "platform"=>$this->agent->platform());

                $this->login_model->lastLogin($loginInfo);
                
                redirect('/dashboard');
            }
            else
            {
                $this->session->set_flashdata('error', 'Email or password mismatch');
                
                $this->index();
            }
        }
    }

    /**
     * This function used to load forgot password view
     */
    public function forgotPassword()
    {
        $isLoggedIn = $this->session->userdata('isLoggedIn');
        
        if(!isset($isLoggedIn) || $isLoggedIn != TRUE)
        {
            $this->load->view('forgotPassword');
        }
        else
        {
            redirect('/dashboard');
        }
    }
    
    /**
     * This function used to generate reset password request link
     */
    function resetPasswordUser()
    {
        $status = '';
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('login_email','Email','trim|required|valid_email');
                
        if($this->form_validation->run() == FALSE)
        {
            $this->forgotPassword();
        }
        else 
        {
            $email = strtolower($this->security->xss_clean($this->input->post('login_email')));
            
            if($this->login_model->checkEmailExist($email))
            {
                $encoded_email = urlencode($email);
                
                $this->load->helper('string');
                $data['email'] = $email;
                $data['activation_id'] = random_string('alnum',15);
                $data['createdDtm'] = date('Y-m-d H:i:s');
                $data['agent'] = getBrowserAgent();
                $data['client_ip'] = $this->input->ip_address();
                
                $save = $this->login_model->resetPasswordUser($data);                
                
                if($save)
                {
                    $data1['reset_link'] = base_url() . "resetPasswordConfirmUser/" . $data['activation_id'] . "/" . $encoded_email;
                    $userInfo = $this->login_model->getCustomerInfoByEmail($email);

                    if(!empty($userInfo)){
                        $data1["name"] = $userInfo->name;
                        $data1["email"] = $userInfo->email;
                        $data1["message"] = "Reset Your Password";
                    }

                    $sendStatus = resetPasswordEmail($data1);

                    if($sendStatus){
                        $status = "send";
                        setFlashData($status, "Reset password link sent successfully, please check mails.");
                    } else {
                        $status = "notsend";
                        setFlashData($status, "Email has been failed, try again.");
                    }
                }
                else
                {
                    $status = 'unable';
                    setFlashData($status, "It seems an error while sending your details, try again.");
                }
            }
            else
            {
                $status = 'invalid';
                setFlashData($status, "This email is not registered with us.");
            }
            redirect('/forgotPassword');
        }
    }

    /**
     * This function used to reset the password 
     * @param string $activation_id : This is unique id
     * @param string $email : This is user email
     */
    function resetPasswordConfirmUser($activation_id, $email)
    {
        // Get email and activation code from URL values at index 3-4
        $email = urldecode($email);
        
        // Check activation id in database
        $is_correct = $this->login_model->checkActivationDetails($email, $activation_id);
        
        $data['email'] = $email;
        $data['activation_code'] = $activation_id;
        
        if ($is_correct == 1)
        {
            $this->load->view('newPassword', $data);
        }
        else
        {
            redirect('/login');
        }
    }
    
    /**
     * This function used to create new password for user
     */
    function createPasswordUser()
    {
        $status = '';
        $message = '';
        $email = strtolower($this->input->post("email"));
        $activation_id = $this->input->post("activation_code");
        
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('password','Password','required|max_length[20]');
        $this->form_validation->set_rules('cpassword','Confirm Password','trim|required|matches[password]|max_length[20]');
        
        if($this->form_validation->run() == FALSE)
        {
            $this->resetPasswordConfirmUser($activation_id, urlencode($email));
        }
        else
        {
            $password = $this->input->post('password');
            $cpassword = $this->input->post('cpassword');
            
            // Check activation id in database
            $is_correct = $this->login_model->checkActivationDetails($email, $activation_id);
            
            if($is_correct == 1)
            {                
                $this->login_model->createPasswordUser($email, $password);
                
                $status = 'success';
                $message = 'Password reset successfully';
            }
            else
            {
                $status = 'error';
                $message = 'Password reset failed';
            }
            
            setFlashData($status, $message);

            redirect("/login");
        }
    }
}

?>
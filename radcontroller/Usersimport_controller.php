<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016 2017
 */
class Usersimport_controller extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Services_model');
        $this->load->model('Users_model');
        //$this->load->library('csvimport');
        $this -> load -> library('form_validation');
        $this->isLoggedIn();   
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->global['pageTitle'] = 'Users List Import : Dashboard';
        
        $this->loadViews("dashboard", $this->global, NULL , NULL);
    }

    function userLists_upload()
    {

        $data['packages'] = $this->Users_model->getPackages();
        $data['managername'] = $this->Users_model->getManagersList();
     
        $this->global['pageTitle'] = 'Pace-Tel : Accounts';

        $this->loadViews("usersimport", $this->global, $data, NULL);
    }


    function importcsvfile($status = NULL){

        //$this->load->view('import_data');
		if(isset($_POST["submit"]))
		{
            
            $managername = $this->security->xss_clean($this->input->post('manager'));
            $srvid = $this->security->xss_clean($this->input->post('service'));

            $effected_rows = null;

            $file = $_FILES['file']['tmp_name'];

			$handle = fopen($file, "r");
			$c = 0;//

            

            while(($filesop = fgetcsv($handle, 1000, ",")) !== false)
            {

                $username = filter_var(str_replace(' ', '', !empty($filesop[0]) ? $filesop[0] : ''), FILTER_SANITIZE_STRING);
                $password = filter_var(str_replace(' ', '', !empty($filesop[1]) ? $filesop[1] : ''), FILTER_SANITIZE_STRING);
                $firstname = filter_var(str_replace(' ', '', !empty($filesop[2]) ? $filesop[2] : ''), FILTER_SANITIZE_STRING);
                $lastname = filter_var(str_replace(' ', '', !empty($filesop[3]) ? $filesop[3] : ''), FILTER_SANITIZE_STRING);
                $address = filter_var(str_replace(' ', '', !empty($filesop[4]) ? $filesop[4] : ''), FILTER_SANITIZE_STRING);
                $emailaddress = filter_var(str_replace(' ', '', !empty($filesop[5]) ? $filesop[5] : ''), FILTER_VALIDATE_EMAIL);
                $mobileno = filter_var(str_replace(' ', '', !empty($filesop[6]) ? $filesop[6] : ''), FILTER_SANITIZE_STRING);
                $cnic = filter_var(str_replace(' ', '', !empty($filesop[7]) ? $filesop[7] : ''), FILTER_SANITIZE_STRING);

                $username = strlen($username) <= 64 ? $username : '';
                $password = strlen($password) <= 32 ? $password : '';
                $firstname = strlen($firstname) <= 50 ? $firstname : '';
                $lastname = strlen($lastname) <= 50 ? $lastname : '';
                $address = strlen($address) <= 100 ? $address : '';
                $emailaddress = strlen($emailaddress) <= 100 ? $emailaddress : '';
                $mobileno = strlen($mobileno) <= 15 ? $mobileno : '';
                $cnic = strlen($cnic) <= 40 ? $cnic : '';


                if($c<>0){

                    if(!empty($username) &&  !empty($password) && !empty($firstname) && !empty($lastname) && !empty($mobileno)){

                        //echo ($username.' - '.$password.' - '.$firstname.' - '.$lastname.' - '.$address.' - '.$emailaddress.' - '.$mobileno.' - '.$cnic);
                        //exit;

                        $result = $this->Users_model->checkUsernameExists($username);
                        if(empty($result))
                        {
                            $userInfo = array('username'=>$username, 'password'=>$password, 'groupid'=>1, 'enableuser'=> 1,
                                            'uplimit'=>0, 'downlimit'=>0, 'comblimit'=>0,
                                            'firstname'=>$firstname, 'lastname'=>$lastname, 'address'=>$address,
                                            'mobile'=>$mobileno, 'email'=>$emailaddress, 'taxid'=>$cnic,
                                            'gpslat'=>0.00000000000000, 'gpslong'=>0.00000000000000,
                                            'usemacauth'=>0, 'expiration'=>date('Y-m-d'), 'uptimelimit'=>0, 'srvid'=>$srvid, 
                                            'ipmodecm'=>0, 'ipmodecpe'=>0, 'poolidcm'=>0, 'poolidcpe'=>0,
                                            'createdon'=>date('Y-m-d'), 'acctype'=>0, 'credits'=>0.00, 'cardfails'=>0,
                                            'createdby'=>$managername,
                                            'owner'=>$managername,
                                            'warningsent'=>0, 'verified'=>0, 'selfreg'=>0, 'verifyfails'=>0, 'verifysentnum'=>0,
                                            'contractvalid'=>'0000-00-00', 'pswactsmsnum'=>0, 'alertemail'=>0, 'alertsms'=>0,
                                            'custattr'=>'Mikrotik-Address-List := '.$managername,
                                            'lang'=>'English');

                            $radpassword = array('username'=>$username, 'attribute'=>'Cleartext-Password', 'op'=>':=', 'value'=>$password);
                            $radsimuse = array('username'=>$username, 'attribute'=>'Simultaneous-Use', 'op'=>':=', 'value'=>'1');

                            if($this->Users_model->checkUserExist($username) == FALSE)
                            {
                                //if($status == 'insertdata'){
                                    //$result = $this->Users_model->addNewUser($userInfo, $radpassword, $radsimuse);
                                //}
                                //$this->session->set_flashdata('success', $username.' New User created successfully');
                                $effected_rows[$c] = $userInfo;

                            }
                        }
                        else
                        {
                            //$this->session->set_flashdata('success', $username.' Invalid Data');
                        }
                    }
                }

                $c = $c + 1;
            }
            

            $data['userdata'] = $effected_rows;
            $data['manager'] = $managername;
            $data['service_id'] = $srvid;
            $data['file'] = $file;


            if($status == 'review'){
                $this->loadViews("usersimportreview", $this->global, $data, NULL);
            }elseif($status == 'insertdata'){
                redirect('usersListing');
            }
		
		}

    }

    function usersCreateNewFromCSV(){

        $managername = $this->input->post('manager_id');
        $srvid = $this->input->post('service_id');

        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $firstname = $this->input->post('firstname');
        $lastname = $this->input->post('lastname');
        $address = $this->input->post('address');
        $mobileno = $this->input->post('mobile');
        $cnic = $this->input->post('cnic');
        $emailaddress = "support@pace-tel.com";

        //$data = $username.' - '.$password.' - '.$firstname.' - '.$lastname.' - '.$address.' - '.$emailaddress.' - '.$mobileno.' - '.$cnic;
        //$info = "username:".$username;

        $result = $this->Users_model->checkUsernameExists($username);
        if(empty($result))
        {
            $userInfo = array('username'=>$username, 'password'=>MD5($password), 'groupid'=>1, 'enableuser'=> 1,
                            'uplimit'=>0, 'downlimit'=>0, 'comblimit'=>0,
                            'firstname'=>$firstname, 'lastname'=>$lastname, 'address'=>$address,
                            'mobile'=>$mobileno, 'email'=>$emailaddress, 'taxid'=>$cnic,
                            'gpslat'=>0.00000000000000, 'gpslong'=>0.00000000000000,
                            'usemacauth'=>0, 'expiration'=>date('Y-m-d'), 'uptimelimit'=>0, 'srvid'=>$srvid, 
                            'ipmodecm'=>0, 'ipmodecpe'=>0, 'poolidcm'=>0, 'poolidcpe'=>0,
                            'createdon'=>date('Y-m-d'), 'acctype'=>0, 'credits'=>0.00, 'cardfails'=>0,
                            'createdby'=>$managername,
                            'owner'=>$managername,
                            'warningsent'=>0, 'verified'=>0, 'selfreg'=>0, 'verifyfails'=>0, 'verifysentnum'=>0,
                            'contractvalid'=>'0000-00-00', 'pswactsmsnum'=>0, 'alertemail'=>0, 'alertsms'=>0,
                            'custattr'=>'Mikrotik-Address-List := '.$managername,
                            'lang'=>'English');

            $radpassword = array('username'=>$username, 'attribute'=>'Cleartext-Password', 'op'=>':=', 'value'=>$password);
            $radsimuse = array('username'=>$username, 'attribute'=>'Simultaneous-Use', 'op'=>':=', 'value'=>'1');

            if($this->Users_model->checkUserExist($username) == FALSE)
            {
                $result = $this->Users_model->addNewUser($userInfo, $radpassword, $radsimuse);
                //$this->session->set_flashdata('success', $username.' New User created successfully');
            }
        }

        echo json_encode($data);

    }

    function billInquiry(){

        $request_data = json_decode($this->input->raw_input_stream, true);

        $username = $request_data['username'];
        $password = $request_data['password'];
        $consumer_number = $request_data['Consumer_number'];
        $bank_mnemonic = $request_data['Bank_Mnemonic'];
        $reserved = $request_data['Reserved'];

        $info = $username.": u2nasir ";
        
        echo json_encode($username);
    }


    function getManagerPackages()
    {

        $managername = $this->input->post('manager_id');
        $data = $this->Users_model->getPackagesByManager($managername);

        echo json_encode($data);

    }

    // ************************************ //
    // ************************************ //
    // ************************************ //
    // ***** Cards Import From CSV ************** //
    function cardsLists_upload()
    {

        $data['packages'] = $this->Users_model->getPackages();
        $data['managername'] = $this->Users_model->getManagersList();
        $data['usersGroup'] = $this->Users_model->getUsersGroup();
     
        $this->global['pageTitle'] = 'Pace-Tel : Accounts';

        $this->loadViews("Prepaid/cardsimport", $this->global, $data, NULL);
    }

    function importcardsfromcsvfile($status = NULL){

        //$this->load->view('import_data');
		if(isset($_POST["submit"]))
		{
            
            $managername = $this->security->xss_clean($this->input->post('managername'));
            $srvid = $this->security->xss_clean($this->input->post('service'));
            $grpid = $this->input->post('usergroup');

            // Card Properties
            $downlimit = $this->security->xss_clean($this->input->post('downlimit'));
            $uplimit = $this->security->xss_clean($this->input->post('uplimit'));
            $comblimit = $this->security->xss_clean($this->input->post('comblimit'));
            $uptimelimit = $this->security->xss_clean($this->input->post('uptimelimit'));
            $expiremode = $this->security->xss_clean($this->input->post('expiremode'));
            $expiretime = $this->security->xss_clean($this->input->post('expiretime'));
            $timebaseexp = $this->security->xss_clean($this->input->post('timebaseexp'));
            
            $effected_rows = null;

            $file = $_FILES['file']['tmp_name'];

			$handle = fopen($file, "r");
			$c = 0;

            while(($filesop = fgetcsv($handle, 5000, ",")) !== false)
            {

                $username = filter_var(str_replace(' ', '', !empty($filesop[0]) ? $filesop[0] : ''), FILTER_SANITIZE_STRING);
                $password = filter_var(str_replace(' ', '', !empty($filesop[1]) ? $filesop[1] : ''), FILTER_SANITIZE_STRING);
                $expiration = filter_var(str_replace(' ', '', !empty($filesop[2]) ? $filesop[2] : ''), FILTER_SANITIZE_STRING);
                $firstname = 'CSV';
                $lastname = 'Cards Import';
                $address = 'Cards Import By '.$managername;
                $emailaddress = '';
                $mobileno = '';
                $cnic = '';

                $username = strlen($username) <= 64 ? $username : '';
                $password = strlen($password) <= 32 ? $password : '';
                $expiration = strlen($expiration) <= 32 ? $expiration : '';

                if(empty($expiration)){
                    $expiration = $this->security->xss_clean($this->input->post('expiration'));
                }

                if($c<>0){

                    if(!empty($username)){

                        //echo ($username.' - '.$password.' - '.$firstname.' - '.$lastname.' - '.$address.' - '.$emailaddress.' - '.$mobileno.' - '.$cnic);
                        //exit;

                        $result = $this->Users_model->checkUsernameExists($username);
                        if(empty($result))
                        {
                            $userInfo = array('username'=>$username, 'password'=>$password, 'groupid'=>$grpid, 'enableuser'=> 1,
                                            'uplimit'=>0, 'downlimit'=>0, 'comblimit'=>0,
                                            'firstname'=>$firstname, 'lastname'=>$lastname, 'address'=>$address,
                                            'mobile'=>$mobileno, 'email'=>$emailaddress, 'taxid'=>$cnic,
                                            'gpslat'=>0.00000000000000, 'gpslong'=>0.00000000000000,
                                            'usemacauth'=>0, 'expiration'=>$expiration, 'uptimelimit'=>0, 'srvid'=>$srvid, 
                                            'ipmodecm'=>0, 'ipmodecpe'=>0, 'poolidcm'=>0, 'poolidcpe'=>0,
                                            'createdon'=>date('Y-m-d'), 'acctype'=>2, 'credits'=>0.00, 'cardfails'=>0,
                                            'createdby'=>$managername,
                                            'owner'=>$managername,
                                            'warningsent'=>0, 'verified'=>1, 'selfreg'=>0, 'verifyfails'=>0, 'verifysentnum'=>0,
                                            'contractvalid'=>'0000-00-00', 'pswactsmsnum'=>0, 'alertemail'=>0, 'alertsms'=>0,
                                            'custattr'=>'Mikrotik-Address-List := '.$managername,
                                            'lang'=>'English');

                            $radpassword = array('username'=>$username, 'attribute'=>'Cleartext-Password', 'op'=>':=', 'value'=>$password);
                            $radsimuse = array('username'=>$username, 'attribute'=>'Simultaneous-Use', 'op'=>':=', 'value'=>'1');

                            if($this->Users_model->checkUserExist($username) == FALSE)
                            {
                                $effected_rows[$c] = $userInfo;
                            }
                        }
                    }
                }

                $c = $c + 1;
            }

            $cardseries = $this->Users_model->genCardSeriesNumber();
            $cardseries = date('Y').'-'.sprintf('%04d', $cardseries->series);

            $data['userdata'] = $effected_rows;
            $data['manager'] = $managername;
            $data['service_id'] = $srvid;
            $data['groupid'] = $grpid;
            $data['file'] = $file;

            $data['downlimit'] = $downlimit;
            $data['uplimit'] = $uplimit;
            $data['comblimit'] = $comblimit;
            $data['uptimelimit'] = $uptimelimit;
            $data['expiremode'] = $expiremode;
            $data['expiretime'] = $expiretime;
            $data['timebaseexp'] = $timebaseexp;
            $data['cardseries'] = $cardseries;

            if($status == 'review'){
                $this->loadViews("Prepaid/cardsimportreview", $this->global, $data, NULL);
            }elseif($status == 'insertdata'){
                redirect('usersListing');
            }
		}
    }

    function cardsCreateNewFromCSV(){

        $managername = $this->input->post('manager_id');
        $srvid = $this->input->post('service_id');
        $groupid = $this->input->post('groupid');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $expiration = $this->input->post('expiration');
        $firstname = 'CSV';
        $lastname = 'Cards Import';
        $address = 'Cards Import By '.$managername;
        $emailaddress = '';
        $mobileno = '';
        $cnic = '';

        $downlimit = $this->input->post('downlimit');
        $uplimit = $this->input->post('uplimit');
        $comblimit = $this->input->post('comblimit');
        $uptimelimit = $this->input->post('uptimelimit');
        $expiremode = $this->input->post('expiremode');
        $expiretime = $this->input->post('expiretime');
        $timebaseexp = $this->input->post('timebaseexp');
        $cardseries = $this->input->post('cardseries');

        //$data = $username.' - '.$password.' - '.$firstname.' - '.$lastname.' - '.$address.' - '.$emailaddress.' - '.$mobileno.' - '.$cnic;
        //$info = "username:".$username;

        $result = $this->Users_model->checkUsernameExists($username);
        if(empty($result))
        {
            $userInfo = array('username'=>$username, 'password'=>MD5($password), 'groupid'=>$groupid, 'enableuser'=> 1,
                            'uplimit'=>$uplimit, 'downlimit'=>$downlimit, 'comblimit'=>$comblimit,
                            'firstname'=>$firstname, 'lastname'=>$lastname, 'address'=>$address,
                            'mobile'=>$mobileno, 'email'=>$emailaddress, 'taxid'=>$cnic,
                            'gpslat'=>0.00000000000000, 'gpslong'=>0.00000000000000,
                            'usemacauth'=>0, 'expiration'=>$expiration, 'uptimelimit'=>$uptimelimit, 'srvid'=>$srvid, 
                            'ipmodecm'=>0, 'ipmodecpe'=>0, 'poolidcm'=>0, 'poolidcpe'=>0,
                            'createdon'=>date('Y-m-d'), 'acctype'=>2, 'credits'=>0.00, 'cardfails'=>0,
                            'createdby'=>$managername,
                            'owner'=>$managername,
                            'warningsent'=>0, 'verified'=>1, 'selfreg'=>0, 'verifyfails'=>0, 'verifysentnum'=>0,
                            'contractvalid'=>'0000-00-00', 'pswactsmsnum'=>0, 'alertemail'=>0, 'alertsms'=>0,
                            'custattr'=>'Mikrotik-Address-List := '.$managername,
                            'lang'=>'English');

            $radpassword = array('username'=>$username, 'attribute'=>'Cleartext-Password', 'op'=>':=', 'value'=>$password);
            $radsimuse = array('username'=>$username, 'attribute'=>'Simultaneous-Use', 'op'=>':=', 'value'=>'1');

            // ******* Generate Card Series Information ********//
            $cardNextID = $this->Users_model->getMaxId();
            $cardvalue = $this->Services_model->getPackageDetails($managername, $srvid);

            //$cardseries = $this->Users_model->genCardSeriesNumber();
            //$cardseries = date('Y').'-'.sprintf('%04d', $cardseries);

            $cardsInfo = array('id'=>$cardNextID->id + 1,
                                'cardnum'=>$username,
                                'password'=>$password,
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

            if($this->Users_model->checkUserExist($username) == FALSE)
            {
                $this->Users_model->addNewCard($cardsInfo);
                $result = $this->Users_model->addNewUser($userInfo, $radpassword, $radsimuse);
                //$this->session->set_flashdata('success', $username.' New User created successfully');
            }
        }
        
        echo json_encode($data);
    }

    function getRandomStronString($length = 13) {

        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $string = '';
    
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }

        return $string;

    }


}
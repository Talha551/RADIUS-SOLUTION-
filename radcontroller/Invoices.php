<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

class Invoices extends BaseController
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
        $this->load->helper(array('form', 'url'));
        $this->isLoggedIn();   
    }

    public function index()
    {
        $this->global['pageTitle'] = 'Invoices List : Dashboard';
        
        $this->loadViews("dashboard", $this->global, NULL , NULL);
    }

    public function invoiceListing($page = 1)
    {
        $this->load->model('invoices_model');
        
        $searchText = $this->input->post('searchText');
        $searchUsername = $this->input->post('searchUsername');
        $searchRenewDate = $this->input->post('searchRenewDate');
        $searchRenewDateTo = $this->input->post('searchRenewDateTo');
        $searchManager = $this->input->post('searchManager');
        
        // Set default dates if not provided (first page load)
        if (empty($searchRenewDate)) {
            $searchRenewDate = date('Y-m-d', strtotime('-1 month'));
        }
        if (empty($searchRenewDateTo)) {
            $searchRenewDateTo = date('Y-m-d');
        }
        
        $data['searchText'] = $searchText;
        $data['searchUsername'] = $searchUsername;
        $data['searchRenewDate'] = $searchRenewDate;
        $data['searchRenewDateTo'] = $searchRenewDateTo;
        $data['searchManager'] = $searchManager;
        
        $this->load->library('pagination');
        
        $count = $this->invoices_model->invoiceListingCount($searchText, $searchUsername, $searchRenewDate, $searchRenewDateTo, $searchManager);
        
        $returns = $this->paginationCompress("invoiceListing/", $count, 10);

        $data['invoiceRecords'] = $this->invoices_model->invoiceListing($searchText, $searchUsername, $searchRenewDate, $searchRenewDateTo, $returns["page"], $returns["segment"], $searchManager);
        
        // Get usernames for dropdown
        $data['usernames'] = $this->invoices_model->getUsernamesList();
        
        if ($this->session->userdata('name') == 'admin' || $this->ismaster > 0) {
            $data['managerList'] = $this->users_model->getManagersList();
        }

        $data['grand_totals'] = $this->invoices_model->invoiceListingSummery($searchText, $searchUsername, $searchRenewDate, $searchRenewDateTo, $searchManager);
        
        $this->global['pageTitle'] = 'Invoices';
        
        $this->loadViews("invoiceslist", $this->global, $data, NULL);
    }

    function invoiceAddNew($username = NULL)
    {

        if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(3, 4))){
            $this->session->set_flashdata('error', 'You are now allowed to create invoice');
            $this->loadViews("dashboard", $this->global, NULL , NULL);
            return;
        }

        $managername = $this->session->userdata ( 'name' );
        $data['managerInfo'] = $this->users_model->getManagerInfo($managername);

        $userInfo = $this->users_model->getUserInfo($username);
        $userCurrentPackage = $this->Invoices_model->getUserPackage($username, $userInfo->owner);
        $data['userInfo'] = $userInfo;

        $data['getUserPackage'] = $userCurrentPackage;
        
        $data['gracedays_old'] = $this->Invoices_model->getManagerSettings_bytype($managername, "GRACE-DAYS-OLD");
        $gracedays_new = $this->Invoices_model->getManagerSettings_bytype($managername, "GRACE-DAYS-NEW");
        $data['gracedays_new'] = $gracedays_new;

        $inactive_days_bill = $this->Invoices_model->getManagerSettings_bytype($managername, "INACTIVE-DAYS-BILL");
        $data['inactive_days_bill'] = $inactive_days_bill;

        $data['packages'] = $this->users_model->getResellerPackages();

        $inactive_days_bill_val = isset($inactive_days_bill[0]->stgvalue) ? (int)$inactive_days_bill[0]->stgvalue : (is_numeric($inactive_days_bill) ? (int)$inactive_days_bill : 0);
        $rechargeInfo = $this->Invoices_model->getRechargeInfo($username, $userInfo->owner, $inactive_days_bill_val);
        $data['rechargeInfo'] = $rechargeInfo;

        $data['creditaccount'] = $this->Accounts_model->getAccountsList('INCOME', 2);
        $data['debitaccount'] = $this->Accounts_model->getAccountsList('CASH', 3);

        //$data['customeraccount'] = $this->Accounts_model->getAccountsList('CUSTOMER', 3);

        if(empty($rechargeInfo)){
            $data['userInactiveDays'] = 0;
            $date1 = Date("Y-m-d");  // Service Date
            //$date2 = Date("Y-m-t");  // Get Last date of month
            $date2 = date('Y-m-d', strtotime("+1 month", strtotime($date1)));
            $data['isnew'] = 1;
            
        }
        else
        {
            $expdate1 = $userInfo->expiration;
            $inactiveDays = $this->userInactiveDays($username, $expdate1, $inactive_days_bill);
            $data['userInactiveDays'] = $inactiveDays;
            //print_r($inactiveDays);
            $date1 = date('Y-m-d', strtotime("+0 day", strtotime($expdate1)));
            //echo $date1;
            //$date2 = Date("Y-m-t", strtotime($date1));
            $date2 = date('Y-m-d', strtotime("+1 month", strtotime($date1)));
            $data['isnew'] = 0;
            
        }


        $packagePrice = $this->Invoices_model->getPackagePrice($userInfo->owner, $userInfo->srvid);

        if(!empty($packagePrice)){
            $data['packagePrice'] = array('unitprice'=> $packagePrice->unitprice, 'baseprice'=> $packagePrice->baseprice, 'saleprice'=> $packagePrice->saleprice, 'srv_date'=> $date1, 'exp_date'=>$date2);
        }

        //$getPackagePrice = $this->getPackagePrice($userInfo->srvid, $date1, $date2, $userInfo->owner);
        //$data['packagePrice'] = $getPackagePrice;

        $this->global['pageTitle'] = 'Recharge : User';

        //print_r($getPackagePrice);

        $this->load->model('Invoices_model');
        $data['userInvoices'] = $this->Invoices_model->getAllInvoicesByUsername($username);

        $this->loadViews("invoiceAddNew", $this->global, $data, NULL);
        //$this->load->view('invoiceAddNew', $data, NULL);
        
    }


    function saveRecharge()
    {

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
            $this->load->model('users_model');
            $this->load->model('Invoices_model');
            $this->load->model('Services_model');
            
            //$this->form_validation->set_rules('user','User Name','trim|required|max_length[50]|callback_usernameExists');
            //$this->form_validation->set_rules('service','Service','required');

            $this->form_validation->set_rules('price','Price','required|numeric');
            $this->form_validation->set_rules('amount','Amount','required|numeric|callback_checkBalance');

            $this->form_validation->set_rules('srvdate', 'Renew Date', 'required');
            $this->form_validation->set_rules('expdate', 'Expiry Date', 'required');

            if($this->session->userdata('isaccountmanager') == 1){
                $this->form_validation->set_rules('acccr', 'Sales Account', 'numeric');
                $this->form_validation->set_rules('accdr', 'Customer Account', 'numeric');
            }


            if($this->form_validation->run() == FALSE)
            {
                $this->invoiceAddNew($this->input->post('user'));
            }
            else
            {
                
                $user = $this->security->xss_clean($this->input->post('user'));
                $price = $this->security->xss_clean($this->input->post('price'));
                $amount = -($this->security->xss_clean($this->input->post('amount')));
                $srvid = $this->security->xss_clean($this->input->post('srvid'));
                $srv_date = $this->security->xss_clean($this->input->post('srvdate'));
                $exp_date = $this->security->xss_clean($this->input->post('expdate'));
                $remarks = $this->security->xss_clean($this->input->post('remarks'));

                $userDetails = $this->users_model->getUserInfo($user);
                $userManager = $userDetails->owner;

                if($this->session->userdata('isaccountmanager') == 5){ // For now 5 means disable it for now
                    $accdr = $this->security->xss_clean($this->input->post('accdr'));
                    $acccr = $this->security->xss_clean($this->input->post('acccr'));
                }else{
                    $accdr = 0;
                    $acccr = 0;
                }

                $userSuspensionTicket = $this->Invoices_model->activationTicket_get_usertype($user, 3);

                if(isset($userSuspensionTicket->expdate) && $userSuspensionTicket->expdate >= date("Y-m-d"))
                {
                    $this->session->set_flashdata('error', 'User is suspended');
                    redirect('userslist/userListing');
                    //return;
                }

                if($userManager == $this->session->userdata ( 'name' ))
                {

                    if(abs($amount) > $price)
                    {
                        $price = abs($amount);
                    }
                
                    $userInfo = array('username'=>$user,
                                        'srvid'=>$srvid, 
                                        'managername'=>$this->session->userdata ( 'name' ),
                                        'createdBy'=>0,
                                        'invtype'=>'Recharge',
                                        'srvdate'=>$srv_date,
                                        'expdate'=>$exp_date,
                                        'price'=>$price,
                                        'amount'=>$amount,
                                        'remarks'=>$remarks);

                    if($this->session->userdata('isaccountmanager') == 1){

                        $userJVEntry = array('jvdate'=>$srv_date,
                                            'acctdr'=>$accdr, 
                                            'acctcr'=>$acccr, 
                                            'desc'=>$remarks,
                                            'jvtype'=>128,
                                            'managername'=>$this->session->userdata ( 'name' ),
                                            'invinqty'=>1,
                                            'invprice'=>$price,
                                            'invtotal'=>$price,
                                            'debit'=>$price,
                                            'credit'=>$price,
                                            'balance'=>0,
                                            'username'=>$user);
                        log_message('info', 'Invoice-SaveRecharge: ' . json_encode($userJVEntry));
                    }
                    
                    log_message('info', 'Invoice-SaveRecharge: ' . json_encode($userInfo));
                    
                    // Revert Credit to Master for Recharge
                    
                    if(($this->users_model->checkUserExist($user)) == true)
                    {

                        $result = $this->Invoices_model->addRecharge($userInfo); // Generate Customer Invoice
                        log_message('info', 'Invoice-SaveRecharge: Invoice Created -> ' . $result);
                        
                        $qryUpdateExpiry = array('expiration'=>$exp_date.' 12:00:00');
                        $expiryupdate = $this->Invoices_model->updateExpiry($qryUpdateExpiry, $user, $exp_date);

                        log_message('info', 'Invoice-SaveRecharge: Expiry updated -> ' . json_encode($qryUpdateExpiry));

                        if($this->session->userdata('isaccountmanager') == 1){
                            //$jvresult = $this->Jvs_model->addNewJv($userJVEntry);
                        }
                        

                        // This will call masterCreditRefund function to reverse credit to master
                        $this->Invoices_model->getResellerChain($userDetails, $userManager, $amount, $srvid, $srv_date, $exp_date, $accdr, $acccr);
    

                        $userTrafficInfo = $this->users_model->getUserInfo($user);
                        $serviceTrafficInfo = $this->Services_model->getServiceDownloadInfo($srvid);

                        $downLimit = $userTrafficInfo->downlimit + ((($serviceTrafficInfo->trafficunitdl*(1024))*1024));
                        $upLimit =  $userTrafficInfo->uplimit + ((($serviceTrafficInfo->trafficunitul*(1024))*1024));
                        $combineLimit = $userTrafficInfo->comblimit + ((($serviceTrafficInfo->trafficunitcomb*(1024))*1024));

                        $addTrafficInfo = array('downlimit'=>$downLimit,
                                        'uplimit'=>$upLimit,
                                        'comblimit'=>$combineLimit);

                        $addDownloadLimit = $this->Invoices_model->addDownloadLimit($user, $addTrafficInfo);

                        //**** This will reset previous data plan ******/
                        $this->Services_model->resetDataPlan($user);
                    
                        if($result == True)
                        {
                            $this->session->set_flashdata('success', 'User recharged successfully');

                            /*/$managername = $this->session->userdata ( 'name' );
                            $managerInfo = $this->users_model->getManagerInfo($managername);

                            $messagetext = "Dear ".$userTrafficInfo->firstname." ".$userTrafficInfo->lastname.", your Monthly Bill Received with thanks. For any inquiry please Call 0310544666 or whatsapp 03105222084";

                            if($managerInfo->perm_allowdiscount == 1){
                                //$this->sendsmsgateway($userTrafficInfo->mobile, $messagetext, $errorCode);
                            }
                            else
                            {
                                //$this->sendsmsgateway($userTrafficInfo->mobile, "Username ".$user." recharged successfully. Next expiry date:".$exp_date, $errorCode);
                            }*/

                        }
                        else
                        {
                            $this->session->set_flashdata('error', 'User recharged failed');
                        }
                    }else{
                        $this->session->set_flashdata('error', 'User not exists....');
                    }

                }else{
                    $this->session->set_flashdata('error', 'User is suspended or Recharge Not Allowed from this Panel');
                }

                redirect('userslist/userListing');
            }
        }
    }

    private function getResellerChain($userDetails, $managername,  $amount, $srvid, $srv_date, $exp_date, $accdr, $acccr, $creditType = 'CR', $drdays = 0) {
        
        $chain = array();
        $currentManager = $managername;
        
        $chain[] = $currentManager;
        while(true) {
            $masterInfo = $this->Invoices_model->getMasterManager($currentManager);
            
            if(strtoupper($masterInfo->mastername) == "NONE" || $masterInfo->mastername == $currentManager || $masterInfo->mastername == NULL || $masterInfo->mastername == "" || $masterInfo->mastername == "0") {
                break;
            }
            $chain[] = $masterInfo->mastername;
            $currentManager = $masterInfo->mastername;
        }

        log_message('Info', 'invoice-masterCreditRefund: Chain Info '.json_encode($chain));

        // Process each level in the chain
        foreach($chain as $masterManager) {
            $this->masterCreditRefund($userDetails, $masterManager, $amount, $srvid, $srv_date, $exp_date, $accdr, $acccr, $creditType, $drdays);
        }
            
    }

    function masterCreditRefund($userDetails, $baseManager, $amount, $srvid, $srv_date, $exp_date, $accdr, $acccr, $creditType = 'CR', $drdays = 0){
        

        $masterInfo = $this->Invoices_model->getMasterManager($baseManager);
        
        $divratio = $this->Invoices_model->getManagerSettings_bytype($masterInfo->mastername, "DIV-RATIO");
        if (!empty($divratio)) {
            return;
        }

        if(strtoupper($masterInfo->mastername )<> "NONE" || $masterInfo->mastername <> NULL || $masterInfo->mastername <> "" 
            || $masterInfo->mastername <> 0 )
        {
                

            $getPackageBasePrice = $this->Invoices_model->getPackagePrice($baseManager, $userDetails->srvid);
            $getPackagePriceMaster = $this->Invoices_model->getPackagePrice($masterInfo->mastername, $userDetails->srvid);


            if(empty($getPackageBasePrice) || empty($getPackagePriceMaster)){
                return;
            }

            //echo $amount;
            //echo "<br>";

            /*echo "Base Manager: ".$baseManager."<br>";
            echo "Base Manager: ".$masterInfo->mastername."<br>";
            print_r($getPackageBasePrice)."<br> Next Line <br>";
            print_r($getPackagePriceMaster)."<br>";
            exit;*/
            
            if( !empty($getPackagePriceMaster) && $getPackageBasePrice->baseprice > $getPackagePriceMaster->baseprice)
            {

                if($creditType == 'CR')
                {
                $amountToRefund = (abs($getPackageBasePrice->baseprice) - $getPackagePriceMaster->baseprice);
                $amountToRefundDetails = " Price:".abs($getPackageBasePrice->baseprice)." - ".$getPackagePriceMaster->baseprice.
                                            " = ".(abs($getPackageBasePrice->baseprice) - $getPackagePriceMaster->baseprice);
                }elseif($creditType == 'DR' && $drdays > 0){
                    $amountToRefund = (($getPackagePriceMaster->baseprice - $getPackageBasePrice->baseprice) / 30) * $drdays;
                    $amountToRefundDetails = " Reverse Master Credit for ".$drdays." days:".abs($getPackagePriceMaster->baseprice)." - ".$getPackageBasePrice->baseprice.
                                            " = ".(abs($getPackagePriceMaster->baseprice) - $getPackageBasePrice->baseprice);
                }

                $masterRechargeInfo = array('username'=>$userDetails->username,
                                'srvid'=>$srvid, 
                                'managername'=>$masterInfo->mastername,
                                'createdBy'=>0,
                                'invtype'=>'Credit',
                                'srvdate'=>$srv_date,
                                'expdate'=>$exp_date,
                                'price'=>0,
                                'amount'=>$amountToRefund,
                                'remarks'=>"Refund to:".$masterInfo->mastername.
                                            $amountToRefundDetails);

                $masterJVEntry = array('jvdate'=>$srv_date,
                                'acctdr'=>$acccr, 
                                'acctcr'=>$accdr, 
                                'desc'=>"Refund to:".$masterInfo->mastername.
                                            $amountToRefundDetails,
                                'jvtype'=>128,
                                'managername'=>$baseManager,
                                'invinqty'=>1,
                                'invprice'=>$amountToRefund,
                                'invtotal'=>$amountToRefund,
                                'debit'=>$amountToRefund,
                                'credit'=>$amountToRefund,
                                'balance'=>0,
                                'username'=>$baseManager);

                log_message('info', 'invoice-masterCreditRefund: (Master): ' . json_encode($masterRechargeInfo));
                log_message('info', 'invoice-masterCreditRefund (Master): ' . json_encode($masterJVEntry));
            }

            log_message('info', 'invoice-masterCreditRefund:  ' . $getPackageBasePrice->baseprice . 
                        ' ' . $getPackagePriceMaster->baseprice);

            if($getPackageBasePrice->baseprice > $getPackagePriceMaster->baseprice && 
                !empty($masterRechargeInfo)){ // Reverse Credit to Master

                $result = $this->Invoices_model->addRecharge($masterRechargeInfo);
                //$jvresult = $this->Jvs_model->addNewJv($masterJVEntry);
                log_message('info', 'invoice-masterCreditRefund: Invoice Created for Master Refund -> ' . $result);

            }
        
        }

    }

    function editOld($userId = NULL)
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
            
            $data['packages'] = $this->users_model->getPackages();
            $data['userInfo'] = $this->users_model->getUserInfo($userId);
            
            $this->global['pageTitle'] = 'PaceTel : Edit User';
            
            $this->loadViews("usersEditOld", $this->global, $data, NULL);
        }
    }

    // Add Credit to Reseller

    function creditAddNew($managername = NULL)
    {

        $this->load->model('users_model');
        //$data['packages'] = $this->users_model->getPackages();

        $managername = $this->session->userdata ( 'name' );
        $managerInfo = $this->users_model->getManagerInfo($managername);
        $data['managerInfo'] = $this->users_model->getManagerInfo($managername);

        if($managerInfo->perm_addcredits == 1)
        {

            $data['managerList'] = $this->users_model->getManagersList();
            $this->global['pageTitle'] = 'Credit : User';

            $this->loadViews("creditAddNew", $this->global, $data, NULL);

        }else{
            $this->session->set_flashdata('error', 'Not allowed to add credit ');
            redirect('usersListing');
        }
        
    }

    function saveCredit()
    {

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            //$this->form_validation->set_rules('user','User Name','trim|required|max_length[50]|callback_usernameExists');
            $this->form_validation->set_rules('manager','manager','required');
            //$this->form_validation->set_rules('amount','Amount','required|numeric|greater_than[0]');
            $this->form_validation->set_rules('amount','Amount','required|numeric|greater_than[0]|callback_checkBalance');
            $this->form_validation->set_rules('invtype','invtype','required');

            if($this->form_validation->run() == FALSE)
            {
                $this->creditAddNew($this->input->post('manager'));
            }
            else
            {
                $manager = $this->security->xss_clean($this->input->post('manager'));
                $invtype = $this->security->xss_clean($this->input->post('invtype'));
                $remarks = $this->security->xss_clean($this->input->post('remarks'));

                $managerInfo = $this->Reseller_model->getResellerInfo($manager);

                /*if($managerInfo->perm_addcredits == 0 || ($managerInfo->mastername <> $this->session->userdata ( 'name' ) && strtoupper($managerInfo->mastername) <> "NONE" && $managerInfo->mastername <> "0"))
                {
                    $this->session->set_flashdata('error', 'Sub Manager Transaction not allowed from this Panel.');
                    redirect('resellerListing');
                }*/

                if($invtype == 'Debit')
                {
                    $amount = -($this->security->xss_clean($this->input->post('amount')));
                    $userPanelBalance = $this->Invoices_model->checkManagerBalance($manager);

                    if($this->security->xss_clean($this->input->post('amount')) > $userPanelBalance->amount && 
                        $this->session->userdata ( 'name' ) <> "admin"){

                            $this->session->set_flashdata('error', 'Not Enough Balance, Manager recharged failed');
                            redirect('resellerListing');

                    }
                }
                else
                {
                    $amount = $this->security->xss_clean($this->input->post('amount'));

                }

                $manager_master = $this->session->userdata ( 'name' );
                
                $user = $manager;

                $dt1 = new DateTime();
                $date1 = $dt1->format("Y-m-d");

                //$dt2 = new DateTime("+1 year");
                //$dt2 = date("Y-m-t", $dt2);
                //$date2 = $dt2->format("Y-m-d");

                $srv_date = $date1;
                $exp_date = $date1;

                $userInfo = array('username'=>$manager,
                                'srvid'=>0, 
                                'managername'=>$manager,
                                'createdBy'=>0,
                                'invtype'=>$invtype,
                                'srvdate'=>$srv_date,
                                'expdate'=>$exp_date,
                                'price'=>0,
                                'amount'=>$amount,
                                'remarks'=>"CreditBy: ".$manager_master." (".$remarks.")");

                $masterInfo = array('username'=>$manager,
                                'srvid'=>0, 
                                'managername'=>($this->session->userdata('name') <> 'admin') ? $manager_master : (strtoupper($managerInfo->mastername) <> "NONE" && $managerInfo->mastername <> "0" ? $managerInfo->mastername : $manager_master),
                                'createdBy'=>0,
                                'invtype'=>$invtype,
                                'srvdate'=>$srv_date,
                                'expdate'=>$exp_date,
                                'price'=>0,
                                'amount'=>-$amount,
                                'remarks'=>$remarks);
                                    
                $this->load->model('users_model');
                $this->load->model('Invoices_model');

                if(($this->users_model->checkManagerExist($manager)) == true)
                {

                    $result = $this->Invoices_model->addRecharge($userInfo);
                    $resultmaster = $this->Invoices_model->addRecharge($masterInfo);

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

                redirect('resellerListing');

                
            }
        }
    }


    function editUser()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {

            $this->load->library('form_validation');
            $user = $this->input->post('user');
            //$this->form_validation->set_rules('user','User Name','trim|required|max_length[50]|callback_usernameExists');
            $this->form_validation->set_rules('password','Password','matches[cpassword]|max_length[20]');
            $this->form_validation->set_rules('cpassword','Confirm Password','matches[password]|max_length[20]');

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
                $cnic = $this->input->post('cnic');
                $srvid = $this->input->post('service');
                               
                if(empty($password))
                {
                    //$userInfo = array('email'=>$email, 'roleId'=>$roleId, 'name'=>$name,
                    //                'mobile'=>$mobile, 'updatedBy'=>$this->vendorId, 'updatedDtm'=>date('Y-m-d H:i:s'));
                    $userInfo = array('groupid'=>1, 'enableuser'=> 1,
                        'firstname'=>$fname, 'lastname'=>$lname, 'address'=>$address,
                        'mobile'=>$mobile, 'email'=>$email, 'taxid'=>$cnic,
                        'gpslat'=>0.00000000000000, 'gpslong'=>0.00000000000000,
                        'usemacauth'=>0, 'uptimelimit'=>0, 'srvid'=>$srvid, 
                        'createdby'=>$managername = $this->session->userdata ( 'name' ),
                        'owner'=>$managername = $this->session->userdata ( 'name' ),
                        'lang'=>'English');
                }
                else
                {
                    //$userInfo = array('email'=>$email, 'password'=>getHashedPassword($password), 'roleId'=>$roleId,
                    //    'name'=>ucwords($name), 'mobile'=>$mobile, 'updatedBy'=>$this->vendorId, 
                    //    'updatedDtm'=>date('Y-m-d H:i:s'));

                    $userInfo = array('password'=>MD5($password), 'groupid'=>1, 'enableuser'=> 1,
                        'firstname'=>$fname, 'lastname'=>$lname, 'address'=>$address,
                        'mobile'=>$mobile, 'email'=>$email, 'taxid'=>$cnic,
                        'gpslat'=>0.00000000000000, 'gpslong'=>0.00000000000000,
                        'usemacauth'=>0, 'uptimelimit'=>0, 'srvid'=>$srvid, 
                        'createdby'=>$managername = $this->session->userdata ( 'name' ),
                        'owner'=>$managername = $this->session->userdata ( 'name' ),
                        'lang'=>'English');
    
                    $radpassword = array('username'=>$user, 'attribute'=>'Cleartext-Password', 'op'=>':=', 'value'=>$password);
                    $radsimuse = array('username'=>$user, 'attribute'=>'Simultaneous-Use', 'op'=>':=', 'value'=>'1');

                }

                $result = $this->users_model->editUser($userInfo, $user);
                
                if($result == true)
                {
                    $this->session->set_flashdata('success', 'User updated successfully '.$message);
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
        //$this->db = $this->load->database('default', TRUE);
        //$userId = $this->vendorId;
        $this->load->model('users_model');
        $return = false;

        if(empty($userId)){
            $result = $this->users_model->checkUsernameExists($username);
        }

        if(!empty($result)){ $return = true; }
        else {
            $this->form_validation->set_message('username not Exists', 'The {field} not exists...');
            $return = false;
        }

        return $return;
    }

    function getPackagePrice_notinuse($service_id, $date1, $date2, $manager){

        //$output = 0;
        $result = $this->Invoices_model->getPackagePrice($manager, $service_id);

        //foreach($result as $row){
        //    $output = $row->unitprice;
        //}

        //$srv_date = strtotime($date1);
        //$daysinmonth = (date('t', $srv_date) - date('j', $srv_date))+1;

        //$price1 = ($output/(date('t', $srv_date)))*$daysinmonth;
        if(!empty($result)){
            $info = array('unitprice'=> $result->unitprice, 'baseprice'=> $result->baseprice, 'saleprice'=> $result->saleprice, 'srv_date'=> $date1, 'exp_date'=>$date2);
            return $info;
        }else{
            redirect('usersListing');
        }
    }

    // **** Recharge Live Package Price Costing Customized Date ************
    function getServicePrice()
    {

        $managername = $this->session->userdata ( 'name' );

        $service_id = $this->input->post('service_id');
        $exp_date = $this->input->post('exp_date');
        $srv_date = $this->input->post('srv_date');

        $result = $this->Invoices_model->getPackagePriceSrvID($managername, $service_id);
        //$result = $this->Invoices_model->getPackagePrice($managername, $radsrvid);

        $unitprice = $result->unitprice;
        $saleprice = $result->saleprice;

        $date1 = date_create($exp_date);
        $date2 = date_create($srv_date);
        $diff=date_diff($date1,$date2);
        $daysinmonth = $diff->format("%a");

        $tot_srvmonthdays=cal_days_in_month(CAL_GREGORIAN,date("m",strtotime($srv_date)),date("y",strtotime($srv_date)));
        $tot_expmonthdays=cal_days_in_month(CAL_GREGORIAN,date('m', strtotime("+0 day", strtotime($exp_date))),date("y",strtotime($exp_date)));

        //$srvday = date('Y-m-d', strtotime("+1 month", strtotime($srv_date)));
        $daysinsrvmonth = ($tot_srvmonthdays+1) - (date('d', strtotime("+0 day", strtotime($srv_date))));
        //$daysinexpmonth = ($tot_expmonthdays) - date('d', strtotime("+0 day", strtotime($exp_date)));
        $daysinexpmonth = date('d', strtotime("+0 day", strtotime($exp_date)))-1;

        //$chargeinsrvmonth = ($unitprice/$tot_srvmonthdays)*$daysinsrvmonth;
        if($tot_srvmonthdays <> $tot_expmonthdays){
            $chargeinexpmonth = (($unitprice/$tot_srvmonthdays) * $daysinsrvmonth) + (($unitprice/$tot_expmonthdays)*$daysinexpmonth);
        }
        else{
            $chargeinexpmonth = ($unitprice/$tot_srvmonthdays) * $daysinmonth;
        }
        
        //$billingDays = ($expmonthdays+$srvmonthdays)/2;

        //$price1 = round(($unitprice/$billingDays)*($daysinmonth),0);
        
        //$chargeinexpmonth = round($chargeinexpmonth,0);
        //if($chargeinexpmonth < 0){
        //    $chargeinexpmonth = $unitprice;
        //}

        $price1 = round($chargeinexpmonth,0);

        // ******** NOTES **********
        //$daysinmonth = (date('t', $date1) - date('j', $date1));
        //$dt2 = new DateTime("+1 month");
        //$date1 = strtotime($srv_date->format("Y-m-d"));
        //if((date("m", strtotime($exp_date)) == (date("m", strtotime($srv_date)))))
        //$date2 = strtotime($exp_date);
        //$daysremaining = date('t', $date2) - date('j', $date1);
        //$price2 = (($output/(date('t', $date2)))*(date('j', $date2)));

        //$info = array(['unitprice'=> $price1, 'saleprice'=> $price1]);
        $info = array(['unitprice'=> $price1, 'saleprice'=> $price1]);

        echo json_encode($info);

    }

    public function storeImages()
    {
        //$this->load->helper('url', 'form');

        $config['upload_path']   = '/var/www/html/paceradius/uploads/'; 
        $config['allowed_types'] = 'gif|jpg|png|jpeg'; 
        $config['max_size']      = 0; 
        $config['max_width']     = 0; 
        $config['max_height']    = 0;  
        $this->load->library('upload', $config);
    
        //************* */ CNIC Image 1 Upload Updates
        if ( ! $this->upload->do_upload('cnic_image1')) {
            $error = array('error' => $this->upload->display_errors()); 
            print_r($error);
            //$this->load->view('upload_form', $error); 
        }
        else { 
            
            $data = array('upload_data1' => $this->upload->data());

            foreach ($data['upload_data1'] as $item => $value)
            {
                if($item == 'file_name')
                    $fullPathImage1 = $value;
            }
            //$this->load->view('upload_success', $data); 
         } 

         // *********  */ CNIC Image 2 Upload Updates
         if ( ! $this->upload->do_upload('cnic_image2')) {
            $error = array('error' => $this->upload->display_errors()); 
            print_r($error);

        }
        else { 
            
            $data = array('upload_data2' => $this->upload->data());

            foreach ($data['upload_data2'] as $item => $value)
            {
                if($item == 'file_name')
                    $fullPathImage2 = $value;
            }

        }

        $usersInfo =  array('comment'=>$fullPathImage1, 'verified'=>1);
        $user = $this->input->post('user');
        $this->users_model->updateVerifyUser($usersInfo, $user);

        $userInfoImage1 = array('username'=>$user, 'cnic_file1'=>$fullPathImage1, 'cnic_file2'=>$fullPathImage2, 
                                'createdby'=>$managername = $this->session->userdata ( 'name' ));
        $result = $this->users_model->store_images($userInfoImage1, $user);

        if($result == true)
        {
            $this->session->set_flashdata('success', 'Files uploaded successfully '.$message);
        }
        else
        {
            $this->session->set_flashdata('error', 'Files upload failed');
        }
                
        redirect('usersListing');

    }


    function docsUpload($userId = NULL)
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
            $data['userInfo'] = $this->users_model->getUserInfo($userId);
            
            $this->global['pageTitle'] = 'Upload : User Docs';
            
            $this->loadViews("docsUpload", $this->global, $data, NULL);
        }
    }

    function checkBalance($amount){

        $this->load->library('form_validation');
        $managername = $this->session->userdata ( 'name' );
        $balInfo = $this->Invoices_model->checkManagerBalance($managername);

        if($managername <> 'admin'){
            if(empty($balInfo))
            {
                $this->form_validation->set_message('balance_check', 'You do not have enough balance to recharge Rs. {field}');
                return false;
            }
            else{
                $balance = ($balInfo->amount - $amount);

                // Add Credit Limit
                if($this->postpaidmanager > 0){
                    $balance = $balance + $this->postpaidmanager;
                }

                if($balance < 0)
                {
                    $this->form_validation->set_message('balance_check', 'You do not have enough balance to recharge Rs. {field}');
                    return false;
                }
                else
                {
                    return true;
                }
            }
        }
        else{
            return true;
        }

    }

    function userInactiveDays($username, $expdate, $inactive_days_bill = 0){
        if (empty($inactive_days_bill)) {
            return array('inactiveDays' => 0);
        }

        $lastSessionDate = $this->Invoices_model->getLastSessionDate($username);
        $inactive_days_bill_val = isset($inactive_days_bill[0]->stgvalue) ? (int)$inactive_days_bill[0]->stgvalue : (is_numeric($inactive_days_bill) ? (int)$inactive_days_bill : 0);
        
        if(!empty($lastSessionDate)){
            $date1 = date_create($lastSessionDate);
            $date1->modify('-1 day');
            $date2 = date_create(Date("Y-m-d"));  // Service Date
            $date2->modify('-1 day');
            $diff=date_diff($date1,$date2);
            $daysNonActive = (int)$diff->format("%a");


            if ($inactive_days_bill_val > 0) {
                $daysNonActive -= $inactive_days_bill_val;
            }

            $info = array('inactiveDays'=> $inactive_days_bill_val);
               return $info;
        }
        else{
            $info = array('inactiveDays'=> 0);
            return $info;
        }
    }

    // ******* USER QUICK RECHARGE ******** //
    function userQuickRecharge($userId = NULL, $invType = NULL, $accdr = 0, $acccr = 0){

        //if($managername <> 'admin'){
        if($this->ismaster == 0){ // Allow Quick Recharge for User Panel Resellers Only

            $this->load->helper('array');
            $this->load->model('users_model');
            $this->load->model('Invoices_model');
            $this->load->model('Services_model');
                    
            $userInfo = $this->users_model->getUserInfo($userId);

            $managername = $userInfo->owner;

            $managerInfo = $this->users_model->getManagerInfo($managername);

            $alldiscount = $managerInfo->perm_allowdiscount;


            $userCurrentPackage = $this->Invoices_model->getUserPackage($userId, $userInfo->owner);

            $rechargeInfo = $this->Invoices_model->getRechargeInfo($userId);

            $discount = $userCurrentPackage->discount;
            $adjustment = $userCurrentPackage->adjamount;

            if(empty($rechargeInfo)){
                $date1 = Date("Y-m-d");  // Service Date
                //$date2 = Date("Y-m-t");  // Get Last date of month
                $date2 = date('Y-m-d', strtotime("+1 month", strtotime($date1)));
            }
            else
            {
                
                $invExpdate1 = $rechargeInfo->expdate;
                $expdate1 = $userInfo->expiration;
                $currentsysdate = $rechargeInfo->currentdate;

                $inactiveDays = $this->userInactiveDays($userId, $expdate1);
                $data['userInactiveDays'] = $inactiveDays;
                //echo $inactiveDays;
                //exit;
                if($inactiveDays > 30)
                {
                    $days = "+".$inactiveDays. " day";
                }
                else{
                    $days = "+0 day";
                }


                $date1 = date('Y-m-d', strtotime($days, strtotime($invExpdate1)));
                if($rechargeInfo->invtype == 'Gracedays')
                {
                    $date1 = date('Y-m-d', strtotime("+0 day", strtotime($currentsysdate)));
                }elseif($date1 < $currentsysdate)
                {
                    $date1 = date('Y-m-d', strtotime("+0 day", strtotime($currentsysdate)));
                }
                $date2 = date('Y-m-d', strtotime("+1 month", strtotime($date1)));

                /*$expdate1 = $userInfo->expiration;
                $inactiveDays = $this->userInactiveDays($userId, $expdate1);
                $data['userInactiveDays'] = $inactiveDays;
                //print_r($inactiveDays);
                $date1 = date('Y-m-d', strtotime("+0 day", strtotime($expdate1)));
                //echo $date1;
                //$date2 = Date("Y-m-t", strtotime($date1));
                $date2 = date('Y-m-d', strtotime("+1 month", strtotime($date1)));*/
            }

            
            //$getPackagePrice = $this->getPackagePrice($userInfo->srvid, $date1, $date2, $userInfo->owner);

            $packagePrice = $this->Invoices_model->getPackagePrice($userInfo->owner, $userInfo->srvid);

            if(!empty($packagePrice)){
                $data['packagePrice'] = array('unitprice'=> $packagePrice->unitprice, 'baseprice'=> $packagePrice->baseprice, 'saleprice'=> $packagePrice->saleprice, 'srv_date'=> $date1, 'exp_date'=>$date2);
            }

            $baseprice = $packagePrice->baseprice;
            $unitPrice = $packagePrice->unitprice;
            $saleprice = $packagePrice->saleprice;

            log_message('Info', 'subscribers-manage-bulk-recharge: Price Info '.json_encode($packagePrice));

            //$baseprice = element('baseprice',$getPackagePrice); // Charged to Master
            //$unitPrice = element('unitprice',$getPackagePrice); // Charged to Reseller
            //$saleprice = element('saleprice',$getPackagePrice); // Charged to Customer

            // ************** Inactive Days Calculator ****************** //


            if(empty($rechargeInfo)){
                $date1 = Date("Y-m-d");  // Service Date
                $date2 = Date("Y-m-t");  // Get Last date of month
                $date2 = date('Y-m-d', strtotime("+1 month", strtotime($date1))); // Add One Day to Recharge
                $daysforDisc = abs(strtotime($date2) - strtotime($date1));
                $years = floor($daysforDisc / (365*60*60*24));
                $months = floor(($daysforDisc - $years * 365*60*60*24) / (30*60*60*24));
                $daysforDisc = floor(($daysforDisc - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
            
                $discount = ($discount*$daysforDisc)/30;
                $adjustment = ($adjustment*$daysforDisc)/30;
            
                $expdate1 = date('Y-m-d', strtotime("+0 month", strtotime($date1)));
            }
            else
            {
            
                $inactiveDays = element('inactiveDays', $inactiveDays);
                //echo $inactiveDays;
                //exit;
                if($inactiveDays > 30)
                {
                    $days = "+".$inactiveDays. " day";
                }
                else{
                    $days = "+0 day";
                }
            
                // Result are from 1st June Sin Khan and closed on 11/5/2021 so system calculate 55 days
                $currentsysdate = Date("Y-m-d");
                $expdate1 = $userInfo->expiration;
                $date1 = date('Y-m-d', strtotime("+0 day", strtotime($expdate1)));
                if($date1 < $currentsysdate)
                {
                    $date1 = date('Y-m-d', strtotime("+0 day", strtotime($currentsysdate)));
                }
                //$date2 = Date("Y-m-t", strtotime($date1));
                $date2 = date('Y-m-d', strtotime("+1 month", strtotime($date1)));  // Add one day to Recharge
            }
            
            if($unitPrice == $saleprice){
                $unitPrice = $unitPrice + $adjustment - $discount;}
            else{
                $unitPrice = $unitPrice;
            }


            // ******** END OF SECTION

            $balInfo = $this->Invoices_model->checkManagerBalance($managername);

            $balance = ($balInfo->amount - $unitPrice);

            // Add Credit Limit
            if($this->postpaidmanager > 0){
                $balance = $balance + $this->postpaidmanager;
            }

            if($managername == $userInfo->owner)
            {

                if($balance > 0)
                {

                    $user = $userId;
                    $price = $saleprice;
                    $amount = -$unitPrice;
                    $srvid = $userCurrentPackage->managersrvid;;
                    $srv_date = $date1;
                    $exp_date = $date2;
                    $remarks = "Automatic Recharge from Panel UID:".$managername;

                    if(abs($amount) > $price)
                    {
                        $price = abs($amount);
                    }

                        $userInfo = array('username'=>$user,
                            'srvid'=>$srvid,
                            'managername'=>$userInfo->owner,
                            'createdBy'=>0,
                            'invtype'=>'Recharge',
                            'srvdate'=>$srv_date,
                            'expdate'=>$exp_date,
                            'price'=>$price,
                            'amount'=>$amount,
                            'remarks'=>$remarks);
                        
                        // Revert Credit to Master for Recharge
                        $userDetails = $this->users_model->getUserInfo($user);
                        $masterInfo = $this->Invoices_model->getMasterManager($userDetails->owner);

                        if($masterInfo->mastername <> "NONE"){
                            $getPackagePrice = $this->Invoices_model->getPackagePrice($userDetails->owner, $userDetails->srvid);
                            if(abs($unitPrice) <> $getPackagePrice->baseprice){
                                $masterInfoInsert = array('username'=>$user,
                                    'srvid'=>$srvid, 
                                    'managername'=>$masterInfo->mastername,
                                    'createdBy'=>0,
                                    'invtype'=>'Credit',
                                    'srvdate'=>$srv_date,
                                    'expdate'=>$exp_date,
                                    'price'=>0,
                                    'amount'=>(abs($amount) - $getPackagePrice->baseprice),
                                    'remarks'=>"Bulk Recharge Refund:".$masterInfo->mastername." Price:".abs($amount)." - ".$getPackagePrice->baseprice." = ".(abs($amount) - $getPackagePrice->baseprice));
                            }
                        }

                        //echo("Amount = ".$unitPrice." Package Price: ".$getPackagePrice->baseprice."  ".$masterInfo->mastername);

                        //print_r($masterInfo);
                        //exit;


                        if(($this->users_model->checkUserExist($user)) == true)
                        {

                            $result = $this->Invoices_model->addRecharge($userInfo);
                            //$result = $this->Invoices_model->deleteCustomerCredit($userId);  // Delete Customer Credits and Refresh Billing
                            //$result = $this->Invoices_model->addCustomerCredit($userInfo);  // Delete Customer Credits and Refresh Billing

                            log_message('Info', 'subscribers-manage-bulk-recharge: Recharge Info '.json_encode($userInfo));
                            //if($masterInfo->mastername <> "NONE" and abs($amount) <> $getPackagePrice->baseprice){ // Reverse Credit to Master
                                //$result = $this->Invoices_model->addRecharge($masterInfoInsert);
                                $this->getResellerChain($userDetails, $userDetails->owner, $amount, $srvid, $srv_date, $exp_date, $accdr, $acccr);
                            //}

                                $qryUpdateExpiry = array('expiration'=>$exp_date.' 12:00:00');
                                $expiryupdate = $this->Invoices_model->updateExpiry($qryUpdateExpiry, $user, $exp_date);

                                $userTrafficInfo = $this->users_model->getUserInfo($user);
                                $serviceTrafficInfo = $this->Services_model->getServiceDownloadInfo($srvid);

                                $downLimit = $userTrafficInfo->downlimit + ((($serviceTrafficInfo->trafficunitdl*(1024))*1024));
                                $upLimit =  $userTrafficInfo->uplimit + ((($serviceTrafficInfo->trafficunitul*(1024))*1024));
                                $combineLimit = $userTrafficInfo->comblimit + ((($serviceTrafficInfo->trafficunitcomb*(1024))*1024));

                                $addTrafficInfo = array('downlimit'=>$downLimit,
                                                'uplimit'=>$upLimit,
                                                'comblimit'=>$combineLimit);

                                log_message('Info', 'subscribers-manage-bulk-recharge: Traffic Info '.json_encode($addTrafficInfo));

                                $addDownloadLimit = $this->Invoices_model->addDownloadLimit($user, $addTrafficInfo);

                                //**** This will reset previous data plan ******/
                                $this->Services_model->resetDataPlan($user, $managername);

                                log_message('Info', 'subscribers-manage-bulk-recharge: completed for Username: '.$user);
                        

                                $info = array(['errorBalance'=> 3, 'balance'=> $balance, 'expdate'=> $date2]);
                                echo json_encode($info);
                                //return;

                                //$this->session->set_flashdata('success', 'User recharged successfully');

                                //$managername = $this->session->userdata ( 'name' );
                                //$managerInfo = $this->users_model->getManagerInfo($managername);

                    }

                    IF($invType == NULL)
                    {
                        $info = array(['errorBalance'=> 0, 'balance'=> $balance, 'expdate'=> $date2]);
                        echo json_encode($info);
                    }

                }else{
                    IF($invType == NULL)
                    {
                        $info = array(['errorBalance'=> 1, 'balance'=> $balance, 'expdate'=> $date2]);
                        echo json_encode($info);
                    }
                }
            
            }else{

                IF($invType == NULL)
                {
                    $info = array(['errorBalance'=> 2, 'balance'=> $balance, 'expdate'=> $date2]);
                    echo json_encode($info);
                }

            }
        }
    }

    function batchpriceUpdate($seriesRef = ''){

        $manager = $this->session->userdata ( 'name' );

        if($manager == 'admin' || $this->ismaster > 0){

            $series = $this->input->post('series');
            $new_price = $this->input->post('new_price');

            if (!empty($seriesRef) || !empty($series)){

                if (!empty($series) && is_numeric($new_price)) {
                    // Update the price in the database
                    $update_status = $this->Invoices_model->update_price_by_series($series, $new_price);
                    
                    if ($update_status) {
                        $this->session->set_flashdata('success', 'Price updated successfully.');
                        redirect('serieslist');
                    } else {
                        $this->session->set_flashdata('error', 'Failed to update price.');
                        redirect('serieslist');
                    }
                }
            
            }else {
                $this->session->set_flashdata('error', 'Please provide valid series number.');
            }

            $this->global['pageTitle'] = 'Series : Update Price';
            $data['series'] = $seriesRef;

                
            $this->loadViews("Prepaid/seriespriceupdate", $this->global, $data, NULL);

        }else{

            $this->session->set_flashdata('error', 'You are not allowed to change prices.');
            redirect('serieslist');

        }

    }

    // Load the form to add a new collection
    public function cardscollection_add($series = '') {

        $data['series_list'] = $this->Invoices_model->get_unique_series();

        $data['seriesref'] = $series;
        $data['cardsseriesInfo'] = $this->Invoices_model->cardsseries_info($series);

        $this->loadViews("Prepaid/cardscollection_add", $this->global, $data, NULL);
    }

    // Handle the form submission for adding a new collection
    public function cardscollection_save() {

        $data = array(
            'series' => $this->input->post('series'),
            'date' => $this->input->post('date'),
            'cardreturn' => $this->input->post('cardreturn'),
            'sold' => $this->input->post('sold'),
            'value' => $this->input->post('value'),
            'amount' => $this->input->post('amount'),
            'remarks' => $this->input->post('remarks'),
            'collectBy' => $this->input->post('collectBy')
        );

        if ($this->Invoices_model->insert_cardscollection($data)) {
            $this->session->set_flashdata('success', 'Collection added successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to add collection.');
        }

        redirect('Invoices/cardscollection_list');

    }

    // List all collections
    public function cardscollection_list() {

        $count = $this->Invoices_model->get_all_cardscollections_count();
        $returns = $this->paginationCompress ( "Invoices/cardscollection_list", $count, 50, 3 );

        $data['cardscollection'] = $this->Invoices_model->get_all_cardscollections($returns["page"], $returns["segment"]);

        $this->global['pageTitle'] = 'Invoice Collections';
        $this->loadViews("Prepaid/cardscollection_list", $this->global, $data, NULL);

    }

    // Load the edit form for a specific collection
    public function cardscollection_edit($TransID) {

        $data['collection'] = $this->Invoices_model->get_cardscollection_by_id($TransID);
        //$this->load->view('Prepaid/cards_collection_edit', $data);
        $this->loadViews("Prepaid/cards_collection_edit", $this->global, $data, NULL);
    
    }

    // Update an existing collection
    public function cardscollection_update($TransID) {

        $data = array(
            'series' => $this->input->post('series'),
            'date' => $this->input->post('date'),
            'cardreturn' => $this->input->post('cardreturn'),
            'sold' => $this->input->post('sold'),
            'value' => $this->input->post('value'),
            'amount' => $this->input->post('amount'),
            'remarks' => $this->input->post('remarks'),
            'collectBy' => $this->input->post('collectBy')
        );

        if ($this->Invoices_model->update_cardscollection($TransID, $data)) {
            $this->session->set_flashdata('success', 'Collection updated successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to update collection.');
        }

        redirect('Invoices/cardscollection_list');

    }

    // ********** WALLET MANAGEMENT ********** //
    function walletListing($page = 1)
    {
        $searchText = $this->input->post('searchText');
        $searchFromDate = $this->input->post('searchFromDate');
        $searchToDate = $this->input->post('searchToDate');
        
        $data['searchText'] = $searchText;
        $data['searchFromDate'] = $searchFromDate;
        $data['searchToDate'] = $searchToDate;
        
        $this->load->library('pagination');
        
        $count = $this->Invoices_model->walletListingCount($searchText, $searchFromDate, $searchToDate);
        $returns = $this->paginationCompress("walletListing/", $count, 10);
        
        $data['walletRecords'] = $this->Invoices_model->walletListing($searchText, $returns["page"], $returns["segment"], $searchFromDate, $searchToDate);
        $data['searchFromDate'] = $searchFromDate;
        $data['searchToDate'] = $searchToDate;
        $this->global['pageTitle'] = 'Wallet Listing';
        $this->loadViews("wallets/walletListing", $this->global, $data, NULL);
    }

    function addNewWallet()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $managername = $this->session->userdata ( 'name' );
            $data['walletInfo'] = null;

            $data['managerInfo'] = $this->users_model->getManagerInfo($managername);
            $data['managerList'] = $this->users_model->getManagersList();

            $data['debitaccount'] = $this->Accounts_model->getAccountsList('INCOME', 2);
            $data['creditaccount'] = $this->Accounts_model->getAccountsList('CASH', 3);
            
            $this->global['pageTitle'] = 'Add New Wallet';
            $this->loadViews("wallets/addNewWallet", $this->global, $data, NULL);
        }
    }

    function walletExists($walletname)
    {
        $this->load->model('Invoices_model');
        $return = false;

        $result = $this->Invoices_model->checkWalletExists($walletname);

        if(!empty($result)){ 
            $this->form_validation->set_message('walletExists', 'The {field} already exists');
            $return = false;
        } else {
            $return = true;
        }

        return $return;
    }

    function addNewWalletProcess()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('walletname','Wallet Name','trim|required|max_length[255]|callback_walletExists');
            $this->form_validation->set_rules('managername','Manager Name','trim|required|max_length[64]');
            $this->form_validation->set_rules('wallettype','Wallet Type','trim|required|numeric');
            $this->form_validation->set_rules('opdate','Opening Date','trim|required');
            $this->form_validation->set_rules('opbal','Opening Balance','trim|required|numeric');
            $this->form_validation->set_rules('closdate','Closing Date','trim');
            $this->form_validation->set_rules('closbal','Closing Balance','trim|numeric');
            $this->form_validation->set_rules('accdr','Debit Account','trim|numeric');
            $this->form_validation->set_rules('acccr','Credit Account','trim|numeric');
            $this->form_validation->set_rules('remarks','Remarks','trim|max_length[500]');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->addNewWallet();
            }
            else
            {
                $walletname = $this->security->xss_clean($this->input->post('walletname'));
                $managername = $this->security->xss_clean($this->input->post('managername'));
                $wallettype = $this->security->xss_clean($this->input->post('wallettype'));
                $profileid = $this->security->xss_clean($this->input->post('profileid'));
                $opdate = $this->security->xss_clean($this->input->post('opdate'));
                $opbal = $this->security->xss_clean($this->input->post('opbal'));
                $closdate = $this->security->xss_clean($this->input->post('closdate'));
                $closbal = $this->security->xss_clean($this->input->post('closbal'));
                $accdr = $this->security->xss_clean($this->input->post('accdr'));
                $acccr = $this->security->xss_clean($this->input->post('acccr'));
                $remarks = $this->security->xss_clean($this->input->post('remarks'));
                
                $walletInfo = array(
                    'walletname'=>$walletname,
                    'managername'=>$managername,
                    'wallettype'=>$wallettype,
                    'profileid'=>$profileid,
                    'opdate'=>$opdate,
                    'opbal'=>$opbal,
                    'closdate'=>$closdate,
                    'closbal'=>$closbal,
                    'totdebit'=>0,
                    'totcredit'=>0,
                    'accdr'=>$accdr,
                    'acccr'=>$acccr,
                    'remarks'=>$remarks
                );
                
                $result = $this->Invoices_model->addNewWallet($walletInfo);
                
                if($result > 0)
                {
                    $this->session->set_flashdata('success', 'New Wallet created successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Wallet creation failed');
                }
                
                redirect('Invoices/walletListing');
            }
        }
    }

    function editWallet($walletId = NULL)
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            if($walletId == null)
            {
                redirect('walletListing');
            }

            $managername = $this->session->userdata ( 'name' );
            
            $data['walletInfo'] = $this->Invoices_model->getWalletInfo($walletId);

            $data['managerInfo'] = $this->users_model->getManagerInfo($managername);
            $data['managerList'] = $this->users_model->getManagersList();

            $data['debitaccount'] = $this->Accounts_model->getAccountsList('INCOME', 2);
            $data['creditaccount'] = $this->Accounts_model->getAccountsList('CASH', 3);
            
            $this->global['pageTitle'] = 'Edit Wallet';
            $this->loadViews("wallets/editWallet", $this->global, $data, NULL);
        }
    }

    function editWalletProcess()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
            $walletId = $this->input->post('walletid');
            
            $this->form_validation->set_rules('walletname','Wallet Name','trim|required|max_length[255]');
            $this->form_validation->set_rules('managername','Manager Name','trim|required|max_length[64]');
            $this->form_validation->set_rules('wallettype','Wallet Type','trim|required|numeric');
            $this->form_validation->set_rules('opdate','Opening Date','trim|required');
            $this->form_validation->set_rules('opbal','Opening Balance','trim|required|numeric');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->editWallet($walletId);
            }
            else
            {
                $walletname = $this->input->post('walletname');
                $managername = $this->input->post('managername');
                $wallettype = $this->input->post('wallettype');
                $profileid = $this->input->post('profileid');
                $closdate = $this->input->post('closdate');
                $closbal = $this->input->post('closbal');
                $accdr = $this->input->post('accdr');
                $acccr = $this->input->post('acccr');
                $remarks = $this->input->post('remarks');
                
                $walletInfo = array(
                    'profileid'=>$profileid,
                    'closdate'=>$closdate,
                    'closbal'=>$closbal,
                    'accdr'=>$accdr,
                    'acccr'=>$acccr,
                    'remarks'=>$remarks
                );
                
                $result = $this->Invoices_model->editWallet($walletInfo, $walletId);
                
                if($result == true)
                {
                    $this->session->set_flashdata('success', 'Wallet updated successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Wallet updation failed');
                }
                
                redirect('Invoices/walletListing');
            }
        }
    }

    // Invoice Collection: Add new collection entry
    public function invcollection_add() {
        $this->load->model('Invoices_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $data['wallets'] = $this->Invoices_model->invcollection_get_wallets();
        $data['success'] = false;
        $data['error'] = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->form_validation->set_rules('walletid', 'Wallet', 'required|numeric');
            $this->form_validation->set_rules('paydate', 'Pay Date', 'required');
            $this->form_validation->set_rules('remarks', 'Remarks', 'trim|max_length[255]');

            if ($this->form_validation->run() == TRUE) {
                $walletid = $this->security->xss_clean($this->input->post('walletid'));
                $paydate = $this->security->xss_clean($this->input->post('paydate'));
                $remarks = $this->security->xss_clean($this->input->post('remarks'));
                $collectionData = array(
                    'walletid' => $walletid,
                    'paydate' => $paydate,
                    'remarks' => $remarks,
                );
                $insertId = $this->Invoices_model->invcollection_add($collectionData);
                if ($insertId) {
                    $data['success'] = true;
                    
                    // Check if this is an AJAX request
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['success' => true, 'collectionId' => $insertId]);
                        exit;
                    }
                } else {
                    $data['error'] = 'Failed to create collection entry';
                    
                    // Check if this is an AJAX request
                    if ($this->input->is_ajax_request()) {
                        echo json_encode(['success' => false, 'message' => $data['error']]);
                        exit;
                    }
                }
            } else {
                $data['error'] = validation_errors();
                
                // Check if this is an AJAX request
                if ($this->input->is_ajax_request()) {
                    echo json_encode(['success' => false, 'message' => $data['error']]);
                    exit;
                }
            }
        }

        $this->loadViews("invcollection/add", $this->global, $data, NULL);
    }

    // AJAX: Get invoices for invoice collection grid
    public function invcollection_get_invoices() {
        $filterType = $this->input->post('filterType');
        $fromDate = $this->input->post('fromDate');
        $toDate = $this->input->post('toDate');
        $limit = $this->input->post('limit');
        if ($limit === 'all') {
            $limit = 10000; // or any reasonable max
        } else {
            $limit = intval($limit);
            if ($limit <= 0) $limit = 25;
        }
        $this->load->model('Invoices_model');
        $invoices = $this->Invoices_model->invcollection_get_invoices($filterType, $fromDate, $toDate, $limit);
        echo json_encode(['success' => true, 'data' => $invoices]);
        exit;
    }

    // AJAX: Add selected invoices to collection
    public function invcollection_add_invoices() {
        $this->load->model('Invoices_model');
        
        $invoiceIds = $this->input->post('invoiceIds');
        $collectionId = $this->input->post('collectionId');
        $paidAmounts = $this->input->post('paidAmounts');

        log_message('Info', "billing-collection-add-invoices: " . json_encode($invoiceIds) . " - " . json_encode($collectionId) . " - " . json_encode($paidAmounts));

        // Validate input
        if (empty($invoiceIds) || !is_array($invoiceIds) || empty($collectionId)) {
            echo json_encode(['success' => false, 'message' => 'Invalid input parameters']);
            exit;
        }
        
        // Update invoices to associate with collection and update paid amounts
        $result = $this->Invoices_model->invcollection_add_invoices($invoiceIds, $collectionId, $paidAmounts);
        
        if ($result) {
            // Update paid amounts for linked invoices only
            if (is_array($paidAmounts)) {
                foreach ($invoiceIds as $invid) {
                    if (isset($paidAmounts[$invid])) {
                        $this->Invoices_model->update_invoice_paid_amount($invid, $paidAmounts[$invid]);
                    }
                }
            }

            // Update total paidamount in collection
            $totalPaid = 0;
            if (is_array($paidAmounts)) {
                foreach ($paidAmounts as $amt) {
                    $totalPaid += floatval($amt);
                }
            }
            $this->Invoices_model->update_collection($collectionId, ['paidamount' => $totalPaid]);

            echo json_encode([
                'success' => true, 
                'message' => 'Invoices added to collection successfully',
                'count' => count($invoiceIds)
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add invoices to collection']);
        }
        exit;
    }

    public function invcollection_test() {
        $invoiceIds = array(1, 2, 3);
        $collectionId = 1;
        $result = $this->Invoices_model->invcollection_add_invoices($invoiceIds, $collectionId);
        echo json_encode(['success' => true, 'message' => 'Test completed']);
        exit;
    }

    // Invoice Collection: List all collections
    public function invcollection_list($page = 0) {
        $this->load->model('Invoices_model');
        
        // Get filter values from POST or set to empty
        $searchWalletId = $this->input->post('walletid');
        $searchFromDate = $this->input->post('fromdate');
        $searchToDate = $this->input->post('todate');

        $this->load->library('pagination');
        // Count with filters
        $count = $this->Invoices_model->invcollection_count($searchWalletId, $searchFromDate, $searchToDate);

        $returns = $this->paginationCompress("invcollection_list/", $count, 50);

        // Get filtered collections
        $data['collections'] = $this->Invoices_model->invcollection_list($returns['page'], $returns['segment'], $searchWalletId, $searchFromDate, $searchToDate);
        // For filter dropdown
        $data['wallets'] = $this->Invoices_model->invcollection_get_wallets();
        $data['searchWalletId'] = $searchWalletId;
        $data['searchFromDate'] = $searchFromDate;
        $data['searchToDate'] = $searchToDate;
        $this->global['pageTitle'] = 'Invoice Collections';
        $this->loadViews("invcollection/list", $this->global, $data, NULL);
    }

    // Edit an existing invoice collection
    public function invcollection_edit($collectionId = null) {
        $this->load->model('Invoices_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        if (!$collectionId) {
            redirect('Invoices/invcollection_list');
        }

        // Get collection record
        $collection = $this->Invoices_model->get_collection_by_id($collectionId);
        if (!$collection) {
            $this->session->set_flashdata('error', 'Collection not found.');
            redirect('Invoices/invcollection_list');
        }

        // Get all wallets for dropdown
        $wallets = $this->Invoices_model->invcollection_get_wallets();
        // Get all invoices linked to this collection
        $invoices = $this->Invoices_model->get_invoices_by_collection($collectionId);

        $data = [
            'collection' => $collection,
            'wallets' => $wallets,
            'invoices' => $invoices,
            'success' => false,
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->form_validation->set_rules('walletid', 'Wallet', 'required|numeric');
            $this->form_validation->set_rules('remarks', 'Remarks', 'trim|max_length[255]');

            if ($this->form_validation->run() == TRUE) {
                $walletid = $this->security->xss_clean($this->input->post('walletid'));
                $remarks = $this->security->xss_clean($this->input->post('remarks'));
                $paidAmounts = $this->input->post('paidAmounts'); // array: invoice_id => paid_amount

                // Update collection
                $updateData = [
                    'walletid' => $walletid,
                    'remarks' => $remarks
                ];
                $this->Invoices_model->update_collection($collectionId, $updateData);

                // Update paid amounts for linked invoices only
                if (is_array($paidAmounts)) {
                    foreach ($invoices as $inv) {
                        $invid = $inv->transid;
                        if (isset($paidAmounts[$invid])) {
                            $this->Invoices_model->update_invoice_paid_amount($invid, $paidAmounts[$invid]);
                        }
                    }
                }

                // Update total paidamount in collection
                $totalPaid = 0;
                if (is_array($paidAmounts)) {
                    foreach ($paidAmounts as $amt) {
                        $totalPaid += floatval($amt);
                    }
                }
                $this->Invoices_model->update_collection($collectionId, ['paidamount' => $totalPaid]);

                $data['success'] = true;
                $data['collection'] = $this->Invoices_model->get_collection_by_id($collectionId);
                $data['invoices'] = $this->Invoices_model->get_invoices_by_collection($collectionId);
            } else {
                $data['error'] = validation_errors();
            }
        }

        $this->loadViews('invcollection/edit', $this->global, $data, NULL);
    }

    // Invoice Collection: Delete a collection (only if created within 24 hours)
    public function invcollection_delete($collectionId) {
        $this->load->model('Invoices_model');
        $collection = $this->Invoices_model->get_collection_by_id($collectionId);
        if (!$collection) {
            $this->session->set_flashdata('error', 'Collection not found.');
            redirect('Invoices/invcollection_list');
        }
        $createdTime = strtotime($collection->createdDtm);
        $now = time();
        if (($now - $createdTime) > 86400) {
            $this->session->set_flashdata('error', 'Cannot delete: Only collections created within the last 24 hours can be deleted.');
            redirect('Invoices/invcollection_list');
        }
        $result = $this->Invoices_model->delete_collection_and_release_invoices($collectionId);
        if ($result) {
            $this->session->set_flashdata('success', 'Collection deleted and invoices released successfully.');
        } else {
            $this->session->set_flashdata('error', 'Failed to delete collection.');
        }
        redirect('Invoices/invcollection_list');
    }

    // Activation Ticket: Add New
    public function activationTicket_addNew() {
        
        $this->load->helper(array('form', 'url'));
        $this->load->library('form_validation');

        $managername = $this->session->userdata('name');
        $data['gracedays_old'] = $this->Invoices_model->getManagerSettings_bytype($managername, "GRACE-DAYS-OLD");
        $data['gracedays_new'] = $this->Invoices_model->getManagerSettings_bytype($managername, "GRACE-DAYS-NEW");
        $data['users'] = $this->Invoices_model->activationTicket_get_users($managername);
        $data['accounts'] = $this->Invoices_model->activationTicket_get_accounts();

        if ($this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(3, 4))) {
            $data['acttypes'] = array(
                1 => 'Gracedays'
            );
        }else{

            $data['acttypes'] = array(
                0 => 'Activation',
                1 => 'Gracedays',
                2 => 'Deployment',
                3 => 'Suspension'
            );
        }

        $data['actstatus'] = array(
            0 => 'PENDING',
            1 => 'APPROVED'
        );

        $data['success'] = false;
        $data['error'] = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $actType = (int)$this->input->post('acttype');
            $username = trim((string)$this->input->post('username'));

            // Block Gracedays activation if user is beyond GRACE-DAYS-OLD from last Recharge/Activation invoice.
            if ($actType === 1 && !empty($username)) {
                $gracedaysOld = isset($data['gracedays_old'][0]->stgvalue) ? (int)$data['gracedays_old'][0]->stgvalue : 0;
                $lastInvoice = $this->Invoices_model->getLastRechargeOrActivationInvoice($username);

                if (!empty($lastInvoice) && !empty($lastInvoice->expdate) && $lastInvoice->expdate !== '0000-00-00') {
                    $expiryDateObj = DateTime::createFromFormat('Y-m-d', $lastInvoice->expdate);
                    if ($expiryDateObj !== false) {
                        $allowedUntil = clone $expiryDateObj;
                        if ($gracedaysOld > 0) {
                            $allowedUntil->modify('+' . $gracedaysOld . ' days');
                        }
                        $today = new DateTime(date('Y-m-d'));

                        if ($today > $allowedUntil) {
                            $data['error'] = 'Grace days activation not allowed. User is beyond the allowed grace period.';
                            $this->global['pageTitle'] = 'Activation Ticket';
                            $this->loadViews('activationticket/addNew', $this->global, $data, NULL);
                            return;
                        }
                    }
                }
            }

            $main = array(
                'acttype' => $this->input->post('acttype'),
                'username' => $this->input->post('username'),
                'srvdate' => $this->input->post('srvdate'),
                'expdate' => $this->input->post('expdate'),
                'totalamount' => $this->input->post('totalamount'),
                'actstatus' => $this->input->post('actstatus'),
                'jvid' => 0,
                'remarks' => $this->input->post('remarks')
            );

            $main_id = $this->Invoices_model->activationTicket_add_main($main);
            log_message('info', 'subscribers-manage-activation-ticket: Invoice Created -> ' . json_encode($main));

            if ($main_id) {

                $userDetails = $this->users_model->getUserInfo($this->input->post('username'));

                if ($this->input->post('actstatus') == 1 && in_array($this->input->post('acttype'), [0, 1, 2])) {

                    $serviceDetails = $this->Invoices_model->getPackagePrice($userDetails->owner, $userDetails->srvid);
                    // Post Invoice if ACTIVATION Approved - 1 => 'APPROVED',
                    $invoiceAdd = array('username'=>$userDetails->username,
                            'srvid'=>$serviceDetails->srvid, 
                            'managername'=>$userDetails->owner,
                            'createdBy'=>0,
                            'invtype'=> ($this->input->post('acttype') == 0) ? 'Activation' : (($this->input->post('acttype') == 1) 
                                        ? 'Gracedays' : (($this->input->post('acttype') == 2) ? 'Installation' : 'Suspension')),
                            'srvdate'=>$this->input->post('srvdate'),
                            'expdate'=>$this->input->post('expdate'),
                            'price'=>abs($this->input->post('totalamount')),
                            'amount'=>-abs($this->input->post('totalamount')),
                            'remarks'=>$this->input->post('remarks'));

                    $invtransid = $this->Invoices_model->addRecharge($invoiceAdd); // Generate Customer Invoice
                    $this->Invoices_model->update_activation_ticket_invtransid($main_id, $invtransid);
                    log_message('info', 'invoice-save-activation-ticket: Invoice Created -> ' . json_encode($invoiceAdd));

                    if($userDetails->expiration < $this->input->post('expdate')) {
                        $qryUpdateExpiry = array('expiration'=>$this->input->post('expdate').' 12:00:00');
                        $expiryupdate = $this->Invoices_model->updateExpiry($qryUpdateExpiry, $userDetails->username, $this->input->post('expdate'));
                    }

                }elseif($this->input->post('actstatus') == 1 && $this->input->post('acttype') == 3) {

                    $qryUpdateExpiry = array('expiration'=>$this->input->post('srvdate').' 12:00:00');
                    $expiryupdate = $this->Invoices_model->updateExpiry($qryUpdateExpiry, $userDetails->username, $this->input->post('expdate'));

                }

                // Add Details for Activation Ticket
                $acctid = $this->input->post('acctid');
                $details = $this->input->post('details');
                $invqty = $this->input->post('invqty');
                $invprice = $this->input->post('invprice');
                $invamount = $this->input->post('invamount');
                if (is_array($acctid)) {
                    for ($i = 0; $i < count($acctid); $i++) {
                        $detail = array(
                            'transid' => $main_id,
                            'acctid' => $acctid[$i],
                            'details' => $details[$i],
                            'invqty' => $invqty[$i],
                            'invprice' => $invprice[$i],
                            'invamount' => $invamount[$i],
                            'accjvid' => 0
                        );
                        $this->Invoices_model->activationTicket_add_detail($detail);

                        log_message('info', 'invoice-save-activation-ticket: Invoice DetailsCreated -> ' . json_encode($detail));
                    }
                }
                $data['success'] = true;
            } else {
                $data['error'] = 'Failed to create activation ticket.';
            }
        }

        $this->global['pageTitle'] = 'Activation Ticket';
        $this->loadViews('activationticket/addNew', $this->global, $data, NULL);
        
    }
    // Activation Ticket: List
    public function activationTicket_list() {
        $this->load->model('Invoices_model');
        $managername = $this->session->userdata('name');
        $acttype = $this->input->post('acttype');
        $searchText = $this->input->post('searchText');
        $data['acttype'] = $acttype;
        $data['searchText'] = $searchText;
        $data['tickets'] = $this->Invoices_model->activationTicket_list($managername, $acttype, $searchText);
        $data['acttype_label'] = array(
            0 => 'Activation',
            1 => 'Gracedays',
            2 => 'Deployment',
            3 => 'Suspension'
        );
        $this->global['pageTitle'] = 'Activation Ticket List';
        $this->loadViews('activationticket/list', $this->global, $data, NULL);
    }
    // Activation Ticket: View
    public function activationTicket_view($id) {

        $this->load->model('Invoices_model');
        $data['main'] = $this->Invoices_model->activationTicket_get_main($id);
        $data['details'] = $this->Invoices_model->activationTicket_get_details($id);
        $data['acttype_label'] = array(
            0 => 'Activation',
            1 => 'Gracedays',
            2 => 'Deployment',
            3 => 'Suspension'
        );
        // Pass status update messages to view
        $data['status_update_success'] = $this->session->flashdata('status_update_success');
        $data['status_update_error'] = $this->session->flashdata('status_update_error');

        $this->global['pageTitle'] = 'Activation Ticket View';
        $this->loadViews('activationticket/view', $this->global, $data, NULL);

    }

    // Update status of activation ticket
    public function activationTicket_update_status($id) {
        
        $this->load->model('Invoices_model');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_status = $this->input->post('actstatus');
            $mainTicket = $this->Invoices_model->activationTicket_get_main($id);

            if (in_array($new_status, ['1', '2'])) {

                if($mainTicket->srvdate >= date("Y-m-d")) {
                    $result = $this->Invoices_model->update_activation_ticket_status($id, $new_status);
                }else{
                    $this->session->set_flashdata('status_update_error', false);
                    $this->session->set_flashdata('error', 'Service date passed now you cannot cancel it.');
                    return redirect('Invoices/activationTicket_view/' . $id);
                }

                if($new_status == 1 && $mainTicket->invtransid == 0) {

                    $userDetails = $this->users_model->getUserInfo($mainTicket->username);
                    $serviceDetails = $this->Invoices_model->getPackagePrice($userDetails->owner, $userDetails->srvid);
                    
                    if($mainTicket->acttype == 0 || $mainTicket->acttype == 1 || $mainTicket->acttype == 2) {
                        // Post Invoice if ACTIVATION Approved - 1 => 'APPROVED',
                        $invoiceAdd = array('username'=>$userDetails->username,
                                'srvid'=>$serviceDetails->srvid, 
                                'managername'=> $userDetails->owner,
                                'createdBy'=> 0,
                                'invtype'=> ($mainTicket->acttype == 0) ? 'Activation' : (($mainTicket->acttype == 1) 
                                            ? 'Gracedays' : (($mainTicket->acttype == 2) ? 'Installation' : 'Suspension')),
                                'srvdate'=> $mainTicket->srvdate,
                                'expdate'=> $mainTicket->expdate,
                                'price'=> $mainTicket->totalamount,
                                'amount'=> -$mainTicket->totalamount,
                                'remarks'=> $mainTicket->remarks);

                        log_message('info', 'invoice-save-activation-ticket: Invoice Created -> ' . json_encode($invoiceAdd));
                        $invtransid = $this->Invoices_model->addRecharge($invoiceAdd);
                        $this->Invoices_model->update_activation_ticket_invtransid($id, $invtransid);

                        if($userDetails->expiration < $mainTicket->expdate) {
                            $qryUpdateExpiry = array('expiration'=>$mainTicket->expdate.' 12:00:00');
                            $expiryupdate = $this->Invoices_model->updateExpiry($qryUpdateExpiry, $mainTicket->username, $mainTicket->expdate);
                        }

                    }elseif($mainTicket->acttype == 3) {
                        $qryUpdateExpiry = array('expiration'=>$mainTicket->srvdate.' 12:00:00');
                        $expiryupdate = $this->Invoices_model->updateExpiry($qryUpdateExpiry, $mainTicket->username, $mainTicket->expdate);
                    }

                }elseif($new_status == 2 && $mainTicket->invtransid != 0) {
                
                    $this->Invoices_model->update_activation_ticket_invtransid($id, 0);
                    $this->Invoices_model->delete_activation_ticket_invoice($mainTicket->invtransid);
                    $this->Invoices_model->reset__activation_ticket_Expiry($mainTicket->username);

                }

                if ($result) {
                    $this->session->set_flashdata('status_update_success', true);
                } else {
                    $this->session->set_flashdata('status_update_error', true);
                }
            } else {
                $this->session->set_flashdata('status_update_error', true);
            }
        }

        redirect('Invoices/activationTicket_view/' . $id);

    }

    // AJAX: Map payments for displayed invoices
    public function ajaxMapPayments() {

        $usernames = $this->input->post('usernames'); // array of usernames
        $fromDate = $this->input->post('fromDate'); // custom from date
        $toDate = $this->input->post('toDate'); // custom to date
        
        $result = [];
        if (is_array($usernames) && count($usernames) > 0) {
            $this->db->from('tbl_eptransaction');
            $this->db->where_in('username', $usernames);
            
            // Use custom date range if provided, otherwise use current month
            if (!empty($fromDate) && !empty($toDate)) {
                // Convert date format from YYYY-MM-DD to YYYYMMDD for database comparison
                $fromDateFormatted = date('Ymd', strtotime($fromDate));
                $toDateFormatted = date('Ymd', strtotime($toDate));
                $this->db->where('transaction_date >=', $fromDateFormatted);
                $this->db->where('transaction_date <=', $toDateFormatted);
            } else {
                // Default to current month
                $this->db->where('transaction_date >=', date('Ym01')); // First day of current month
                $this->db->where('transaction_date <=', date('Ymt'));  // Last day of current month
            }
            
            $this->db->order_by('transaction_date', 'DESC');
            $query = $this->db->get();
            foreach ($query->result() as $row) {
                $result[$row->username][] = [
                    'consumer_number' => $row->consumer_number,
                    'amount_paid' => $row->amount_paid,
                    'transaction_date' => $row->transaction_date,
                    'importdate' => $row->createdDtm
                ];
            }
        }
        echo json_encode(['success' => true, 'data' => $result]);
        exit;
        
    }

}
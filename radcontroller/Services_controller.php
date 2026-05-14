<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016 2017
 */
class Services_controller extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Services_model');
        $this->load->model('users_model');
        $this->isLoggedIn();   
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->global['pageTitle'] = 'Services : Dashboard';
        
        $this->loadViews("dashboard", $this->global, NULL , NULL);
    }

    function servicesListing($managerFilter = NULL)
    {

        if($this->perm_listservices == 0 && $this->session->userdata('login_type') != 'profile')
        {
            $this->session->set_flashdata('error', 'You do not have permission to access this page');
            redirect('dashboard');
        }elseif($this->perm_listservices == 1 && $this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(2,3,4))){
            $this->session->set_flashdata('error', 'Profile do not have permission to access this page');
            redirect('dashboard');
        }

        $managerInfo = $this->users_model->getManagerInfo($this->session->userdata ( 'name' ));
        $managerAllServices = $managerInfo->perm_createservices;
        
        if($managerAllServices == 0 && $this->session->userdata ( 'name' ) <> 'admin' && $this->ismaster > 0){
            $this->session->set_flashdata('error', 'You are not allowed to view reseller packages. Contact Admin to allow permission');
            //redirect('serviceslist');
        }
     
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;
        $type = $this->security->xss_clean($this->input->post('searchText1'));

        if(empty($managerFilter)){
            if(empty($type)) 
            { $defaultFilter = $this->Services_model->getDefaultManagerFilter($this->session->userdata ( 'name' ));
                if(!empty($defaultFilter))
                    { $type = $defaultFilter->managername; }
            }
        }else{
            $type = $managerFilter;
        }

        $this->load->library('pagination');
        
        $count = $this->Services_model->servicesCount($searchText, $type, $managerAllServices);

        $returns = $this->paginationCompress ( "serviceListing/", $count, 20 );

        //print_r($returns);
        //exit;

        $data['serviceListing'] = $this->Services_model->servicesListing($searchText, $type, $returns["page"], $returns["segment"], $managerAllServices);
        $data['type'] = $type;
        $data['managerList'] = $this->users_model->getManagersList();
        $data['managerFilter'] = $managerFilter;
        $this->global['pageTitle'] = 'Pace-Tel : Services';
        
        $this->loadViews("serviceslist", $this->global, $data, NULL);

    }

    function serviceAddNew()
    {

        if($this->perm_createservices == 0 && $this->session->userdata('login_type') != 'profile')
        {
            $this->session->set_flashdata('error', 'You do not have permission to access this page');
            redirect('dashboard');
        }elseif($this->perm_createservices == 1 && $this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(2,3,4))){
            $this->session->set_flashdata('error', 'Profile do not have permission to access this page');
            redirect('dashboard');
        }

        $this->load->model('users_model');
        $managername = $this->session->userdata ( 'name' );
        $managerInfo = $this->users_model->getManagerInfo($managername);
        if($managerInfo->perm_createservices == 1)
        {
            $this->load->model('users_model');
            $data['packages'] = $this->users_model->getPackages();
            $data['managername'] = $this->users_model->getManagersList();
            
            $this->global['pageTitle'] = 'Services : Add New User';

            $this->loadViews("servicesAddNew", $this->global, $data, NULL);
        }else{
            //echo "Not Allowed..........";
            $this->session->set_flashdata('error', 'Operation not allowed........');
            redirect('serviceslist');
        }
    }

    function saveService()
    {

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('srvname','Service Name','trim|required|max_length[64]|callback_serviceNameExists');
            $this->form_validation->set_rules('service','Service','required');
            $this->form_validation->set_rules('manager','Manager Name','required');
            $this->form_validation->set_rules('costprice','Cost Price','required|numeric|greater_than[0]');
            $this->form_validation->set_rules('saleprice','Sale Price','required|numeric|greater_than[0]');

            if($this->form_validation->run() == FALSE)
            {
                $this->serviceAddNew($this->input->post('srvname'));
            }
            else
            {
                $service = $this->security->xss_clean($this->input->post('srvname'));
                $manager = $this->security->xss_clean($this->input->post('manager'));
                $baseprice = $this->security->xss_clean($this->input->post('baseprice'));
                $price = $this->security->xss_clean($this->input->post('costprice'));
                $sale = $this->security->xss_clean($this->input->post('saleprice'));
                $srvid = $this->security->xss_clean($this->input->post('service'));
              
                $serviceInfo = array('srvname'=>$service,
                                    'radsrvid'=>$srvid,
                                    'managername'=>$manager,
                                    'baseprice'=>$baseprice,
                                    'costprice'=>$price,
                                    'saleprice'=>$sale);
                                    
                $this->load->model('users_model');
                $this->load->model('Services_model');

                if(($this->Services_model->checkServiceExists($service, $manager)) == false && 
                    ($this->Services_model->checkBaseServiceExists($manager, $srvid)) == false && 
                    ($this->Services_model->checkMasterServiceExists($manager, $srvid)) == true)
                {
                    $result = $this->Services_model->addService($serviceInfo);

                    if($result == True)
                    {
                        // Update allowed managers
                        $allowedManagers = $this->input->post('allowedmanagers');
                        $this->Services_model->spUpdateAllowedManagers($result, $allowedManagers);

                        $this->session->set_flashdata('success', 'Service Created successfully');
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Service creation failed');
                    }

                }else{
                    $this->session->set_flashdata('error', 'Service already exists or Master Reseller Service not allowed');
                }
                redirect('serviceslist');
            }
        }
    }

    function serviceNameExists($srvname)
    {

        if(!empty($srvname)){

            $managername = $this->session->userdata ( 'name' );
            $result = $this->Services_model->checkServiceExists($srvname, $managername);

            if(!empty($result))
                $this->form_validation->set_message('usernameExists', 'The {field} already taken');
                return true;
            }
            else{
                return false;
            }
    }

    function editService($srvid = NULL)
    {

        if($this->perm_editservices == 0 && $this->session->userdata('login_type') != 'profile')
        {
            $this->session->set_flashdata('error', 'You do not have permission to access this page');
            redirect('dashboard');
        }elseif($this->perm_editservices == 1 && $this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(2,3,4))){
            $this->session->set_flashdata('error', 'Profile do not have permission to access this page');
            redirect('dashboard');
        }

        if($srvid == null)
        {
            redirect('serviceslist');
        }

        $this->load->model('users_model');
        $managername = $this->session->userdata ( 'name' );
        $managerInfo = $this->users_model->getManagerInfo($managername);

        if($managerInfo->perm_editservices == 1)
        {

            $data['packages'] = $this->users_model->getPackages();
            $data['managername'] = $this->users_model->getManagersList();
            $data['serviceInfo'] = $this->Services_model->editServiceInfo($srvid);

            if(empty($data['serviceInfo'])){
                $this->session->set_flashdata('error', 'Service not found');
                redirect('serviceslist');
            }

            if($data['serviceInfo']->managername == $managername){
                $this->session->set_flashdata('error', 'You cannot edit your own service');
                redirect('serviceslist');
            }
            
            $this->global['pageTitle'] = 'Pace Radius : Edit User';
            
            $this->loadViews("serviceEditOld", $this->global, $data, NULL);

        }else{
            //$this->session->set_flashdata('error', 'Service creation failed');
            $this->session->set_flashdata('error', 'Operation not allowed........');
            redirect('serviceslist');
        }
    }

    function updateService()
    {

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {

            $this->load->library('form_validation');
            $srvid = $this->input->post('srvid');
            $pradsrvid = $this->input->post('pradsrvid');
            $this->form_validation->set_rules('service','Service','required');
            $this->form_validation->set_rules('manager','Manager Name','required');
            $this->form_validation->set_rules('baseprice','Base Price','required|numeric|greater_than[-1]');
            $this->form_validation->set_rules('costprice','Cost Price','required|numeric|greater_than[-1]');
            $this->form_validation->set_rules('saleprice','Sale Price','required|numeric|greater_than[-1]');

            if($this->form_validation->run() == FALSE)
            {
                $this->editService($srvid);
            }
            else
            {

                $srvid = $this->security->xss_clean($this->input->post('srvid'));
                $radsrvid = $this->security->xss_clean($this->input->post('service'));
                $manager = $this->security->xss_clean($this->input->post('manager'));
                $baseprice = $this->security->xss_clean($this->input->post('baseprice'));
                $price = $this->security->xss_clean($this->input->post('costprice'));
                $sale = $this->security->xss_clean($this->input->post('saleprice'));

                $serviceInfo = array('radsrvid'=>$radsrvid,
                                    'managername'=>$manager,
                                    'baseprice'=>$baseprice,
                                    'costprice'=>$price,
                                    'saleprice'=>$sale);
                
                $radServiceInfo = array('srvid'=>$radsrvid);
                                    
                $this->load->model('users_model');

                

                if(($this->users_model->checkManagerExist($manager)) == true)
                {

                    if(($this->Services_model->checkBaseServiceExists($manager, $radsrvid)) == false && 
                        ($this->Services_model->checkMasterServiceExists($manager, $radsrvid)) == true){

                        $result = $this->Services_model->updateService($serviceInfo, $srvid);

                    }else{
                        $serviceInfo = array('baseprice'=>$baseprice, 'costprice'=>$price, 'saleprice'=>$sale);
                        $result = $this->Services_model->updateService($serviceInfo, $srvid);
                    }

                    if($result == True)
                    {
                        $radServiceUpdate = $this->Services_model->updateUserServices($radServiceInfo, $radsrvid, $pradsrvid, $manager);
                        $this->session->set_flashdata('success', 'Service Updated successfully. Effected Customers '.$radServiceUpdate);
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Failed to update Service');
                    }
                
                }else{
                    $this->session->set_flashdata('error', 'Manager not exists or Service already assigned');
                }

                redirect('serviceslistFilter/'.$manager);

                
            }
        }
    }

    function changeUserService($userId = NULL)
    {

        if($this->perm_editusers == 0 && $this->session->userdata('login_type') != 'profile')
        {
            $this->session->set_flashdata('error', 'You do not have permission to access this page');
            redirect('dashboard');
        }elseif($this->perm_editusers == 1 && $this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(3,4))){
            $this->session->set_flashdata('error', 'Profile do not have permission to access this page');
            redirect('dashboard');
        }

        if($userId == null)
        {
            redirect('usersListing');
        }

        $this->load->model('users_model');
        $managername = $this->session->userdata ( 'name' );
        $managerInfo = $this->users_model->getManagerInfo($managername);

        if($managerInfo->enablemanager == 1)
        {

            $data['packages'] = $this->users_model->getResellerPackages();
            $data['managername'] = $this->users_model->getManagersList();
            $data['userInfo'] = $this->users_model->getUserInfo($userId);
            
            $this->global['pageTitle'] = 'Pace Radius : Change Service';
            
            $this->loadViews("usersChangeService", $this->global, $data, NULL);

        }else{
            $this->session->set_flashdata('error', 'Service update failed');
        }

    }

    function updateUserService()
    {

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {

            $managername = $this->session->userdata ( 'name' );
            $user = $this->input->post('user');
            $srvid = $this->input->post('service');

            $this->load->model('users_model');
            $this->load->model('Invoices_model');
            $this->load->model('Services_model');

            $userSuspensionTicket = $this->Invoices_model->activationTicket_get_usertype($user, 3);

            if(!empty($userSuspensionTicket) && $userSuspensionTicket->expdate <= date("Y-m-d"))
            {
                $this->session->set_flashdata('error', 'User is suspended');
                redirect('usersListing');
            }

            if($srvid <> 0)
            {

                //$prevSrvId = $this->Invoices_model->getUserPackage($user, $managername);
                $prevSrvId = $this->Invoices_model->getUserPrevPackage($user, $managername);
                $prevServiceInfo = $this->Services_model->getServiceInfo($prevSrvId->srvid); // Service Details of Old Service

                $radsrvid = $this->Services_model->getServiceInfo($srvid); // Service Details of New Service

                if(empty($radsrvid)){
                    $this->session->set_flashdata('error', 'Service not found in Master allowed packages');
                    redirect('usersListing');
                }

                $dateReset = $this->Services_model->getTrafficData($user);

                $userDetails = $this->users_model->getUserInfo($user);
                $userManager = $userDetails->owner;

                if(empty($prevSrvId))
                {
                    $prevradsrvId = $radsrvid->radsrvid;
                    $prevsrvId = $srvid;
                }else{
                    $prevradsrvId = $prevSrvId->radsrvid;
                    $prevsrvId = $prevSrvId->srvid;
                }

                //if(!empty($dateReset)){
                //    $dataUpLimit = $dateReset->uplimit-$dateReset->acctinputoctets;
                //    $dataDnLimit = $dateReset->downlimit-$dateReset->acctoutputoctets;
                //    $dataCmbLimit = $dataUpLimit + $dataDnLimit;
                //}
                //else{
                    $dataUpLimit = 0;
                    $dataDnLimit = 0;
                    $dataCmbLimit = 0;
                //}


                $masterInfo = $this->Invoices_model->getMasterManager($userManager);

                $expdate = $this->Invoices_model->getLastExpiry($user);
                $getMasterPackagePrice = $this->Invoices_model->getPackagePrice($masterInfo->mastername, $prevradsrvId);
                $getPackagePrice = $this->Invoices_model->getPackagePrice($managername, $prevradsrvId);

                $allowedFullRefund = false;

                if(!empty($getPackagePrice) && strtolower($masterInfo->mastername) <> "none" && $masterInfo->mastername <> '0'){


                    $servicePrice = $getPackagePrice->baseprice;
                    $serviceMasterPrice = $getMasterPackagePrice->baseprice;

                    $date1 = Date("Y-m-d");
                    //echo $expdate->expdate;
                    //exit;

                    //if(!empty($expdate->expdate) && $expdate->expdate != '0000-00-00')

                    $srvInfo = array('srvid'=>$radsrvid->radsrvid, 'expiration'=>Date("Y-m-d", strtotime(' -1 day')), 
                                'uplimit'=>abs($dataUpLimit), 'downlimit'=>abs($dataDnLimit), 'comblimit'=>abs($dataCmbLimit));
                                

                    if($expdate->expdate > date("Y-m-d") && $this->perm_fullrefund == 1){

                        //$srvUpdate = array('username'=>$user, 'attribute'=>'Cleartext-Password', 'op'=>':=', 'value'=>$password);
                        $result = $this->users_model->editUser($srvInfo, $user);
                        $refundresult = $this->Invoices_model->masterCreditRefund($user, $prevsrvId);
                        $allowedFullRefund = true;
                        $allowServiceChangeAdjustment = true;

                    }elseif($expdate->expdate > date("Y-m-d")){

                        $srvInfo = array('srvid'=>$radsrvid->radsrvid, 'uplimit'=>abs($dataUpLimit), 
                                    'downlimit'=>abs($dataDnLimit), 'comblimit'=>abs($dataCmbLimit));
                                
                        $downgradeInfo = array('username'=>$user, 'srvid'=>$srvid, 'price'=>$radsrvid->baseprice, 
                                    'amount'=>-$radsrvid->baseprice, 'invtype'=>'Recharge', 'managername'=>$userManager, 
                                    'createdBy'=>0, 'remarks'=>'Package change from '.$prevradsrvId.' to '.$srvid, 
                                    'paid'=>0.00, 'crdays'=>0, 'jvid'=>0, 'radsrvid'=>$radsrvid->radsrvid, 
                                    'expdate'=>$expdate->expdate, 'srvname'=>$radsrvid->srvname, 'prevsrvname'=>$prevServiceInfo->srvname);
                        
                        $result = $this->users_model->editUser($srvInfo, $user);
                        if($prevServiceInfo->baseprice < $radsrvid->baseprice || $this->perm_allowdowngrade == 1){
                            $refundresult = $this->Invoices_model->packagechangeadjustment($downgradeInfo);
                            $allowServiceChangeAdjustment = true;
                        }

                    }else{

                        $result = $this->users_model->editUser($srvInfo, $user);
                        $refundresult = false;
                        $allowServiceChangeAdjustment = false;
                    }

                    $this->manageUserRadusergroup(['srvname' => $userDetails->srvname, 'username' => $user, 'priority' => 1]);

                    log_message('info', "services-updateUserService: ".$user. " details: " . json_encode($srvInfo));

                    if($refundresult == true){
                        $this->session->set_flashdata('success', 
                        ($allowServiceChangeAdjustment) ? ' User Package successfully Changed with refund. (Package Change Adjustment made) ' 
                        : 'User Package successfully Changed with refund. (Downgrade or full refund not allowed)');
                    }else{
                        $this->session->set_flashdata('success', 'User Package successfully Changed.');
                    }
                
                }else{
                    $this->session->set_flashdata('error', 'User Packages not changed');
                }

                
            }
            else
            {

                $this->session->set_flashdata('error', 'User updation failed');

            }


            redirect('usersListing');

        }
    }

    function getRefundPrice($unitprice, $srv_date, $exp_date)
    {

        $managername = $this->session->userdata ( 'name' );

        //$service_id = $this->input->post('service_id');
        //$exp_date = $this->input->post('exp_date');
        //$srv_date = $this->input->post('srv_date');

        //$result = $this->Invoices_model->getPackagePriceSrvID($managername, $service_id);
        //$result = $this->Invoices_model->getPackagePrice($managername, $radsrvid);

        //$unitprice = $result->unitprice;
        //$saleprice = $result->saleprice;
        $saleprice = $unitprice;

        $date1 = date_create($exp_date);
        $date2 = date_create($srv_date);
        //$date2 = $srv_date;

        $diff=date_diff($date1,$date2);
        $daysinmonth = $diff->format("%a");

        //echo "DAYS REMAINING: ".$daysinmonth;
        //exit;

        $tot_srvmonthdays=cal_days_in_month(CAL_GREGORIAN,date("m",strtotime($srv_date)),date("y",strtotime($srv_date)));
        $tot_expmonthdays=cal_days_in_month(CAL_GREGORIAN,date('m', strtotime("+0 day", strtotime($exp_date))),date("y",strtotime($exp_date)));

        $daysinsrvmonth = ($tot_srvmonthdays+1) - (date('d', strtotime("+0 day", strtotime($srv_date))));
        $daysinexpmonth = date('d', strtotime("+0 day", strtotime($exp_date)))-1;

        /////////////$chargeinsrvmonth = ($unitprice/$tot_srvmonthdays)*$daysinsrvmonth;
        /*if($tot_srvmonthdays <> $tot_expmonthdays){
            $chargeinexpmonth = (($unitprice/$tot_srvmonthdays) * $daysinsrvmonth) + (($unitprice/$tot_expmonthdays)*$daysinexpmonth);
        }
        else{
            $chargeinexpmonth = ($unitprice/$tot_srvmonthdays) * $daysinmonth;
        }*/

        $chargeinexpmonth = ($unitprice/$tot_srvmonthdays) * ($daysinmonth-1);

        $price1 = round($chargeinexpmonth,0);

        //$info = array(['unitprice'=> $price1, 'saleprice'=> $price1]);
        //echo json_encode($info);
        return $price1;

    }


    function export_tocsv($status = 0, $expiry = 0)
    {

        $file_name = 'service_list_on_'.date('Ymd').'.csv'; 
        header("Content-Description: File Transfer"); 
        header("Content-Disposition: attachment; filename=$file_name"); 
        header("Content-Type: application/csv;");

        // get data 
        $user_data = $this->Services_model->services_tocsv($status, $expiry);

        // file creation 
        $file = fopen('php://output', 'w');
    
        $header = array("Service", "Manager", "CostPrice", "SalePrice", "Users", "Cost", "Revenue"); 
        fputcsv($file, $header);
        foreach ($user_data->result_array() as $key => $value)
        { 
          fputcsv($file, $value); 
        }
        fclose($file); 
        exit;
    }

    function getManagerPackages($managername = NULL){

        $managername = $this->input->post('managername');
        $result = $this->Services_model->getPrepaidPackages($managername);
        echo json_encode($result);
    }

    /**
     * This function is used to load the service profile list
     */
    public function spList()
    {

        if($this->perm_listservices == 0 && $this->perm_allusers == 0 && $this->session->userdata('login_type') != 'profile')
        {
            $this->session->set_flashdata('error', 'You do not have permission to access this page');
            redirect('dashboard');
        }elseif($this->perm_listservices == 1 && $this->perm_allusers == 1 && $this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(2,3,4))){
            $this->session->set_flashdata('error', 'Profile do not have permission to access this page');
            redirect('dashboard');
        }
       
        $this->load->model('users_model');
        $managerInfo = $this->users_model->getManagerInfo($this->session->userdata('name'));
        $managerAllServices = $managerInfo->perm_createservices;
        
        if($managerAllServices == 0 && $this->session->userdata('name') <> 'admin' && $this->ismaster > 0){
            $this->session->set_flashdata('error', 'You are not allowed to view service profiles. Contact Admin to allow permission');
            redirect('dashboard');
        }
        
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;
        
        $this->load->library('pagination');
        
        $count = $this->Services_model->spListingCount($searchText);
        
        $returns = $this->paginationCompress("spList/", $count, 10);

        $data['serviceRecords'] = $this->Services_model->spListing($searchText, $returns["page"], $returns["segment"]);
        
        $this->global['pageTitle'] = 'Pace-Tel : Service Profiles';
        
        $this->loadViews("servicesprofilelist", $this->global, $data, NULL);
    }

    /**
     * This function is used to load the add new service profile form
     */
    public function spAddNew()
    {
        

        if($this->perm_createservices == 0 && $this->perm_allusers == 0 && $this->session->userdata('login_type') != 'profile')
        {
            $this->session->set_flashdata('error', 'You do not have permission to access this page');
            redirect('dashboard');
        }elseif($this->perm_createservices == 1 && $this->perm_allusers == 1 && $this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(2,3,4))){
            $this->session->set_flashdata('error', 'Profile do not have permission to access this page');
            redirect('dashboard');
        }
        
            $this->load->model('users_model');
            $managername = $this->session->userdata('name');
            $managerInfo = $this->users_model->getManagerInfo($managername);
            
            if($managerInfo->perm_createservices == 1)
            {
                $data['nasList'] = $this->Services_model->getNases();
                $data['managersList'] = $this->Services_model->getManagers();
                
                // Add data for Radius Attributes tab
                $data['groupAttributes'] = $this->Services_model->getGroupAttributes();
                $data['operators'] = $this->Services_model->getOperators();
                
                $this->global['pageTitle'] = 'Pace-Tel : Add New Service Profile';
                
                $this->loadViews("servicesprofileAddNew", $this->global, $data, NULL);
            }
            else
            {
                $this->session->set_flashdata('error', 'Operation not allowed........');
                redirect('spList');
            }
        
    }

    /**
     * This function is used to save the service profile to the database
     */
    public function spSaveService()
    {
    
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('srvname','Service Name','trim|required|max_length[50]');
        $this->form_validation->set_rules('downrate','Download Rate','required|numeric');
        $this->form_validation->set_rules('uprate','Upload Rate','required|numeric');
        
        if($this->form_validation->run() == FALSE)
        {
            $this->spAddNew();
        }
        else
        {
            $srvname = $this->security->xss_clean($this->input->post('srvname'));
            $descr = $this->security->xss_clean($this->input->post('descr'));
            $downrate = $this->security->xss_clean($this->input->post('downrate'));
            $uprate = $this->security->xss_clean($this->input->post('uprate'));
            $limitdl = $this->security->xss_clean($this->input->post('limitdl'));
            $limitul = $this->security->xss_clean($this->input->post('limitul'));
            $limitcomb = $this->security->xss_clean($this->input->post('limitcomb'));
            $limitexpiration = $this->security->xss_clean($this->input->post('limitexpiration'));
            $limituptime = $this->security->xss_clean($this->input->post('limituptime'));
            $poolname = $this->security->xss_clean($this->input->post('poolname'));
            $enableservice = $this->security->xss_clean($this->input->post('enableservice'));
            $srvtype = $this->security->xss_clean($this->input->post('srvtype'));
            
            // Get the next available srvid
            $nextSrvId = $this->Services_model->getNextSrvId();
            
            // After collecting the existing form inputs, add the new fields:
            $enableburst = $this->security->xss_clean($this->input->post('enableburst'));
            $dlburstlimit = $this->security->xss_clean($this->input->post('dlburstlimit'));
            $ulburstlimit = $this->security->xss_clean($this->input->post('ulburstlimit'));
            $dlburstthreshold = $this->security->xss_clean($this->input->post('dlburstthreshold'));
            $ulburstthreshold = $this->security->xss_clean($this->input->post('ulburstthreshold'));
            $dlbursttime = $this->security->xss_clean($this->input->post('dlbursttime'));
            $ulbursttime = $this->security->xss_clean($this->input->post('ulbursttime'));

            // Validate burst thresholds
            if($enableburst) {
                if($dlburstthreshold >= $dlburstlimit) {
                    $this->session->set_flashdata('error', 'Download burst threshold must be less than download burst limit');
                    redirect('Services_controller/spAddNew');
                    return;
                }
                if($ulburstthreshold >= $ulburstlimit) {
                    $this->session->set_flashdata('error', 'Upload burst threshold must be less than upload burst limit');
                    redirect('Services_controller/spAddNew');
                    return;
                }
            }
            
            // Modify the serviceInfo array to include the new fields
            $serviceInfo = array(
                'srvid' => $nextSrvId,
                'srvname' => $srvname,
                'descr' => $descr,
                'downrate' => ($downrate*1024)*1024,
                'uprate' => ($uprate*1024)*1024,
                'limitdl' => $limitdl,
                'limitul' => $limitul,
                'limitcomb' => $limitcomb,
                'limitexpiration' => $limitexpiration,
                'limituptime' => $limituptime,
                'poolname' => $poolname,
                'enableservice' => $enableservice ? $enableservice : 0,
                'srvtype' => $srvtype ? $srvtype : 0,
                // Add the new burst fields
                'enableburst' => $enableburst ? $enableburst : 0,
                'dlburstlimit' => $dlburstlimit ? ($dlburstlimit*1024)*1024 : 0,
                'ulburstlimit' => $ulburstlimit ? ($ulburstlimit*1024)*1024 : 0,
                'dlburstthreshold' => $dlburstthreshold ? ($dlburstthreshold*1024)*1024 : 0,
                'ulburstthreshold' => $ulburstthreshold ? ($ulburstthreshold*1024)*1024 : 0,
                'dlbursttime' => $dlbursttime ? $dlbursttime : 0,
                'ulbursttime' => $ulbursttime ? $ulbursttime : 0,
                // Add billing fields
                'pricecalcdownload' => $this->input->post('pricecalcdownload') ? 1 : 0,
                'pricecalcupload' => $this->input->post('pricecalcupload') ? 1 : 0,
                'pricecalcuptime' => $this->input->post('pricecalcuptime') ? 1 : 0,
                'monthly' => $this->input->post('monthly') ? 1 : 0,
                'renew' => $this->input->post('renew') ? 1 : 0,
                'carryover' => $this->input->post('carryover') ? 1 : 0,
                'resetcounters' => $this->input->post('resetcounters') ? 1 : 0,
                'enaddcredits' => $this->input->post('enaddcredits') ? 1 : 0,
                'unitpricetax' => $this->security->xss_clean($this->input->post('unitpricetax')),
                'unitpriceaddtax' => $this->security->xss_clean($this->input->post('unitpriceaddtax')),
                'unitpriceadd' => $this->security->xss_clean($this->input->post('unitpriceadd')),
                // Add expiration tab fields
                'timeaddmodeexp' => $this->security->xss_clean($this->input->post('timeaddmodeexp')),
                'timeaddmodeonline' => $this->security->xss_clean($this->input->post('timeaddmodeonline')),
                'trafficaddmode' => $this->security->xss_clean($this->input->post('trafficaddmode')),
                'timebaseexp' => $this->security->xss_clean($this->input->post('timebaseexp')),
                'timeunitexp' => $this->security->xss_clean($this->input->post('timeunitexp')),
                'inittimeexp' => $this->security->xss_clean($this->input->post('inittimeexp')),
                'timebaseonline' => $this->security->xss_clean($this->input->post('timebaseonline')),
                'timeunitonline' => $this->security->xss_clean($this->input->post('timeunitonline')),
                'inittimeonline' => $this->security->xss_clean($this->input->post('inittimeonline')),
                'trafficunitdl' => $this->security->xss_clean($this->input->post('trafficunitdl')),
                'initdl' => $this->security->xss_clean($this->input->post('initdl')),
                'trafficunitul' => $this->security->xss_clean($this->input->post('trafficunitul')),
                'initul' => $this->security->xss_clean($this->input->post('initul')),
                'trafficunitcomb' => $this->security->xss_clean($this->input->post('trafficunitcomb')),
                'inittotal' => $this->security->xss_clean($this->input->post('inittotal')),
                'minamount' => $this->security->xss_clean($this->input->post('minamount')),
                'minamountadd' => $this->security->xss_clean($this->input->post('minamountadd')),
                'addamount' => $this->security->xss_clean($this->input->post('addamount'))
            );
            
            // Initialize other required fields with default values
            $defaultFields = array(
                'unitprice' => 0,
                'dlquota' => 0,
                'ulquota' => 0,
                'combquota' => 0,
                'timequota' => 0,
                'priority' => 0,
                'nextsrvid' => -1,
                'dailynextsrvid' => -1,
                'disnextsrvid' => -1,
                'availucp' => 0,
                'policymapdl' => '',
                'policymapul' => '',
                'custattr' => '',
                'gentftp' => 0,
                'cmcfg' => '',
                'advcmcfg' => 0,
                'ignstatip' => 0
            );
            
            $serviceInfo = array_merge($serviceInfo, $defaultFields);
            
            $result = $this->Services_model->spAddNewService($serviceInfo);
            
            if($result == true)
            {
                // Update allowed NASes
                $allowedNases = $this->input->post('allowednases');
                $this->Services_model->spUpdateAllowedNases($nextSrvId, $allowedNases);

                // Update allowed managers
                $allowedManagers = $this->input->post('allowedmanagers');
                $this->Services_model->spUpdateAllowedManagers($nextSrvId, $allowedManagers);
                
                $this->session->set_flashdata('success', 'Service Profile updated successfully');
            }
            else
            {
                $this->session->set_flashdata('error', 'Service Profile update failed');
            }
            
            redirect('Services_controller/spList');
        }
        
    }

    /**
     * This function is used to load the edit service profile form
     */
    public function spEditOld($srvid = NULL)
    {


        if($this->perm_editservices == 0 && $this->perm_allusers == 0 && $this->session->userdata('login_type') != 'profile')
        {
            $this->session->set_flashdata('error', 'You do not have permission to access this page');
            redirect('dashboard');
        }elseif($this->perm_editservices == 1 && $this->perm_allusers == 1 && $this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(2,3,4))){
            $this->session->set_flashdata('error', 'Profile do not have permission to access this page');
            redirect('dashboard');
        }

        if($this->isAdmin() == FALSE || $srvid == null)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('users_model');
            
            $data['serviceInfo'] = $this->Services_model->spGetServiceInfo($srvid);
            
            if(empty($data['serviceInfo']))
            {
                $this->session->set_flashdata('error', 'Service Profile not found');
                redirect('Services_controller/spList');
            }
            
            $data['nasList'] = $this->Services_model->getNases();
            $data['allowedNases'] = $this->Services_model->spgetAllowedNases($srvid);
            
            // Get managers list and allowed managers
            $data['managersList'] = $this->Services_model->getManagers();
            $data['allowedManagers'] = $this->Services_model->getAllowedManagers($srvid);
            
            // Add data for Radius Attributes tab
            $data['groupAttributes'] = $this->Services_model->getGroupAttributes();
            $data['operators'] = $this->Services_model->getOperators();
            $data['radiusAttributes'] = $this->Services_model->getRadiusGroupReplyAttributes($data['serviceInfo']->srvname);
            
            $this->global['pageTitle'] = 'Pace-Tel : Edit Service Profile';
            
            $this->loadViews("servicesprofileEditOld", $this->global, $data, NULL);
        }
    }

    /**
     * This function is used to update the service profile information
     */
    public function spUpdateService()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
            $srvid = $this->input->post('srvid');
            
            $this->form_validation->set_rules('srvname','Service Name','trim|required|max_length[50]');
            $this->form_validation->set_rules('downrate','Download Rate','required|numeric');
            $this->form_validation->set_rules('uprate','Upload Rate','required|numeric');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->spEditOld($srvid);
            }
            else
            {
                $srvname = $this->security->xss_clean($this->input->post('srvname'));
                $descr = $this->security->xss_clean($this->input->post('descr'));
                $downrate = $this->security->xss_clean($this->input->post('downrate'));
                $uprate = $this->security->xss_clean($this->input->post('uprate'));
                $limitdl = $this->security->xss_clean($this->input->post('limitdl'));
                $limitul = $this->security->xss_clean($this->input->post('limitul'));
                $limitcomb = $this->security->xss_clean($this->input->post('limitcomb'));
                $limitexpiration = $this->security->xss_clean($this->input->post('limitexpiration'));
                $limituptime = $this->security->xss_clean($this->input->post('limituptime'));
                $poolname = $this->security->xss_clean($this->input->post('poolname'));
                $enableservice = $this->security->xss_clean($this->input->post('enableservice'));
                $srvtype = $this->security->xss_clean($this->input->post('srvtype'));
                
                // After collecting the existing form inputs, add the new fields:
                $enableburst = $this->security->xss_clean($this->input->post('enableburst'));
                $dlburstlimit = $this->security->xss_clean($this->input->post('dlburstlimit'));
                $ulburstlimit = $this->security->xss_clean($this->input->post('ulburstlimit'));
                $dlburstthreshold = $this->security->xss_clean($this->input->post('dlburstthreshold'));
                $ulburstthreshold = $this->security->xss_clean($this->input->post('ulburstthreshold'));
                $dlbursttime = $this->security->xss_clean($this->input->post('dlbursttime'));
                $ulbursttime = $this->security->xss_clean($this->input->post('ulbursttime'));

                // Validate burst thresholds
                if($enableburst) {
                    if($dlburstthreshold >= $dlburstlimit) {
                        $this->session->set_flashdata('error', 'Download burst threshold must be less than download burst limit');
                        redirect('Services_controller/spEditOld/'.$srvid);
                        return;
                    }
                    if($ulburstthreshold >= $ulburstlimit) {
                        $this->session->set_flashdata('error', 'Upload burst threshold must be less than upload burst limit');
                        redirect('Services_controller/spEditOld/'.$srvid);
                        return;
                    }
                }
                
                // Modify the serviceInfo array to include the new fields
                $serviceInfo = array(
                    'srvid' => $srvid,
                    'srvname' => $srvname,
                    'descr' => $descr,
                    'downrate' => ($downrate*1024)*1024,
                    'uprate' => ($uprate*1024)*1024,
                    'limitdl' => $limitdl,
                    'limitul' => $limitul,
                    'limitcomb' => $limitcomb,
                    'limitexpiration' => $limitexpiration,
                    'limituptime' => $limituptime,
                    'poolname' => $poolname,
                    'enableservice' => $enableservice,
                    'srvtype' => $srvtype,
                    // Add the new burst fields
                    'enableburst' => $enableburst ? $enableburst : 0,
                    'dlburstlimit' => $dlburstlimit ? ($dlburstlimit*1024)*1024 : 0,
                    'ulburstlimit' => $ulburstlimit ? ($ulburstlimit*1024)*1024 : 0,
                    'dlburstthreshold' => $dlburstthreshold ? ($dlburstthreshold*1024)*1024 : 0,
                    'ulburstthreshold' => $ulburstthreshold ? ($ulburstthreshold*1024)*1024 : 0,
                    'dlbursttime' => $dlbursttime ? $dlbursttime : 0,
                    'ulbursttime' => $ulbursttime ? $ulbursttime : 0,
                    // Add billing fields
                    'pricecalcdownload' => $this->input->post('pricecalcdownload') ? 1 : 0,
                    'pricecalcupload' => $this->input->post('pricecalcupload') ? 1 : 0,
                    'pricecalcuptime' => $this->input->post('pricecalcuptime') ? 1 : 0,
                    'monthly' => $this->input->post('monthly') ? 1 : 0,
                    'renew' => $this->input->post('renew') ? 1 : 0,
                    'carryover' => $this->input->post('carryover') ? 1 : 0,
                    'resetcounters' => $this->input->post('resetcounters') ? 1 : 0,
                    'enaddcredits' => $this->input->post('enaddcredits') ? 1 : 0,
                    'unitpricetax' => $this->security->xss_clean($this->input->post('unitpricetax')),
                    'unitpriceaddtax' => $this->security->xss_clean($this->input->post('unitpriceaddtax')),
                    'unitpriceadd' => $this->security->xss_clean($this->input->post('unitpriceadd')),
                    // Add expiration tab fields
                    'timeaddmodeexp' => $this->security->xss_clean($this->input->post('timeaddmodeexp')),
                    'timeaddmodeonline' => $this->security->xss_clean($this->input->post('timeaddmodeonline')),
                    'trafficaddmode' => $this->security->xss_clean($this->input->post('trafficaddmode')),
                    'timebaseexp' => $this->security->xss_clean($this->input->post('timebaseexp')),
                    'timeunitexp' => $this->security->xss_clean($this->input->post('timeunitexp')),
                    'inittimeexp' => $this->security->xss_clean($this->input->post('inittimeexp')),
                    'timebaseonline' => $this->security->xss_clean($this->input->post('timebaseonline')),
                    'timeunitonline' => $this->security->xss_clean($this->input->post('timeunitonline')),
                    'inittimeonline' => $this->security->xss_clean($this->input->post('inittimeonline')),
                    'trafficunitdl' => $this->security->xss_clean($this->input->post('trafficunitdl')),
                    'initdl' => $this->security->xss_clean($this->input->post('initdl')),
                    'trafficunitul' => $this->security->xss_clean($this->input->post('trafficunitul')),
                    'initul' => $this->security->xss_clean($this->input->post('initul')),
                    'trafficunitcomb' => $this->security->xss_clean($this->input->post('trafficunitcomb')),
                    'inittotal' => $this->security->xss_clean($this->input->post('inittotal')),
                    'minamount' => $this->security->xss_clean($this->input->post('minamount')),
                    'minamountadd' => $this->security->xss_clean($this->input->post('minamountadd')),
                    'addamount' => $this->security->xss_clean($this->input->post('addamount'))
                );
                
                $result = $this->Services_model->spUpdateService($serviceInfo, $srvid);
                
                if($result == true)
                {
                    // Update allowed NASes
                    $allowedNases = $this->input->post('allowednases');
                    $this->Services_model->spUpdateAllowedNases($srvid, $allowedNases);
                    
                    // Update allowed managers
                    $allowedManagers = $this->input->post('allowedmanagers');
                    $this->Services_model->spUpdateAllowedManagers($srvid, $allowedManagers);
                    
                    $this->session->set_flashdata('success', 'Service Profile updated successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Service Profile update failed');
                }
                
                redirect('spList');
            }
        }
    }

    /**
     * This function is used to export service profiles to CSV
     */
    public function splist_csv($status = 0, $expiry = 0)
    {
        $file_name = 'service_profiles_list_on_'.date('Ymd').'.csv'; 
        header("Content-Description: File Transfer"); 
        header("Content-Disposition: attachment; filename=$file_name"); 
        header("Content-Type: application/csv;");

        // get data 
        $user_data = $this->Services_model->spToCSV($status, $expiry);

        // file creation 
        $file = fopen('php://output', 'w');

        $header = array("ID", "Service Name", "Description", "Download Rate", "Upload Rate", "Status"); 
        fputcsv($file, $header);
        foreach ($user_data->result_array() as $key => $value)
        { 
            fputcsv($file, $value); 
        }
        fclose($file); 
        exit;
    }

    /**
     * This function is used to load the attributes list
     */
    public function attributesList()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('Services_model');
            
            $searchText = $this->security->xss_clean($this->input->post('searchText'));
            $filterType = $this->security->xss_clean($this->input->post('filterType'));
            
            $data['searchText'] = $searchText;
            $data['filterType'] = $filterType;
            
            $this->load->library('pagination');
            
            $count = $this->Services_model->attributesListingCount($searchText, $filterType);

            $returns = $this->paginationCompress("attributesList/", $count, 10);
            
            $data['attributesRecords'] = $this->Services_model->attributesListing($searchText, $returns["page"], $returns["segment"], $filterType);
            
            $this->global['pageTitle'] = 'RadSPOT : Attributes Listing';
            
            $this->loadViews("attributesList", $this->global, $data, NULL);
        }
    }

    /**
     * This function is used to load the add new attribute form
     */
    public function addNewAttribute()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->global['pageTitle'] = 'RadSPOT : Add New Attribute';
            
            $this->loadViews("attributeAddNew", $this->global, NULL, NULL);
        }
    }

    /**
     * This function is used to add a new attribute to the system
     */
    public function insertAttribute()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
            $this->form_validation->set_rules('attname','Attribute Name','trim|required|max_length[255]');
            $this->form_validation->set_rules('atttype','Attribute Type','trim|required');
            $this->form_validation->set_rules('descr','Description','trim|max_length[255]');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->addNewAttribute();
            }
            else
            {
                $attname = $this->security->xss_clean($this->input->post('attname'));
                $atttype = $this->security->xss_clean($this->input->post('atttype'));
                $descr = $this->security->xss_clean($this->input->post('descr'));
                
                $this->load->model('Services_model');
                
                $attributeInfo = array(
                    'attname' => $attname,
                    'atttype' => $atttype,
                    'descr' => $descr
                );
                
                $result = $this->Services_model->addAttribute($attributeInfo);
                
                if($result > 0)
                {
                    $this->session->set_flashdata('success', 'New attribute created successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Attribute creation failed');
                }
                
                redirect('Services_controller/attributesList');
            }
        }
    }

    /**
     * This function is used to load the edit attribute form
     */
    public function editAttribute($attid = NULL)
    {
        if($this->isAdmin() == FALSE || $attid == null)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('Services_model');
            
            $data['attributeInfo'] = $this->Services_model->getAttributeInfo($attid);
            
            $this->global['pageTitle'] = 'RadSPOT : Edit Attribute';
            
            $this->loadViews("attributeEdit", $this->global, $data, NULL);
        }
    }

    /**
     * This function is used to update the attribute information
     */
    public function updateAttribute()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            
            $attid = $this->input->post('attid');
            
            $this->form_validation->set_rules('attname','Attribute Name','trim|required|max_length[255]');
            $this->form_validation->set_rules('atttype','Attribute Type','trim|required');
            $this->form_validation->set_rules('descr','Description','trim|max_length[255]');
            
            if($this->form_validation->run() == FALSE)
            {
                $this->editAttribute($attid);
            }
            else
            {
                $attname = $this->security->xss_clean($this->input->post('attname'));
                $atttype = $this->security->xss_clean($this->input->post('atttype'));
                $descr = $this->security->xss_clean($this->input->post('descr'));
                
                $this->load->model('Services_model');
                
                $attributeInfo = array(
                    'attname' => $attname,
                    'atttype' => $atttype,
                    'descr' => $descr,
                    'createdDtm' => date('Y-m-d H:i:s')
                );
                
                $result = $this->Services_model->updateAttribute($attributeInfo, $attid);
                
                if($result == true)
                {
                    $this->session->set_flashdata('success', 'Attribute updated successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Attribute update failed');
                }
                
                redirect('attributesList');
            }
        }
    }

    /**
     * This function is used to delete a attribute
     */
    public function deleteAttribute($attid = NULL)
    {
        if($this->isAdmin() == FALSE)
        {
            echo json_encode(array('status' => 'access'));
        }
        else
        {
            $this->load->model('Services_model');
            
            $result = $this->Services_model->deleteAttribute($attid);
            
            log_message('info', "services-deleteAttribute: ".$attid. " deleted with result: " . json_encode($result));

            if ($result > 0) {
                echo json_encode(array('status' => TRUE));
                $this->session->set_flashdata('success', 'Attribute deleted successfully');
            } else {
                echo json_encode(array('status' => FALSE));
                $this->session->set_flashdata('error', 'Attribute deleted failed');
            }

            redirect('attributesList');
        }
    }

    /**
     * This function is used to add a radius group reply attribute via AJAX
     */
    public function addRadiusGroupAttribute()
    {
        $groupname = $this->input->post('groupname');
        $attribute = $this->input->post('attribute');
        $op = $this->input->post('op');
        $value = $this->input->post('value');

        // Check for duplicate
        $this->db->where('groupname', $groupname);
        $this->db->where('attribute', $attribute);
        $exists = $this->db->get('radgroupreply')->num_rows() > 0;

        if ($exists) {
            echo json_encode(['status' => false, 'error' => 'Duplicate attribute for this group']);
            return;
        }

        // Insert new attribute
        $data = [
            'groupname' => $groupname,
            'attribute' => $attribute,
            'op' => $op,
            'value' => $value
        ];
        $this->db->insert('radgroupreply', $data);
        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => true, 'id' => $this->db->insert_id()]);
        } else {
            echo json_encode(['status' => false, 'error' => 'Failed to add attribute']);
        }
    }

    /**
     * This function is used to delete a radius group reply attribute via AJAX
     */
    public function deleteRadiusGroupAttribute($id = NULL)
    {
        if($this->isAdmin() == FALSE)
        {
            echo json_encode(array('status' => 'access'));
        }
        else
        {
            if($id === NULL)
            {
                $id = $this->security->xss_clean($this->input->post('id'));
            }
            
            $this->load->model('Services_model');
            
            $result = $this->Services_model->deleteRadiusGroupReplyAttribute($id);
            
            if($result)
            {
                echo json_encode(array('status' => true));
            }
            else
            {
                echo json_encode(array('status' => false));
            }
        }
    }

    function resellerAssignPackages($managername = NULL) {

        if($this->perm_editmanagers == 0 && $this->perm_createservices == 0){
            $this->session->set_flashdata('error', 'You are not allowed to assign packages to resellers. Contact Admin to allow permission');
            redirect('spList');
        }

        if($this->isAdmin() == FALSE) {
            redirect('login');
        } else {
            $this->load->model('Services_model');
            
            if($managername === NULL) {
                $managername = $this->input->post('managername');
            }
            
            // Get manager info to check mastername
            $this->load->model('users_model');
            $this->load->model('Invoices_model');
            $managerInfo = $this->users_model->getManagerInfo($managername);
            
            $masterInfo = $this->Invoices_model->getMasterManager($managername);
            $mastername = $masterInfo->mastername;
            
            $data['packages'] = $this->Services_model->getResellerPackages($managername, $mastername);
            $data['managername'] = $managername;
            
            $this->global['pageTitle'] = 'Pace-Tel : Assign Packages';
            $this->loadViews("services/resellerAssignPackages", $this->global, $data, NULL);
        }

    }

    public function resellerPackageAdd()
    {
        if($this->isAdmin() == FALSE) {
            redirect('login');
        } else {
            $this->load->model('Services_model');
            $this->load->model('users_model');
            
            // Get and sanitize input data
            $srvid = $this->security->xss_clean($this->input->post('srvid'));
            $managername = $this->security->xss_clean($this->input->post('managername'));

            log_message('info', "reseller-package-add: " . $managername . " with srvid " . $srvid);
            
            // Validate input
            if(empty($srvid) || empty($managername)) {
                echo json_encode(array('status' => FALSE, 'message' => 'Required fields are missing'));
                return;
            }
            
            // Get the original service details
            $originalService = $this->Services_model->getServiceInfo($srvid);
            
            if(empty($originalService)) {
                echo json_encode(array('status' => FALSE, 'message' => 'Service not found'));
                return;
            }
            
            // Check if service already exists for this manager
            if($this->Services_model->checkBaseServiceExists($managername, $originalService->radsrvid) && 
                $this->Services_model->checkMasterServiceExists($managername, $originalService->radsrvid)) {
                echo json_encode(array('status' => FALSE, 'message' => 'This service already exists for the manager'));
                return;
            }
            
            // Prepare service info for new manager
            $serviceInfo = array(
                'srvname' => $originalService->srvname,
                'radsrvid' => $originalService->radsrvid,
                'managername' => $managername,
                'baseprice' => $originalService->baseprice,
                'costprice' => $originalService->costprice,
                'saleprice' => $originalService->saleprice
            );
            
            $servicePermission = $this->Services_model->addServiceToAllowedManagers($originalService->radsrvid, $managername);

            // Add the new service
            $result = $this->Services_model->addService($serviceInfo);
            
            if($result) {
                echo json_encode(array('status' => TRUE, 'message' => 'Package added successfully.
                    ' . ($servicePermission ? ' Service permission also added successfully' : 'Service permission already exists')));
            } else {
                echo json_encode(array('status' => FALSE, 'message' => 'Failed to add package'));
            }
        }
    }

    function servicesListingview($managerFilter = NULL)
    {

        if($this->perm_listservices == 0 && $this->session->userdata('login_type') != 'profile')
        {
            $this->session->set_flashdata('error', 'You do not have permission to access this page');
            redirect('dashboard');
        }elseif($this->perm_listservices == 1 && $this->session->userdata('login_type') == 'profile' && in_array($this->session->userdata('roleId'), array(2,3,4))){
            $this->session->set_flashdata('error', 'Profile do not have permission to access this page');
            redirect('dashboard');
        }

        $managerInfo = $this->users_model->getManagerInfo($this->session->userdata ( 'name' ));
        $managerAllServices = $managerInfo->perm_createservices;
        
        if($managerAllServices == 0 && $this->session->userdata ( 'name' ) <> 'admin' && $this->ismaster > 0){
            $this->session->set_flashdata('error', 'You are not allowed to view reseller packages. Contact Admin to allow permission');
            //redirect('serviceslist');
        }
     
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;
        $type = $this->security->xss_clean($this->input->post('searchText1'));

        if(empty($managerFilter)){
            if(empty($type)) 
            { $defaultFilter = $this->Services_model->getDefaultManagerFilter($this->session->userdata ( 'name' ));
                if(!empty($defaultFilter))
                    { $type = $defaultFilter->managername; }
            }
        }else{
            $type = $managerFilter;
        }

        $this->load->library('pagination');
        
        $count = $this->Services_model->servicesCount($searchText, $type, $managerAllServices);

        $returns = $this->paginationCompress ( "serviceListing/", $count, 20 );

        //print_r($returns);
        //exit;

        $data['serviceListing'] = $this->Services_model->servicesListing($searchText, $type, $returns["page"], $returns["segment"], $managerAllServices);
        $data['type'] = $type;
        $data['managerList'] = $this->users_model->getManagersList();
        $data['managerFilter'] = $managerFilter;
        $this->global['pageTitle'] = 'Pace-Tel : Services';
        
        $this->loadViews("serviceslistview", $this->global, $data, NULL);

    }

    /**
     * Show all sub-reseller services for master reseller or admin
     */
    public function subResellerServicesView()
    {
        $username = $this->session->userdata('name');
        if ($username == 'admin' || $this->ismaster > 0) {
            $mastername = ($username == 'admin') ? null : $username;
            $data['subResellerServices'] = $this->Services_model->getSubResellerServices($mastername);
            $this->global['pageTitle'] = 'Sub-Reseller Services';
            $this->loadViews('subreseller_services_view', $this->global, $data, NULL);
        } else {
            $this->session->set_flashdata('error', 'You are not a master reseller or admin.');
            redirect('dashboard');
        }
    }

    /**
     * AJAX: Get sub-reseller services for a given master and srvid
     */
    public function getSubResellerServicesAjax()
    {
        $mastername = $this->input->get('managername');
        $srvid = $this->input->get('srvid');

        $data = $this->Services_model->getSubResellerServices($mastername, $srvid);
        echo json_encode($data);
    }

    /**
     * AJAX: Update sub-reseller service prices (base, cost, sale) by srvid
     */
    public function updateSubResellerPrices()
    {
        $srvid = $this->input->post('srvid');
        $baseprice = $this->input->post('baseprice');
        $costprice = $this->input->post('costprice');
        $saleprice = $this->input->post('saleprice');
        if (!$srvid) {
            echo json_encode(['success' => false, 'error' => 'Missing srvid']);
            return;
        }
        $this->db->where('srvid', $srvid);
        $result = $this->db->update('tbl_services', [
            'baseprice' => $baseprice,
            'costprice' => $costprice,
            'saleprice' => $saleprice
        ]);
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Update failed']);
        }
    }

    /**
     * Return unique attribute names from radgroupreply for use in attribute select dropdown
     */
    public function getUniqueRadgroupreplyAttributes()
    {
        $this->db->distinct();
        $this->db->select('attribute');
        $this->db->from('radgroupreply');
        $query = $this->db->get();
        $attributes = [];
        foreach ($query->result() as $row) {
            $attributes[] = $row->attribute;
        }
        echo json_encode($attributes);
    }

    public function updateRadiusGroupAttribute()
    {
        $id = $this->input->post('id');
        $groupname = $this->input->post('groupname');
        $attribute = $this->input->post('attribute');
        $op = $this->input->post('op');
        $value = $this->input->post('value');

        // Check for duplicate (excluding this id)
        $this->db->where('groupname', $groupname);
        $this->db->where('attribute', $attribute);
        $this->db->where('id !=', $id);
        $exists = $this->db->get('radgroupreply')->num_rows() > 0;

        if ($exists) {
            echo json_encode(['status' => false, 'error' => 'Duplicate attribute for this group']);
            return;
        }

        $this->db->where('id', $id);
        $this->db->update('radgroupreply', [
            'attribute' => $attribute,
            'op' => $op,
            'value' => $value
        ]);
        if ($this->db->affected_rows() > 0) {
            echo json_encode(['status' => true]);
        } else {
            echo json_encode(['status' => false, 'error' => 'Failed to update attribute']);
        }
    }

    /**
     * Delete Service Profile - AJAX endpoint
     * Validates that service is not assigned to any users before deletion
     * Performs cascading deletes from related tables
     */
    public function spDeleteProfile()
    {
        // Check if user has permission to delete services
        if($this->perm_deleteservices == 0){
            $response = array(
                'status' => false,
                'message' => 'You are not allowed to delete services. Contact Admin to allow permission'
            );
            echo json_encode($response);
            return;
        }

        // Get the service ID from POST request
        $srvid = $this->input->post('srvid');
        
        if(empty($srvid)) {
            $response = array(
                'status' => false,
                'message' => 'Service ID is required'
            );
            echo json_encode($response);
            return;
        }

        try {
            // Step 1: Check if service exists in rm_services
            $serviceExists = $this->Services_model->checkServiceProfileExists($srvid);
            if(!$serviceExists) {
                $response = array(
                    'status' => false,
                    'message' => 'Service profile not found'
                );
                echo json_encode($response);
                return;
            }

            // Step 2: Check if service is assigned to any users in rm_users
            $usersWithService = $this->Services_model->getUsersWithService($srvid);
            if(!empty($usersWithService)) {
                $userCount = count($usersWithService);
                $response = array(
                    'status' => false,
                    'message' => "Cannot delete service profile. It is currently assigned to {$userCount} user(s). Please reassign or remove these users first."
                );
                echo json_encode($response);
                return;
            }

            // Step 3: Get the radsrvid from rm_services for cascading delete
            $serviceInfo = $this->Services_model->getServiceProfileInfo($srvid);
            $radsrvid = $serviceInfo->srvid;

            // Step 4: Begin transaction for cascading deletes
            $this->db->trans_start();

            // Delete from rm_allowedmanagers
            $this->Services_model->deleteServiceFromAllowedManagers($srvid);
            log_message('info', "Deleted service ID {$srvid} from rm_allowedmanagers");

            // Delete from rm_allowednases  
            $this->Services_model->deleteServiceFromAllowedNases($srvid);
            log_message('info', "Deleted service ID {$srvid} from rm_allowednases");

            // Delete from rm_services (main service profile)
            $this->Services_model->deleteServiceProfile($srvid);
            log_message('info', "Deleted service profile ID {$srvid} from rm_services");

            // Delete from tbl_services using radsrvid
            if(!empty($radsrvid)) {
                $this->Services_model->deleteServiceFromTblServices($radsrvid);
                log_message('info', "Deleted service with radsrvid {$radsrvid} from tbl_services");
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                // Transaction failed
                $this->db->trans_rollback();
                $response = array(
                    'status' => false,
                    'message' => 'Failed to delete service profile. Database transaction failed.'
                );
                log_message('error', "Failed to delete service profile ID {$srvid}. Transaction rolled back.");
            } else {
                // Transaction successful
                $response = array(
                    'status' => true,
                    'message' => 'Service profile deleted successfully'
                );
                log_message('info', "Successfully deleted service profile ID {$srvid} and all related records");
            }

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $response = array(
                'status' => false,
                'message' => 'An error occurred while deleting the service profile: ' . $e->getMessage()
            );
            log_message('error', "Exception while deleting service profile ID {$srvid}: " . $e->getMessage());
        }

        echo json_encode($response);
    }

    /**
     * Create or update a record in radusergroup for a user
     * @param array $params ['srvname' => ..., 'username' => ..., 'priority' => ...]
     * Usage: $this->manageUserRadusergroup(['srvname' => ..., 'username' => ..., 'priority' => ...]);
     * Returns JSON: {status: true/false, action: 'created'/'updated', id: ...}
     */
    public function manageUserRadusergroup($params = [])
    {
        $srvname = isset($params['srvname']) ? $params['srvname'] : null;
        $username = isset($params['username']) ? $params['username'] : null;
        $priority = isset($params['priority']) ? $params['priority'] : 0;
        $this->load->model('Services_model');
        $result = $this->Services_model->manageUserRadusergroup($srvname, $username, $priority);
        echo json_encode($result);
    }

    /**
     * Delete Service - AJAX endpoint
     * Validates that service is not assigned to any users before deletion
     */
    public function deleteService()
    {
        // Check if user has permission to delete services
        if($this->perm_deleteservices == 0){
            $response = array(
                'status' => false,
                'message' => 'You are not allowed to delete services. Contact Admin to allow permission'
            );
            echo json_encode($response);
            return;
        }

        // Get the service ID from POST request
        $srvid = $this->input->post('srvid');
        
        if(empty($srvid)) {
            $response = array(
                'status' => false,
                'message' => 'Service ID is required'
            );
            echo json_encode($response);
            return;
        }

        try {
            // Step 1: Check if service exists in tbl_services
            $serviceExists = $this->Services_model->checkServiceExistsById($srvid);
            if(!$serviceExists) {
                $response = array(
                    'status' => false,
                    'message' => 'Service not found'
                );
                echo json_encode($response);
                return;
            }

            // Step 2: Check if service is assigned to any users in rm_users
            $usersWithService = $this->Services_model->getUsersWithTblService($srvid);
            if(!empty($usersWithService)) {
                $userCount = count($usersWithService);
                $response = array(
                    'status' => false,
                    'message' => "Cannot delete service. It is currently assigned to {$userCount} user(s). Please reassign or remove these users first."
                );
                echo json_encode($response);
                return;
            }

            // Step 3: Delete the service from tbl_services
            $result = $this->Services_model->deleteTblService($srvid);
            
            if($result) {
                $response = array(
                    'status' => true,
                    'message' => 'Service deleted successfully'
                );
                log_message('info', "Successfully deleted service ID {$srvid} from tbl_services");
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Failed to delete service'
                );
                log_message('error', "Failed to delete service ID {$srvid} from tbl_services");
            }

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => 'An error occurred while deleting the service: ' . $e->getMessage()
            );
            log_message('error', "Exception while deleting service ID {$srvid}: " . $e->getMessage());
        }

        echo json_encode($response);
    }

}

?>
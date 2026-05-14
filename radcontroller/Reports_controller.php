<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class Reports_controller extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Reports_model');
        $this->load->model('users_model');
        $this->isLoggedIn();   
    }
    
    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->global['pageTitle'] = 'PaceTel : Dashboard';
        $data = array();
        $data['topProducts'] = $this->Reports_model->getTop5ServicePlansLastMonth();
        $this->loadViews("dashboard", $this->global, $data , NULL);
    }

    function onlineCallsListing()
    {

        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;
        
        $this->load->library('pagination');
        
        $count = $this->Reports_model->onlineCallsCount($searchText);

        $returns = $this->paginationCompress ( "onlineusers/", $count, 20 );
        
        $data['onlineCallsListing'] = $this->Reports_model->onlineCallsListing($searchText, $returns["page"], $returns["segment"]);
        
        $this->global['pageTitle'] = 'Pace-Tel : Online Calls';
        
        $this->loadViews("onlinecalls", $this->global, $data, NULL);

    }

    function DisconnectUser($user)
    {

        log_message('debug', "Job Started: DisconnectUser: ");
        $this->load->model('Reports_model');
        $connectedUser = $this->Reports_model->disconnectUser($user);
        
        $command = "echo User-Name=".$connectedUser->username.",Framed-IP-Address=".$connectedUser->framedipaddress.
                    " | radclient -r 1 ".$connectedUser->nasipaddress.":3799 disconnect ".$connectedUser->secret;

        
        //echo $command;
        exec($command);

        $result = 1;
        if ($result > 0) { 
            echo(json_encode(array('status'=>TRUE)));
            $this->Reports_model->disconnectUserAccountUpdate($user);
        }
        else 
        {
            echo(json_encode(array('status'=>FALSE))); 
        }

    }

    function getDdnsIP(){

        echo gethostbyname('ec190f326e17.sn.mynetname.net');

    }

    function RestartSession()
    {

        $this->load->model('Reports_model');
        //$connectedUser = $this->Reports_model->RestartSession();

        //$this->session->set_flashdata('Session Restart', 'All users will be disconnected from system please wait...');


        //foreach($connectedUser as $record){
        //        $command = "echo User-Name=".$record->username.",Framed-IP-Address=".$record->framedipaddress.
        //                    " | radclient -r 1 ".$record->nasipaddress.":3799 disconnect ".$record->secret;
                
                //echo $command;

                //exec($command);
        //}
       
        //$this->session->set_flashdata('Session Restart', 'Session restart completed....');
        redirect('onlineusers');

        //$result = 1;
        //if ($result > 0) { echo(json_encode(array('status'=>TRUE))); }
        //else { echo(json_encode(array('status'=>FALSE))); }

    }

    function chartTest()
    {
        //$count = $this->Reports_model->onlineCallsCount($searchText);
        $this->loadViews("chart2", $this->global, NULL, NULL);

    }

    function salesReport_load(){

            $this->load->library('pagination');

            $this->load->model('users_model');

            if($this->session->userdata ( 'name' ) <> 'admin' && $this->ismaster == 0){
                $searchText = $this->session->userdata ( 'name' ); 
            }else{
                $searchText = $this->security->xss_clean($this->input->post('searchText')); 
            }

            $data['searchText'] = $searchText;
            $data['searchText1'] = "";
            $data['searchText2'] = "";
            $data['searchText3'] = "";
            $data['searchText4'] = "";
            
            $count = 0;

			$returns = $this->paginationCompress ( "salesReports/", $count, 500 );

            $data['managerList'] = $this->users_model->getManagersList();
            $data['salesReport'] = NULL;
            $data['salesSummery'] = NULL;

            $this->global['pageTitle'] = 'Pace-Tel : Sales Report';
            
            $this->loadViews("salesReport", $this->global, $data, NULL);

    }

    function salesReport()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('users_model');
            if($this->session->userdata ( 'name' ) <> 'admin' && $this->ismaster == 0){
                $searchText = $this->session->userdata ( 'name' ); 
            }else{
                $searchText = $this->security->xss_clean($this->input->post('searchText')); 
            }

            $searchText1 = $this->security->xss_clean($this->input->post('searchText1'));
            $searchText2 = $this->security->xss_clean($this->input->post('searchText2'));
            $searchText3 = $this->security->xss_clean($this->input->post('searchText3'));
            $searchText4 = $this->security->xss_clean($this->input->post('searchText4'));

            $data['searchText'] = $searchText;
            $data['searchText1'] = $searchText1;
            $data['searchText2'] = $searchText2;
            $data['searchText3'] = $searchText3;
            $data['searchText4'] = $searchText4;
            //$data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            
            $count = $this->Reports_model->salesReportCount($searchText, $searchText1, $searchText2, $searchText3);

			$returns = $this->paginationCompress ( "salesReports/", $count, 500 );

            $data['managerList'] = $this->users_model->getManagersList();
            $data['salesReport'] = $this->Reports_model->salesReport($searchText, $searchText1, $searchText2, $searchText3, $returns["page"], $returns["segment"]);
            $data['salesSummery'] = $this->Reports_model->salesSummery($searchText, $searchText1, $searchText2, $searchText3, $returns["page"], $returns["segment"]);

            $this->global['pageTitle'] = 'Pace-Tel : Sales Report';
            
            $this->loadViews("salesReport", $this->global, $data, NULL);
        }
    }

    function packageSalesReport()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('users_model');
            if($this->session->userdata ( 'name' ) <> 'admin' && $this->ismaster == 0){
                $searchText = $this->session->userdata ( 'name' ); 
            }else{
                $searchText = $this->security->xss_clean($this->input->post('searchText')); 
            }
            $searchText1 = $this->security->xss_clean($this->input->post('searchText1'));
            $searchText2 = $this->security->xss_clean($this->input->post('searchText2'));
            $searchText3 = $this->security->xss_clean($this->input->post('searchText3'));
            $searchText4 = $this->security->xss_clean($this->input->post('searchText4'));

            $data['searchText'] = $searchText;
            $data['searchText1'] = $searchText1;
            $data['searchText2'] = $searchText2;
            $data['searchText3'] = $searchText3;
            $data['searchText4'] = $searchText4;
            //$data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            
            $count = $this->Reports_model->salesReportCount($searchText, $searchText1, $searchText2, $searchText3);

			$returns = $this->paginationCompress ( "salesReports/", $count, 500 );

            $data['managerList'] = $this->users_model->getManagersList();
            $data['salesReport'] = $this->Reports_model->salesReport($searchText, $searchText1, $searchText2, $searchText3, $returns["page"], $returns["segment"]);
            $data['salesSummery'] = $this->Reports_model->salesSummery($searchText, $searchText1, $searchText2, $searchText3, $returns["page"], $returns["segment"]);

            $this->global['pageTitle'] = 'Pace-Tel : Sales Report';
            
            $this->loadViews("salesReport", $this->global, $data, NULL);
        }
    }

    function managerSalesReport()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('users_model');
            if($this->session->userdata ( 'name' ) <> 'admin' && $this->ismaster == 0){
                $searchText = $this->session->userdata ( 'name' ); 
            }else{
                $searchText = $this->security->xss_clean($this->input->post('searchText')); 
            }
            $searchText1 = $this->security->xss_clean($this->input->post('searchText1'));
            $searchText2 = $this->security->xss_clean($this->input->post('searchText2'));
            $searchText3 = $this->security->xss_clean($this->input->post('searchText3'));
            $searchText4 = $this->security->xss_clean($this->input->post('searchText4'));

            $data['searchText'] = $searchText;
            $data['searchText1'] = $searchText1;
            $data['searchText2'] = $searchText2;
            $data['searchText3'] = $searchText3;
            $data['searchText4'] = $searchText4;
            //$data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            
            $count = $this->Reports_model->salesReportCount($searchText, $searchText1, $searchText2, $searchText3);

			$returns = $this->paginationCompress ( "salesReports/", $count, 500 );

            $data['managerList'] = $this->users_model->getManagersList();
            $data['salesReport'] = $this->Reports_model->salesReportManager($searchText, $searchText1, $searchText2, $searchText3, $returns["page"], $returns["segment"]);
            $data['salesSummery'] = $this->Reports_model->salesSummeryManager($searchText, $searchText1, $searchText2, $searchText3, $returns["page"], $returns["segment"]);

            $this->global['pageTitle'] = 'Pace-Tel : Sales Report';
            
            $this->loadViews("salesReportManager", $this->global, $data, NULL);
        }
    }

    function costOfSalesReport()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('users_model');
            if($this->session->userdata ( 'name' ) <> 'admin' && $this->ismaster == 0){
                $searchText = $this->session->userdata ( 'name' ); 
            }else{
                $searchText = $this->security->xss_clean($this->input->post('searchText')); 
            }
            $searchText1 = $this->security->xss_clean($this->input->post('searchText1'));
            $searchText2 = $this->security->xss_clean($this->input->post('searchText2'));
            $searchText3 = $this->security->xss_clean($this->input->post('searchText3'));
            $searchText4 = $this->security->xss_clean($this->input->post('searchText4'));

            $data['searchText'] = $searchText;
            $data['searchText1'] = $searchText1;
            $data['searchText2'] = $searchText2;
            $data['searchText3'] = $searchText3;
            $data['searchText4'] = $searchText4;
            //$data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            
            $count = $this->Reports_model->costOfSalesReportCount($searchText, $searchText1, $searchText2, $searchText3);

			$returns = $this->paginationCompress ( "salesReports/", $count, 500 );

            $data['managerList'] = $this->users_model->getManagersList();
            $data['salesReport'] = $this->Reports_model->costOfSalesReport($searchText, $searchText1, $searchText2, $searchText3, $returns["page"], $returns["segment"]);
            $data['salesSummery'] = $this->Reports_model->costOfSalesSummery($searchText, $searchText1, $searchText2, $searchText3, $returns["page"], $returns["segment"]);

            $this->global['pageTitle'] = 'Pace-Tel : Sales Report';
            
            $this->loadViews("salesReportCost", $this->global, $data, NULL);
        }
    }

    function testsms(){
        $this->sendsmsgateway("923349900957", "Test Message", $pointerError);        
    }


    // SMS Logs Screen
    function smsLogsInfo()
    {
     
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $managername = $this->session->userdata ( 'name' );
            if($this->bulksmsmanager == 1 || $managername == 'admin'){
                //$searchText = $this->session->userdata ( 'name' );     
                
                $searchText = $this->security->xss_clean($this->input->post('searchText'));
                $searchText1 = $this->security->xss_clean($this->input->post('searchText1'));
                $searchText2 = $this->security->xss_clean($this->input->post('searchText2'));
                $searchText3 = $this->security->xss_clean($this->input->post('searchText3'));
                $searchText4 = $this->security->xss_clean($this->input->post('searchText4'));

                $data['searchText'] = $searchText;
                $data['searchText1'] = $searchText1;
                $data['searchText2'] = $searchText2;
                $data['searchText3'] = $searchText3;
                $data['searchText4'] = $searchText4;
                //$data['searchText'] = $searchText;
                
                $this->load->library('pagination');
                
                $count = $this->Reports_model->smsReportCount($searchText, $searchText1, $searchText2);

                $returns = $this->paginationCompress ( "smsReports/", $count, 10 );
                
                $data['smsReport'] = $this->Reports_model->smsReport($searchText, $searchText1, $searchText2, $returns["page"], $returns["segment"]);

                $this->global['pageTitle'] = 'Pace-Tel : SMS Report';
                
                $this->loadViews("smsReport", $this->global, $data, NULL);
            }else{

                $this->session->set_flashdata('error', 'Not allowed to send group messages ');
                redirect('usersListing');

            }
        }
    }

    function sendGroupSms()
    {
     
        $this->load->model('users_model');
        $this->load->model('login_model');
        //$data['packages'] = $this->users_model->getPackages();

        $managername = $this->session->userdata ( 'name' );
        $managerInfo = $this->users_model->getManagerInfo($managername);

        $data['managerInfo'] = $this->users_model->getManagerInfo($managername);

        if($managerInfo->perm_addcredits == 1 || $this->bulksmsmanager == 1)
        {

            $data['managerList'] = $this->users_model->getManagersList();
            $this->global['pageTitle'] = 'Group SMS | Pace Tel';

            $data['getManagersList'] = $this->users_model->getManagersList();
            

            $this->loadViews("sendgroupsms", $this->global, $data, NULL);

        }else{

            $this->session->set_flashdata('error', 'Not allowed to send group messages ');
            redirect('usersListing');
            
        }
    }

    function sendGroupMsgNow(){

        $this->load->library('form_validation');

        $this->form_validation->set_rules('title','Title','trim|required|max_length[30]');
        $this->form_validation->set_rules('subject','Subject','trim|required|max_length[30]');
        $this->form_validation->set_rules('txtmsg','Text Message','trim|required|max_length[250]');

        if($this->form_validation->run() == FALSE)
        {
            $this->sendGroupSms();
        }
        else
        {

            $managername = $this->security->xss_clean($this->input->post('managername'));
            $typeofsms = $this->input->post('typeofsms');
            $title = ucwords(strtolower($this->security->xss_clean($this->input->post('title'))));
            $subject = ucwords(strtolower($this->security->xss_clean($this->input->post('subject'))));
            $txtmsg = $this->input->post('txtmsg');

            $status = 0;

            $type = $this->input->post('type');

            if($type==1){

                try
                {

                    $result = $this->sendsmsgateway($subject, $txtmsg, $errorCode);
                    $statusType = simplexml_load_string($result);
                    $status = $statusType->type;

                    $msgInfo = array('title'=>$title, 'subject'=> $subject, 'txtmsg'=>$txtmsg, 'status'=>$status);
                    $this->Reports_model->insertSmsLogs($msgInfo);
                    $status = "Failed"; // Set Variable to Default State

                }
                catch (\Exception $e)
                {
                    //die($e->getMessage());
                    $this->session->set_flashdata('error', $e->getMessage());
                }

            }
            else
            {
                
                $this->db->select("A.username, A.firstname, A.lastname, if(left(trim(A.mobile),2)=92, A.mobile , if(left(trim(A.mobile),1)=0, (if(left(trim(A.mobile),2)=3,Concat('92',substring(trim(A.mobile),2)),substring(trim(A.mobile),2))), REPLACE(A.mobile,' ',''))) as mobile,
                                    B.payid, B.payname, (C.saleprice-B.discount) as amount");
                $this->db->from('rm_users as A');
                $this->db->join('tbl_userdocs as B', 'A.username = B.username','left');
                $this->db->join('tbl_services as C', 'A.srvid = C.radsrvid','left');
                $this->db->where('A.owner', $managername);
                //$this->db->where('A.enableuser', 1);
                $this->db->where('C.managername', $managername);
                $this->db->where('A.mobile<>"" and isnull(A.mobile)=False');
                //$this->db->where('left(A.mobile,1)=0');
                //$this->db->where('A.mobile','923349900957');
                //echo $this->db->count_all_results();
                $curDate = date("y-m-d");
                if($typeofsms == 0){ $this->db->where('A.enableuser', 1); }
                if($typeofsms == 1){ $this->db->where('A.expiration <= "'.$curDate.'"'); }
                if($typeofsms == 2){ $this->db->where('A.enableuser', 0); }

                $query = $this->db->get();
                $userInfo = $query->result();
                $status = "Failed";

                foreach($userInfo as $record)
                {

                    $txtmsg = $this->input->post('txtmsg');

                    if(strpos($txtmsg, "#username") == true){
                        $txtmsg = str_replace('#username', $record->username, $txtmsg);
                    }
                    
                    if(strpos($txtmsg, "#name") == true){
                        $txtmsg = str_replace('#name', $record->firstname.' '.$record->lastname, $txtmsg);
                    }

                    if(strpos($txtmsg, "#payinfo") == true){
                        $txtmsg = str_replace('#payinfo', 'ID:'.$record->payid.' Name:'.$record->payname.' Amount:'.$record->amount, $txtmsg);
                    }

                    //echo nl2br($txtmsg);
                    //echo "<br>";

                    try
                    {

                        $result = $this->sendsmsgateway($record->mobile, $txtmsg, $errorCode);
                        $statusType = simplexml_load_string($result);
                        $status = $statusType->type;
                        $mobile = $record->mobile;

                        $msgInfo = array('title'=>$title, 'subject'=> $mobile, 'txtmsg'=>$txtmsg, 'status'=>$status);
                        $this->Reports_model->insertSmsLogs($msgInfo);
                        $status = "Failed"; // Set Variable to Default State

                    }
                    catch (\Exception $e)
                    {
                        //die($e->getMessage());
                        $this->session->set_flashdata('error', $e->getMessage());
                    }

                }
            }

            redirect('smsReport');

        }
    }


    // Resellers Listing
    function resellerListReport()
    {
        
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            //$searchText = $this->session->userdata ( 'name' );        
            $searchText = $this->security->xss_clean($this->input->post('searchText'));

            $data['searchText'] = $searchText;

            //$data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            
            $count = $this->Reports_model->resellerListingCount($searchText);

            $returns = $this->paginationCompress ( "resellerListReport/", $count, 10 );
            
            $data['resellerListReport'] = $this->Reports_model->resellerListing($searchText, $returns["page"], $returns["segment"]);

            $this->global['pageTitle'] = 'Pace-Tel : SMS Report';
            
            $this->loadViews("resellerListReport", $this->global, $data, NULL);
        }
    }

    // Generate Easypaisa Report

    function easypaisaListing()
    {

        $this->load->model('users_model');
        $managerInfo = $this->users_model->getManagerInfo($this->session->userdata ( 'name' ));
        $managerAllServices = $managerInfo->perm_createservices;
     
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;
        
        $this->load->library('pagination');
        
        $count = $this->Reports_model->easypaisaCount($searchText, $managerAllServices);

        $returns = $this->paginationCompress ( "serviceListing/", $count, 10 );

        $data['easypaisaListing'] = $this->Reports_model->easypaisaListing($searchText, $returns["page"], $returns["segment"], $managerAllServices);
        
        $this->global['pageTitle'] = 'Pace-Tel : Services';
        
        $this->loadViews("easypaisalist", $this->global, $data, NULL);

    }

    function easypaisaAddNew()
    {
        $this->load->model('users_model');
        $managername = $this->session->userdata ( 'name' );
        $managerInfo = $this->users_model->getManagerInfo($managername);
        if($managerInfo->perm_createservices == 1)
        {
            $this->load->model('users_model');
            $data['packages'] = $this->users_model->getPackages();
            $data['managername'] = $this->users_model->getManagersList();
            
            $this->global['pageTitle'] = 'Easypaisa : Add New List';

            $this->loadViews("easypaisaAddNew", $this->global, $data, NULL);
        }else{
            //echo "Not Allowed..........";
            $this->session->set_flashdata('error', 'Operation not allowed........');
            redirect('easypaisalist');
        }
    }

    function easypaisaSave()
    {

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('filename','File Name','trim|required|max_length[150]');
            $this->form_validation->set_rules('billingmonth','Billing Month','required');
            $this->form_validation->set_rules('duedate','Billing Month','required');
            $this->form_validation->set_rules('filepath','File Path','required');

            if($this->form_validation->run() == FALSE)
            {
                $this->easypaisaAddNew($this->input->post('filename'));
            }
            else
            {
                $filename = $this->security->xss_clean($this->input->post('filename'));
                $billingmonth = $this->security->xss_clean($this->input->post('billingmonth'));
                $duedate = $this->security->xss_clean($this->input->post('duedate'));
                $filepath = $this->security->xss_clean($this->input->post('filepath'));
              
                $easypaisaFileInfo = array('filename'=>$filename,
                                    'billingmonth'=>$billingmonth,
                                    'duedate'=>$duedate,
                                    'filepath'=>$filepath);
                
                $this->load->model('users_model');
                $this->load->model('Reports_model');

                if(($this->Reports_model->checkEasypaisaFileExists($filename)) == false)
                {
                    $result = $this->Reports_model->addEasypaisaFile($easypaisaFileInfo);

                    if($result == True)
                    {
                        // Fetch report data from model
                        $users_data = $this->Reports_model->easypaisaReport($billingmonth, $duedate);

                        // Set headers for CSV download
                        header('Content-Type: text/csv');
                        header('Content-Disposition: attachment; filename="' . $filepath . '"');
                        header('Pragma: no-cache');
                        header('Expires: 0');

                        // Output CSV to browser
                        $output = fopen('php://output', 'w');
                        $header = array("SNo","Groupid", "GroupName", "BillingMonth", "DueDateAmount", "DueDate", "AmountAfterDueDate");
                        fputcsv($output, $header);
                        foreach ($users_data->result_array() as $key => $value)
                        { 
                            fputcsv($output, $value);
                        }
                        fclose($output);
                        exit; // Stop further output
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'File creation failed');
                        redirect('easypaisalist');
                    }
                }else{
                    $this->session->set_flashdata('error', 'File Name already exists....');
                    redirect('easypaisalist');
                }

                redirect('easypaisalist');

                
            }
        }
    }

    function fileNameExists($filename)
    {

        if(!empty($srvname)){

            $managername = $this->session->userdata ( 'name' );
            $result = $this->Reports_model->checkEasypaisaFileExists($filename);

            if(!empty($result))
                $this->form_validation->set_message('filenameExists', 'The {field} already taken');
                return true;
            }
            else{
                return false;

            }
    }

    function generateEasyPaisaReport($folder_path, $file_name, $billingmonth, $duedate){

        //header("Content-Description: File Transfer"); 
        //header("Content-Disposition: attachment; filename=$file_name"); 
        //header("Content-Type: application/csv;");
        
        $users_data = $this->Reports_model->easypaisaReport($billingmonth, $duedate);
        //$file = fopen('php://output', 'w');
        $file = fopen($folder_path.$file_name, 'w+');
        $header = array("SNo","Groupid", "GroupName", "BillingMonth", "DueDateAmount", "DueDate", "AmountAfterDueDate");
        fputcsv($file, $header);
        foreach ($users_data->result_array() as $key => $value)
        { 
            fputcsv($file, $value);
        }
        
        fclose($file); 
        //exit; 

    }


    // Indicators Report
    function userIndictors()
    {
        
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $managername = $this->session->userdata ( 'name' );
            if($managername == 'admin')
            {
                //$searchText = $this->session->userdata ( 'name' );        
                $searchText = $this->security->xss_clean($this->input->post('searchText'));
                $searchText1 = $this->security->xss_clean($this->input->post('searchText1'));
                $searchText2 = $this->security->xss_clean($this->input->post('searchText2'));
                $searchText3 = $this->security->xss_clean($this->input->post('searchText3'));
                $searchText4 = $this->security->xss_clean($this->input->post('searchText4'));

                $data['searchText'] = $searchText;
                $data['searchText1'] = $searchText1;
                $data['searchText2'] = $searchText2;
                $data['searchText3'] = $searchText3;
                $data['searchText4'] = $searchText4;
                //$data['searchText'] = $searchText;
                
                $this->load->library('pagination');
                
                //$count = $this->Reports_model->indicatorReport($searchText, $searchText1, $searchText2);

                //$returns = $this->paginationCompress ( "userIndictors/", $count, 10 );
                
                $data['userIndictor'] = $this->Reports_model->indicatorReport($searchText, $searchText1, $searchText2);

                $this->global['pageTitle'] = 'Pace-Tel : Indicators Report';
                
                $this->loadViews("indicator", $this->global, $data, NULL);
            }else{
                $this->session->set_flashdata('error', 'Operation not allowed........');
                redirect('usersListing');
            }
        }
    }

    function easypaisaCollection()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('users_model');
            if($this->session->userdata ( 'name' ) <> 'admin'){
                $searchText = $this->session->userdata ( 'name' ); 
            }else{
                $searchText = $this->security->xss_clean($this->input->post('searchText')); 
            }
            $searchText1 = $this->security->xss_clean($this->input->post('searchText1'));
            $searchText2 = $this->security->xss_clean($this->input->post('searchText2'));
            $searchText3 = $this->security->xss_clean($this->input->post('searchText3'));
            $searchText4 = $this->security->xss_clean($this->input->post('searchText4'));

            $data['searchText'] = $searchText;
            $data['searchText1'] = $searchText1;
            $data['searchText2'] = $searchText2;
            $data['searchText3'] = $searchText3;
            $data['searchText4'] = $searchText4;
            //$data['searchText'] = $searchText;
            
            $this->load->library('pagination');

            $data['managerList'] = $this->users_model->getManagersList();

            $count = $this->Reports_model->easypaisaCollectionCount($searchText, $searchText1, $searchText2, $searchText3);
            $returns = $this->paginationCompress ( "salesReports/", $count, 500 );
            $data['collectionReport'] = $this->Reports_model->easypaisaCollection($searchText, $searchText1, $searchText2, $searchText3, $searchText4, $returns["page"], $returns["segment"]);
            $data['easypaisaCollectionSummery'] = $this->Reports_model->easypaisaCollectionSummery($searchText, $searchText1, $searchText2, $searchText3, $searchText4);

            $this->global['pageTitle'] = 'Pace-Tel : Easy Paisa Report';
            
            $this->loadViews("easypaisacollection", $this->global, $data, NULL);
        }
    }

    function userFairUseReport()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $data['managerList'] = $this->users_model->getManagersList();
            //$searchText = $this->security->xss_clean($this->input->post('searchText'));

            if($this->session->userdata ( 'name' ) <> 'admin' && $this->ismaster == 0){
                $searchText = $this->session->userdata ( 'name' ); 
            }else{
                $searchText = $this->input->post('searchText');
            }

            $searchText1 = $this->security->xss_clean($this->input->post('searchText1'));
            $searchText2 = $this->security->xss_clean($this->input->post('searchText2'));
            $searchText3 = $this->security->xss_clean($this->input->post('searchText3'));
            $searchText4 = $this->security->xss_clean($this->input->post('searchText4'));

            //echo $searchText." - ".$searchText1."-".$searchText2."-".$searchText3."-".$searchText4;

            $data['searchText'] = $searchText;
            $data['searchText1'] = $searchText1;
            $data['searchText2'] = $searchText2;
            $data['searchText3'] = $searchText3;
            $data['searchText4'] = $searchText4;
            //$data['searchText'] = $searchText;
            
            $this->load->library('pagination');
            
            $count = $this->Reports_model->usersFairUsePolicyCount($searchText, $searchText1, $searchText2, 
                                                                    $searchText3 , $searchText4);

			$returns = $this->paginationCompress ( "userListing/", $count, 100 );
            
            $data['userRecords'] = $this->Reports_model->usersFairUsePolicyListing($searchText, $searchText1, $searchText2, 
                                                            $searchText3, $searchText4, $returns["page"], $returns["segment"]);
            $data['userSummery'] = $this->Reports_model->usersFairUsePolicySummery($searchText, $searchText1, $searchText2, 
                                                                                    $searchText3, $searchText4);
            
            $this->global['pageTitle'] = 'PaceTel : User Listing';
            $this->loadViews("usersFairUseReport", $this->global, $data, NULL);

        }
    }


    // ******* Radius Report ****** //
    
    function radiusReport()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->model('users_model');
            if($this->session->userdata ( 'name' ) <> 'admin' && $this->ismaster == 0){
                $searchText = $this->session->userdata ( 'name' ); 
            }else{
                $searchText = $this->security->xss_clean($this->input->post('searchText')); 
            }
            $searchText1 = $this->security->xss_clean($this->input->post('searchText1'));
            $searchText2 = $this->security->xss_clean($this->input->post('searchText2'));
            $searchText3 = $this->security->xss_clean($this->input->post('searchText3'));

            echo $searchText;
            echo $searchText1;
            echo $searchText2;
            //exit;

            $data['searchText'] = $searchText;
            $data['searchText1'] = $searchText1;
            $data['searchText2'] = $searchText2;
            $data['searchText3'] = $searchText3;
            
            $this->load->library('pagination');
            
            $count = $this->Reports_model->salesReportCount($searchText, $searchText1, $searchText2, $searchText3);

			$returns = $this->paginationCompress ( "salesReports/", $count, 500 );

            $data['managerList'] = $this->users_model->getManagersList();
            $data['radiusReport'] = $this->Reports_model->radiusReport($searchText, $searchText1, $searchText2, $searchText3, $returns["page"], $returns["segment"]);
            $data['radiusSummery'] = null;
            //$this->Reports_model->salesSummeryManager($searchText, $searchText1, $searchText2, $searchText3, $returns["page"], $returns["segment"]);

            $this->global['pageTitle'] = 'Pace-Tel : Sales Report';
            
            $this->loadViews("Prepaid/radiusReport", $this->global, $data, NULL);
        }
    }

    /**
     * NOT IN USE, WILL BE DELETED SAFELY
     * AJAX: Get sales and paid by month for last 12 months
     */
    public function ajaxSalesAndPaidByMonth()
    {
        $managername = $this->session->userdata('name');
        $cache_key = 'dashboardgraph_salesbymonth_' . $managername;
        $this->load->driver('cache', array('adapter' => 'file'));
        $force_refresh = $this->input->get('refresh');
        if ($force_refresh) {
            $data = $this->Reports_model->getSalesByMonthLastYear();
            $this->cache->save($cache_key, $data, 900);
        } else {
            $data = $this->cache->get($cache_key);
            if ($data === FALSE) {
                $data = $this->Reports_model->getSalesByMonthLastYear();
                $this->cache->save($cache_key, $data, 900);
            }
        }
        echo json_encode($data);
    }

    /**
     * NOT IN USE, WILL BE DELETED SAFELY
     * AJAX: Get online/offline users by hour for last 24h
     */
    public function ajaxOnlineOffline24h()
    {
        $managername = $this->session->userdata('name');
        $cache_key = 'dashboardgraph_onlineoffline_' . $managername;
        $this->load->driver('cache', array('adapter' => 'file'));
        $force_refresh = $this->input->get('refresh');
        if ($force_refresh) {
            $data = $this->Reports_model->getOnlineOfflineByHourLast24h();
            $this->cache->save($cache_key, $data, 900);
        } else {
            $data = $this->cache->get($cache_key);
            if ($data === FALSE) {
                $data = $this->Reports_model->getOnlineOfflineByHourLast24h();
                $this->cache->save($cache_key, $data, 900);
            }
        }
        echo json_encode($data);
    }

    /**
     * NOT IN USE, WILL BE DELETED SAFELY
     * AJAX: Get top 5 service plans with price and sales for the last month
     */
    public function ajaxTop5ServicePlansLastMonth()
    {
        $managername = $this->session->userdata('name');
        $cache_key = 'dashboardgraph_top5plans_' . $managername;
        $this->load->driver('cache', array('adapter' => 'file'));
        $force_refresh = $this->input->get('refresh');
        if ($force_refresh) {
            $data = $this->Reports_model->getTop5ServicePlansLastMonth();
            $this->cache->save($cache_key, $data, 900);
        } else {
            $data = $this->cache->get($cache_key);
            if ($data === FALSE) {
                $data = $this->Reports_model->getTop5ServicePlansLastMonth();
                $this->cache->save($cache_key, $data, 900);
            }
        }
        echo json_encode($data);
    }

    /**
     * NOT IN USE, WILL BE DELETED SAFELY
     * AJAX: Get dashboard fair use alert (top 5 users by usage last month)
     */
    public function ajaxDashboardFairuseAlert()
    {
        $managername = $this->session->userdata('name');
        $cache_key = 'dashboardgraph_fairuse_' . $managername;
        $this->load->driver('cache', array('adapter' => 'file'));
        $force_refresh = $this->input->get('refresh');
        if ($force_refresh) {
            $data = $this->Reports_model->dashboard_fairuse_alert();
            $this->cache->save($cache_key, $data, 900);
        } else {
            $data = $this->cache->get($cache_key);
            if ($data === FALSE) {
                $data = $this->Reports_model->dashboard_fairuse_alert();
                $this->cache->save($cache_key, $data, 900);
            }
        }
        echo json_encode($data);
    }

}

?>

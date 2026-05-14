<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016 2017
 */
class Stock_controller extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Accounts/Jvs_model');
        $this->load->model('Accounts/Accounts_model');
        $this->load->model('Invoices_model');
        $this->isLoggedIn();   
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->global['pageTitle'] = 'Stock : Dashboard';
        
        $this->loadViews("dashboard", $this->global, NULL , NULL);
    }

    function stockListing($type = NULL)
    {

        $this->load->model('users_model');
        $managerInfo = $this->users_model->getManagerInfo($this->session->userdata ( 'name' ));
        $managerAllServices = $managerInfo->perm_createservices;
     
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;
        
        $type = $this->security->xss_clean($this->input->post('searchText1'));
        if($type == ''){ $type = 'STOCK'; }

        $this->load->library('pagination');
        
        $count = $this->Jvs_model->jvsCount($searchText, $type, $managerAllServices);

        $returns = $this->paginationCompress ( "stockList/", $count, 20 );
        $data['stockList'] = $this->Jvs_model->jvsListing($searchText, $type, $returns["page"], $returns["segment"], $managerAllServices);
        $data['type'] = $type;
        $data['accountsGroup'] = $this->Accounts_model->getAccountsGroup();
        $data['managerList'] = $this->users_model->getManagersList();
        $data['typesList'] = $this->Jvs_model->getStkTypeList();
        $this->global['pageTitle'] = 'Pace-Tel : Stocks List';
        
        $this->loadViews("Accounts/stockList", $this->global, $data, NULL);

    }

    function stockAddNew($stkType = 0, $username = NULL)
    {
        $this->load->model('users_model');
        $managername = $this->session->userdata ( 'name' );
        $managerInfo = $this->users_model->getManagerInfo($managername);
        if($this->accountsmanager == 1 || $managername == 'admin')
        {
            $this->load->model('users_model');
            $data['managerList'] = $this->users_model->getManagersList();
            $data['accountsGroup'] = $this->Accounts_model->getAccountsGroup();
            $data['debitaccount'] = $this->Accounts_model->getAccountsList('ITEM', 3);
            $data['wallets'] = $this->Invoices_model->invcollection_get_wallets();

            $data['stocktype'] = $stkType;
            $data['username'] = $username;

            if($stkType == 1){
                $data['pagetitle'] = 'New Purchase Invoice';
                $data['jvtype'] = 'STOCK';
                $data['creditaccount'] = $this->Accounts_model->getAccountsList('', 1);
            }elseif($stkType == 4){
                $data['users'] = $this->Invoices_model->activationTicket_get_users($managername);
                $data['pagetitle'] = 'New Stock Issue';
                $data['jvtype'] = 'ISSUE';
                $data['creditaccount'] = $this->Accounts_model->getAccountsList('COSTOFSALE', 3);
            }elseif($stkType == 6){
                $data['pagetitle'] = 'Stock Transfer IN';
                $data['jvtype'] = 'TRANSFER-IN';
                $data['creditaccount'] = $this->Accounts_model->getAccountsList('COSTOFSALE', 3);
            }elseif($stkType == 7){
                $data['pagetitle'] = 'Stock Transfer OUT';
                $data['jvtype'] = 'TRANSFER-OUT';
                $data['creditaccount'] = $this->Accounts_model->getAccountsList('COSTOFSALE', 3);
            }
            
            $this->global['pageTitle'] = 'Stocks : Add New Entry';

            $this->loadViews("Accounts/stockAddNew", $this->global, $data, NULL);
        }else{
            //echo "Not Allowed..........";
            $this->session->set_flashdata('error', 'Operation not allowed........');
            redirect('stockList');
        }
    }

    function saveStockEntry($stkType = 0)
    {

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('jvtype','JV Type','required');
            $this->form_validation->set_rules('jvdate','Date','required');
            $this->form_validation->set_rules('accdr','Account ID','trim|required|max_length[11]');
            $this->form_validation->set_rules('acccr','Account ID','trim|required|max_length[11]');
            $this->form_validation->set_rules('desc','Description','required|max_length[250]');
            $this->form_validation->set_rules('invinqty','In Qty','trim|required|numeric|greater_than[0]|max_length[11]');
            $this->form_validation->set_rules('invprice','Price','trim|numeric|max_length[16]');
            //$this->form_validation->set_rules('invtotal','Total','trim|required|numeric|max_length[16]');

            if($this->form_validation->run() == FALSE)
            {
                $this->saveStockEntry($this->input->post('jvid'));
            }
            else
            {
                $managername = $this->security->xss_clean($this->input->post('manager'));
                if(empty($managername)){
                    $managername = $this->session->userdata ( 'name' );
                }

                $jvtype = $this->security->xss_clean($this->input->post('jvtype'));
                $jvdate = $this->security->xss_clean($this->input->post('jvdate'));
                $accdr = $this->security->xss_clean($this->input->post('accdr'));
                $acccr = $this->security->xss_clean($this->input->post('acccr'));
                $desc = $this->security->xss_clean($this->input->post('desc'));
                $qty = $this->security->xss_clean($this->input->post('invinqty'));
                $price = $this->security->xss_clean($this->input->post('invprice'));
                $total = $this->security->xss_clean($this->input->post('invtotal'));
                $username = $this->security->xss_clean($this->input->post('username'));

                $jvTypeCode = $this->Jvs_model->getJvTypeCode($jvtype);

                if($stkType == 1 || $stkType == 6){

                    $jvInfo = array('jvdate'=>$jvdate,
                                    'acctdr'=>$accdr,
                                    'acctcr'=>$acccr,
                                    'desc'=>$desc,
                                    'jvtype'=>$jvTypeCode->typeid,
                                    'invinqty'=>$qty,
                                    'invprice'=>$price,
                                    'invtotal'=>$total,
                                    'debit'=>$total,
                                    'credit'=>$total,
                                    'username'=>$username,
                                    'walletid'=>($stkType == 1) ? $this->input->post('walletid') : 0,
                                    'managername'=>$managername);

                    $result = $this->Jvs_model->addNewJv($jvInfo);
                    if($username == NULL){
                        redirect('stockList');
                    }else{
                        redirect('docsUpload/0/'.$username);
                    }

                }else{

                    $stkAva = $this->Jvs_model->getItemStockInfo($accdr, $managername); // Get Stock Info of Item
                    //$accinfo = $this->Accounts_model->getCGSInfo();

                    if(($stkAva->stock - ($stkAva->Issue + $qty)) >= 0)
                    {
                        $jvInfo = array('jvdate'=>$jvdate,
                                    'acctdr'=>$acccr,
                                    'acctcr'=>$accdr,
                                    'desc'=>$desc,
                                    'jvtype'=>$jvTypeCode->typeid,
                                    'invinqty'=>$qty,
                                    'invprice'=>$price,
                                    'invtotal'=>$total,
                                    'debit'=>$total,
                                    'credit'=>$total,
                                    'username'=>$username,
                                    'managername'=>$managername);
                        $result = $this->Jvs_model->addNewJv($jvInfo);
                        if($username == NULL){
                            redirect('stockList');
                        }else{
                            redirect('docsUpload/0/'.$username);
                        }

                    }else{
                        $this->session->set_flashdata('error', 'Do not have enough stock for this Item');
                        redirect('stockAddNew/'.$stkType);
                        $result = False;
                    }

                }
                                                   

            
                if($result == True)
                {
                    $this->session->set_flashdata('success', 'Purchase Invoice Created successfully');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Purchase Invoice creation failed');
                }

                //redirect('stockList');
            }
        }
    }

    function accountNameExists($accname)
    {

        if(!empty($accname)){

            $managername = $this->session->userdata ( 'name' );
            $result = $this->Accounts_model->accountNameExists($accname);

            if(!empty($result))
                $this->form_validation->set_message('accountExists', 'The {field} already taken');
                return true;
            }
            else{
                return false;
            }
    }

    function editStockEntry($jvid = NULL, $stkType = 0)
    {

            if($jvid == null)
            {
                redirect('stockList');
            }

            $this->load->model('users_model');
            $managername = $this->session->userdata ( 'name' );
            $managerInfo = $this->users_model->getManagerInfo($managername);

            if($this->accountsmanager == 1 || $managername == 'admin')
            {

                $data['managerList'] = $this->users_model->getManagersList();
                $data['accountsGroup'] = $this->Accounts_model->getAccountsGroup();
                $data['debitaccount'] = $this->Accounts_model->getAccountsList('ITEM', 3);
                $data['transInfo'] = $this->Jvs_model->getStockEntryInfo($jvid);
                $data['wallets'] = $this->Invoices_model->invcollection_get_wallets();

                $data['stocktype'] = $stkType;

                if($stkType == 1 || $stkType == 6){
                    $data['pagetitle'] = 'Edit Purchase Entry';
                    $data['jvtypeName'] = 'STOCK';
                    $data['creditaccount'] = $this->Accounts_model->getAccountsList('', 1);
                }else{
                    $data['pagetitle'] = 'Edit Stock Issue';
                    $data['jvtypeName'] = 'ISSUE';
                    $data['creditaccount'] = $this->Accounts_model->getAccountsList('COSTOFSALE', 3);
                }
                
                $this->global['pageTitle'] = 'Pace Radius : Edit Accounts';
                
                $this->loadViews("Accounts/stockEditOld", $this->global, $data, NULL);

            }else{
                //$this->session->set_flashdata('error', 'Service creation failed');
                $this->session->set_flashdata('error', 'Operation not allowed........');
                redirect('stockList');
            }
    }

    function updateStockEntry($stkType = 0)
    {

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {

            $this->load->library('form_validation');
            $jvid = $this->input->post('jvid');
            $this->form_validation->set_rules('jvdate','Date','required');
            $this->form_validation->set_rules('accdr','Account ID','trim|required|max_length[11]');
            $this->form_validation->set_rules('acccr','Account ID','trim|required|max_length[11]');
            $this->form_validation->set_rules('desc','Description','required|max_length[250]');
            $this->form_validation->set_rules('invinqty','In Qty','trim|required|numeric|greater_than[0]|max_length[11]');
            $this->form_validation->set_rules('invprice','Price','trim|numeric|max_length[16]');

            if($this->form_validation->run() == FALSE)
            {
                $this->updateExpenseEntry($jvid);
            }
            else
            {
                $managername = $this->security->xss_clean($this->input->post('manager'));

                if(empty($managername)){
                    $managername = $this->session->userdata ( 'name' );
                }

                //$jvtype = $this->security->xss_clean($this->input->post('jvtype'));
                $jvdate = $this->security->xss_clean($this->input->post('jvdate'));
                $accdr = $this->security->xss_clean($this->input->post('accdr'));
                $acccr = $this->security->xss_clean($this->input->post('acccr'));
                $desc = $this->security->xss_clean($this->input->post('desc'));
                $qty = $this->security->xss_clean($this->input->post('invinqty'));
                $price = $this->security->xss_clean($this->input->post('invprice'));
                $total = $this->security->xss_clean($this->input->post('invtotal'));

                //$jvTypeCode = $this->Jvs_model->getJvTypeCode($jvtype);
                                                                  
                $this->load->model('users_model');
                
                if($stkType == 1 || $stkType == 6){

                    $jvInfo = array('jvdate'=>$jvdate,
                                    'acctdr'=>$accdr,
                                    'acctcr'=>$acccr,
                                    'desc'=>$desc,
                                    'invinqty'=>$qty,
                                    'invprice'=>$price,
                                    'invtotal'=>$total,
                                    'debit'=>$total,
                                    'credit'=>$total,
                                    'managername'=>$managername);
                    $result = $this->Jvs_model->updateExpenseEntry($jvInfo, $jvid);
                    redirect('stockList');

                }else{

                    $prev_issue = $this->Jvs_model->getExpenseInfo($jvid);
                    $stkAva = $this->Jvs_model->getItemStockInfo($accdr, $managername); // Get Stock Info of Item
                    $accinfo = $this->Accounts_model->getCGSInfo();
                    $stockInHand = ($stkAva->stock+$prev_issue->invinqty) - ($stkAva->Issue + $qty);

                    if($stockInHand >= 0)
                    {
                        $jvInfo = array('jvdate'=>$jvdate,
                                        'acctdr'=>$accinfo->acctid,
                                        'acctcr'=>$accdr,
                                        'desc'=>$desc,
                                        'invinqty'=>$qty,
                                        'invprice'=>$price,
                                        'invtotal'=>$total,
                                        'debit'=>$total,
                                        'credit'=>$total,
                                        'managername'=>$managername);
                        $result = $this->Jvs_model->updateExpenseEntry($jvInfo, $jvid);
                        redirect('stockList');

                    }else{
                        $this->session->set_flashdata('error', 'Do not have enough stock for this Item');
                        redirect('stockEdit/'.$jvid.'/2');
                        $result = False;
                    }

                }

                if($result == True){
                    $this->session->set_flashdata('success', 'Expense Entry Updated successfully.');
                }else{
                    $this->session->set_flashdata('error', 'Failed to update Expense Entry');
                }

                redirect('stockList');

                
            }
        }
    }

    function stockListing_user($userId = NULL)
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
            
            $this->load->model('users_model');
            $userInfo = $this->users_model->getUserInfo($userId);
            
            if(empty($userInfo))
            {
                $this->session->set_flashdata('error', 'User not found');
                redirect('usersListing');
            }
            
            $data['userInfo'] = $userInfo;
            $data['stockList'] = $this->Jvs_model->getStockListByUser($userInfo->username);
            
            $this->global['pageTitle'] = 'Stock List : '.$userInfo->firstname.' '.$userInfo->lastname;
            
            $this->loadViews("Accounts/stockList_user", $this->global, $data, NULL);
        }
    }

}

?>
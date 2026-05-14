<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016 2017
 */
class Jvs_controller extends BaseController
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
        $this->global['pageTitle'] = 'JVS : Dashboard';
        
        $this->loadViews("dashboard", $this->global, NULL , NULL);
    }

    function jvsListing($type = NULL)
    {

        $this->load->model('users_model');
        $managerInfo = $this->users_model->getManagerInfo($this->session->userdata ( 'name' ));
        $managerAllServices = $managerInfo->perm_createservices;
     
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;
        $type = $this->security->xss_clean($this->input->post('searchText1'));

        $this->load->library('pagination');
        
        $count = $this->Jvs_model->jvsCount($searchText, $type, $managerAllServices);

        $returns = $this->paginationCompress ( "jvslist/", $count, 20 );

        $data['jvslist'] = $this->Jvs_model->jvsListing($searchText, $type, $returns["page"], $returns["segment"], $managerAllServices);
        $data['type'] = $type;
        $data['accountsGroup'] = $this->Accounts_model->getAccountsGroup();
        $data['managerList'] = $this->users_model->getManagersList();
        $data['typesList'] = $this->Jvs_model->getJvTypeList();
        $this->global['pageTitle'] = 'Pace-Tel : JVs List';
        
        $this->loadViews("Accounts/jvslist", $this->global, $data, NULL);

    }

    function expenseAddNew($username = NULL)
    {
        $this->load->model('users_model');
        $managername = $this->session->userdata ( 'name' );
        $managerInfo = $this->users_model->getManagerInfo($managername);
        if($this->accountsmanager == 1 || $managername == 'admin')
        {
            $this->load->model('users_model');
            $data['managerList'] = $this->users_model->getManagersList();
            $data['accountsGroup'] = $this->Accounts_model->getAccountsGroup();
            $data['defaultaccount'] = $this->Accounts_model->getAccountsList('CASH', 1);
            $data['expenseaccount'] = $this->Accounts_model->getAccountsList('EXPENSES', 0);
            $data['wallets'] = $this->Invoices_model->invcollection_get_wallets();

            $data['username'] = $username;
            
            $this->global['pageTitle'] = 'Expenses : Add New Entry';

            $this->loadViews("Accounts/expenseAddNew", $this->global, $data, NULL);
        }else{
            //echo "Not Allowed..........";
            $this->session->set_flashdata('error', 'Operation not allowed........');
            redirect('jvslist');
        }
    }

    function saveJv()
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
            $this->form_validation->set_rules('accdr','Account ID','trim|required|max_length[150]');
            $this->form_validation->set_rules('acccr','Account ID','trim|required|max_length[150]');
            $this->form_validation->set_rules('desc','Description','required|max_length[250]');
            $this->form_validation->set_rules('amount','Amount','trim|required|numeric|greater_than[0]|max_length[16]');

            if($this->form_validation->run() == FALSE)
            {
                $this->expenseAddNew($this->input->post('jvid'));
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
                $amount = $this->security->xss_clean($this->input->post('amount'));
                $username = $this->security->xss_clean($this->input->post('username'));
                $walletid = $this->security->xss_clean($this->input->post('walletid'));

                $jvTypeCode = $this->Jvs_model->getJvTypeCode($jvtype);
              
                $jvInfo = array('jvdate'=>$jvdate,
                                    'acctdr'=>$accdr,
                                    'acctcr'=>$acccr,
                                    'desc'=>$desc,
                                    'jvtype'=>$jvTypeCode->typeid,
                                    'debit'=>$amount,
                                    'credit'=>$amount,
                                    'username'=>$username,
                                    'walletid'=>$walletid,
                                    'managername'=>$managername);

                if(!empty($accdr) && !empty($acccr)){
                    $this->load->model('users_model');
                    $result = $this->Jvs_model->addNewJv($jvInfo);
                }else{
                    $this->session->set_flashdata('error', 'Kindly select account & try again...');
                    $result = False;
                    redirect('jvslist');
                }

            
                if($result == True)
                {
                    $this->session->set_flashdata('success', 'Account Created successfully');
                    redirect('jvslist');
                }
                else
                {
                    $this->session->set_flashdata('error', 'Account creation failed');
                    redirect('jvslist');
                }
                
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

    function editExpenseEntry($jvid = NULL)
    {

            if($jvid == null)
            {
                redirect('jvslist');
            }

            $this->load->model('users_model');
            $managername = $this->session->userdata ( 'name' );
            $managerInfo = $this->users_model->getManagerInfo($managername);

            if($this->accountsmanager == 1 || $managername == 'admin')
            {

                $data['managerList'] = $this->users_model->getManagersList();
                $data['defaultaccount'] = $this->Accounts_model->getAccountsList('CASH',1);
                $data['expenseaccount'] = $this->Accounts_model->getAccountsList('EXPENSES');
                $data['expenseInfo'] = $this->Jvs_model->getExpenseInfo($jvid);
                $data['wallets'] = $this->Invoices_model->invcollection_get_wallets();
                
                $this->global['pageTitle'] = 'Pace Radius : Edit Accounts';
                
                $this->loadViews("Accounts/expenseEditOld", $this->global, $data, NULL);

            }else{
                //$this->session->set_flashdata('error', 'Service creation failed');
                $this->session->set_flashdata('error', 'Operation not allowed........');
                redirect('jvslist');
            }
    }

    function updateJvsEntry()
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
            $this->form_validation->set_rules('amount','Amount','trim|required|numeric|greater_than[0]|max_length[16]');

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

                $jvtype = $this->security->xss_clean($this->input->post('jvtype'));
                $jvdate = $this->security->xss_clean($this->input->post('jvdate'));
                $accdr = $this->security->xss_clean($this->input->post('accdr'));
                $acccr = $this->security->xss_clean($this->input->post('acccr'));
                $desc = $this->security->xss_clean($this->input->post('desc'));
                $amount = $this->security->xss_clean($this->input->post('amount'));
                $walletid = $this->security->xss_clean($this->input->post('walletid'));

                $jvTypeCode = $this->Jvs_model->getJvTypeCode($jvtype);
              
                $jvInfo = array('jvdate'=>$jvdate,
                                    'acctdr'=>$accdr,
                                    'acctcr'=>$acccr,
                                    'desc'=>$desc,
                                    'jvtype'=>$jvTypeCode->typeid,
                                    'debit'=>$amount,
                                    'credit'=>$amount,
                                    'walletid'=>$walletid,
                                    'managername'=>$managername);

                if(!empty($accdr) && !empty($acccr)){
                    $this->load->model('users_model');
                    //$result = $this->Jvs_model->addNewJv($jvInfo);
                    $result = $this->Jvs_model->updateExpenseEntry($jvInfo, $jvid);
                }else{
                    $this->session->set_flashdata('error', 'Update Failed, Kindly select account & try again..');
                    $result = False;
                    redirect('jvslist');
                }


                if($result == True){
                    $this->session->set_flashdata('success', 'Expense Entry Updated successfully.');
                }else{
                    $this->session->set_flashdata('error', 'Failed to update Entry');
                }

                redirect('jvslist');

                
            }
        }
    }


    // *******   Deposit Add New ***********
    function depositAddNew($username = NULL)
    {
        $this->load->model('users_model');
        $managername = $this->session->userdata ( 'name' );
        $managerInfo = $this->users_model->getManagerInfo($managername);
        if($this->accountsmanager == 1 || $managername == 'admin')
        {
            $this->load->model('users_model');
            $data['managerList'] = $this->users_model->getManagersList();
            $data['accountsGroup'] = $this->Accounts_model->getAccountsGroup();
            $data['defaultaccount'] = $this->Accounts_model->getAccountsList('CASH',1);
            $data['depositaccount'] = $this->Accounts_model->getAccountsList('BANK',1);
            $data['wallets'] = $this->Invoices_model->invcollection_get_wallets();

            $data['username'] = $username;
            
            $this->global['pageTitle'] = 'Deposit : Add New Deposit';

            $this->loadViews("Accounts/depositAddNew", $this->global, $data, NULL);
        }else{
            //echo "Not Allowed..........";
            $this->session->set_flashdata('error', 'Operation not allowed........');
            redirect('jvslist');
        }
    }

    function editDepositEntry($jvid = NULL)
    {

            if($jvid == null)
            {
                redirect('jvslist');
            }

            $this->load->model('users_model');
            $managername = $this->session->userdata ( 'name' );
            $managerInfo = $this->users_model->getManagerInfo($managername);

            if($this->accountsmanager == 1 || $managername == 'admin')
            {

                $data['managerList'] = $this->users_model->getManagersList();
                $data['defaultaccount'] = $this->Accounts_model->getAccountsList('CASH',1);
                $data['depositaccount'] = $this->Accounts_model->getAccountsList('BANK',1);
                $data['transInfo'] = $this->Jvs_model->getDepsoitInfo($jvid);
                $data['wallets'] = $this->Invoices_model->invcollection_get_wallets();
                
                $this->global['pageTitle'] = 'Pace Radius : Edit Accounts';
                
                $this->loadViews("Accounts/depositEditOld", $this->global, $data, NULL);

            }else{
                //$this->session->set_flashdata('error', 'Service creation failed');
                $this->session->set_flashdata('error', 'Operation not allowed........');
                redirect('jvslist');
            }
    }

        // *******   Deposit Add New ***********
        function salesAddNew($username = NULL)
        {
            $this->load->model('users_model');
            $managername = $this->session->userdata ( 'name' );
            $managerInfo = $this->users_model->getManagerInfo($managername);
            if($this->accountsmanager == 1 || $managername == 'admin')
            {
                $this->load->model('users_model');
                $data['managerList'] = $this->users_model->getManagersList();
                $data['accountsGroup'] = $this->Accounts_model->getAccountsGroup();
                $data['creditaccount'] = $this->Accounts_model->getAccountsList('INCOME', 2);
                $data['debitaccount'] = $this->Accounts_model->getAccountsList('CUSTOMER', 1);
                $data['wallets'] = $this->Invoices_model->invcollection_get_wallets();

                $data['username'] = $username;
                
                $this->global['pageTitle'] = 'Invoice : Add New Sales Entry';
    
                $this->loadViews("Accounts/salesAddNew", $this->global, $data, NULL);
            }else{
                //echo "Not Allowed..........";
                $this->session->set_flashdata('error', 'Operation not allowed........');
                redirect('jvslist');
            }
        }

        function editSalesEntry($jvid = NULL)
        {
    
                if($jvid == null)
                {
                    redirect('jvslist');
                }
    
                $this->load->model('users_model');
                $managername = $this->session->userdata ( 'name' );
                $managerInfo = $this->users_model->getManagerInfo($managername);
    
                if($this->accountsmanager == 1 || $managername == 'admin')
                {
    
                    $data['managerList'] = $this->users_model->getManagersList();
                    $data['creditaccount'] = $this->Accounts_model->getAccountsList('INCOME', 2);
                    $data['debitaccount'] = $this->Accounts_model->getAccountsList('CUSTOMER', 1);
                    $data['transInfo'] = $this->Jvs_model->getDepsoitInfo($jvid);
                    $data['wallets'] = $this->Invoices_model->invcollection_get_wallets();
                    
                    $this->global['pageTitle'] = 'Pace Radius : Edit Sales Entry';
                    
                    $this->loadViews("Accounts/salesEditOld", $this->global, $data, NULL);
    
                }else{
                    //$this->session->set_flashdata('error', 'Service creation failed');
                    $this->session->set_flashdata('error', 'Operation not allowed........');
                    redirect('jvslist');
                }
        }


}

?>
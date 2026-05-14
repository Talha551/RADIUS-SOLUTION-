<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016 2017
 *
 * @property CI_Loader $load
 * @property CI_DB_query_builder $db
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Security $security
 */
class Accounts_controller extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Accounts/Accounts_model');
        $this->load->model('Accounts/Jvs_model');
        $this->isLoggedIn();   
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->global['pageTitle'] = 'Accounts : Dashboard';
        
        $this->loadViews("dashboard", $this->global, NULL , NULL);
    }

    function accountsListing($managerFilter = NULL)
    {

        $this->load->model('users_model');
        $managerInfo = $this->users_model->getManagerInfo($this->session->userdata ( 'name' ));
        $managerAllServices = $managerInfo->perm_createservices;
     
        $searchText = $this->security->xss_clean($this->input->post('searchText'));
        $data['searchText'] = $searchText;
        $type = $this->security->xss_clean($this->input->post('searchText1'));

        $this->load->library('pagination');
        
        $count = $this->Accounts_model->accountsCount($searchText, $type, $managerAllServices);

        $returns = $this->paginationCompress ( "accountsListing/", $count, 200 );

        $data['accountsListing'] = $this->Accounts_model->accountsListing($searchText, $type, $returns["page"], $returns["segment"], $managerAllServices);
        $data['type'] = $type;
        $data['accountsGroup'] = $this->Accounts_model->getAccountsGroup();
        $data['sumAccountsDrCr'] = $this->Accounts_model->sumAccountsDrCr($searchText, $type, $returns["page"], $returns["segment"], $managerAllServices);
        $data['managerFilter'] = $managerFilter;
        $this->global['pageTitle'] = 'Pace-Tel : Accounts';
        
        $this->loadViews("Accounts/accountslist", $this->global, $data, NULL);

    }

    function accountsAddNew()
    {
        $this->load->model('users_model');

        $managername = $this->session->userdata ( 'name' );
        $managerInfo = $this->users_model->getManagerInfo($managername);

        if($managername == 'admin' || $managerInfo->perm_allowaccountsadd == 1)
        {
            $this->load->model('users_model');
            $data['accountsGroup'] = $this->Accounts_model->getAccountsGroup();
            $data['managerList'] = $this->users_model->getManagersList();
            
            $this->global['pageTitle'] = 'Services : Add New User';

            $this->loadViews("Accounts/accountsAddNew", $this->global, $data, NULL);
        }else{
            //echo "Not Allowed..........";
            $this->session->set_flashdata('error', 'Operation not allowed........');
            redirect('accountslist');
        }
    }

    function saveAccounts()
    {

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('accname','Account Name','trim|required|max_length[150]');
            $this->form_validation->set_rules('grpname','Group','required');

            if($this->form_validation->run() == FALSE)
            {
                $this->accountsAddNew($this->input->post('accname'));
            }
            else
            {
                $managername = $this->security->xss_clean($this->input->post('managername'));
                if(empty($managername) || $managername == "0"){
                    $managername = $this->session->userdata ( 'name' );
                }
                
                $accname = $this->security->xss_clean($this->input->post('accname'));
                $groupname = $this->security->xss_clean($this->input->post('grpname'));
              
                $accountsInfo = array('accname'=>$accname,
                                    'accgroup'=>$groupname,
                                    'managername'=>$managername);
                                    
                $this->load->model('users_model');
                $this->load->model('Services_model');

                if(($this->Accounts_model->accountNameExists($accname)) == false)
                {
                    $result = $this->Accounts_model->addNewAccount($accountsInfo);
                
                    if($result == True)
                    {
                        $this->session->set_flashdata('success', 'Account Created successfully');
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'Account creation failed');
                    }
                
                }else{
                    $this->session->set_flashdata('error', 'Account already exists....');
                }

                redirect('accountslist');
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

    function editAccount($accid = NULL)
    {

            if($accid == null)
            {
                redirect('accountslist');
            }

            $this->load->model('users_model');
            $managername = $this->session->userdata ( 'name' );
            $managerInfo = $this->users_model->getManagerInfo($managername);

            if($managername == 'admin')
            {

                $data['accountsGroup'] = $this->Accounts_model->getAccountsGroup();
                $data['managerList'] = $this->users_model->getManagersList();
                $data['accountsInfo'] = $this->Accounts_model->getAccountsInfo($accid);
                
                $this->global['pageTitle'] = 'Pace Radius : Edit Accounts';
                
                $this->loadViews("Accounts/accountEditOld", $this->global, $data, NULL);

            }else{
                //$this->session->set_flashdata('error', 'Service creation failed');
                $this->session->set_flashdata('error', 'Operation not allowed........');
                redirect('accountslist');
            }
    }

    function updateAccount()
    {

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {

            $this->load->library('form_validation');
            $acctid = $this->input->post('acctid');

            $this->form_validation->set_rules('accname','Account Name','trim|required|max_length[150]');
            $this->form_validation->set_rules('accgroup','Group','required');

            if($this->form_validation->run() == FALSE)
            {
                $this->editService($acctid);
            }
            else
            {
                
                $managername = $this->security->xss_clean($this->input->post('managername'));
                if(empty($managername) || $managername  == "0"){
                    $managername = $this->session->userdata ( 'name' );
                }

                $accgroup = $this->security->xss_clean($this->input->post('accgroup'));
                $accname = $this->security->xss_clean($this->input->post('accname'));

                $accountInfo = array('accname'=>$accname, 'accgroup'=>$accgroup, 'managername'=>$managername);
                
                $result = $this->Accounts_model->updateAccout($accountInfo, $acctid);

                if($result == True){
                    $this->session->set_flashdata('success', 'Account Updated successfully.');
                }else{
                    $this->session->set_flashdata('error', 'Failed to update Service');
                }

                redirect('accountslist');

                
            }
        }
    }

    function accountsLedger()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            if($this->session->userdata ( 'name' ) <> 'admin'){   
                $searchText = $this->session->userdata ( 'name' );  }
            else{
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
            
            $this->load->library('pagination');
            
            $count = $this->Accounts_model->ledgerReportCount($searchText, $searchText1, $searchText2, $searchText3, $searchText4);

			$returns = $this->paginationCompress ( "accountsLedger/", $count, 1000 );
            
            $data['ledgerReport'] = $this->Accounts_model->ledgerReport($searchText, $searchText1, $searchText2, $searchText3, $searchText4, $returns["page"], $returns["segment"]);
            $data['ledgerSummery'] = $this->Accounts_model->ledgerSummery($searchText, $searchText1, $searchText2, $searchText3, $searchText4);
            $data['accountsList'] = $this->Accounts_model->getAccountsList('',3);
            $data['accountsGroup'] = $this->Accounts_model->getAccountsGroup();
            $data['jvTypes'] = $this->Jvs_model->getJvTypeList();

            $this->load->model('users_model');
            $data['managerList'] = $this->users_model->getManagersList();

            $this->global['pageTitle'] = 'Pace-Tel : Accounts Ledger Report';
            
            $this->loadViews("Accounts/ledgerAccounts", $this->global, $data, NULL);
        }
    }

    function accountsdashboard(){

        //$data['title'] = "Dashboard ";
        $this->global['pageTitle'] = 'Pace-Tel : Dashboard';

        $data['CashInHand'] = $this->Accounts_model->CashInHand();

        //print_r($data);
        //exit;
        
        $this->loadViews("Accounts/accountsdashboard", $this->global, $data, NULL);
    }


    function trialBalanceReport()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            if($this->session->userdata ( 'name' ) <> 'admin'){   
                $searchText = $this->session->userdata ( 'name' );  }
            else{
                $searchText = $this->security->xss_clean($this->input->post('searchText')); 
            }
            $searchText1 = $this->security->xss_clean($this->input->post('searchText1'));
            $searchText2 = $this->security->xss_clean($this->input->post('searchText2'));
            $searchText3 = $this->security->xss_clean($this->input->post('searchText3'));

            $data['searchText'] = $searchText;
            $data['searchText1'] = $searchText1;
            $data['searchText2'] = $searchText2;
            $data['searchText3'] = $searchText3;
            
            $data['ledgerReport'] = $this->Accounts_model->trialBalance($searchText, $searchText1, $searchText2, $searchText3);
            $data['trialSummery'] = $this->Accounts_model->trialSummery($searchText, $searchText1, $searchText2, $searchText3);
            $data['accountsList'] = $this->Accounts_model->getAccountsList('',0);
            $data['accountsGroup'] = $this->Accounts_model->getAccountsGroup();
            $data['jvTypes'] = $this->Jvs_model->getJvTypeList();

            $this->load->model('users_model');
            $data['managerList'] = $this->users_model->getManagersList();

            $this->global['pageTitle'] = 'Pace-Tel : Accounts Ledger Report';
            
            $this->loadViews("Accounts/trialBalance", $this->global, $data, NULL);
        }
    }

    public function getWalletAccount()
    {
        $walletid = $this->input->post('walletid');
        $wallet = $this->db->get_where('tbl_paywallets', ['walletid' => $walletid])->row();
        if ($wallet) {
            echo json_encode(['accdr' => $wallet->accdr, 'acccr' => $wallet->acccr]);
        } else {
            echo json_encode(['accdr' => null, 'acccr' => null]);
        }
        exit;
    }

    public function accountsLedgerStock()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $searchItem = $this->input->post('searchItem');
            $fromDate = $this->input->post('fromDate');
            $toDate = $this->input->post('toDate');

            $data['searchItem'] = $searchItem;
            $data['fromDate'] = $fromDate;
            $data['toDate'] = $toDate;

            $data['accountsList'] = $this->Accounts_model->getAccountsList('ITEM', 3);
            $data['stockLedger'] = $this->Accounts_model->stockLedgerReport($searchItem, $fromDate, $toDate);

            $this->global['pageTitle'] = 'Pace-Tel : Stock Ledger Report';
            $this->loadViews("Accounts/ledgerStockAccount", $this->global, $data, NULL);
        }
    }

    public function ledgerWallet()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $walletid = $this->input->post('walletid');
            $fromDate = $this->input->post('fromDate');
            $toDate = $this->input->post('toDate');

            $data['walletid'] = $walletid;
            $data['fromDate'] = $fromDate;
            $data['toDate'] = $toDate;

            $data['walletList'] = $this->Accounts_model->getWalletList();
            $data['walletLedger'] = $this->Accounts_model->walletLedgerReport($walletid, $fromDate, $toDate);

            $this->global['pageTitle'] = 'Pace-Tel : Wallet Ledger Report';
            $this->loadViews("Accounts/ledgerWallet", $this->global, $data, NULL);
        }
    }

}

?>
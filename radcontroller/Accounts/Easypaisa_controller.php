<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016 2017
 */
class Easypaisa_controller extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Accounts/Accounts_model');
        $this->load->model('Accounts/Jvs_model');
        //$this->load->library('csvimport');
        $this->isLoggedIn();   
    }

    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->global['pageTitle'] = 'Easy Paisa : Dashboard';
        
        $this->loadViews("dashboard", $this->global, NULL , NULL);
    }

    function easyPaisa_upload()
    {

        $data['epdata'] = $this->Accounts_model->get_easypaisalist();
        $data['epdataowner'] = $this->Accounts_model->get_easypaisaownerlist();
     
        $this->global['pageTitle'] = 'Pace-Tel : Accounts';

        $this->loadViews("Accounts/epcollection", $this->global, $data, NULL);
    }

    function importcsvfile(){

        //$this->load->view('import_data');
        if(isset($_POST["submit"]))
        {
            $file = $_FILES['file']['tmp_name'];
            $handle = fopen($file, "r");
            $c = 0;//
            $expected_header = ['Consumer Number', 'Customer Name', 'Amount Paid', 'Transaction Date'];
            while(($filesop = fgetcsv($handle, 1000, ",")) !== false)
            {
                if($c == 0) { // Header row
                    $header = array_map(function($h) { return trim($h, " \t\n\r\0\x0B\""); }, $filesop);
                    if($header !== $expected_header) {
                        $this->session->set_flashdata('Error', 'CSV header does not match the required format. Please use the template: Consumer Number,Customer Name,Amount Paid,Transaction Date');
                        redirect('epimport');
                        return;
                    }
                    $c++;
                    continue;
                }
                $consumer_number = $filesop[0];
                $customer_name = $filesop[1];
                $amount_paid = $filesop[2];
                $transaction_date = $filesop[3];
                if($c<>0){                   /* SKIP THE FIRST ROW */
                    $result = $this->Accounts_model->check_epentryexists($consumer_number, $customer_name, $amount_paid, $transaction_date);
                    if(empty($result))
                    {
                        $username = $this->Accounts_model->get_userfrompayid($consumer_number);
                        if(!empty($username)){
                            $this->Accounts_model->insert_eptransactioncsv($consumer_number, $customer_name, $amount_paid, $transaction_date, $username->username);
                        }else{
                            if(!empty($consumer_number)){
                                $this->Accounts_model->insert_eptransactioncsv($consumer_number, $customer_name, $amount_paid, $transaction_date, "");
                            }else{
                                echo $consumer_number." - ".$customer_name." - ".$amount_paid." - ".$transaction_date;
                                $this->session->set_flashdata('Error', 'Reached end of file with empty rows');
                                redirect('epimport');
                            }
                        }
                    }
                }
                $c = $c + 1;
            }

            redirect('epimport');
        }

    }

    function postresellercredit(){

        $result = $this->Accounts_model->ep_addresellercredit();
        $this->session->set_flashdata('success', 'Entries are posted successfully.');
        redirect('epimport');
        
    }

    function importcsv() {

        $data['epdata'] = $this->Accounts_model->get_easypaisalist();
        $data['error'] = '';    //initialize image upload error array to empty
 
        //$config['upload_path'] = './uploads/';
        $config['upload_path'] = '/var/www/html/radspot/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = '1000';
 
        $this->load->library('upload', $config);
 
 
        // If upload failed, display error
        if (!$this->upload->do_upload()) {
            $data['error'] = $this->upload->display_errors();
 
            $this->load->view("Accounts/epcollection", $data);

        } else {

            $file_data = $this->upload->data();
            $file_path =  '/var/www/html/paceradius/'.$file_data['file_name'];
 
            if ($this->csvimport->get_array($file_path)) {
                $csv_array = $this->csvimport->get_array($file_path);
                foreach ($csv_array as $row) {
                    $insert_data = array(
                        'consumer_number'=>$row['consumer_number'],
                        'customer_name'=>$row['customer_name'],
                        'amount_paid'=>$row['amount_paid'],
                        'transaction_date'=>$row['transaction_date'],
                    );
                    $this->csv_model->insert_csv($insert_data);
                }
                $this->session->set_flashdata('success', 'Csv Data Imported Succesfully');
                redirect(base_url().'csv');
                //echo "<pre>"; print_r($insert_data);
            } else 
                $data['error'] = "Error occured";
                $this->load->view('csvindex', $data);
            }
 
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

}
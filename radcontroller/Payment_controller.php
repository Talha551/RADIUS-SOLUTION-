<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * Class : User (UserController)
 * User Class to control all user related operations.
 * @author : Kishor Mali
 * @version : 1.1
 * @since : 15 November 2016
 */
class Payment_controller extends BaseController
{
    /**
     * This is default constructor of the class
     */
    public function __construct()
    {
        parent::__construct();
        //$this->load->model('payment_model');
        $this->load->model('users_model');
        $this->isLoggedIn();   
    }
    
    /**
     * This function used to load the first screen of the user
     */
    public function index()
    {
        $this->global['pageTitle'] = 'PaceTel : Dashboard';
        
        $this->loadViews("dashboard", $this->global, NULL , NULL);
    }

    function payproAddNew()
    {

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

    function payproAPIConnect(){

        //$url, $clientid, $clientsecret

        $this->load->helper('array');

        $url = Globals::payproAuthURL();
        $clientid = Globals::clientid();
        $clientsecret = Globals::clientsecret();

        $data = "$clientid&$clientsecret";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl , CURLOPT_FOLLOWLOCATION , 1 );
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $curl , CURLOPT_HEADER , true );
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        $result = curl_exec($curl);

        // how big are the headers
        $headerSize = curl_getinfo( $curl , CURLINFO_HEADER_SIZE );
        $headerStr = substr( $result , 0 , $headerSize );
        $bodyStr = substr( $result , $headerSize );

        // convert headers to array
        $headers = $this->headersToArray( $headerStr );

        
        $token = element('Token', $headers);

        Globals::setPayproToken($token);

        curl_close($curl);

    }

    function headersToArray( $str )
    {
        $headers = array();
        $headersTmpArray = explode( "\r\n" , $str );
        for ( $i = 0 ; $i < count( $headersTmpArray ) ; ++$i )
        {
            // we dont care about the two \r\n lines at the end of the headers
            if ( strlen( $headersTmpArray[$i] ) > 0 )
            {
                // the headers start with HTTP status codes, which do not contain a colon so we can filter them out too
                if ( strpos( $headersTmpArray[$i] , ":" ) )
                {
                    $headerName = substr( $headersTmpArray[$i] , 0 , strpos( $headersTmpArray[$i] , ":" ) );
                    $headerValue = substr( $headersTmpArray[$i] , strpos( $headersTmpArray[$i] , ":" )+1 );
                    $headers[$headerName] = $headerValue;
                }
            }
        }
        return $headers;
    }

    function payproAPIPost($token, $method, $url, $data){

        $curl = curl_init();
        switch ($method){
           case "POST":
              curl_setopt($curl, CURLOPT_POST, 1);
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
              break;
           case "PUT":
              curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
              if ($data)
                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
              break;
           default:
              if ($data)
                 $url = sprintf("%s?%s", $url, http_build_query($data));
        }

        // OPTIONS:
        curl_setopt($curl, CURLOPT_URL, $url);

        $authorization = "Token: ".$token;

        //'Content-Type: application/json' ,
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: text/plain', $authorization));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);

        // EXECUTE:
        $result = curl_exec($curl);
        if(!$result){die("Connection Failure");}
        curl_close($curl);

        return $result;

    }

    function payproAPIV2(){

    }

    function payproCreateSingleOrder($MerchantId = '', $OrderNumber = '', $OrderAmount = '', $OrderDueDate = '', $OrderType = '',
                                $IssueDate = '', $OrderExpireAfterSeconds = '', $CustomerName = '', $CustomerMobile = '',
                                $CustomerEmail = '', $CustomerAddress = ''){
        
        $this->payproAPIConnect();
        $token = Globals::payproToken();

        $data = '[
            {
            "MerchantId": "'.$MerchantId.'"
            },
                {
                    "OrderNumber": "'.$OrderNumber.'",
                    "OrderAmount": "'.$OrderAmount.'",
                    "OrderDueDate": "'.$OrderDueDate.'",
                    "OrderType": "'.$OrderType.'",
                    "IssueDate": "'.$IssueDate.'",
                    "OrderExpireAfterSeconds": "'.$OrderExpireAfterSeconds.'",
                    "CustomerName": "'.$CustomerName.'",
                    "CustomerMobile": "'.$CustomerMobile.'",
                    "CustomerEmail": "'.$CustomerEmail.'",
                    "CustomerAddress": "'.$CustomerAddress.'"
                }
            ]';

        $request = $this->payproAPIPost($token, "POST", Globals::payproOrderSingle() , $data); // Send or retrieve data

        print_r($request);

    }


}
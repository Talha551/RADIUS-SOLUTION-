<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';
require APPPATH . 'libraries/routeros_api.class.php';


class Mikrotik_api extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Invoices_model');
        $this->load->helper(array('form', 'url'));
        $this->isLoggedIn();   
    }


    public function index()
    {
        $this->global['pageTitle'] = 'Invoices List : Dashboard';
        
        $this->loadViews("dashboard", $this->global, NULL , NULL);
    }

    function connectMikrotik(){

        $username = "u2nasir10";
        $this->load->model('Reports_model');
        //$isUserOnline = $this->Reports_model->checkUserOnlineStatus($username);

        $API = new RouterosAPI();

        $API->debug = true;
        
        if ($API->connect('115.186.148.250', 'admin', 'Khyber@007')) {
                    
            $API->write('/interface/monitor-traffic',false);
            $API->write('=interface=<pppoe-u2nasir3>',false);
            $API->write('=once=');
        
           $READ = $API->read(false);
           $ARRAY = $API->parseResponse($READ);
           print_r($ARRAY);

           //$API->comm("/ip/firewall/mangle/add", array("chain" => "prerouting", "action" => "mark-routing", "new-routing-mark" => "to_WAN3"));

           $API->disconnect();
           
        }

    }
}


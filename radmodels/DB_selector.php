<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

// app/models/DB_selector.php

class DB_selector extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    public function select_database($hostname) {
        $db_config = $this->load->config('databases');
        foreach ($db_config as $config) {
            if ($config['hostname'] == $hostname) {
                return $config;
            }
        }
        return false;
    }

}
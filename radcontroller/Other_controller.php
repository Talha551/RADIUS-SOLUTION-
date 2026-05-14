<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';
require_once APPPATH . 'libraries/crontab.php';

class Other_controller extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        // Allow this method to be called without auth for testing only
    $method = $this->router->fetch_method();
    if ($method === 'mikrotik_webhook') {
        return; // skip the usual auth checks for this request
    }
    $method = $this->router->fetch_method();
    if ($method === 'webhook_tester') {
        return; // skip the usual auth checks for this request
    }
        $this->load->model('Other_model');
        $this->load->model('users_model');
        $this->load->helper(array('form', 'url'));
        $this->isLoggedIn();
    }

    public function index()
    {
        $this->global['pageTitle'] = 'NAS List : Dashboard';

        $this->loadViews("dashboard", $this->global, NULL , NULL);
    }

    function naslistDynamic()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {

            $searchText = $this->security->xss_clean($this->input->post('searchText'));
            $data['searchText'] = $searchText;

            $this->load->library('pagination');

            $count = $this->Other_model->naslistingDynamicCount($searchText);
            $returns = $this->paginationCompress ( "dynnaslisting/", $count, 1000 );
            $data['dynNasListing'] = $this->Other_model->naslistingDynamic($searchText, $returns["page"], $returns["segment"]);

            $this->global['pageTitle'] = 'Pace-Tel : NAS Listing';
            $this->loadViews("naslistdynamic", $this->global, $data, NULL);

        }
    }

    function nasDynamicAddNew()
    {
        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $managername = $this->session->userdata ( 'name' );
            $data['managername'] = $this->users_model->getManagersList();
            $data['nasname'] = $this->Other_model->getNasList();
            $data['usersGroup'] = $this->users_model->getUsersGroup();

            $this->global['pageTitle'] = 'NAS : Dynamic DNS MAP';
            $this->loadViews("nasdynamicAddNew", $this->global, $data, NULL);
        }

    }

    function saveNasDynamicMap()
    {

        if($this->isAdmin() == FALSE)
        {
            $this->loadThis();
        }
        else
        {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('nasname','NAS IP','trim|required|max_length[64]|valid_ip|callback_nasMappingExists');
            $this->form_validation->set_rules('shortname','Short Name','required|max_length[25]');
            $this->form_validation->set_rules('domain','Domain','valid_url');

            if($this->form_validation->run() == FALSE)
            {
                $this->nasDynamicAddNew($this->input->post('domain'));
                $this->nasDynamicAddNew($this->input->post('authmode'));
            }
            else
            {
                $shortname = $this->security->xss_clean($this->input->post('shortname'));
                $nasname = $this->security->xss_clean($this->input->post('nasname'));
                $secret = $this->security->xss_clean($this->input->post('secret'));
                $domain = $this->security->xss_clean($this->input->post('domain'));
                $authmode = $this->security->xss_clean($this->input->post('authmode'));
                $apiuser = $this->security->xss_clean($this->input->post('apiuser'));
                $apipasswd = $this->security->xss_clean($this->input->post('apipasswd'));
                $apiport = $this->security->xss_clean($this->input->post('apiport'));

                $nasInfo = array('shortname'=>$shortname,
                                'nasname'=>$nasname,
                                'type'=>0,
                                'secret'=>$secret,
                                'description'=>$domain,
                                'starospassword'=>"",
                                'ciscobwmode'=>0,
                                'apiusername'=>$apiuser,
                                'apipassword'=>$apipasswd,
                                'ports'=>$apiport,
                                'enableapi'=>0);

                $apiInfo = array('shortname'=>$shortname,
                                'nasname'=>$nasname,
                                'domain'=>$domain,
                                'authmode'=>$authmode,
                                'apiuser'=>$apiuser,
                                'apipasswd'=>$apipasswd,
                                'apiport'=>$apiport,
                                'managername'=>"admin");


                if($this->Other_model->checkNasMappingExists($nasname) == false)
                {
                    $result = $this->Other_model->addNasNew($nasInfo);

                    if($result == True)
                    {
                        $this->Other_model->addNasDynamic($apiInfo);
                        $radstatus = $this->nasCreateClientsConf();

                        $this->session->set_flashdata('success', 'NAS Mapping successfull. Radius Restarted with status ('.$radstatus.')');
                    }
                    else
                    {
                        $this->session->set_flashdata('error', 'NAS Mapping failed');
                    }

                }else{
                    $this->session->set_flashdata('error', 'NAS Mapping exists....');
                }

                redirect('Other_controller/naslistDynamic');
            }
        }
    }

    // Edit Existing NAS Mapping
    function editNas($nasId = NULL)
    {
        if($this->isAdmin() == FALSE || $nasId == 1)
        {
            $this->loadThis();
        }
        else
        {
            if($nasId == null)
            {
                redirect('dyndnslisting');
            }

            $managername = $this->session->userdata ( 'name' );
            $data['managername'] = $this->users_model->getManagersList();
            $data['nasname'] = $this->Other_model->getNasList();
            $data['usersGroup'] = $this->users_model->getUsersGroup();

            $this->global['pageTitle'] = 'NAS : Dynamic DNS Edit';

            $this->loadViews("nasdynamicEdit", $this->global, $data, NULL);
        }
    }

    function nasMappingExists($domain)
    {

        if(!empty($domain)){

            $managername = $this->session->userdata ( 'name' );
            $result = $this->Other_model->checkNasMappingExists($domain);
        
            if(!empty($result))
                $this->form_validation->set_message('NAS-Exists', 'The {field} already taken');
                return true;
            }
            else{
                return false;
            }
    }

    function nasMappingDelete($nasname)
    {
        if(!empty($nasname)){

            $managername = $this->session->userdata ( 'name' );

            $this->Other_model->deleteNasToManagers($nasname);
            $result = $this->Other_model->deleteNas($nasname);

            if(!empty($result))
                $result = $this->Other_model->deleteNasMapping($nasname);

                $this->nasCreateClientsConf();
                $this->session->set_flashdata('success', 'NAS '.$nasname.' Mapping deleted');
                redirect('Other_controller/naslistDynamic');
            }
            else{
                $this->session->set_flashdata('error', 'NAS '.$nasname.' Mapping deletion failed');
                redirect('Other_controller/naslistDynamic');
            }
    }

    function nasCreateClientsConf()
    {

        $restartStatus = 0;
        $file = '/usr/local/etc/raddb/clients.conf';
        //$clientsConf = file_get_contents($file);
        $clientsConf = "# This file generated by RadSpot Please do not edit \n\n";

        $result = $this->Other_model->getNasList();

        if(!empty($result))
        {
            $row_count = 1;
            foreach($result as $record)
            {

                $restartStatus = 1;
                $shortname = $record->shortname;
                $nasip = $record->nasname;
                $secret = $record->secret;
                //$search[] = $record->nasname; $replace[] = $newDomainIP;

                $nasInfo = "client ".$nasip." { \n\tsecret\t\t = 654321\n\tshortname\t = ".$shortname."\n}\n";

                $file = '/usr/local/etc/raddb/clients.conf';
                $clientsConf .= $nasInfo;
                file_put_contents($file, $clientsConf);

                $this->Other_model->allowNasToManagers($record->id);

                //$this->stringReplaceFile('/usr/local/etc/raddb/clients.conf', $search, $replace);

            }
            $row_count++;
        }

        $this->restartRadiusServer();

    }

    function restartRadiusServer(){

        // ## sudo visudo  // Add To the end of File to Allow Permission
        // ## apache ALL=NOPASSWD: /usr/bin/systemctl restart radiusd ##//

        $result = shell_exec('sudo /usr/bin/systemctl restart radiusd');
        $restartRadius = shell_exec('systemctl status radiusd');

        $isActive = preg_match("/running/i", $restartRadius);

        if ($isActive >= 1) {
            echo "Radius reStarted";
        } else {
            echo "Command execution failed.";
        }

    }

    // JOBS QUEUE MANAGEMENT (ADMIN ONLY)
    public function jobsList() {
        if ($this->isAdmin() == FALSE) {
            $this->loadThis();
        } else {
            $data['jobs'] = $this->Other_model->jobsList();
            $this->global['pageTitle'] = 'Jobs Queue List';
            $this->loadViews('Other/jobs_list', $this->global, $data, NULL);
        }
    }

    public function jobsAdd() {
        if ($this->isAdmin() == FALSE) {
            $this->loadThis();
        } else {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('jobtype', 'Job Type', 'required');
            $this->form_validation->set_rules('jobname', 'Job Name', 'required|max_length[255]');
            $this->form_validation->set_rules('url', 'URL', 'required|valid_url');
            $this->form_validation->set_rules('jobstart', 'Job Start', 'required');
            $this->form_validation->set_rules('jobend', 'Job End', 'required');
            $this->form_validation->set_rules('jobinterval', 'Job Interval', 'required|integer');

            if ($this->form_validation->run() == FALSE) {
                $this->global['pageTitle'] = 'Add Job';
                $this->loadViews('Other/jobs_add', $this->global, NULL, NULL);
            } else {
                $data = array(
                    'jobtype' => $this->input->post('jobtype'),
                    'jobname' => $this->input->post('jobname'),
                    'url' => $this->input->post('url'),
                    'active' => $this->input->post('active') ? 1 : 0,
                    'jobstart' => $this->input->post('jobstart'),
                    'jobend' => $this->input->post('jobend'),
                    'jobinterval' => $this->input->post('jobinterval'),
                );
                $jobid = $this->Other_model->jobsAdd($data);

                if ($data['jobtype'] == 3) {
                    $this->cronjob_create($jobid, false); // false = no echo
                }
                $this->session->set_flashdata('success', 'Job added successfully');
                redirect('Other_controller/jobsList');
            }
        }
    }

    public function jobsEdit($jobid = NULL) {
        if ($this->isAdmin() == FALSE || $jobid == NULL) {
            $this->loadThis();
        } else {
            $job = $this->Other_model->jobsGet($jobid);
            if (empty($job)) {
                $this->session->set_flashdata('error', 'Job not found');
                redirect('Other_controller/jobsList');
            }
            $this->load->library('form_validation');
            $this->form_validation->set_rules('jobtype', 'Job Type', 'required');
            $this->form_validation->set_rules('jobname', 'Job Name', 'required|max_length[255]');
            $this->form_validation->set_rules('url', 'URL', 'required|valid_url');
            $this->form_validation->set_rules('jobstart', 'Job Start', 'required');
            $this->form_validation->set_rules('jobend', 'Job End', 'required');
            $this->form_validation->set_rules('jobinterval', 'Job Interval', 'required|integer');

            if ($this->form_validation->run() == FALSE) {
                $data['job'] = $job;
                $this->global['pageTitle'] = 'Edit Job';
                $this->loadViews('Other/jobs_edit', $this->global, $data, NULL);
            } else {
                $old_jobtype = $job->jobtype;
                $old_url = $job->url;
                $data = array(
                    'jobtype' => $this->input->post('jobtype'),
                    'jobname' => $this->input->post('jobname'),
                    'url' => $this->input->post('url'),
                    'active' => $this->input->post('active') ? 1 : 0,
                    'jobstart' => $this->input->post('jobstart'),
                    'jobend' => $this->input->post('jobend'),
                    'jobinterval' => $this->input->post('jobinterval'),
                );
                $this->Other_model->jobsEdit($jobid, $data);
                // Remove old cron if jobtype/url changed from/to 3
                if ($old_jobtype == 3) {
                    $this->cronjob_remove($jobid, $old_url, false); // false = no echo
                }
                if ($data['jobtype'] == 3) {
                    $this->cronjob_create($jobid, false); // false = no echo
                }
                $this->session->set_flashdata('success', 'Job updated successfully');
                redirect('Other_controller/jobsList');
            }
        }
    }

    public function jobsDelete($jobid = NULL) {
        if ($this->isAdmin() == FALSE || $jobid == NULL) {
            $this->loadThis();
        } else {
            $job = $this->Other_model->jobsGet($jobid);
            if ($job && $job->jobtype == 3) {
                $this->cronjob_remove($jobid, $job->url, false); // false = no echo
            }
            $this->Other_model->jobsDelete($jobid);
            $this->session->set_flashdata('success', 'Job deleted successfully');
            redirect('Other_controller/jobsList');
        }
    }

    // This should be called on login and dashboard load
    public function jobsRunQueue() {
        $jobs = $this->Other_model->jobsGetDueJobs();
        foreach ($jobs as $job) {
            // Execute the job's URL via cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $job->url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $response = curl_exec($ch);
            curl_close($ch);
            // Update jobend to NOW after execution
            $this->Other_model->jobsEdit($job->jobid, array('jobend' => date('Y-m-d')));
        }
    }

    // Create a cron job for jobtype=3
    public function cronjob_create($jobid = null, $echo = true)
    {
        $this->load->model('Other_model');
        $job = $this->Other_model->jobsGet($jobid);
        if (!$job || $job->jobtype != 3) {
            if ($echo) echo json_encode(['status' => 'error', 'message' => 'Invalid job or not a cron job']);
            return;
        }
        $php_path = PHP_BINDIR . '/php';
        $index_path = FCPATH . 'index.php';
        $hostname = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (getenv('APP_HOSTNAME') ?: 'localhost');
        $url = trim($job->url);
        if (!preg_match('#^[A-Za-z0-9_]+/[A-Za-z0-9_]+$#', $url)) {
            if ($echo) echo json_encode(['status' => 'error', 'message' => 'URL must be in Controller/Method format']);
            return;
        }
        $cli_command = str_replace('/', ' ', $url);
        $command = "APP_HOSTNAME=$hostname $php_path $index_path $cli_command";

        // Convert seconds to appropriate cron interval
        $cron_time = $this->secondsToCron($job->jobinterval);

        $cron_job = "$cron_time $command";
        // Read current crontab
        $output = [];
        exec('crontab -l', $output, $return_var);
        $crontab = implode("\n", $output);
        // Add the new cron job if not present
        if (strpos($crontab, $command) === false) {
            $newCrontab = $crontab . "\n" . $cron_job . "\n";
            $tmpFile = tempnam(sys_get_temp_dir(), 'cron');
            file_put_contents($tmpFile, $newCrontab);
            exec("crontab $tmpFile");
            unlink($tmpFile);
        }
        if ($echo) echo json_encode(['status' => 'success', 'cron' => $cron_job]);
    }

    // Helper function to convert seconds to cron format
    private function secondsToCron($seconds) {
        if ($seconds < 60) {
            // Less than 1 minute - use every minute
            return "*/1 * * * *";
        } elseif ($seconds < 3600) {
            // Less than 1 hour - convert to minutes
            $minutes = ceil($seconds / 60);
            return "*/$minutes * * * *";
        } elseif ($seconds < 86400) {
            // Less than 1 day - convert to hours
            $hours = ceil($seconds / 3600);
            return "0 */$hours * * *";
        } else {
            // 1 day or more - convert to days
            $days = ceil($seconds / 86400);
            return "0 0 */$days * *";
        }
    }

    // Remove a cron job for jobtype=3
    public function cronjob_remove($jobid = null, $url = null, $echo = true)
    {
        $this->load->model('Other_model');
        $job = $this->Other_model->jobsGet($jobid);
        if (!$job || $job->jobtype != 3) {
            if ($echo) echo json_encode(['status' => 'error', 'message' => 'Invalid job or not a cron job']);
            return;
        }
        $php_path = PHP_BINDIR . '/php';
        $index_path = FCPATH . 'index.php';
        $hostname = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (getenv('APP_HOSTNAME') ?: 'localhost');
        $url = trim($url ? $url : $job->url);
        if (!preg_match('#^[A-Za-z0-9_]+/[A-Za-z0-9_]+$#', $url)) {
            if ($echo) echo json_encode(['status' => 'error', 'message' => 'URL must be in Controller/Method format']);
            return;
        }
        $cli_command = str_replace('/', ' ', $url);
        $command = "APP_HOSTNAME=$hostname $php_path $index_path $cli_command";
        // Read current crontab
        $output = [];
        exec('crontab -l', $output, $return_var);
        $crontab = implode("\n", $output);
        // Remove the cron job line(s) containing the command
        $lines = explode("\n", $crontab);
        $newLines = array_filter($lines, function($line) use ($command) {
            return strpos($line, $command) === false;
        });
        $newCrontab = implode("\n", $newLines) . "\n";
        $tmpFile = tempnam(sys_get_temp_dir(), 'cron');
        file_put_contents($tmpFile, $newCrontab);
        exec("crontab $tmpFile");
        unlink($tmpFile);
        if ($echo) echo json_encode(['status' => 'success', 'removed' => $command]);
    }


/**
 * AJAX endpoint: push series cards to selected NAS.
 * POST: nas_id, series_id
 */
public function pushCardsToMikrotik()
{
    $this->output->set_content_type('application/json');
    try {
        $nasId = $this->input->post('nas_id', true);
        $seriesId = $this->input->post('series_id', true);

        log_message('debug', "pushCardsToMikrotik POST nas_id={$nasId} series_id={$seriesId}");

        if (empty($nasId) || empty($seriesId)) {
            $this->output->set_status_header(400);
            echo json_encode(['success'=>false,'message'=>'nas_id and series_id are required','posted'=>['nas_id'=>$nasId,'series_id'=>$seriesId],'csrf_hash'=>$this->security->get_csrf_hash()]);
            return;
        }

        // Lookup NAS info using model (handles numeric nas.id or legacy nasid)
        $nasInfo = $this->Other_model->getDynamicNasInfo($nasId);
        log_message('debug', 'pushCardsToMikrotik nasInfo: ' . json_encode($nasInfo));

        if (is_object($nasInfo)) {
            if (empty($nasInfo->apiuser) && !empty($nasInfo->apiusername)) $nasInfo->apiuser = $nasInfo->apiusername;
            if (empty($nasInfo->apipasswd) && !empty($nasInfo->apipassword)) $nasInfo->apipasswd = $nasInfo->apipassword;
            if (empty($nasInfo->apiport) && !empty($nasInfo->ports)) $nasInfo->apiport = $nasInfo->ports;
        }

        if (empty($nasInfo)) {
            $nasRow = null;
            if (ctype_digit((string)$nasId)) {
                $nasRow = $this->db->get_where('nas', ['id' => (int)$nasId])->row();
            }
            if (!empty($nasRow)) {
                $nasInfo = new stdClass();
                $nasInfo->shortname = isset($nasRow->shortname) ? $nasRow->shortname : '';
                $nasInfo->nasname   = isset($nasRow->nasname) ? $nasRow->nasname : '';
                $dyn = $this->db->get_where('tbl_dynamicnas', ['nasname' => $nasInfo->nasname])->row();
                if (!empty($dyn)) {
                    $nasInfo->domain    = isset($dyn->domain) ? $dyn->domain : $nasInfo->nasname;
                    $nasInfo->apiuser   = isset($dyn->apiuser) ? $dyn->apiuser : (isset($dyn->apiusername) ? $dyn->apiusername : '');
                    $nasInfo->apipasswd = isset($dyn->apipasswd) ? $dyn->apipasswd : (isset($dyn->apipassword) ? $dyn->apipassword : '');
                    $nasInfo->apiport   = isset($dyn->apiport) ? $dyn->apiport : (isset($dyn->ports) ? $dyn->ports : null);
                } else {
                    $nasInfo->domain    = $nasInfo->nasname;
                    $nasInfo->apiuser   = isset($nasRow->apiusername) ? $nasRow->apiusername : (isset($nasRow->apiuser) ? $nasRow->apiuser : '');
                    $nasInfo->apipasswd = isset($nasRow->apipassword) ? $nasRow->apipassword : (isset($nasRow->apipasswd) ? $nasRow->apipasswd : '');
                    $nasInfo->apiport   = isset($nasRow->ports) ? $nasRow->ports : (isset($nasRow->apiport) ? $nasRow->apiport : null);
                }
            } else {
                $this->output->set_status_header(404);
                echo json_encode(['success'=>false,'message'=>'NAS not found','csrf_hash'=>$this->security->get_csrf_hash()]);
                return;
            }
        }

        $apiUser = isset($nasInfo->apiuser) ? $nasInfo->apiuser : (isset($nasInfo->apiusername) ? $nasInfo->apiusername : '');
        $apiPass = isset($nasInfo->apipasswd) ? $nasInfo->apipasswd : (isset($nasInfo->apipassword) ? $nasInfo->apipassword : '');
        $apiPort = isset($nasInfo->apiport) ? $nasInfo->apiport : (isset($nasInfo->ports) ? $nasInfo->ports : null);

        // Optional override for troubleshooting and controlled push calls.
        $ovUser = $this->input->post('api_user', true);
        if ($ovUser === null || $ovUser === '') $ovUser = $this->input->get('api_user', true);
        $ovPass = $this->input->post('api_pass', true);
        if ($ovPass === null || $ovPass === '') $ovPass = $this->input->get('api_pass', true);
        $ovPort = $this->input->post('api_port', true);
        if ($ovPort === null || $ovPort === '') $ovPort = $this->input->get('api_port', true);

        if ($ovUser !== null && $ovUser !== '' && $ovPass !== null && $ovPass !== '') {
            $apiUser = trim((string)$ovUser);
            $apiPass = (string)$ovPass;
            if ($ovPort !== null && $ovPort !== '' && (int)$ovPort > 0) $apiPort = (int)$ovPort;
            $nasInfo->apiuser = $apiUser;
            $nasInfo->apipasswd = $apiPass;
            if (!empty($apiPort)) $nasInfo->apiport = $apiPort;
            log_message('info', 'pushCardsToMikrotik: using manual API override user=' . $apiUser . ' port=' . (int)$apiPort);
        }

        if (empty($apiUser) || empty($apiPass)) {
            $this->output->set_status_header(422);
            echo json_encode(['success'=>false,'message'=>'NAS API credentials missing','nas'=>$nasInfo,'csrf_hash'=>$this->security->get_csrf_hash()]);
            return;
        }

        // Fetch cards for series
        $cards = $this->users_model->cardsListing('', $seriesId);
        if (empty($cards)) {
            $this->output->set_status_header(404);
            echo json_encode(['success'=>false,'message'=>'No cards found for series','posted'=>['series_id'=>$seriesId],'csrf_hash'=>$this->security->get_csrf_hash()]);
            return;
        }

        // --- attach NAS id and readable label for each card (always) ---
        $cardnums = [];
        foreach ($cards as $c) {
            $cn = isset($c->cardnum) ? $c->cardnum : (isset($c->username) ? $c->username : null);
            if ($cn) $cardnums[] = $cn;
        }
        $cardnums = array_values(array_unique($cardnums));

        $cardStates = [];
        if (!empty($cardnums) && method_exists($this->Other_model, 'getCardPushStates')) {
            $cardStates = $this->Other_model->getCardPushStates($cardnums);
        }
        $data['cardStates'] = $cardStates;

        // Merge into $cards so views and subsequent logic can use $card->nas_id and $card->nas_label
        foreach ($cards as &$c) {
            $cn = isset($c->cardnum) ? $c->cardnum : (isset($c->username) ? $c->username : null);

            // default values
            $c->nas_id = null;
            $c->nas_label = null;

            // Prefer cardStates resolved above (returned by getCardPushStates)
            if ($cn && isset($cardStates[$cn])) {
                $nid = isset($cardStates[$cn]['nas_id']) ? (int)$cardStates[$cn]['nas_id'] : null;
                if ($nid > 0) {
                    $c->nas_id = $nid;
                    $c->nas_label = isset($cardStates[$cn]['nas_label']) ? $cardStates[$cn]['nas_label'] : null;
                }
                continue;
            }

            // Fallback: accept only positive integers from record fields
            $maybe = null;
            if (isset($c->nasid) && is_numeric($c->nasid) && (int)$c->nasid > 0) {
                $maybe = (int)$c->nasid;
            } elseif (isset($c->nas_id) && is_numeric($c->nas_id) && (int)$c->nas_id > 0) {
                $maybe = (int)$c->nas_id;
            }

            if ($maybe !== null) {
                $c->nas_id = $maybe;
            }
        }
        unset($c);
        // --- end attach ---

        // --- BEGIN: nasid pre-check (use nasid integer column) ---
        $requestedNasId = (int)$nasId;
        $assignedNas = null; // null = unassigned, int = assigned to that NAS, 'mixed' = conflicting assignments
        foreach ($cards as $c) {
            $nass = null;
            if (isset($c->nasid)) {
                $nass = (int)$c->nasid;
                if ($nass === 0) $nass = null;
            } elseif (isset($c->nas_id)) {
                $nass = (int)$c->nas_id;
                if ($nass === 0) $nass = null;
            }

            if ($nass === null) continue;

            if ($assignedNas === null) {
                $assignedNas = $nass;
            } elseif ($assignedNas !== $nass) {
                $assignedNas = 'mixed';
                break;
            }
        }
        // --- END: nasid pre-check ---

        $force = (int)$this->input->post('force', true);
        $isAdmin = ($this->session && $this->session->userdata('is_admin')) ? true : false;

        if ($assignedNas === 'mixed') {
            $this->output->set_status_header(409);
            echo json_encode(['success'=>false,'message'=>'Series has conflicting NAS assignments; contact admin to resolve.','csrf_hash'=>$this->security->get_csrf_hash()]);
            return;
        }

        if ($assignedNas !== null && $assignedNas !== $requestedNasId && !($force && $isAdmin)) {
            $nasRow = $this->db->get_where('nas', ['id'=>$assignedNas])->row();
            $nasLabel = $nasRow ? ($nasRow->shortname . ' (' . $nasRow->nasname . ')') : 'NAS '.$assignedNas;
            $this->output->set_status_header(409);
            echo json_encode(['success'=>false,'message'=>"Series already pushed to {$nasLabel}", 'assigned_nas' => $assignedNas, 'csrf_hash'=>$this->security->get_csrf_hash()]);
            return;
        }

        $maxPerReq = 1000;
        if (count($cards) > $maxPerReq) $cards = array_slice($cards, 0, $maxPerReq);

        $pushed = 0;
        $skippedAlreadyAssigned = 0;
        $errors = [];

        // define host for logging and sendToMikrotik calls
        $host = isset($nasInfo->domain) ? $nasInfo->domain : (isset($nasInfo->nasname) ? $nasInfo->nasname : null);

        // Use the existing sendToMikrotik($nasInfo, $payload) directly
        $APIerr = null;
        if (empty($nasInfo) || empty($apiUser) || empty($apiPass)) {
            $APIerr = 'NAS connection info or API credentials missing';
        }

        if ($APIerr !== null) {
            foreach ($cards as $card) {
                $cardName = isset($card->cardnum) ? $card->cardnum : (isset($card->username) ? $card->username : 'unknown');
                $errors[] = $cardName . ': ' . $APIerr;
            }
        } else {
            foreach ($cards as $card) {
                $username = isset($card->cardnum) ? $card->cardnum : (isset($card->username) ? $card->username : '');
                $password = isset($card->password) ? $card->password : '';
                if (empty($username)) { $errors[] = 'Missing username in card record'; continue; }

                // Skip cards already pushed to this same NAS (idempotent) using nasid
                $nassCurrent = null;
                if (isset($card->nasid)) {
                    $nassCurrent = (int)$card->nasid;
                } elseif (isset($card->nas_id)) {
                    $nassCurrent = (int)$card->nas_id;
                }
                if (!empty($nassCurrent) && $nassCurrent === $requestedNasId) {
                    $skippedAlreadyAssigned++;
                    continue;
                }

                $payload = [
                    'username'   => $username,
                    'password'   => $password,
                    'start_date' => isset($card->date) ? $card->date : date('Y-m-d'),
                    'expire_date'=> isset($card->expiration) ? $card->expiration : null
                ];

                log_message('debug', "pushCardsToMikrotik: pushing card {$username} to NAS {$requestedNasId} (host={$host})");
                try {
                    $res = $this->sendToMikrotik($nasInfo, $payload);
                } catch (Throwable $e) {
                    $res = 'exception: ' . $e->getMessage();
                }

                if ($res === true) {
                    // Mark card as pushed by setting nasid = <requestedNasId>
                    if (method_exists($this->Other_model, 'markCardPushedToNasIfUnassigned')) {
                        $markResult = $this->Other_model->markCardPushedToNasIfUnassigned($payload['username'], $requestedNasId);
                        if ($markResult === 1) {
                            $pushed++;
                        } elseif ($markResult === 0) {
                            $errors[] = $payload['username'] . ': conflict - already assigned to another NAS';
                            log_message('warning', 'pushCardsToMikrotik: card ' . $payload['username'] . ' assignment conflict during markCardPushedToNasIfUnassigned');
                        } else {
                            $markWrite = $this->updateRmCardNasAssignment($payload['username'], $requestedNasId);
                            if ($markWrite === 1) $pushed++;
                            elseif ($markWrite === 0) $errors[] = $payload['username'] . ': mark skipped (nas column missing)';
                            else $errors[] = $payload['username'] . ': mark failed (fallback)';
                        }
                    } elseif (method_exists($this->Other_model, 'markCardPushedToNas')) {
                        $ok = $this->Other_model->markCardPushedToNas($payload['username'], $requestedNasId);
                        if ($ok) $pushed++;
                        else {
                            $markWrite = $this->updateRmCardNasAssignment($payload['username'], $requestedNasId);
                            if ($markWrite === 1) $pushed++;
                            elseif ($markWrite === 0) $errors[] = $payload['username'] . ': mark skipped (nas column missing)';
                            else $errors[] = $payload['username'] . ': mark failed';
                        }
                    } else {
                        $markWrite = $this->updateRmCardNasAssignment($payload['username'], $requestedNasId);
                        if ($markWrite === 1) $pushed++;
                        elseif ($markWrite === 0) $errors[] = $payload['username'] . ': mark skipped (nas column missing)';
                        else $errors[] = $payload['username'] . ': mark failed';
                    }

                    log_message('info', "pushCardsToMikrotik: success {$username} on NAS {$requestedNasId}");
                } else {
                    log_message('error', "pushCardsToMikrotik: failed {$username} -> {$res}");
                    $errors[] = $payload['username'] . ': ' . $res;
                }
            }
        }

        $message = "$pushed cards pushed";
        if ($pushed === 0 && $skippedAlreadyAssigned > 0 && empty($errors)) {
            $message = 'All cards are already assigned/pushed to selected NAS';
        }
        $response = [
            'success'=>true,
            'message'=>$message,
            'pushed'=>$pushed,
            'skipped_already_assigned'=>$skippedAlreadyAssigned,
            'using_api_user'=>$apiUser,
            'using_api_port'=>$apiPort,
            'errors'=>$errors,
            'csrf_hash'=>$this->security->get_csrf_hash()
        ];
        if (!empty($data['cardStates'])) $response['cardStates'] = $data['cardStates'];

        echo json_encode($response);
    } catch (Throwable $e) {
        log_message('error', 'pushCardsToMikrotik exception: '.$e->getMessage().' in '.$e->getFile().':'.$e->getLine());
        $this->output->set_status_header(500);
        echo json_encode(['success'=>false,'message'=>'Internal server error','error'=>substr($e->getMessage(),0,300),'csrf_hash'=>$this->security->get_csrf_hash()]);
    }
}




/**
 * Try RouterOS PHP API, else fall back to HTTP endpoint.
 * Returns true on success or string error on failure.
 */
/**
 * Try RouterOS PHP API, else fall back to HTTP endpoint.
 * Returns true on success or string error on failure.
 */
/**
 * Try RouterOS PHP API, else fall back to HTTP endpoint.
 * Returns true on success or string error on failure.
 */
private function sendToMikrotik($nasInfo, $payload)
{
    // Resolve host and API credentials using aliases
    $host = !empty($nasInfo->domain) ? $nasInfo->domain : (isset($nasInfo->nasname) ? $nasInfo->nasname : null);
    $apiUser = isset($nasInfo->apiuser) ? $nasInfo->apiuser : (isset($nasInfo->apiusername) ? $nasInfo->apiusername : '');
    $apiPass = isset($nasInfo->apipasswd) ? $nasInfo->apipasswd : (isset($nasInfo->apipassword) ? $nasInfo->apipassword : '');
    $apiPort = isset($nasInfo->apiport) && $nasInfo->apiport !== '' ? intval($nasInfo->apiport) : (isset($nasInfo->ports) && $nasInfo->ports !== '' ? intval($nasInfo->ports) : 8728);

    if (empty($host) || empty($apiUser) || empty($apiPass)) return 'NAS host or API user/password missing';

    // Resolve hostname to IP early and log
    $resolved = @gethostbyname($host);
    log_message('debug', "sendToMikrotik: host={$host} resolved={$resolved} port={$apiPort} user={$apiUser}");

    if ($resolved === $host) {
        if (!filter_var($host, FILTER_VALIDATE_IP)) {
            log_message('error', "sendToMikrotik: DNS resolution failed for {$host}");
            return "DNS resolution failed for {$host}";
        }
    }

    // Normalize payload values
    $username = isset($payload['username']) ? $payload['username'] : '';
    $password = isset($payload['password']) ? $payload['password'] : '';
    $startDate = isset($payload['start_date']) ? $payload['start_date'] : null;
    $expireDate = isset($payload['expire_date']) ? $payload['expire_date'] : null;

    if (empty($username)) return 'username missing in payload';
    if ($password === null) $password = '';

    // Compute comment and optional limit-uptime
    $comment = '';
    $limitUptime = null;

    if (!empty($expireDate)) {
        $tsExpire = @strtotime($expireDate);
        if ($tsExpire !== false && $tsExpire !== -1) {
            $comment = 'Expires: ' . date('Y-m-d', $tsExpire);

            $tsStart = null;
            if (!empty($startDate)) $tsStart = @strtotime($startDate);
            if ($tsStart === false) $tsStart = null;
            if ($tsStart === null) $tsStart = time();

            $diffSeconds = $tsExpire - $tsStart;
            if ($diffSeconds > 0) {
                if ($diffSeconds % 86400 === 0) {
                    $days = (int)($diffSeconds / 86400);
                    $limitUptime = $days . 'd';
                } else {
                    $hours = (int)ceil($diffSeconds / 3600);
                    $limitUptime = $hours . 'h';
                }
            } else {
                $limitUptime = null;
            }
        }
    }

    // Prepare RouterOS params
    $params = [
        'name'     => $username,
        'password' => $password,
    ];
    if ($comment !== '') $params['comment'] = $comment;
    if (!empty($limitUptime)) $params['limit-uptime'] = $limitUptime;

    // --- Attempt: RouterosAPI (3 retries) ---
    $routerosLib = APPPATH . 'libraries/routeros_api.class.php';
    if (file_exists($routerosLib)) require_once $routerosLib;
    else log_message('error', 'sendToMikrotik: missing RouterOS library: ' . $routerosLib);

    if (class_exists('RouterosAPI')) {
        $lastErr = null;
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            try {
                $API = new RouterosAPI();
                $API->debug = false;
                if (property_exists($API, 'timeout')) $API->timeout = 10;

                $connectHost = $resolved ?: $host;
                log_message('debug', "sendToMikrotik: RouterosAPI connect attempt {$attempt} to {$connectHost}:{$apiPort}");

                if ($API->connect($connectHost, $apiUser, $apiPass, $apiPort)) {
                    // send command
                    $res = $API->comm('/ip/hotspot/user/add', $params);
                    $API->disconnect();

                    if (is_array($res)) {
                        log_message('debug', 'sendToMikrotik: RouterosAPI add user success for '.$username);
                        return true;
                    } else {
                        $lastErr = 'RouterOS API returned unexpected result: ' . json_encode($res);
                        log_message('error', 'sendToMikrotik: RouterosAPI unexpected result: ' . json_encode($res));
                    }
                } else {
                    $lastErr = 'RouterOS API connect failed';
                    log_message('error', 'sendToMikrotik: RouterosAPI connection failed attempt ' . $attempt . ' to ' . $connectHost);
                }
            } catch (Throwable $e) {
                $lastErr = 'RouterosAPI exception: ' . $e->getMessage();
                log_message('error', 'sendToMikrotik: RouterosAPI exception: ' . $e->getMessage());
            }
            sleep(1);
        }
        log_message('error', "sendToMikrotik: RouterosAPI failed after retries: {$lastErr}");
    } else {
        log_message('error', 'sendToMikrotik: RouterosAPI class not available');
    }

    // --- HTTP fallback (only if remote device provides such endpoint) ---
    $ip = $resolved && $resolved !== $host ? $resolved : $host;
    $url = "http://{$ip}:{$apiPort}/api/add_user";
    $postData = [
        'username'    => $username,
        'password'    => $password,
        'start_date'  => $startDate,
        'expire_date' => $expireDate
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    if (!empty($apiUser)) curl_setopt($ch, CURLOPT_USERPWD, "{$apiUser}:{$apiPass}");
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    curl_close($ch);

    if ($curlErr) {
        log_message('error', "sendToMikrotik: HTTP fallback cURL error: {$curlErr} for {$url}");
        return 'cURL error: ' . $curlErr;
    }
    if ($httpCode >= 200 && $httpCode < 300) {
        log_message('debug', "sendToMikrotik: HTTP fallback success HTTP={$httpCode} url={$url}");
        return true;
    }

    log_message('error', "sendToMikrotik: HTTP fallback failed HTTP={$httpCode} response=" . substr($result ?: '', 0, 300));
    return 'HTTP ' . $httpCode . ' - ' . substr($result ?: '', 0, 300);
}
public function webhook_tester()
{
    // Collect incoming POST (no XSS filtering here so we forward raw values; sanitize if you store them)
    $post = $this->input->post(NULL, FALSE);
    if (!is_array($post)) { $post = []; }

    // Add a forward marker so webhook can detect forwarded requests and avoid loops
    $post['__forwarded_by'] = 'webhook_tester';

    // Target where the view will re-post the data (final handler that issues the Location header)
    $data['target'] = site_url('other_controller/mikrotik_webhook');

    // Pass the raw POST array to the view
    $data['post'] = $post;

    // Load the view that will rebuild the form and auto-submit to $data['target']
    $this->load->view('hotspot/webhookhotspotlogin', $data);
}

/**
 * mikrotik_echo
 *
 * - Accepts JSON body, POST form, or GET params from MikroTik
 * - Extracts username, ip, mac, start_date, expire_date, session_time/uptime
 * - Computes session duration when possible
 * - Returns a JSON response (no DB writes)
 *
 * Compatible with older PHP (no null-coalescing, no Throwable)
 */
/**
 * Safe logging wrapper to avoid CI Log undefined index notices.
 * Use $this->safe_log($level, $message) instead of log_message().
 */
public function mikrotik_webhook()
{
    // --- read incoming data (raw JSON preferred) ---
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);

    // POST fallback (form)
    $post = $this->input->post(NULL, TRUE);
    if (!is_array($post)) $post = array();

    // Merge: JSON > POST > GET
    if (is_array($json) && count($json)) {
        $data = $json;
    } elseif (!empty($post)) {
        $data = $post;
    } else {
        // Accept common GET params too (router redirect)
        $data = array(
            'username'   => $this->input->get('username', TRUE),
            'mac'        => $this->input->get('mac', TRUE),
            'ip'         => $this->input->get('ip', TRUE),
            'link_login' => $this->input->get('link-login', TRUE) ?: $this->input->get('link-orig', TRUE)
        );
    }

    // --- normalize fields ---
    $username = isset($data['username']) ? trim($data['username']) : '';
    $client_mac = isset($data['mac']) ? trim($data['mac']) : '';
    $client_ip = isset($data['ip']) ? trim($data['ip']) : '';
    $link = isset($data['link_login']) ? trim($data['link_login']) : (isset($data['link']) ? trim($data['link']) : '');

    // --- build response object (what we will return) ---
    $response = array(
        'status'      => 'ok',
        'username'    => $username,
        'client_ip'   => $client_ip,
        'client_mac'  => $client_mac,
        'link'        => $link,
        'received_raw'=> substr($raw, 0, 2000)
    );
    

    // --- persist event to file (JSON Lines) ---
    $logDir = APPPATH . '../webhook_logs'; // adjust if needed
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0750, true);
    }
    $logFile = $logDir . '/mikrotik_events.jsonl';

    $record = array(
        'ts'         => date('c'),
        'router_ip'  => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null,
        'username'   => $username,
        'client_ip'  => $client_ip,
        'client_mac' => $client_mac,
        'link'       => $link,
        'raw'        => substr($raw, 0, 2000)
    );

    $line = json_encode($record, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;
    $fp = @fopen($logFile, 'a');
    if ($fp) {
        @flock($fp, LOCK_EX);
        @fwrite($fp, $line);
        @fflush($fp);
        @flock($fp, LOCK_UN);
        @fclose($fp);
        // include stored filename in response for router logging if desired
        $response['stored_file'] = basename($logFile);
    } else {
        log_message('error', 'mikrotik_webhook: cannot open log file ' . $logFile);
        $response['stored_file'] = null;
    }

    // --- RouterOS detection: treat RouterOS fetch as API client ---
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
    $is_routeros = (strpos($ua, 'mikrotik') !== false) || $this->input->get('force_json', TRUE);

    // If this is a browser POST and link exists, redirect back to router (captive flow)
    if (!$is_routeros && $this->input->method() === 'post' && !empty($link)) {
        $this->output
             ->set_status_header(302)
             ->set_header('Location: ' . $link)
             ->set_output('');
        return;
    }

    // Otherwise return JSON (RouterOS fetch or API clients)
    $this->output
         ->set_status_header(200)
         ->set_content_type('application/json')
         ->set_output(json_encode($response));
}

/**
 * Recover/sync voucher-like hotspot users from MikroTik into rm_cards.
 *
 * Safe defaults for live systems:
 * - dry_run=1 by default (no DB writes)
 * - admin-only on web requests, allowed from CLI cron
 *
 * Params (GET/POST/CLI):
 * - nas_id (required): NAS id from nas table
 * - dry_run: 1|0 (default 1)
 * - limit: max users to process (default 500)
 * - owner: rm_cards owner for newly imported cards (default admin or current user)
 * - srvid: service id for newly imported cards (default 0)
 * - mode: voucher_like|all (default voucher_like)
 * - name_prefix: optional username prefix filter
 */
public function sync_mikrotik_vouchers()
{
    $this->output->set_content_type('application/json');

    if (!$this->isSyncAuthorized()) {
        $this->output->set_status_header(403);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }

    try {
        $nasId = $this->input->post('nas_id', true);
        if (empty($nasId)) $nasId = $this->input->get('nas_id', true);
        // CLI support: php index.php other_controller sync_mikrotik_vouchers <nas_id> [dry_run] [limit]
        if (empty($nasId) && is_cli()) {
            $nasId = $this->uri->segment(3);
            if (empty($nasId)) $nasId = $this->input->get('nas_id', true);
        }

        $dryRun = $this->input->post('dry_run', true);
        if ($dryRun === null || $dryRun === '') $dryRun = $this->input->get('dry_run', true);
        if (($dryRun === null || $dryRun === '') && is_cli()) {
            $dryRun = $this->uri->segment(4);
        }
        if ($dryRun === null || $dryRun === '') {
            // Keep web requests safe by default; CLI cron defaults to write mode.
            $dryRun = is_cli() ? 0 : 1;
        }
        $dryRun = ((int)$dryRun === 1) ? 1 : 0;

        $limit = $this->input->post('limit', true);
        if ($limit === null || $limit === '') $limit = $this->input->get('limit', true);
        if (($limit === null || $limit === '') && is_cli()) {
            $limit = $this->uri->segment(5);
        }
        $limit = (int)$limit;
        if ($limit <= 0) $limit = 500;
        if ($limit > 3000) $limit = 3000;

        $mode = strtolower((string)$this->input->post('mode', true));
        if ($mode === '') $mode = strtolower((string)$this->input->get('mode', true));
        if ($mode !== 'all') $mode = 'voucher_like';

        $namePrefix = trim((string)$this->input->post('name_prefix', true));
        if ($namePrefix === '') $namePrefix = trim((string)$this->input->get('name_prefix', true));

        $owner = trim((string)$this->input->post('owner', true));
        if ($owner === '') $owner = trim((string)$this->input->get('owner', true));
        if ($owner === '') {
            $owner = $this->session ? (string)$this->session->userdata('name') : '';
            if ($owner === '') $owner = 'admin';
        }

        $srvid = $this->input->post('srvid', true);
        if ($srvid === null || $srvid === '') $srvid = $this->input->get('srvid', true);
        $srvid = (int)$srvid;
        if ($srvid < 0) $srvid = 0;

        if (empty($nasId)) {
            $this->output->set_status_header(400);
            echo json_encode(['success' => false, 'message' => 'nas_id is required']);
            return;
        }

        $nasInfo = $this->resolveNasInfoForSync($nasId);
        if (empty($nasInfo)) {
            $this->output->set_status_header(404);
            echo json_encode(['success' => false, 'message' => 'NAS not found']);
            return;
        }

        $host = !empty($nasInfo->domain) ? $nasInfo->domain : (isset($nasInfo->nasname) ? $nasInfo->nasname : '');
        $apiUser = isset($nasInfo->apiuser) ? $nasInfo->apiuser : (isset($nasInfo->apiusername) ? $nasInfo->apiusername : '');
        $apiPass = isset($nasInfo->apipasswd) ? $nasInfo->apipasswd : (isset($nasInfo->apipassword) ? $nasInfo->apipassword : '');
        $apiPort = isset($nasInfo->apiport) && $nasInfo->apiport !== '' ? (int)$nasInfo->apiport : (isset($nasInfo->ports) ? (int)$nasInfo->ports : 8728);

        $apiUser = trim((string)$apiUser);
        $apiPass = trim((string)$apiPass);

        // Optional manual override for quick live diagnostics from URL.
        $overrideUser = $this->input->post('api_user', true);
        if ($overrideUser === null || $overrideUser === '') $overrideUser = $this->input->get('api_user', true);
        $overridePass = $this->input->post('api_pass', true);
        if ($overridePass === null || $overridePass === '') $overridePass = $this->input->get('api_pass', true);
        $overridePort = $this->input->post('api_port', true);
        if ($overridePort === null || $overridePort === '') $overridePort = $this->input->get('api_port', true);

        if ($host === '') {
            $this->output->set_status_header(422);
            echo json_encode(['success' => false, 'message' => 'NAS host/API credentials missing']);
            return;
        }

        $routerosLib = APPPATH . 'libraries/routeros_api.class.php';
        if (!file_exists($routerosLib)) {
            $this->output->set_status_header(500);
            echo json_encode(['success' => false, 'message' => 'RouterOS API library missing']);
            return;
        }
        require_once $routerosLib;

        if (!class_exists('RouterosAPI')) {
            $this->output->set_status_header(500);
            echo json_encode(['success' => false, 'message' => 'RouterosAPI class unavailable']);
            return;
        }

        // Build credential candidates from resolved NAS info + dynamicnas + nas tables.
        $credCandidates = [];
        $credSeen = [];
        $pushCred = function($u, $p, $port, $source) use (&$credCandidates, &$credSeen) {
            $u = trim((string)$u);
            $p = (string)$p;
            $port = (int)$port;
            if ($u === '' || $p === '') return;
            if ($port <= 0) $port = 8728;
            $k = md5($u . '|' . $p . '|' . $port);
            if (isset($credSeen[$k])) return;
            $credSeen[$k] = true;
            $credCandidates[] = [
                'user' => $u,
                'pass' => $p,
                'port' => $port,
                'source' => $source,
            ];
        };

        $pushCred($apiUser, $apiPass, $apiPort, 'resolved_nasinfo');
        if ($overrideUser !== null && $overrideUser !== '' && $overridePass !== null && $overridePass !== '') {
            $ovPort = (int)$overridePort;
            if ($ovPort <= 0) $ovPort = $apiPort > 0 ? $apiPort : 8728;
            $pushCred($overrideUser, $overridePass, $ovPort, 'manual_override');
        }

        $nasNameForCreds = isset($nasInfo->nasname) ? trim((string)$nasInfo->nasname) : '';
        if ($nasNameForCreds !== '') {
            $dynRows = $this->db->get_where('tbl_dynamicnas', ['nasname' => $nasNameForCreds])->result();
            foreach ($dynRows as $dr) {
                $u = isset($dr->apiuser) ? $dr->apiuser : (isset($dr->apiusername) ? $dr->apiusername : '');
                $p = isset($dr->apipasswd) ? $dr->apipasswd : (isset($dr->apipassword) ? $dr->apipassword : '');
                $pt = isset($dr->apiport) ? (int)$dr->apiport : (isset($dr->ports) ? (int)$dr->ports : $apiPort);
                $pushCred($u, $p, $pt, 'tbl_dynamicnas:nasname');
            }
        }

        $dynRowsByDomain = $this->db->get_where('tbl_dynamicnas', ['domain' => $host])->result();
        foreach ($dynRowsByDomain as $dr) {
            $u = isset($dr->apiuser) ? $dr->apiuser : (isset($dr->apiusername) ? $dr->apiusername : '');
            $p = isset($dr->apipasswd) ? $dr->apipasswd : (isset($dr->apipassword) ? $dr->apipassword : '');
            $pt = isset($dr->apiport) ? (int)$dr->apiport : (isset($dr->ports) ? (int)$dr->ports : $apiPort);
            $pushCred($u, $p, $pt, 'tbl_dynamicnas:domain');
        }

        $nasRows = [];
        if (ctype_digit((string)$nasId)) {
            $nr = $this->db->get_where('nas', ['id' => (int)$nasId])->row();
            if (!empty($nr)) $nasRows[] = $nr;
        }
        if (empty($nasRows) && $nasNameForCreds !== '') {
            $nr = $this->db->get_where('nas', ['nasname' => $nasNameForCreds])->row();
            if (!empty($nr)) $nasRows[] = $nr;
        }
        if (empty($nasRows)) {
            $nr = $this->db->get_where('nas', ['nasname' => $host])->row();
            if (!empty($nr)) $nasRows[] = $nr;
        }
        foreach ($nasRows as $nr) {
            $u = isset($nr->apiusername) ? $nr->apiusername : (isset($nr->apiuser) ? $nr->apiuser : '');
            $p = isset($nr->apipassword) ? $nr->apipassword : (isset($nr->apipasswd) ? $nr->apipasswd : '');
            $pt = isset($nr->ports) ? (int)$nr->ports : (isset($nr->apiport) ? (int)$nr->apiport : $apiPort);
            $pushCred($u, $p, $pt, 'nas');
        }

        if (empty($credCandidates)) {
            $this->output->set_status_header(422);
            echo json_encode(['success' => false, 'message' => 'No API credentials found for selected NAS']);
            return;
        }

        $nasNameHost = isset($nasInfo->nasname) ? trim((string)$nasInfo->nasname) : '';
        $hostCandidates = [];
        if ($host !== '') $hostCandidates[] = $host;
        if ($nasNameHost !== '' && !in_array($nasNameHost, $hostCandidates, true)) $hostCandidates[] = $nasNameHost;

        $portCandidates = [];
        if ($apiPort > 0) $portCandidates[] = (int)$apiPort;
        foreach ($credCandidates as $cc) {
            if (!empty($cc['port']) && !in_array((int)$cc['port'], $portCandidates, true)) {
                $portCandidates[] = (int)$cc['port'];
            }
        }
        if (!in_array(8728, $portCandidates, true)) $portCandidates[] = 8728;
        if (!in_array(8729, $portCandidates, true)) $portCandidates[] = 8729;

        $connected = false;
        $connectDiag = [];
        $connectedHost = '';
        $connectedPort = 0;
        $connectedSsl = null;
        $connectedCredSource = '';
        $connectedApiUser = '';

        foreach ($hostCandidates as $hostTry) {
            $resolvedHost = @gethostbyname($hostTry);
            $connectHost = $resolvedHost;
            if ($connectHost === $hostTry && !filter_var($hostTry, FILTER_VALIDATE_IP)) {
                $connectHost = $hostTry;
            }

            foreach ($portCandidates as $portTry) {
                $tcpOk = false;
                $tcpErrNo = 0;
                $tcpErrStr = '';
                $sock = @fsockopen($connectHost, (int)$portTry, $tcpErrNo, $tcpErrStr, 3);
                if (is_resource($sock)) {
                    $tcpOk = true;
                    fclose($sock);
                }

                $sslModes = [null];
                if ((int)$portTry === 8729) {
                    $sslModes = [true, false, null];
                }

                foreach ($sslModes as $sslMode) {
                    foreach ($credCandidates as $cred) {
                        $API = new RouterosAPI();
                        $API->debug = false;
                        if (property_exists($API, 'timeout')) $API->timeout = 12;
                        if ($sslMode !== null && property_exists($API, 'ssl')) {
                            $API->ssl = (bool)$sslMode;
                        }

                        $ok = false;
                        try {
                            $ok = $API->connect($connectHost, $cred['user'], $cred['pass'], (int)$portTry);
                        } catch (Throwable $e) {
                            $connectDiag[] = [
                                'host_input' => $hostTry,
                                'host_connect' => $connectHost,
                                'resolved' => $resolvedHost,
                                'port' => (int)$portTry,
                                'ssl' => $sslMode,
                                'cred_source' => $cred['source'],
                                'cred_user' => $cred['user'],
                                'pass_len' => strlen((string)$cred['pass']),
                                'tcp' => $tcpOk ? 'open' : ('closed: ' . $tcpErrNo . ' ' . $tcpErrStr),
                                'api' => 'exception: ' . $e->getMessage(),
                            ];
                            continue;
                        }

                        if ($ok) {
                            $connected = true;
                            $connectedHost = $connectHost;
                            $connectedPort = (int)$portTry;
                            $connectedSsl = $sslMode;
                            $connectedCredSource = $cred['source'];
                            $connectedApiUser = $cred['user'];
                            break 4;
                        }

                        $connectDiag[] = [
                            'host_input' => $hostTry,
                            'host_connect' => $connectHost,
                            'resolved' => $resolvedHost,
                            'port' => (int)$portTry,
                            'ssl' => $sslMode,
                            'cred_source' => $cred['source'],
                            'cred_user' => $cred['user'],
                            'pass_len' => strlen((string)$cred['pass']),
                            'tcp' => $tcpOk ? 'open' : ('closed: ' . $tcpErrNo . ' ' . $tcpErrStr),
                            'api' => 'connect_failed',
                        ];
                    }
                }
            }
        }

        if (!$connected) {
            $this->output->set_status_header(502);
            echo json_encode([
                'success' => false,
                'message' => 'MikroTik connection failed',
                'hints' => [
                    'Check API service enabled on MikroTik (/ip service print).',
                    'Confirm API port (8728) or API-SSL (8729).',
                    'Allow panel server IP in firewall/input rules.',
                    'Verify API username/password in NAS settings.',
                ],
                'debug' => [
                    'nas_id_input' => $nasId,
                    'api_user' => $apiUser,
                    'credential_candidates' => array_map(function($c){
                        return ['source' => $c['source'], 'user' => $c['user'], 'port' => $c['port'], 'pass_len' => strlen((string)$c['pass'])];
                    }, $credCandidates),
                    'host_candidates' => $hostCandidates,
                    'port_candidates' => $portCandidates,
                    'attempts' => $connectDiag,
                ],
            ]);
            return;
        }

        log_message('info', 'sync_mikrotik_vouchers connected host=' . $connectedHost . ' port=' . $connectedPort . ' ssl=' . json_encode($connectedSsl) . ' nas_input=' . $nasId . ' cred_source=' . $connectedCredSource . ' api_user=' . $connectedApiUser);

        $users = $API->comm('/ip/hotspot/user/print');
        $activeUsers = $API->comm('/ip/hotspot/active/print');
        $API->disconnect();

        if (!is_array($users)) $users = [];
        if (!is_array($activeUsers)) $activeUsers = [];

        $activeMap = [];
        foreach ($activeUsers as $a) {
            $n = isset($a['user']) ? trim($a['user']) : (isset($a['name']) ? trim($a['name']) : '');
            if ($n !== '') {
                $activeMap[$n] = [
                    'uptime' => isset($a['uptime']) ? $a['uptime'] : null,
                    'ip' => isset($a['address']) ? $a['address'] : null,
                    'mac' => isset($a['mac-address']) ? $a['mac-address'] : null,
                ];
            }
        }

        $filtered = [];
        foreach ($users as $u) {
            $name = isset($u['name']) ? trim($u['name']) : '';
            if ($name === '') continue;

            if ($namePrefix !== '' && strpos($name, $namePrefix) !== 0) {
                continue;
            }

            if ($mode !== 'all') {
                $comment = isset($u['comment']) ? (string)$u['comment'] : '';
                $hasVoucherComment = (stripos($comment, 'expires:') !== false) || (stripos($comment, 'expired:') !== false);
                $hasLimit = !empty($u['limit-uptime']);
                $digitsOnly = (bool)preg_match('/^[0-9]{6,}$/', $name);
                if (!$hasVoucherComment && !$hasLimit && !$digitsOnly) {
                    continue;
                }
            }

            $filtered[] = $u;
            if (count($filtered) >= $limit) break;
        }

        $usernames = [];
        foreach ($filtered as $f) {
            if (!empty($f['name'])) $usernames[] = $f['name'];
        }

        $radacctStats = $this->getRadacctStatsByUsernames($usernames);

        $tableFields = $this->db->list_fields('rm_cards');
        $fieldSet = [];
        foreach ($tableFields as $tf) $fieldSet[$tf] = true;

        $maxId = 0;
        $rowMax = $this->db->select_max('id')->get('rm_cards')->row();
        if (!empty($rowMax) && isset($rowMax->id)) $maxId = (int)$rowMax->id;

        // Use the same series pattern as normal generated cards (YYYY-XXXX).
        $seriesName = null;
        if (isset($this->users_model) && method_exists($this->users_model, 'genCardSeriesNumber')) {
            $cardseriesmax = $this->users_model->genCardSeriesNumber();
            if (!empty($cardseriesmax) && isset($cardseriesmax->series)) {
                $rawSeries = trim((string)$cardseriesmax->series);
                $nextNum = null;

                if (preg_match('/^(\d{4})-(\d+)$/', $rawSeries, $m)) {
                    $nextNum = (int)$m[2] + 1;
                } elseif (preg_match('/(\d+)$/', $rawSeries, $m)) {
                    $nextNum = (int)$m[1] + 1;
                } elseif (is_numeric($rawSeries)) {
                    $nextNum = (int)$rawSeries + 1;
                }

                if ($nextNum !== null) {
                    $seriesName = date('Y') . '-' . sprintf('%04d', $nextNum);
                }
            }
        }
        if (empty($seriesName)) {
            $seriesName = date('Y') . '-' . sprintf('%04d', (int)date('His'));
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = [];
        $rows = [];

        foreach ($filtered as $u) {
            $username = isset($u['name']) ? trim($u['name']) : '';
            if ($username === '') continue;

            $password = isset($u['password']) ? (string)$u['password'] : '';
            $comment = isset($u['comment']) ? (string)$u['comment'] : '';
            $disabled = isset($u['disabled']) ? (string)$u['disabled'] : 'false';
            $limitUptime = isset($u['limit-uptime']) ? (string)$u['limit-uptime'] : '';

            $expiry = $this->parseExpiryFromComment($comment);
            $isActiveNow = isset($activeMap[$username]);
            $stat = isset($radacctStats[$username]) ? $radacctStats[$username] : [];
            $activeUptimeRaw = ($isActiveNow && isset($activeMap[$username]['uptime'])) ? (string)$activeMap[$username]['uptime'] : '';

            $totalSeconds = isset($stat['total_session_seconds']) ? (int)$stat['total_session_seconds'] : 0;
            $lastStart = isset($stat['last_start']) ? $stat['last_start'] : null;
            $lastStop = isset($stat['last_stop']) ? $stat['last_stop'] : null;
            $lastIp = isset($stat['last_ip']) ? $stat['last_ip'] : null;
            $lastMac = isset($stat['last_mac']) ? $stat['last_mac'] : null;

            // Fallback: derive minimal activity from MikroTik active uptime when radacct is missing.
            if ($totalSeconds <= 0 && $activeUptimeRaw !== '') {
                $parsedSeconds = $this->parseRouterosDurationToSeconds($activeUptimeRaw);
                if ($parsedSeconds > 0) {
                    $totalSeconds = $parsedSeconds;
                }
            }

            if ($isActiveNow) {
                if (empty($lastIp) && !empty($activeMap[$username]['ip'])) $lastIp = $activeMap[$username]['ip'];
                if (empty($lastMac) && !empty($activeMap[$username]['mac'])) $lastMac = $activeMap[$username]['mac'];
                if (empty($lastStart)) $lastStart = date('Y-m-d H:i:s');
            }

            $isUsed = ($totalSeconds > 0) || $isActiveNow;

            $existing = $this->db->get_where('rm_cards', ['cardnum' => $username])->row();

            $detail = [
                'username' => $username,
                'exists' => !empty($existing),
                'used' => $isUsed ? 1 : 0,
                'active_now' => $isActiveNow ? 1 : 0,
                'disabled' => (strtolower($disabled) === 'true') ? 1 : 0,
                'expiry' => $expiry,
                'limit_uptime' => $limitUptime,
                'active_uptime' => $activeUptimeRaw,
                'session_seconds' => $totalSeconds,
                'last_start' => $lastStart,
                'last_stop' => $lastStop,
                'last_ip' => $lastIp,
                'last_mac' => $lastMac,
            ];

            if (empty($existing)) {
                $maxId++;
                $insert = [];
                if (isset($fieldSet['id'])) $insert['id'] = $maxId;
                if (isset($fieldSet['cardnum'])) $insert['cardnum'] = $username;
                if (isset($fieldSet['password'])) $insert['password'] = $password;
                if (isset($fieldSet['value'])) $insert['value'] = 0;
                if (isset($fieldSet['expiration'])) $insert['expiration'] = $expiry ? $expiry : date('Y-m-d');
                if (isset($fieldSet['series'])) $insert['series'] = $seriesName;
                if (isset($fieldSet['date'])) $insert['date'] = date('Y-m-d');
                if (isset($fieldSet['owner'])) $insert['owner'] = $owner;
                if (isset($fieldSet['cardtype'])) $insert['cardtype'] = 0;
                if (isset($fieldSet['revoked'])) $insert['revoked'] = 0;
                if (isset($fieldSet['downlimit'])) $insert['downlimit'] = 0;
                if (isset($fieldSet['uplimit'])) $insert['uplimit'] = 0;
                if (isset($fieldSet['comblimit'])) $insert['comblimit'] = 0;
                if (isset($fieldSet['uptimelimit'])) $insert['uptimelimit'] = 0;
                if (isset($fieldSet['srvid'])) $insert['srvid'] = $srvid;
                if (isset($fieldSet['transid'])) $insert['transid'] = md5($username . microtime(true));
                if (isset($fieldSet['active'])) $insert['active'] = $isUsed ? 1 : 0;
                if (isset($fieldSet['expiretime'])) $insert['expiretime'] = 0;
                if (isset($fieldSet['timebaseexp'])) $insert['timebaseexp'] = 0;
                if (isset($fieldSet['timebaseonline'])) $insert['timebaseonline'] = $totalSeconds;
                if (isset($fieldSet['used'])) $insert['used'] = $isUsed ? 1 : 0;
                if (isset($fieldSet['nasid'])) $insert['nasid'] = (int)$nasId;
                elseif (isset($fieldSet['nas_id'])) $insert['nas_id'] = (int)$nasId;
                if (isset($fieldSet['activated_on']) && !empty($lastStart)) $insert['activated_on'] = $lastStart;
                if (isset($fieldSet['activatedon']) && !empty($lastStart)) $insert['activatedon'] = $lastStart;
                if (isset($fieldSet['last_active_on']) && !empty($lastStart)) $insert['last_active_on'] = $lastStart;
                if (isset($fieldSet['lastactiveon']) && !empty($lastStart)) $insert['lastactiveon'] = $lastStart;

                if ($dryRun) {
                    $created++;
                    $detail['action'] = 'would_create';
                } else {
                    $ok = $this->db->insert('rm_cards', $insert);
                    if ($ok) {
                        $created++;
                        $detail['action'] = 'created';
                    } else {
                        $errors[] = $username . ': insert failed';
                        $detail['action'] = 'error_insert';
                    }
                }
            } else {
                $update = [];
                if (isset($fieldSet['expiration']) && !empty($expiry)) $update['expiration'] = $expiry;
                if (isset($fieldSet['active'])) $update['active'] = $isUsed ? 1 : 0;
                if (isset($fieldSet['used'])) $update['used'] = $isUsed ? 1 : 0;
                if (isset($fieldSet['timebaseonline'])) $update['timebaseonline'] = $totalSeconds;
                if (isset($fieldSet['nasid'])) {
                    $existingNas = isset($existing->nasid) ? (int)$existing->nasid : 0;
                    if ($existingNas === 0) $update['nasid'] = (int)$nasId;
                } elseif (isset($fieldSet['nas_id'])) {
                    $existingNas = isset($existing->nas_id) ? (int)$existing->nas_id : 0;
                    if ($existingNas === 0) $update['nas_id'] = (int)$nasId;
                }
                if (isset($fieldSet['last_active_on']) && !empty($lastStart)) $update['last_active_on'] = $lastStart;
                if (isset($fieldSet['lastactiveon']) && !empty($lastStart)) $update['lastactiveon'] = $lastStart;
                if (isset($fieldSet['last_seen']) && !empty($lastStart)) $update['last_seen'] = $lastStart;
                if (isset($fieldSet['lastseen']) && !empty($lastStart)) $update['lastseen'] = $lastStart;

                if (empty($update)) {
                    $skipped++;
                    $detail['action'] = 'skipped_no_change';
                } else {
                    if ($dryRun) {
                        $updated++;
                        $detail['action'] = 'would_update';
                    } else {
                        $this->db->where('cardnum', $username)->update('rm_cards', $update);
                        if ($this->db->affected_rows() >= 0) {
                            $updated++;
                            $detail['action'] = 'updated';
                        } else {
                            $errors[] = $username . ': update failed';
                            $detail['action'] = 'error_update';
                        }
                    }
                }
            }

            $rows[] = $detail;
        }

        echo json_encode([
            'success' => true,
            'dry_run' => $dryRun,
            'nas_id' => (int)$nasId,
            'nas_host' => $host,
            'mode' => $mode,
            'batch_series' => $seriesName,
            'scanned_users' => count($users),
            'selected_users' => count($filtered),
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
            'errors' => $errors,
            'rows' => $rows,
            'message' => $dryRun ? 'Dry run complete. No DB writes were made.' : 'Sync completed.',
        ]);
    } catch (Throwable $e) {
        log_message('error', 'sync_mikrotik_vouchers exception: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
        $this->output->set_status_header(500);
        echo json_encode(['success' => false, 'message' => 'Internal server error', 'error' => substr($e->getMessage(), 0, 500)]);
    }
}

private function isSyncAuthorized()
{
    if (is_cli()) return true;
    if (!$this->session) return false;
    $name = (string)$this->session->userdata('name');
    if ($name === 'admin') return true;
    if ((int)$this->session->userdata('is_admin') === 1) return true;
    return false;
}

private function resolveNasInfoForSync($nasId)
{
    $nasId = trim((string)$nasId);
    $nasInfo = null;
    if (method_exists($this->Other_model, 'getDynamicNasInfo')) {
        $nasInfo = $this->Other_model->getDynamicNasInfo($nasId);
    }

    if (is_object($nasInfo)) {
        if (empty($nasInfo->apiuser) && !empty($nasInfo->apiusername)) $nasInfo->apiuser = $nasInfo->apiusername;
        if (empty($nasInfo->apipasswd) && !empty($nasInfo->apipassword)) $nasInfo->apipasswd = $nasInfo->apipassword;
        if (empty($nasInfo->apiport) && !empty($nasInfo->ports)) $nasInfo->apiport = $nasInfo->ports;
        if (empty($nasInfo->domain) && !empty($nasInfo->nasname)) $nasInfo->domain = $nasInfo->nasname;
        return $nasInfo;
    }

    $nasRow = null;
    if ($nasId !== '' && ctype_digit($nasId)) {
        $nasRow = $this->db->get_where('nas', ['id' => (int)$nasId])->row();
    } elseif ($nasId !== '') {
        // Allow callers to pass NAS IP/host directly (e.g. nasname = 103.204.32.125)
        $this->db->from('nas');
        $this->db->group_start();
        $this->db->where('nasname', $nasId);
        $this->db->or_where('shortname', $nasId);
        $this->db->group_end();
        $nasRow = $this->db->get()->row();

        // If not found in nas table, try dynamicnas by domain/nasname then map back to nas
        if (empty($nasRow)) {
            $dynRow = $this->db->get_where('tbl_dynamicnas', ['domain' => $nasId])->row();
            if (empty($dynRow)) {
                $dynRow = $this->db->get_where('tbl_dynamicnas', ['nasname' => $nasId])->row();
            }
            if (!empty($dynRow) && !empty($dynRow->nasname)) {
                $nasRow = $this->db->get_where('nas', ['nasname' => $dynRow->nasname])->row();
            }
        }
    }
    if (empty($nasRow)) return null;

    $obj = new stdClass();
    $obj->shortname = isset($nasRow->shortname) ? $nasRow->shortname : '';
    $obj->nasname = isset($nasRow->nasname) ? $nasRow->nasname : '';
    $obj->domain = $obj->nasname;
    $obj->apiuser = isset($nasRow->apiusername) ? $nasRow->apiusername : '';
    $obj->apipasswd = isset($nasRow->apipassword) ? $nasRow->apipassword : '';
    $obj->apiport = isset($nasRow->ports) ? $nasRow->ports : 8728;

    $dyn = $this->db->get_where('tbl_dynamicnas', ['nasname' => $obj->nasname])->row();
    if (!empty($dyn)) {
        if (!empty($dyn->domain)) $obj->domain = $dyn->domain;
        if (!empty($dyn->apiuser)) $obj->apiuser = $dyn->apiuser;
        if (!empty($dyn->apipasswd)) $obj->apipasswd = $dyn->apipasswd;
        if (!empty($dyn->apiport)) $obj->apiport = $dyn->apiport;
    }

    return $obj;
}

private function getRmCardsNasColumn()
{
    $fields = $this->db->list_fields('rm_cards');
    if (in_array('nasid', $fields, true)) return 'nasid';
    if (in_array('nas_id', $fields, true)) return 'nas_id';
    return null;
}

// Returns: 1 = updated, 0 = NAS column missing, -1 = failed
private function updateRmCardNasAssignment($cardnum, $nasId)
{
    $col = $this->getRmCardsNasColumn();
    if ($col === null) return 0;

    $exists = $this->db->select('cardnum')->from('rm_cards')->where('cardnum', $cardnum)->get()->row();
    if (empty($exists)) return -1;

    $ok = $this->db->where('cardnum', $cardnum)->update('rm_cards', [$col => (int)$nasId]);
    return $ok ? 1 : -1;
}

private function parseExpiryFromComment($comment)
{
    if (!is_string($comment) || $comment === '') return null;

    if (preg_match('/(\d{4}-\d{2}-\d{2})/', $comment, $m)) {
        $t = strtotime($m[1]);
        if ($t !== false) {
            $parsed = date('Y-m-d', $t);
            $year = (int)substr($parsed, 0, 4);
            if ($year >= 2000 && $year <= 2100) {
                return $parsed;
            }
        }
    }

    return null;
}

private function parseRouterosDurationToSeconds($duration)
{
    if (!is_string($duration) || trim($duration) === '') return 0;
    $duration = trim($duration);

    // RouterOS duration examples: 1w2d03:04:05, 2d12:00:00, 00:10:30, 3h20m10s
    $total = 0;

    if (preg_match('/^(?:(\d+)w)?(?:(\d+)d)?(?:(\d{1,2}):(\d{2}):(\d{2}))$/', $duration, $m)) {
        $w = isset($m[1]) && $m[1] !== '' ? (int)$m[1] : 0;
        $d = isset($m[2]) && $m[2] !== '' ? (int)$m[2] : 0;
        $h = isset($m[3]) && $m[3] !== '' ? (int)$m[3] : 0;
        $i = isset($m[4]) && $m[4] !== '' ? (int)$m[4] : 0;
        $s = isset($m[5]) && $m[5] !== '' ? (int)$m[5] : 0;
        return ($w * 604800) + ($d * 86400) + ($h * 3600) + ($i * 60) + $s;
    }

    if (preg_match('/^(\d{1,2}):(\d{2}):(\d{2})$/', $duration, $m)) {
        return ((int)$m[1] * 3600) + ((int)$m[2] * 60) + (int)$m[3];
    }

    if (preg_match_all('/(\d+)([wdhms])/', $duration, $parts, PREG_SET_ORDER)) {
        foreach ($parts as $p) {
            $v = (int)$p[1];
            $u = $p[2];
            if ($u === 'w') $total += $v * 604800;
            elseif ($u === 'd') $total += $v * 86400;
            elseif ($u === 'h') $total += $v * 3600;
            elseif ($u === 'm') $total += $v * 60;
            elseif ($u === 's') $total += $v;
        }
    }

    return $total;
}

private function getRadacctStatsByUsernames($usernames)
{
    $stats = [];
    if (empty($usernames) || !is_array($usernames)) return $stats;

    $tableExists = $this->db->query("SHOW TABLES LIKE 'radacct'")->num_rows() > 0;
    if (!$tableExists) return $stats;

    $chunkSize = 300;
    $chunks = array_chunk($usernames, $chunkSize);

    foreach ($chunks as $chunk) {
        if (empty($chunk)) continue;

        $this->db->select('username, MAX(acctstarttime) AS last_start, MAX(acctstoptime) AS last_stop, SUM(IFNULL(acctsessiontime,0)) AS total_session_seconds, MAX(callingstationid) AS last_mac, MAX(framedipaddress) AS last_ip', false);
        $this->db->from('radacct');
        $this->db->where_in('username', $chunk);
        $this->db->group_by('username');
        $rows = $this->db->get()->result();

        foreach ($rows as $r) {
            $u = isset($r->username) ? $r->username : null;
            if (!$u) continue;
            $stats[$u] = [
                'last_start' => isset($r->last_start) ? $r->last_start : null,
                'last_stop' => isset($r->last_stop) ? $r->last_stop : null,
                'total_session_seconds' => isset($r->total_session_seconds) ? (int)$r->total_session_seconds : 0,
                'last_mac' => isset($r->last_mac) ? $r->last_mac : null,
                'last_ip' => isset($r->last_ip) ? $r->last_ip : null,
            ];
        }
    }

    return $stats;
}




}


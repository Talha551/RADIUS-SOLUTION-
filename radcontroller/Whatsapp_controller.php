<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

// WAHA API Controller
/**
 * Class Whatsapp_controller
 * @property Whatsapp_model $Whatsapp_model
 * @property Users_model $Users_model
 * @property CI_Input $input
 * @property CI_Session $session
 * @property CI_Form_validation $form_validation
 * @property CI_Loader $load
 * @property CI_Output $output
 * @property CI_URI $uri
 */
class Whatsapp_controller extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Whatsapp_model');
        $this->load->model('Users_model');
        $this->load->model('Other_model');
        $this->isLoggedIn();
    }

    public function index()
    {
        $data['sessions'] = $this->Whatsapp_model->get_all_sessions_with_status();
        $this->global['pageTitle'] = 'WhatsApp Sessions';
        $this->loadViews('whatsapp/waha_sessions_list', $this->global, $data, NULL);
    }

    public function add()
    {
        $data['managers'] = $this->Users_model->getManagersList();
        $this->global['pageTitle'] = 'Add WhatsApp Session';
        $this->loadViews('whatsapp/waha_session_add', $this->global, $data, NULL);
    }

    public function create()
    {
        $session_name = trim($this->input->post('session_name'));
        $managername = trim($this->input->post('managername'));
        $manual_waha_session_id = trim((string) $this->input->post('manual_waha_session_id'));
        $manual_status = trim((string) $this->input->post('manual_status'));

        if (!$session_name || !$managername) {
            $this->session->set_flashdata('error', 'Session name and manager name are required.');
            redirect('Whatsapp_controller/add');
        }

        $api_data = [
            'name' => $session_name
        ];

        $result = $this->Whatsapp_model->call_waha_api('sessions/start', 'POST', $api_data);
        $api_response = json_decode($result['response'], true);

        $api_ok = in_array($result['http_code'], [200, 201], true)
            && is_array($api_response)
            && !empty($api_response['name'])
            && !empty($api_response['status']);

        if ($api_ok) {
            $waha_session_id = $api_response['name'];
            $status = $api_response['status'];
        } elseif ($manual_waha_session_id !== '' && $manual_status !== '') {
            $waha_session_id = $manual_waha_session_id;
            $status = $manual_status;
        } else {
            $err = 'Failed to create session via WAHA API: ' . ($result['error'] ?: '') . ' ' . ($result['response'] ?: '');
            $this->session->set_flashdata('error', $err . ' You can enter Manual WA session ID and Manual status below (same session name and manager), then submit again to save without the API.');
            $this->session->set_flashdata('old_session_name', $session_name);
            $this->session->set_flashdata('old_managername', $managername);
            $this->session->set_flashdata('show_manual_fallback', true);
            redirect('Whatsapp_controller/add');
        }

        $data = [
            'session_name' => $session_name,
            'managername' => $managername,
            'waha_session_id' => $waha_session_id,
            'status' => $status,
            'created_by' => $this->session->userdata('userId'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->Whatsapp_model->add_session($data);

        if ($api_ok) {
            $this->session->set_flashdata('success', 'Session created. Please scan QR or enter pairing code to connect.');
        } else {
            $this->session->set_flashdata('success', 'Session saved using manual WA session ID and status (WAHA API did not return a valid response).');
        }
        redirect('Whatsapp_controller/index');
    }

    public function edit($id)
    {
        $data['session'] = $this->Whatsapp_model->get_session($id);
        $this->global['pageTitle'] = 'Edit WhatsApp Session';
        $this->loadViews('whatsapp/waha_session_edit', $this->global, $data, NULL);
    }

    public function update($id)
    {
        // TODO: Validate and update session, call WAHA API if needed
    }

    public function delete($id)
    {
        $this->Whatsapp_model->delete_session($id);
        $this->session->set_flashdata('success', 'Session deleted successfully');
        redirect('Whatsapp_controller/index');
    }

    public function show_qr($name)
    {
        $session = $this->Whatsapp_model->get_session_by_name($name);
        if (!$session) {
            echo '<div class="alert alert-danger">Session not found.</div>';
            return;
        }
        // Get real debug output
        $debug_qr_response = $this->Whatsapp_model->call_waha_api('sessions/' . urlencode($name) . '/auth/qr', 'GET');
        $debug_pairing_response = $this->Whatsapp_model->call_waha_api('sessions/' . urlencode($name) . '/auth/request-code', 'GET');
        $qr_code = $this->Whatsapp_model->get_qr_code($name);
        $pairing_code = $this->Whatsapp_model->get_pairing_code($name);
        $data = [
            'session' => (object)$session,
            'qr_code' => $qr_code,
            'pairing_code' => $pairing_code,
            'debug_qr_response' => $debug_qr_response,
            'debug_pairing_response' => $debug_pairing_response
        ];
        $this->load->view('whatsapp/waha_session_qr_modal', $data);
    }

    public function get_qr_and_status($name)
    {
        $qr_code = $this->Whatsapp_model->get_qr_code($name);
        $status = $this->Whatsapp_model->get_session_status($name);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'qr' => $qr_code,
                'status' => $status
            ]));
    }

    public function ajax_sessions_table()
    {
        $sessions = $this->Whatsapp_model->get_all_sessions_with_status();
        $data['sessions'] = $sessions;
        $this->load->view('whatsapp/waha_sessions_table_rows', $data);
    }

    public function sync_sessions_status()
    {
        try {
            $updated_count = $this->Whatsapp_model->sync_all_sessions_status();
            
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'success',
                    'message' => "Synced {$updated_count} sessions",
                    'updated_count' => $updated_count
                ]));
        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Failed to sync sessions: ' . $e->getMessage()
                ]));
        }
    }

    public function start_session($name) {
        $result = $this->Whatsapp_model->call_waha_api('sessions/' . urlencode($name) . '/start', 'POST');
        echo json_encode($result);
    }

    public function stop_session($name) {
        $result = $this->Whatsapp_model->call_waha_api('sessions/' . urlencode($name) . '/stop', 'POST');
        echo json_encode($result);
    }

    public function restart_session($name) {
        $result = $this->Whatsapp_model->call_waha_api('sessions/' . urlencode($name) . '/restart', 'POST');
        echo json_encode($result);
    }

    public function logout_session($name) {
        $result = $this->Whatsapp_model->call_waha_api('sessions/logout', 'POST', array('name' => $name));
        echo json_encode($result);
    }

    public function get_screenshot($name) {
        $result = $this->Whatsapp_model->call_waha_api($name . '/screenshot', 'GET');
        echo json_encode($result);
    }

    public function delete_session($name) {
        // 1. Delete from WAHA API
        $result = $this->Whatsapp_model->call_waha_api('sessions/' . urlencode($name), 'DELETE');
        // 2. Delete from local DB
        $this->Whatsapp_model->delete_session_by_name($name);
        echo json_encode($result);
    }

    public function logs()
    {
        $data['logs'] = $this->Whatsapp_model->get_whatsapp_logs();
        $this->global['pageTitle'] = 'WhatsApp Incoming Logs';
        $this->loadViews('whatsapp/waha_logs_list', $this->global, $data, NULL);
    }

    public function delete_log($id)
    {
        $result = $this->Whatsapp_model->delete_log($id);
        
        if($result > 0) {
            $this->session->set_flashdata('success', 'Log deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Log deletion failed');
        }
        
        redirect('whatsapp_controller/logs');
    }

    public function mark_messages_read()
    {
        $log_id = $this->input->post('log_id');
        $result = $this->Whatsapp_model->mark_messages_as_read($log_id);
        
        if($result > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Messages marked as read']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to mark messages as read']);
        }
    }

    public function alerts()
    {
        $data['alerts'] = $this->Whatsapp_model->get_all_alerts();
        $this->global['pageTitle'] = 'WhatsApp Alerts';
        $this->loadViews('whatsapp/waha_alerts_list', $this->global, $data, NULL);
    }

    public function add_alert()
    {
        $data['managers'] = $this->Users_model->getManagersList();
        $data['sessions'] = $this->Whatsapp_model->get_all_sessions();
        $bx = $this->waha_alert_form_baileys_sessions();
        $data['baileys_sessions'] = $bx['sessions'];
        $data['baileys_sessions_error'] = isset($bx['error']) ? $bx['error'] : null;
        $this->global['pageTitle'] = 'Add WhatsApp Alert';
        $this->loadViews('whatsapp/waha_alert_form', $this->global, $data, NULL);
    }

    public function edit_alert($id)
    {
        $data['alert'] = $this->Whatsapp_model->get_alert($id);
        if(empty($data['alert'])) {
            $this->session->set_flashdata('error', 'Alert not found');
            redirect('Whatsapp_controller/alerts');
        }
        $data['managers'] = $this->Users_model->getManagersList();
        $data['sessions'] = $this->Whatsapp_model->get_all_sessions();
        $bx = $this->waha_alert_form_baileys_sessions();
        $data['baileys_sessions'] = $bx['sessions'];
        $data['baileys_sessions_error'] = isset($bx['error']) ? $bx['error'] : null;
        $this->global['pageTitle'] = 'Edit WhatsApp Alert';
        $this->loadViews('whatsapp/waha_alert_form', $this->global, $data, NULL);
    }

    /**
     * Baileys sessions for alert form (same visibility as Baileys UI for current user).
     * Returns keys: sessions (array), error (string, optional).
     */
    private function waha_alert_form_baileys_sessions()
    {
        $out = array('sessions' => array());
        $list = $this->Whatsapp_model->baileys_list_sessions();
        if ($list['http_code'] === 200 && isset($list['json']['sessions']) && is_array($list['json']['sessions'])) {
            $out['sessions'] = $this->whats_filter_baileys_sessions_for_current_user($list['json']['sessions']);
        } else {
            $detail = isset($list['json']['detail']) ? $list['json']['detail'] : ($list['body'] ?: $list['curl_error']);
            $out['error'] = 'Could not load Baileys sessions (HTTP ' . $list['http_code'] . '). ' . $detail;
        }
        return $out;
    }

    public function create_alert()
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('alert_name', 'Alert Name', 'trim|required');
        $this->form_validation->set_rules('managername', 'Manager', 'trim|required');
        $this->form_validation->set_rules('alert_type', 'Alert Type', 'trim|required|numeric');
        $this->form_validation->set_rules('waha_session_id', 'WhatsApp Session', 'trim|required');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|numeric');
        $this->form_validation->set_rules('interval', 'Interval', 'trim|required|numeric');
        $this->form_validation->set_rules('duration', 'Duration', 'trim|required|numeric');
        $this->form_validation->set_rules('frequency', 'Frequency', 'trim|required|numeric');
        
        if($this->input->post('alert_type') == 5) {
            $this->form_validation->set_rules('alert_message', 'Alert Message', 'trim|required');
        }

        if($this->form_validation->run() === FALSE) {
            $this->add_alert();
            return;
        }

        $data = array(
            'alert_name' => $this->input->post('alert_name'),
            'managername' => $this->input->post('managername'),
            'alert_type' => $this->input->post('alert_type'),
            'alert_message' => $this->input->post('alert_message'),
            'mobile' => $this->input->post('mobile'),
            'waha_session_id' => $this->input->post('waha_session_id'),
            'status' => $this->input->post('status'),
            'interval' => $this->input->post('interval'),
            'duration' => $this->input->post('duration'),
            'frequency' => $this->input->post('frequency'),
            'created_at' => date('Y-m-d H:i:s')
        );

        $result = $this->Whatsapp_model->add_alert($data);

        if($this->input->post('alert_type') == 1) {
            $data = array(
                'jobtype' => 0,
                'jobname' => $this->input->post('alert_name'),
                'url' => 'sendexpiration/'.$result,
                'active' => 1,
                'jobstart' => date('Y-m-d H:i:s'),
                'jobend' => date('Y-m-d H:i:s'),
                'jobinterval' => 86400,
            );
            $jobid = $this->Other_model->jobsAdd($data);
        }
        
        if($result > 0) {
            $this->session->set_flashdata('success', 'Alert created successfully');
        } else {
            $this->session->set_flashdata('error', 'Alert creation failed');
        }
        
        redirect('Whatsapp_controller/alerts');
    }

    public function update_alert($id)
    {
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('alert_name', 'Alert Name', 'trim|required');
        $this->form_validation->set_rules('managername', 'Manager', 'trim|required');
        $this->form_validation->set_rules('alert_type', 'Alert Type', 'trim|required|numeric');
        $this->form_validation->set_rules('waha_session_id', 'WhatsApp Session', 'trim|required');
        $this->form_validation->set_rules('mobile', 'Mobile Number', 'trim|required');
        $this->form_validation->set_rules('status', 'Status', 'trim|required|numeric');
        $this->form_validation->set_rules('interval', 'Interval', 'trim|required|numeric');
        $this->form_validation->set_rules('duration', 'Duration', 'trim|required|numeric');
        $this->form_validation->set_rules('frequency', 'Frequency', 'trim|required|numeric');
        
        if($this->input->post('alert_type') == 5) {
            $this->form_validation->set_rules('alert_message', 'Alert Message', 'trim|required');
        }

        if($this->form_validation->run() === FALSE) {
            $this->edit_alert($id);
            return;
        }

        $data = array(
            'alert_name' => $this->input->post('alert_name'),
            'managername' => $this->input->post('managername'),
            'alert_type' => $this->input->post('alert_type'),
            'alert_message' => $this->input->post('alert_message'),
            'mobile' => $this->input->post('mobile'),
            'waha_session_id' => $this->input->post('waha_session_id'),
            'status' => $this->input->post('status'),
            'interval' => $this->input->post('interval'),
            'duration' => $this->input->post('duration'),
            'frequency' => $this->input->post('frequency'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $result = $this->Whatsapp_model->update_alert($id, $data);
        
        if($result > 0) {
            $this->session->set_flashdata('success', 'Alert updated successfully');
        } else {
            $this->session->set_flashdata('error', 'Alert update failed');
        }
        
        redirect('Whatsapp_controller/alerts');
    }

    public function delete_alert($id)
    {
        $alert_data = $this->Whatsapp_model->get_alert($id);
        $result = $this->Whatsapp_model->delete_alert($id);
        
        $this->Other_model->jobsDelete(null, $alert_data->alert_name);

        if($result > 0) {
            $this->session->set_flashdata('success', 'Alert deleted successfully');
        } else {
            $this->session->set_flashdata('error', 'Alert deletion failed');
        }
        
        redirect('Whatsapp_controller/alerts');
    }

    public function get_manager_mobile()
    {
        $managername = $this->input->post('managername');
        $mobile = $this->Whatsapp_model->get_manager_mobile($managername);
        
        echo json_encode(['mobile' => $mobile]);
    }

    public function clear_all_messages()
    {
        $result = $this->Whatsapp_model->clear_all_messages();
        if($result > 0) {
            echo json_encode(['status' => 'success', 'message' => 'All messages cleared successfully', 'affected_rows' => $result]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No messages to clear or operation failed']);
        }
    }

    // --- Baileys FastAPI (session id from user input; no local DB for sessions) ---

    /**
     * Main Baileys WhatsApp UI: lists sessions from API on load.
     */
    public function whats_dashboard()
    {
        $data['sessions'] = [];
        $data['api_error'] = null;
        $data['baileys_base_url'] = $this->Whatsapp_model->baileys_get_config()['base_url'];
        $list = $this->Whatsapp_model->baileys_list_sessions();
        if ($list['http_code'] === 200 && isset($list['json']['sessions']) && is_array($list['json']['sessions'])) {
            $data['sessions'] = $this->whats_filter_baileys_sessions_for_current_user($list['json']['sessions']);
        } else {
            $detail = isset($list['json']['detail']) ? $list['json']['detail'] : ($list['body'] ?: $list['curl_error']);
            $data['api_error'] = 'Could not load sessions (HTTP ' . $list['http_code'] . '). ' . $detail;
        }
        $data['baileys_manager_login'] = $this->whats_current_manager_name();
        $data['baileys_is_admin'] = $this->whats_is_baileys_admin();
        $this->global['pageTitle'] = 'WhatsApp (Baileys API)';
        $this->loadViews('whatsapp/whats_dashboard', $this->global, $data, NULL);
    }

    /**
     * Baileys sessions list (same layout style as waha_sessions_list; data from Baileys API only).
     */
    public function whats_sessions()
    {
        $data['sessions'] = [];
        $data['api_error'] = null;
        $data['baileys_base_url'] = $this->Whatsapp_model->baileys_get_config()['base_url'];
        $list = $this->Whatsapp_model->baileys_list_sessions();
        if ($list['http_code'] === 200 && isset($list['json']['sessions']) && is_array($list['json']['sessions'])) {
            $data['sessions'] = $this->whats_filter_baileys_sessions_for_current_user($list['json']['sessions']);
        } else {
            $detail = isset($list['json']['detail']) ? $list['json']['detail'] : ($list['body'] ?: $list['curl_error']);
            $data['api_error'] = 'Could not load sessions (HTTP ' . $list['http_code'] . '). ' . $detail;
        }
        $data['baileys_manager_login'] = $this->whats_current_manager_name();
        $data['baileys_is_admin'] = $this->whats_is_baileys_admin();
        $this->global['pageTitle'] = 'WhatsApp Sessions (Baileys)';
        $this->loadViews('whatsapp/whats_sessions_list', $this->global, $data, NULL);
    }

    /**
     * Add Baileys session form (POST handled by whats_session_create).
     */
    public function whats_add()
    {
        $data = array(
            'baileys_manager_login' => $this->whats_current_manager_name(),
            'baileys_is_admin' => $this->whats_is_baileys_admin(),
        );
        $this->global['pageTitle'] = 'Add Baileys WhatsApp Session';
        $this->loadViews('whatsapp/whats_session_add', $this->global, $data, NULL);
    }

    /**
     * Create session on Baileys API from form (no WAHA / local DB required).
     */
    public function whats_session_create()
    {
        $session_id = trim((string) $this->input->post('session_id'));
        if (!$this->whats_validate_session_id($session_id)) {
            $this->session->set_flashdata('error', 'Session ID must be 1–64 characters: letters, digits, underscore, hyphen only.');
            redirect('Whatsapp_controller/whats_add');
        }
        if (!$this->whats_session_owned_by_current_user($session_id)) {
            $this->session->set_flashdata('error', 'Session ID must start with your login and an underscore, e.g. ' . $this->whats_current_manager_name() . '_office — or use exactly your login as the session id.');
            redirect('Whatsapp_controller/whats_add');
        }
        $result = $this->Whatsapp_model->baileys_create_session($session_id);
        $http = isset($result['http_code']) ? (int) $result['http_code'] : 0;
        $json = isset($result['json']) && is_array($result['json']) ? $result['json'] : null;
        if (in_array($http, [200, 201], true) && $json !== null) {
            $this->session->set_flashdata('success', 'Baileys session created: ' . $session_id . '. Scan QR from the session list to connect.');
            redirect('Whatsapp_controller/whats_sessions');
        }
        $detail = is_array($json) && isset($json['detail']) ? $json['detail'] : ($result['body'] ?: $result['curl_error'] ?: 'Unknown error');
        $this->session->set_flashdata('error', 'Baileys API error (HTTP ' . $http . '): ' . (is_string($detail) ? $detail : json_encode($detail)));
        redirect('Whatsapp_controller/whats_add');
    }

    /**
     * AJAX: refresh tbody HTML for Baileys sessions table (same data source as whats_sessions).
     */
    public function whats_ajax_sessions_table()
    {
        $sessions = [];
        $list = $this->Whatsapp_model->baileys_list_sessions();
        if ($list['http_code'] === 200 && isset($list['json']['sessions']) && is_array($list['json']['sessions'])) {
            $sessions = $this->whats_filter_baileys_sessions_for_current_user($list['json']['sessions']);
        }
        $data['sessions'] = $sessions;
        $this->load->view('whatsapp/whats_sessions_table_rows', $data);
    }

    /** Admin sees all Baileys sessions; managers only sessions named `login` or `login_*`. */
    private function whats_is_baileys_admin()
    {
        return $this->session->userdata('name') === 'admin';
    }

    /** Logged-in manager username (same as WAHA / RadSpot manager name). */
    private function whats_current_manager_name()
    {
        return trim((string) $this->session->userdata('name'));
    }

    /**
     * Non-admin: session_id must equal manager login, or start with `login_`.
     */
    private function whats_session_owned_by_current_user($session_id)
    {
        if ($this->whats_is_baileys_admin()) {
            return true;
        }
        $m = $this->whats_current_manager_name();
        if ($m === '' || !$this->whats_validate_session_id($session_id)) {
            return false;
        }
        if ($session_id === $m) {
            return true;
        }
        $prefix = $m . '_';
        return strlen($session_id) >= strlen($prefix) && substr($session_id, 0, strlen($prefix)) === $prefix;
    }

    /**
     * @param array $sessions List of Baileys API session rows (session_id, status, …)
     */
    private function whats_filter_baileys_sessions_for_current_user(array $sessions)
    {
        if ($this->whats_is_baileys_admin()) {
            return $sessions;
        }
        $out = array();
        foreach ($sessions as $row) {
            if (!is_array($row)) {
                continue;
            }
            $sid = isset($row['session_id']) ? $row['session_id'] : '';
            if ($sid !== '' && $this->whats_session_owned_by_current_user($sid)) {
                $out[] = $row;
            }
        }
        return $out;
    }

    private function whats_forbidden_session_json()
    {
        $hint = $this->whats_current_manager_name();
        $msg = 'You do not have access to this session.';
        if ($hint !== '') {
            $msg .= ' Name sessions as "' . $hint . '" or "' . $hint . '_something".';
        }
        $this->output->set_status_header(403)->set_content_type('application/json')
            ->set_output(json_encode(array('ok' => false, 'detail' => $msg)));
    }

    private function whats_validate_session_id($id)
    {
        return is_string($id) && preg_match('/^[a-zA-Z0-9_-]{1,64}$/', $id) === 1;
    }

    private function whats_baileys_error_payload($result)
    {
        $j = $result['json'];
        $detail = is_array($j) && isset($j['detail']) ? $j['detail'] : ($result['body'] ?: $result['curl_error'] ?: 'Request failed');
        return ['ok' => false, 'detail' => $detail, 'http_code' => $result['http_code']];
    }

    public function whats_ajax_list_sessions()
    {
        $result = $this->Whatsapp_model->baileys_list_sessions();
        if ($result['http_code'] === 200 && isset($result['json']['sessions'])) {
            $sess = is_array($result['json']['sessions']) ? $result['json']['sessions'] : [];
            $filtered = $this->whats_filter_baileys_sessions_for_current_user($sess);
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'sessions' => $filtered]));
            return;
        }
        $this->output->set_status_header($result['http_code'] ?: 502)->set_content_type('application/json')
            ->set_output(json_encode($this->whats_baileys_error_payload($result)));
    }

    public function whats_ajax_create_session()
    {
        $raw = $this->input->raw_input_stream;
        $post = json_decode($raw, true);
        if (!is_array($post)) {
            $post = [];
        }
        $session_id = isset($post['session_id']) ? trim((string) $post['session_id']) : trim((string) $this->input->post('session_id'));
        if (!$this->whats_validate_session_id($session_id)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'detail' => 'session_id must be 1–64 chars: letters, digits, _, -']));
            return;
        }
        if (!$this->whats_session_owned_by_current_user($session_id)) {
            $this->whats_forbidden_session_json();
            return;
        }
        $result = $this->Whatsapp_model->baileys_create_session($session_id);
        if (in_array($result['http_code'], [200, 201], true) && is_array($result['json'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'data' => $result['json']]));
            return;
        }
        $this->output->set_status_header($result['http_code'] ?: 502)->set_content_type('application/json')
            ->set_output(json_encode($this->whats_baileys_error_payload($result)));
    }

    public function whats_ajax_session_status($session_id = '')
    {
        $session_id = rawurldecode($session_id);
        if (!$this->whats_validate_session_id($session_id)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'detail' => 'Invalid session_id']));
            return;
        }
        if (!$this->whats_session_owned_by_current_user($session_id)) {
            $this->whats_forbidden_session_json();
            return;
        }
        $result = $this->Whatsapp_model->baileys_session_status($session_id);
        if ($result['http_code'] === 200 && is_array($result['json'])) {
            $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            $this->output->set_header('Pragma: no-cache');
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'data' => $result['json']]));
            return;
        }
        $this->output->set_status_header($result['http_code'] ?: 502)->set_content_type('application/json')
            ->set_output(json_encode($this->whats_baileys_error_payload($result)));
    }

    public function whats_ajax_session_qr($session_id = '')
    {
        $session_id = rawurldecode($session_id);
        if (!$this->whats_validate_session_id($session_id)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'detail' => 'Invalid session_id']));
            return;
        }
        if (!$this->whats_session_owned_by_current_user($session_id)) {
            $this->whats_forbidden_session_json();
            return;
        }
        $result = $this->Whatsapp_model->baileys_session_qr_json($session_id);
        if ($result['http_code'] === 200 && is_array($result['json'])) {
            $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            $this->output->set_header('Pragma: no-cache');
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'data' => $result['json']]));
            return;
        }
        $this->output->set_status_header($result['http_code'] ?: 502)->set_content_type('application/json')
            ->set_output(json_encode($this->whats_baileys_error_payload($result)));
    }

    /**
     * Proxies GET .../qr.png so <img src="..."> works without exposing the API key in the browser.
     * Validates PNG signature — API may return JSON errors as body when QR is not ready.
     */
    public function whats_session_qr_png($session_id = '')
    {
        $session_id = rawurldecode($session_id);
        if (!$this->whats_validate_session_id($session_id)) {
            show_404();
            return;
        }
        if (!$this->whats_session_owned_by_current_user($session_id)) {
            $this->output->set_status_header(403)->set_content_type('text/plain')->set_output('Forbidden');
            return;
        }
        $result = $this->Whatsapp_model->baileys_session_qr_png($session_id);
        $body = $result['body'];
        $is_png = strlen($body) >= 8 && substr($body, 0, 8) === "\x89PNG\r\n\x1a\n";

        if ($result['http_code'] === 200 && $is_png) {
            $this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
            $this->output->set_header('Content-Length: ' . strlen($body));
            $this->output->set_content_type('image/png')->set_output($body);
            return;
        }

        $this->output->set_status_header($result['http_code'] ?: 502)->set_content_type('text/plain')
            ->set_output($result['curl_error'] ?: ($body ?: 'QR image unavailable'));
    }

    public function whats_ajax_delete_session($session_id = '')
    {
        $session_id = rawurldecode($session_id);
        if (!$this->whats_validate_session_id($session_id)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'detail' => 'Invalid session_id']));
            return;
        }
        if (!$this->whats_session_owned_by_current_user($session_id)) {
            $this->whats_forbidden_session_json();
            return;
        }
        $result = $this->Whatsapp_model->baileys_delete_session($session_id);
        if ($result['http_code'] === 204 || $result['http_code'] === 200) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true]));
            return;
        }
        $this->output->set_status_header($result['http_code'] ?: 502)->set_content_type('application/json')
            ->set_output(json_encode($this->whats_baileys_error_payload($result)));
    }

    /**
     * When status is logged_out (user unlinked device), delete stored auth and recreate the same session_id
     * so a fresh QR can be shown without manual delete + add.
     */
    public function whats_ajax_recover_logged_out_session($session_id = '')
    {
        $session_id = rawurldecode($session_id);
        if (!$this->whats_validate_session_id($session_id)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'detail' => 'Invalid session_id']));
            return;
        }
        if (!$this->whats_session_owned_by_current_user($session_id)) {
            $this->whats_forbidden_session_json();
            return;
        }

        $status_res = $this->Whatsapp_model->baileys_session_status($session_id);
        $current = '';
        if ($status_res['http_code'] === 200 && is_array($status_res['json']) && isset($status_res['json']['status'])) {
            $current = strtolower((string) $status_res['json']['status']);
        }

        if ($current !== 'logged_out') {
            $this->output->set_header('Cache-Control: no-store');
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'ok' => true,
                'skipped' => true,
                'session_id' => $session_id,
                'detail' => 'Not logged_out (current: ' . ($current !== '' ? $current : 'unknown') . ')',
            ]));
            return;
        }

        $del = $this->Whatsapp_model->baileys_delete_session($session_id);
        $del_code = isset($del['http_code']) ? (int) $del['http_code'] : 0;
        $del_ok = in_array($del_code, [200, 204, 404], true);

        if (!$del_ok) {
            $err = $this->whats_baileys_error_payload($del);
            $this->output->set_status_header($del_code ?: 502)->set_content_type('application/json')
                ->set_output(json_encode([
                    'ok' => false,
                    'detail' => 'Failed to delete session before recreate: ' . (isset($err['detail']) ? $err['detail'] : ''),
                    'http_code' => $del_code,
                ]));
            return;
        }

        $create = $this->Whatsapp_model->baileys_create_session($session_id);
        $http = isset($create['http_code']) ? (int) $create['http_code'] : 0;
        $json = isset($create['json']) && is_array($create['json']) ? $create['json'] : null;

        if (in_array($http, [200, 201], true) && $json !== null) {
            log_message('info', 'Baileys recover logged_out: recreated session ' . $session_id);
            $this->output->set_header('Cache-Control: no-store');
            $this->output->set_content_type('application/json')->set_output(json_encode([
                'ok' => true,
                'recovered' => true,
                'session_id' => $session_id,
                'data' => $json,
            ]));
            return;
        }

        $cerr = $this->whats_baileys_error_payload($create);
        $this->output->set_status_header($http ?: 502)->set_content_type('application/json')
            ->set_output(json_encode([
                'ok' => false,
                'detail' => 'Session was deleted but recreate failed: ' . (isset($cerr['detail']) ? $cerr['detail'] : '') . ' — add the session manually if needed.',
                'http_code' => $http,
            ]));
    }

    public function whats_ajax_send()
    {
        $raw = $this->input->raw_input_stream;
        $post = json_decode($raw, true);
        if (!is_array($post)) {
            $post = [];
        }
        $session_id = isset($post['session_id']) ? trim((string) $post['session_id']) : trim((string) $this->input->post('session_id'));
        $to = isset($post['to']) ? trim((string) $post['to']) : trim((string) $this->input->post('to'));
        $text = isset($post['text']) ? (string) $post['text'] : (string) $this->input->post('text');
        $typing_raw = isset($post['typing']) ? $post['typing'] : $this->input->post('typing');
        $typing = ($typing_raw === true || $typing_raw === 1 || $typing_raw === '1' || strtolower((string) $typing_raw) === 'true');
        $typing_ms_raw = isset($post['typing_ms']) ? $post['typing_ms'] : $this->input->post('typing_ms');
        $typing_ms = $typing_ms_raw === null || $typing_ms_raw === '' ? null : (int) $typing_ms_raw;
        if (!$typing && $typing_ms !== null && $typing_ms > 0) {
            $typing = true;
        }
        if (!$this->whats_validate_session_id($session_id)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'detail' => 'Invalid session_id']));
            return;
        }
        if (!$this->whats_session_owned_by_current_user($session_id)) {
            $this->whats_forbidden_session_json();
            return;
        }
        if ($to === '' || $text === '') {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'detail' => 'to and text are required']));
            return;
        }
        $send_opts = array();
        if ($typing) {
            $send_opts['typing'] = true;
        }
        if ($typing_ms !== null && $typing_ms > 0) {
            $send_opts['typing_ms'] = $typing_ms;
        }
        $result = $this->Whatsapp_model->baileys_send_text($session_id, $to, $text, $send_opts);
        if ($result['http_code'] === 200 && is_array($result['json']) && !empty($result['json']['ok'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'data' => $result['json']]));
            return;
        }
        if ($result['http_code'] === 200 && is_array($result['json'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'data' => $result['json']]));
            return;
        }
        $this->output->set_status_header($result['http_code'] ?: 502)->set_content_type('application/json')
            ->set_output(json_encode($this->whats_baileys_error_payload($result)));
    }

    public function whats_ajax_messages($session_id = '')
    {
        $session_id = rawurldecode($session_id);
        if (!$this->whats_validate_session_id($session_id)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'detail' => 'Invalid session_id']));
            return;
        }
        if (!$this->whats_session_owned_by_current_user($session_id)) {
            $this->whats_forbidden_session_json();
            return;
        }
        $limit = (int) $this->input->get('limit');
        if ($limit < 1) {
            $limit = 50;
        }
        $clear = $this->input->get('clear') === 'true' || $this->input->get('clear') === '1';
        $result = $this->Whatsapp_model->baileys_poll_messages($session_id, $limit, $clear);
        if ($result['http_code'] === 200 && is_array($result['json'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'data' => $result['json']]));
            return;
        }
        $this->output->set_status_header($result['http_code'] ?: 502)->set_content_type('application/json')
            ->set_output(json_encode($this->whats_baileys_error_payload($result)));
    }

    /** GET /health on Baileys (no API key required by spec; we still send key if configured). */
    public function whats_ajax_health()
    {
        $result = $this->Whatsapp_model->baileys_health();
        if ($result['http_code'] === 200 && is_array($result['json'])) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['ok' => true, 'data' => $result['json']]));
            return;
        }
        $this->output->set_status_header($result['http_code'] ?: 502)->set_content_type('application/json')
            ->set_output(json_encode($this->whats_baileys_error_payload($result)));
    }
} 
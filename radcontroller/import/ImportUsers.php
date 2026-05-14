<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require APPPATH . '/libraries/BaseController.php';

/**
 * ImportUsers Controller
 * 
 * @property CI_Loader $load
 * @property CI_Session $session
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Upload $upload
 * @property ImportUsers_model $ImportUsers_model
 * @property Services_model $Services_model
 * @property Users_model $users_model
 * @property bool $ismaster
 */
class ImportUsers extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('import/ImportUsers_model');
        $this->load->model('Services_model');
        $this->load->model('Reseller_model');
        $this->load->model('users_model');
        $this->load->helper(array('form', 'url'));
        $this->load->library('session');
        $this->isLoggedIn();
    }

    public function index()
    {

        if($this->session->userdata('name') <> 'admin' && $this->Reseller_model->checkManager_validity() == false){
            $this->session->set_flashdata('error', 'You are not authorized to import users.');
            redirect('dashboard');
        }

        $managername = $this->session->userdata('name');
        $isAdmin = ($managername == 'admin');
        $isMaster = isset($this->ismaster) ? $this->ismaster : 0;
        $data = [];
        if ($isAdmin || $isMaster) {
            $data['managerList'] = $this->users_model->getManagersList();
            $data['selectedManager'] = $this->input->post('managername') ?: $managername;
        } else {
            $data['managerList'] = [];
            $data['selectedManager'] = $managername;
        }
        $data['services'] = $this->ImportUsers_model->getServicesByManager($data['selectedManager']);
        $data['sampleUrl'] = base_url('import/ImportUsers/sample_csv');
        $this->global['pageTitle'] = 'Import Users';
        $this->loadViews('import/import_users', $this->global, $data, NULL);
    }

    public function upload()
    {
        $managername = $this->input->post('managername') ?: $this->session->userdata('name');
        $isAdmin = ($this->session->userdata('name') == 'admin');
        $isMaster = isset($this->ismaster) ? $this->ismaster : 0;
        if (($isAdmin || $isMaster) && $this->input->post('managername')) {
            $owner = $this->input->post('managername');
        } else {
            $owner = $managername;
        }
        // File upload config
        $config['upload_path'] = FCPATH . 'uploads/imports/';
        if (!is_dir($config['upload_path'])) {
            mkdir($config['upload_path'], 0755, true);
        }
        $config['allowed_types'] = 'csv';
        $config['max_size'] = 4096;
        $config['encrypt_name'] = TRUE;
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('import_file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
            redirect('import/ImportUsers');
        }
        $fileData = $this->upload->data();
        $filePath = $fileData['full_path'];
        // Log import start
        $import_id = $this->ImportUsers_model->logImport($owner, $fileData['file_name']);
        
        // Process CSV (Validation Pass Only)
        $result = $this->ImportUsers_model->validateCSV($filePath, $owner, $import_id);
        
        if ($result['status'] === 1) {
            // Validation succeeded, store data in session for AJAX processing
            $this->session->set_userdata('import_data', $result['rows_to_process']);
            $this->session->set_userdata('import_log_id', $import_id);
            $this->session->set_userdata('import_owner', $owner);
            $this->session->set_userdata('import_total', count($result['rows_to_process']));
            
            // Set flag to trigger AJAX import on page load
            $this->session->set_flashdata('start_import', true);
            $this->session->set_flashdata('import_message', 'File validated successfully. Starting import of ' . count($result['rows_to_process']) . ' users...');
        } else {
            // Update log to failed
            $this->ImportUsers_model->updateImportLog($import_id, 0);
            $this->session->set_flashdata('error', 'Import validation failed. ' . $result['error_message']);
        }
        redirect('import/ImportUsers');
    }

    public function process_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $import_data = $this->session->userdata('import_data');
        $import_log_id = $this->session->userdata('import_log_id');
        $owner = $this->session->userdata('import_owner');

        if (empty($import_data)) {
            // Completed!
            if ($import_log_id) {
                $this->ImportUsers_model->updateImportLog($import_log_id, 1);
                $this->session->unset_userdata(['import_data', 'import_log_id', 'import_owner', 'import_total']);
            }
            echo json_encode(['status' => 'complete']);
            return;
        }

        // Pop one record off the array
        $record = array_shift($import_data);
        
        // Update session with remaining data
        $this->session->set_userdata('import_data', $import_data);

        // Import the single user
        $result = $this->ImportUsers_model->importSingleUser($record, $owner);

        // Return status
        echo json_encode([
            'status' => 'processing',
            'username' => $record['username'],
            'success' => $result['success'],
            'message' => $result['message'],
            'remaining' => count($import_data)
        ]);
        return;
    }

    public function sample_csv()
    {
        $filename = 'sample_import_users.csv';
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fputcsv($output, ['username','password','firstname','lastname','address','mobile','email','taxid','expiration','service_id']);
        fputcsv($output, [
            'johndoe',
            'password123',
            'John',
            'Doe',
            '123 Main St',
            '="923001234567"', // mobile as text
            'john@example.com',
            '="1234567890"',   // taxid as text
            '2025-12-31',
            '46'
        ]);
        fclose($output);
        exit;
    }

    /**
     * Display import logs with role-based access control
     */
    public function logs()
    {
        $managername = $this->session->userdata('name');
        $isAdmin = ($managername == 'admin');
        $isMaster = isset($this->ismaster) ? $this->ismaster : 0;
        $managerChain = $this->session->userdata('manager_chain') ?: [];
        
        $data['logs'] = $this->ImportUsers_model->getImportLogs($managername, $isAdmin, $isMaster, $managerChain);
        $data['isAdmin'] = $isAdmin;
        $data['isMaster'] = $isMaster;
        
        $this->global['pageTitle'] = 'Import Logs';
        $this->loadViews('import/import_logs', $this->global, $data, NULL);
    }

    /**
     * Delete import log via AJAX
     */
    public function deleteLog()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $logId = $this->input->post('log_id');
        if (!$logId) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Log ID is required']));
            return;
        }
        
        $managername = $this->session->userdata('name');
        $isAdmin = ($managername == 'admin');
        $isMaster = isset($this->ismaster) ? $this->ismaster : 0;
        $managerChain = $this->session->userdata('manager_chain') ?: [];
        
        $result = $this->ImportUsers_model->deleteImportLog($logId, $managername, $isAdmin, $isMaster, $managerChain);
        
        if ($result) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => true, 'message' => 'Log deleted successfully']));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Failed to delete log or insufficient permissions']));
        }
    }

    /**
     * Get import log details via AJAX
     */
    public function getLogDetails()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $logId = $this->input->post('log_id');
        if (!$logId) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Log ID is required']));
            return;
        }
        
        $managername = $this->session->userdata('name');
        $isAdmin = ($managername == 'admin');
        $isMaster = isset($this->ismaster) ? $this->ismaster : 0;
        $managerChain = $this->session->userdata('manager_chain') ?: [];
        
        $log = $this->ImportUsers_model->getImportLogById($logId, $managername, $isAdmin, $isMaster, $managerChain);
        
        if ($log) {
            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => true, 'log' => $log]));
        } else {
            $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'message' => 'Log not found or insufficient permissions']));
        }
    }

    /**
     * Download the original imported CSV file for a log
     * @param int $logId
     */
    public function downloadCsv($logId)
    {
        $managername = $this->session->userdata('name');
        $isAdmin = ($managername == 'admin');
        $isMaster = isset($this->ismaster) ? $this->ismaster : 0;
        $managerChain = $this->session->userdata('manager_chain') ?: [];

        $log = $this->ImportUsers_model->getImportLogById($logId, $managername, $isAdmin, $isMaster, $managerChain);
        if (!$log) {
            show_error('You do not have permission to download this file.', 403);
            return;
        }

        $filePath = FCPATH . 'uploads/imports/' . $log->importfile;
        if (!file_exists($filePath)) {
            show_error('File not found.', 404);
            return;
        }

        // Clean output buffer
        if (ob_get_level()) {
            ob_end_clean();
        }
        
        $this->output
            ->set_content_type('text/csv')
            ->set_header('Content-Disposition: attachment; filename="' . basename($filePath) . '"')
            ->set_output(file_get_contents($filePath));
    }
} 
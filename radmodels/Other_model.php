<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Other_model extends CI_Model
{

    function naslistingDynamicCount($searchText = '')
    {
        $managername = $this->session->userdata ( 'name' );

        $this->db->select('B.nasid, B.shortname, B.nasname, B.domain, B.managername, B.description, date(B.createdDtm) as createdDtm');
        $this->db->from('nas as A');
        $this->db->join('tbl_dynamicnas as B', 'A.nasname = B.nasname', 'left');

        if(!empty($searchText)) {
            $likeCriteria = "(B.nasname  LIKE '%".$searchText."%'
                            OR  B.domain  LIKE '%".$searchText."%'
                            OR  B.description  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }

        if($managername <> "admin")
            $this->db->where('B.managername = ', $managername);

        $query = $this->db->get();
        
        return $query->num_rows();
    }

    function naslistingDynamic($searchText = '', $page, $segment)
    {
        $managername = $this->session->userdata ( 'name' );

        $this->db->query('Set @row_number = 0');

        $this->db->select('B.nasid, A.shortname, A.nasname, ifnull(B.domain, "") as domain, ifnull(B.managername, "") as managername, 
                            ifnull(B.description, "") as description, ifnull(B.authmode, "") as authmode, 
                            ifnull(B.apiuser, "") as apiuser, ifnull(B.apipasswd, "") as apipasswd, ifnull(B.apiport, "") as apiport, 
                            date(B.createdDtm) as createdDtm');
        $this->db->from('nas as A');
        $this->db->join('tbl_dynamicnas as B', 'A.nasname = B.nasname', 'left');

        if(!empty($searchText)) {
            $likeCriteria = "(B.nasname  LIKE '%".$searchText."%'
                            OR  B.domain  LIKE '%".$searchText."%'
                            OR  B.description  LIKE '%".$searchText."%')";
            $this->db->where($likeCriteria);
        }
        
        if($managername <> "admin")
            $this->db->where('B.managername = ', $managername);

        $this->db->order_by('B.createdDtm', 'DESC');
        $this->db->limit($page, $segment);
        $query = $this->db->get();
        
        $result = $query->result();
        return $result;

    }

    function getNasList()
    {
        $this->db->select('id, shortname, nasname, secret');
        $this->db->from('nas');

        $query = $this->db->get();
        
        return $query->result();
    }

    function allowNasToManagers($nasid){

        $this->db->query('Insert into rm_allowednases  (select srvid as srvid, '.$nasid.' as nasid from rm_services 
                        where srvid not in (select srvid from rm_allowednases where srvid = rm_services.srvid and nasid = '.$nasid.') 
                        order by srvid,nasid)');

    }

    function deleteNasToManagers($nasname){

        $this->db->query('delete from rm_allowednases where nasid IN (Select id from nas where nasname = "'.$nasname.'")');

    }

    function getNASIP($shortname){

        $this->db->select('nasname');
        $this->db->from('nas as A');
        $this->db->where('shortname', $shortname);
        
        $query = $this->db->get();

        return $query->row();

    }

    function get_newCardActivation($day = 1)
    {
        $this->db->select('A.username, min(A.nasipaddress) as nasipaddress, min(A.calledstationid) as calledstationid,
                        min(A.radacctid) as radacctid');
        $this->db->from('radacct as A');
        $this->db->where('acctstarttime >= (NOW() - INTERVAL '.$day.' day)');
        $this->db->group_by('username');
        $query = $this->db->get();

        return $query->result();
    }

    function getDDNSMapping()
    {
        $this->db->select('shortname, nasname, domain');
        $this->db->from('tbl_dynamicnas');

        $query = $this->db->get();

        return $query->result();
    }

    function getDDNSMappingInfo($nasipaddress)
    {
        $this->db->select('shortname, nasname, domain, apiuser, apipasswd, apiport, authmode');
        $this->db->from('tbl_dynamicnas');
        $this->db->where('nasname = "'.$nasipaddress.'"');

        $query = $this->db->get();
        
        return $query->row();
    }

    function checkNasMappingExists($nasname)
    {
        $this->db->select('nasname');
        $this->db->from("nas");
        $this->db->where('nasname', $nasname);

        $query = $this->db->get();

        if ($query->num_rows() > 0){
            return true;
        } else {
            return false;
        }
    }

    function addNasNew($nasInfo)
    {
        $this->db->trans_start();
        $this->db->insert('nas', $nasInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return True;
    }

    function addNasDynamic($apiInfo)
    {
        $this->db->trans_start();
        $this->db->insert('tbl_dynamicnas', $apiInfo);
        
        $insert_id = $this->db->insert_id();
        
        $this->db->trans_complete();
        
        return True;
    }

    // Edit Dynamic NAS Information
    function editDynamicNasTable($nasInfo, $shortname, $oldDomainIP){

        $this->db->where('shortname', $shortname);
        $this->db->where('nasname', $oldDomainIP);
        $this->db->update('tbl_dynamicnas', $nasInfo);

        return TRUE;

    }

   /**
 * Accept numeric nas.id or legacy tbl_dynamicnas.nasid
 */
public function getDynamicNasInfo($nasId)
{
    // Attempt join by numeric nas.id first
    if (ctype_digit((string)$nasId)) {
        $row = $this->db
            ->select('td.*') // return all columns from tbl_dynamicnas
            ->from('tbl_dynamicnas td')
            ->join('nas n', 'n.nasname = td.nasname', 'left')
            ->where('n.id', (int)$nasId)
            ->get()
            ->row();
        if ($row) {
            // Normalize common names in PHP so controller can rely on consistent properties
            if (!isset($row->apiuser) && isset($row->apiusername)) $row->apiuser = $row->apiusername;
            if (!isset($row->apipasswd) && isset($row->apipassword)) $row->apipasswd = $row->apipassword;
            if (!isset($row->apiport) && isset($row->ports)) $row->apiport = $row->ports;
            return $row;
        }
    }

    // Fallback: lookup by tbl_dynamicnas.nasid (legacy string)
    $row = $this->db
        ->select('td.*')
        ->from('tbl_dynamicnas td')
        ->where('td.nasid', $nasId)
        ->get()
        ->row();

    if ($row) {
        if (!isset($row->apiuser) && isset($row->apiusername)) $row->apiuser = $row->apiusername;
        if (!isset($row->apipasswd) && isset($row->apipassword)) $row->apipasswd = $row->apipassword;
        if (!isset($row->apiport) && isset($row->ports)) $row->apiport = $row->ports;
    }

    return $row;
}

    function updateNasToNewIP($nasInfo, $shortname, $oldDomainIP){
        //$this->db->where('shortname', $shortname);
        //$this->db->where('nasname', $oldDomainIP);
        //$this->db->update('nas', $nasInfo);
        
        //return TRUE;
    }

    function updateDynamicNasTable($nasInfo, $shortname, $oldDomainIP){

        $this->db->where('shortname', $shortname);
        $this->db->where('nasname', $oldDomainIP);
        $this->db->update('tbl_dynamicnas', $nasInfo);

        return TRUE;

    }

    function deleteNas($nasname){
        $this->db->where('nasname', $nasname);
        $this->db->delete('nas');
        return $this->db->affected_rows();
    }

    function deleteNasMapping($nasname){
        $this->db->where('nasname', $nasname);
        $this->db->delete('tbl_dynamicnas');
        return $this->db->affected_rows();
    }

    function temp_table_create(){

        $this->db->query('DROP TABLE IF EXISTS temp_fairusealert');
        $this->db->query('Create Table temp_fairusealert as (select users.username, users.firstname, users.lastname, users.mobile, 
                        users.owner, Round(sum(radacct.acctinputoctets)/1073741824, 0) as upload, 
                        Round(sum(radacct.acctoutputoctets)/1073741824, 0) as download from rm_users as users 
                        left join radacct on users.username = radacct.username 
                        where radacct.acctstarttime >= now() - interval 30 day 
                        group by users.username having download > 1200 Order By download DESC)');

        $this->db->query('DROP TABLE IF EXISTS temp_salessummery');
        $this->db->query('Create Table temp_salessummery as select managername, max(createdDtm) as entrydate,
                        CONCAT(month(createdDtm)) as salesmonth,
                        sum(if(createdDtm >= now() - interval 180 day and invtype = "Recharge", abs(amount),0)) as recharge,
                        sum(if(createdDtm >= now() - interval 180 day and invtype = "Credit", abs(amount),0)) as credit,
                        sum(if(createdDtm >= now() - interval 180 day and expdate = "0000-00-00 00:00:00", abs(amount),0)) as refund 
                        from tbl_invoices group by managername, salesmonth having recharge > 0');

    }

    function get_nasmanager($nasipaddress){

        $this->db->select('managername');
        $this->db->from('tbl_managergroup as A');
        $this->db->where('grpname in (select shortname from nas where nasname = "'.$nasipaddress.'")');

        $query = $this->db->get();

        return $query->row();
    }
    function get_callingStationID($username){

        $this->db->select('username, max(callingstationid) as callingstationid, max(nasipaddress) as nasipaddress, max(radacctid) as radacctid');
        $this->db->from("radacct");
        $this->db->where('calledstationid in (select managername from rm_managers)');
        $this->db->where('username', $username);

        $query = $this->db->get();
        return $query->row();
    }

    function get_onlineUserDetails($username){

        $this->db->select('username, nasipaddress, nasportid, nasporttype, acctstarttime, acctstoptime, acctauthentic, calledstationid,
                            BaseTbl.framedipaddress, NasTbl.secret');
        $this->db->from('radacct as BaseTbl');
        $this->db->join('nas as NasTbl', 'BaseTbl.nasipaddress = NasTbl.nasname','left');
        $this->db->where('BaseTbl.username', $username);
        $this->db->where('isnull(BaseTbl.acctstoptime) = true', null, false);

        $query = $this->db->get();
        $result = $query->row();
        return $result;
    }


    function updateVoucherOwner($username, $calledstationid, $mode){

        if($mode == 1){
            $this->db->query('Update rm_users set owner = "'.$calledstationid.'" where username = "'.$username.'" 
                and owner <> "'.$calledstationid.'" and owner in 
                (select managername from rm_managers where managername = "'.$calledstationid.'")');
        }elseif($mode == 0){
            $this->db->query('');
        }

    }

    function addDynamicRecharge($username = ""){

        $this->db->query('Insert into tbl_invoices (username, srvid, price, amount, invtype, managername, createdBy, 
                        srvdate, expdate, remarks) select A.username as username, B.srvid as srvid, B.costprice as price, 
                        -B.costprice as amount, "Recharge" as invtype, A.owner as managername, "Auto" as createdBy, 
                        curdate() as srvdate, expiration as expdate, "Auto Debit Voucher" as remarks 
                        from rm_users as A left join tbl_services as B on A.owner = B.managername and A.srvid = B.radsrvid 
                        where username in (select username from temp_cardsactivation)');

    }

    function updateUserOwnership(){

        $this->db->query('DROP TABLE IF EXISTS temp_cardsactivation');
        $this->db->query('Create Table if not exists temp_cardsactivation as (
                        select username, min(radacctid) as radacctid,min(calledstationid) as owner, min(nasipaddress) as nasipaddress, 
                        min(callingstationid) as callingstationid, min(acctstarttime) as acctstarttime  
                        from radacct where username in (select cardnum from rm_cards where apiaction=0 and active=1) group by username)');

    }

    function getNewCardsNAS()
    {
        $this->db->select('username, nasipaddress, owner, callingstationid, acctstarttime');
        $this->db->from('temp_cardsactivation');
        $query = $this->db->get();
        return $query->result();
    }

    function updateCardAPIAction($username, $apiAction = 1){
        $this->db->query('UPDATE rm_cards set apiaction='.$apiAction.' where cardnum = "'.$username.'"');
    }

    /**
     * Mark a single card as pushed to a NAS using nasid integer field.
     * Returns boolean.
     */
   /**
 * markCardPushedToNas: write integer NAS id into rm_cards.
 * Returns boolean.
 */
public function markCardPushedToNas($cardnum, $nasId)
{
    if (empty($cardnum)) return false;
    $nasId = (int)$nasId;

    // Choose integer column: prefer nasid, allow nassid legacy
    $col = $this->db->field_exists('nasid', 'rm_cards') ? 'nasid' : ($this->db->field_exists('nassid', 'rm_cards') ? 'nassid' : null);
    if ($col === null) {
        return false;
    }

    $this->db->where('cardnum', $cardnum);
    return (bool)$this->db->update('rm_cards', [$col => $nasId]);
}

/**
 * markCardPushedToNasIfUnassigned: atomic compare-and-set on integer NAS column.
 * Returns:
 *   1  = success (updated or already assigned to same NAS)
 *   0  = conflict (already assigned to different NAS)
 *  -1  = error / missing row / no NAS column
 */
public function markCardPushedToNasIfUnassigned($cardnum, $nasId)
{
    if (empty($cardnum)) return -1;
    $nasId = (int)$nasId;

    // Choose integer column: prefer nasid, allow nassid legacy
    $col = $this->db->field_exists('nasid', 'rm_cards') ? 'nasid' : ($this->db->field_exists('nassid', 'rm_cards') ? 'nassid' : null);
    if ($col === null) {
        return -1;
    }

    // Read current value
    $row = $this->db->select($col)->from('rm_cards')->where('cardnum', $cardnum)->limit(1)->get()->row();
    if ($row === null) return -1;
    $current = isset($row->{$col}) ? (int)$row->{$col} : 0;

    // already assigned to same NAS -> success
    if ($current === $nasId && $nasId !== 0) return 1;

    // assigned to different NAS -> conflict
    if ($current !== 0 && $current !== $nasId) return 0;

    // Conditional update: only when column IS NULL or = 0
    $this->db->where('cardnum', $cardnum);
    $this->db->where("({$col} IS NULL OR {$col} = 0)", null, false);
    $this->db->update('rm_cards', [$col => $nasId]);

    if ($this->db->affected_rows() === 1) return 1;

    // Re-check outcome (concurrent assignment may have occurred)
    $row2 = $this->db->select($col)->from('rm_cards')->where('cardnum', $cardnum)->limit(1)->get()->row();
    if ($row2 === null) return -1;
    $now = isset($row2->{$col}) ? (int)$row2->{$col} : 0;
    if ($now === $nasId && $nasId !== 0) return 1;
    if ($now !== 0 && $now !== $nasId) return 0;
    return -1;
}

/**
 * getCardPushStates: returns push state map for given cardnums using integer NAS column when available.
 * Map format: cardnum => ['state' => 'not_pushed'|'pushed_known'|'pushed_unknown', 'nas_id' => int|null, 'nas_label' => null]
 */
/**
 * getCardPushStates: returns push state map for given cardnums using integer NAS column when available.
 * Map format: cardnum => ['state' => 'not_pushed'|'pushed_known'|'pushed_unknown', 'nas_id' => int|null, 'nas_label' => null|string]
 */
public function getCardPushStates(array $cardnums)
{
    if (empty($cardnums)) return [];

    // detect integer NAS column name
    $col = $this->db->field_exists('nasid', 'rm_cards') ? 'nasid' : ($this->db->field_exists('nassid', 'rm_cards') ? 'nassid' : null);

    // select cardnum and the chosen nas column (if any)
    if ($col !== null) {
        $this->db->select('cardnum, ' . $col);
    } else {
        $this->db->select('cardnum');
    }
    $this->db->from('rm_cards');
    $this->db->where_in('cardnum', $cardnums);
    $rows = $this->db->get()->result();

    $map = [];
    $nasIds = [];

    // build basic map and collect nas ids to resolve labels
    foreach ($rows as $r) {
        $nid = null;
        if ($col !== null && isset($r->{$col}) && (int)$r->{$col} > 0) {
            $nid = (int)$r->{$col};
            $map[$r->cardnum] = ['state' => 'pushed_known', 'nas_id' => $nid, 'nas_label' => null];
            $nasIds[$nid] = $nid;
        } else {
            $map[$r->cardnum] = ['state' => 'not_pushed', 'nas_id' => null, 'nas_label' => null];
        }
    }

    // If we found NAS ids, fetch their labels from nas table in one query
    if (!empty($nasIds)) {
        $this->db->select('id, shortname, nasname');
        $this->db->from('nas');
        $this->db->where_in('id', array_values($nasIds));
        $nasRows = $this->db->get()->result();

        $labels = [];
        foreach ($nasRows as $n) {
            $s = trim((string)$n->shortname);
            $host = trim((string)$n->nasname);
            // prefer shortname (host) formatting
            if ($s !== '') $labels[(int)$n->id] = $s . ' (' . $host . ')';
            else $labels[(int)$n->id] = $host !== '' ? $host : 'NAS ' . (int)$n->id;
        }

        // inject labels back into map
        foreach ($map as $cardnum => &$entry) {
            if (!empty($entry['nas_id']) && isset($labels[$entry['nas_id']])) {
                $entry['nas_label'] = $labels[$entry['nas_id']];
            }
        }
        unset($entry);
    }

    // ensure all requested cardnums present
    foreach ($cardnums as $cn) {
        if (!isset($map[$cn])) $map[$cn] = ['state' => 'not_pushed', 'nas_id' => null, 'nas_label' => null];
    }

    return $map;
}


    public function resolveNasLabels(array $nasIds)
    {
        if (empty($nasIds)) return [];
        $this->db->select('id, shortname, nasname')->from('nas')->where_in('id', $nasIds);
        $rows = $this->db->get()->result();
        $labels = [];
        foreach ($rows as $n) {
            $labels[(int)$n->id] = $n->shortname ?: $n->nasname;
        }
        return $labels;
    }

    //Mode = Single User or Manager Based, Type = 1-Activate MacAuth or 0-Deactvate
    function mac_binding_apply($mode = 0, $type = 0, $managername = "", $username = ""){

        if($mode == 0 && $type == 1){

            $this->db->query('
                UPDATE rm_users AS ru
                JOIN (
                    SELECT 
                        ra.username,
                        ra.callingstationid
                    FROM radacct ra
                    INNER JOIN (
                        SELECT 
                            username,
                            MAX(acctstarttime) AS latest_acctstarttime
                        FROM radacct
                        GROUP BY username
                    ) AS latest_ra ON ra.username = latest_ra.username AND ra.acctstarttime = latest_ra.latest_acctstarttime
                ) AS latest_radacct ON ru.username = latest_radacct.username
                SET 
                    ru.mac = latest_radacct.callingstationid,
                    ru.usemacauth = 1
                WHERE 
                    ru.owner = ' . $this->db->escape($managername) . ' and ru.mac = 0 
            ');
        }elseif($mode == 1 && $type == 1){

            $this->db->query('
                UPDATE rm_users AS ru
                JOIN (
                    SELECT 
                        ra.username,
                        ra.callingstationid
                    FROM radacct ra
                    INNER JOIN (
                        SELECT 
                            username,
                            MAX(acctstarttime) AS latest_acctstarttime
                        FROM radacct
                        WHERE username = ' . $this->db->escape($username) . ' 
                        GROUP BY username
                    ) AS latest_ra ON ra.username = latest_ra.username AND ra.acctstarttime = latest_ra.latest_acctstarttime
                ) AS latest_radacct ON ru.username = latest_radacct.username
                SET 
                    ru.mac = latest_radacct.callingstationid,
                    ru.usemacauth = 1
                WHERE 
                    ru.username = ' . $this->db->escape($username) . ' and ru.mac = 0 
            ');
        }elseif($mode == 0 && $type == 0){
            $this->db->query('
                    UPDATE rm_users AS ru
                    JOIN (
                        SELECT username 
                        FROM rm_users 
                        WHERE owner = ' . $this->db->escape($managername) . '
                    ) AS subquery ON ru.username = subquery.username
                    SET ru.usemacauth = 0 where ru.usemacauth = 1
            ');
        }elseif($mode == 1 && $type == 0){
            $this->db->query('
                UPDATE rm_users set usemacauth = 0 where username = ' . $this->db->escape($username) . ' and usemacauth = 1
            ');
        }
    }

    function mac_settings_manager()
    {
        $this->db->select('stgtype, stgvalue, managername');
        $this->db->from('tbl_settings');
        $this->db->where('stgtype', 'BIND-MAC');
        $this->db->where('stgvalue', 1);
        $query = $this->db->get();
        return $query->result();
    }

    function update_deviceId($username, $device_id = "")
    {
        $this->db->query("Update rm_cards set deviceid = '' 
                where cardnum in (Select username from rm_users where expiration <= now()) and deviceid <> ''");

        $this->db->query("Update rm_cards set deviceid = " . $this->db->escape($device_id) . " where cardnum = " . $this->db->escape($username));
    }

    function validate_deviceId($device_id)
    {
        $this->db->select('deviceid');
        $this->db->from("rm_cards");
        $this->db->where('deviceid', $device_id);

        $query = $this->db->get();
        return $query->row();
    }

    function get_deviceid($device_id, $stored_username = '')
    {
        $this->db->select('A.cardnum, B.expiration, A.deviceid, B.owner');
        $this->db->from("rm_cards as A");
        $this->db->join('rm_users as B', 'A.cardnum = B.username', 'left');

        if ($stored_username == '') {
            $this->db->where('A.deviceid', $device_id);
        } else {
            $this->db->where('A.cardnum', $stored_username);
        }

        $query = $this->db->get();
        return $query->row();
    }

    function get_userowner($username)
    {
        $this->db->select('*');
        $this->db->from("users as A");
        $this->db->where('A.username', $username);

        $query = $this->db->get();
        return $query->row();
    }
    function get_cardInfo($cardnum){

        $this->db->select('cardnum, expiration, deviceid');
        $this->db->from("rm_cards");
        $this->db->where('cardnum', $cardnum);

        $query = $this->db->get();
        return $query->row();

    }

    // Android App IP Updater IP
    function add_ipUpdaterCall($data){

        $sql = "
        CREATE TABLE IF NOT EXISTS temp_ipcall_logs (
            callerip VARCHAR(255),
            ip_address VARCHAR(255),
            status INT DEFAULT 0,
            remarks VARCHAR(255),
            createdon TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        );
        ";

        $this->db->query($sql);

        $this->db->insert('temp_ipcall_logs', $data);

    }

    function get_expiredUserDetails(){

        $this->db->select('A.cardnum, B.username, B.expiration, min(C.radacctid) as accid, C.nasipaddress');
        $this->db->from('rm_cards as A');
        $this->db->join('rm_users as B', 'A.cardnum = B.username','left');
        $this->db->join('radacct as C', 'A.cardnum = C.username','left');
        $this->db->where('B.expiration >= now() - interval 24 hour AND B.expiration <= now()');
        $this->db->where('C.radacctid IS NOT NULL');

        $this->db->group_by('A.cardnum');

        $query = $this->db->get();
        return $query->result();
    }

    // JOBS QUEUE FUNCTIONS
    function jobsList() {
        $this->db->order_by('createdDtm', 'DESC');
        $query = $this->db->get('tbl_jobsqueue');
        return $query->result();
    }

    function jobsGet($jobid) {
        $this->db->where('jobid', $jobid);
        $query = $this->db->get('tbl_jobsqueue');
        return $query->row();
    }

    function jobsGet_byname($jobname) {
        $this->db->where('jobname', $jobname);
        $query = $this->db->get('tbl_jobsqueue');
        return $query->row();
    }

    function jobsAdd($data) {
        $this->db->insert('tbl_jobsqueue', $data);
        return $this->db->insert_id();
    }

    function jobsEdit($jobid, $data) {
        $this->db->where('jobid', $jobid);
        return $this->db->update('tbl_jobsqueue', $data);
    }

    function jobsDelete($jobid = null, $jobname = null) {
        if($jobid) {
            $this->db->where('jobid', $jobid);
        }elseif($jobname) {
            $this->db->where('jobname', $jobname);
        }
        return $this->db->delete('tbl_jobsqueue');
    }

    function jobsGetDueJobs() {
        $sql = "SELECT * FROM tbl_jobsqueue WHERE active=1 and jobtype=0 AND DATE_ADD(jobend, INTERVAL jobinterval SECOND) <= NOW()";
        $query = $this->db->query($sql);
        return $query->result();
    }

    public function runDueJobs()
    {
        $CI =& get_instance();
        $base_url = rtrim($CI->config->item('base_url'), '/');
        
        $jobs = $this->jobsGetDueJobs();
        
        foreach ($jobs as $job) {
            $url = $job->url;
            if ((string)$job->jobtype === '0') {
                $url = $base_url . '/' . ltrim($job->url, '/');
            }

            log_message('debug', "Job Started: ID={$job->jobid}, Type={$job->jobtype}, URL={$url}");

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                'Accept-Language: en-US,en;q=0.5',
                'Accept-Encoding: gzip, deflate',
                'Connection: keep-alive',
                'Upgrade-Insecure-Requests: 1'
            ));
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            $curl_errno = curl_errno($ch);
            
            curl_close($ch);

            if ($curl_errno) {
                log_message('error', "Job Failed: ID={$job->jobid}, cURL Error: {$curl_error} (Code: {$curl_errno})");
            } elseif ($http_code >= 400) {
                log_message('error', "Job Failed: ID={$job->jobid}, HTTP Error: {$http_code}, Response: " . substr($response, 0, 200));
            } else {
                log_message('debug', "Job Completed: ID={$job->jobid}, HTTP Code: {$http_code}, Response Length: " . strlen($response));
            }
            
            $this->jobsEdit($job->jobid, array('jobend' => date('Y-m-d H:i:s')));
        }
    }

    public function archive_old_radacct() {
        $this->db->trans_start();
        $sql_insert = "INSERT INTO radacct_archive SELECT * FROM radacct WHERE acctstoptime IS NOT NULL AND acctstoptime < (NOW() - INTERVAL 1 MONTH)";
        $this->db->query($sql_insert);
        $archived = $this->db->affected_rows();
        $sql_delete = "DELETE FROM radacct WHERE acctstoptime IS NOT NULL AND acctstoptime < (NOW() - INTERVAL 1 MONTH)";
        $this->db->query($sql_delete);
        $this->db->trans_complete();
        return $archived;
    }

    public function sync_radacct_to_tbl_radacct()
    {
        $now = date('Y-m-d H:i:s');
        $this->load->database();

        $online_query = $this->db->query('
            SELECT r.username, r.radacctid, r.acctstarttime, r.acctstoptime, r.framedipaddress, r.nasipaddress, r.callingstationid, r.acctsessiontime, r.acctinputoctets, r.acctoutputoctets, u.owner, u.expiration
            FROM radacct r
            JOIN rm_users u ON r.username = u.username
            WHERE r.acctstoptime IS NULL AND u.expiration IS NOT NULL
        ');
        $online_rows = $online_query->result_array();

        $last_session_query = $this->db->query('
            SELECT r1.username, r1.radacctid, r1.acctstarttime, r1.acctstoptime, r1.framedipaddress, r1.nasipaddress, r1.callingstationid, r1.acctsessiontime, r1.acctinputoctets, r1.acctoutputoctets, u.owner, u.expiration
            FROM rm_users u
            JOIN (
                SELECT username, MAX(radacctid) AS max_radacctid
                FROM radacct
                GROUP BY username
            ) rmax ON u.username = rmax.username
            JOIN radacct r1 ON r1.username = rmax.username AND r1.radacctid = rmax.max_radacctid
            WHERE u.expiration IS NOT NULL AND u.expiration >= ?
        ', [$now]);
        $last_session_rows = $last_session_query->result_array();

        $users = [];
        foreach ($last_session_rows as $row) {
            $users[$row['username']] = $row;
        }
        foreach ($online_rows as $row) {
            $users[$row['username']] = $row;
        }

        $batchSize = 1000;
        $offset = 0;
        while (true) {
            $recent_session_query = $this->db->query('
                SELECT r.username, r.radacctid, r.acctstarttime, r.acctstoptime, r.framedipaddress, r.nasipaddress, r.callingstationid, r.acctsessiontime, r.acctinputoctets, r.acctoutputoctets, u.owner, u.expiration
                FROM radacct r
                JOIN rm_users u ON r.username = u.username
                WHERE (r.acctstarttime >= (NOW() - INTERVAL 24 HOUR) OR r.acctstoptime >= (NOW() - INTERVAL 24 HOUR))
                LIMIT ' . $batchSize . ' OFFSET ' . $offset . '
            ');
            $recent_session_rows = $recent_session_query->result_array();
            if (empty($recent_session_rows)) break;
            foreach ($recent_session_rows as $row) {
                if (!isset($users[$row['username']])) {
                    $users[$row['username']] = $row;
                }
            }
            $offset += $batchSize;
        }

        $this->db->query('DROP TEMPORARY TABLE IF EXISTS tbl_radacct_temp');
        $this->db->query('CREATE TEMPORARY TABLE tbl_radacct_temp LIKE tbl_radacct');

        $fields = ['username','radacctid','acctstarttime','acctstoptime','framedipaddress','nasipaddress','callingstationid','acctsessiontime','acctinputoctets','acctoutputoctets','owner','expiration'];
        $batch = [];
        $count = 0;
        foreach ($users as $row) {
            $row_data = [];
            foreach ($fields as $field) {
                $row_data[$field] = $row[$field];
            }
            $batch[] = $row_data;
            if (count($batch) >= 1000) {
                $this->db->insert_batch('tbl_radacct_temp', $batch);
                $count += count($batch);
                $batch = [];
            }
        }
        if (!empty($batch)) {
            $this->db->insert_batch('tbl_radacct_temp', $batch);
            $count += count($batch);
        }

        $update_sql = "UPDATE tbl_radacct t INNER JOIN tbl_radacct_temp temp ON t.username = temp.username SET
            t.radacctid = temp.radacctid,
            t.acctstarttime = temp.acctstarttime,
            t.acctstoptime = temp.acctstoptime,
            t.framedipaddress = temp.framedipaddress,
            t.nasipaddress = temp.nasipaddress,
            t.callingstationid = temp.callingstationid,
            t.acctsessiontime = temp.acctsessiontime,
            t.acctinputoctets = temp.acctinputoctets,
            t.acctoutputoctets = temp.acctoutputoctets,
            t.owner = temp.owner,
            t.expiration = temp.expiration";
        $this->db->query($update_sql);
        $updated = $this->db->affected_rows();

        $insert_sql = "INSERT INTO tbl_radacct (" . implode(",", $fields) . ")
            SELECT temp." . implode(", temp.", $fields) . " FROM tbl_radacct_temp temp
            LEFT JOIN tbl_radacct t ON t.username = temp.username
            WHERE t.username IS NULL";
        $this->db->query($insert_sql);
        $inserted = $this->db->affected_rows();

        $this->db->query('DROP TEMPORARY TABLE IF EXISTS tbl_radacct_temp');

        $delete_sql = "DELETE FROM tbl_radacct WHERE expiration IS NOT NULL AND expiration < (NOW() - INTERVAL 7 DAY)";
        $this->db->query($delete_sql);
        $deleted = $this->db->affected_rows();

        return ['updated' => $updated, 'inserted' => $inserted, 'deleted' => $deleted, 'total_processed' => $count];
    }

}

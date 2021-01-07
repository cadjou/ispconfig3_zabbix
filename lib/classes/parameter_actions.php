<?php

class parameter_actions extends tform_actions
{
    protected $app;

    protected $tpl;

    protected $auth;

    protected $db;

    protected $tform;

    protected $client;

    protected $list_limit = 'limit_monitor,limit_min_check_period,limit_max_retries,limit_max_timeout';

    protected $user_type;

    protected $is_admin;

    protected $is_reseller_for_client;

    protected $db_table;

    protected $db_table_idx;

    protected $table_time_suffixes = [
                                        's' => 1,
                                        'm' => 60,
                                        'h' => 60 * 60,
                                        'd' => 60 * 60 * 24,
                                        'w' => 60 * 60 * 24 * 7,
                                    ];
    protected $regex_time_suffixes = '/(\d+)([s|m|h|d|w])/m';

    protected $debug = false;

    public function __construct($user_type)
    {
        global $app;
        $this->app = $app;
        $this->tpl = $this->app->tpl;
        $this->auth = $this->app->auth;
        $this->db = $this->app->db;
        $this->tform = $this->app->tform;

        $this->user_type = in_array($user_type,['client','reseller','admin']) ? $user_type : 'client';
        $this->is_admin = $this->auth->is_admin();
        $this->is_reseller_for_client = $this->auth->has_clients($_SESSION['s']['user']['userid']) and $this->user_type == 'client';
    }

    function onShow() {
        if ($this->debug) echo '1<br>';
        if (!$this->is_admin and !$this->is_reseller_for_client and $_SESSION['s']['user']['client_id'] <> $this->id) $this->app->error($this->app->lng('error_no_view_permission'));

        $this->db_table = $this->tform->formDef['db_table'];
        $this->db_table_idx = $this->tform->formDef['db_table_idx'];

        $sql = 'SELECT * FROM ?? WHERE ?? = ?';

        if ($this->user_type == 'admin') {
            $_SESSION['zabbix']['parameters']['id'] = 1;
            $this->id = ($this->db->queryOneRecord($sql, $this->db_table, $this->db_table_idx, $this->id)) ? 1 : 0;
        }else{
            $condition_sql = $this->user_type == 'client' ? 'client.limit_client = 0' : 'client.parent_client_id = 0 AND (client.limit_client > 0 OR client.limit_client = -1)';

            $this->client = $this->db->queryOneRecord('SELECT 
            CONCAT(
                IF(client.company_name != "", CONCAT(client.company_name, " :: "), ""),
                IF(client.contact_firstname != "", CONCAT(client.contact_firstname, " "), ""),
                client.contact_name, " (", client.username, 
                IF(client.customer_no != "", CONCAT(", ", client.customer_no), ""), ")"
            ) as name, client_id, sys_userid, sys_groupid, sys_perm_user, sys_perm_group, sys_perm_other
        FROM client 
        WHERE client_id = ? AND ' . $condition_sql,$this->id);

            if ($this->debug) echo '2<br>';
            if(!$this->client or empty($this->client['name'])) $this->app->error($this->app->lng('error_no_view_permission'));

            $this->tpl->setVar('client_name',$this->client['name']);

            $_SESSION['zabbix']['parameters']['id'] = $this->client[$this->db_table_idx];



            $this->id = ($this->db->queryOneRecord($sql, $this->db_table, $this->db_table_idx, $this->id)) ? $this->id : 0;

            $this->tpl->setVar('not_admin',!$this->is_admin);
            $this->tpl->setVar('not_reseler',!($this->is_reseller_for_client or $this->is_admin));
        }
        parent::onShow();
    }

    function onShowNew() {
        $record = [];
        $record = $this->tform->getHTML($record, $this->tform->formDef['tab_default'], 'NEW');
        if($this->client[$this->db_table_idx] or $this->user_type == 'admin'){
            $record[$this->db_table_idx] = $this->user_type == 'admin' ? 1 : $this->client[$this->db_table_idx];

            $this->tpl->setVar($record);
            $this->dataRecord['limit_monitor'] = 'SELECT count(domain_id) as nbr domain FROM web_domain WHERE web_domain.type = \'vhost\' AND `active`="y" AND {AUTHSQL::web_domain} ';
        } else{
            if ($this->debug) echo '3<br>';
            $this->app->error($this->app->lng('error_no_view_permission'));
        }
    }

    function onSubmit() {
        if (!isset($_SESSION['zabbix']['parameters']['id']) or $_SESSION['zabbix']['parameters']['id'] <> $this->dataRecord[$this->db_table_idx]){
            if (isset($_SESSION['zabbix']['parameters']['id'])) unset($_SESSION['zabbix']['parameters']['id']);
            if ($this->debug) echo '4<br>';
            $this->app->error($this->app->lng('error_no_view_permission'));
        }
        if ($this->user_type <> 'admin')  $this->validation_admin_limit();

        parent::onSubmit();
    }

    function onBeforeInsert() {
        if ($_SESSION['zabbix']['parameters']['id'] == $this->dataRecord[$this->db_table_idx]){
            $this->id = $this->dataRecord[$this->db_table_idx];
        } else{
            unset($_SESSION['zabbix']['parameters']['id']);
            if ($this->debug) echo '5<br>';
            $this->app->error($this->app->lng('error_no_view_permission'));
        }

    }

    function onAfterInsert() {
        if ($_SESSION['zabbix']['parameters']['id'] == $this->dataRecord[$this->db_table_idx]){
            $this->id = $this->dataRecord[$this->db_table_idx];
        } else{
            unset($_SESSION['zabbix']['parameters']['id']);
            if ($this->debug) echo '5<br>';
            $this->app->error($this->app->lng('error_no_view_permission'));
        }
    }

    function onAfterUpdate() {
        if ($this->user_type <> 'admin') $this->zabbix_addon_acces('add');
    }

    function onAfterDelete() {
        if ($this->user_type <> 'admin') $this->zabbix_addon_acces('remove');
    }

    protected function parse_time_suffixes($time,$default = null){
        if (!is_numeric($time)) {
            preg_match_all($this->regex_time_suffixes, $time, $matches, PREG_SET_ORDER);
            if (isset($matches[0][1], $matches[0][2], $this->table_time_suffixes[$matches[0][2]])) {
                return $matches[0][1] * $this->table_time_suffixes[$matches[0][2]];
            } else {
                return $default;
            }
        }
        return intval($time);
    }

    protected function to_time_suffixes($second){
        arsort($this->table_time_suffixes);
        foreach ($this->table_time_suffixes as $key=>$item) {
            if (fmod($second,$item) == 0) return ($second/$item) . $key;
        }
        return '0s';
    }

    protected function validation_admin_limit(){
        // TODO : Check default value
        $sql = 'SELECT ' . $this->list_limit . ' FROM zabbix_admin WHERE admin_id=1';
        $admin_limit = $this->db->queryOneRecord($sql);

        if ($this->is_reseller_for_client){
            $sql = 'SELECT ' . $this->list_limit . ' FROM zabbix_client WHERE client_id= ' . $_SESSION['s']['user']['client_id'];
            $reseller_limit = $this->db->queryOneRecord($sql);
        }

        foreach (explode(',',$this->list_limit) as $key) {
            if ($this->is_admin or $this->is_reseller_for_client) {
                if (isset($admin_limit[$key],$this->dataRecord[$key])){
                    $value_admin = $this->parse_time_suffixes($admin_limit[$key],'4w');
                    $value_limit = $this->parse_time_suffixes($this->dataRecord[$key],$value_admin);
                    //echo "$key = $value_admin / $value_limit<br>";
                    $min = count(explode('_min_',$key)) == 2;

                    $adminMresel = $value_admin > $value_limit;
                    $value = (($min xor $adminMresel) or $admin_limit[$key] == '-1') ? $value_limit : $value_admin;

                    if ($this->is_reseller_for_client and isset($reseller_limit[$key])){
                        $value_reseller = $this->parse_time_suffixes($reseller_limit[$key],$value_admin);
                        $reselMclient = $value_reseller > $value;
                        $value = (($min xor $reselMclient) or $reseller_limit[$key] == '-1') ? $value : $value_reseller;
                    }
                    $this->dataRecord[$key] = is_numeric($this->dataRecord[$key]) ? $value :  $this->to_time_suffixes($value) ;
                } else {
                    unset($this->dataRecord[$key]);
                }
            }else{
                foreach (explode(',',$this->list_limit) as $item){
                    unset($this->dataRecord[$item]);
                }
            }
        }
    }

    protected function zabbix_addon_acces($action){
        $users_data = $this->db->queryAllRecords('SELECT userid,modules FROM sys_user WHERE client_id = ?',$this->dataRecord[$this->db_table_idx]);
        $updates = [];
        foreach($users_data as $data){
            $modules = $data['modules'];
            $table = explode('zabbix',$modules);
            if (count($table) == 1 and $action == 'add'){
                $modules .= ',zabbix';
                $updates[$modules][] = $data['userid'];
            } elseif (count($table) == 2 and $action == 'remove'){
                $modules = trim(trim($table[0],',') . ',' . trim($table[0],','),',');
                $updates[$modules][] = $data['userid'];
            }
        }
        $sql = [];
        foreach ($updates as $module_list => $userids){
            $sql[] = 'UPDATE sys_user SET modules = "' . $module_list . '" WHERE userid IN (' . implode(',',$userids) . ')';
        }
        if ($sql){
            $this->db->query(implode(';',$sql));
        }
    }
}
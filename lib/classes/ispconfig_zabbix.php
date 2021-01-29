<?php


class ispconfig_zabbix
{
    private const SHARED_HOST = 'zp.kwa.digital';

    protected $app;

    protected $db;

    protected $auth;

    protected $functions;

    protected $is_reseller;

    protected $is_admin;

    protected $data;

    protected $reseller_id;

    protected $client_id;

    protected $admin_params;

    protected $reseller_params;

    protected $client_params;

    protected $table_time_suffixes = [
        's' => 1,
        'm' => 60,
        'h' => 60 * 60,
        'd' => 60 * 60 * 24,
        'w' => 60 * 60 * 24 * 7,
    ];
    protected $limits = [
        'limit_monitor' => [
            'default' => '-1',
            'type' => 'max'
        ],
        'limit_check_period' => [
                'default' => '4w',
                'type' => 'min'
            ],
        'limit_retries' => [
                'default' => '15',
                'type' => 'max'
            ],
        'limit_max_timeout' => [
                'default' => '2m',
                'type' => 'max'
            ]
    ];

    protected $regex_time_suffixes = '/(\d+)([s|m|h|d|w])/m';

    private $messages;
    /**
     * @var zabbix_manager
     */
    private $zabbix_manager;

    public function __construct($app){
        $this->app = $app;

        $this->db = $this->app->db;
        $this->auth = $this->app->auth;
        $this->functions = $this->app->functions;

        $this->is_admin = $this->auth->is_admin();
        $this->is_reseller = $this->auth->has_clients($_SESSION['s']['user']['userid']);
        $this->client_id = $this->functions->intval($_SESSION['s']['user']['client_id']);
    }

    public function checkAdmin($data){
        $data['zabbix_host'] = $data['zabbix_shared'] == 'y' ? self::SHARED_HOST : $data['zabbix_host'];

        $admin_data = $this->getZabbixParams($data);

        $zabbix_manager = new zabbix_manager($admin_data->getZabbixHost(),$admin_data->getZabbixUser(),$admin_data->getZabbixPwd());
        $zabbix_response = $zabbix_manager->checkConnexion();
        $this->messages = $zabbix_manager->get_messages_html();

        if ($data['zabbix_shared'] == 'y'){
            if (is_array($zabbix_response)) {
                $convertion = [
                    'httptest' => 'limit_monitor',
                    'httptest:delay' => 'limit_check_period',
                    'httptest:retries' => 'limit_retries',
                    'httptest:step:timeout' => 'limit_timeout',
                ];
                foreach ($zabbix_response as $method => $limit) {
                    if (isset($convertion[$method],$admin_data->$convertion[$method])) {
                        $admin_data->$convertion[$method] = $limit;
                    }
                }
            }
        }
        return $admin_data->getArrayData();
    }

    public function checkLimits($data)
    {
        $client_data = $this->getZabbixParams($data);
        $reseller_data = $this->getResellerParams($data['client_id']);
        $admin_data = $this->getAdminParams();

        $client_data = $this->validationLimit($client_data,$admin_data);
        if ($reseller_data){
            $client_data = $this->validationLimit($client_data,$reseller_data);
        }
        return $client_data->getArrayData();
    }

    public function runZabbixLink($data)
    {
        $zp = $this->getZabbixParams($data);

        // MediaType
        $zp->mediaType();
        // UserGroup
        $zp->userGroup();
        // User
        $zp->user();

        if (!$zp->has_messages()){
            if (!empty($data['client_id'])) $this->zabbix_addon_acces('add',$data['client_id']);
            return true;
        }
        return $zp->get_messages_html();
    }

    public function runZabbixUnlink($data)
    {
        return true;
    }

    public function getMessages()
    {
        return $this->messages;
    }

    protected function getZabbixParams($data){
        $clientInfo = $reseller = $admin = [];
        if(isset($data['client_id'])){
            $sql = 'SELECT company_name,contact_firstname,contact_name,email,parent_client_id,username FROM client WHERE client_id = ?';
            $clientInfo = $this->db->queryOneRecord($sql,$data['client_id']);

            if (!empty($clientInfo['parent_client_id'])){
                $reseller = $this->getResellerParams($clientInfo['parent_client_id']);
            }

            $admin = $this->getAdminParams();
        }
        return new zabbix_parameter($data,$clientInfo,$reseller,$admin);
    }


    protected function getAdminParams(){
        if ($this->admin_params) $this->admin_params;

        $sql = 'SELECT * FROM zabbix_server WHERE server_id = 1';
        $this->admin_params = $this->getZabbixParams($this->db->queryOneRecord($sql));
        return $this->admin_params;
    }

    protected function getResellerParams($client_id)
    {
        if ($this->reseller_params) return $this->reseller_params;

        if (!$this->auth->has_clients($client_id)){
            $response = $this->db->queryOneRecord('SELECT parent_client_id FROM client WHERE client.client_id = ?', $client_id);
            if (!empty($response['parent_client_id'])){
                $this->reseller_params = $this->getZabbixParams($this->db->queryOneRecord('SELECT * FROM zabbix_client WHERE client_id = ?', $response['parent_client_id']));
                return $this->reseller_params;
            }
            else {
                $this->messages = '<br> Not find the reseller';
            }
        }
        return false;
    }

    protected function zabbix_addon_acces($action,$client_id){
        $users_data = $this->db->queryAllRecords('SELECT userid,modules FROM sys_user WHERE client_id = ?',$client_id);
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

    public function getClientParams($client_id){
        $sql = 'SELECT * FROM zabbix_client WHERE client_id = ?';
        $this->client_params = $this->getZabbixParams($this->db->queryOneRecord($sql,$this->client_id));
        return $this->client_params;
    }

    public function getMonitorByClient($client_id){
        if ($this->is_admin or $this->is_reseller or !$this->client_id) return [];

        $sql = 'SELECT * FROM zabbix_client WHERE client_id = ?';
        $this->client_params = $this->db->queryOneRecord($sql,$this->client_id);
        return $this->client_params;
    }

    /**
     * @param zabbix_parameter $values
     * @param zabbix_parameter $limits
     * @return zabbix_parameter
     */
    protected function validationLimit(zabbix_parameter $values, zabbix_parameter $limits){
        foreach ($this->limits as $key=>$data) {
            $value_master = $this->parse_time_suffixes($limits->$key ? $limits->$key : $data['default'] ,$data['default']);
            $value_slave = $this->parse_time_suffixes($values->$key ? $values->$key : $data['default'] ,$data['default']);

            $min = $data['type'] == 'min';

            $masterMslave = $value_master > $value_slave;
            $value = (($min xor $masterMslave) or $values[$key] == '-1') ? $value_slave : $value_master;

            $values->$key = gettype($data['default']) == 'string' ? $this->to_time_suffixes($value)  : $value;
        }
        return $values;
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
}
<?php

class zabbix_monitor
{
    // Global Parameters
    /**
     * @var string
     */
    private $isp_glue;

    /**
     * @var string
     */
    private $isp_keyword;

    /**
     * @var string
     */
    private $httptest_keyword;

    /**
     * @var string
     */
    private $step_keyword;

    /**
     * @var string
     */
    private $trigger_keyword;

    /**
     * @var string
     */
    private $smtp_server;

    /**
     * @var Integer
     */
    private $smtp_port;

    /**
     * @var Integer
     */
    private $smtp_ssl;

    /**
     * @var string
     */
    private $smtp_email;

    /**
     * @var string
     */
    private $smtp_user;

    /**
     * @var string
     */
    private $smtp_pwd;

    /**
     * @var string
     */
    private $smtp_message_alert_subject;

    /**
     * @var string
     */
    private $smtp_message_alert_content;

    /**
     * @var string
     */
    private $smtp_message_recovery_subject;

    /**
     * @var string
     */
    private $smtp_message_recovery_content;

    /**
     * @var string
     */
    private $limit_def_check_period;

    /**
     * @var string
     */
    private $limit_max_check_period;

    /**
     * @var string
     */
    private $limit_min_check_period;

    /**
     * @var Integer
     */
    private $limit_def_retries;

    /**
     * @var Integer
     */
    private $limit_max_retries;

    /**
     * @var Integer
     */
    private $limit_min_retries;

    /**
     * @var string
     */
    private $limit_def_timeout;

    /**
     * @var string
     */
    private $limit_max_timeout;

    /**
     * @var string
     */
    private $limit_min_timeout;

    /**
     * @var Integer
     */
    private $limit_def_status_codes;

    /**
     * @var Integer
     */
    private $limit_max_status_codes;

    /**
     * @var Integer
     */
    private $limit_min_status_codes;

    // Data parameters
    /**
     * @var String
     */
    private $server_name;

    /**
     * @var Integer
     */
    private $reseller_id;

    /**
     * @var Integer
     */
    private $client_id;

    /**
     * @var String
     */
    private $client_name;

    /**
     * @var String
     */
    private $client_email;

    /**
     * @var String
     */
    private $client_pwd;

    /**
     * @var Integer
     */
    private $monitor_id;

    /**
     * @var String
     */
    private $monitor_protocol;

    /**
     * @var String
     */
    private $monitor_domain;

    /**
     * @var String
     */
    private $monitor_path;

    /**
     * @var String
     */
    private $monitor_check_period;

    /**
     * @var Integer
     */
    private $monitor_retries;

    /**
     * @var Integer
     */
    private $monitor_active;

    /**
     * @var String
     */
    private $monitor_timeout;

    /**
     * @var String
     */
    private $monitor_string_search;

    /**
     * @var Integer
     */
    private $monitor_status_codes;

    // ID in Zabbix server
    /**
     * @var Integer
     */
    private $hostgroup_id;

    /**
     * @var Integer
     */
    private $host_id;

    /**
     * @var Integer
     */
    private $httptest_id;

    /**
     * @var Integer
     */
    private $item_trend_id;

    /**
     * @var Integer
     */
    private $trigger_id;

    /**
     * @var Integer
     */
    private $usergroup_id;

    /**
     * @var Integer
     */
    private $user_id;

    /**
     * @var Integer
     */
    private $smtp_id;

    /**
     * @var Integer
     */
    private $action_id;

    public function __construct($data)
    {
        // Check parameter in array $data
        $list_data = 'server_name,parent_client_id,client_id,parent_client_id,client_id,contact_name,email,' .
            'insertID,ssl,domain,url_path,check_period,retries,active,timeout,string_search,code_status';
        $missing_param = [];
        foreach (explode(',', $list_data) as $param) {
            if (!isset($data[$param])) {
                $missing_param[] = $param;
            }
        }
        // Die if missing parameter in $data
        if ($missing_param) die('Missing Parameters : ' . nl2br(print_r($missing_param, true)));


        // Instance global parameters
        $this->getGlobalParameters();

        // Instance Data
        $this->setServerName($data['server_name'])
            ->setResellerId($data['parent_client_id'], $data['client_id'])
            ->setClientId($data['client_id'])
            ->setClientName($data['contact_name'])
            ->setClientEmail($data['email'])
            ->setClientPwd()
            ->setMonitorId($data['insertID'])
            ->setMonitorProtocol($data['ssl'])
            ->setMonitorDomain($data['domain'])
            ->setMonitorPath($data['url_path'])
            ->setMonitorCheckPeriod($data['check_period'])
            ->setMonitorRetries($data['retries'])
            ->setMonitorActive($data['active'])
            ->setMonitorTimeout($data['timeout'])
            ->setMonitorStringSearch($data['string_search'])
            ->setMonitorStatusCodes($data['code_status']);
    }

    protected function getGlobalParameters()
    {
        if (!is_file(__DIR__ . '/../init_module.php')) die('Missing Config.php');

        include __DIR__ . '/../init_module.php';

        if (!isset($config)) die('Error in Config.php');

        $list = 'isp_glue,isp_keyword,isp_keyword,httptest_keyword,step_keyword,trigger_keyword,' .
            'smtp_server,smtp_port,smtp_ssl,smtp_email,smtp_user,smtp_pwd,' .
            'smtp_message_alert_subject,smtp_message_alert_content,smtp_message_recovery_subject,smtp_message_recovery_content,' .
            'limit_def_check_period,limit_max_check_period,limit_min_check_period,' .
            'limit_def_retries,limit_max_retries,limit_min_retries,' .
            'limit_def_timeout,limit_max_timeout,limit_min_timeout,' .
            'limit_def_status_codes,limit_max_status_codes,limit_min_status_codes';
        $missing_param = [];
        foreach (explode(',', $list) as $param) {
            if (isset($config[$param])) {
                $this->$param = $config[$param];
            } else {
                $missing_param[] = $param;
            }
        }
        // Die if missing parameter in $data
        if ($missing_param) die('Missing Parameters : ' . nl2br(print_r($missing_param, true)));

        return true;
    }

    /**
     * @return string
     */
    public function getHostgroupName(): string
    {
        return implode($this->isp_glue, [$this->isp_keyword, $this->getServerName(), $this->getResellerId()]);
    }

    /**
     * @return string
     */
    public function getHostClientId(): string
    {
        return implode($this->isp_glue, [$this->isp_keyword, $this->getServerName(), $this->getClientId()]);
    }

    /**
     * @return string
     */
    public function getHostClientName(): string
    {
        return implode($this->isp_glue, [$this->isp_keyword, $this->getServerName(), $this->getClientName()]);
    }

    /**
     * @return string
     */
    public function getHttptestName(): string
    {
        return implode($this->isp_glue, [$this->isp_keyword, $this->getMonitorId(), $this->httptest_keyword, $this->getMonitorDomain()]);
    }

    /**
     * @return array
     */
    public function getHttptestConf(): array
    {
        return [
            'name' => $this->getHttptestName(),
            'hostid' => $this->getHostId(),
            'delay' => $this->getMonitorCheckPeriod(),
            'retries' => $this->getMonitorRetries(),
            'status' => $this->getMonitorActive(),
        ];
    }

    /**
     * @return string
     */
    public function getMonitorUrl(): string
    {
        return $this->getMonitorProtocol() . '://' . $this->getMonitorDomain() . $this->getMonitorPath();
    }

    /**
     * @return string
     */
    public function getMonitorName(): string
    {
        return $this->step_keyword . ' ' . $this->getMonitorDomain();
    }

    /**
     * @return array
     */
    public function getStepConf(): array
    {
        return [
            'name' => $this->getMonitorName(),
            'url' => $this->getMonitorUrl(),
            'timeout' => $this->getMonitorTimeout(),// $monitor_timeout,
            'required' => $this->getMonitorStringSearch(), // $monitor_string_search,
            'status_codes' => strval($this->getMonitorStatusCodes()),// $monitor_status_codes,
            'follow_redirects' => '1',
            'no' => '1',
            'retrieve_mode' => '0',
        ];
    }

    /**
     * @return string
     */
    public function getTriggerName(): string
    {
        return $this->trigger_keyword . ' : ' . $this->getMonitorDomain();
    }

    /**
     * @return string
     */
    public function getUsergroupName(): string
    {
        return implode($this->isp_glue, [$this->isp_keyword, $this->getServerName(), $this->getResellerId()]);
    }

    /**
     * @return string
     */
    public function getUserName(): string
    {
        return implode($this->isp_glue, [$this->isp_keyword, $this->getServerName(), $this->getClientName()]);
    }

    /**
     * @return string
     */
    public function getUserAlias(): string
    {
        return implode($this->isp_glue, [$this->isp_keyword, $this->getServerName(), $this->getClientId()]);
    }

    /**
     * @return string
     */
    public function getUserPwd(): string
    {
        return $this->getClientPwd();
    }

    /**
     * @return array
     */
    public function getMessageConf(): Array
    {
        return [
            'alert' => [
                'subject' => $this->getSmtpMessageAlertSubject(),
                'message' => $this->getSmtpMessageAlertContent(),
            ],
            'recovery' => [
                'subject' => $this->getSmtpMessageRecoverySubject(),
                'message' => $this->getSmtpMessageRecoveryContent(),
            ],
        ];
    }

    /**
     * @param String $variable
     * @param mixed $value
     * @return NULL|mixed
     */
    public function _set(string $variable, $value)
    {
        if (isset($this->$variable)) {
            $this->$variable = $value;
            return $this->$variable;
        }
        return null;
    }

    /**
     * @param String $var
     * @return NULL|mixed
     */
    public function _get(string $var)
    {
        if (isset($this->$var)) return $this->server_name;
        return null;
    }

    /**
     * @return String
     */
    public function getServerName(): string
    {
        return $this->server_name;
    }

    /**
     * @param String $server_name
     * @return zabbix_monitor
     */
    protected function setServerName(string $server_name): zabbix_monitor
    {
        $this->server_name = $server_name;
        return $this;
    }

    /**
     * @return int
     */
    public function getResellerId(): int
    {
        return $this->reseller_id;
    }

    /**
     * @param int $parent_client_id
     * @param int $client_id
     * @return zabbix_monitor
     */
    protected function setResellerId(int $parent_client_id, int $client_id): zabbix_monitor
    {
        $this->reseller_id = $parent_client_id == 0 ? $client_id : $parent_client_id;
        return $this;
    }

    /**
     * @return int
     */
    public function getClientId(): int
    {
        return $this->client_id;
    }

    /**
     * @param int $client_id
     * @return zabbix_monitor
     */
    protected function setClientId(int $client_id): zabbix_monitor
    {
        $this->client_id = $client_id;
        return $this;
    }

    /**
     * @return String
     */
    public function getClientName(): string
    {
        return $this->client_name;
    }

    /**
     * @param String $client_name
     * @return zabbix_monitor
     */
    protected function setClientName(string $client_name): zabbix_monitor
    {
        $this->client_name = $client_name;
        return $this;
    }

    /**
     * @return String
     */
    public function getClientEmail(): string
    {
        return $this->client_email;
    }

    /**
     * @param String $client_email
     * @return zabbix_monitor
     */
    protected function setClientEmail(string $client_email): zabbix_monitor
    {
        $this->client_email = $client_email;
        return $this;
    }

    /**
     * @return String
     */
    public function getClientPwd(): string
    {
        return $this->client_pwd;
    }

    /**
     * @return zabbix_monitor
     */
    protected function setClientPwd(): zabbix_monitor
    {
        $this->client_pwd = md5($this->getClientName() . time());
        return $this;
    }

    /**
     * @return int
     */
    public function getMonitorId(): int
    {
        return $this->monitor_id;
    }

    /**
     * @param int $monitor_id
     * @return zabbix_monitor
     */
    protected function setMonitorId(int $monitor_id): zabbix_monitor
    {
        $this->monitor_id = $monitor_id;
        return $this;
    }

    /**
     * @return String
     */
    public function getMonitorProtocol(): string
    {
        return $this->monitor_protocol;
    }

    /**
     * @param String $ssl
     * @return zabbix_monitor
     */
    protected function setMonitorProtocol(string $ssl): zabbix_monitor
    {
        $this->monitor_protocol = $ssl == 'y' ? 'https' : 'http';
        return $this;
    }

    /**
     * @return String
     */
    public function getMonitorDomain(): string
    {
        return $this->monitor_domain;
    }

    /**
     * @param String $monitor_domain
     * @return zabbix_monitor
     */
    protected function setMonitorDomain(string $monitor_domain): zabbix_monitor
    {
        $this->monitor_domain = $monitor_domain;
        return $this;
    }

    /**
     * @return String
     */
    public function getMonitorPath(): string
    {
        return $this->monitor_path;
    }

    /**
     * @param String $url_path
     * @return zabbix_monitor
     */
    protected function setMonitorPath(string $url_path): zabbix_monitor
    {
        $this->monitor_path = !empty($url_path) ? $url_path : '/';
        return $this;
    }

    /**
     * @return String
     */
    public function getMonitorCheckPeriod(): string
    {
        return $this->monitor_check_period;
    }

    /**
     * @param String $monitor_check_period
     * @return zabbix_monitor
     * TODO : Make Min Max
     */
    protected function setMonitorCheckPeriod(string $monitor_check_period): zabbix_monitor
    {
        $this->monitor_check_period = $monitor_check_period;
        return $this;
    }

    /**
     * @return int
     */
    public function getMonitorRetries(): int
    {
        return $this->monitor_retries;
    }

    /**
     * @param int $monitor_retries
     * @return zabbix_monitor
     */
    protected function setMonitorRetries(int $monitor_retries): zabbix_monitor
    {
        $this->monitor_retries = max($this->getLimitMinRetries(),min($this->getLimitMaxRetries(),$monitor_retries));
        return $this;
    }

    /**
     * @return int
     */
    public function getMonitorActive(): int
    {
        return $this->monitor_active;
    }

    /**
     * @param string $active
     * @return zabbix_monitor
     */
    protected function setMonitorActive(string $active): zabbix_monitor
    {
        $this->monitor_active = $active == 'y' ? 0 : 1;
        return $this;
    }

    /**
     * @return String
     */
    public function getMonitorTimeout(): string
    {
        return $this->monitor_timeout;
    }

    /**
     * @param String $monitor_timeout
     * @return zabbix_monitor
     * TODO : Make Min Max
     */
    protected function setMonitorTimeout(string $monitor_timeout): zabbix_monitor
    {
        $this->monitor_timeout = $monitor_timeout;
        return $this;
    }

    /**
     * @return String
     */
    public function getMonitorStringSearch(): string
    {
        return $this->monitor_string_search;
    }

    /**
     * @param String $monitor_string_search
     * @return zabbix_monitor
     */
    protected function setMonitorStringSearch(string $monitor_string_search): zabbix_monitor
    {
        $this->monitor_string_search = $monitor_string_search;
        return $this;
    }

    /**
     * @return int
     */
    public function getMonitorStatusCodes(): int
    {
        return $this->monitor_status_codes;
    }

    /**
     * @param int $monitor_status_codes
     * @return zabbix_monitor
     */
    protected function setMonitorStatusCodes(int $monitor_status_codes): zabbix_monitor
    {
        $this->monitor_status_codes = max($this->getLimitMinStatusCodes(),min($this->getLimitMaxStatusCodes(),$monitor_status_codes));
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHostgroupId()
    {
        return $this->hostgroup_id;
    }

    /**
     * @param mixed $hostgroup_id
     * @return zabbix_monitor
     */
    public function setHostgroupId($hostgroup_id): zabbix_monitor
    {
        $this->hostgroup_id = $hostgroup_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHostId()
    {
        return $this->host_id;
    }

    /**
     * @param mixed $host_id
     * @return zabbix_monitor
     */
    public function setHostId($host_id): zabbix_monitor
    {
        $this->host_id = $host_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHttptestId()
    {
        return $this->httptest_id;
    }

    /**
     * @param mixed $httptest_id
     * @return zabbix_monitor
     */
    public function setHttptestId($httptest_id): zabbix_monitor
    {
        $this->httptest_id = $httptest_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getItemTrendId()
    {
        return $this->item_trend_id;
    }

    /**
     * @param int $item_trend_id
     * @return zabbix_monitor
     */
    public function setItemTrendId($item_trend_id): zabbix_monitor
    {
        $this->item_trend_id = $item_trend_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTriggerId()
    {
        return $this->trigger_id;
    }

    /**
     * @param mixed $trigger_id
     * @return zabbix_monitor
     */
    public function setTriggerId($trigger_id): zabbix_monitor
    {
        $this->trigger_id = $trigger_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsergroupId()
    {
        return $this->usergroup_id;
    }

    /**
     * @param mixed $usergroup_id
     * @return zabbix_monitor
     */
    public function setUsergroupId($usergroup_id): zabbix_monitor
    {
        $this->usergroup_id = $usergroup_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     * @return zabbix_monitor
     */
    public function setUserId($user_id): zabbix_monitor
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActionId()
    {
        return $this->action_id;
    }

    /**
     * @param mixed $action_id
     * @return zabbix_monitor
     */
    public function setActionId($action_id): zabbix_monitor
    {
        $this->action_id = $action_id;
        return $this;
    }

    /**
     * @return Mixed
     */
    public function getSmtpId()
    {
        return $this->smtp_id;
    }

    /**
     * @param int $smtp_id
     */
    public function setSmtpId($smtp_id)
    {
        $this->smtp_id = $smtp_id;
    }

    /**
     * @return string
     */
    public function getSmtpServer(): string
    {
        return $this->smtp_server;
    }

    /**
     * @return int
     */
    public function getSmtpPort(): int
    {
        return $this->smtp_port;
    }

    /**
     * @return int
     */
    public function getSmtpSsl(): int
    {
        return $this->smtp_ssl;
    }

    /**
     * @return string
     */
    public function getSmtpEmail(): string
    {
        return $this->smtp_email;
    }

    /**
     * @return string
     */
    public function getSmtpUser(): string
    {
        return $this->smtp_user;
    }

    /**
     * @return string
     */
    public function getSmtpPwd(): string
    {
        return $this->smtp_pwd;
    }

    /**
     * @return string
     */
    public function getSmtpMessageAlertSubject(): string
    {
        return $this->smtp_message_alert_subject;
    }

    /**
     * @return string
     */
    public function getSmtpMessageAlertContent(): string
    {
        return $this->smtp_message_alert_content;
    }

    /**
     * @return string
     */
    public function getSmtpMessageRecoverySubject(): string
    {
        return $this->smtp_message_recovery_subject;
    }

    /**
     * @return string
     */
    public function getSmtpMessageRecoveryContent(): string
    {
        return $this->smtp_message_recovery_content;
    }

    /**
     * @return string
     */
    public function getLimitDefCheckPeriod(): string
    {
        return $this->limit_def_check_period;
    }

    /**
     * @return string
     */
    public function getLimitMaxCheckPeriod(): string
    {
        return $this->limit_max_check_period;
    }

    /**
     * @return string
     */
    public function getLimitMinCheckPeriod(): string
    {
        return $this->limit_min_check_period;
    }

    /**
     * @return int
     */
    public function getLimitDefRetries(): int
    {
        return $this->limit_def_retries;
    }

    /**
     * @return int
     */
    public function getLimitMaxRetries(): int
    {
        return $this->limit_max_retries;
    }

    /**
     * @return int
     */
    public function getLimitMinRetries(): int
    {
        return $this->limit_min_retries;
    }

    /**
     * @return string
     */
    public function getLimitDefTimeout(): string
    {
        return $this->limit_def_timeout;
    }

    /**
     * @return string
     */
    public function getLimitMaxTimeout(): string
    {
        return $this->limit_max_timeout;
    }

    /**
     * @return string
     */
    public function getLimitMinTimeout(): string
    {
        return $this->limit_min_timeout;
    }

    /**
     * @return int
     */
    public function getLimitDefStatusCodes(): int
    {
        return $this->limit_def_status_codes;
    }

    /**
     * @return int
     */
    public function getLimitMaxStatusCodes(): int
    {
        return $this->limit_max_status_codes;
    }

    /**
     * @return int
     */
    public function getLimitMinStatusCodes(): int
    {
        return $this->limit_min_status_codes;
    }
}
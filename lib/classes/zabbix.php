<?php
# use cadjou\zabbix;
# require __DIR__ . '/zabbix.php';
#
# $conf_zabbix['url']         = 'https://zabbix.kwa.digital/';
# $conf_zabbix['user']        = 'Admin';
# $conf_zabbix['password']    = '**********';
# $conf_zabbix['hostgroup']   = 'Hostgroup';
# $conf_zabbix['host']        = 'Host';
# $conf_zabbix['application'] = 'Website Check';
#
# $zabbix = new zabbix($conf_zabbix);
# $zabbix->run();

class zabbix extends zabbix_manager
{
    // TODO : Make Delete
    // TODO : Make Admin / reseler / user management

    private $zm_new;
    private $zm_old;

    private $run_order = [
        'get_add_update_hostgroup' => 0,
        'get_add_update_host' => 1,
        'get_add_update_httptest' => 2,
        'get_add_update_trigger' => 3,
        'get_add_update_usergroup' => 4,
        'get_add_update_user' => 5,
    ];

    private $check_change = [
        'getServerName' => 'get_add_update_hostgroup',
        'getResellerId' => 'get_add_update_hostgroup',
        'getClientId' => 'get_add_update_host',
        'getClientName' => 'get_add_update_host',
        'getClientEmail' => '',
        'getMonitorId' => '',
        'getMonitorProtocol' => '',
        'getMonitorDomain' => '',
        'getMonitorPath' => '',
        'getMonitorCheckPeriod' => '',
        'getMonitorRetries' => '',
        'getMonitorActive' => '',
        'getMonitorTimeout' => '',
        'getMonitorStringSearch' => '',
        'getMonitorStatusCodes' => '',
    ];

    /**
     * @var array
     */
    private $list_actions = [];

    public function __construct($config,zabbix_monitor $data, $data_old = [])
    {
        parent::__construct($config['server'],$config['user'],$config['pwd']);
        if (!function_exists('curl_init')) {
            die('Need Curl extension');
        }
        $this->zm_new = $data;
        if ($data_old) {
            if ($data_old instanceof zabbix_monitor) {
                $this->zm_old = $data_old;
                foreach ($this->check_change as $param => $action) {
                    $new = call_user_func([$this->zm_new, $param]);
                    $old = call_user_func([$this->zm_old, $param]);
                    if ($new <> $old and isset($this->run_order[$action])) {
                        $this->list_actions[$this->run_order[$action]] = $action;
                    }
                }
            } else {
                die('Error on the old data is not zabbix_parameters class');
            }
        } else {
            $this->zm_old = $this->zm_new;
            $this->list_actions = array_flip($this->run_order);
        }

        //echo nl2br(print_r($this->zp_new,true)) . '<br>';
        //echo nl2br(print_r($this->zp_old,true)) . '<br>';
    }

    public function run()
    {
        $this->connexion();

        $this->get_add_update_action();

        $this->disconnection();
        /*die(
            'HostgroupId = ' . $this->zp_new->getHostgroupId() . '<br>'.
            'HostId = ' . $this->zp_new->getHostId() . '<br>'.
            'HttptestId = ' . $this->zp_new->getHttptestId() . '<br>'.
            'ItemTrendId = ' . $this->zp_new->getItemTrendId() . '<br>'.
            'TriggerId = ' . $this->zp_new->getTriggerId() . '<br>'.
            'UsergroupId = ' . $this->zp_new->getUsergroupId() . '<br>'.
            'UserId = ' . $this->zp_new->getUserId() . '<br>'.
            'SmtpId = ' . $this->zp_new->getSmtpId() . '<br>'.
            'ActionId = ' . $this->zp_new->getActionId() . '<br>'
        );*/
        return (!empty($this->zm_new->getHostgroupId())
            and !empty($this->zm_new->getHostId())
            and !empty($this->zm_new->getHttptestId())
            and !empty($this->zm_new->getItemTrendId())
            and !empty($this->zm_new->getTriggerId())
            and !empty($this->zm_new->getUsergroupId())
            and !empty($this->zm_new->getUserId())
            and !empty($this->zm_new->getSmtpId())
            and !empty($this->zm_new->getActionId())) ? $this->zm_new : false;
    }

    // HOSTGROUP
    private function get_add_update_hostgroup()
    {
        $this->zm_new->setHostgroupId(!empty($this->zm_old->getHostgroupId()) ? $this->get_hostgroup_id() : $this->get_hostgroup_name());

        if (empty($this->zm_new->getHostgroupId())) {
            $this->zm_new->setHostgroupId($this->add_hostgroup());
        } elseif ($this->zm_new->getHostgroupName() <> $this->zm_old->getHostgroupName()) {
            $this->zm_new->setHostgroupId($this->update_hostgroup());
        }
        if (empty($this->zm_new->getHostgroupId())) die('Error Hostgroup id');
    }

    private function get_hostgroup_id(bool $old = true)
    {
        $hostgroup_id = $old ? $this->zm_old->getHostgroupId() : $this->zm_new->getHostgroupId();

        $method = 'hostgroup.get';
        $params = [
            'output' => 'extend',
            'groupids' => $hostgroup_id,
        ];

        return $this->_response_get($this->curl($method, $params),'groupid');
    }

    private function get_hostgroup_name(bool $old = true)
    {
        $hostgroup_name = $old ? $this->zm_old->getHostgroupName() : $this->zm_new->getHostgroupName();

        $method = 'hostgroup.get';
        $params = [
            'output' => 'extend',
            'filter' => [
                'name' => [
                    0 => $hostgroup_name,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'groupid');
    }

    private function add_hostgroup(bool $old = false)
    {
        $hostgroup_name = $old ? $this->zm_old->getHostgroupName() : $this->zm_new->getHostgroupName();

        $method = 'hostgroup.create';
        $params = [
            'name' => $hostgroup_name,
        ];

        return $this->_response_modified($this->curl($method, $params),'groupids');
    }

    private function update_hostgroup(bool $old = false)
    {
        $hostgroup_id   = $old ? $this->zm_old->getHostgroupId() : $this->zm_new->getHostgroupId();
        $hostgroup_name = $old ? $this->zm_old->getHostgroupName() : $this->zm_new->getHostgroupName();

        $method = 'hostgroup.update';
        $params = [
            'groupid' => $hostgroup_id,
            'name' => $hostgroup_name,
        ];

        return $this->_response_modified($this->curl($method, $params),'groupids');
    }

    // HOST
    private function get_add_update_host()
    {
        if (empty($this->zm_new->getHostgroupId())) {
            $this->get_add_update_hostgroup();
        }

        $this->zm_new->setHostId(!empty($this->zm_old->getHostId()) ? $this->get_host_id() : $this->get_host_host());
        if (empty($this->zm_new->getHostId())) $this->zm_new->setHostId($this->get_host_name());

        if (empty($this->zm_new->getHostId())) {
            $this->zm_new->setHostId($this->add_host());
        } elseif ($this->zm_new->getHostgroupId() <> $this->zm_old->getHostgroupId()
            or $this->zm_new->getHostClientId() <> $this->zm_old->getHostClientId()
            or $this->zm_new->getHostClientName() <> $this->zm_old->getHostClientName()) {
            $this->zm_new->setHostId($this->update_host());
        }
        if (empty($this->zm_new->getHostId())) die('Error to get Host ID');
    }

    private function get_host_id(bool $old = true)
    {
        $hostgroup_id = $this->zm_new->getHostgroupId();
        $host_id = $old ? $this->zm_old->getHostId() : $this->zm_new->getHostId();

        $method = 'host.get';
        $params = [
            'output' => 'extend',
            'groupids' => $hostgroup_id,
            'hostids' => $host_id,
        ];

        return $this->_response_get($this->curl($method, $params),'hostid');
    }

    private function get_host_host(bool $old = true)
    {
        $hostgroup_id = $this->zm_new->getHostgroupId();
        $host_client_id = $old ? $this->zm_old->getHostClientId() : $this->zm_new->getHostClientId();

        $method = 'host.get';
        $params = [
            'output' => 'extend',
            'groupids' => $hostgroup_id,
            'filter' => [
                'host' => [
                    0 => $host_client_id,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'hostid');
    }

    private function get_host_name(bool $old = true)
    {
        $hostgroup_id = $this->zm_new->getHostgroupId();
        $host_client_name = $old ? $this->zm_old->getHostClientName() : $this->zm_new->getHostClientName();

        $method = 'host.get';
        $params = [
            'output' => 'extend',
            'groupids' => $hostgroup_id,
            'filter' => [
                'name' => [
                    0 => $host_client_name,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'hostid');
    }

    private function add_host(bool $old = false)
    {
        $hostgroup_id = $this->zm_new->getHostgroupId();
        $host_client_id = $old ? $this->zm_old->getHostClientId() : $this->zm_new->getHostClientId();
        $host_client_name = $old ? $this->zm_old->getHostClientName() : $this->zm_new->getHostClientName();

        $method = 'host.create';
        $params = [
            'host' => $host_client_id,
            'name' => $host_client_name,
            'groups' => [
                'groupid' => $hostgroup_id
            ],
        ];

        return $this->_response_modified($this->curl($method, $params),'hostids');
    }

    private function update_host(bool $old = false)
    {
        $hostgroup_id = $this->zm_new->getHostgroupId();
        $host_id = $this->zm_new->getHostId();
        $host_client_id = $old ? $this->zm_old->getHostClientId() : $this->zm_new->getHostClientId();
        $host_client_name = $old ? $this->zm_old->getHostClientName() : $this->zm_new->getHostClientName();

        $method = 'host.update';
        $params = [
            'hostid' => $host_id,
            'host' => $host_client_id,
            'name' => $host_client_name,
            'groups' => [
                'groupid' => $hostgroup_id
            ],
        ];

        return $this->_response_modified($this->curl($method, $params),'hostids');
    }

    // HTTPTEST
    private function get_add_update_httptest()
    {
        if (empty($this->zm_new->getHostId())) {
            $this->get_add_update_host();
        }

        $this->zm_new->setHttptestId(!empty($this->zm_old->getHttptestId()) ? $this->get_httptest_id() : $this->get_httptest_name());

        if (empty($this->zm_new->getHttptestId())) {
            $this->zm_new->setHttptestId($this->add_httptest());
        } elseif ($this->zm_new->getHttptestConf() <> $this->zm_old->getHttptestConf()
            or $this->zm_new->getStepConf() <> $this->zm_old->getStepConf()) {
            $this->zm_new->setHttptestId($this->update_httptest());
        }
        if (empty($this->zm_new->getHttptestId())) die('Error to get Httptest ID');

        $this->zm_new->setItemTrendId($this->get_item_trend());
    }

    private function get_httptest_id(bool $old = true)
    {
        $host_id = $this->zm_new->getHostId();
        $httptest_id = $old ? $this->zm_old->getHttptestId() : $this->zm_new->getHttptestId();

        $method = 'httptest.get';
        $params = [
            'output' => 'extend',
            'selectSteps' => 'extend',
            'hostids' => $host_id,
            'httptestids' => $httptest_id,
        ];

        return $this->_response_get($this->curl($method, $params),'httptestid');
    }

    private function get_httptest_name(bool $old = true)
    {
        $host_id = $this->zm_new->getHostId();
        $httptest_name = $old ? $this->zm_old->getHttptestName() : $this->zm_new->getHttptestName();

        $method = 'httptest.get';
        $params = [
            'output' => 'extend',
            'selectSteps' => 'extend',
            'hostids' => $host_id,
            'filter' => [
                'name' => [
                    0 => $httptest_name,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'httptestid');
    }

    private function add_httptest(bool $old = false)
    {
        $httptest_conf = $old ? $this->zm_old->getHttptestConf() : $this->zm_new->getHttptestConf();
        $step_conf = $old ? $this->zm_old->getStepConf() : $this->zm_new->getStepConf();

        $method = 'httptest.create';
        $params = $httptest_conf;
        $params['steps'] = [$step_conf];

        return $this->_response_modified($this->curl($method, $params),'httptestids');
    }

    private function update_httptest(bool $old = false)
    {
        $httptest_id = $this->zm_new->getHttptestId();
        $httptest_conf = $old ? $this->zm_old->getHttptestConf() : $this->zm_new->getHttptestConf();
        $step_conf = $old ? $this->zm_old->getStepConf() : $this->zm_new->getStepConf();

        $method = 'httptest.update';
        $params = $httptest_conf;
        unset($params['hostid']);
        $params['httptestid'] = $httptest_id;
        $params['steps'] = [$step_conf];

        return $this->_response_modified($this->curl($method, $params),'httptestids');
    }

    private function get_item_trend()
    {
        $method = 'item.get';
        $params = [
            'hostids'=> $this->zm_new->getHostId(),
            'webitems'=> true,
            'searchWildcardsEnabled'=> true,
            'search' => [
                'key_' => '*' . $this->zm_new->getMonitorDomain() . '*',
                'name' => 'Response time for step*',
            ]
        ];

        return $this->_response_get($this->curl($method, $params),'itemid');
    }

    // TRIGGER
    private function get_add_update_trigger()
    {
        if (empty($this->zm_new->getHttptestId())) {
            $this->get_add_update_httptest();
        }

        $this->zm_new->setTriggerId(!empty($this->zm_old->getTriggerId()) ? $this->get_trigger_id() : $this->get_trigger_name());

        if (empty($this->zm_new->getTriggerId())) {
            $this->zm_new->setTriggerId($this->add_trigger());
        } elseif ($this->zm_new->getTriggerName() <> $this->zm_old->getTriggerName()
            or $this->zm_new->getHttptestName() <> $this->zm_old->getHttptestName()
            or $this->zm_new->getHostClientId() <> $this->zm_old->getHostClientId()) {
            $this->zm_new->setTriggerId($this->update_trigger());
        }
        if (empty($this->zm_new->getHttptestId())) die('Error to get Httptest ID');
    }

    private function get_trigger_id(bool $old = true)
    {
        $host_id = $this->zm_new->getHostId();
        $trigger_id = $old ? $this->zm_old->getTriggerId() : $this->zm_new->getTriggerId();

        $method = 'trigger.get';
        $params = [
            'output' => 'extend',
            'hostids' => $host_id,
            'triggerids' => $trigger_id,
        ];

        return $this->_response_get($this->curl($method, $params),'triggerid');
    }

    private function get_trigger_name(bool $old = true)
    {
        $host_id = $this->zm_new->getHostId();
        $trigger_name = $old ? $this->zm_old->getTriggerName() : $this->zm_new->getTriggerName();

        $method = 'trigger.get';
        $params = [
            'output' => 'extend',
            'hostids' => $host_id,
            'filter' => [
                'name' => [
                    0 => $trigger_name,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'triggerid');
    }

    private function add_trigger(bool $old = false)
    {
        $host_id = $this->zm_new->getHostId();
        $host_client_id = $old ? $this->zm_old->getHostClientId() : $this->zm_new->getHostClientId() ;
        $httptest_name = $old ? $this->zm_old->getHttptestName() : $this->zm_new->getHttptestName();
        $trigger_name = $old ? $this->zm_old->getTriggerName() : $this->zm_new->getTriggerName();

        $method = 'trigger.create';
        $params = [
            'hostid' => $host_id,
            'description' => $trigger_name,
            'expression' => '{' . $host_client_id .
                ':web.test.fail[' . $httptest_name . '].last(#1)}>0',
            'priority' => '5',
        ];

        return $this->_response_modified($this->curl($method, $params),'triggerids');
    }

    private function update_trigger(bool $old = false)
    {
        $trigger_id = $this->zm_new->getTriggerId();
        $host_client_id = $old ? $this->zm_old->getHostClientId() : $this->zm_new->getHostClientId();
        $httptest_name = $old ? $this->zm_old->getHttptestName() : $this->zm_new->getHttptestName();
        $trigger_name = $old ? $this->zm_old->getTriggerName() : $this->zm_new->getTriggerName();

        $method = 'trigger.update';
        $params = [
            'triggerid' => $trigger_id,
            'description' => $trigger_name,
            'expression' => '{' . $host_client_id .
                ':web.test.fail[Web check ' . $httptest_name . '].last(#1)}>0',
            'priority' => '5', // TODO : Make managed
        ];

        return $this->_response_modified($this->curl($method, $params),'triggerids');
    }

    // SMTP
    private function get_add_update_smtp()
    {
        $this->zm_new->setSmtpId(!empty($this->zm_old->getSmtpId()) ? $this->get_smtp_id() : $this->get_smtp_name());
        if (empty($this->zm_new->getSmtpId())) $this->zm_new->setSmtpId($this->get_smtp_user());

        if (empty($this->zm_new->getSmtpId())) {
            $this->zm_new->setSmtpId($this->add_smtp());
        } elseif ($this->zm_new->getSmtpServer() <> $this->zm_old->getSmtpServer()
            or $this->zm_new->getSmtpPort() <> $this->zm_old->getSmtpPort()
            or $this->zm_new->getSmtpSsl() <> $this->zm_old->getSmtpSsl()
            or $this->zm_new->getSmtpUser() <> $this->zm_old->getSmtpUser()
            or $this->zm_new->getSmtpPwd() <> $this->zm_old->getSmtpPwd()) {
            $this->zm_new->setSmtpId($this->update_smtp());
        }
        if (empty($this->zm_new->getSmtpId())) die('Error to get Smtp ID');
    }

    private function get_smtp_id(bool $old = true)
    {
        $smtp_id = $old ? $this->zm_old->getSmtpId() : $this->zm_new->getSmtpId();

        $method = 'mediatype.get';
        $params = [
            'filter' => [
                'mediatypeid' => [
                    0 => $smtp_id,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'mediatypeid');
    }

    private function get_smtp_name(bool $old = true)
    {
        $smtp_name = $old ? $this->zm_old->getUsergroupName() : $this->zm_new->getUsergroupName();

        $method = 'mediatype.get';
        $params = [
            'filter' => [
                'name' => [
                    0 => $smtp_name,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'mediatypeid');
    }

    private function get_smtp_user(bool $old = true)
    {
        $smtp_user = $old ? $this->zm_old->getSmtpUser() : $this->zm_new->getSmtpUser();

        $method = 'mediatype.get';
        $params = [
            'filter' => [
                'username' => [
                    0 => $smtp_user,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'mediatypeid');
    }

    private function add_smtp(bool $old = false)
    {
        $smtp_name = $old ? $this->zm_old->getUsergroupName() : $this->zm_new->getUsergroupName();
        $smtp_server = $old ? $this->zm_old->getSmtpServer() : $this->zm_new->getSmtpServer();
        $smtp_port = $old ? $this->zm_old->getSmtpPort() : $this->zm_new->getSmtpPort();
        $smtp_ssl = $old ? $this->zm_old->getSmtpSsl() : $this->zm_new->getSmtpSsl();
        $smtp_email = $old ? $this->zm_old->getSmtpEmail() : $this->zm_new->getSmtpEmail();
        $smtp_user = $old ? $this->zm_old->getSmtpUser() : $this->zm_new->getSmtpUser();
        $smtp_pwd = $old ? $this->zm_old->getSmtpPwd() : $this->zm_new->getSmtpPwd();

        $method = 'mediatype.create';
        $params = [
            'name' => $smtp_name,
            'type' => 0,
            'smtp_server' => $smtp_server,
            'smtp_port' => $smtp_port,
            'smtp_security' => $smtp_ssl,
            'smtp_helo' => $smtp_server,
            'smtp_email' => $smtp_email,
            'username' => $smtp_user,
            'passwd' => $smtp_pwd,
            'smtp_authentication' => 1,
        ];

        return $this->_response_modified($this->curl($method, $params),'mediatypeids');
    }

    private function update_smtp(bool $old = false)
    {
        $smtp_id = $this->zm_new->getSmtpId();
        $smtp_name = $old ? $this->zm_old->getUsergroupName() : $this->zm_new->getUsergroupName();
        $smtp_server = $old ? $this->zm_old->getSmtpServer() : $this->zm_new->getSmtpServer();
        $smtp_port = $old ? $this->zm_old->getSmtpPort() : $this->zm_new->getSmtpPort();
        $smtp_ssl = $old ? $this->zm_old->getSmtpSsl() : $this->zm_new->getSmtpSsl();
        $smtp_email = $old ? $this->zm_old->getSmtpEmail() : $this->zm_new->getSmtpEmail();
        $smtp_user = $old ? $this->zm_old->getSmtpUser() : $this->zm_new->getSmtpUser();
        $smtp_pwd = $old ? $this->zm_old->getSmtpPwd() : $this->zm_new->getSmtpPwd();

        $method = 'mediatype.update';
        $params = [
            'mediatypeid' => $smtp_id,
            'name' => $smtp_name,
            'type' => 0,
            'smtp_server' => $smtp_server,
            'smtp_port' => $smtp_port,
            'smtp_security' => $smtp_ssl,
            'smtp_helo' => $smtp_server,
            'smtp_email' => $smtp_email,
            'username' => $smtp_user,
            'passwd' => $smtp_pwd,
            'smtp_authentication' => 1,
        ];

        return $this->_response_modified($this->curl($method, $params),'mediatypeids');
    }

    // USERGROUP
    private function get_add_update_usergroup()
    {
        if (empty($this->zm_new->getHostgroupId())) {
            $this->get_add_update_hostgroup();
        }

        $this->zm_new->setUsergroupId(!empty($this->zm_old->getUsergroupId()) ? $this->get_usergroup_id() : $this->get_usergroup_name());

        if (empty($this->zm_new->getUsergroupId())) {
            $this->zm_new->setUsergroupId($this->add_usergroup());
        } elseif ($this->zm_new->getUsergroupName() <> $this->zm_old->getUsergroupName()
            or $this->zm_new->getHostgroupId() <> $this->zm_old->getHostgroupId()) {
            $this->zm_new->setUsergroupId($this->update_usergroup());
        }
        if (empty($this->zm_new->getHttptestId())) die('Error to get Usergroup ID');
    }

    private function get_usergroup_id(bool $old = true)
    {
        $usergroup_id = $old ? $this->zm_old->getUsergroupId() : $this->zm_new->getUsergroupId();

        $method = 'usergroup.get';
        $params = [
            'filter' => [
                'usrgrpid' => [
                    0 => $usergroup_id,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'usrgrpid');
    }

    private function get_usergroup_name(bool $old = true)
    {
        $usergroup_name = $old ? $this->zm_old->getUsergroupName() : $this->zm_new->getUsergroupName();

        $method = 'usergroup.get';
        $params = [
            'filter' => [
                'name' => [
                    0 => $usergroup_name,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'usrgrpid');
    }

    private function add_usergroup(bool $old = false)
    {
        $hostgroup_id = $this->zm_new->getHostgroupId();
        $usergroup_name = $old ? $this->zm_old->getUsergroupName() : $this->zm_new->getUsergroupName();

        $method = 'usergroup.create';
        $params = [
            'name' => $usergroup_name,
            'rights' => [
                'permission' => 2,
                'id' => $hostgroup_id,
            ],
        ];

        return $this->_response_modified($this->curl($method, $params),'usrgrpids');
    }

    private function update_usergroup(bool $old = false)
    {
        $usergroup_id = $this->zm_new->getUsergroupId();
        $hostgroup_id = $old ? $this->zm_old->getHostgroupId() : $this->zm_new->getHostgroupId();
        $usergroup_name = $old ? $this->zm_old->getUsergroupName() : $this->zm_new->getUsergroupName();

        $method = 'usergroup.create';
        $params = [
            'usrgrpid' => $usergroup_id,
            'name' => $usergroup_name,
            'rights' => [
                'permission' => 2,
                'id' => $hostgroup_id,
            ],
        ];
        // TODO : Make users_status managed => See https://www.zabbix.com/documentation/current/manual/api/reference/usergroup/object

        return $this->_response_modified($this->curl($method, $params),'usrgrpids');
    }

    // USER
    private function get_add_update_user()
    {
        if (empty($this->zm_new->getUsergroupId())) {
            $this->get_add_update_usergroup();
        }
        if (empty($this->zm_new->getSmtpId())) {
            $this->get_add_update_smtp();
        }

        $this->zm_new->setUserId(!empty($this->zm_old->getUserId()) ? $this->get_user_id() : $this->get_user_name());
        if (empty($this->zm_new->getUserId())) $this->zm_new->setUserId($this->get_user_alias());

        if (empty($this->zm_new->getUserId())) {
            $this->zm_new->setUserId($this->add_user());
        } elseif ($this->zm_new->getUsergroupName() <> $this->zm_old->getUsergroupName()
            or $this->zm_new->getHostgroupId() <> $this->zm_old->getHostgroupId()) {
            $this->zm_new->setUserId($this->update_user());
        }
        if (empty($this->zm_new->getHttptestId())) die('Error to get User ID');
    }

    private function get_user_id(bool $old = true)
    {
        $user_id = $old ? $this->zm_old->getUserId() : $this->zm_new->getUserId();

        $method = 'usergroup.get';
        $params = [
            'userids' => $user_id,
        ];

        return $this->_response_get($this->curl($method, $params),'userid');
    }

    private function get_user_name(bool $old = true)
    {
        $user_name = $old ? $this->zm_old->getUserName() : $this->zm_new->getUserName();

        $method = 'user.get';
        $params = [
            'filter' => [
                'name' => [
                    0 => $user_name,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'userid');
    }

    private function get_user_alias(bool $old = true)
    {
        $user_alias = $old ? $this->zm_old->getUserAlias() : $this->zm_new->getUserAlias();

        $method = 'user.get';
        $params = [
            'filter' => [
                'alias' => [
                    0 => $user_alias,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'userid');
    }

    private function add_user(bool $old = false)
    {
        $usergroup_id = $this->zm_new->getUsergroupId();
        $user_name = $old ? $this->zm_old->getUserName() : $this->zm_new->getUserName();
        $user_alias = $old ? $this->zm_old->getUserAlias(): $this->zm_new->getUserAlias();
        $user_pwd = $old ? $this->zm_old->getUserPwd(): $this->zm_new->getUserPwd();
        $user_email = $old ? $this->zm_old->getClientEmail() : $this->zm_new->getClientEmail();
        $mediatype_id = $old ? $this->zm_old->getSmtpId() : $this->zm_new->getSmtpId();

        $method = 'user.create';
        $params = [
            'alias' => $user_alias,
            'name' => $user_name,
            'passwd' => $user_pwd,
            'roleid' => 1,
            'usrgrps' => [
                [
                    'usrgrpid' => $usergroup_id,
                ],
            ],
            'medias' => [
                [
                    'mediatypeid' => $mediatype_id,
                    'sendto' => [
                        $user_email,
                    ],
                ],
            ]
        ];

        return $this->_response_modified($this->curl($method, $params),'userids');
    }

    private function update_user(bool $old = false)
    {
        $usergroup_id = $this->zm_new->getUsergroupId();
        $user_id = $this->zm_new->getUserId();
        $user_name = $old ? $this->zm_old->getUserName() : $this->zm_new->getUserName();
        $user_alias = $old ? $this->zm_old->getUserAlias(): $this->zm_new->getUserAlias();
        $user_pwd = $old ? $this->zm_old->getUserPwd(): $this->zm_new->getUserPwd();
        $user_email = $old ? $this->zm_old->getClientEmail() : $this->zm_new->getClientEmail();
        $mediatype_id = $old ? $this->zm_old->getSmtpId() : $this->zm_new->getSmtpId();

        $method = 'user.update';
        $params = [
            'userid' => $user_id,
            'alias' => $user_alias,
            'name' => $user_name,
            'passwd' => $user_pwd,
            'roleid' => 1,
            'usrgrps' => [
                [
                    'usrgrpid' => $usergroup_id,
                ],
            ],
            'medias' => [
                [
                    'mediatypeid' => $mediatype_id,
                    'sendto' => [
                        $user_email,
                    ],
                ],
            ]
        ];

        return $this->_response_modified($this->curl($method, $params),'userids');
    }

    // ACTION
    private function get_add_update_action()
    {
        if (empty($this->zm_new->getTriggerId())) {
            $this->get_add_update_trigger();
        }
        if (empty($this->zm_new->getUserId())) {
            $this->get_add_update_user();
        }

        $this->zm_new->setActionId(!empty($this->zm_old->getActionId()) ? $this->get_action_id() : $this->get_action_name());

        if (empty($this->zm_new->getActionId())) {
            $this->zm_new->setActionId($this->add_action());
        } elseif ($this->zm_new->getHostClientId() <> $this->zm_old->getHostClientId()
            or $this->zm_new->getHostClientName() <> $this->zm_old->getHostClientName()
            or $this->zm_new->getMessageConf() <> $this->zm_old->getMessageConf()) {
            $this->zm_new->setActionId($this->update_action());
        }
        if (empty($this->zm_new->getSmtpId())) die('Error to get Action ID');
    }

    private function get_action_id(bool $old = true)
    {
        $action_id = $old ? $this->zm_old->getActionId() : $this->zm_new->getActionId();

        $method = 'action.get';
        $params = [
            'filter' => [
                'actionid' => [
                    0 => $action_id,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'actionid');
    }

    private function get_action_name(bool $old = true)
    {
        $action_name = $old ? $this->zm_old->getHostClientId(): $this->zm_new->getHostClientId();

        $method = 'action.get';
        $params = [
            'filter' => [
                'name' => [
                    0 => $action_name,
                ],
            ],
        ];

        return $this->_response_get($this->curl($method, $params),'actionid');
    }

    private function add_action(bool $old = false)
    {
        $smtp_id = $this->zm_new->getSmtpId();
        $user_id = $this->zm_new->getUserId();
        $host_id = $old ? $this->zm_old->getHostId() : $this->zm_new->getHostId() ;
        $action_name = $old ? $this->zm_old->getHostClientName(): $this->zm_new->getHostClientId();
        $message_conf = $old ? $this->zm_old->getMessageConf(): $this->zm_new->getMessageConf();

        $method = 'action.create';
        $params = [
            'name' => $action_name,      // Id du client
            'eventsource' => '0',      // ??
            'status' => '0',           // 0 - activé / 1 - désactivé
            'esc_period' => '1h',      // Temps par défaut pour passer au destinataire suivant
            'filter' => [
                'evaltype' => '0',     // 0 - and/or / 1 - and / 2 - or / 3 - expresion personnalisée
                'conditions' => [
                    [
                        'conditiontype' => 1, // 0 - Hostgroup / 1 - Host ...
                        'value' => $host_id,
                    ],
                ],
            ],
            'operations' => [
                [
                    'operationtype' => 0, // send message
                    'opmessage' => [
                        'default_msg' => 0,
                        'subject' => $message_conf['alert']['subject'],
                        'message' => $message_conf['alert']['message'],
                        'mediatypeid' => $smtp_id,  // service SMTP
                    ],
                    'opmessage_usr' => [
                        [
                            'userid' => $user_id,
                        ],
                    ],
                ],
            ],
            'recovery_operations' => [
                [
                    'operationtype' => 0,
                    'opmessage' => [
                        'default_msg' => 0,
                        'subject' => $message_conf['recovery']['subject'],
                        'message' => $message_conf['recovery']['message'],
                        'mediatypeid' => $smtp_id,
                    ],
                    'opmessage_usr' => [
                        [
                            'userid' => $user_id,
                        ],
                    ],
                ],
            ],
        ];

        return $this->_response_modified($this->curl($method, $params),'actionids');
    }

    private function update_action(bool $old = false)
    {
        $action_id = $this->zm_new->getActionId();
        $smtp_id = $this->zm_new->getSmtpId();
        $user_id = $this->zm_new->getUserId();
        $host_id = $old ? $this->zm_old->getHostClientId() : $this->zm_new->getHostClientId() ;
        $action_name = $old ? $this->zm_old->getHostClientName(): $this->zm_new->getHostClientId();
        $message_conf = $old ? $this->zm_old->getMessageConf(): $this->zm_new->getMessageConf();

        $method = 'action.update';
        $params = [
            'actionid' => $action_id,
            'name' => $action_name,      // Id du client
            'eventsource' => '0',      // ??
            'status' => '0',           // 0 - activé / 1 - désactivé
            'esc_period' => '1h',      // Temps par défaut pour passer au destinataire suivant
            'filter' => [
                'evaltype' => '0',     // 0 - and/or / 1 - and / 2 - or / 3 - expresion personnalisée
                'conditions' => [
                    [
                        'conditiontype' => 1, // 0 - Hostgroup / 1 - Host ...
                        'value' => $host_id,
                    ],
                ],
            ],
            'operations' => [
                [
                    'operationtype' => 0, // send message
                    'opmessage' => [
                        'default_msg' => 0,
                        'subject' => $message_conf['alert']['subject'],
                        'message' => $message_conf['alert']['message'],
                        'mediatypeid' => $smtp_id,  // service SMTP
                    ],
                    'opmessage_usr' => [
                        [
                            'userid' => $user_id,
                        ],
                    ],
                ],
            ],
            'recoveryOperations' => [
                [
                    'operationtype' => 0,
                    'opmessage' => [
                        'default_msg' => 0,
                        'subject' => $message_conf['recovery']['subject'],
                        'message' => $message_conf['recovery']['message'],
                        'mediatypeid' => $smtp_id,
                    ],
                    'opmessage_usr' => [
                        [
                            'userid' => $user_id,
                        ],
                    ],
                ],
            ],
        ];

        return $this->_response_modified($this->curl($method, $params),'actionids');
    }
}
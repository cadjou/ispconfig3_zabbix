<?php


class zabbix_parameter
{
    // Parameters
    // Connexion
    protected $zabbix_shared;
    protected $zabbix_host;
    protected $zabbix_user;
    protected $zabbix_pwd;
    // Email
    protected $receiver;
    protected $alert_subject;
    protected $alert_content;
    protected $recovery_subject;
    protected $recovery_content;
    // Constants
    protected $isp_glue;
    protected $isp_keyword;
    protected $httptest_keyword;
    protected $step_keyword;
    protected $application_keyword;
    protected $trigger_keyword;
    // Limits Monitor
    protected $limit_monitor;
    protected $limit_check_period;
    protected $limit_retries;
    protected $limit_timeout;
    // Default Monitor
    protected $default_check_period;
    protected $default_retries;
    protected $default_timeout;
    protected $default_status_codes;
    // Default SMTP
    protected $smtp_host;
    protected $smtp_port;
    protected $smtp_ssl;
    protected $smtp_user;
    protected $smtp_pwd;
    protected $smtp_sender;

    protected $data;
    protected $type;
    private $id;

    /**
     * zabbix_parameter constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
        $missing_param=[];
        if (!empty($data['admin_id'])){
            $this->type = 'admin';
            $this->id = $data['admin_id'];
        }elseif (!empty($data['client_id'])){
            $this->zabbix_shared = 'null';
            $this->zabbix_host = 'null';
            $this->isp_glue = 'null';
            $this->isp_keyword = 'null';
            $this->type = 'client';
            $this->id = $data['client_id'];
        }
        foreach ($this as $param=>$v) {
            if (!$v and isset($data[$param])) {
                $this->$param = $data[$param];
            }elseif(!$v) {
                $missing_param[] = $param;
            }
        }

        // Die if missing parameter in $data
        // TODO : Improve error management
        if ($missing_param) die('Missing Parameters : ' . nl2br(print_r($missing_param, true)));

    }

    /**
     * @return array
     */
    public function getArrayData(): array
    {
        foreach ($this->data as $param=>$v) {
            if (isset($this->$param)) {
                $this->data[$param] = $this->$param;
            }
        }
        return $this->data;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    public function getId()
    {
        return $this->id;
    }

    // **********
    // MediaType
    // **********
    public function getMediaTypeId(){
        return false;
    }

    // **********
    // UserGroup
    // **********
    public function setUsrGrpId($usrgrpid): void
    {
        $this->_usrgrpid = $usrgrpid;
    }

    public function _getGroupUser()
    {
        return ['name' => implode($this->getIspGlue(), [$this->getIspKeyword(), $this->getId()])];
    }

    // **********
    // User
    // **********
    public function setUsrId($value): void
    {
        $this->_usrid = $value;
    }

    public function setMedias($value): void
    {
        $this->_medias = $value;
    }

    public function _getUser()
    {
        return ['selectMedias'=> 'extend','filter' => ['alias' => [0 => $this->getZabbixUser()]]];
    }

    public function _updateUser()
    {
        if ($this->getMediaTypeId() and $this->getReceiver()){
            $this->_medias[] = ['mediatypeid' => $this->getMediaTypeId(),'sendto' => $this->getReceiver()];
        }

        $return = ['userid' => $this->_usrid, 'usrgrps' => ['usrgrpid' => [$this->_usrgrpid]]];

        if ($this->_medias){
            $return['medias'] = $this->_medias;
        }
        return $return;
    }

    /**
     * @return string
     */
    public function getZabbixShared(): string
    {
        return $this->zabbix_shared;
    }

    /**
     * @param string $zabbix_shared
     */
    public function setZabbixShared(string $zabbix_shared): void
    {
        $this->zabbix_shared = $zabbix_shared;
    }

    /**
     * @return mixed|string
     */
    public function getZabbixHost()
    {
        return $this->zabbix_host;
    }

    /**
     * @param mixed|string $zabbix_host
     */
    public function setZabbixHost($zabbix_host): void
    {
        $this->zabbix_host = $zabbix_host;
    }

    /**
     * @return mixed
     */
    public function getZabbixUser()
    {
        return $this->zabbix_user;
    }

    /**
     * @param mixed $zabbix_user
     */
    public function setZabbixUser($zabbix_user): void
    {
        $this->zabbix_user = $zabbix_user;
    }

    /**
     * @return mixed
     */
    public function getZabbixPwd()
    {
        return $this->zabbix_pwd;
    }

    /**
     * @param mixed $zabbix_pwd
     */
    public function setZabbixPwd($zabbix_pwd): void
    {
        $this->zabbix_pwd = $zabbix_pwd;
    }

    /**
     * @return mixed
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param mixed $receiver
     */
    public function setReceiver($receiver): void
    {
        $this->receiver = $receiver;
    }

    /**
     * @return mixed
     */
    public function getAlertSubject()
    {
        return $this->alert_subject;
    }

    /**
     * @param mixed $alert_subject
     */
    public function setAlertSubject($alert_subject): void
    {
        $this->alert_subject = $alert_subject;
    }

    /**
     * @return mixed
     */
    public function getAlertContent()
    {
        return $this->alert_content;
    }

    /**
     * @param mixed $alert_content
     */
    public function setAlertContent($alert_content): void
    {
        $this->alert_content = $alert_content;
    }

    /**
     * @return mixed
     */
    public function getRecoverySubject()
    {
        return $this->recovery_subject;
    }

    /**
     * @param mixed $recovery_subject
     */
    public function setRecoverySubject($recovery_subject): void
    {
        $this->recovery_subject = $recovery_subject;
    }

    /**
     * @return mixed
     */
    public function getRecoveryContent()
    {
        return $this->recovery_content;
    }

    /**
     * @param mixed $recovery_content
     */
    public function setRecoveryContent($recovery_content): void
    {
        $this->recovery_content = $recovery_content;
    }

    /**
     * @return mixed
     */
    public function getIspGlue()
    {
        return $this->isp_glue;
    }

    /**
     * @param mixed $isp_glue
     */
    public function setIspGlue($isp_glue): void
    {
        $this->isp_glue = $isp_glue;
    }

    /**
     * @return mixed
     */
    public function getIspKeyword()
    {
        return $this->isp_keyword;
    }

    /**
     * @param mixed $isp_keyword
     */
    public function setIspKeyword($isp_keyword): void
    {
        $this->isp_keyword = $isp_keyword;
    }

    /**
     * @return mixed
     */
    public function getHttptestKeyword()
    {
        return $this->httptest_keyword;
    }

    /**
     * @param mixed $httptest_keyword
     */
    public function setHttptestKeyword($httptest_keyword): void
    {
        $this->httptest_keyword = $httptest_keyword;
    }

    /**
     * @return mixed
     */
    public function getStepKeyword()
    {
        return $this->step_keyword;
    }

    /**
     * @param mixed $step_keyword
     */
    public function setStepKeyword($step_keyword): void
    {
        $this->step_keyword = $step_keyword;
    }

    /**
     * @return mixed
     */
    public function getApplicationKeyword()
    {
        return $this->application_keyword;
    }

    /**
     * @param mixed $application_keyword
     */
    public function setApplicationKeyword($application_keyword): void
    {
        $this->application_keyword = $application_keyword;
    }

    /**
     * @return mixed
     */
    public function getTriggerKeyword()
    {
        return $this->trigger_keyword;
    }

    /**
     * @param mixed $trigger_keyword
     */
    public function setTriggerKeyword($trigger_keyword): void
    {
        $this->trigger_keyword = $trigger_keyword;
    }

    /**
     * @return mixed
     */
    public function getLimitMonitor()
    {
        return $this->limit_monitor;
    }

    /**
     * @param mixed $limit_monitor
     */
    public function setLimitMonitor($limit_monitor): void
    {
        $this->limit_monitor = $limit_monitor;
    }

    /**
     * @return mixed
     */
    public function getLimitCheckPeriod()
    {
        return $this->limit_check_period;
    }

    /**
     * @param mixed $limit_check_period
     */
    public function setLimitCheckPeriod($limit_check_period): void
    {
        $this->limit_check_period = $limit_check_period;
    }

    /**
     * @return mixed
     */
    public function getLimitRetries()
    {
        return $this->limit_retries;
    }

    /**
     * @param mixed $limit_retries
     */
    public function setLimitRetries($limit_retries): void
    {
        $this->limit_retries = $limit_retries;
    }

    /**
     * @return mixed
     */
    public function getLimitTimeout()
    {
        return $this->limit_timeout;
    }

    /**
     * @param mixed $limit_timeout
     */
    public function setLimitTimeout($limit_timeout): void
    {
        $this->limit_timeout = $limit_timeout;
    }

    /**
     * @return mixed
     */
    public function getDefaultCheckPeriod()
    {
        return $this->default_check_period;
    }

    /**
     * @param mixed $default_check_period
     */
    public function setDefaultCheckPeriod($default_check_period): void
    {
        $this->default_check_period = $default_check_period;
    }

    /**
     * @return mixed
     */
    public function getDefaultRetries()
    {
        return $this->default_retries;
    }

    /**
     * @param mixed $default_retries
     */
    public function setDefaultRetries($default_retries): void
    {
        $this->default_retries = $default_retries;
    }

    /**
     * @return mixed
     */
    public function getDefaultTimeout()
    {
        return $this->default_timeout;
    }

    /**
     * @param mixed $default_timeout
     */
    public function setDefaultTimeout($default_timeout): void
    {
        $this->default_timeout = $default_timeout;
    }

    /**
     * @return mixed
     */
    public function getDefaultStatusCodes()
    {
        return $this->default_status_codes;
    }

    /**
     * @param mixed $default_status_codes
     */
    public function setDefaultStatusCodes($default_status_codes): void
    {
        $this->default_status_codes = $default_status_codes;
    }

    /**
     * @return mixed
     */
    public function getSmtpHost()
    {
        return $this->smtp_host;
    }

    /**
     * @param mixed $smtp_host
     */
    public function setSmtpHost($smtp_host): void
    {
        $this->smtp_host = $smtp_host;
    }

    /**
     * @return mixed
     */
    public function getSmtpPort()
    {
        return $this->smtp_port;
    }

    /**
     * @param mixed $smtp_port
     */
    public function setSmtpPort($smtp_port): void
    {
        $this->smtp_port = $smtp_port;
    }

    /**
     * @return mixed
     */
    public function getSmtpSsl()
    {
        return $this->smtp_ssl;
    }

    /**
     * @param mixed $smtp_ssl
     */
    public function setSmtpSsl($smtp_ssl): void
    {
        $this->smtp_ssl = $smtp_ssl;
    }

    /**
     * @return mixed
     */
    public function getSmtpUser()
    {
        return $this->smtp_user;
    }

    /**
     * @param mixed $smtp_user
     */
    public function setSmtpUser($smtp_user): void
    {
        $this->smtp_user = $smtp_user;
    }

    /**
     * @return mixed
     */
    public function getSmtpPwd()
    {
        return $this->smtp_pwd;
    }

    /**
     * @param mixed $smtp_pwd
     */
    public function setSmtpPwd($smtp_pwd): void
    {
        $this->smtp_pwd = $smtp_pwd;
    }

    /**
     * @return mixed
     */
    public function getSmtpSender()
    {
        return $this->smtp_sender;
    }

    /**
     * @param mixed $smtp_sender
     */
    public function setSmtpSender($smtp_sender): void
    {
        $this->smtp_sender = $smtp_sender;
    }


    public function __get($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }
        return null;
    }

    public function __set($name, $value)
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        }
    }
}
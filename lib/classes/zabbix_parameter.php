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

    // Limits Client
    protected $enable_connexion;
    protected $enable_trend;
    protected $enable_event;
    protected $enable_smtp;
    protected $enable_alert;

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

    // Id Zabbix
    protected $id_mediatype;
    protected $id_usergroup;
    protected $id_user;

    // Internal Parameters
    protected $data;
    protected $infoClient;
    protected $reseller;
    protected $admin;
    protected $type;
    protected $id;

    // Temporal Parameters
    protected $_medias = [];
    protected $_usrgrps = [];

    /**
     * @var zabbix_manager
     */
    protected $_zm;

    /**
     * zabbix_parameter constructor.
     * @param array $data
     * @param array $infoClient
     * @param zabbix_parameter $reseller
     * @param zabbix_parameter $admin
     */
    public function __construct(array $data, array $infoClient, $reseller, $admin)
    {
        $this->data = $data;
        $this->infoClient = $infoClient ? $infoClient : ['null'];
        $this->reseller = $reseller ? $reseller : 'null';
        $this->admin = $admin ? $admin : 'null';

        if (!empty($data['client_id']) and !$this->hasAdmin()) die('Missing Admin Setup');

        $missing_param = [];
        if (!empty($data['admin_id'])){
            $this->enable_connexion = 'y';
            $this->enable_trend = 'y';
            $this->enable_event = 'y';
            $this->enable_smtp = 'y';
            $this->enable_alert = 'y';

            $this->type = 'admin';
            $this->id = $data['admin_id'];
        }elseif (!empty($data['client_id'])){
            // Init var unused
            $this->zabbix_shared = 'null';
            $this->zabbix_host = 'null';

            $this->isp_glue = $this->admin->getIspGlue();
            $this->isp_keyword = $this->admin->getIspKeyword();

            $this->type = 'client';
            $this->id = $data['client_id'];
        }
        foreach ($this as $param=>$v) {

            if ($this->isTemporalParam($param)) continue;

            if (!$v and isset($data[$param])) {
                $this->$param = $data[$param];
            }elseif(!$v) {
                $missing_param[] = $param;
            }
        }
        // Die if missing parameter in $data
        // TODO : Improve error management
        if ($missing_param) die('Missing Parameters : ' . nl2br(print_r($missing_param, true)));

        if($this->type == 'admin') $this->admin = $this;
        $this->getZabbixConnexion();
    }

    // **********
    // Internal Parameters Getters
    // **********
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
    // Info Client
    // **********

    protected function getInfoClient($item)
    {
        if (!isset($this->infoClient[$item])) return false;
        return $this->infoClient[$item];
    }

    // **********
    // Reseller
    // **********

    protected function hasReseller()
    {
        return is_object($this->reseller) and $this->reseller instanceof zabbix_parameter;
    }

    // **********
    // Admin
    // **********

    protected function hasAdmin()
    {
        return is_object($this->admin) and $this->admin instanceof zabbix_parameter;
    }

    public function getZabbixConnexion()
    {
        if ($this->_zm) return $this->_zm;

        $this->_zm = new zabbix_manager($this->admin->getZabbixHost(),$this->admin->getZabbixUser(),$this->admin->getZabbixPwd());
        return $this->_zm;
    }

    // **********
    // MediaType
    // **********

    public function mediaType()
    {
        $data = false;
        $findbys = ['id','name'];
        foreach ($findbys as $findby) {
            $var = $this->_forGetMediaType($findby);
            if (!$var) continue;

            $data = $this->_zm->requestGet($var);
            if ($data) break;
        }
        if (!empty($data['mediatypeid'])) $this->setIdMediatype($data['mediatypeid']);

        $zp = $this->whereMediatype();

        if ($this == $zp) {
            if(!$data){
                $data = $this->_zm->requestChange($this->_forCreateMediaType());
            }else{
                $needUpdate = $this->_needUpdateMediaType($data);
                if ($needUpdate) {
                    $this->_zm->requestChange($needUpdate);
                }
            }
        } else {
            $data = false;
            foreach ($findbys as $findby) {
                $var = $zp->_forGetMediaType($findby);
                if (!$var) continue;

                $data = $this->_zm->requestGet($var);
                if ($data) break;
            }
            if(!$data){
                $data = $this->_zm->requestChange($zp->_forCreateMediaType());
            }else{
                $needUpdate = $zp->_needUpdateMediaType($data);
                if ($needUpdate) {
                    $this->_zm->requestChange($needUpdate);
                }
                // $zp->_needchangeChild(); // TODO : Manage the change ID in the action
                $this->_zm->requestChange($this->_forDeleteMediaType());
            }
        }

        if (empty($data['mediatypeids'])) die('Cannot create MediaType');

        $this->setIdMediatype($data['mediatypeids']);
    }

    public function _needUpdateMediaType($data)
    {
        $init = [
            'name' => $this->_makeMediaTypeName(),
            'smtp_server' => $this->getSmtpHost(),
            'smtp_port' => $this->getSmtpPort(),
            'smtp_security' => $this->getSmtpSsl(),
            'smtp_helo' => $this->getSmtpHost(),
            'smtp_email' => $this->getSmtpSender(),
            'username' => $this->getSmtpUser(),
            'passwd' => $this->getSmtpPwd(),
        ];
        $need_change = [];
        foreach ($data as $key=>$value){
            if (isset($init[$key]) and $init[$key] <> $value) {
                $need_change[$key] = $init[$key];
            }
        }
        if ($need_change){
            $need_change['mediatypeid'] = $data['mediatypeid'];
            return $this->_forUpdateMediaType($need_change);
        }
        return false;
    }

    public function whereMediatype()
    {
        if ($this->getEnableSmtp() and $this->getSmtpHost()){
            return $this;
        } elseif ($this->hasReseller() and $this->reseller->getEnableSmtp() and $this->getSmtpHost()){
            return $this->reseller;
        } else {
            return $this->admin;
        }
    }

    public function _makeMediaTypeName(){
        return implode($this->getIspGlue(), [$this->getIspKeyword(), $this->getId()]);
    }

    public function _forGetMediaType($findby)
    {
        $return['method'] = 'mediatype.get';
        if ($findby == 'id'){
            if (!$this->getIdMediatype()) return false;
            $return['params'] = ['mediatypeids' => $this->getIdMediatype()];
        } elseif ($findby == 'name') {
            $return['params'] = ['filter' => ['name' => $this->_makeMediaTypeName()]];
        } else {
            return false;
        }
        $return['items'] = '*';
        return $return;
    }

    public function _forCreateMediaType()
    {
        $return['method'] = 'mediatype.create';
        $return['params'] = [
            'name' => $this->_makeMediaTypeName(),
            'type' => 0,
            'smtp_server' => $this->getSmtpHost(),
            'smtp_port' => $this->getSmtpPort(),
            'smtp_security' => $this->getSmtpSsl(),
            'smtp_helo' => $this->getSmtpHost(),
            'smtp_email' => $this->getSmtpSender(),
            'username' => $this->getSmtpUser(),
            'passwd' => $this->getSmtpPwd(),
            'smtp_authentication' => 1, // TODO : Manage anonymus
        ];
        $return['items'] = 'mediatypeids';
        return $return;
    }

    public function _forUpdateMediaType($params)
    {
        $return['method'] = 'mediatype.update';
        $return['params'] = $params;
        $return['items'] = 'mediatypeids';
        return $return;
    }

    public function _forDeleteMediaType()
    {
        $return['method'] = 'mediatype.delete';
        $return['params'] = [$this->getIdMediatype()];
        $return['items'] = 'mediatypeids';
        return $return;
    }

    public function _needchangeChild()
    {
        return null;
    }

    // **********
    // UserGroup
    // **********

    public function userGroup()
    {
        $data = false;
        $findbys = ['id','name'];
        foreach ($findbys as $findby) {
            $var = $this->_forGetUserGroup($findby);
            if (!$var) continue;

            $data = $this->_zm->requestGet($var);
            if ($data) break;
        }
        if (!empty($data['usrgrpid'])) $this->setIdUsergroup($data['usrgrpid']);

        $zp = $this->whereUserGroup();

        if ($this == $zp) {
            if(!$data){
                $data = $this->_zm->requestChange($this->_forCreateUserGroup());
            }else{
                $needUpdate = $this->_needUpdateUserGroup($data);
                if ($needUpdate) {
                    $this->_zm->requestChange($needUpdate);
                }
            }
        } else {
            $data = false;
            foreach ($findbys as $findby) {
                $var = $zp->_forGetUserGroup($findby);
                if (!$var) continue;

                $data = $this->_zm->requestGet($var);
                if ($data) break;
            }
            if(!$data){
                $data = $this->_zm->requestChange($zp->_forCreateUserGroup());
            }else{
                $needUpdate = $zp->_needUpdateUserGroup($data);
                if ($needUpdate) {
                    $this->_zm->requestChange($needUpdate);
                }
                // $zp->_needchangeChild(); // TODO : Manage the change ID in the action
                $this->_zm->requestChange($this->_forDeleteUserGroup());
            }
        }

        if (empty($data['usrgrpid'])) die('Cannot create UserGroup');

        $this->setIdMediatype($data['usrgrpid']);
    }

    public function whereUserGroup()
    {
        return $this->hasReseller() ? $this->reseller : $this;
    }

    public function _needUpdateUserGroup($data)
    {
        $init = [
            'name' => $this->_makeUserGroupName(),
        ];
        $need_change = [];
        foreach ($data as $key=>$value){
            if (isset($init[$key]) and $init[$key] <> $value) {
                $need_change[$key] = $init[$key];
            }
        }
        if ($need_change){
            $need_change['usrgrpid'] = $data['usrgrpid'];
            return $this->_forUpdateUserGroup($need_change);
        }
        return false;
    }

    public function _makeUserGroupName(){
        return implode($this->getIspGlue(), [$this->getIspKeyword(), $this->getId()]);
    }

    public function _forGetUserGroup($findby)
    {
        $return['method'] = 'usergroup.get';
        if ($findby == 'id'){
            if (!$this->getIdUsergroup()) return false;
            $return['params'] = ['usrgrpids' => $this->getIdUsergroup()];
        } elseif ($findby == 'name') {
            $return['params'] = ['filter' => ['name' => $this->_makeUserGroupName()]];
        } else {
            return false;
        }
        $return['items'] = '*';//'usrgrpid';
        return $return;
    }

    public function _forCreateUserGroup()
    {
        $return['method'] = 'usergroup.create';
        $return['params'] = [
            'name' => $this->_makeUserGroupName(),
        ];
        $return['items'] = 'usrgrpids';
        return $return;
    }

    public function _forUpdateUserGroup($params)
    {
        $return['method'] = 'usergroup.update';
        $return['params'] = $params;
        $return['items'] = 'usrgrpids';
        return $return;
    }

    public function _forDeleteUserGroup()
    {
        $return['method'] = 'usergroup.delete';
        $return['params'] = [$this->getIdUsergroup()];
        $return['items'] = 'usrgrpids';
        return $return;
    }

    // **********
    // User
    // **********

    public function user()
    {
        $data = false;
        $findbys = ['id','name'];
        foreach ($findbys as $findby) {
            $var = $this->_forGetUser($findby);
            if (!$var) continue;

            $data = $this->_zm->requestGet($var);
            if ($data) break;
        }
        if (!empty($data['usrid'])) $this->setIdUser($data['usrid']);
        if(!$data){
            $data = $this->_zm->requestChange($this->_forCreateUser());
        }else{
            $needUpdate = $this->_needUpdateUser($data);
            if ($needUpdate) {
                $this->_zm->requestChange($needUpdate);
            }
        }
        if (empty($data['usrid'])) die('Cannot create Usr');
    }

    public function _needUpdateUser($data){
        $init = [
            'alias' => $this->getZabbixUser(),
            'name' => $this->getInfoClient('contact_name'),
            'passwd' => $this->getZabbixPwd(),
            'roleid' => 1
        ];
        $need_change = [];
        foreach ($data as $key=>$value){
            if (isset($init[$key]) and $init[$key] <> $value) {
                $need_change[$key] = $init[$key];
            }
        }
        if ($this->_needUpdateReceiver($data['medias'])){
            $need_change['medias'] = $data['medias'];
            $need_change['medias'][] = ['mediatypeid' => $this->getIdMediatype(),'sendto'=>$this->getReceiver()];
        }
        if ($this->_needUpdateUsrGrp($data['usrgrps'])){
            $need_change['usrgrps'] = $data['usrgrps'];
            $need_change['usrgrps'] = ['usrgrpid' => $this->getIdUsergroup()];
        }
        if ($need_change){
            if (empty($need_change['medias'])) $need_change['medias'] = $data['medias'];
            if (empty($need_change['usrgrps'])) $need_change['usrgrps'] = $data['usrgrps'];
            $need_change['userid'] = $data['userid'];
            return $this->_forUpdateUser($need_change);
        }
        return false;
    }

    public function _needUpdateReceiver($data){
        $find_email = false;
        foreach ($data as $media) {
            if (isset($media['sendto'])){
                if(is_array($media['sendto'])){
                    foreach ($media['sendto'] as $email){
                        if ($email == $this->getReceiver()){
                            $find_email = true;
                            break;
                        }
                    }
                } else{
                    $find_email = $media['sendto'] == $this->getReceiver();
                }
            }
            if ($find_email) break;
        }
        return !$find_email;
    }

    public function _needUpdateUsrGrp($data){
        $find_usrgrp = false;
        foreach ($data as $usrgrp) {
            if (isset($usrgrp['usrgrpid'])){
                $find_usrgrp = $usrgrp['usrgrpid'] == $this->getIdUsergroup();
            }
            if ($find_usrgrp) break;
        }
        return !$find_usrgrp;
    }

    public function _forGetUser($findby)
    {
        $return['method'] = 'user.get';
        if ($findby == 'id'){
            if (!$this->getIdUser()) return false;
            $return['params'] = ['userids' => $this->getIdUser()];
        } elseif ($findby == 'name') {
            $return['params'] = ['filter' => ['alias' => $this->getZabbixUser()]];
        } else {
            return false;
        }
        $return['items'] = '*';//'usrid';
        return $return;
    }

    public function _forCreateUser()
    {
        $return['method'] = 'user.create';
        $return['params'] = [
            'alias' => $this->getZabbixUser(),
            'name' => $this->getInfoClient('contact_name'),
            'passwd' => $this->getZabbixPwd(),
            'roleid' => 1,
            'usrgrps' => [
                [
                    'usrgrpid' => $this->getIdUsergroup(),
                ],
            ],
            'medias' => [
                [
                    'mediatypeid' => $this->getIdMediatype(),
                    'sendto' => $this->getReceiver(),
                ],
            ]
        ];
        $return['items'] = 'usrids';
        return $return;
    }

    public function _forUpdateUser($params)
    {
        $return['method'] = 'user.update';
        $return['params'] = $params;
        $return['items'] = 'usrgrpids';
        return $return;
    }

    public function _forDeleteUser()
    {
        $return['method'] = 'user.delete';
        $return['params'] = [$this->getIdUser()];
        $return['items'] = 'usrids';
        return $return;
    }

    public function has_messages(){
        return $this->_zm->has_messages();
    }

    public function get_messages(){
        return $this->_zm->get_messages();
    }

    public function get_messages_html(){
        return $this->_zm->get_messages_html();
    }


    private function isTemporalParam($param){
        return substr($param,0,1) == '_';
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
     * @return bool
     */
    public function getEnableConnexion(): bool
    {
        return $this->enable_connexion == 'y';
    }

    /**
     * @param string $enable_connexion
     */
    public function setEnableConnexion(string $enable_connexion): void
    {
        $this->enable_connexion = $enable_connexion;
    }

    /**
     * @return bool
     */
    public function getEnableTrend(): bool
    {
        return $this->enable_trend == 'y';
    }

    /**
     * @param string $enable_trend
     */
    public function setEnableTrend(string $enable_trend): void
    {
        $this->enable_trend = $enable_trend;
    }

    /**
     * @return bool
     */
    public function getEnableEvent(): bool
    {
        return $this->enable_event == 'y';
    }

    /**
     * @param string $enable_event
     */
    public function setEnableEvent(string $enable_event): void
    {
        $this->enable_event = $enable_event;
    }

    /**
     * @return bool
     */
    public function getEnableSmtp(): bool
    {
        return $this->enable_smtp == 'y';
    }

    /**
     * @param string $enable_smtp
     */
    public function setEnableSmtp(string $enable_smtp): void
    {
        $this->enable_smtp = $enable_smtp;
    }

    /**
     * @return bool
     */
    public function getEnableAlert(): bool
    {
        return $this->enable_alert == 'y';
    }

    /**
     * @param string $enable_alert
     */
    public function setEnableAlert(string $enable_alert): void
    {
        $this->enable_alert = $enable_alert;
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

    /**
     * @return mixed
     */
    public function getIdMediatype()
    {
        return $this->id_mediatype;
    }

    /**
     * @param mixed $id_mediatype
     */
    public function setIdMediatype($id_mediatype): void
    {
        $this->id_mediatype = $id_mediatype;
    }

    /**
     * @return mixed
     */
    public function getIdUsergroup()
    {
        return $this->id_usergroup;
    }

    /**
     * @param mixed $id_usergroup
     */
    public function setIdUsergroup($id_usergroup): void
    {
        $this->id_usergroup = $id_usergroup;
    }

    /**
     * @return mixed
     */
    public function getIdUser()
    {
        return $this->id_user;
    }

    /**
     * @param $user
     */
    public function setIdUser($user): void
    {
        if (is_array($user)){
            $this->id_user = $user['userid'];
            $this->_medias = isset($user['medias']) ? $user['medias'] : [];
            $this->_usrgrps = isset($user['usrgrps']) ? $user['usrgrps'] : [];
        }else{
            $this->id_user = $user;
        }
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
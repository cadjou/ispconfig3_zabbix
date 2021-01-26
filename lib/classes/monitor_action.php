<?php

class monitor_action extends tform_actions
{
    /**
     * @var zabbix
     */
    protected $zabbix;

    protected $domain_data;

    protected $zabbix_data_admin;

    protected $zabbix_data_reseller;

    protected $zabbix_data_user;

    function __construct()
    {
        global $app;
        $this->zabbix_data_admin = $app->db->queryOneRecord('SELECT * FROM zabbix_admin WHERE admin_id=1');
    }

    function onSubmit()
    {
        global $app;
        // Check data form
        $this->domain_data = $app->db->queryOneRecord('SELECT domain_id,sys_userid,sys_groupid FROM web_domain , sys_group WHERE web_domain.type = "vhost" AND active ="y"  AND domain_id = ? AND ' . $app->tform->getAuthSQL('ru','web_domain'),$this->dataRecord['domain_id']);
        if ($this->dataRecord['domain_id'] <> $this->domain_data['domain_id']){
            $app->tform->errorMessage .= 'Error: No permission on this domain<br>';
        }
        parent::onSubmit();
    }

    function onAfterInsert()
    {
        global $app;
        $app->db->query("UPDATE zabbix_monitor SET sys_userid = ?, sys_perm_user = 'ru', sys_groupid = ?, sys_perm_group = 'ru' WHERE monitor_id = ?", $this->domain_data['sys_userid'],$this->domain_data['sys_groupid'], $this->id);
        $this->get_zabbix_parameters();
    }

    function onAfterUpdate()
    {
        global $app;
        $this->get_zabbix_parameters();
    }

    function onBeforeDelete()
    {
        global $app;
        $this->get_zabbix_parameters();
    }

    function get_zabbix_parameters()
    {
        global $app;
        //$client_group_id = $_SESSION["s"]["user"]["default_group"];
        $domain_data = $app->db->queryOneRecord('SELECT web_domain.*, client.*, server.server_name FROM web_domain, server, sys_group, client WHERE server.server_id = web_domain.server_id AND web_domain.domain_id = ? AND sys_group.groupid = web_domain.sys_groupid AND sys_group.client_id = client.client_id', $this->dataRecord['domain_id']);
        $parameter_admin = '';
        $parameter_reseller = '';
        $parameter_reseller = '';

        //echo nl2br(print_r($domain_data, true)) . '<br>';
        if (isset($domain_data['sys_userid']) and $this->id)
        {
            $this->dataRecord['active'] = isset($this->dataRecord['active']) ? $this->dataRecord['active'] : 'n';
            $data = array_merge($domain_data,$this->dataRecord);
            $data['insertID'] = $this->id;
            //echo nl2br(print_r($this->dataRecord,true));
            //echo nl2br(print_r($this->oldDataRecord,true));
            if (empty($this->oldDataRecord)) {
                $this->zabbix = new \zabbix($this->zabbix_data_admin,new \zabbix_monitor($data));
            } else {
                $this->zabbix = new \zabbix($this->zabbix_data_admin,new \zabbix_monitor($data),unserialize($this->oldDataRecord['object']));
            }
            if ($zabbix_monitor = $this->zabbix->run()) {
                $app->db->query('UPDATE `zabbix_monitor` SET `object` = ? WHERE monitor_id = ?',serialize($zabbix_monitor),$this->id);
            } else {
                $app->db->query('DELETE FROM zabbix_monitor WHERE monitor_id = ?',$this->id);
                $this->id = null;
                $app->error($app->tform->wordbook["limit_webdav_user_txt"]);
            }
        }
    }

    protected function getAdminParams(){

    }

    protected function getResellerParams(){

    }

    protected function getClientParams(){

    }
}
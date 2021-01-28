DROP TABLE IF EXISTS `zabbix_monitor`;
CREATE TABLE `zabbix_monitor` (
  `monitor_id` int(11) UNSIGNED NOT NULL,
  `sys_userid` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `sys_groupid` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `sys_perm_user` varchar(5) DEFAULT NULL,
  `sys_perm_group` varchar(5) DEFAULT NULL,
  `sys_perm_other` varchar(5) DEFAULT NULL,
  `domain_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `url_path` varchar(255) DEFAULT '/',
  `check_period` varchar(7) DEFAULT NULL,
  `timeout` varchar(7) DEFAULT NULL,
  `code_status` varchar(7) DEFAULT NULL,
  `retries` varchar(7) DEFAULT NULL,
  `string_search` varchar(255) DEFAULT NULL,
  `active` enum('n','y') NOT NULL DEFAULT 'y',
  `object` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

ALTER TABLE `zabbix_monitor`
  ADD PRIMARY KEY (`monitor_id`);

ALTER TABLE `zabbix_monitor`
  MODIFY `monitor_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `zabbix_client`;
CREATE TABLE `zabbix_client` (
  `client_id` int(11) UNSIGNED NOT NULL,
  -- Server Zabbix --
  `zabbix_user` varchar(64) NOT NULL,
  `zabbix_pwd` varchar(64) NOT NULL,
  -- Email --
  `receiver` varchar(255) DEFAULT NULL,
  `alert_subject` varchar(255) DEFAULT NULL,
  `alert_content` text DEFAULT NULL,
  `recovery_subject` varchar(255) DEFAULT NULL,
  `recovery_content` text DEFAULT NULL,
  -- Constants --
  `httptest_keyword` varchar(20) DEFAULT 'Web check',
  `step_keyword` varchar(30) DEFAULT 'Check Mainpage',
  `application_keyword` varchar(40) DEFAULT 'Web Scenario',
  `trigger_keyword` varchar(40) DEFAULT 'Web Scenario Fail',
  -- Limits client --
  `enable_connexion` varchar(1) DEFAULT 'y',
  `enable_trend` varchar(1) DEFAULT 'y',
  `enable_event` varchar(1) DEFAULT 'y',
  `enable_alert` varchar(1) DEFAULT 'y',
  `enable_smtp` varchar(1) DEFAULT 'y',
  -- Limits Monitor --
  `limit_monitor` varchar(7) DEFAULT '-1',
  `limit_check_period` varchar(7) DEFAULT '15m',
  `limit_retries` int(2) DEFAULT '10',
  `limit_timeout` varchar(7) DEFAULT '120s',
  -- Default Monitor --
  `default_check_period` varchar(7) DEFAULT '60m',
  `default_retries` int(2) DEFAULT '5',
  `default_timeout` varchar(7) DEFAULT '20s',
  `default_status_codes` int(3) DEFAULT '200',
  -- Default SMTP --
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` int(10) DEFAULT '587',
  `smtp_ssl` varchar(1) DEFAULT 'y',
  `smtp_user` varchar(255) DEFAULT NULL,
  `smtp_pwd` varchar(64) DEFAULT NULL,
  `smtp_sender` varchar(64) DEFAULT NULL,
  -- Zabbix ID --
  `id_mediatype` int(11) DEFAULT NULL,
  `id_usergroup` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

ALTER TABLE `zabbix_client`
  ADD PRIMARY KEY (`user_id`);

ALTER TABLE `zabbix_client`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `zabbix_admin`;
CREATE TABLE `zabbix_admin` (
  `admin_id` int(11) UNSIGNED NOT NULL,
  -- Server Zabbix --
  `zabbix_shared` varchar(1) DEFAULT 'n',
  `zabbix_host` varchar(255) NOT NULL,
  `zabbix_user` varchar(64) NOT NULL,
  `zabbix_pwd` varchar(64) NOT NULL,
  -- Email --
  `receiver` varchar(255) DEFAULT NULL,
  `alert_subject` varchar(255) DEFAULT NULL,
  `alert_content` text DEFAULT NULL,
  `recovery_subject` varchar(255) DEFAULT NULL,
  `recovery_content` text DEFAULT NULL,
  -- Constants --
  `isp_glue` varchar(1) DEFAULT '-',
  `isp_keyword` varchar(10) DEFAULT 'isp',
  `httptest_keyword` varchar(20) DEFAULT 'Web check',
  `step_keyword` varchar(30) DEFAULT 'Check Mainpage',
  `application_keyword` varchar(40) DEFAULT 'Web Scenario',
  `trigger_keyword` varchar(40) DEFAULT 'Web Scenario Fail',
  -- Limits Admin --
  `limit_monitor` varchar(7) DEFAULT '-1',
  `limit_check_period` varchar(7) DEFAULT '15m',
  `limit_retries` int(2) DEFAULT '10',
  `limit_timeout` varchar(7) DEFAULT '120s',
  -- Default Monitor --
  `default_check_period` varchar(7) DEFAULT '60m',
  `default_retries` int(2) DEFAULT '5',
  `default_timeout` varchar(7) DEFAULT '20s',
  `default_status_codes` int(3) DEFAULT '200',
  -- Default SMTP --
  `smtp_host` varchar(255) DEFAULT NULL,
  `smtp_port` int(10) DEFAULT '587',
  `smtp_ssl` varchar(1) DEFAULT 'y',
  `smtp_user` varchar(255) DEFAULT NULL,
  `smtp_pwd` varchar(64) DEFAULT NULL,
  `smtp_sender` varchar(64) DEFAULT NULL,
  -- Zabbix ID --
  `id_mediatype` int(11) DEFAULT NULL,
  `id_usergroup` int(11) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

ALTER TABLE `zabbix_admin`
  ADD PRIMARY KEY (`admin_id`);

ALTER TABLE `zabbix_admin`
  MODIFY `admin_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
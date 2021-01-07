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
  `user_id` int(11) UNSIGNED NOT NULL,
  `sys_userid` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `sys_groupid` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `sys_perm_user` varchar(5) DEFAULT NULL,
  `sys_perm_group` varchar(5) DEFAULT NULL,
  `sys_perm_other` varchar(5) DEFAULT NULL,
  `client_id` int(11) UNSIGNED NOT NULL,
  `parent_client_id` int(11) UNSIGNED NOT NULL DEFAULT '0',
  `email` varchar(255) DEFAULT NULL,
  `pwd` varchar(64) DEFAULT NULL,
  `alert_subject` varchar(255) DEFAULT NULL,
  `alert_content` text DEFAULT NULL,
  `recovery_subject` varchar(255) DEFAULT NULL,
  `recovery_content` text DEFAULT NULL,
  `limit_monitor` varchar(7) DEFAULT '-1',
  `limit_user` varchar(1) DEFAULT 'n',
  `limit_def_check_period` varchar(7) DEFAULT '60m',
  `limit_min_check_period` varchar(7) DEFAULT '15m',
  `limit_def_retries` int(2) DEFAULT '5',
  `limit_max_retries` int(2) DEFAULT '10',
  `limit_def_timeout` varchar(7) DEFAULT '20s',
  `limit_max_timeout` varchar(7) DEFAULT '120s',
  `limit_def_status_codes` int(3) DEFAULT '200',
  `object` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

ALTER TABLE `zabbix_client`
  ADD PRIMARY KEY (`user_id`);

ALTER TABLE `zabbix_client`
  MODIFY `user_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

DROP TABLE IF EXISTS `zabbix_admin`;
CREATE TABLE `zabbix_admin` (
  `admin_id` int(11) UNSIGNED NOT NULL,
  `server` varchar(255) NOT NULL,
  `user` varchar(64) NOT NULL,
  `pwd` varchar(64) NOT NULL,
  `smtp_server` varchar(255) DEFAULT NULL,
  `smtp_port` int(10) DEFAULT '587',
  `smtp_ssl` varchar(1) DEFAULT 'y',
  `smtp_email` varchar(255) DEFAULT NULL,
  `smtp_user` varchar(64) DEFAULT NULL,
  `smtp_pwd` varchar(64) DEFAULT NULL,
  `isp_glue` varchar(1) DEFAULT '-',
  `isp_keyword` varchar(10) DEFAULT 'isp',
  `httptest_keyword` varchar(20) DEFAULT 'Web check',
  `step_keyword` varchar(30) DEFAULT 'Check Mainpage',
  `application_keyword` varchar(40) DEFAULT 'Web Scenario',
  `trigger_keyword` varchar(40) DEFAULT 'Web Scenario Fail',
  `email` varchar(255) DEFAULT NULL,
  `alert_subject` varchar(255) DEFAULT NULL,
  `alert_content` text DEFAULT NULL,
  `recovery_subject` varchar(255) DEFAULT NULL,
  `recovery_content` text DEFAULT NULL,
  `limit_monitor` varchar(7) DEFAULT '-1',
  `limit_reseler` varchar(1) DEFAULT 'n',
  `limit_user` varchar(1) DEFAULT 'n',
  `limit_def_check_period` varchar(7) DEFAULT '60m',
  `limit_min_check_period` varchar(7) DEFAULT '15m',
  `limit_def_retries` int(2) DEFAULT '5',
  `limit_max_retries` int(2) DEFAULT '10',
  `limit_def_timeout` varchar(7) DEFAULT '20s',
  `limit_max_timeout` varchar(7) DEFAULT '120s',
  `limit_def_status_codes` int(3) DEFAULT '200'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

ALTER TABLE `zabbix_admin`
  ADD PRIMARY KEY (`admin_id`);

ALTER TABLE `zabbix_admin`
  MODIFY `admin_id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
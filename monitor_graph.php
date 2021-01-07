<?php
$app = require_once __DIR__ . '/lib/init_module.php';

require_once __DIR__ . '/lib/classes/zabbix_manager.php';
require_once __DIR__ . '/lib/classes/zabbix_monitor.php';
require_once __DIR__ . '/lib/classes/zabbix_trend.php';

//* Check permissions for module
$app->auth->check_module_permissions('zabbix');

// Loading the template
$app->uses('tpl');
$app->tpl->newTemplate("form.tpl.htm");
$app->tpl->setInclude('content_tpl', 'templates/monitor_graph.htm');

$zabbix_data_admin = $app->db->queryOneRecord('SELECT * FROM zabbix_admin WHERE admin_id=1');
$zabbix_trend = (isset($_SESSION['zabbix'],$_SESSION['zabbix']['trend_obj'])) ? unserialize($_SESSION['zabbix']['trend_obj']) : new zabbix_trend($zabbix_data_admin);

$app->tpl->setVar('refresh', $zabbix_trend->getRefreshOption());
$app->tpl->setVar('start_date', $zabbix_trend->getTrendStartDate());
$app->tpl->setVar('end_date', $zabbix_trend->getTrendEndDate());
$app->tpl->setVar('domaines', $zabbix_trend->getMonitor());
$app->tpl->setVar('datatrend', $zabbix_trend->getDataTrend());
$app->tpl->setVar("list_head_txt", $app->lng("list_head_txt"));
echo $zabbix_trend->get_messages_html();
$app->tpl_defaults();
$app->tpl->pparse();


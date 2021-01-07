<?php
$app = require_once __DIR__ . '/lib/init_module.php';

$tform_def_file = "form/monitor.tform.php";


// Loading classes
$app->uses('tpl,tform,tform_actions,tools_sites');

require_once __DIR__ . '/lib/classes/zabbix_manager.php';
require_once __DIR__ . '/lib/classes/zabbix.php';
require_once __DIR__ . '/lib/classes/zabbix_monitor.php';
require_once __DIR__ . '/lib/classes/monitor_action.php';

$page = new monitor_action;
$page->onLoad();

?>

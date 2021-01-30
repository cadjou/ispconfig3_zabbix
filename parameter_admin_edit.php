<?php
$app = require_once __DIR__ . '/lib/init_module.php';

$tform_def_file = "form/parameter_admin.tform.php";

//* Check permissions for Admin
if(!$app->auth->is_admin()) die('Access only for administrators.');

// Loading classes
$app->uses('tpl,tform,tform_actions,tools_sites');

require_once __DIR__ . '/lib/classes/zabbix_manager.php';
require_once __DIR__ . '/lib/classes/zabbix_parameter.php';
require_once __DIR__ . '/lib/classes/ispconfig_zabbix.php';
require_once __DIR__ . '/lib/classes/parameter_actions.php';

$page = new parameter_actions('admin');
$page->onLoad();
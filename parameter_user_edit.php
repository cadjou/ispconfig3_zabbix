<?php
$app = require_once __DIR__ . '/lib/init_module.php';

$tform_def_file = "form/parameter_user.tform.php";

// Loading classes
$app->uses('tpl,tform,tform_actions,tools_sites');

if (!$this->auth->has_clients($_SESSION['s']['user']['userid']) and $_GET['type'] == 'reseller') die('Don\'t try to hack please.');

require_once __DIR__ . '/lib/classes/zabbix_manager.php';
require_once __DIR__ . '/lib/classes/ispconfig_zabbix.php';
require_once __DIR__ . '/lib/classes/parameter_actions.php';

$page = new parameter_actions($_GET['type']);
$page->onLoad();


<?php

/******************************************
 * Begin Form configuration
 ******************************************/

$tform_def_file = "form/parameters_admin.tform.php";

/******************************************
 * End Form configuration
 ******************************************/

require_once '../../lib/config.inc.php';
require_once '../../lib/app.inc.php';


//* Check permissions for module
if($_SESSION["s"]["user"]["typ"] != 'admin') die('Access only for administrators.');
$app->auth->check_module_permissions('zabbix');

// Loading classes
$app->uses('tpl,tform,tform_actions,tools_sites');
$app->load('tform_actions');

$page = new tform_actions;
$page->onLoad();
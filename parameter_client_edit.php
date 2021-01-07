<?php

/******************************************
 * Begin Form configuration
 ******************************************/

$tform_def_file = "form/parameter_client.tform.php";

/******************************************
 * End Form configuration
 ******************************************/

require_once '../../lib/config.inc.php';
require_once '../../lib/app.inc.php';

//* Check permissions for module
$app->auth->check_module_permissions('zabbix');

// Loading classes
$app->uses('tpl,tform,tools_sites');
$app->load('tform_actions');

require_once __DIR__ . '/lib/classes/parameter_actions.php';

$page = new parameter_actions('client');
$page->onLoad();
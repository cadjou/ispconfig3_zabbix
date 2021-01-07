<?php
$app = require_once __DIR__ . '/lib/init_module.php';

$list_def_file = "list/monitor.list.php";

//* Check permissions for module
$app->auth->check_module_permissions('zabbix');

$app->load('listform_actions');

$app->listform_actions->onLoad();
?>

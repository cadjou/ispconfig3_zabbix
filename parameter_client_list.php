<?php
$app = require_once __DIR__ . '/lib/init_module.php';

$list_def_file = "list/parameter_client.list.php";

//* Check permissions for module
$app->auth->check_module_permissions('client');

$app->uses('listform_actions');

require_once __DIR__ . '/lib/classes/list_actions.php';

$_SESSION['zabbix']['parameters']['type'] = 'client';

$list_action = new list_actions;
$list_action->SQLOrderBy = 'ORDER BY client.company_name, client.contact_name, client.client_id';
$list_action->SQLExtWhere = "client.limit_client = 0";
$list_action->onLoad();
?>

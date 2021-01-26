<?php
$app = require_once __DIR__ . '/lib/init_module.php';

$list_def_file = "list/parameter_reseller.list.php";

//* Check permissions for module
$app->auth->check_module_permissions('client');

if(!$app->auth->is_admin()) die('Access only for administrators.');

$app->uses('listform_actions');

require_once __DIR__ . '/lib/classes/list_actions.php';

$_SESSION['zabbix']['parameters']['type'] = 'reseller';

$list_action = new list_actions;
$list_action->SQLOrderBy = 'ORDER BY client.company_name, client.contact_name, client.client_id';
$list_action->SQLExtWhere = "(client.limit_client > 0 or client.limit_client = -1)";
$list_action->SQLExtSelect = ', LOWER(client.country) as countryiso';
$list_action->onLoad();

?>
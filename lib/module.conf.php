<?php

//* Name of the module. The module name must match the name of the module directory. The module name may not contain spaces.
$module['name']      = 'zabbix';

//* Title of the module. The title is dispalayed in the top navigation.
$module['title']     = 'Zabbix';

//* The template file of the module. This is always module.tpl.htm if you do not have any special requirements like a 3 column layout.
$module['template']  = 'module.tpl.htm';

//* The page that is displayed when the module is loaded. the path must is relative to the web directory

$module['startpage'] = 'zabbix/monitor_list.php';

//* The width of the tab. Normally you should leave this empty and let the browser define the width automatically.
$module['tab_width'] = '';

$module['order']    = '21';



//*** Menu Definition *****************************************

//* make sure that the items array is empty
$items = [];

//* Add a menu item with the label 'Send message'
$items[] = [
	'title'   => 'Monitors',
	'target'  => 'content',
	'link'    => 'zabbix/monitor_list.php',
	'html_id' => 'zabbix_monitor_list'
];

$items[] = [
	'title'   => 'Trends',
	'target'  => 'content',
	'link'    => 'zabbix/monitor_graph.php',
	'html_id' => 'zabbix_monitor_graph'
];

$items[] = [
	'title'   => 'Alerts',
	'target'  => 'content',
	'link'    => 'zabbix/monitor_alert.php',
	'html_id' => 'zabbix_monitor_alert'
];

//* Add the menu items defined above to a menu section labeled 'Support'
$module['nav'][] = [
	'title' => 'Monitor',
	'open'  => 1,
	'items' => $items
];

$items = [];
if($_SESSION["s"]["user"]["typ"] == 'admin') {
	$items[] = [
		'title' => 'Admin Parameters',
		'target' => 'content',
		'link' => 'zabbix/parameter_admin_edit.php?id=1',
		'html_id' => 'parameters_admin'
	];
	$items[] = [
		'title'   => 'Reseler Parameters',
		'target'  => 'content',
		'link'    => 'zabbix/parameter_reseller_list.php',
		'html_id' => 'reseller_list'
	];
	$items[] = [
		'title'   => 'Client Parameters',
		'target'  => 'content',
		'link'    => 'zabbix/parameter_client_list.php',
		'html_id' => 'client_list'
	];

} elseif($app->auth->has_clients($_SESSION['s']['user']['userid'])){
	$items[] = [
		'title'   => 'Reseler Parameters',
		'target'  => 'content',
		'link'    => 'zabbix/parameter_user_edit.php?type=reseller&id=' . $_SESSION['s']['user']['client_id'],
		'html_id' => 'reseller_list'
	];
	$items[] = [
		'title'   => 'Client Parameters',
		'target'  => 'content',
		'link'    => 'zabbix/parameter_client_list.php',
		'html_id' => 'client_list'
	];
} else {
	$items[] = [
		'title'   => 'Client Parameters',
		'target'  => 'content',
		'link'    => 'zabbix/parameter_user_edit.php?type=client&id=' . $_SESSION['s']['user']['client_id'],
		'html_id' => 'client_list'
	];
}

$module['nav'][] = [
	'title' => 'Parameters',
	'open'  => 1,
	'items' => $items
];
?>

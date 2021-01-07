<?php
$app = require_once __DIR__ . '/lib/init_module.php';

$tform_def_file = "form/parameter_reseller.tform.php";

// Loading classes
$app->uses('tpl,tform,tools_sites');

require_once __DIR__ . '/lib/classes/parameter_actions.php';

$page = new parameter_actions('reseller');
$page->onLoad();


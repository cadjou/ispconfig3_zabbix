<?php

/*
	Datatypes:
	- INTEGER
	- DOUBLE
	- CURRENCY
	- VARCHAR
	- TEXT
	- DATE
*/



// Name of the list
$liste["name"]    = "parameter_reseller";

// Database table
$liste["table"]   = "client";

// Index index field of the database table
$liste["table_idx"]  = "client_id";

// Search Field Prefix
$liste["search_prefix"]  = "search_";

// Records per page
$liste["records_per_page"]  = "15";

// Script File of the list
$liste["file"]   = "parameter_reseller_list.php";

// Script file of the edit form
$liste["edit_file"]  = "parameter_reseller_edit.php";

// Script File of the delete script
$liste["delete_file"]  = "parameters_reseller_del.php";

// Paging Template
$liste["paging_tpl"]  = "templates/paging.tpl.htm";

// Enable authe
$liste["auth"]   = "yes";


/*****************************************************
 * Suchfelder
 *****************************************************/

$liste["item"][] = [
    'field'     => "client_id",
    'datatype' => "INTEGER",
    'formtype' => "TEXT",
    'op' => "=",
    'prefix' => "",
    'suffix' => "",
    'width' => "",
    'value' => ""
];

$liste["item"][] = [
    'field' => "company_name",
    'datatype' => "VARCHAR",
    'formtype' => "TEXT",
    'op' => "like",
    'prefix' => "%",
    'suffix' => "%",
    'width' => "",
    'value' => ""
];

$liste["item"][] = [
    'field' => "contact_name",
    'datatype' => "VARCHAR",
    'formtype' => "TEXT",
    'op' => "like",
    'prefix' => "%",
    'suffix' => "%",
    'width' => "",
    'value' => ""
];

$liste["item"][] = [
    'field'     => "customer_no",
    'datatype' => "VARCHAR",
    'formtype' => "TEXT",
    'op' => "like",
    'prefix' => "%",
    'suffix' => "%",
    'width' => "",
    'value' => ""
];

$liste["item"][] = [
    'field'     => "username",
    'datatype' => "VARCHAR",
    'formtype' => "TEXT",
    'op' => "like",
    'prefix' => "%",
    'suffix' => "%",
    'width' => "",
    'value' => ""
];

?>

<?php

/*
	Form Definition

	Tabledefinition

	Datatypes:
	- INTEGER (Forces the input to Int)
	- DOUBLE
	- CURRENCY (Formats the values to currency notation)
	- VARCHAR (no format check, maxlength: 255)
	- TEXT (no format check)
	- DATE (Dateformat, automatic conversion to timestamps)

	Formtype:
	- TEXT (Textfield)
	- TEXTAREA (Textarea)
	- PASSWORD (Password textfield, input is not shown when edited)
	- SELECT (Select option field)
	- RADIO
	- CHECKBOX
	- CHECKBOXARRAY
	- FILE

	VALUE:
	- Wert oder Array

	Hint:
	The ID field of the database table is not part of the datafield definition.
	The ID field must be always auto incement (int or bigint).

	Search:
	- searchable = 1 or searchable = 2 include the field in the search
	- searchable = 1: this field will be the title of the search result
	- searchable = 2: this field will be included in the description of the search result


*/

$form["title"]			= 'Zabbix Client';
$form["description"]	= "";
$form["name"]			= "zabbix_admin";
$form["action"]			= "parameter_client_edit.php";
$form["db_table"]		= "zabbix_client";
$form["db_table_idx"]	= "client_id";
$form["db_history"]		= "yes";
$form["tab_default"]	= "parameters"; // Onglet par defaut
$form["list_default"]	= "parameter_client_list.php";
$form["auth"]  = 'no'; // yes / no

// TODO : Add email parameters

$form["tabs"]['parameters'] = [
	'title'  => 'Zabbix Client Parameters',
	'width'  => 100,
	'template'  => "templates/parameter_client_edit.htm",
	'readonly' => false,
    'fields'  => [
        //#################################
        // Begin Datatable fields
        //#################################
        'client_id' => [
            'datatype' => 'INTEGER',
            'formtype' => 'HIDDEN',
            'default' => '',
            'value'  => ''
        ],
        'limit_monitor' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '-1',
            'value'  => ''
        ],
        'limit_user' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'n',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'limit_def_check_period' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '60m',
            'value'  => ''
        ],
        'limit_min_check_period' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '15m',
            'value'  => ''
        ],
        'limit_def_retries' => [
            'datatype' => 'INTEGER',
            'formtype' => 'TEXT',
            'default' => '5',
            'value'  => ''
        ],
        'limit_max_retries' => [
            'datatype' => 'INTEGER',
            'formtype' => 'TEXT',
            'default' => '10',
            'value'  => ''
        ],
        'limit_def_timeout' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '20s',
            'value'  => ''
        ],
        'limit_max_timeout' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '120s',
            'value'  => ''
        ],
        'limit_def_status_codes' => [
            'datatype' => 'INTEGER',
            'formtype' => 'TEXT',
            'default' => '200',
            'value'  => ''
        ],
    ],
];
?>
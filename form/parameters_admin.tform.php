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

$form["title"]			= 'Zabbix parameters';
$form["description"]	= "";
$form["name"]			= "zabbix_admin";
$form["action"]			= "parameter_admin_edit.php";
$form["db_table"]		= "zabbix_admin";
$form["db_table_idx"]	= "admin_id";
$form["db_history"]		= "yes";
$form["tab_default"]	= "parameters"; // Onglet par defaut
$form["list_default"]	= "monitor_list.php";
$form["auth"]  = 'yes'; // yes / no

$form["auth_preset"]["userid"]  = 0; // 0 = id of the user, > 0 id must match with id of current user
$form["auth_preset"]["groupid"] = 0; // 0 = default groupid of the user, > 0 id must match with groupid of current user
$form["auth_preset"]["perm_user"] = 'riud'; //r = read, i = insert, u = update, d = delete
$form["auth_preset"]["perm_group"] = 'riud'; //r = read, i = insert, u = update, d = delete
$form["auth_preset"]["perm_other"] = ''; //r = read, i = insert, u = update, d = delete

$form["tabs"]['parameters'] = [
	'title'  => 'Zabbix Administration Parameters',
	'width'  => 100,
	'template'  => "templates/parameter_admin_edit.htm",
	'readonly' => false,
	'fields'  => [
		//#################################
		// Begin Datatable fields
		//#################################
        'server' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '',
            'value'  => ''
        ],
        'user' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '',
            'value'  => ''
        ],
        'pwd' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '',
            'value'  => ''
        ],
        'email' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '',
            'value'  => ''
        ],
        'smtp_server' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '',
            'value'  => ''
        ],
        'smtp_port' => [
            'datatype' => 'INTEGER',
            'formtype' => 'TEXT',
            'default' => '587',
            'value'  => ''
        ],
        'smtp_ssl' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'smtp_email' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '"Web Alert" <email@domaine.com>',
            'value'  => ''
        ],
        'smtp_user' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => 'email@domaine.com',
            'value'  => ''
        ],
        'smtp_pwd' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '',
            'value'  => ''
        ],
        'isp_glue' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'SELECT',
            'default' => '-',
            'value'  => [
                0 => '-',
                1 => '_',
            ],
        ],
        'isp_keyword' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => 'isp',
            'value'  => ''
        ],
        'httptest_keyword' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => 'Web check',
            'value'  => ''
        ],
        'step_keyword' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => 'Check Mainpage',
            'value'  => ''
        ],
        'trigger_keyword' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => 'Web Scenario Fail',
            'value'  => ''
        ],
        'limit_monitor' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '-1',
            'value'  => ''
        ],
        'limit_reseler' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'n',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
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
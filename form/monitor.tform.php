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


$form["title"]			= 'Monitor';
$form["description"]	= "";
$form["name"]			= "monitor";
$form["action"]			= "monitor_edit.php";
$form["db_table"]		= "zabbix_monitor";
$form["db_table_idx"]	= "monitor_id";
$form["db_history"]		= "yes";
$form["tab_default"]	= "monitor"; // Onglet par defaut
$form["list_default"]	= "monitor_list.php";
$form["auth"]			= 'yes'; // yes / no

// Voir utilisation
$form["auth_preset"]["userid"]  = 0; // 0 = id of the user, > 0 id must match with id of current user
$form["auth_preset"]["groupid"] = 0; // 0 = default groupid of the user, > 0 id must match with groupid of current user
$form["auth_preset"]["perm_user"] = 'riud'; //r = read, i = insert, u = update, d = delete
$form["auth_preset"]["perm_group"] = 'riud'; //r = read, i = insert, u = update, d = delete
$form["auth_preset"]["perm_other"] = ''; //r = read, i = insert, u = update, d = delete

$form["tabs"]['monitor'] = [
	'title'  => 'Monitor',
	'width'  => 100,
	'template'  => "templates/monitor_edit.htm",
	'readonly' => false,
	'fields'  => [
		//#################################
		// Begin Datatable fields
		//#################################
        'domain_id' => [
            'datatype' => 'INTEGER',
            'formtype' => 'SELECT',
            'default' => '',
            'datasource' => [
                'type' => 'SQL',
                'querystring' => 'SELECT domain_id, domain FROM web_domain WHERE web_domain.type = \'vhost\' AND `active`="y" AND {AUTHSQL::web_domain} AND web_domain.domain_id NOT IN (SELECT domain_id FROM zabbix_monitor WHERE 1) ORDER BY domain',
                'keyfield'=> 'domain_id',
                'valuefield'=> 'domain'
            ],
            'value'  => ''
        ],
        'url_path' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '/',
            'value'  => '',
            'width'  => '255',
            'maxlength' => '255'
        ],
        'check_period' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '60m',
            'value'  => '',
            'width'  => '7',
            'maxlength' => '7'
        ],
        'timeout' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '20s',
            'value'  => '',
            'width'  => '7',
            'maxlength' => '7'
        ],
        'code_status' => [
            'datatype' => 'INTEGER',
            'formtype' => 'TEXT',
            'default' => '200',
            'value'  => '',
            'width'  => '7',
            'maxlength' => '7'
        ],
        'retries' => [
            'datatype' => 'INTEGER',
            'formtype' => 'TEXT',
            'default' => '3',
            'value'  => '',
            'width'  => '7',
            'maxlength' => '7'
        ],
        'string_search' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'value'  => '',
            'width'  => '20',
            'maxlength' => '20'
        ],
        'active' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ]
	],
];
?>
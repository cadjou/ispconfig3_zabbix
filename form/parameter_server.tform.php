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

$form["title"]			= 'Zabbix serveur parameters';
$form["description"]	= "";
$form["name"]			= "zabbix_server";
$form["action"]			= "parameter_server_edit.php";
$form["db_table"]		= "zabbix_server";
$form["db_table_idx"]	= "server_id";
$form["db_history"]		= "yes";
$form["tab_default"]	= "parameters"; // Onglet par defaut
$form["list_default"]	= "monitor_list.php";
$form["auth"]  = 'no'; // yes / no

$form["tabs"]['parameters'] = [
	'title'  => 'Zabbix Server Parameters',
	'width'  => 100,
	'template'  => "templates/parameter_server_edit.htm",
	'readonly' => false,
	'fields'  => [
		//#################################
		// Server Zabbix
		//#################################
        'zabbix_mutu' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'zabbix_host' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '',
            'value'  => ''
        ],
        'zabbix_user' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '',
            'value'  => ''
        ],
        'zabbix_pwd' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '',
            'value'  => ''
        ],
        //#################################
        // Constants
        //#################################
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
        'application_keyword' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => 'Web Scenario',
            'value'  => ''
        ],
        'trigger_keyword' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => 'Web Scenario Fail',
            'value'  => ''
        ],
        //#################################
        // Limits Admin
        //#################################
        'limit_monitor' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '-1',
            'value'  => ''
        ],
        //#################################
        // Limits Reseller
        //#################################
        'reseller_connexion' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'n',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'reseller_trend' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'reseller_event' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'reseller_smtp' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'reseller_alert' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        //#################################
        // Limits Client
        //#################################
        'client_connexion' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'n',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'client_trend' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'client_event' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'client_smtp' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'client_alert' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        //#################################
        // Limits Monitor
        //#################################
        'limit_check_period' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '15m',
            'value'  => ''
        ],
        'limit_retries' => [
            'datatype' => 'INTEGER',
            'formtype' => 'TEXT',
            'default' => '10',
            'value'  => ''
        ],
        'limit_timeout' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '120s',
            'value'  => ''
        ],
        //#################################
        // Default Monitor
        //#################################
        'default_check_period' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '60m',
            'value'  => ''
        ],
        'default_retries' => [
            'datatype' => 'INTEGER',
            'formtype' => 'TEXT',
            'default' => '5',
            'value'  => ''
        ],
        'default_timeout' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '20s',
            'value'  => ''
        ],
        'default_status_codes' => [
            'datatype' => 'INTEGER',
            'formtype' => 'TEXT',
            'default' => '200',
            'value'  => ''
        ],
        //#################################
        // Default SMTP
        //#################################
        'smtp_host' => [
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
        'smtp_sender' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '"Web Alert" <email@domaine.com>',
            'value'  => ''
        ],
	],
];
?>
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
if (empty($_SESSION['zabbix']['parameters']['type'])) die('No type');

$form['title']			= $_SESSION['zabbix']['parameters']['type'] == 'reseller' ? 'Zabbix Reseller' : 'Zabbix Client';
$form['description']	= '';
$form['name']			= 'zabbix_parameter_user';
$form['action']			= 'parameter_user_edit.php';
$form['db_table']		= 'zabbix_client';
$form['db_table_idx']	= 'client_id';
$form['db_history']		= 'yes';
$form['tab_default']	= 'parameters'; // Onglet par defaut
$form['list_default']	= $_SESSION['zabbix']['parameters']['type'] == 'reseller' ? 'parameter_reseller_list.php' : 'parameter_client_list';
$form['auth']  = 'no'; // yes / no

$form["tabs"]['parameters'] = [
	'title'  => $_SESSION['zabbix']['parameters']['type'] == 'reseller' ? 'Zabbix Reseler Parameters' : 'Zabbix Client Parameters',
	'width'  => 100,
	'template'  => "templates/parameter_user_edit.htm",
	'readonly' => false,
	'fields'  => [
        'client_id' => [
            'datatype' => 'INTEGER',
            'formtype' => 'HIDDEN',
            'default' => '',
            'value'  => ''
        ],
        //#################################
        // Zabbix Connexion
        //#################################
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
        // Email
        //#################################
        'receiver' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '',
            'value'  => ''
        ],
        'alert_subject' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '',
            'value'  => ''
        ],
        'alert_content' => [
            'datatype' => 'TEXT',
            'formtype' => 'TEXTAREA',
            'filters'   => [
                0 => ['event' => 'SAVE',
                    'type' => 'STRIPTAGS']
            ],
            'default' => '',
            'value'  => '',
            'separator' => '',
            'width'  => '',
            'maxlength' => '',
            'rows'  => '10',
            'cols'  => '30'
        ],
        'recovery_subject' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '',
            'value'  => ''
        ],
        'recovery_content' => [
            'datatype' => 'TEXT',
            'formtype' => 'TEXTAREA',
            'filters'   => [
                0 => ['event' => 'SAVE',
                    'type' => 'STRIPTAGS']
            ],
            'default' => '',
            'value'  => '',
            'separator' => '',
            'width'  => '',
            'maxlength' => '',
            'rows'  => '10',
            'cols'  => '30'
        ],
        //#################################
        // Constants
        //#################################
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
        // Limits Client
        //#################################
        'enable_connexion' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'n',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'enable_trend' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'enable_event' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'enable_smtp' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'CHECKBOX',
            'default' => 'y',
            'value'  => [
                0 => 'n',
                1 => 'y',
            ],
        ],
        'enable_alert' => [
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
        'limit_monitor' => [
            'datatype' => 'VARCHAR',
            'formtype' => 'TEXT',
            'default' => '-1',
            'value'  => ''
        ],
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
        // SMTP
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
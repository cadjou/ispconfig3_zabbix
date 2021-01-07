<?php
/*
Copyright (c) 2010 Till Brehm, projektfarm Gmbh and Oliver Vogel www.muv.com
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice,
      this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice,
      this list of conditions and the following disclaimer in the documentation
      and/or other materials provided with the distribution.
    * Neither the name of ISPConfig nor the names of its contributors
      may be used to endorse or promote products derived from this software without
      specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE,
EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

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
$liste["name"] = "monitor";

// Database table
$liste["table"] = "zabbix_monitor";

// Index index field of the database table
$liste["table_idx"] = "monitor_id";

// Search Field Prefix
$liste["search_prefix"] = "search_";

// Records per page
$liste["records_per_page"] = 15;

// Script File of the list
$liste["file"] = "monitor_list.php";

// Script file of the edit form
$liste["edit_file"] = "monitor_edit.php";

// Script File of the delete script
$liste["delete_file"] = "monitor_del.php";

// Paging Template
$liste["paging_tpl"] = "templates/paging.tpl.htm";

// Enable auth
$liste["auth"] = "yes";


/*****************************************************
 * Suchfelder
 *****************************************************/
$liste["item"][] = [
    'field' => "monitor_id",
    'datatype' => "INTEGER",
    'formtype' => "INTEGER",
    'op' => "=",
    'prefix' => "",
    'suffix' => "",
    'width' => "",
];
$liste["item"][] = [
    'field' => "domain_id",
    'datatype' => "INTEGER",
    'formtype' => "SELECT",
    'op'  => "=",
    'prefix' => "",
    'suffix' => "",
    'datasource' => [
        'type' => 'SQL',
        'querystring' => 'SELECT a.domain_id, a.domain FROM web_domain a, monitor b WHERE (a.domain_id = b.domain_id) ORDER BY a.domain_id',
        'keyfield'=> 'domain_id',
        'valuefield'=> 'domain'
    ],
];
//$liste["item"][] = array( 'field'  => "server_id",
//    'datatype' => "INTEGER",
//    'formtype' => "SELECT",
//    'op'  => "=",
//    'prefix' => "",
//    'suffix' => "",
//    'datasource' => array (  'type' => 'SQL',
//        'querystring' => 'SELECT a.server_id, a.server_name FROM server a, web_domain b WHERE (a.server_id = b.server_id) AND ({AUTHSQL-B}) ORDER BY a.server_name',
//        'keyfield'=> 'server_id',
//        'valuefield'=> 'server_name'
//    ),
//    'width'  => "",
//    'value'  => "");


$liste["item"][] = [
    'field'  => "active",
    'datatype' => "VARCHAR",
    'formtype' => "SELECT",
    'op'  => "=",
    'prefix' => "",
    'suffix' => "",
    'width'  => "",
    'value'  => [
        'y' => $app->lng('yes_txt'),
        'n' => $app->lng('no_txt'),
    ]
];
?>

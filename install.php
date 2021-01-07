<?php
// *************************************************************************
// *************************************************************************
// ***                                                                   ***
// ***  Database connexion information with permission to create Tables  ***
// ***         /!\ This file will delete alter installation /!\          ***
// ***                                                                   ***
// *************************************************************************
// *************************************************************************
$db_host      = DB_HOST;
$db_port      = DB_PORT;
$db_charset   = DB_CHARSET;
$db_database  = DB_DATABASE;
$db_root_user = DB_USER; // By default DB_USER or user with create table permission
$db_root_pwd  = DB_PASSWORD; // By DB_PASSWORD or Your root password


// ******************************************
// ******************************************
// ***                                    ***
// ***  /!\ Don't change the code bellow  ***
// ***                                    ***
// ******************************************
// ******************************************
if(!is_file(__DIR__ . '/install.sql'))
{
    die('Error : The database file missing. Check the repo to get it and put in this dir ' . __DIR__);
}
echo 'Create Monitor Database<br>';
$bdd = null;
try {
    $bdd = new PDO('mysql:host=' . $db_host . ':' . $db_port . ';dbname=' . $db_database . ';charset=' . $db_charset, $db_root_user, $db_root_pwd);
}
catch (Exception $e) {
    die('Error : Check connexion parameters in file ' . __FILE__);
}
echo 'Connexion OK<br>';
if ($bdd->query(file_get_contents('install.sql')))
{
    unlink(__DIR__ . '/install.sql');
    unlink(__FILE__);
    echo 'Database created -> the files install.php and install.sql have been remove<br>';
}
else
{
    echo 'Error : <br>';
    echo nl2br(print_r($bdd->errorInfo(),1));
}

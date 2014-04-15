<?php

if(!defined('SARHAN_SURVEY'))
    die("Improper use of file!");

return array(
    "test" => array(
        'driver' => 'pdo_sqlite',
        'memory' => true)
    ,
    "production" => array(
        'driver' => 'pdo_mysql',
        'dbname'=>'mihraabc_smsdb',
        'user'=>'mihraabc_smsuser',
        'password'=>'smsuser1059',
        'host'=>'localhost'
    )
);
?>
<?php

$user = 'MYSQL_USER';
$pass = 'MYSQL_PASSWORD';
$host = 'mysql_8';
$db = 'api_weather_db';

try
{
    $dbh = new PDO('mysql:host='.$host.';dbname='.$db, $user, $pass);
} 
catch (PDOException $e) 
{
    print "Ошибка подключения к базе данных: " . $e->getMessage() . "<br/>";
    die();
}
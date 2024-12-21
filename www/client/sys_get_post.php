<?php

$city_id = 1;
$city_set = false;


if (isset($_GET['sl_city_id'])) (integer) $city_id = $_GET['sl_city_id'];


$res = $dbh->query('SELECT `latitude`, `longitude` FROM `tt_cities` WHERE `id`='.$city_id);
$row_count = $res->rowCount();


if ($row_count == 1)
{
    $city_set = true;
}
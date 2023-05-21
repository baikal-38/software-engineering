<?php
require_once 'db_conn.php';
require_once 'sys_get_post.php';
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Прогноз погоды от Open-meteo.com</title>
    </head>
    <body>
        <div style="width: 100%;">
            <div style="width: 200px; float:left;">
                    <label for="sl_cities">Выберите город:</label><br>
                    <form method="GET" action="<?php  echo $_SERVER['PHP_SELF'];   ?>">
                    <select name="sl_city_id" size="20">
<?php
                    foreach($dbh->query('SELECT * FROM tt_cities') as $row) 
                    {
                        $arr_cities[] = $row;
                    }

                    foreach($arr_cities as $k1 => $v1)
                    {
                        echo '<option '.($city_id == $v1['id'] ? 'selected' : '').' value="'.$v1['id'].'">'.$v1['name'].'</option>'."\r\n";
                    }
?>
                    </select><br>
                    <input type="submit" value="OK" style="width: 100px;" >
                    </form>
            </div>
            <div style="width: 70%; float:left;">
<?php
                    if ($city_set)     echo '<img src="client.php?sl_city_id='.$city_id.'">';
?>
            </div>
        </div>

    </body>
</html>








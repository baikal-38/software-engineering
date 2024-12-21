<?php
if (!$handle = fopen(__DIR__.'/config/current_ip_db.txt', 'r'))
{
        error_log('Не могу открыть файл '.__DIR__.'/config/current_ip_db.txt');
        echo 'Отсутствует конфигурационный файл с параметрами подключения.';
}
else
{
        $ip_bd = fread($handle, filesize(__DIR__.'/config/current_ip_db.txt'));
        fclose($handle);
        //echo '_'.$ip_bd.'_';


        $host = trim($ip_bd);	//'10.111.28.155';
        $database = 'arm_maket'; 
        $user = 'MYSQL_USER'; 
        $password = 'MYSQL_PASSWORD'; 



        $link1 = new mysqli($host, $user, $password, $database);
        if ($link1->connect_error)
        {
                error_log('Connect Error: '. $link1 ->connect_error.'('.$link1 ->connect_errno.')' );
                die('Connect DB Error!');
        }
		
		$link1->set_charset("utf8mb4");
}
?>
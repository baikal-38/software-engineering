<?php
require_once 'sys_con_db.php';
require_once 'sys_vars.php';
require_once 'sys_func.php';
require_once 'sys_get_post.php';
require_once __DIR__.'/boot.php';
    


if (!check_auth())
{
    header('Location: index.php');
    die;
}




$fn = '';


if ($id_doc == 1)    $fullname = 'D:\Docums\FullDocs\\'.$y1.'\\'.$m1.'\\'.$d1.'\nbt_siz_d.xlsx';
if ($id_doc == 2)    $fullname = 'D:\rp\arm_maket\\'.$y1.'_dcntib_1.xls';



if (file_exists($fullname))
{
        // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
        // если этого не сделать файл будет читаться в память полностью!
        if (ob_get_level())
        {
                ob_end_clean();
        }

        // заставляем браузер показать окно сохранения файла
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($fullname));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fullname));

        // читаем файл и отправляем его пользователю
        if ($fd = fopen($fullname, 'rb'))
        {
                while (!feof($fd))
                {
                        print fread($fd, 1024);
                }
                fclose($fd);
        }
}
else
{
	echo '<!DOCTYPE html>';
	echo '<html>';
	echo '<head>';
	echo '	<title>Оперативная отчетность ВСЖД</title>';
	echo '	<meta http-equiv="Content-Type" content="text/html; charset=utf-8;">';
	echo '	<meta http-equiv="expires" content="0">';
	echo '	<meta http-equiv="cashe-control" content="no-cashe">';
	require_once "style.css.php";		
	echo '</head>';
	echo '<body>';
	echo '<font class=font_text1>Документ за '.$y1.'-'.$m1.'-'.$d1.' не найден!</font><br><br>';
	//echo $fullname;
	echo '<a class=font_text1_blue href="http://esrr-skull.esrr.oao.rzd/arm_maket/">Перейти на главную страницу.</a>';
	echo '</body>';
	echo '</html>';
}



//require_once 'bpr/connection.php';
//require_once 'visit_write.php';
?>
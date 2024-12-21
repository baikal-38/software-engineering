<?php
	require_once 'sys_func.php';


	$ip = $_SERVER['REMOTE_ADDR'];
	/*
        $auth_user_name = $_SERVER['AUTH_USER'];
	
	
	$s = strrchr($auth_user_name, '\\');
	$auth_user_name2 = substr($s, 1);                                   //d_PaushevaEV
	
	
	if (strlen($auth_user_name) > 30) $auth_user_name = 'оч.длин.имя';
	$auth_user_name1 = str_replace('\\', '_', $auth_user_name);         //ESRR_d_PaushevaEV
	
	
	
	$user_name_str1 = '';
	*/
	
	if (@$_SESSION['user_id'] == 0)
	{
		$user_str = 'unknown';
		$user_id = 0;
	}
	else
	{
		$user_id = $_SESSION['user_id'];
		$res1 = $link1->query('SELECT `USER_NAME` FROM '.$tbl_users.' WHERE `USER_ID`="'.$user_id.'"');
		while ($row_res1 = $res1->fetch_assoc())
		{
			$user_str = $row_res1['USER_NAME'];
		}
	}
	
	
	$post_str = '';
	$arr = get_defined_vars();
	$array1 = array();
	if (count($arr['_POST']) != 0) $array1 = $arr['_POST'];
	if (count($arr[ '_GET']) != 0) $array1 = $arr['_GET'];
	foreach ($array1 as $k => $v)
	{
		$post_str.= '&'.$k.'='.$v;
	}
	if (strlen($post_str) > 950)	$post_str = substr($post_str, 0, 950);
	$array1 = array();
	
	
	
	$tbl_name = $tbl_visits_dor;
	if ($ip == '10.110.2.105' || $ip == '127.0.0.1')    $tbl_name = $tbl_visits_ivc;
	if (substr($user_str, 0, 4) == 'ivc_')              $tbl_name = $tbl_visits_ivc;


	$sql_arr = array();

	$sql_arr[] = 'INSERT INTO '.$tbl_name.' (`ip`,`user`,`date`,`date_time`,`page_name`,`link`,`browser`) VALUES 
															(	"'.$ip.'",
																	"'.$user_str.'",
																	"'.date('Y-m-d').'",
																	"'.date('Y-m-d H:i:s').'",
																	"'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].'",
																	"'.addslashes($_SERVER['PHP_SELF'].$post_str).'",
																	"'.$_SERVER['HTTP_USER_AGENT'].'"
															)';
	$sql_arr[] = 'UPDATE '.$tbl_users.' SET `last_visit`="'.date('Y-m-d H:i:s').'" WHERE `USER_ID`="'.$user_id.'"';
	ExecuteSQLArray($link1, $sql_arr, false);

	//if ($link1->query($sql1) == false)	error_log('Запись в лог посещений не вставлена: ('. $link1->error.') ('.$link1->errno . ') ('.strlen($post_str).'___'.$sql1.') ');


?>
<?php
require_once 'class_calendar1.php';
require_once 'class_ARMMaketObjects.php';
require_once 'class_MaketManager.php';
require_once 'class_Users.php';
require_once 'class_MathExprCalculator.php';

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



if (isset($_POST['btn_delete_data']))
{
	//$sql_arr = array();
	//$sql_arr[] = 'DELETE FROM '.$tbl_maket_data.'       WHERE `date`="'.date('Y-m-d',$mkt1).'" AND `MOCKUP_ID`="'.$maket_id.'" AND `POINT_ID`="'.$maket_kpp.'"';
	//$sql_arr[] = 'DELETE FROM '.$tbl_log_user_action.'  WHERE `date`="'.date('Y-m-d',$mkt1).'" AND `MOCKUP_ID`="'.$maket_id.'" AND `POINT_ID`="'.$maket_kpp.'"';
}



$head_str1 = '<!DOCTYPE html>
<html>
<head>
	<title>АРМ Макет</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8;">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="cashe-control" content="no-cashe">	
</head>
<body>';

$bottom_str1 = '</body></html>';



//Определяем перечень макетов, доступных пользователю, и список доступных КПП для этих макетов.
$arr_user_maket_id = array();
$arr_user_maket_kpp = array();
$res1 = $link1->query('SELECT `MOCKUP_ID`,`USER_ACCESS_KPP` FROM '.$tbl_user_rights.' WHERE `USER_ID`="'.$_SESSION['user_id'].'"');
while ($row_res1 = $res1->fetch_assoc())
{
		$arr_user_maket_id[] = $row_res1['MOCKUP_ID'];
		
		if ($row_res1['USER_ACCESS_KPP'] != '')
		{
				$arr_user_maket_kpp[$row_res1['MOCKUP_ID']] = explode(';',$row_res1['USER_ACCESS_KPP']);
		}
		else
		{
				$arr_user_maket_kpp[$row_res1['MOCKUP_ID']] = array();
		}
}
if ($maket_id > 0 && !in_array($maket_id, $arr_user_maket_id))
{
    echo $head_str1.'<p class=p3_red>У вас нет доступа к указанному макету!</p>'.$bottom_str1;
    exit();
}
if ($maket_id > 0 && $maket_kpp > 0)
{
		if (count($arr_user_maket_kpp[$maket_id]) > 0)
		{
				$res1 = $link1->query('SELECT `POINT_CODE` FROM '.$tbl_points.' WHERE `POINT_ID`="'.$maket_kpp.'"');
				$row_res1 = $res1->fetch_assoc();
				$kpp_code_str1 = $row_res1['POINT_CODE'];
				
				if (!in_array($kpp_code_str1, $arr_user_maket_kpp[$maket_id]))
				{
						echo $head_str1.'<p class=p3_red>У вас нет доступа к указанному КПП!</p>'.$bottom_str1;
						exit();
				}
		}
}




$obj_maket = new MaketManager($link1);

$obj_maket->tbl_points = $tbl_points;
$obj_maket->tbl_makets = $tbl_makets;
$obj_maket->tbl_user_rights = $tbl_user_rights;
$obj_maket->tbl_maket_data = $tbl_maket_data;
$obj_maket->tbl_makets_tables = $tbl_makets_tables;
$obj_maket->tbl_makets_groups = $tbl_makets_groups;
$obj_maket->tbl_phrases = $tbl_phrases;
$obj_maket->tbl_fields = $tbl_fields;
$obj_maket->tbl_log_user_action = $tbl_log_user_action;

$maket_period = $obj_maket->GetMaketPeriod($maket_id);
$visibility_send_button = $obj_maket->GetVisibilitySendButton($maket_id);


$otch_data = date('Y-m-d', $mkt1);
if ($maket_period == 1)		$otch_data = date('Y-m-01', $mkt1);		//месячный макет
if ($maket_period == 2)		$otch_data = date('Y-01-01', $mkt1);	//годовой макет




$save_rezult = '';
$send_rezult = '';

if (isset($_POST['btn_save']))
{        
		$save_rezult = $obj_maket->saveMaketData($_SESSION['user_id'], $maket_id, $maket_kpp, $_POST, $mkt1);
}
if (isset($_POST['btn_send']))
{
		$send_rezult = $obj_maket->sendMaketData($_SESSION['user_id'], $maket_id, $maket_kpp, $path_for_save_maket, $mkt1);
		if (!is_numeric($send_rezult))	error_log($send_rezult);
}

if (isset($_GET['sl_copy_maket']))
{
		$only_plan = false;
		if ((integer) $_GET['sl_copy_maket'] == 4)	$only_plan = true;
		
													$mkt2 = mktime(0, 0, 0, date('m',$mkt1)+1, date('d',$mkt1), date('Y',$mkt1));	//по умолчанию
		if ((integer) $_GET['sl_copy_maket'] == 0)	$mkt2 = mktime(0, 0, 0, date('m',$mkt1)-1, date('d',$mkt1), date('Y',$mkt1));	//копирование макета на предыдущий месяц
		if ((integer) $_GET['sl_copy_maket'] == 1)	$mkt2 = mktime(0, 0, 0, date('m',$mkt1), date('d',$mkt1)-1, date('Y',$mkt1));	//копирование макета на предыдущие сутки
		if ((integer) $_GET['sl_copy_maket'] == 2)	$mkt2 = mktime(0, 0, 0, date('m',$mkt1), date('d',$mkt1)+1, date('Y',$mkt1));	//копирование макета на следующие сутки
		if ((integer) $_GET['sl_copy_maket'] == 3)	$mkt2 = mktime(0, 0, 0, date('m',$mkt1)+1, date('d',$mkt1), date('Y',$mkt1));	//копирование макета на следующий месяц
		if ((integer) $_GET['sl_copy_maket'] == 4)	$mkt2 = mktime(0, 0, 0, date('m',$mkt1)+1, date('d',$mkt1), date('Y',$mkt1));	//копирование макета 7000 (СИЗ) на следующий месяц без факта
		
		$save_rezult = $obj_maket->copyMaketData($_SESSION['user_id'], $maket_id, $maket_kpp, $mkt1, $mkt2, $only_plan);
}





if ($maket_id > 0 && $maket_kpp > 0)
{
		$res1 = $link1->query('SELECT * FROM '.$tbl_makets.' WHERE `MOCKUP_ID`='.$maket_id);
		$row_res1 = $res1->fetch_assoc();
		
		$kpp_type_id = $row_res1['KPP_TYPE_ID'];
		
		$res2 = $link1->query('SELECT * FROM '.$tbl_points.' WHERE `POINT_ID`='.$maket_kpp.' ORDER BY `POINT_ORDER_NO`');
		$row_res2 = $res2->fetch_assoc();
		
		$maket_name     = $row_res1['MOCKUP_TITLE'];
		$maket_code     = $row_res1['MOCKUP_CODE'];
		$maket_kpp_code = $row_res2['POINT_CODE'];
		$maket_kpp_name = $row_res2['POINT_TITLE'];
}
else
{
		$maket_name     = '';
		$maket_code     = '';
		$maket_kpp_code = '';
		$maket_kpp_name = '';
}





//формирования перечня доступных пользователю макетов для HTML вида
$arr_maket_list = html_maket_list($link1, $mkt1, $obj_maket, $tbl_makets, $tbl_points, $tbl_log_user_action, $arr_user_maket_id, $arr_user_maket_kpp, $maket_id, $maket_kpp);


/*
 * формирования перечня доступных пользователю макетов для HTML вида
 */
function html_maket_list($link1, $mkt1, $obj_maket, $tbl_makets, $tbl_points, $tbl_log_user_action, $arr_user_maket_id, $arr_user_maket_kpp, $maket_id, $maket_kpp)
{
		$row1 = 1;
		
		$arr_maket_list = array();
		
		
		if (count($arr_user_maket_id) == 0) return $arr_maket_list;
		
		
		$res1 = $link1->query('SELECT * FROM '.$tbl_makets.' WHERE `MOCKUP_ID` IN ('.implode(',', $arr_user_maket_id).') ORDER BY `MOCKUP_CODE`');
		while ($row_res1 = $res1->fetch_assoc())
		{
				$id = $row_res1['MOCKUP_ID'];
				$code = $row_res1['MOCKUP_CODE'];
				$name = $row_res1['MOCKUP_TITLE'];
				$kpp_type_id = $row_res1['KPP_TYPE_ID'];
				$mp = $obj_maket->GetMaketPeriod($id);


				$res2 = $link1->query('SELECT * FROM '.$tbl_points.' WHERE `POINT_TYPE_ID`='.$kpp_type_id.' ORDER BY `POINT_ORDER_NO`');
				while ($row_res2 = $res2->fetch_assoc())
				{
						$kpp_id = $row_res2['POINT_ID'];
						$kpp_code = $row_res2['POINT_CODE'];
						$kpp_title = $row_res2['POINT_TITLE'];
						$kpp_short_title = $row_res2['POINT_SHORT_TITLE'];


						if (count($arr_user_maket_kpp[$id]) > 0)
						{
								if (!in_array($kpp_code, $arr_user_maket_kpp[$id]))		Continue;		//не имеет доступ к этому КПП
						}


						$odata = date('Y-m-d', $mkt1);
						if ($mp == 1)	$odata = date('Y-m-01', $mkt1);		//месячный макет
						if ($mp == 2)	$odata = date('Y-01-01', $mkt1);	//годовой макет
						
						
						$action = '';
						
						$res4 = $link1->query('SELECT `action` FROM '.$tbl_log_user_action.'   WHERE `date`="'.$odata.'" AND `MOCKUP_ID`="'.$id.'" AND `POINT_ID`="'.$kpp_id.'" AND `time_ins` = (SELECT MAX(`time_ins`) FROM '.$tbl_log_user_action.'   WHERE `date`="'.$odata.'" AND `MOCKUP_ID`="'.$id.'" AND `POINT_ID`="'.$kpp_id.'")');
						if ($res4->num_rows > 0)
						{
							$row_res4 = $res4->fetch_assoc();
							$action = $row_res4['action'];
						}
						
						

						$img_str = '';
						if ($action == 1)	{	$img_str = '<img src="img/maket_save.png">';	}
						if ($action == 2)	{	$img_str = '<img src="img/maket_send.png">';	}
						if ($action == 3)	{	$img_str = '<img src="img/maket_save.png">';	}


						$arr_maket_list[$row1][1] = $img_str;
						$arr_maket_list[$row1][2] = $id;
						$arr_maket_list[$row1][3] = $kpp_id;
						$arr_maket_list[$row1][4] = $mkt1;
						$arr_maket_list[$row1][5] = $maket_id;
						$arr_maket_list[$row1][6] = $maket_kpp;
						$arr_maket_list[$row1][7] = $code;
						$arr_maket_list[$row1][8] = $kpp_short_title;
						
						
						$row1++;
				}
		}
		
		
		return $arr_maket_list;
}



$notif = '';
//отправка уведомления на EMAIL
if (isset($_POST['btn_inform']))
{
	//https://ncona.com/2011/06/using-utf-8-characters-on-an-e-mail-subject/
	
	$to  = "ivc_druzhininayayu@esrr.rzd"; 
	//$to  = "ivc_halitovdp@esrr.rzd" ;
	$subject = "Информирование от пользователя АРМ МАКЕТ";
	$message = 'Пользователь  '.$_SESSION['USER_TITLE'].'  информирует о необходимости ввода в БД ДИСКОР макета '.$maket_code.'  ("'.$maket_name.'") по КПП '.$maket_kpp_code.' ('.$maket_kpp_name.') за отчетные сутки '.$otch_data.'.';
	$headers  = "Content-type: text/html; charset=utf-8; \r\n"; 
	$headers .= "From: APM MAKET <ivc_halitovdp@esrr.rzd>\r\n"; 
	$headers .= "Reply-To: ivc_halitovdp@esrr.rzd\r\n";
	$headers .= 'Cc: ivc_halitovdp@esrr.rzd, ivc_ZayacPA@esrr.rzd, '.$_SESSION['USER_email'];
	mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, $headers);
}
//отправка письма в ЕСПП на регистрацию обращения
if (isset($_GET['sl_make_zapros']))
{
	$to  = "espp@espp.gvc.rzd"; 
	//$to  = "ivc_halitovdp@esrr.rzd" ;
	//$subject = mb_convert_encoding("консультация по APM MAKET", "utf-8", "windows-1251");	//"консультация по APM MAKET"
	$subject = "APM MAKET";
	$message = 'Просьба проконсультировать по работе в АРМ Макет. Обращение направить в РГ ТЕХНОЛОГИ-ПО-ВАГОНООБОРОТ-ВСИБ.';
	$headers = '';
    $headers  = "From: ".$_SESSION['USER_email']."\n";
    $headers .= "X-Sender: ARM_MAKET <".$_SESSION['USER_email'].">\n";
    $headers .= 'X-Mailer: ARM_MAKET';
    $headers .= "X-Priority: 1\n"; // Urgent message!
    $headers .= "Return-Path: ".$_SESSION['USER_email']."\n"; // Return path for errors
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\n";
	if (mail($to, $subject, $message, $headers))
	{
			$notif = 'Обращение направлено в ЕСПП. При успешной регистрации на email адрес '.$_SESSION['USER_email'].' придет подтверждение о создании обращения и его номер.';
	}
}






$res1 = $link1->query('SELECT `ADMIN` FROM '.$tbl_users.' WHERE `USER_ID`="'.$_SESSION['user_id'].'"');
$row_res1 = $res1->fetch_assoc();
$admin = $row_res1['ADMIN'];







//формирование истории действий с макетом
$arr_history_list = form_maket_action_arr($link1, $tbl_users, $tbl_log_user_action, $maket_id, $maket_kpp, $otch_data);


/*
 * формирование истории действий с макетом
 */
function form_maket_action_arr($link1, $tbl_users, $tbl_log_user_action, $maket_id, $maket_kpp, $otch_data)
{
		$i = 0;
		
		$arr_history_list = array();
		
		$res1 = $link1->query(' SELECT  '.$tbl_users.'.`USER_NAME`          AS `USER_NAME`,
										'.$tbl_log_user_action.'.`action`   AS `action`,
										'.$tbl_log_user_action.'.`time_ins` AS `time_ins`
								FROM '.$tbl_log_user_action.'
								LEFT JOIN '.$tbl_users.' ON '.$tbl_log_user_action.'.`USER_ID`='.$tbl_users.'.`USER_ID` 
								WHERE   '.$tbl_log_user_action.'.`MOCKUP_ID`="'.$maket_id.'"         AND 
										'.$tbl_log_user_action.'.`POINT_ID`="'.$maket_kpp.'"         AND 
										'.$tbl_log_user_action.'.`date`="'.$otch_data.'" 
								ORDER BY '.$tbl_log_user_action.'.`time_ins`');
		$row_cnt = $res1->num_rows;
		if ($row_cnt > 0)
		{
				while ($row_res1 = $res1->fetch_assoc())
				{
						$i++;				
						$arr_history_list[$i][1] = $row_res1['USER_NAME'];
						$arr_history_list[$i][2] = $row_res1['action'];
						$arr_history_list[$i][3] = $row_res1['time_ins'];
				}
		}

		return $arr_history_list;
}


$arr_tables_content = formAllTablesContent(	$link1, $mkt1, $tbl_makets_tables, $tbl_user_rights, $tbl_fields, $maket_id, $maket_code, $obj_maket, $maket_kpp);





if ($fmt_doc == 1)
{
		require_once('../excel_lib/Spreadsheet/Excel/Writer.php');
		require_once('maket_input_xls.php');
		
		
		$xls = new Spreadsheet_Excel_Writer();
		$xls->setVersion(8);
		$sheet =& $xls-> addWorksheet('Лист1');
		$sheet->setInputEncoding("UTF-8");
		
		$x1 = 1;
		$y1 = 5;

		foreach($arr_tables_content as $k1 => $v1)
		{
				$rez_arr = $arr_tables_content[$k1][0];
				$maket_table_id = $arr_tables_content[$k1][1];
				$kol_vo_fld_level = $arr_tables_content[$k1][2];
				$maket_table_title = $arr_tables_content[$k1][3];
				$fields_count = $arr_tables_content[$k1][4];
				$maket_table_title_width_web = $arr_tables_content[$k1][5];
				$maket_table_code_width = $arr_tables_content[$k1][6];
				$maket_table_title_width_xls = $arr_tables_content[$k1][7];
				
				list($arr_head1, $arr_head2, $arr_head3, $arr_head4) = form_Array_for_table_head($link1, $tbl_fields, $maket_table_id, $kol_vo_fld_level);
				list($x1, $y1) = form_XLS_table_head($xls, $sheet, $x1, $y1, $arr_head3, $arr_head4, $maket_table_title, $kol_vo_fld_level);
				$x1 = 1;
				list($x1, $y1) = form_XLS_OneTable($xls, $sheet, $x1, $y1, $rez_arr, $maket_table_title_width_xls, $arr_head2);
				$x1 = 1;
		}
		
		
		
		Show_XLS_version($xls, $sheet, $maket_code, $maket_name, $maket_kpp_name, $mkt1);
		
		
		$xls-> send('АРМ Макет.xls'); //!!!
		$xls-> close();
}
else
{
		require_once('maket_input_htm.php');
		
		
		$html_tables_content = '';
		foreach($arr_tables_content as $k1 => $v1)
		{
				$rez_arr = $arr_tables_content[$k1][0];
				$maket_table_id = $arr_tables_content[$k1][1];
				$kol_vo_fld_level = $arr_tables_content[$k1][2];
				$maket_table_title = $arr_tables_content[$k1][3];
				$fields_count = $arr_tables_content[$k1][4];
				$maket_table_title_width_web = $arr_tables_content[$k1][5];
				$maket_table_code_width = $arr_tables_content[$k1][6];
				
				list($arr_head1, $arr_head2, $arr_head3, $arr_head4) = form_Array_for_table_head($link1, $tbl_fields, $maket_table_id, $kol_vo_fld_level);
				$HTML_table_head_str = form_HTML_table_head($arr_head1, $arr_head3, $arr_head4, $maket_table_title, $kol_vo_fld_level, $maket_table_title_width_web, $maket_table_code_width);
				$html_tables_content.= form_HTML_OneTable($HTML_table_head_str, $maket_id, $rez_arr, $fields_count, $maket_kpp);
		}
		
		$nbt_siz_menu_str = form_predpr_menu_nbt_siz_html($link1, $mkt1, $tbl_points);
		//$dcntib_ntu12_menu_str = form_predpr_menu_dcntib_ntu12_html($link1, $mkt1, $tbl_points);
		
		show_HTML_version(	$arr_maket_list, $arr_history_list, $arr_user_maket_id, 
							$dat1, $mkt1, $admin, $maket_kpp_name, $maket_code, $maket_name, $html_tables_content,
							$save_rezult, $send_rezult, $notif, $maket_period, $visibility_send_button, $nbt_siz_menu_str);
}


function formAllTablesContent($link1, $mkt1, $tbl_makets_tables, $tbl_user_rights, $tbl_fields, $maket_id, $maket_code, $obj_maket, $maket_kpp)
{
		$rez_arr = array();
		$i = 1;
		
		$res1 = $link1->query('SELECT * FROM '.$tbl_makets_tables.' WHERE `MOCKUP_ID`="'.$maket_id.'" ORDER BY `MOCKUP_TABLE_ORDER_NO`');
		while ($row_res = $res1->fetch_assoc())
		{
				$maket_table_id = $row_res['MOCKUP_TABLE_ID'];
				$maket_table_title = $row_res['MOCKUP_TABLE_TITLE'];
				$maket_table_title_width_web = $row_res['MOCKUP_TITLE_WIDTH_WEB'];
				$maket_table_title_width_xls = $row_res['MOCKUP_TITLE_WIDTH_XLS'];
				$maket_table_code_width = $row_res['MOCKUP_CODE_WIDTH'];
				
				
				if ($maket_table_title == '') $maket_table_title = $maket_code;


				$fields_count = 0;
				$kol_vo_fld_level = 0;
				$arr_fields_in_maket_table = array();


				//определяем количество уровней в шапке
				$res3 = $link1->query('SELECT * FROM '.$tbl_fields.' WHERE `MOCKUP_TABLE_ID`="'.$maket_table_id.'" ORDER BY `FIELD_ORDER_NO`');
				while ($row_res3 = $res3->fetch_assoc())
				{
						$fields_count++;
						$count = substr_count($row_res3['FIELD_TITLE'],'|');
						if ($count >= $kol_vo_fld_level)   $kol_vo_fld_level = $count;
						$arr_fields_in_maket_table[] = $row_res3['FIELD_CODE'];
				}
				$kol_vo_fld_level++;


				//определяем к каким полям и фразам пользователь имеет доступ                        
				$obj_user = new Users($link1);                        
				$obj_user->tbl_user_rights = $tbl_user_rights;
				list($arr_user_frazes, $arr_user_fields, $str_user_frazes_fields) = $obj_user->get_available_fields_frazes($maket_id, $_SESSION['user_id']);
				
				
				//отображение HTML таблицы с содержимым макета
				$rez_arr[$i][0] = form_Array_OneTableContent($mkt1, $obj_maket, 
															$maket_id, $maket_kpp, $maket_table_id, 
															$arr_user_frazes, $arr_user_fields, $str_user_frazes_fields);
				$rez_arr[$i][1] = $maket_table_id;
				$rez_arr[$i][2] = $kol_vo_fld_level;
				$rez_arr[$i][3] = $maket_table_title;
				$rez_arr[$i][4] = $fields_count;
				$rez_arr[$i][5] = $maket_table_title_width_web;
				$rez_arr[$i][6] = $maket_table_code_width;
				$rez_arr[$i][7] = $maket_table_title_width_xls;

				$i++;
		}
		
		return $rez_arr;
}



function form_Array_OneTableContent($mkt1, $obj_maket, 
									$maket_id, $maket_kpp, $maket_table_id, 
									$arr_user_frazes, $arr_user_fields, $str_user_frazes_fields)
{
		$rez_arr = array();
		
		$list_frazes = $obj_maket->GetListFrazesByMaketTableID($maket_table_id);
		$arr_frazes_masks = $obj_maket->GetArrFrazesMasksByMaketTableID($maket_table_id);
		$arr_frazes_titles = $obj_maket->GetArrFrazesTitlesByMaketTableID($maket_table_id);
		$frazes_arr = $obj_maket->GetFrazesArrByMaketTableID($maket_table_id);
		
		$list_fields = $obj_maket->GetListFieldsByMaketTableID($maket_table_id);
		$fields_arr = $obj_maket->GetFieldsArrByMaketTableID($maket_table_id);
		$arr_fields_width = $obj_maket->GetListFieldsWidthByMaketTableID($maket_table_id);
		
		$mask_arr = $obj_maket->GetMasksArr($arr_frazes_masks, $fields_arr);
		$temp_arr = $obj_maket->GetDataFromTableMaketData(array_keys($list_frazes), array_keys($list_fields), $fields_arr, $frazes_arr, $mkt1, $maket_id, $maket_kpp, $mask_arr);
		
		
		//нужно заполнить массив на тот случай, если макет еще ни разу не сохранялся
		foreach($list_frazes as $k1 => $v1)
		{
				$phraze_id	= $k1;
				
				$rez_arr[$phraze_id]['C1'] = $arr_frazes_titles[$phraze_id];
				$rez_arr[$phraze_id]['C2'] = $list_frazes[$phraze_id];
				$rez_arr[$phraze_id]['C3'] = $phraze_id;
				
				foreach($list_fields as $k2 => $v2)
				{
						$field_id	= $k2;
						$phraze_code = $list_frazes[$phraze_id];
						$field_code = $list_fields[$field_id];
						$val = $temp_arr[$phraze_id][$field_id];
						
						
						if (array_key_exists($phraze_id, $mask_arr) && is_array($mask_arr[$phraze_id]))
						{
								$mask_str = (array_key_exists($field_id, $mask_arr[$phraze_id]) ? $mask_arr[$phraze_id][$field_id] : '') ;
						}
						else
						{
								$mask_str = '';
						}
						$val = $obj_maket->GetTextCELL($temp_arr[$phraze_id], $field_id, $mask_str);
						
						
						
						$read_only_flag = false;
						if (substr($mask_str,0,1) == '*' || substr($mask_str,0,8) == 'formul2(')
						{
								$read_only_flag = true;
						}
						else
						{
								if ($str_user_frazes_fields != ''  &&  !(in_array($field_code, $arr_user_fields) && in_array($phraze_code, $arr_user_frazes)))
								{
										$read_only_flag = true;
								}
						}
						
						
						$rez_arr[$phraze_id][$field_id][0] = $val;
						$rez_arr[$phraze_id][$field_id][1] = $read_only_flag;
						$rez_arr[$phraze_id][$field_id][2] = $phraze_code;
						$rez_arr[$phraze_id][$field_id][3] = $field_code;
						$rez_arr[$phraze_id][$field_id][4] = $arr_fields_width[$field_id];
				}				
		}
		
		return $rez_arr;
}



function form_Array_for_table_head($link1, $tbl_fields, $maket_table_id, $kol_vo_fld_level)
{
        $arr_head1 = array();           //ширина полей WEB
        $arr_head2 = array();           //ширина полей XLS
        $arr_head3 = array();           //содержит перечень кодов полей
        $arr_head4 = array();
        
        for($i=0; $i<$kol_vo_fld_level; $i++)
        {
                $prev_field = '__pusto__';
                $cnt = 1;
                $num_field = 0;
				//т.к. к таблице $tbl_fields обращаемся $kol_vo_fld_level раз, то эти массивы необходимо обнулить
				$arr_head2 = array();
                $arr_head3 = array();

                $res3 = $link1->query('SELECT * FROM '.$tbl_fields.' WHERE `MOCKUP_TABLE_ID`="'.$maket_table_id.'" ORDER BY `FIELD_ORDER_NO`');
                while ($row_res = $res3->fetch_assoc())
                {
                        $arr = explode('|',$row_res['FIELD_TITLE']);

                        $text_cell = '';
                        for ($j=0; $j<=$i; $j++)
                        {
                                if (@$arr[$j] == '') $arr[$j] = '_';
                                if ($text_cell == '')   {   $text_cell.= $arr[$j];  }    else    {   $text_cell.= '|'.$arr[$j];    }
                        }
                        $num_field++;

                        if ($prev_field == $text_cell)//&&  @$text_cell != ''
                        {
                            $cnt++;
                            $num_field--;
                        }
                        if ($prev_field != $text_cell) $cnt = 1;

                        $arr_head1[] = $row_res['FIELD_WIDTH_WEB'];
						$arr_head2[] = $row_res['FIELD_WIDTH_XLS'];
                        $arr_head3[] = $row_res['FIELD_CODE'];
                        $arr_head4[$i][] = $text_cell;

                        $prev_field = $text_cell;
                }
        }
        //echo '<pre>';
        //error_log( print_r( $arr_head2, true ) );
        //echo '</pre>';
		return array($arr_head1, $arr_head2, $arr_head3, $arr_head4);
}


function form_predpr_menu_nbt_siz_html($link1, $mkt1, $tbl_points)
{
		$rez_str = '<table class=tbl_1>'."\r\n";
		$rez_str.= '<tr style="text-align: center;">'."\r\n";
		$rez_str.= '<td class=td_1>Служба</td>'."\r\n";
		$rez_str.= '<td class=td_1>Предприятия</td>'."\r\n";
		$rez_str.= '</tr>'."\r\n";
		
		$res1 = $link1->query('SELECT `POINT_TOTAL`, count(*) AS `cnt` FROM '.$tbl_points.' WHERE `POINT_TYPE_ID`=192 AND `POINT_TOTAL`<>"" GROUP BY `POINT_TOTAL` ORDER BY `cnt` DESC');
		while ($row_res1 = $res1->fetch_assoc())
		{				
				$sluzhba_name = $row_res1['POINT_TOTAL'];
				$cnt = $row_res1['cnt'];
				
				$rez_str.= '<tr>'."\r\n";
				$rez_str.= '<td class=td_1 style="text-align: center;"><a class=a1 href="xls_nbt_siz.php?sl_sluzhba_name='.urlencode($sluzhba_name).'&sl_fmt_doc=1&sl_dat1='.date('Y-m-01', $mkt1).'">'.$sluzhba_name.'</a></td>'."\r\n";
				$rez_str.= '<td class=td_1>'."\r\n";
				
				if ($cnt > 1)
				{
						$arr_predpr = array();
						$res2 = $link1->query('SELECT `POINT_SHORT_TITLE`,`POINT_ID` FROM '.$tbl_points.' WHERE `POINT_TYPE_ID`=192 AND `POINT_TOTAL`="'.$sluzhba_name.'"');
						while ($row_res2 = $res2->fetch_assoc())
						{				
								$predpr_name = $row_res2['POINT_SHORT_TITLE'];
								$predpr_id = $row_res2['POINT_ID'];
								
								$arr_predpr[] = '<a class=a1 href="xls_nbt_siz.php?sl_id_doc='.$predpr_id.'&sl_fmt_doc=1&sl_dat1='.date('Y-m-01', $mkt1).'">'.$predpr_name.'</a>'."\r\n";
						}
						$rez_str.= implode(', ', $arr_predpr);
						$rez_str.= '</td>'."\r\n";
				}
				
				$rez_str.= '</tr>'."\r\n";
		}
		
		$rez_str.= '<tr>'."\r\n";
		$rez_str.= '<td class=td_1 style="text-align: center; "><a class=a1 href="xls_nbt_siz.php?sl_id_doc=800990&sl_fmt_doc=1&sl_dat1='.date('Y-m-01', $mkt1).'">ДИ</a></td>'."\r\n";
		$rez_str.= '<td class=td_1></td>'."\r\n";
		$rez_str.= '</tr>'."\r\n";
		
		$rez_str.= '<tr>'."\r\n";
		$rez_str.= '<td class=td_1 style="text-align: center; font-weight: bold;"><a class=a1 href="xls_nbt_siz.php?sl_id_doc=800999&sl_fmt_doc=1&sl_dat1='.date('Y-m-01', $mkt1).'">Дорога</a></td>'."\r\n";
		$rez_str.= '<td class=td_1></td>'."\r\n";
		$rez_str.= '</tr>'."\r\n";
		$rez_str.= '</table>'."\r\n";
		return	$rez_str;
}

/*
function form_predpr_menu_dcntib_ntu12_html($link1, $mkt1, $tbl_points)
{
		$rez_str = '<table class=tbl_1>'."\r\n";
		$rez_str.= '<tr style="text-align: center;">'."\r\n";
		$rez_str.= '<td class=td_1>Станция</td>'."\r\n";
		$rez_str.= '</tr>'."\r\n";
		$res2 = $link1->query('SELECT `POINT_SHORT_TITLE`,`POINT_ID` FROM '.$tbl_points.' WHERE `POINT_TYPE_ID`=197');
		while ($row_res2 = $res2->fetch_assoc())
		{				
				$stan_name = $row_res2['POINT_SHORT_TITLE'];
				$stan_id = $row_res2['POINT_ID'];

				$rez_str.= '<tr>'."\r\n";				
				$rez_str.= '<td class=td_1 style="text-align: center;">'."\r\n";
				$rez_str.= '<a class=a1 href="xls_dcntib_ntu12.php?sl_id_stan='.$stan_id.'&sl_dat1='.date('Y-m-01', $mkt1).'">'.$stan_name.'</a>'."\r\n";
				$rez_str.= '</td>'."\r\n";
				$rez_str.= '</tr>'."\r\n";
		}
		$rez_str.= '</table>'."\r\n";
		return	$rez_str;
}
*/
?>
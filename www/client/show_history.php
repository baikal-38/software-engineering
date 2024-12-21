<?php
header("Content-type: text/html; charset=UTF-8;");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
    
	require_once 'class_MathExprCalculator.php';
    require_once 'class_ARMMaketObjects.php';
    require_once 'class_MaketManager.php';
    require_once 'class_Users.php';
    require_once 'class_PointTypes.php';
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
?>
<!DOCTYPE html>
<html>
<head>
	<title>АРМ Макет</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8;">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="cashe-control" content="no-cashe">

</head>
<body>

<style>
		.table_hist_1   {											border-color: #787878; border-width: .5pt .5pt .5pt .5pt; border-style: solid; border-collapse:collapse; }
		.td_hist_1      { font-family: Verdana; font-size: 10px;	border-color: #787878; border-width: .5pt .5pt .5pt .5pt; border-style: solid;     }
</style>


<?php
$res1 = $link1->query('SELECT `ADMIN` FROM '.$tbl_users.' WHERE `USER_ID`="'.$_SESSION['user_id'].'"');
$row_res1 = $res1->fetch_assoc();
$admin = $row_res1['ADMIN'];

if ($obj === 'makets'        && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'coord_gr'      && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'tables'        && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'fields'        && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'frazes'        && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'point_types'   && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'points'        && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'users'         && !($admin == 1 || $admin == 2))   {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'user_rights'   && !($admin == 1 || $admin == 2))   {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'maket_rights'  && !($admin == 1 || $admin == 2))   {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
//error_log($obj);


$res1 = $link1->query('SELECT `ADMIN` FROM '.$tbl_users.' WHERE `USER_ID`="'.$_SESSION['user_id'].'"');
$row_res1 = $res1->fetch_assoc();
$admin = $row_res1['ADMIN'];


$phraze_id = $edit_id;

if ($phraze_id == 0)
{
	echo 'Некорректный ID фразы.';
	exit;
}
else
{
	$res1 = $link1->query('SELECT `PH_CODE` FROM '.$tbl_phrases.' WHERE `PH_ID`="'.$phraze_id.'"');
	$row_res1 = $res1->fetch_assoc();
	$phraze_code = $row_res1['PH_CODE'];
	$phraze_arr[$phraze_id] = $phraze_code;
}



$maket_table_id = 319;
$maket_id = 314;
if ($maket_kpp == 0)
{
	echo 'Некорректный ID КПП.';
	exit;
}


$obj_maket = new MaketManager($link1);
$obj_maket->tbl_points = $tbl_points;
$obj_maket->tbl_point_types = $tbl_point_types;
$obj_maket->tbl_makets = $tbl_makets;
$obj_maket->tbl_user_rights = $tbl_user_rights;
$obj_maket->tbl_maket_data = $tbl_maket_data;
$obj_maket->tbl_makets_tables = $tbl_makets_tables;
$obj_maket->tbl_makets_groups = $tbl_makets_groups;
$obj_maket->tbl_phrases = $tbl_phrases;
$obj_maket->tbl_fields = $tbl_fields;
$obj_maket->tbl_log_user_action = $tbl_log_user_action;



echo showHistory($mkt1, $obj_maket, $maket_table_id, $maket_id, $maket_kpp, $phraze_arr, $phraze_id);

function showHistory($mkt1, $obj_maket, $maket_table_id, $maket_id, $maket_kpp, $phraze_arr, $phraze_id)
{
	$rez_str = '<table class=table_hist_1   style="margin: auto; ">'."\r\n";
	$rez_str.= '<tr style="background-color: #c9f5c9; text-align: center;">'."\r\n";
	$rez_str.= '<td class=td_hist_1 rowspan=2 style="">Месяц</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  colspan="2">план на 2024 г.</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  colspan="2">план получения на отчетный квартал (нарастающим итогом)</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  colspan="2">факт получения на отчетный период (нарастающим итогом с начала года)</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  colspan="2">факт получения за отчетный месяц</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  colspan="2">% выполнения</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  colspan="2">Кол-во работников</td>'."\r\n";
	$rez_str.= '</tr>'."\r\n";
	$rez_str.= '<tr style="background-color: #c9f5c9; text-align: center;">'."\r\n";
	$rez_str.= '<td class=td_hist_1  >ед.</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  >тыс.руб.</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  >ед.</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  >тыс.руб.</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  >ед.</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  >тыс.руб.</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  >ед.</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  >тыс.руб.</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  >на отчетный период, ед.</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  >от плана 2024 г., ед.</td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  >подлежащих обеспечению СИЗ, чел. </td>'."\r\n";
	$rez_str.= '<td class=td_hist_1  >обеспеченных СИЗ, чел. </td>'."\r\n";
	$rez_str.= '</tr>'."\r\n";
	$rez_str.= '<tr style="background-color: #c9f5c9; text-align: center;">'."\r\n";
	$rez_str.= '<td class=td_hist_1 ><div style="width: 80px;"></div></td>'."\r\n";
	$rez_str.= '<td class=td_hist_1>1<div style="width: 30px;"></div></td>'."\r\n";
	$rez_str.= '<td class=td_hist_1>2<div style="width: 30px;"></div></td>'."\r\n";
	$rez_str.= '<td class=td_hist_1>3<div style="width: 30px;"></div></td>'."\r\n";
	$rez_str.= '<td class=td_hist_1>4<div style="width: 30px;"></div></td>'."\r\n";
	$rez_str.= '<td class=td_hist_1>11<div style="width: 30px;"></div></td>'."\r\n";
	$rez_str.= '<td class=td_hist_1>12<div style="width: 30px;"></div></td>'."\r\n";
	$rez_str.= '<td class=td_hist_1>5<div style="width: 30px;"></div></td>'."\r\n";
	$rez_str.= '<td class=td_hist_1>6<div style="width: 30px;"></div></td>'."\r\n";
	$rez_str.= '<td class=td_hist_1>7<div style="width: 30px;"></div></td>'."\r\n";
	$rez_str.= '<td class=td_hist_1>8<div style="width: 30px;"></div></td>'."\r\n";
	$rez_str.= '<td class=td_hist_1>9<div style="width: 30px;"></div></td>'."\r\n";
	$rez_str.= '<td class=td_hist_1>10<div style="width: 30px;"></div></td>'."\r\n";
	$rez_str.= '</tr>'."\r\n";

	for($i=0; $i<=date("m",$mkt1)-1; $i++)
	{
			$mkt2 = mktime(0, 0, 0, date("m", $mkt1)-$i, 1, date("Y", $mkt1));

			$list_frazes = $phraze_arr;
			$arr_frazes_masks = $obj_maket->GetArrFrazesMasksByMaketTableID($maket_table_id);
			$frazes_arr = $obj_maket->GetFrazesArrByMaketTableID($maket_table_id);

			$list_fields = $obj_maket->GetListFieldsByMaketTableID($maket_table_id);
			$fields_arr = $obj_maket->GetFieldsArrByMaketTableID($maket_table_id);

			$mask_arr = $obj_maket->GetMasksArr($arr_frazes_masks, $fields_arr);
			$temp_arr = $obj_maket->GetDataFromTableMaketData(array_keys($list_frazes), array_keys($list_fields), $fields_arr, $frazes_arr, $mkt2, $maket_id, $maket_kpp, $mask_arr);

			$rez_str.= '<tr style="text-align: center;">'."\r\n";
			$rez_str.= '<td class=td_hist_1>'.date('Y-m-01', $mkt2).'</td>'."\r\n";
			foreach($temp_arr[$phraze_id] as $k1 => $v1)
			{
					$rez_str.= '<td class=td_hist_1>'.$v1.'</td>'."\r\n";
			}
			$rez_str.= '</tr>'."\r\n";
	}
	$rez_str.= '</table>'."\r\n";
	
	return $rez_str;
}



?>

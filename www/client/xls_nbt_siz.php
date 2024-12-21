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


set_time_limit(120);


if (!check_auth())
{
    header('Location: index.php');
    die;
}



$start_time = gettime();


if ($fmt_doc == 1)
{
		require_once('../excel_lib/Spreadsheet/Excel/Writer.php');
		require_once('maket_input_xls.php');
		
		
		$xls = new Spreadsheet_Excel_Writer();
		$xls->setVersion(8);

		
		
		$frmt1 =& $xls-> addFormat();
		$frmt1-> setFontFamily('Times New Roman');		
		$frmt1-> setSize('10');
		$frmt1-> setAlign('center');
		$frmt1-> setVAlign('vjustify');
		$frmt1-> setVAlign('vcenter');
		$frmt1-> SetBorder('1');

		$frmt1_2 =& $xls-> addFormat();
		$frmt1_2-> setFontFamily('Times New Roman');		
		$frmt1_2-> setSize('10');
		$frmt1_2-> setAlign('center');
		$frmt1_2-> setVAlign('vjustify');
		$frmt1_2-> setVAlign('vcenter');

		$frmt2 =& $xls-> addFormat();
		$frmt2-> setFontFamily('Times New Roman');		
		$frmt2-> setSize('10');
		$frmt2-> setAlign('center');
		$frmt2-> setVAlign('vjustify');
		$frmt2-> setVAlign('vcenter');
		$frmt2-> setFgColor(22);
		$frmt2-> SetBorder('1');

		$frmt2_2 =& $xls-> addFormat();
		$frmt2_2-> setFontFamily('Times New Roman');		
		$frmt2_2-> setSize('10');
		$frmt2_2-> setAlign('center');
		$frmt2_2-> setVAlign('vjustify');
		$frmt2_2-> setVAlign('vcenter');
		$frmt2_2-> setFgColor(23);
		$frmt2_2-> SetBorder('1');
		
		$frmt3 =& $xls-> addFormat();
		$frmt3-> setFontFamily('Times New Roman');		
		$frmt3-> setSize('10');
		$frmt3-> setAlign('center');
		$frmt3-> setVAlign('vjustify');
		$frmt3-> setVAlign('vcenter');
		$frmt3-> SetBold();
		$frmt3-> SetBorder('1');
		$frmt3-> setTextWrap();
		//$frmt3-> setFgColor(41);

		$frmt4 =& $xls-> addFormat();
		$frmt4-> setFontFamily('Times New Roman');		
		$frmt4-> setSize('10');
		$frmt4-> setAlign('center');
		$frmt4-> setVAlign('vjustify');
		$frmt4-> setVAlign('vcenter');
		$frmt4-> setTextWrap();
		
		$frmt5 =& $xls-> addFormat();
		$frmt5-> setFontFamily('Times New Roman');		
		$frmt5-> setSize('12');
		$frmt5-> setAlign('center');
		$frmt5-> setVAlign('vjustify');
		$frmt5-> setVAlign('vcenter');
		$frmt5-> SetBold();
		$frmt5-> setTextWrap();
		
		
		$mockup_table_id = 319;
		$mockup_id = 314;
		$point_type_id = 192;
		
		
		$arr_points = MakeListPoints($link1, $point_type_id, $id_doc, $tbl_points);
		$arr_fields = MakeListFields($link1, $mockup_table_id, $tbl_fields);
		$arr_frazes = MakeListFrazes($link1, $mockup_table_id, $tbl_phrases);
		
		/*
		if ($id_doc == 999)
		{
				$point_id = 999;
				$arr_points[999] = 'Дорога';
		}
		*/
		
		$arr_sum = array();
		
		
		$sheet_no = 1;
		foreach ($arr_points as $k1 => $v1)
		{
				$point_id = $k1;
				$point_title = $v1;
				

				$sh_name = 'sheet'.$sheet_no;
				$$sh_name =& $xls-> addWorksheet($point_title);				
				$$sh_name->setInputEncoding("UTF-8");
				$$sh_name->setZoom(85);

				$$sh_name->setColumn(1, 1, 57.86);
				for($i=3; $i<=14; $i++)
				{
						$$sh_name->setColumn($i, $i, 11);
				}
				$$sh_name->setRow(4, 49);

				///if ($point_id == 'vsego_po_sluzhbe')	{	$first_sheet = $$sh_name;	}


				$$sh_name->freezePanes(array(7, 0, 7, 0));

				$col = 1;
				$row = 1;

				$$sh_name->setMerge($row,$col, $row, $col+13);
				$$sh_name->write($row, $col, 'ОТЧЕТ '."\r\n".'по обеспечению средствами индивидуальной защиты работников Восточно-Сибирской железной дороги ', $frmt5);
				$$sh_name->setRow($row, 33);
				$row++;
				$$sh_name->setMerge($row,$col, $row, $col+13);
				$$sh_name->write($row, $col, $point_title, $frmt4);
				$$sh_name->setRow($row, 24);
				$row++;
				$$sh_name->setRow($row, 51);

				WriteHead($$sh_name, $row, $col, $frmt3, $mkt1);
				$row = 7;				
				
				
				$arr_values = MakeArrValues($link1, $mockup_id, $point_id, $mkt1, $tbl_points, $tbl_makets, $tbl_user_rights, $tbl_maket_data, $tbl_makets_tables, $tbl_makets_groups, $tbl_phrases, $tbl_fields, $tbl_log_user_action);
				$arr_sum = WriteEvent($arr_sum, $arr_fields, $arr_frazes, $arr_values, $$sh_name, $frmt1, $frmt1_2, $frmt2, $frmt2_2, $row);
				
				
				
				$sheet_no++;
		}
		
		$xls-> send('НБТ СИЗ '.$point_title.'.xls');
		$xls-> close();
}



function WriteEvent($arr_sum, $arr_fields, $arr_frazes, $arr_values, &$sh_name, $frmt1, $frmt1_2, $frmt2, $frmt2_2, $row)
{
		$fraze_key = 0;
		foreach ($arr_frazes as $k3 => $v3)
		{
				$phrase_id = $k3;
				$col = 3;
				
				
				if ($row == 7)
				{
						$sh_name->write($row, 1, 'ЗИМНИЕ СИЗ', $frmt2_2);
						for($i=1; $i<=13; $i++)
						{
							$sh_name->write($row, 1+$i, '', $frmt2_2);
						}
						$row++;
						$fraze_key++;
				}
				
				
				$frmt_X = $frmt1;
				if (in_array($fraze_key, array(1,2,14,15,18,22,26,27,31,37,52,54,55,58,60,66,67)))	$frmt_X = $frmt2;
				if (in_array($fraze_key, array(0,34,36,74)))	$frmt_X = $frmt2_2;
				
				
				$sh_name->write($row, 1, $v3, $frmt_X);
				$sh_name->write($row, 2, $fraze_key, $frmt_X);
				
				
				foreach ($arr_fields as $k2 => $v2)
				{
						$field_id = $v2;
						
						if (isset($arr_values[$phrase_id][$field_id]))
						{
								$val = html_entity_decode($arr_values[$phrase_id][$field_id]);
								if ($val == '0')	$val = '';
								
								$sh_name->write($row, $col, $val, $frmt_X);
								
								if (isset($arr_sum[$phrase_id][$field_id]))
								{
										$arr_sum[$phrase_id][$field_id] += $arr_values[$phrase_id][$field_id];
								}
								else
								{
										$arr_sum[$phrase_id][$field_id] = $arr_values[$phrase_id][$field_id];
								}
						}
						if (!isset($arr_values[$phrase_id][$field_id]))
						{
								$sh_name->write($row, $col, '', $frmt_X);
						}
						
						$col++;
				}
				
				$sh_name->setRow($row, 15);
				$row++;
				$fraze_key++;
		}
		$row++;
		
		$sh_name->write($row, 1, 'Время формирования отчета: '.date('Y-m-d H:i:s'), $frmt1_2);
		
		return $arr_sum;
}



function WriteHead($sh_name, $row, $col, $frmt3, $mkt1)
{
		if ($col == 1)
		{
			$sh_name->write($row,	$col,   '', $frmt3);
			$sh_name->write($row+1,	$col,   '', $frmt3);
			$sh_name->write($row+2,	$col,   '', $frmt3);
			$sh_name->setMerge($row,$col, $row+1, $col);
			$col++;
			
			$sh_name->write($row,	$col,   'Код фразы', $frmt3);
			$sh_name->write($row+1,	$col,   '', $frmt3);
			$sh_name->write($row+2,	$col,   '', $frmt3);
			$sh_name->setMerge($row,$col, $row+1, $col);
			$col++;
			
			$sh_name->write($row,	$col,	'план на 2024 г.', $frmt3);						
			$sh_name->write($row+1, $col,   'ед.', $frmt3);									
			$sh_name->write($row+2,	$col,   '1', $frmt3);
			$sh_name->setMerge($row,$col, $row,   $col+1);
			$col++;
			
			$sh_name->write($row,	$col,	'', $frmt3);						
			$sh_name->write($row+1, $col,   'тыс.руб.', $frmt3);									
			$sh_name->write($row+2,	$col,   '2', $frmt3);
			$col++;
			
			//
			$sh_name->write($row,	$col,	'план получения на отчетный квартал (нарастающим итогом)', $frmt3);						
			$sh_name->write($row+1, $col,   'ед.', $frmt3);									
			$sh_name->write($row+2,	$col,   '3', $frmt3);
			$sh_name->setMerge($row,$col, $row,   $col+1);
			$col++;
			
			$sh_name->write($row,	$col,	'', $frmt3);						
			$sh_name->write($row+1, $col,   'тыс.руб.', $frmt3);									
			$sh_name->write($row+2,	$col,   '4', $frmt3);
			$col++;
			
			$sh_name->write($row,	$col,	'факт получения на отчетный период (нарастающим итогом с начала года)', $frmt3);						
			$sh_name->write($row+1, $col,   'ед.', $frmt3);									
			$sh_name->write($row+2,	$col,   '11', $frmt3);
			$sh_name->setMerge($row,$col, $row,   $col+1);
			$col++;
			
			$sh_name->write($row,	$col,	'', $frmt3);						
			$sh_name->write($row+1, $col,   'тыс.руб.', $frmt3);									
			$sh_name->write($row+2,	$col,   '12', $frmt3);
			$col++;
			
			$sh_name->write($row,	$col,	'факт получения за '.get_month_name(date('m', $mkt1)).' месяц', $frmt3);						
			$sh_name->write($row+1, $col,   'ед.', $frmt3);									
			$sh_name->write($row+2,	$col,   '5', $frmt3);
			$sh_name->setMerge($row,$col, $row,   $col+1);
			$col++;
			
			$sh_name->write($row,	$col,	'', $frmt3);						
			$sh_name->write($row+1, $col,   'тыс.руб.', $frmt3);									
			$sh_name->write($row+2,	$col,   '6', $frmt3);
			$col++;
			
			$sh_name->write($row,	$col,	'% выполнения', $frmt3);						
			$sh_name->write($row+1, $col,   'на отчетный период, ед.', $frmt3);									
			$sh_name->write($row+2,	$col,   '7', $frmt3);
			$sh_name->setMerge($row,$col, $row,   $col+1);
			$col++;
			
			$sh_name->write($row,	$col,	'', $frmt3);						
			$sh_name->write($row+1, $col,   'от плана 2024 г., ед.', $frmt3);									
			$sh_name->write($row+2,	$col,   '8', $frmt3);
			$col++;
			
			$sh_name->write($row,	$col,	'Кол-во работников', $frmt3);						
			$sh_name->write($row+1, $col,   'ед.', $frmt3);									
			$sh_name->write($row+2,	$col,   '9', $frmt3);
			$sh_name->setMerge($row,$col, $row,   $col+1);
			$col++;
			
			$sh_name->write($row,	$col,	'', $frmt3);						
			$sh_name->write($row+1, $col,   'тыс.руб.', $frmt3);									
			$sh_name->write($row+2,	$col,   '10', $frmt3);
			$col++;
		}
		
		return $row;
}






function MakeArrValues($link1, $maket_id, $maket_kpp, $mkt1, $tbl_points, $tbl_makets, $tbl_user_rights, $tbl_maket_data, $tbl_makets_tables, $tbl_makets_groups, $tbl_phrases, $tbl_fields, $tbl_log_user_action)
{
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

		$maket_table_id = 319;
		
		$list_frazes = $obj_maket->GetListFrazesByMaketTableID($maket_table_id);
		$arr_frazes_masks = $obj_maket->GetArrFrazesMasksByMaketTableID($maket_table_id);
		$frazes_arr = $obj_maket->GetFrazesArrByMaketTableID($maket_table_id);
		
		$list_fields = $obj_maket->GetListFieldsByMaketTableID($maket_table_id);
		$fields_arr = $obj_maket->GetFieldsArrByMaketTableID($maket_table_id);
		
		$mask_arr = $obj_maket->GetMasksArr($arr_frazes_masks, $fields_arr);
		$temp_arr = $obj_maket->GetDataFromTableMaketData(array_keys($list_frazes), array_keys($list_fields), $fields_arr, $frazes_arr, $mkt1, $maket_id, $maket_kpp, $mask_arr);
		
		return $temp_arr;
}		


function MakeListPoints($link1, $point_type_id, $id_doc, $tbl_points)
{
		$arr_points = array();
		
		//службы
		if ($id_doc == 0)
		{
				$sluzhba_name = $_GET['sl_sluzhba_name'];
				
				$query1 = 'SELECT `POINT_ID`,`POINT_SHORT_TITLE` FROM '.$tbl_points.' WHERE `POINT_TYPE_ID`=? AND POINT_TOTAL=?';
				$stmt1 = $link1->prepare($query1);
				$stmt1->bind_param("is", $point_type_id, $sluzhba_name);
				$stmt1->execute();
				$result = $stmt1->get_result();
				while ($row = $result->fetch_assoc())
				{
						$arr[] = $row['POINT_ID'];
				}
				$arr_points[implode($arr, ',')] = 'Всего по службе '.$sluzhba_name;
		}
		
		//предприятия
		if ($id_doc != 0 && $id_doc != 800999)
		{
				$res1 = $link1->query('SELECT * FROM '.$tbl_points.' WHERE `POINT_TYPE_ID`='.$point_type_id.' AND POINT_ID IN ('.$id_doc.') ORDER BY `POINT_ORDER_NO`');		
				while ($row_res1 = $res1->fetch_assoc())
				{				
						$arr_points[$row_res1['POINT_ID']] = $row_res1['POINT_SHORT_TITLE'];
				}
		}
		
		
		//всего по ДИ
		if ($id_doc == 800990)
		{
				$s1 = 'П';
				$s2 = 'В';
				$s3 = 'Ш';
				
				$query1 = 'SELECT `POINT_ID`,`POINT_SHORT_TITLE` FROM '.$tbl_points.' WHERE `POINT_TYPE_ID`=? AND (POINT_TOTAL=? OR POINT_TOTAL=? OR POINT_TOTAL=?)';
				$stmt1 = $link1->prepare($query1);
				$stmt1->bind_param("isss", $point_type_id, $s1,$s2,$s3);
				$stmt1->execute();
				$result = $stmt1->get_result();
				while ($row = $result->fetch_assoc())
				{
						$arr[] = $row['POINT_ID'];
				}
				$arr_points[implode($arr, ',')] = 'Всего по службе ДИ';
		}


		//Дорога
		if ($id_doc == 800999)
		{
				$arr_points2 = array();
				
				$res1 = $link1->query('SELECT * FROM '.$tbl_points.' WHERE `POINT_TYPE_ID`='.$point_type_id.' ORDER BY `POINT_ORDER_NO`');		
				while ($row_res1 = $res1->fetch_assoc())
				{				
						$arr_points2[$row_res1['POINT_ID']] = $row_res1['POINT_SHORT_TITLE'];
				}
				
				$arr_points3[implode(',', array_keys($arr_points2))] = 'Всего по ВСЖД';
				
				$arr_points = $arr_points3;
		}
		
		return $arr_points;
}		

function MakeListFields($link1, $mockup_table_id, $tbl_fields)
{
		$arr_fields = array();
		
		$res2 = $link1->query('SELECT * FROM '.$tbl_fields.' WHERE `MOCKUP_TABLE_ID`='.$mockup_table_id.' ORDER BY `FIELD_ORDER_NO`');
		while ($row_res2 = $res2->fetch_assoc())
		{
				$arr_fields[] = $row_res2['FIELD_ID'];
		}
		
		return $arr_fields;
}


function MakeListFrazes($link1, $mockup_table_id, $tbl_phrases)
{
		$arr_frazes = array();
		
		$res3 = $link1->query('SELECT * FROM '.$tbl_phrases.' WHERE `MOCKUP_TABLE_ID`='.$mockup_table_id.' ORDER BY `phrases`.`PH_ORDER_NO` ASC');
		while ($row_res3 = $res3->fetch_assoc())
		{
				$arr_frazes[$row_res3['PH_ID']] = $row_res3['PH_TITLE'];
		}
		
		return $arr_frazes;
}

function get_month_name($num)
{
	switch ($num)
	{
		case 1:	return "январь";
		case 2:	return "февраль";		
		case 3: return "март";
		case 4: return "апрель";
		case 5: return "май";
		case 6: return "июнь";
		case 7: return "июль";
		case 8: return "август";
		case 9: return "сентябрь";	
		case 10: return "октябрь";	
		case 11: return "ноябрь";	
		case 12: return "декабрь";	
		default:
			   return "Некорректный номер месяца.";
	}
}

?>
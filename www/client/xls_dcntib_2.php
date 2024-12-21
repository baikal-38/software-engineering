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
		$frmt1-> setFontFamily('Arial');		
		$frmt1-> setSize('10');
		$frmt1-> setAlign('center');
		$frmt1-> SetBorder('1');

		$frmt1_2 =& $xls-> addFormat();
		$frmt1_2-> setFontFamily('Arial');		
		$frmt1_2-> setSize('10');
		$frmt1_2-> setAlign('left');
		$frmt1_2-> SetBorder('1');
		
		$frmt2 =& $xls-> addFormat();
		$frmt2-> setFontFamily('Arial');		
		$frmt2-> setSize('10');
		$frmt2-> setAlign('center');
		$frmt2-> setVAlign('vjustify');
		$frmt2-> setVAlign('vcenter');
		$frmt2-> SetBorder('1');
		$frmt2-> SetBold();
		$frmt2-> setTextRotation(270);
		$frmt2-> setTextWrap();
		$frmt2-> setFgColor(41);

		$frmt3 =& $xls-> addFormat();
		$frmt3-> setFontFamily('Arial');		
		$frmt3-> setSize('10');
		$frmt3-> setAlign('center');
		$frmt3-> setVAlign('vjustify');
		$frmt3-> setVAlign('vcenter');
		$frmt3-> SetBold();
		$frmt3-> SetBorder('1');
		$frmt3-> setTextWrap();
		$frmt3-> setFgColor(41);

		$frmt4 =& $xls-> addFormat();
		$frmt4-> setFontFamily('Arial');		
		$frmt4-> setSize('10');
		$frmt4-> setAlign('center');
		$frmt4-> setVAlign('vjustify');
		$frmt4-> setVAlign('vcenter');
		$frmt4-> SetBorder('1');
		$frmt4-> setFgColor(51);
		$frmt4-> setTextWrap();
		
		$frmt5 =& $xls-> addFormat();
		$frmt5-> setFontFamily('Arial');		
		$frmt5-> setSize('14');
		$frmt5-> setAlign('center');
		
		
		
		$mockup_id_arr = array(319, 308, 312, 321, 322, 323);
		$point_type_id = 197;
		
		$arr_values = MakeArrValues($link1, $mockup_id_arr, $mkt1, $tbl_maket_data);
		$arr_points = MakeListPoints($link1, $point_type_id, $tbl_points);
		$arr_events = array(319 => 'День информации', 
							308 => 'День специалиста', 
							312 => 'Книжные выставки. Тематические', 
							321 => 'Книжные выставки. Новые поступления', 
							322 => 'Обзоры, беседы',
							323 => 'Другие мероприятия');
		
		
		$sheet_no = 1;
		foreach ($arr_points as $k1 => $v1)
		{
				$point_id = $k1;
				$point_title = $v1;
				
				
				$sh_name = 'sheet'.$sheet_no;
				$$sh_name =& $xls-> addWorksheet($point_title);				
				$$sh_name->setInputEncoding("UTF-8");
				$$sh_name->setZoom(85);
				$$sh_name->setColumn(1, 1, 15);
				$$sh_name->setColumn(2, 2, 85);
				$$sh_name->setColumn(3, 3, 15);
				$$sh_name->setColumn(4, 4, 15);
				$$sh_name->setColumn(5, 5, 15);
				$$sh_name->setColumn(6, 6, 60);
				$$sh_name->setColumn(7, 7, 45);				
				
				$$sh_name->write(2, 3, 'Журнал учета мероприятий массового информирования и выставочной работы ТБ ВС ЦНТИБ ст.'.$point_title, $frmt5);
				$$sh_name->freezePanes(array(7, 0, 7, 0));
				
				$col = 1;
				$row = 4;
				WriteHead($$sh_name, $row, $col, $frmt2, $frmt3);
				$row = 7;
				
				
				$arr_dop_table = array();
				
				
				//перебор событий
				foreach ($mockup_id_arr as $k4 => $event_id)
				{
						if ($event_id == 319)		{	$mockup_table_id = 338;		$event_title = 'День информации';	}
						if ($event_id == 308)		{	$mockup_table_id = 317;		$event_title = 'День специалиста';	}
						if ($event_id == 312)		{	$mockup_table_id = 341;		$event_title = 'Книжные выставки. Тематические';	}
						if ($event_id == 321)		{	$mockup_table_id = 343;		$event_title = 'Книжные выставки. Новые поступления';	}
						if ($event_id == 322)		{	$mockup_table_id = 344;		$event_title = 'Обзоры, беседы';	}
						if ($event_id == 323)		{	$mockup_table_id = 345;		$event_title = 'Другие мероприятия';	}
						
						$arr_fields = MakeListFields($link1, $mockup_table_id, $tbl_fields);
						$arr_frazes = MakeListFrazes($link1, $mockup_table_id, $tbl_phrases);
						
						list($row, $arr_dop_table) = WriteEvent($arr_fields, $arr_frazes, $arr_values, $arr_dop_table, $point_id, $event_id, $event_title, $$sh_name, $frmt1, $frmt5, $row);
						
						$sheet_no++;
				}
				WriteDopTable($arr_events, $arr_dop_table, $$sh_name, $frmt1, $frmt1_2);
		}
		$xls-> send('Отчет ДЦНТИБ НТУ-18.xls'); //!!!
		$xls-> close();
}



function WriteEvent($arr_fields, $arr_frazes, $arr_values, $arr_dop_table, $point_id, $event_id, $event_title, &$sh_name, $frmt1, $frmt5, $row)
{
		$sh_name->write($row, 4, $event_title, $frmt5);
		$row++;
		
		foreach ($arr_frazes as $k3 => $v3)
		{
				$phrase_id = $k3;
				$col = 1;
				$flag = false;
				
				$month = 0;
				foreach ($arr_fields as $k2 => $v2)
				{
						$field_id = $v2;
						
						if (isset($arr_values[$point_id][$field_id][$phrase_id][$event_id]))
						{
								$val = html_entity_decode($arr_values[$point_id][$field_id][$phrase_id][$event_id]);
								
								$sh_name->write($row, $col, $val, $frmt1);
								
								$flag = true;
								
								if ($col == 1)
								{
										$mkt = strtotime($val);
										
										$y = date('Y', $mkt);
										$m = date('n', $mkt);
										$d = date('d', $mkt);
										if (checkdate($m, $d, $y)) $month = $m;
								}
						}
						if (!isset($arr_values[$point_id][$field_id][$phrase_id][$event_id]) && $flag == true)
						{
								$sh_name->write($row, $col, '', $frmt1);
						}
						
						$col++;
				}
				
				if (!isset($arr_dop_table[$month][$event_id]))	$arr_dop_table[$month][$event_id] = 0;
				if ($month != 0)	$arr_dop_table[$month][$event_id]++;
				
				if ($flag)	$row++;
		}
		
		return array($row, $arr_dop_table);
}


function WriteDopTable($arr_events, $arr_dop_table, $sh_name, $frmt1, $frmt1_2)
{
		$col = 9;
		$row = 7;
		$sh_name->write($row, $col, 'Месяц', $frmt1);
		foreach($arr_events as $k1 => $v1)
		{
				$col++;
				$sh_name->write($row, $col, $v1, $frmt1_2);
				$sh_name->setColumn($col, $col, 15);
		}
		$row++;
		
		
		for($m=1; $m<=12; $m++)
		{
				$col = 9;
				$sh_name->write($row, $col, $m, $frmt1);
				foreach($arr_events as $k1 => $v1)
				{
						$col++;
						
						$val = '';
						if (isset($arr_dop_table[$m][$k1]))	$val = $arr_dop_table[$m][$k1];
						
						$sh_name->write($row, $col, $val, $frmt1);
				}
				$row++;
		}
}


function WriteHead($sh_name, $row, $col, $frmt2, $frmt3)
{
		if ($col == 1)
		{
			$sh_name->write($row,	$col,   'Дата', $frmt3);
			$sh_name->write($row+1,	$col,   '', $frmt3);
			$sh_name->write($row+2,	$col,   '1', $frmt3);
			$sh_name->setMerge($row,$col, $row+1, $col);
			$col++;
			
			$sh_name->write($row,	$col,   'Наименование мероприятия', $frmt3);
			$sh_name->write($row+1,	$col,   '', $frmt3);
			$sh_name->write($row+2,	$col,   '2', $frmt3);
			$sh_name->setMerge($row,$col, $row+1, $col);
			$col++;
			
			$sh_name->write($row,	$col,		'Количество', $frmt3);						
			$sh_name->write($row+1, $col,   'посетителей', $frmt3);									
			$sh_name->write($row+2,	$col,   '3', $frmt3);
			$sh_name->setMerge($row,$col, $row,   $col+2);
			$col++;
			
			$sh_name->write($row,	$col,		'', $frmt3);
			$sh_name->write($row+1, $col,   'экспонируемых документов', $frmt3);
			$sh_name->write($row+2,	$col,   '4', $frmt3);
			$col++;
			
			$sh_name->write($row,	$col,		'', $frmt3);
			$sh_name->write($row+1, $col,   'просмотренных документов', $frmt3);
			$sh_name->write($row+2,	$col,   '5', $frmt3);
			$col++;
			
			$sh_name->write($row,	$col,		'Место проведения мероприятия', $frmt3);	
			$sh_name->write($row+1, $col,   '', $frmt3);
			$sh_name->write($row+2,	$col,   '6', $frmt3);
			$sh_name->setMerge($row,$col, $row+1,   $col);
			$col++;
			
			$sh_name->write($row,	$col,		'Наименование структурного подразделения или фамилия сотрудника, проводившего мероприятие', $frmt3);
			$sh_name->write($row+1, $col,   '', $frmt2);
			$sh_name->write($row+2,	$col,   '7', $frmt3);
			$sh_name->setMerge($row,$col, $row+1,   $col);
			$col++;
		}
		
		return $row;
}






function MakeArrValues($link1, $mockup_id_arr, $mkt1, $tbl_maket_data)
{
		$arr_values = array();
		
		$res1 = $link1->query('SELECT * FROM '.$tbl_maket_data.' WHERE `date`="'.date('Y', $mkt1).'-01-01" AND `MOCKUP_ID` IN ('.implode(",", $mockup_id_arr).') ORDER BY `MOCKUP_ID`');
		while ($row_res1 = $res1->fetch_assoc())
		{
				$arr_values[$row_res1['POINT_ID']][$row_res1['FIELD_ID']][$row_res1['PH_ID']][$row_res1['MOCKUP_ID']] = $row_res1['value'];
		}
		
		return $arr_values;
}		


function MakeListPoints($link1, $point_type_id, $tbl_points)
{
		$arr_points = array();
		
		$res1 = $link1->query('SELECT * FROM '.$tbl_points.' WHERE `POINT_TYPE_ID`='.$point_type_id.' ORDER BY `POINT_ORDER_NO`');
		while ($row_res1 = $res1->fetch_assoc())
		{				
				$arr_points[$row_res1['POINT_ID']] = $row_res1['POINT_SHORT_TITLE'];
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

?>
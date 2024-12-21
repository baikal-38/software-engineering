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

$report = new ReportGenerator();
$report->MakeXLSReport($link1, $mkt1, $tbl_makets, $tbl_user_rights, $tbl_maket_data, $tbl_makets_tables, $tbl_makets_groups, $tbl_phrases, $tbl_fields, $tbl_log_user_action, $tbl_points);




class ReportGenerator
{
		public function MakeXLSReport($link1, $mkt1, $tbl_makets, $tbl_user_rights, $tbl_maket_data, $tbl_makets_tables, $tbl_makets_groups, $tbl_phrases, $tbl_fields, $tbl_log_user_action, $tbl_points)
		{
				ob_start();


				require_once('../excel_lib/Spreadsheet/Excel/Writer.php');
				require_once('maket_input_xls.php');


				$xls = new Spreadsheet_Excel_Writer();
				$xls->setVersion(8);



				$frmt1 =& $xls-> addFormat();
				$frmt1-> setFontFamily('Times New Roman');		
				$frmt1-> setSize('10');
				$frmt1-> setAlign('center');
				$frmt1-> SetBorder('1');

				$frmt1_2 =& $xls-> addFormat();
				$frmt1_2-> setFontFamily('Times New Roman');		
				$frmt1_2-> setSize('10');
				$frmt1_2-> setAlign('left');
				$frmt1_2-> SetBorder('1');

				$frmt2 =& $xls-> addFormat();
				$frmt2-> setFontFamily('Times New Roman');		
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
				$frmt3-> setFontFamily('Times New Roman');		
				$frmt3-> setSize('10');
				$frmt3-> setAlign('center');
				$frmt3-> setVAlign('vjustify');
				$frmt3-> setVAlign('vcenter');
				$frmt3-> SetBold();
				$frmt3-> SetBorder('1');
				$frmt3-> setTextWrap();
				$frmt3-> setFgColor(41);

				$frmt4 =& $xls-> addFormat();
				$frmt4-> setFontFamily('Times New Roman');		
				$frmt4-> setSize('10');
				$frmt4-> setAlign('center');
				$frmt4-> setVAlign('vjustify');
				$frmt4-> setVAlign('vcenter');
				$frmt4-> SetBorder('1');
				$frmt4-> setFgColor(51);
				$frmt4-> setTextWrap();

				$frmt5 =& $xls-> addFormat();
				$frmt5-> setFontFamily('Times New Roman');		
				$frmt5-> setSize('14');
				$frmt5-> setAlign('center');



				$maket_id = 316;
				$mockup_table_id = 321;
				$point_type_id = 194;



				$arr_points = $this->MakeListPoints($link1, $point_type_id, $tbl_points);
				$arr_fields = $this->MakeListFields($link1, $mockup_table_id, $tbl_fields);
				$arr_frazes = $this->MakeListFrazes($link1, $mockup_table_id, $tbl_phrases);		


				$sh_name =& $xls-> addWorksheet('Лист1');
				$sh_name->setInputEncoding("UTF-8");
				$sh_name->setColumn(2, 2, 33);

				$sh_name->write(0, 6, 'ОТЧЕТ', $frmt5);
				$sh_name->write(1, 6, 'об авариях, инцидентах, произошедших на опасных производственных объектах,', $frmt5);
				$sh_name->write(2, 6, 'и запрещенных к эксплуатации технических устройств', $frmt5);
				$sh_name->write(3, 6, 'за __ квартал 20__ года', $frmt5);
				//$sh_name->setZoom(85);
				$sh_name->freezePanes(array(7, 0, 7, 0));		

				$col = 1;
				$row = 4;
				$this->WriteHead($sh_name, $row, $col, $frmt2, $frmt3);
				$row = 7;

				$arr_values = array();
				$arr_rez = array();

				foreach ($arr_points as $k1 => $v1)
				{
						$point_id = $k1;
						$point_title = $v1;

						$arr_values = $this->MakeArrValues($link1, $maket_id, $mockup_table_id, $point_id, $mkt1, $tbl_points, $tbl_makets, $tbl_user_rights, $tbl_maket_data, $tbl_makets_tables, $tbl_makets_groups, $tbl_phrases, $tbl_fields, $tbl_log_user_action);

						$arr_rez = $this->TransformArray($arr_fields, $arr_frazes, $arr_rez, $arr_values, $point_id);
				}
				$this->WriteEvent($arr_fields, $arr_frazes, $arr_points, $arr_rez, $sh_name, $frmt1, $row);
				$xls-> send('Отчет НБТ АГО-13.xls'); //!!!
				$xls-> close();
		}


		function TransformArray($arr_fields, $arr_frazes, $arr_rez, $arr_values, $point_id)
		{
				foreach ($arr_frazes as $k3 => $v3)
				{
						$phrase_id = $k3;
						foreach ($arr_fields as $k2 => $v2)
						{
								$field_id = $v2;

								//if (isset($arr_values[$phrase_id][$field_id]))
								//{
										if (!isset($arr_values[$phrase_id][$field_id]))		$arr_values[$phrase_id][$field_id] = 0;
										$arr_rez[$phrase_id][$field_id][$point_id] =		$arr_values[$phrase_id][$field_id];

										if (!isset($arr_rez[$phrase_id][$field_id][997]))	$arr_rez[$phrase_id][$field_id][997] = 0;
										if (in_array($point_id, array(2082, 2081, 2080)))	$arr_rez[$phrase_id][$field_id][997] +=	$arr_values[$phrase_id][$field_id];

										if (!isset($arr_rez[$phrase_id][$field_id][999]))	$arr_rez[$phrase_id][$field_id][999] = 0;
										if ($point_id != 997)	$arr_rez[$phrase_id][$field_id][999] +=	$arr_values[$phrase_id][$field_id];
								//}
						}
				}

				return $arr_rez;
		}


		function WriteEvent($arr_fields, $arr_frazes, $arr_points, $arr_values, &$sh_name, $frmt1, $row)
		{
				foreach ($arr_points as $k1 => $v1)
				{
						$point_id = $k1;
						$point_title = $v1;

						$i = 1;

						$sh_name->write($row, 1, $point_title, $frmt1);
						$sh_name->setMerge($row, 1, $row, 12);

						$row++;

						$sh_name->write($row, 1, 'А', $frmt1);
						$sh_name->write($row, 2, 'Б', $frmt1);
						$sh_name->write($row, 3, 'В', $frmt1);
						$sh_name->write($row, 4, '1', $frmt1);
						$sh_name->write($row, 5, '2', $frmt1);
						$sh_name->write($row, 6, '3', $frmt1);
						$sh_name->write($row, 7, '4', $frmt1);
						$sh_name->write($row, 8, '4а', $frmt1);
						$sh_name->write($row, 9, '4б', $frmt1);
						$sh_name->write($row, 10, '5', $frmt1);
						$sh_name->write($row, 11, '5а', $frmt1);
						$sh_name->write($row, 12, '5б', $frmt1);

						$row++;

						foreach ($arr_frazes as $k3 => $v3)
						{
								$phrase_id = $k3;

								$sh_name->write($row, 1, $i, $frmt1);
								$sh_name->write($row, 2, $v3, $frmt1);
								$sh_name->write($row, 3, 'ед.', $frmt1);

								$col = 4;
								foreach ($arr_fields as $k2 => $v2)
								{
										$field_id = $v2;

										if (isset($arr_values[$phrase_id][$field_id][$point_id]))
										{
												$val = html_entity_decode($arr_values[$phrase_id][$field_id][$point_id]);

												$sh_name->write($row, $col, $val, $frmt1);
										}
										if (!isset($arr_values[$phrase_id][$field_id][$point_id]))
										{
												$sh_name->write($row, $col, '', $frmt1);
										}

										$col++;
								}
								$row++;
								$i++;
						}
						//$row++;
				}
		}





		function WriteHead($sh_name, $row, $col, $frmt2, $frmt3)
		{
				if ($col == 1)
				{
					$sh_name->write($row,	$col,   '№', $frmt3);
					$sh_name->write($row+1,	$col,   '', $frmt3);
					$sh_name->write($row+2,	$col,   '', $frmt3);
					$sh_name->setMerge($row,$col, $row+2, $col);
					$col++;

					$sh_name->write($row,	$col,   'Наименование технических устройств', $frmt3);
					$sh_name->write($row+1,	$col,   '', $frmt3);
					$sh_name->write($row+2,	$col,   '', $frmt3);
					$sh_name->setMerge($row,$col, $row+2, $col);
					$col++;

					$sh_name->write($row,	$col,	'Ед. изм.', $frmt3);						
					$sh_name->write($row+1, $col,   '', $frmt3);									
					$sh_name->write($row+2,	$col,   '', $frmt3);
					$sh_name->setMerge($row,$col, $row+2, $col);
					$col++;

					$sh_name->write($row,	$col,	'Всего технических устройств', $frmt3);
					$sh_name->write($row+1, $col,   '', $frmt3);
					$sh_name->write($row+2,	$col,   '', $frmt3);
					$sh_name->setMerge($row,$col, $row+2, $col);
					$col++;

					$sh_name->write($row,	$col,	'Кол-во аварий, произошедших на опасных производственных объектах при эксплуатации технических устройств', $frmt3);
					$sh_name->write($row+1, $col,   '', $frmt3);
					$sh_name->write($row+2,	$col,   '', $frmt3);
					$sh_name->setMerge($row,$col, $row+2, $col);
					$col++;

					$sh_name->write($row,	$col,	'Кол-во инцидентов, произошедших на опасных производственных объектах при эксплуатации технических устройств', $frmt3);	
					$sh_name->write($row+1, $col,   '', $frmt3);
					$sh_name->write($row+2,	$col,   '', $frmt3);
					$sh_name->setMerge($row,$col, $row+2, $col);
					$col++;

					$sh_name->write($row,	$col,	'Запрещено к эксплуатации технических устройств', $frmt3);
					$sh_name->write($row+1, $col,   'Всего', $frmt3);
					$sh_name->write($row+2,	$col,   '', $frmt3);
					$sh_name->setMerge($row,$col, $row,   $col+2);
					$sh_name->setMerge($row+1,$col, $row+2,   $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'в т.ч.', $frmt3);
					$sh_name->write($row+2,	$col,   'по решению ФОИВ и судов', $frmt3);
					$sh_name->setMerge($row+1,$col, $row+1,   $col+1);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   '', $frmt2);
					$sh_name->write($row+2,	$col,   'подлежащих списанию', $frmt3);
					//$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'Кол-во дней простоя, сутки', $frmt3);
					$sh_name->write($row+1, $col,   'Всего', $frmt3);
					$sh_name->write($row+2,	$col,   '', $frmt3);
					$sh_name->setMerge($row,$col, $row,   $col+2);
					$sh_name->setMerge($row+1,$col, $row+2,   $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'в т.ч.', $frmt3);
					$sh_name->write($row+2,	$col,   'по решению ФОИВ и судов', $frmt3);
					$sh_name->setMerge($row+1,$col, $row+1,   $col+1);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   '', $frmt2);
					$sh_name->write($row+2,	$col,   'подлежащих списанию', $frmt3);
					//$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;
				}

				return $row;
		}






		function MakeArrValues($link1, $maket_id, $maket_table_id, $maket_kpp, $mkt1, $tbl_points, $tbl_makets, $tbl_user_rights, $tbl_maket_data, $tbl_makets_tables, $tbl_makets_groups, $tbl_phrases, $tbl_fields, $tbl_log_user_action)
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

				$list_frazes = $obj_maket->GetListFrazesByMaketTableID($maket_table_id);
				$arr_frazes_masks = $obj_maket->GetArrFrazesMasksByMaketTableID($maket_table_id);
				$frazes_arr = $obj_maket->GetFrazesArrByMaketTableID($maket_table_id);

				$list_fields = $obj_maket->GetListFieldsByMaketTableID($maket_table_id);
				$fields_arr = $obj_maket->GetFieldsArrByMaketTableID($maket_table_id);

				$mask_arr = $obj_maket->GetMasksArr($arr_frazes_masks, $fields_arr);
				$temp_arr = $obj_maket->GetDataFromTableMaketData(array_keys($list_frazes), array_keys($list_fields), $fields_arr, $frazes_arr, $mkt1, $maket_id, $maket_kpp, $mask_arr);

				//error_log(print_r($temp_arr, true));

				return $temp_arr;
		}


		function MakeListPoints($link1, $point_type_id, $tbl_points)
		{
				$arr_points = array();
				$arr_points[999] = 'Всего';

				$res1 = $link1->query('SELECT * FROM '.$tbl_points.' WHERE `POINT_TYPE_ID`='.$point_type_id.' ORDER BY `POINT_ORDER_NO`');
				while ($row_res1 = $res1->fetch_assoc())
				{
						if ($row_res1['POINT_ID'] == 2082)
						{
								$arr_points[997] = 'Всего по ДИ';
						}
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
}

?>
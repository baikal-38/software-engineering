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



				$maket_id = 327;
				$mockup_table_id = 347;
				$point_type_id = 198;



				$arr_points = $this->MakeListPoints($link1, $point_type_id, $tbl_points);
				$arr_fields = $this->MakeListFields($link1, $mockup_table_id, $tbl_fields);
				$arr_frazes = $this->MakeListFrazes($link1, $mockup_table_id, $tbl_phrases);		


				$sh_name =& $xls-> addWorksheet('Лист1');
				$sh_name->setInputEncoding("UTF-8");
				$sh_name->setColumn(2, 2, 41);
				$sh_name->setColumn(3, 20, 6);

				
				$sh_name->write(1, 11, 'Отчет о наличии технических устройств, эксплуатирующихся на опасных производственных объектах  Восточно-Сибирской железной дороги - филиала ОАО"РЖД" на ____________ г.', $frmt5);
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
				$xls-> send('Отчет НБТ АГО-12.xls'); //!!!
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


								if (!isset($arr_values[$phrase_id][$field_id]))		$arr_values[$phrase_id][$field_id] = 0;
								$arr_rez[$phrase_id][$field_id][$point_id] =		$arr_values[$phrase_id][$field_id];

								//Итого по ДИ
								if (!isset($arr_rez[$phrase_id][$field_id][997]))		$arr_rez[$phrase_id][$field_id][997] = 0;
								if (in_array($point_id, array(2050, 2051, 2052)))
								{
										if ($field_id != 3007 && $field_id != 3008)		$arr_rez[$phrase_id][$field_id][997] +=		$arr_values[$phrase_id][$field_id];
								}

								//Итого по ВСЖД филиалу  ОАО "РЖД"
								if (!isset($arr_rez[$phrase_id][$field_id][998]))		$arr_rez[$phrase_id][$field_id][998] = 0;
								if (in_array($point_id, array(2044,2045,2046,2047,2048)))
								{
										if ($field_id != 3007 && $field_id != 3008)		$arr_rez[$phrase_id][$field_id][998] +=		$arr_values[$phrase_id][$field_id];
								}

								//Итого по полигону Восточно-Сибирской железной дороги
								if (!isset($arr_rez[$phrase_id][$field_id][999]))		$arr_rez[$phrase_id][$field_id][999] = 0;
								if (in_array($point_id, array(2044,2045,2046,2047,2048,		2050, 2051, 2052,	2053,2054,2055,2056,2057,2058,2059,2060,2061,2062,2063)))
								{
										if ($field_id != 3007 && $field_id != 3008)		$arr_rez[$phrase_id][$field_id][999] +=		$arr_values[$phrase_id][$field_id];
								}
						}
				}

				$ch1 = $arr_rez[2322][3004][997];
				$ch2 = $arr_rez[2314][3004][997];
				$arr_rez[2314][3007][997] = ($ch2 != 0 ? round($ch1/$ch2*100, 2)  : '');
				$arr_rez[2314][3008][997] = $ch2 - $ch1;

				$ch1 = $arr_rez[2322][3004][998];
				$ch2 = $arr_rez[2314][3004][998];
				$arr_rez[2314][3007][998] = ($ch2 != 0 ? round($ch1/$ch2*100, 2)  : '');
				$arr_rez[2314][3008][998] = $ch2 - $ch1;

				$ch1 = $arr_rez[2322][3004][999];
				$ch2 = $arr_rez[2314][3004][999];
				$arr_rez[2314][3007][999] = ($ch2 != 0 ? round($ch1/$ch2*100, 2)  : '');
				$arr_rez[2314][3008][999] = $ch2 - $ch1;

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
						$sh_name->setMerge($row, 1, $row, 25);

						$row++;

						foreach ($arr_frazes as $k3 => $v3)
						{
								$phrase_id = $k3;

								$sh_name->write($row, 1, $i, $frmt1);
								$sh_name->write($row, 2, $v3, $frmt1);

								$col = 3;
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
					//$sh_name->setMerge($row,$col, $row+2, $col);
					$col++;

					$sh_name->write($row,	$col,   'Год ввода технического устройства в эксплуатацию', $frmt3);
					$sh_name->write($row+1,	$col,   '', $frmt3);
					$sh_name->write($row+2,	$col,   'А', $frmt3);
					//$sh_name->setMerge($row,$col, $row+2, $col);
					$col++;

					$sh_name->write($row,	$col,	'Подъемные сооружения, ед.', $frmt3);						
					$sh_name->write($row+1, $col,   'кран мостовой', $frmt2);									
					$sh_name->write($row+2,	$col,   '1', $frmt3);
					$sh_name->setMerge($row, $col, $row, $col+11);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'кран козловой', $frmt2);
					$sh_name->write($row+2,	$col,   '2', $frmt3);
					//$sh_name->setMerge($row,$col, $row+2, $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'кран на автомобильном  ходу', $frmt2);
					$sh_name->write($row+2,	$col,   '3', $frmt3);
					//$sh_name->setMerge($row,$col, $row+2, $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);	
					$sh_name->write($row+1, $col,   'кран на ж.д. ходу', $frmt2);
					$sh_name->write($row+2,	$col,   '4', $frmt3);
					//$sh_name->setMerge($row,$col, $row+2, $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'кран на гусеничном ходу', $frmt2);
					$sh_name->write($row+2,	$col,   '5', $frmt3);
					//$sh_name->setMerge($row,$col, $row,   $col+2);
					//$sh_name->setMerge($row+1,$col, $row+2,   $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'кран башенный', $frmt2);
					$sh_name->write($row+2,	$col,   '6', $frmt3);
					//$sh_name->setMerge($row+1,$col, $row+1,   $col+1);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'кран консольный на ССПС', $frmt2);
					$sh_name->write($row+2,	$col,   '7', $frmt3);
					//$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'кран манипулятор на автомобильном  ходу', $frmt2);
					$sh_name->write($row+2,	$col,   '8', $frmt3);
					//$sh_name->setMerge($row,$col, $row,   $col+2);
					//$sh_name->setMerge($row+1,$col, $row+2,   $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'кран манипулятор на ССПС', $frmt2);
					$sh_name->write($row+2,	$col,   '9', $frmt3);
					//$sh_name->setMerge($row+1,$col, $row+1,   $col+1);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'подъемник (вышка) на автомобильном ходу', $frmt2);
					$sh_name->write($row+2,	$col,   '10', $frmt3);
					//$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'подъемник (вышка) на ССПС', $frmt2);
					$sh_name->write($row+2,	$col,   '11', $frmt3);
					//$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'прочие', $frmt2);
					$sh_name->write($row+2,	$col,   '12', $frmt3);
					//$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'Оборудование работающее под избыточным давлением, ед.', $frmt3);
					$sh_name->write($row+1, $col,   'котел паровой', $frmt2);
					$sh_name->write($row+2,	$col,   '13', $frmt3);
					$sh_name->setMerge($row, $col, $row, $col+5);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'котел водогрейный', $frmt2);
					$sh_name->write($row+2,	$col,   '14', $frmt3);
					//$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'экономайзер', $frmt2);
					$sh_name->write($row+2,	$col,   '15', $frmt3);
					//$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'воздухосборник', $frmt2);
					$sh_name->write($row+2,	$col,   '16', $frmt3);
					//$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'паропровод', $frmt2);
					$sh_name->write($row+2,	$col,   '17', $frmt3);
					//$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'', $frmt3);
					$sh_name->write($row+1, $col,   'прочие', $frmt2);
					$sh_name->write($row+2,	$col,   '18', $frmt3);
					//$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'Всего технических устройств подлежащих учету в органах Ростехнадзора, ед.', $frmt3);
					$sh_name->write($row+1, $col,   '', $frmt2);
					$sh_name->write($row+2,	$col,   '19', $frmt3);
					$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'Технические устройства не подлежащие учету в органах Ростехнадзора, ед.', $frmt3);
					$sh_name->write($row+1, $col,   '', $frmt2);
					$sh_name->write($row+2,	$col,   '20', $frmt3);
					$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'Итого технических устройств, ед.', $frmt3);
					$sh_name->write($row+1, $col,   '', $frmt2);
					$sh_name->write($row+2,	$col,   '21', $frmt3);
					$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'Процент выработавших (поднадзорные)', $frmt3);
					$sh_name->write($row+1, $col,   '', $frmt2);
					$sh_name->write($row+2,	$col,   '22', $frmt3);
					$sh_name->setMerge($row,$col, $row+1,   $col);
					$col++;

					$sh_name->write($row,	$col,	'Не выработали нормотивный срок', $frmt3);
					$sh_name->write($row+1, $col,   '', $frmt2);
					$sh_name->write($row+2,	$col,   '23', $frmt3);
					$sh_name->setMerge($row,$col, $row+1,   $col);
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
				$arr_points[999] = 'Итого по полигону Восточно-Сибирской железной дороги';
				$arr_points[998] = 'Итого по ВСЖД филиалу  ОАО "РЖД"';

				$res1 = $link1->query('SELECT * FROM '.$tbl_points.' WHERE `POINT_TYPE_ID`='.$point_type_id.' ORDER BY `POINT_ORDER_NO`');
				while ($row_res1 = $res1->fetch_assoc())
				{
						if ($row_res1['POINT_ID'] == 2050)
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
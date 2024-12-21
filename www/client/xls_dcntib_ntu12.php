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
//require_once __DIR__.'/boot.php';

    
set_time_limit(120);


/*
if (!check_auth())
{
    header('Location: index.php');
    die;
}
*/


$start_time = gettime();


$filename = 'D:\rp\arm_maket\\'.date('Y', $mkt1).'_dcntib_1.xls';



$report = new ReportGenerator();
$report->MakeXLSReport($link1, $mkt1, $filename, $tbl_maket_data, $tbl_points, $tbl_fields, $tbl_phrases);





class ReportGenerator
{
		public function MakeXLSReport($link1, $mkt1, $filename, $tbl_maket_data, $tbl_points, $tbl_fields, $tbl_phrases)
		{
				//ob_start();
				
				require_once('D:\rp\excel_lib\Spreadsheet\Excel\Writer.php');
				
				$xls = new Spreadsheet_Excel_Writer();
				$xls->setVersion(8);


				$frmt1 =& $xls-> addFormat();
				$frmt1-> setFontFamily('Arial');		
				$frmt1-> setSize('10');
				$frmt1-> setAlign('center');
				$frmt1-> SetBorder('1');

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
				
				$frmt6 =& $xls-> addFormat();
				$frmt6-> setFontFamily('Arial');		
				$frmt6-> setSize('9');
				$frmt6-> setAlign('left');

				$mockup_id = 317;
				$mockup_table_id = 323;
				$point_type_id = 197;


				$arr_values = $this->MakeArrValues($link1, $mockup_id, $mkt1, $tbl_maket_data);
				$arr_points = $this->MakeListPoints($link1, $point_type_id, $tbl_points);
				$arr_fields = $this->MakeListFields($link1, $mockup_table_id, $tbl_fields);
				$arr_frazes = $this->MakeListFrazes($link1, $mockup_table_id, $tbl_phrases);


				$x1 = 1;
				$y1 = 5;


				$sheet_no = 1;
				foreach ($arr_points as $k1 => $v1)
				{
						$point_id = $k1;
						$point_title = $v1;

						$sh_name = 'sheet'.$sheet_no;
						$$sh_name =& $xls-> addWorksheet($point_title);				
						$$sh_name->setInputEncoding("UTF-8");
						$$sh_name->setZoom(70);
						$$sh_name->setColumn(0, 0, 15);
						$$sh_name->setColumn(count($arr_fields)+2, count($arr_fields)+2, 15);
						$$sh_name->freezePanes(array(10, 0, 10, 0));
						//$$sh_name->printArea('1', '1', '5', '8');
						
						$$sh_name->write(0, 0, 'Время формирования отчета: '.date('Y-m-d H:i:s').' МСК', $frmt6);
						$$sh_name->write(4, 14, 'Журнал учета пользователей библиотеки, посещений (обращений), выдачи документов и их копий ТБ ВС ЦНТИБ ст.'.$point_title, $frmt5);

						$this->FillOneSheet($arr_fields, $arr_frazes, $arr_values, $$sh_name, $point_id, $frmt1, $frmt2, $frmt3, $frmt4, $x1, $y1);

						$sheet_no++;
				}
				//$xls-> close();
				$xls-> send('ДЦНТИБ НТУ-12.xls');
				$xls-> close();
				
				//$attachment = ob_get_contents();
				//@ob_end_clean();

				//$this->WriteXLSToFile($attachment, $filename);
		}

		private function FillOneSheet($arr_fields, $arr_frazes, $arr_values, $sh_name, $point_id, $frmt1, $frmt2, $frmt3, $frmt4, $x1, $y1)
		{
				$col = 1;
				$row = 1;


				//обнуляем счетчики для накопления
				$arr_sum['нм'] = array();
				$arr_sum['нг'] = array();


				$mkt1  = mktime(0, 0, 0, 1, 1, date('Y'));
				for ($i=0; $i<=365; $i++)
				{
						$mkt1  = mktime(0, 0, 0, 1, 1+$i, date('Y'));
						$mkt2  = mktime(0, 0, 0, 1, 1+$i+1, date('Y'));
						$mkt3  = mktime(0, 0, 0, 1, 1+$i-1, date('Y'));

						$pdat = date('Y-m-d', $mkt1);

						foreach ($arr_frazes as $k3 => $v3)
						{
								$phrase_id = $k3;
								$sh_name->setColumn($col,$col,6.43);


								//выводим итоги на начало месяца
								if (date('m', $mkt1) != date('m', $mkt3))
								{
										$this->WriteHead($sh_name, $x1, $y1, $row, $frmt2, $frmt3);
										$row = $row+4;

										$this->WriteSummInTheBeginOfMonth($sh_name, $x1, $y1, $row, $arr_fields, $arr_sum, $point_id, $frmt4);

										$row++;

										//обнуляем счетчики для накопления
										$arr_sum['нм'] = array();
								}


								$col = 1;
								foreach ($arr_fields as $k2 => $v2)
								{
										$field_id = $v2;


										//выводим дату в первом и последнем столбце
										if ($col == 1)					
										{
												$sh_name->write			($y1+$row, $x1+$col-2,	$pdat, $frmt1);
												$sh_name->writeFormula	($y1+$row, $x1+$col-1,	'=C'.($row+$y1+1), $frmt1);
												$sh_name->writeFormula	($y1+$row, $x1+$col,	'=sum(D'.($row+$y1+1).':F'.($row+$y1+1).')', $frmt1);
												$sh_name->writeFormula	($y1+$row, $x1+$col+4,	'=sum(H'.($row+$y1+1).':J'.($row+$y1+1).')', $frmt1);
												$sh_name->writeFormula	($y1+$row, $x1+$col+8,	'=sum(L'.($row+$y1+1).':N'.($row+$y1+1).')', $frmt1);
												$sh_name->writeFormula	($y1+$row, $x1+$col+12,	'=sum(P'.($row+$y1+1).':R'.($row+$y1+1).')', $frmt1);
												$sh_name->writeFormula	($y1+$row, $x1+$col+16,	'=sum(T'.($row+$y1+1).':X'.($row+$y1+1).')', $frmt1);
												$sh_name->writeFormula	($y1+$row, $x1+$col+22,	'=sum(Z'.($row+$y1+1).';AA'.($row+$y1+1).';AC'.($row+$y1+1).')', $frmt1);
										}
										if ($col == count($arr_fields))	{	$sh_name->write($y1 + $row, $x1+$col+1, $pdat, $frmt1);	}


										if ($col != 1 && $col != 5 && $col != 9 && $col != 13 && $col != 17 && $col != 23 && isset($arr_values[$point_id][$field_id][$phrase_id][$pdat]))
										{
												$val = $arr_values[$point_id][$field_id][$phrase_id][$pdat];
												$sh_name->write($y1 + $row, $x1+$col, $val, $frmt1);

												if(!isset($arr_sum['нм'][$point_id][$field_id])) $arr_sum['нм'][$point_id][$field_id] = 0;
												if(!isset($arr_sum['нг'][$point_id][$field_id])) $arr_sum['нг'][$point_id][$field_id] = 0;

												$arr_sum['нм'][$point_id][$field_id] += $val;
												$arr_sum['нг'][$point_id][$field_id] += $val;
										}
										if ($col != 1 && $col != 5 && $col != 9 && $col != 13 && $col != 17 && $col != 23 && !isset($arr_values[$point_id][$field_id][$phrase_id][$pdat]))
										{
												$sh_name->write($y1 + $row, $x1+$col, '', $frmt1);
										}

										$col++;
								}


								//выводим итоги месяца
								if (date('m', $mkt1) != date('m', $mkt2))
								{
										$row++;
										$this->WriteSummInTheEndOfMonth($sh_name, $x1, $y1, $row, $arr_fields, $arr_sum, $point_id, $field_id, $frmt4);
										$row = $row + 1;
								}
						}
						$row++;
				}
		}
		/*
		private function WriteXLSToFile($somecontent, $filename)
		{
				if (!$fp = fopen($filename, 'r+'))
				{
					echo "Cannot open file ($filename)";
					exit;
				}
				if (fwrite($fp, $somecontent) === FALSE)
				{
					echo "Cannot write to file ($filename)";
					exit;
				}

				echo "Success, wrote to file ($filename)";
				fclose($fp);

				//$stop_time = gettime();
				//$diff_time = bcsub($stop_time,$start_time,2);
				//error_log($diff_time);
		}
		*/

		private function WriteSummInTheBeginOfMonth($sh_name, $x1, $y1, $row, $arr_fields, $arr_sum, $point_id, $frmt1)
		{
				$col = 1;
				foreach ($arr_fields as $k2 => $v2)
				{
						$field_id = $v2;

						if ($col == 1)
						{
								$sh_name->write			($y1+$row, $x1+$col-2, 'Итого к началу месяца', $frmt1);
								$sh_name->writeFormula	($y1+$row, $x1+$col-1,	'=C'.($row+$y1+1), $frmt1);
								$sh_name->writeFormula	($y1+$row, $x1+$col,	'=sum(D'.($row+$y1+1).':F'.($row+$y1+1).')', $frmt1);
								$sh_name->writeFormula	($y1+$row, $x1+$col+4,	'=sum(H'.($row+$y1+1).':J'.($row+$y1+1).')', $frmt1);
								$sh_name->writeFormula	($y1+$row, $x1+$col+8,	'=sum(L'.($row+$y1+1).':N'.($row+$y1+1).')', $frmt1);
								$sh_name->writeFormula	($y1+$row, $x1+$col+12,	'=sum(P'.($row+$y1+1).':R'.($row+$y1+1).')', $frmt1);
								$sh_name->writeFormula	($y1+$row, $x1+$col+16,	'=sum(T'.($row+$y1+1).':X'.($row+$y1+1).')', $frmt1);
								$sh_name->writeFormula	($y1+$row, $x1+$col+22,	'=sum(Z'.($row+$y1+1).';AA'.($row+$y1+1).';AC'.($row+$y1+1).')', $frmt1);
						}
						if ($col != 1 && $col != 5 && $col != 9 && $col != 13 && $col != 17 && $col != 23)
						{
								$val = 0;
								if (isset($arr_sum['нг'][$point_id][$field_id]))	$val = $arr_sum['нг'][$point_id][$field_id];

								$sh_name->write($y1+$row, $x1+$col, $val, $frmt1);
						}
						if ($col == count($arr_fields))
						{
								$sh_name->write($y1+$row, $x1+$col+1, 'Итого к началу месяца', $frmt1);
						}

						$col++;
				}
		}

		private function WriteSummInTheEndOfMonth($sh_name, $x1, $y1, $row, $arr_fields, $arr_sum, $point_id, $field_id, $frmt1)
		{
				$col = 1;
				foreach ($arr_fields as $k2 => $v2)
				{
						$field_id = $v2;

						if ($col == 1)
						{
								$sh_name->write			($y1+$row,	$x1+$col-2,		'Итого с начала месяца', $frmt1);
								$sh_name->writeFormula	($y1+$row,	$x1+$col-1,		'=C'.($row+$y1+1), $frmt1);
								$sh_name->writeFormula	($y1+$row,	$x1+$col,		'=sum(D'.($row+$y1+1).':F'.($row+$y1+1).')', $frmt1);
								$sh_name->writeFormula	($y1+$row,	$x1+$col+4,		'=sum(H'.($row+$y1+1).':J'.($row+$y1+1).')', $frmt1);
								$sh_name->writeFormula	($y1+$row,	$x1+$col+8,		'=sum(L'.($row+$y1+1).':N'.($row+$y1+1).')', $frmt1);
								$sh_name->writeFormula	($y1+$row,	$x1+$col+12,	'=sum(P'.($row+$y1+1).':R'.($row+$y1+1).')', $frmt1);
								$sh_name->writeFormula	($y1+$row,	$x1+$col+16,	'=sum(T'.($row+$y1+1).':X'.($row+$y1+1).')', $frmt1);
								$sh_name->writeFormula	($y1+$row,	$x1+$col+22,	'=sum(Z'.($row+$y1+1).';AA'.($row+$y1+1).';AC'.($row+$y1+1).')', $frmt1);

								$sh_name->write			($y1+$row+1, $x1+$col-2,	'Итого с начала года', $frmt1);
								$sh_name->writeFormula	($y1+$row+1, $x1+$col-1,	'=C'.($row+$y1+2), $frmt1);
								$sh_name->writeFormula	($y1+$row+1, $x1+$col,		'=sum(D'.($row+$y1+2).':F'.($row+$y1+2).')', $frmt1);
								$sh_name->writeFormula	($y1+$row+1, $x1+$col+4,	'=sum(H'.($row+$y1+2).':J'.($row+$y1+2).')', $frmt1);
								$sh_name->writeFormula	($y1+$row+1, $x1+$col+8,	'=sum(L'.($row+$y1+2).':N'.($row+$y1+2).')', $frmt1);
								$sh_name->writeFormula	($y1+$row+1, $x1+$col+12,	'=sum(P'.($row+$y1+2).':R'.($row+$y1+2).')', $frmt1);
								$sh_name->writeFormula	($y1+$row+1, $x1+$col+16,	'=sum(T'.($row+$y1+2).':X'.($row+$y1+2).')', $frmt1);
								$sh_name->writeFormula	($y1+$row+1, $x1+$col+22,	'=sum(Z'.($row+$y1+2).';AA'.($row+$y1+2).';AC'.($row+$y1+2).')', $frmt1);
						}
						if ($col != 1 && $col != 5 && $col != 9 && $col != 13 && $col != 17 && $col != 23)
						{
								$val1 = 0;
								$val2 = 0;

								if (isset($arr_sum['нм'][$point_id][$field_id]))	$val1 = $arr_sum['нм'][$point_id][$field_id];
								if (isset($arr_sum['нг'][$point_id][$field_id]))	$val2 = $arr_sum['нг'][$point_id][$field_id];

								$sh_name->write($y1+$row,	$x1+$col,   $val1, $frmt1);
								$sh_name->write($y1+$row+1,	$x1+$col,   $val2, $frmt1);
						}
						if ($col == count($arr_fields))
						{
								$sh_name->write($y1+$row,	$x1+$col+1, 'Итого с начала месяца', $frmt1);
								$sh_name->write($y1+$row+1,	$x1+$col+1, 'Итого с начала года', $frmt1);
						}

						$col++;
				}
		}

		private function WriteHead($sh_name, $x1, $y1, $row, $frmt2, $frmt3)
		{
				$col = 1;

				$sh_name->setRow($y1+$row,27.75);
				$sh_name->setRow($y1+$row+1,27.75);
				$sh_name->setRow($y1+$row+2,126.75);

				$sh_name->write($y1+$row,	$x1+$col-2,   'Дата', $frmt3);
				$sh_name->write($y1+$row+1,	$x1+$col-2,   '', $frmt3);		
				$sh_name->write($y1+$row+2,	$x1+$col-2,   '', $frmt3);		
				$sh_name->write($y1+$row+3,	$x1+$col-2,   '', $frmt3);
				$sh_name->setMerge($y1+$row, $x1+$col-2, $y1+$row+2, $x1+$col-2);

				$sh_name->write($y1+$row,	$x1+$col-1,   'Количество пользователей. Всего', $frmt2);
				$sh_name->write($y1+$row+1,	$x1+$col-1,   '', $frmt2);		
				$sh_name->write($y1+$row+2,	$x1+$col-1,   '', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col-1,   $col, $frmt3);
				$sh_name->setMerge($y1+$row, $x1+$col-1, $y1+$row+2, $x1+$col-1);

				$sh_name->write($y1+$row, $x1+$col,		'Количество читателей', $frmt3);			$sh_name->setMerge($y1+$row,   $x1+$col, $y1+$row,   $x1+$col+7);
				$sh_name->write($y1+$row+1, $x1+$col,   'Всего', $frmt2);							$sh_name->setMerge($y1+$row+1, $x1+$col, $y1+$row+2, $x1+$col);	
				$sh_name->write($y1+$row+2, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   'по категориям', $frmt3);					$sh_name->setMerge($y1+$row+1, $x1+$col, $y1+$row+1, $x1+$col+2);
				$sh_name->write($y1+$row+2, $x1+$col,   'руководители', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'специалисты', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'рабочие', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   'Всего', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   '', $frmt2);			
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);
				$sh_name->setMerge($y1+$row+1, $x1+$col, $y1+$row+2, $x1+$col);							$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   'По формам обслуживания', $frmt3);				$sh_name->setMerge($y1+$row+1, $x1+$col, $y1+$row+1, $x1+$col+2);	
				$sh_name->write($y1+$row+2, $x1+$col,   'абонемент', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'внестационарные формы', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'Дистанционные формы обслуживания', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'Количество посещений (обращений)', $frmt3);	$sh_name->setMerge($y1+$row,   $x1+$col, $y1+$row,   $x1+$col+3);
				$sh_name->write($y1+$row+1, $x1+$col,   'Всего', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   '', $frmt2);
				$sh_name->setMerge($y1+$row+1, $x1+$col, $y1+$row+2, $x1+$col);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   'По формам обслуживания', $frmt3);			$sh_name->setMerge($y1+$row+1, $x1+$col, $y1+$row+1, $x1+$col+2);	
				$sh_name->write($y1+$row+2, $x1+$col,   'абонемент, читальный зал', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'внестационарные формы', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'Дистанционные формы обслуживания', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'Учет выданных документов', $frmt3);			$sh_name->setMerge($y1+$row,   $x1+$col, $y1+$row,   $x1+$col+14);
				$sh_name->write($y1+$row+1, $x1+$col,   'Всего', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);
				$sh_name->setMerge($y1+$row+1, $x1+$col, $y1+$row+2, $x1+$col);							$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   'По формам обслуживания', $frmt3);				$sh_name->setMerge($y1+$row+1, $x1+$col, $y1+$row+1, $x1+$col+2);	
				$sh_name->write($y1+$row+2, $x1+$col,   'абонемент, читальный зал', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'внестационарные формы', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'Дистанционные формы обслуживания', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);									$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   'Всего', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);
				$sh_name->setMerge($y1+$row+1, $x1+$col, $y1+$row+2, $x1+$col);							$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   'по видам изданий', $frmt3);					$sh_name->setMerge($y1+$row+1, $x1+$col, $y1+$row+1, $x1+$col+4);	
				$sh_name->write($y1+$row+2, $x1+$col,   'книги', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);								$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'журналы', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);								$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'специальные виды', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);								$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'норматив. производств.-практические', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);								$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'неопубликованные', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);								$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   'Всего', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);
				$sh_name->setMerge($y1+$row+1, $x1+$col, $y1+$row+2, $x1+$col);							$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   'по содержанию', $frmt3);						$sh_name->setMerge($y1+$row+1, $x1+$col, $y1+$row+1, $x1+$col+3);	
				$sh_name->write($y1+$row+2, $x1+$col,   'социально-экономическая', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);								$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'техника', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);								$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'в т. ч. по тематике ж/д. транспорта', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);								$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'художественная', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);								$col++;

				$sh_name->write($y1+$row, $x1+$col,		'Из общего количества выданных документов', $frmt3);	$sh_name->setMerge($y1+$row,   $x1+$col, $y1+$row+1,   $x1+$col+2);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'изданий органов НТИ', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);								$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'электронных изданий', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);								$col++;

				$sh_name->write($y1+$row, $x1+$col,		'', $frmt3);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   'из депозитарного фонда', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);								$col++;

				$sh_name->write($y1+$row, $x1+$col,		'Учет выданных газет (в подшивках)', $frmt2);			$sh_name->setMerge($y1+$row,   $x1+$col, $y1+$row+2,   $x1+$col);
				$sh_name->write($y1+$row+1, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+2, $x1+$col,   '', $frmt2);
				$sh_name->write($y1+$row+3,	$x1+$col,   $col, $frmt3);								$col++;

				$sh_name->write($y1+$row,	$x1+$col,   'Дата', $frmt3);
				$sh_name->write($y1+$row+1,	$x1+$col,   '', $frmt3);
				$sh_name->write($y1+$row+2,	$x1+$col,   '', $frmt3);
				$sh_name->write($y1+$row+3,	$x1+$col,   '', $frmt3);
				$sh_name->setMerge($y1+$row, $x1+$col, $y1+$row+2, $x1+$col);
		}

		private function MakeArrValues($link1, $mockup_id, $mkt1, $tbl_maket_data)
		{
				$arr_values = array();

				$res1 = $link1->query('SELECT * FROM '.$tbl_maket_data.' WHERE `date`>="'.date('Y', $mkt1).'-01-01" AND `date`<="'.date('Y', $mkt1).'-12-31" AND `MOCKUP_ID`='.$mockup_id.'');
				while ($row_res1 = $res1->fetch_assoc())
				{
						$arr_values[$row_res1['POINT_ID']][$row_res1['FIELD_ID']][$row_res1['PH_ID']][$row_res1['date']] = $row_res1['value'];
						@$arr_values['-999'][$row_res1['FIELD_ID']][$row_res1['PH_ID']][$row_res1['date']]+= $row_res1['value'];
				}

				return $arr_values;
		}

		private function MakeListPoints($link1, $point_type_id, $tbl_points)
		{
				$arr_points = array();

				$arr_points['-999'] = 'Сводная';

				$res1 = $link1->query('SELECT * FROM '.$tbl_points.' WHERE `POINT_TYPE_ID`='.$point_type_id.' ORDER BY `POINT_ORDER_NO`');
				while ($row_res1 = $res1->fetch_assoc())
				{				
						$arr_points[$row_res1['POINT_ID']] = $row_res1['POINT_SHORT_TITLE'];
				}

				return $arr_points;
		}		

		private function MakeListFields($link1, $mockup_table_id, $tbl_fields)
		{
				$arr_fields = array();

				$res2 = $link1->query('SELECT * FROM '.$tbl_fields.' WHERE `MOCKUP_TABLE_ID`='.$mockup_table_id.' ORDER BY `FIELD_ORDER_NO`');
				while ($row_res2 = $res2->fetch_assoc())
				{
						$arr_fields[] = $row_res2['FIELD_ID'];
				}

				return $arr_fields;
		}


		private function MakeListFrazes($link1, $mockup_table_id, $tbl_phrases)
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
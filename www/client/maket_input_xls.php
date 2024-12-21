<?php

function Show_XLS_version($xls, $sheet, $maket_code, $maket_name, $maket_kpp_name, $mkt1)
{
		$sheet-> setLandscape('0');			// параметры страницы 0-альбомная 1-книжная
		$sheet-> hideGridLines();
		$sheet-> setMarginLeft('0.2');		// отступ слева
		$sheet-> setMarginRight('0.2');		// отступ справа
		//$sheet->setRow(1,40, $format = 0, $hidden = false, $level = 0); // устанавливаем ширину первой строки
		//$sheet->setPrintScale(78);       // установка масштаба в параметрах страницы для печати 
		$sheet->setZoom(100);             // установка масштаба для просмотра
		$sheet-> fitToPages(1,0);


		$frmt2_stan_table =& $xls-> addFormat();
		$frmt2_stan_table-> setFontFamily('Verdana');
		//$frmt2_stan_table-> setBold();
		$frmt2_stan_table-> setSize('8');
		$frmt2_stan_table-> setAlign('center');
		$frmt2_stan_table-> setAlign('vcenter');
		$frmt2_stan_table-> SetBorder('1');
		$frmt2_stan_table-> setTextWrap();
		//$frmt2_stan_table-> setFgColor($xls_stan_color);


		$frmt3_head_doc_bold =& $xls-> addFormat();
		$frmt3_head_doc_bold-> setFontFamily('Verdana');
		$frmt3_head_doc_bold-> setBold();
		$frmt3_head_doc_bold-> setSize('12');
		$frmt3_head_doc_bold-> setAlign('left');

		$x1 = 1;
		$y1 = 1;


		$sheet->write($y1, $x1, 'Макет: '.$maket_code.': '.$maket_name,		$frmt3_head_doc_bold);	$y1++;
		$sheet->write($y1, $x1, 'КПП: '.$maket_kpp_name,					$frmt3_head_doc_bold);	$y1++;
		$sheet->write($y1, $x1, 'Дата: '.date('Y-m-d', $mkt1),				$frmt3_head_doc_bold);	$y1++;

		$y1++;
}



function form_XLS_table_head($xls, $sheet, $x1, $y1, $arr_head3, $arr_head4, $maket_table_title, $kol_vo_fld_level)
{
		$frmt1_head_table =& $xls-> addFormat();
		$frmt1_head_table-> setFontFamily('Verdana');
		//$frmt1-> setBold();
		$frmt1_head_table-> setSize('8');
		$frmt1_head_table-> setAlign('center');
		$frmt1_head_table-> setAlign('vcenter');
		$frmt1_head_table-> SetBorder('1');
		$frmt1_head_table-> setTextWrap();
		$frmt1_head_table-> setFgColor(22);
		
		
        $res = ''."\r\n";
        for($i=0; $i<$kol_vo_fld_level; $i++)
        {
                ////$res.= '<tr style="background-color: #CDCDCD;">'."\r\n";
                if ($i == 0)
				{
						////$res.= '<td class=td_1 rowspan='.$kol_vo_fld_level.'>'.$maket_table_title.'</td>';
						////$res.= '<td class=td_1 rowspan='.$kol_vo_fld_level.'>Код фразы</td>';
						for($var1 = $y1+1; $var1 <= $y1 + $kol_vo_fld_level - 1; $var1++)
						{
								$sheet->write($var1, $x1, $maket_table_title, $frmt1_head_table);			//для форматирования пустых ячеек
						}
						$sheet->write($y1, $x1, $maket_table_title, $frmt1_head_table);
						$sheet->setMerge($y1, $x1,   $y1 + $kol_vo_fld_level - 1, $x1);
						$x1++;
						for($var1 = $y1+1; $var1 <= $y1 + $kol_vo_fld_level - 1; $var1++)
						{
								$sheet->write($var1, $x1, $maket_table_title, $frmt1_head_table);			//для форматирования пустых ячеек
						}
						$sheet->write($y1, $x1, 'Код фразы', $frmt1_head_table);
						$sheet->setMerge($y1, $x1,   $y1 + $kol_vo_fld_level - 1, $x1);
						$x1++;
				}
				
                $row_arr = $arr_head4[$i];
                $colspan = 1;
                for($j=0; $j<count($row_arr); $j++)
                {
                        if ($row_arr[$j] == @$row_arr[$j+1])
                        {
                                $colspan++;
                        }
                        else
                        {
                                $txt = $row_arr[$j];
                                $arr1 = explode('|',$txt);
                                $text_cell = $arr1[count($arr1)-1];

                                $rowspan_str = '';
                                for($k=1; $k<=50; $k++)
                                {
                                        if (@$arr_head4[$i+$k][$j] == $txt.str_repeat('|_', $k)) 
                                        {
												//$rowspan_str = ' rowspan="'.($k+1).'" ';
												$rowspan_str = $k+1;
                                        }
                                }
                                /**/                                        
                                if ($text_cell != '_')
								{
										////$res.= '<td class=td_1 '.$rowspan_str.' colspan="'.$colspan.'">'.$text_cell.'</td>'."\r\n";
										$sheet->write($y1, $x1, $text_cell, $frmt1_head_table);
										if ($colspan > 1)
										{
												$sheet->setMerge($y1, $x1,   $y1, $x1 + $colspan - 1);
												for($var1 = $x1+1; $var1 <= $x1 + $colspan - 1; $var1++)
												{
														$sheet->write($y1, $var1, $maket_table_title, $frmt1_head_table);			//для форматирования пустых ячеек
												}
										}
										$x1 = $x1 + $colspan - 1;
										if ($rowspan_str != '')
										{
												$sheet->setMerge($y1, $x1,   $y1 + $rowspan_str - 1, $x1);
												for($var1 = $y1+1; $var1 <= $y1 + $rowspan_str - 1; $var1++)
												{
														$sheet->write($var1, $x1, $maket_table_title, $frmt1_head_table);			//для форматирования пустых ячеек
												}
										}
								}
								$x1++;
                                $colspan = 1;
                        }
                }
                ////$res.= '</tr>'."\r\n";
				$x1 = 3;
				$y1++;
        }
		$x1 = 1;
		$sheet->write($y1, $x1, 'Код поля', $frmt1_head_table);			$x1++;
		$sheet->write($y1, $x1, '', $frmt1_head_table);					$x1++;
        foreach($arr_head3 as $k1 => $v1)
        {
				$sheet->write($y1, $x1, $v1, $frmt1_head_table);			$x1++;
        }
		$y1++;
		
        return array($x1, $y1);
}


function form_XLS_OneTable($xls, $sheet, $x1, $y1, $rez_arr, $maket_table_title_width_xls, $arr_field_width_xls)
{
		$frmt1 =& $xls-> addFormat();
		$frmt1-> setFontFamily('Verdana');
		//$frmt1-> setBold();
		$frmt1-> setSize('8');
		$frmt1-> setAlign('center');
		$frmt1-> setAlign('vcenter');
		$frmt1-> SetBorder('1');
		//$frmt1-> setTextWrap();
		//$frmt1-> setFgColor($xls_head_color1);
		

		$frmt2 =& $xls-> addFormat();
		$frmt2-> setFontFamily('Verdana');
		//$frmt2-> setBold();
		$frmt2-> setSize('8');
		$frmt2-> setAlign('center');
		$frmt2-> setAlign('vcenter');
		$frmt2-> SetBorder('1');
		//$frmt2-> setTextWrap();
		$frmt2-> setFgColor(22);
		
		
		
		//error_log( print_r( $arr_field_width_xls, true ) );
		$col = 3;
		foreach($arr_field_width_xls as $k1 => $v1)
		{
				if ($v1 != '' && $v1 != '0')	
				{
						$sheet->setColumn($col, $col, $v1);
				}
				$col++;
		}
		
		
		foreach($rez_arr as $k1 => $v1)
		{
				$sheet->write($y1, $x1, $rez_arr[$k1]['C1'], $frmt1);	$sheet->setColumn($x1,  $x1, $maket_table_title_width_xls);		$x1++;			
				$sheet->write($y1, $x1, $rez_arr[$k1]['C2'], $frmt1);	$sheet->setColumn($x1,  $x1,  4);								$x1++;			
				
				foreach($v1 as $k2 => $v2)
				{
						if ($k2 == 'C1' || $k2 == 'C2' || $k2 == 'C3')	Continue;
						
						$cell_text = $rez_arr[$k1][$k2][0];
						$read_only_flag = $rez_arr[$k1][$k2][1];
						
						$frmt = ($read_only_flag ? $frmt2 : $frmt1);
						
						$sheet->write($y1, $x1, htmlspecialchars_decode($cell_text), $frmt);
						//$sheet->setColumn($x1,  $x1,  15);
						$x1++;
				}
				$x1 = 1;
				$y1++;
		}
		$y1++;
		
		
		return array($x1, $y1);
}


?>
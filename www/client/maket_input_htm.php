<?php


function show_HTML_version(	$arr_maket_list, $arr_history_list, $arr_user_maket_id, 
							$dat1, $mkt1, $admin, $maket_kpp_name, $maket_code, $maket_name, $html_table_content,
							$save_rezult, $send_rezult, $notif, $maket_period, $visibility_send_button, $nbt_siz_menu_str)
{
?>
		<!DOCTYPE html>
		<html>
		<head>
			<title>АРМ Макет</title>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8;">
			<meta http-equiv="expires" content="0">
			<meta http-equiv="cashe-control" content="no-cashe">
			<?php   require_once 'style.css.php';    ?>
		</head>
		<body>
		<script>
		function showHistTable(phraze_id, maket_kpp)
		{
				var obj1, obj2;
				
				obj1 = document.getElementById('tr_hist_' + phraze_id);
				obj2 = document.getElementById('div_hist_' + phraze_id);
				
				//alert(obj1.style.display);
				if (obj1.style.display == '')
				{
					obj1.style.display='none';
				}
				else
				{ 
					obj1.style.display='';
				}
				
				
				req = new XMLHttpRequest();
				var url = "show_history.php?sl_edit_id=" + phraze_id + '&sl_maket_kpp=' + maket_kpp;
				
				req.open("GET", url, true);
				req.send(null);
					
				req.onreadystatechange = function ()
				{
						
						if (req.readyState == 4)
						{
								var result = req.responseText;
								obj2.innerHTML = result;
						}
				}
		}
		function checkKey(key, cell_in_row)
		{
				if (key == 'ArrowRight' || key == 'ArrowDown' || key == 'ArrowUp' || key == 'ArrowLeft')
				{
						//alert('u');

						//add all elements we want to include in our selection
						//var focussableElements = 'a:not([disabled]), button:not([disabled]), input[type=text]:not([disabled]), [tabindex]:not([disabled]):not([tabindex="-1"])';
						var focussableElements = 'input[type=text]:not([disabled]), [tabindex]:not([disabled]):not([tabindex="-1"])';
						if (document.activeElement && document.activeElement.form)
						{
								var focussable = Array.prototype.filter.call(
										document.activeElement.form.querySelectorAll(focussableElements),
										function (element)
										{
											//check for visibility while always include the current activeElement
											return (
												element.offsetWidth > 0 || element.offsetHeight > 0 || element === document.activeElement
											);
										}
								);
								var index = focussable.indexOf(document.activeElement);
								if (index > -1)
								{
										var nextElement;
										if (key == 'ArrowRight')    nextElement = focussable[index + 1]             || focussable[0];
										if (key == 'ArrowLeft')     nextElement = focussable[index - 1]             || focussable[0];
										if (key == 'ArrowDown')     nextElement = focussable[index + cell_in_row]   || focussable[0];
										if (key == 'ArrowUp')       nextElement = focussable[index - cell_in_row]   || focussable[0];
										nextElement.focus();
								}
						}
				}
		}
		</script>


		<?php
		$maket_list_str = '';
		$maket_id		= '';
		$maket_kpp		= '';

		foreach($arr_maket_list as $k1 => $v1)
		{
				$row1			= $k1;
				$img_str		= $arr_maket_list[$row1][1];
				$id				= $arr_maket_list[$row1][2];
				$kpp_id			= $arr_maket_list[$row1][3];
				$mkt1			= $arr_maket_list[$row1][4];
				$maket_id		= $arr_maket_list[$row1][5];
				$maket_kpp		= $arr_maket_list[$row1][6];
				$code			= $arr_maket_list[$row1][7];
				$kpp_short_title= $arr_maket_list[$row1][8];

				$maket_list_str.= '<tr style="text-align: center;">';
				$maket_list_str.= '<td class=td_2>'.$row1.') </td>';
				$maket_list_str.= '<td class=td_2>'.$img_str.'</td>';
				$maket_list_str.= '<td class=td_2><a class=a1 href="'.$_SERVER['PHP_SELF'].'?sl_maket_id='.$id.'&sl_maket_kpp='.$kpp_id.'&sl_dat1='.date('Y-m-d',$mkt1).'" style="'.($id == $maket_id && $kpp_id == $maket_kpp ? 'font-weight: bold; font-size: 16px;' : '').'">'.$code.'</a></td>';
				$maket_list_str.= '<td class=td_2>'.$kpp_short_title.'</td>';
				$maket_list_str.= '</tr>'."\r\n";
		}
		?>




		<table border="0" style="width: 100%;">
		<tr>
		<td style="vertical-align: top; width: 180px; text-align: center;">
			<div style="width: 180px;"></div>
		<?php
		$cnd1 = new Calendar1();
		$cnd1->par_dat1	= 'sl_maket_id='.$maket_id.'&sl_maket_kpp='.$maket_kpp.'&sl_dat1';
		list($d,$m,$y) = get_date_razdel($dat1);
		$cnd1->sel_y = $y;
		$cnd1->sel_m = $m;
		$cnd1->sel_d = $d;
		$cnd1->type = $maket_period;
		$cnd1->ShowCalendar();
		?>


		<p><a class=a1 href="<?php	echo $_SERVER['PHP_SELF'].'?sl_dat1='.$dat1;	?>">Главная страница</a></p>



		<table border=0 cellspacing=0 cellpadding=0 style="margin: auto;">
		<tr style="text-align: center;">
		<td class=td_2 style="width: 40px;">№</td>
		<td class=td_2></td>
		<td class=td_2>Макет</td>
		<td class=td_2>КПП</td>
		</tr>
		<?php
		echo $maket_list_str;
		?>
		</table>



		</td>
		<td style="vertical-align: top;">
			<div class="box_user">
				<p class=p3 style="margin: 0 0 0 0;">Пользователь: <?php    echo $_SESSION['USER_TITLE'];   ?></p>
				<?php   if ($admin == 1  ||  $admin == 2)   echo '<a class=a1 style="margin: 0 0 0 0;" href="maket_editor.php">Редактор</a> / '; ?>
				<a class=a1 style="margin: 0 0 0 0;" href="do_exit.php">Выход</a>
			</div>

		<?php


		if ($maket_id > 0)
		{
				if (!in_array($maket_id, $arr_user_maket_id))
				{
						echo '<table border=0 style="width: 100%; height: 100%;">'."\r\n";
						echo '<tr>'."\r\n";
						echo '<td style="text-align: center; vertical-align: middle;">'."\r\n";
						echo '  <p class=p1>Макета с ID = '.$maket_id.' не существует!</p>'."\r\n";
						echo '</td>'."\r\n";
						echo '</tr>'."\r\n";
						echo '</table>'."\r\n"."\r\n"."\r\n";
				}
				else
				{
						echo '<p class=p1>Макет: '.$maket_code.': '.$maket_name.' <a href="'.$_SERVER['PHP_SELF'].'?sl_maket_id='.$maket_id.'&sl_maket_kpp='.$maket_kpp.'&sl_dat1='.date('Y-m-d',$mkt1).'&sl_fmt_doc=1"><img src="img\excel_icon.png" border=0></a>'.'</p>'."\r\n";
						echo '<p class=p1>КПП: '.$maket_kpp_name.'</p>'."\r\n";


						echo '<form method=post action="'.$_SERVER['PHP_SELF'].'">'."\r\n";
						echo $html_table_content;
						if (isset($_POST['btn_save']) && !$save_rezult)				echo '<p class=p3_red>Ошибка при сохранении макета! Сообщите сопровождающим специалистам.</p>';
						if (isset($_POST['btn_save']) &&  $save_rezult)				echo '<p class=p3_green>'.date('Y-m-d H:i:s').': Макет успешно сохранен!</p>';
						if (isset($_POST['btn_send']) && !is_numeric($send_rezult))	echo '<p class=p3_red>Ошибка при отправке макета! Сообщите сопровождающим специалистам.</p>';
						if (isset($_POST['btn_send']) &&  is_numeric($send_rezult))	echo '<p class=p3_green>'.date('Y-m-d H:i:s').': Макет успешно отправлен!</p>';
						if (isset($_GET['sl_copy_maket']) && !$save_rezult)			echo '<p class=p3_red>Ошибка при копировании макета! Сообщите сопровождающим специалистам.</p>';
						if (isset($_GET['sl_copy_maket']) && $save_rezult)			echo '<p class=p3_green>'.date('Y-m-d H:i:s').': Макет успешно скопирован.</p>';



						if ($maket_id == 314)
						{
							echo '<p class=p3_green>Кнопка "Отправить" скрыта. Нажимать ее больше не нужно. Необходимо нажимать только кнопку "Сохранить".</p>';
						}

						echo '<center>';
						echo '<input type=hidden name="sl_maket_id"   value="'.$maket_id.'"           >'."\r\n";
						echo '<input type=hidden name="sl_maket_kpp"  value="'.$maket_kpp.'"          >'."\r\n";
						echo '<input type=hidden name="sl_dat1"       value="'.date('Y-m-d',$mkt1).'" >'."\r\n";
						echo '<input type=submit name=btn_save value="Сохранить" style="width: 150px;">'."\r\n";
						if ($visibility_send_button != '1')
						{
								echo '<input type=submit name=btn_send value="Отправить" style="width: 150px;">'."\r\n";
								echo '<input type=submit name=btn_inform value="Информировать" style="width: 150px;">'."\r\n";
						}
						//echo '<input type=submit name=btn_delete_data value="Удалить">'."\r\n";

						echo '<table style="margin: 30px 0 0 0;">'."\r\n";
						echo '<tr>'."\r\n";
						echo '<td>';
						if ($maket_period == 1)		echo '<a class=a1 href="'.$_SERVER['PHP_SELF'].'?sl_copy_maket=0&sl_maket_id='.$maket_id.'&sl_maket_kpp='.$maket_kpp.'&sl_dat1='.date('Y-m-d',$mkt1).'">Копировать на предыдущий месяц</a> '."\r\n";
						echo '</td><td style="width: 40px;"></td><td>';
						if ($maket_period == '')	echo '<a class=a1 href="'.$_SERVER['PHP_SELF'].'?sl_copy_maket=1&sl_maket_id='.$maket_id.'&sl_maket_kpp='.$maket_kpp.'&sl_dat1='.date('Y-m-d',$mkt1).'">Копировать на предыдущие сутки</a> '."\r\n";
						echo '</td><td style="width: 40px;"></td><td>';
						if ($maket_period == '')	echo '<a class=a1 href="'.$_SERVER['PHP_SELF'].'?sl_copy_maket=2&sl_maket_id='.$maket_id.'&sl_maket_kpp='.$maket_kpp.'&sl_dat1='.date('Y-m-d',$mkt1).'">Копировать на следующие сутки</a> '."\r\n";
						echo '</td><td style="width: 40px;"></td><td>';
						if ($maket_period == 1)		echo '<a class=a1 href="'.$_SERVER['PHP_SELF'].'?sl_copy_maket=3&sl_maket_id='.$maket_id.'&sl_maket_kpp='.$maket_kpp.'&sl_dat1='.date('Y-m-d',$mkt1).'">Копировать на следующий месяц</a>'."\r\n";
						echo '</td>';
						echo '</tr>'."\r\n";
						echo '</table>'."\r\n";


						if ($maket_period == 1  &&  $maket_id == 314)
						{
								echo '<table style="margin: 10px 0 0 0;">'."\r\n";
								echo '<tr>'."\r\n";
								echo '<td>';
								echo '<a class=a1 href="'.$_SERVER['PHP_SELF'].'?sl_copy_maket=4&sl_maket_id='.$maket_id.'&sl_maket_kpp='.$maket_kpp.'&sl_dat1='.date('Y-m-d',$mkt1).'">Копировать на следующий месяц без факта</a>'."\r\n";
								echo '</td>';
								echo '</tr>'."\r\n";
								echo '</table>'."\r\n";
						}
						echo '</center>'."\r\n";
						echo '</form>'."\r\n";



						if (count($arr_history_list) > 0)
						{
								echo '<p class=p1 style="margin: 50px 0 10px 0;">История действий с макетом:</p>'."\r\n";
								echo '<table class=tbl_1 style="margin: 0 0 0 0;">'."\r\n";
								echo '<tr style="text-align: center; font-weight: bold;">'."\r\n";
								echo '<td class=td_1></td>'."\r\n";
								echo '<td class=td_1>Пользователь</td>'."\r\n";
								echo '<td class=td_1>Действие</td>'."\r\n";
								echo '<td class=td_1>Время</td>'."\r\n";
								echo '</tr>'."\r\n";

								foreach($arr_history_list as $k1 => $v1)
								{
										$user_name	= $arr_history_list[$k1][1];
										$action		= $arr_history_list[$k1][2];
										$time_ins	= $arr_history_list[$k1][3];

										echo '<tr style="text-align: center;">'."\r\n";
										echo '<td class=td_1>'.$k1.'</td>'."\r\n";
										echo '<td class=td_1>'.$user_name.'</td>'."\r\n";
										echo '<td class=td_1>';
										if ($action == '1')	echo 'Сохранение';
										if ($action == '2')	echo 'Отправка';
										if ($action == '3')	echo 'Копирование';
										echo '</td>'."\r\n";
										echo '<td class=td_1>'.$time_ins.'</td>'."\r\n";
										echo '</tr>'."\r\n";
								}
								echo '</table>'."\r\n";
						}
						else
						{
								echo '<p class=p1 style="margin: 50px 0 0 0;">Действия с макетом не осуществлялись.</p>'."\r\n";
						}
				}
		}
		else
		{
				//если пользователь имеет доступ к отчетам СИЗ		
				if (in_array(314, $arr_user_maket_id))
				{
						echo '<p class=p1>Отчеты НБТ:</p>'."\r\n";
						//echo '<a class=a1 href="http://esrr-skull.esrr.oao.rzd/arm_maket/download_file.php?sl_id_doc=1&sl_dat1='.date('Y-m-01', $mkt1).'">Отчеты по СИЗ</a> '."\r\n";
						//echo '<font class=p3 style="text-align: left;">(Документ пересчитывается 3 раза в день. Примерное время готовности - 5:30, 8:00 и 10:30 МСК)</font>'."\r\n";
						
						//if ($_SERVER['REMOTE_ADDR'] == '10.110.2.105')
						//{
								echo '<p class=p3>Для открытия отчета по службе нажмите ссылку в столбце "Службы".</p>'."\r\n";
								echo '<p class=p3>Для открытия отчета по предприятию нажмите ссылку в столбце "Предприятие".</p>'."\r\n";
								echo '<p class=p3>Отчеты формируются в режиме online (без ожидания расчетов на сервере).</p>'."\r\n";
								echo $nbt_siz_menu_str;
						//}
				}

				//если пользователь имеет доступ к отчетам ДЦНТИБ
				if (in_array(317, $arr_user_maket_id))
				{
						echo '<p class=p1 style="">Отчеты ДЦНТИБ:</p>';
						//echo '<a class=a1 href="http://esrr-skull.esrr.oao.rzd/arm_maket/download_file.php?sl_id_doc=2&sl_dat1='.date('Y-m-01', $mkt1).'">Отчет НТУ-12</a>';
						echo '<a class=a1 href="http://esrr-skull.esrr.oao.rzd/arm_maket/xls_dcntib_ntu12.php?sl_dat1='.date('Y-m-01', $mkt1).'">Отчет НТУ-12</a><br>';
						//echo '<font class=p3 style="text-align: left;">(Документ пересчитывается 1 раз в час в рабочее время)</font><br>';
						echo '<a class=a1 href="http://esrr-skull.esrr.oao.rzd/arm_maket/xls_dcntib_2.php?sl_fmt_doc=1&sl_dat1='.date('Y-m-01', $mkt1).'">Отчет НТУ-18</a>';
						
						/*
						if ($_SERVER['REMOTE_ADDR'] == '10.110.2.105')
						{
								echo $dcntib_ntu12_menu_str;
						}
						*/
				}
				
				//если пользователь имеет доступ к отчетам АГО-12, 13
				if (in_array(327, $arr_user_maket_id) || in_array(316, $arr_user_maket_id))
				{
						echo '<p class=p1>Отчеты:</p>'."\r\n";
				}
				if (in_array(327, $arr_user_maket_id))
				{
						echo '<a class=a1 href="http://esrr-skull.esrr.oao.rzd/arm_maket/xls_nbt_ago12.php?sl_dat1='.date('Y-m-01', $mkt1).'">Отчет АГО-12</a><br>';
				}
				if (in_array(316, $arr_user_maket_id))
				{
						echo '<a class=a1 href="http://esrr-skull.esrr.oao.rzd/arm_maket/xls_nbt_ago13.php?sl_dat1='.date('Y-m-01', $mkt1).'">Отчет АГО-13</a><br>';
				}
				

				echo '<p><a class=a1 href="instr/instr_input_data1.docx">Инструкция по работе с АРМ Макет</a></p>';

				echo '<p class=p3 style="text-align: left;">Для связи с сопровождающими технологами (консультации, добавление прав) используйте ссылку ';
				echo '<a class=a1 href="'.$_SERVER['PHP_SELF'].'?sl_dat1='.$dat1.'&sl_make_zapros=1">Создать обращение в ЕСПП</a></p>';
				if ($notif != '')	echo '<p class=p3_green style="text-align: left;">'.$notif.'</p>';


				if ($maket_id === 0)	echo '<p class=p1 style="padding-top: 300px; width: 100%; text-align: center;">Выберите макет в списке слева.</p>'."\r\n";
				if ($maket_id === '')
				{
						echo '<p class=p1 style="padding-top: 300px; width: 100%; text-align: center;">Уважаемый пользователь! Вам еще не назначены отчеты. '
							. '	Чтобы получить доступ к отчетам, напишите сообщение администраторам системы по электронной почте, используя эту '
							. '<a href="mailto:ivc_HalitovDP@esrr.rzd; ivc_ZayacPA@esrr.rzd ?subject=Обращение по АРМ Макеты&body=Добрый день! '
							. 'Прошу в АРМ Макеты предоставить доступ к отчету (укажите наименование отчета) по предприятию '
							. '(укажите наименование предприятия, станции и т.д.).">ССЫЛКУ</a>.</p>'."\r\n";
				}
		}
		?>
		</td>
		</tr>
		</table>



		</body>
		</html>
<?php
}



function form_HTML_table_head($arr_head1, $arr_head3, $arr_head4, $maket_table_title, $kol_vo_fld_level, $maket_table_title_width, $maket_table_code_width)
{

        $res = ''."\r\n";
        for($i=0; $i<$kol_vo_fld_level; $i++)
        {
                $res.= '<tr style="background-color: #CDCDCD;">'."\r\n";
                if ($i == 0)	$res.= '<td class=td_1 rowspan='.$kol_vo_fld_level.' style="width: '.$maket_table_title_width.'px;">'.$maket_table_title.'</td>';
                if ($i == 0)	$res.= '<td class=td_1 rowspan='.$kol_vo_fld_level.' style="width: '.$maket_table_code_width.'px;">Код фразы</td>';

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
                                            $rowspan_str = ' rowspan="'.($k+1).'" ';
                                        }
                                }
                                /**/                                        
                                if ($text_cell != '_')	$res.= '<td class=td_1 '.$rowspan_str.' colspan="'.$colspan.'">'.$text_cell.'</td>'."\r\n";
                                $colspan = 1;
                        }
                }
                $res.= '</tr>'."\r\n";
        }
        $res.= '<tr style="background-color: #CDCDCD;">'."\r\n";
        $res.= '<td class=td_1 >Код поля</td>';
        $res.= '<td class=td_1 ></td>';
        foreach($arr_head3 as $k1 => $v1)
        {
                $res.= '<td class=td_1>'.$v1.'<div style="width: '.$arr_head1[$k1].'px;"></div></td>'."\r\n";
        }
        $res.= '</tr>'."\r\n";    

        return $res;
}



function form_HTML_OneTable($HTML_table_head_str, $maket_id, $rez_arr, $fields_count, $maket_kpp)
{
		$rez_str = '<table  class=tbl_1 style="text-align: center;">'."\r\n";
		$rez_str.= $HTML_table_head_str;
		
		foreach($rez_arr as $k1 => $v1)
		{
				$rez_str.= '<tr>'."\r\n";
				$rez_str.= '<td style="background-color: #CDCDCD;" class=td_1>'.$rez_arr[$k1]['C1'].'</td>'."\r\n";
				$rez_str.= '<td style="background-color: #CDCDCD; '.($maket_id == 314 ? ' cursor: pointer;' : '').'" class=td_1 '.($maket_id == 314 ? ' onclick="showHistTable('.$rez_arr[$k1]['C3'].', '.$maket_kpp.');" ': '').'>';
				$rez_str.= $rez_arr[$k1]['C2'];
				$rez_str.= '</td>'."\r\n";
				foreach($v1 as $k2 => $v2)
				{
						if ($k2 == 'C1' || $k2 == 'C2' || $k2 == 'C3')	Continue;

						$cell_text = $rez_arr[$k1][$k2][0];
						$read_only_flag = $rez_arr[$k1][$k2][1];
						$phraze_code = $rez_arr[$k1][$k2][2];
						$field_code = $rez_arr[$k1][$k2][3];
						$field_width = $rez_arr[$k1][$k2][4];
						
						
						$rez_str.= '<td class=td_1 '.($read_only_flag ? ' style="background-color: #CDCDCD;" ' : '').'>'."\r\n";
						$rez_str.= '<input id="inp_'.$phraze_code.'_'.$field_code.'" name="inp_'.$k1.'_'.$k2.'" type=text value="'.$cell_text.'" style="width: '.$field_width.'px; border-width: 0px; text-align: center; font-size: 12px;'.($read_only_flag ? ' background-color: #CDCDCD; ' : '').'"  onClick="this.select();" '.($read_only_flag ? ' readonly ' : '').' autocomplete="off" onkeydown="checkKey(event.key, '.$fields_count.')" >'."\r\n";
						$rez_str.= '</td>'."\r\n";
				}
				$rez_str.= '</tr>'."\r\n";
				if ($maket_id == 314)	$rez_str.= '<tr id="tr_hist_'.$rez_arr[$k1]['C3'].'" style="display: none;"><td colspan=10><div id="div_hist_'.$rez_arr[$k1]['C3'].'"></div></td></tr>'."\r\n";
		}
		$rez_str.= '</table>'."\r\n";
		$rez_str.= '<br><br>';

		return $rez_str;
}





?>
<?php
	/*
		класс управления календарем
	*/
	class Calendar1
	{
		// We can redeclare the public and protected method, but not private
		
		var $beg_date		= '';
		var $calend_width	= 145;
		var $calend_height	= 90;
		var $height_bottom_row = 100;
		var $head_width		= 165;
		var $head_height	= 15;
		var $par_dat1		= 'sl_dat1';
		var $par_dat2		= 'sl_dat1';
		var $sel_y		= '';
		var $sel_m		= '';
		var $sel_d		= '';
		var $type		= 1;			//0 - ежедневный, 1 - месячный, 2 - годовой
		private $style1 = ' font-family: Arial;  font-size: 11px; color: white;  font-weight: bold; text-decoration: none; ';
		private $style2 = ' font-family: Tahoma; font-size: 11px; color: white;  font-weight: bold; text-decoration: none; ';
		private $style3 = ' font-family: Tahoma; font-size: 9px;  color:#003E69;                    text-decoration: none; '; //цвет чисел тек месяца
		private $style4 = ' font-family: Tahoma; font-size: 9px;  color:#A4A4A4;                    text-decoration: none; '; //цвет чисел др месяца
		private $style5 = ' font-family: Tahoma; font-size: 9px;  color:  white; font-weight: bold; text-decoration: none; '; //цвет выбран чисел
		
		var $tbl_border_style1 = ' ';
		var $td_border_style1  = ' ';
		var $frm_hidden_name   = 'sl_dat1';
		
		
		public function ShowCalendar()
		{
			if ($this->sel_d == '') $this->sel_d = date('j',mktime()-24*60*60);
			if ($this->sel_m == '') $this->sel_m = date('n');
			if ($this->sel_y == '') $this->sel_y = date('Y');
			
			$sel_d2 = $this->sel_d;
			$sel_m2 = $this->sel_m;
			$sel_y2 = $this->sel_y;
			if (strlen($this->sel_d) == 1) $sel_d2 = '0'.$this->sel_d;
			if (strlen($this->sel_m) == 1) $sel_m2 = '0'.$this->sel_m;
			if (strlen($this->sel_y) == 1) $sel_y2 = '000'.$this->sel_y;
			if (strlen($this->sel_y) == 2) $sel_y2 = '00'.$this->sel_y;
			if (strlen($this->sel_y) == 3) $sel_y2 = '0'.$this->sel_y;
			
			if ($this->CheckDTime($sel_y2.'-'.$sel_m2.'-'.$sel_d2) == -1)
			{
				$this->sel_d = date('j');
				$this->sel_m = date('n');
				$this->sel_y = date('Y');
			}
			//echo $this->sel_y.'-'.$this->sel_m.'-'.$this->sel_d;
			
			$mt1	= mktime(0,0,0,$this->sel_m-1,$this->sel_d,$this->sel_y);
			$mt2	= mktime(0,0,0,$this->sel_m+1,$this->sel_d,$this->sel_y);
			
			$mt_1	= mktime(0,0,0,$this->sel_m,$this->sel_d,$this->sel_y-1);
			$mt_2	= mktime(0,0,0,$this->sel_m,$this->sel_d,$this->sel_y+1);
			
			
			echo '<table border=0 cellpadding=0 cellspacing=0 '.$this->tbl_border_style1.'>'."\n";
			echo '<tr>'."\n";
			echo '	<td '.$this->td_border_style1.' style="text-align: center; height: '.$this->head_height.'px; width: '.$this->head_width.'px; background-color: #3466AD;">'."\n";
			echo '			<table style="width: 165px; height: 30px; border: 0px; margin: 0 0 0 3px;" cellpadding=0 cellspacing=0>'."\n";
			echo '			<tr style="vertical-align: middle;">'."\n";
			echo '							<td style="background-color: #3466AD; text-align: center; vertical-align: top; width: 8px;			">		<a href="?'.$this->par_dat1.'='.date('Y-m-d',$mt_1).'" style="'.$this->style1.'" title="Предыдущий год">	<img style="border: 0; width: 17px; height: 16px; margin: 7px 0 0 0;" src="img/prev_year.bmp" alt="" > </a>	</td>'."\n";
			if ($this->type != 2)	echo '	<td style="background-color: #3466AD; text-align: right;  vertical-align: top; width: 15px;			">		<a href="?'.$this->par_dat1.'='.date('Y-m-d',$mt1).'"  style="'.$this->style1.'" title="Предыдущий месяц">	<img style="border: 0; width: 10px; height: 16px; margin: 7px 0 0 0;" src="img/prev_month.bmp" alt="" > </a></td>'."\n";
			echo '							<td style="text-align: center; vertical-align: middle;	'.$this->style2.'	">	'.($this->type != 2 ? $this->getRusMonth((integer) $this->sel_m).', ' : '').' '.$this->sel_y.'</td>'."\n";
			if ($this->type != 2)	echo '	<td style="background-color: #3466AD; text-align: left;   vertical-align: top; width: 15px;			">		<a href="?'.$this->par_dat1.'='.date('Y-m-d',$mt2).'"  style="'.$this->style1.'" title="Следующий месяц">	<img style="border: 0; width: 10px; height: 16px; margin: 7px 0 0 0;" src="img/next_month.bmp" alt="" > </a></td>'."\n";
			echo '							<td style="background-color: #3466AD; text-align: center; vertical-align: top; width: 25px;			">		<a href="?'.$this->par_dat1.'='.date('Y-m-d',$mt_2).'" style="'.$this->style1.'" title="Следующий год">		<img style="border: 0; width: 17px; height: 16px; margin: 7px 0 0 0;" src="img/next_year.bmp" alt="" > </a>	</td>'."\n";
			echo '			</tr>'."\n";
			echo '			</table>'."\n";
			echo '	</td>'."\n";
			echo '</tr>'."\n";
			echo '<tr>'."\n";
			echo '	<td style="text-align: center; vertical-align: middle; height: '.$this->height_bottom_row.'px;" '.$this->td_border_style1.'>'."\n";
			if ($this->type == 2)
			{
					echo '			<table style="'.$this->style3.'; width: '.$this->calend_width.'px; height: '.$this->calend_height.'px; margin: 0 auto;" cellpadding=0 cellspacing=0 '.$this->tbl_border_style1.'>'."\n";
					echo '			<tr align=center style="font-weight: bold">'."\n";
					echo '				<td><div style="font-family: Tahoma; font-size: 14px; color: #BE123C;  font-weight: bold; text-decoration: none;">'.$this->sel_y.' год</div></td>'."\n";
					echo '			</tr>'."\n";
					echo '			</table>'."\n";
			}
			if ($this->type == 1)
			{
					echo '			<table style="'.$this->style3.'; width: '.$this->calend_width.'px; height: '.$this->calend_height.'px; margin: 0 auto;" cellpadding=0 cellspacing=0 '.$this->tbl_border_style1.'>'."\n";
					echo '			<tr align=center style="font-weight: bold">'."\n";
					echo '				<td><div style="font-family: Tahoma; font-size: 14px; color: #BE123C;  font-weight: bold; text-decoration: none;">'.$this->getRusMonth((integer) $this->sel_m).', '.$this->sel_y.'</div></td>'."\n";
					echo '			</tr>'."\n";
					echo '			</table>'."\n";
			}
			if ($this->type == 0)
			{
					echo '			<table style="'.$this->style3.'; width: '.$this->calend_width.'px; height: '.$this->calend_height.'px; margin: 0 auto;" cellpadding=0 cellspacing=0 '.$this->tbl_border_style1.'>'."\n";
					echo '			<tr align=center style="font-weight: bold">'."\n";
					echo '				<td '.$this->td_border_style1.'>Пн</td>'."\n";
					echo '				<td '.$this->td_border_style1.'>Вт</td>'."\n";
					echo '				<td '.$this->td_border_style1.'>Ср</td>'."\n";
					echo '				<td '.$this->td_border_style1.'>Чт</td>'."\n";
					echo '				<td '.$this->td_border_style1.'>Пт</td>'."\n";
					echo '				<td '.$this->td_border_style1.' style="color: #cc0000;">Сб</td>'."\n";
					echo '				<td '.$this->td_border_style1.' style="color: #cc0000;">Вс</td>'."\n";
					echo '			<tr align=center style="font-weight: bold">'."\n";
					echo '				<td '.$this->td_border_style1.' style="height: 1px; background-color: #3466AD;" colspan=7> ';
					echo '						<input type=hidden name='.$this->frm_hidden_name.' value="'.$sel_y2.'-'.$sel_m2.'-'.$sel_d2.'">';
					echo '				</td>'."\n";
					echo '			</tr>'."\n";

					$mkt1 = mktime(0,0,0,$this->sel_m,        1,$this->sel_y);	//первый день месяца
					$mkt2 = mktime(0,0,0,$this->sel_m,date('t'),$this->sel_y);	//последний день месяца
					$w = date('w',$mkt1);										//Порядковый номер дня недели От 0 (воскресенье) до 6 (суббота) 
					if ($w == 1)				$mkt1 = $mkt1 -      7*24*60*60;
					if ($w == 0)				$mkt1 = $mkt1 -      6*24*60*60;
					if (($w != 0) && ($w != 1))	$mkt1 = $mkt1 - ($w-1)*24*60*60;
					$kol_ned = 6;

					for ($i=0; $i<7*$kol_ned; $i++)
					{
							//			mktime(0, 0, 0, date("m"), date("d")+1, date("Y"))	-	специально для доступа к завтрашним суткам

							$print_chislo = date('j', $mkt1);
							$stl = ' style="'.$this->style3.'" ';
							if (date('m',$mkt1) != $this->sel_m)                                                                             { $dr_mes			= true;	$stl = ' style="'.$this->style4.'" ';	} else { $dr_mes		= false;	}
							if ((date('Y',$mkt1) ==    date('Y')) && (date('m',$mkt1) ==    date('m')) && (date('d',$mkt1) ==    date('d'))) { $segodn_chislo	= true;											} else { $segodn_chislo	= false;	}
							if ((date('Y',$mkt1) == $this->sel_y) && (date('n',$mkt1) == $this->sel_m) && (date('j',$mkt1) == $this->sel_d)) { $sel_chislo		= true;	$stl = ' style="'.$this->style5.'" ';	} else { $sel_chislo	= false;	}
							if ($mkt1 > mktime(0, 0, 0, date("m"), date("d")+1, date("Y")) ) $stl = ' style="'.$this->style4.'" ';


							if (date('w',$mkt1) == 1) echo '<tr align=center>'."\n";
							echo '<td '.$this->td_border_style1.' '.$stl.'>';

							if ($sel_chislo)    echo '<table style="width: 100%; height: 100%; border: none;									" cellpadding=0 cellspacing=0 '.$this->tbl_border_style1.' >	<tr><td style="text-align: center; background-color: red; font-size: 9px;"	'.$this->td_border_style1.'>';
							if ($segodn_chislo) echo '<table style="width: 100%; height: 100%; border-collapse: collapse; border: red 1px solid;" cellpadding=0 cellspacing=0 '.$this->tbl_border_style1.' >	<tr><td style="text-align: center;"											'.$this->td_border_style1.'>';

												echo '<a href="?'.$this->par_dat1.'='.date('Y-m-d',$mkt1).'" '.$stl.' >'.$print_chislo.'</a>';

							if ($segodn_chislo) echo '</td></tr></table>'."\n";
							if ($sel_chislo)    echo '</td></tr></table>'."\n";

							echo '</td>'."\n";
							if (date('w',$mkt1) == 0) echo '</tr>'."\n";

							//$mkt1 = $mkt1 + 24*60*60;
							$mkt1 = mktime(0, 0, 0, date("m",$mkt1), date("d",$mkt1)+1, date("Y",$mkt1));
					}
					echo '			</table>'."\n";
			}
			echo '	</td>'."\n";
			echo '</tr>'."\n";
			echo '</table>'."\n";
		}
		
		
		private function getRusMonth($m)
		{
                        if ($m == 1)  return 'Январь';
                        if ($m == 2)  return 'Февраль';
                        if ($m == 3)  return 'Март';
                        if ($m == 4)  return 'Апрель';
                        if ($m == 5)  return 'Май';
                        if ($m == 6)  return 'Июнь';
                        if ($m == 7)  return 'Июль';
                        if ($m == 8)  return 'Август';
                        if ($m == 9)  return 'Сентябрь';
                        if ($m == 10) return 'Октябрь';
                        if ($m == 11) return 'Ноябрь';
                        if ($m == 12) return 'Декабрь';
				
			return 'Январь';
		}
		
		private function CheckDTime($DTime)//2009-15-22
		{
                        if (preg_match("|^([0-9]{4})-([0-9]{2})-([0-9]{2})$|i",$DTime,$regs))
                        {
                                $y = (integer) $regs[1];
                                $m = (integer) $regs[2];
                                $d = (integer) $regs[3];
                                if (!checkdate($m,$d,$y)) return -1;
                                return 0;
                        }
                        else
                        { 
                                return -1;                             
                        }
		}
	}
?>
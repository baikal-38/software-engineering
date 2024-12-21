<?php

class MaketManager extends ARMMaketObjects
{
    private $link1;
    
    public $tbl_points;
    public $tbl_point_types;
    public $tbl_makets;
    public $tbl_user_rights;
    public $tbl_maket_data;
    public $tbl_maket_data_arh;
    public $tbl_makets_tables;
    public $tbl_makets_groups;
    public $tbl_phrases;
    public $tbl_fields;
    public $tbl_log_user_action;
    public $mkt1;
    
    
    
    
    // конструктор для соединения с базой данных
    public function __construct($link1)
    {
        $this->link1 = $link1;
    }
    
    
    //создание нового макета
    function CreateNewMaket()
    {
            $result1 = $this->link1->query('SELECT MAX(`MOCKUP_CODE`) as `val` FROM '.$this->tbl_makets.'');
            while ($row_res1 = $result1->fetch_assoc())
            {
                    $new_maket_code = ((integer) $row_res1['val'])+1;
            }

            $result1 = $this->link1->query('SELECT MIN(`POINT_TYPE_ID`) as `val` FROM '.$this->tbl_point_types.'');
            while ($row_res1 = $result1->fetch_assoc())
            {
                    $point_type_id = ((integer) $row_res1['val']);
            }

            $sql = 'INSERT INTO '.$this->tbl_makets.' (
                                                    `MOCKUP_CODE`,`MOCKUP_TITLE`,`MOCKUP_KEY`,`KPP_TYPE_ID`,`MOCKUP_ORDER_NO`,`MOCKUP_START`,`MOCKUP_PH_SEP`,`MOCKUP_END`
                                                )
                                           VALUES ("'.$new_maket_code.'","Новый макет","@код @кпп @дд@мм","'.$point_type_id.'","'.$new_maket_code.'","(:",":",")")
                                    ';
            if (!$this->link1->query($sql))
            {
                    $save_err_msg = 'Ошибка: '.$sql.'  '.$this->link1->error; 
                    error_log($save_err_msg);
            }
    }
    
    function CreateFields($m_table_id)
    {
            $result1 = $this->link1->query('SELECT MAX(`FIELD_ORDER_NO`) AS `val` FROM '.$this->tbl_fields.' WHERE `MOCKUP_TABLE_ID`="'.$m_table_id.'"');
            while ($row_res1 = $result1->fetch_assoc())
            {
                    $field_order_no = ((integer) $row_res1['val']) + 1;
            }

            $sql_arr[] = 'INSERT INTO '.$this->tbl_fields.' (`MOCKUP_TABLE_ID`,`FIELD_CODE`,`FIELD_ORDER_NO`,`FIELD_TITLE`,`FIELD_COORD`,`FIELD_REQUIRED`,`FIELD_WIDTH_WEB`) VALUES '
                            . '("'.$m_table_id.'","'.$field_order_no.'","'.$field_order_no.'","Новое поле","F","F","60")';
            
            return ExecuteSQLArray($this->link1, $sql_arr, true);
    }

    function CreateFrazes($coord_gr_id, $m_table_id)
    {
            $result1 = $this->link1->query('SELECT MAX(`PH_ORDER_NO`) AS `val` FROM '.$this->tbl_phrases.' WHERE `MOCKUP_TABLE_ID`="'.$m_table_id.'" AND `MOCKUP_GROUP_ID`="'.$coord_gr_id.'"');
            while ($row_res1 = $result1->fetch_assoc())
            {
                    $ph_order_no = ((integer) $row_res1['val']) + 1;
            }

            $sql_arr[] = 'INSERT INTO '.$this->tbl_phrases.' (`MOCKUP_TABLE_ID`,`MOCKUP_GROUP_ID`,`PH_CODE`,`PH_KEY`,`PH_ORDER_NO`,`PH_TITLE`) VALUES '
                                    . '("'.$m_table_id.'","'.$coord_gr_id.'","'.$ph_order_no.'","'.$ph_order_no.'","'.$ph_order_no.'","Новая фраза")';
            
            return ExecuteSQLArray($this->link1, $sql_arr, true);
    }

    function CreateCoordGR($maket_id)
    {
            $sql_arr[] = 'INSERT INTO '.$this->tbl_makets_groups.' (`MOCKUP_ID`) VALUES ("'.$maket_id.'")';
            
            return ExecuteSQLArray($this->link1, $sql_arr, true);
    }

    function CreateTables($maket_id)
    {
            $sql_arr[] = 'INSERT INTO '.$this->tbl_makets_tables.' (`MOCKUP_ID`) VALUES ("'.$maket_id.'")';
            
            return ExecuteSQLArray($this->link1, $sql_arr, true);
    }
    
    function DeleteFields($field_id)
    {
        $sql_arr[] = 'DELETE FROM '.$this->tbl_maket_data.'   WHERE `FIELD_ID`="'.$field_id.'"';
        $sql_arr[] = 'DELETE FROM '.$this->tbl_fields.'       WHERE `FIELD_ID`="'.$field_id.'"';
        
        return ExecuteSQLArray($this->link1, $sql_arr, true);
    }

    function DeleteFrazes($fraze_id)
    {
        $sql_arr[] = 'DELETE FROM '.$this->tbl_maket_data.'   WHERE `PH_ID`="'.$fraze_id.'"';
        $sql_arr[] = 'DELETE FROM '.$this->tbl_phrases.'      WHERE `PH_ID`="'.$fraze_id.'"';
        
        return ExecuteSQLArray($this->link1, $sql_arr, true);
    }

    function DeleteCoordGR($coord_gr_id)
    {
        $sql_arr[] = 'DELETE FROM '.$this->tbl_phrases.'       WHERE `MOCKUP_GROUP_ID`="'.$coord_gr_id.'"';
        $sql_arr[] = 'DELETE FROM '.$this->tbl_makets_groups.' WHERE `MOCKUP_GROUP_ID`="'.$coord_gr_id.'"';
        
        return ExecuteSQLArray($this->link1, $sql_arr, true);
    }

    function DeleteTables($table_id)
    {
        $sql_arr[] = 'DELETE FROM '.$this->tbl_fields.'        WHERE `MOCKUP_TABLE_ID`="'.$table_id.'"';
        $sql_arr[] = 'DELETE FROM '.$this->tbl_phrases.'       WHERE `MOCKUP_TABLE_ID`="'.$table_id.'"';
        $sql_arr[] = 'DELETE FROM '.$this->tbl_makets_tables.' WHERE `MOCKUP_TABLE_ID`="'.$table_id.'"';
        
        return ExecuteSQLArray($this->link1, $sql_arr, true);
    }
    
    function SaveMaket($maket_id, $field_name, $obj)
    {
            $this->SaveObject($this->link1, $this->tbl_makets, $maket_id, $field_name, $obj);
    }
    
    function SaveFields($field_id, $field_name, $obj)
    {
        $this->SaveObject($this->link1, $this->tbl_fields, $field_id, $field_name, $obj);
    }

    function SaveFrazes($fraze_id, $field_name, $obj)
    {
        $this->SaveObject($this->link1, $this->tbl_phrases, $fraze_id, $field_name, $obj);
    }

    function SaveCoordGR($coord_gr_id, $field_name, $obj)
    {
        $this->SaveObject($this->link1, $this->tbl_makets_groups, $coord_gr_id, $field_name, $obj);
    }

    function SaveTables($table_id, $field_name, $obj)
    {
        $this->SaveObject($this->link1, $this->tbl_makets_tables, $table_id, $field_name, $obj);
    }
    
    
    
    
    //удаление макета
    function DeleteMaket($maket_id)
    {
            $sql_arr = array();
            
            $sql_arr[] = 'LOCK TABLES '.    $this->tbl_makets			.' WRITE, '.
                                            $this->tbl_maket_data       .' WRITE, '.
											$this->tbl_maket_data_arh   .' WRITE, '.
                                            $this->tbl_makets_groups	.' WRITE, '.
                                            $this->tbl_makets_tables	.' WRITE, '.
                                            $this->tbl_fields			.' WRITE, '.
                                            $this->tbl_phrases          .' WRITE, '.
                                            $this->tbl_log_user_action  .' WRITE, '.
                                            $this->tbl_user_rights      .' WRITE ';
            
            
            $sql_arr[] = 'DELETE FROM '.$this->tbl_maket_data.'		WHERE `PH_ID`     IN (SELECT `PH_ID`    FROM '.$this->tbl_phrases.'  WHERE `MOCKUP_GROUP_ID` IN (SELECT `MOCKUP_GROUP_ID` FROM '.$this->tbl_makets_groups.' WHERE `MOCKUP_ID`="'.$maket_id.'"))';
            $sql_arr[] = 'DELETE FROM '.$this->tbl_maket_data.'		WHERE `FIELD_ID`  IN (SELECT `FIELD_ID` FROM '.$this->tbl_fields.'   WHERE `MOCKUP_TABLE_ID` IN (SELECT `MOCKUP_TABLE_ID` FROM '.$this->tbl_makets_tables.' WHERE `MOCKUP_ID`="'.$maket_id.'"))';
            $sql_arr[] = 'DELETE FROM '.$this->tbl_maket_data.'		WHERE `PH_ID`     IN (SELECT `PH_ID`    FROM '.$this->tbl_phrases.'  WHERE `MOCKUP_TABLE_ID` IN (SELECT `MOCKUP_TABLE_ID` FROM '.$this->tbl_makets_tables.' WHERE `MOCKUP_ID`="'.$maket_id.'"))';
			
            $sql_arr[] = 'DELETE FROM '.$this->tbl_maket_data_arh.'	WHERE `PH_ID`     IN (SELECT `PH_ID`    FROM '.$this->tbl_phrases.'  WHERE `MOCKUP_GROUP_ID` IN (SELECT `MOCKUP_GROUP_ID` FROM '.$this->tbl_makets_groups.' WHERE `MOCKUP_ID`="'.$maket_id.'"))';
            $sql_arr[] = 'DELETE FROM '.$this->tbl_maket_data_arh.'	WHERE `FIELD_ID`  IN (SELECT `FIELD_ID` FROM '.$this->tbl_fields.'   WHERE `MOCKUP_TABLE_ID` IN (SELECT `MOCKUP_TABLE_ID` FROM '.$this->tbl_makets_tables.' WHERE `MOCKUP_ID`="'.$maket_id.'"))';
            $sql_arr[] = 'DELETE FROM '.$this->tbl_maket_data_arh.'	WHERE `PH_ID`     IN (SELECT `PH_ID`    FROM '.$this->tbl_phrases.'  WHERE `MOCKUP_TABLE_ID` IN (SELECT `MOCKUP_TABLE_ID` FROM '.$this->tbl_makets_tables.' WHERE `MOCKUP_ID`="'.$maket_id.'"))';
            
            
            $sql_arr[] = '																	DELETE FROM         '.$this->tbl_phrases.'    WHERE `MOCKUP_GROUP_ID` IN (SELECT `MOCKUP_GROUP_ID` FROM '.$this->tbl_makets_groups.' WHERE `MOCKUP_ID`="'.$maket_id.'")';
            $sql_arr[] = '																	DELETE FROM         '.$this->tbl_fields.'     WHERE `MOCKUP_TABLE_ID` IN (SELECT `MOCKUP_TABLE_ID` FROM '.$this->tbl_makets_tables.' WHERE `MOCKUP_ID`="'.$maket_id.'")';
            $sql_arr[] = '																	DELETE FROM         '.$this->tbl_phrases.'    WHERE `MOCKUP_TABLE_ID` IN (SELECT `MOCKUP_TABLE_ID` FROM '.$this->tbl_makets_tables.' WHERE `MOCKUP_ID`="'.$maket_id.'")';

            $sql_arr[] = 'DELETE FROM '.$this->tbl_makets_groups.'      WHERE `MOCKUP_ID`="'.$maket_id.'"';
            $sql_arr[] = 'DELETE FROM '.$this->tbl_makets_tables.'      WHERE `MOCKUP_ID`="'.$maket_id.'"';
            $sql_arr[] = 'DELETE FROM '.$this->tbl_user_rights.'        WHERE `MOCKUP_ID`="'.$maket_id.'"';
            $sql_arr[] = 'DELETE FROM '.$this->tbl_log_user_action.'    WHERE `MOCKUP_ID`="'.$maket_id.'"';
            $sql_arr[] = 'DELETE FROM '.$this->tbl_makets.'             WHERE `MOCKUP_ID`="'.$maket_id.'"';
            
            $sql_arr[] = 'UNLOCK TABLES';
            
            
            return ExecuteSQLArray($this->link1, $sql_arr, true);
    }
    

	//Проверяем плановый ли макет. Если да, то подменяем дату - указываем первое число месяца.
    function GetMaketPeriod($maket_id)
    {			
			$maket_period = 0;

			$res1 = $this->link1->query('SELECT `MOCKUP_PERIOD` FROM '.$this->tbl_makets.' WHERE `MOCKUP_ID`="'.$maket_id.'"');
			while ($row_res1 = $res1->fetch_assoc())
			{
				$maket_period = $row_res1['MOCKUP_PERIOD'];
			}
			
			return $maket_period;
	}


	//Проверяем нужно ли скрыват кнопку "Отправить"
    function GetVisibilitySendButton($maket_id)
    {			
			$visibility_send_button = 0;

			$res1 = $this->link1->query('SELECT `HIDE_SEND_BUTTON` FROM '.$this->tbl_makets.' WHERE `MOCKUP_ID`="'.$maket_id.'"');
			while ($row_res1 = $res1->fetch_assoc())
			{
				$visibility_send_button = $row_res1['HIDE_SEND_BUTTON'];
			}
			
			return $visibility_send_button;
	}
	

    function copyMaketData($user_id, $maket_id, $maket_kpp, $mkt1, $mkt2, $only_plan)
    {
			$maket_period = self::GetMaketPeriod($maket_id);
			
			$insert_arr = array();
			$otch_data1 = date('Y-m-d', $mkt1);
			$otch_data2 = date('Y-m-d', $mkt2);
			if ($maket_period == 1)							//месячный макет
			{
					$otch_data1 = date('Y-m-01', $mkt1);	
					$otch_data2 = date('Y-m-01', $mkt2);
			}
			if ($maket_period == 2)							//годовой макет
			{
					$otch_data1 = date('Y-01-01', $mkt1);
					$otch_data2 = date('Y-01-01', $mkt2);
			}
			
			


			$res1 = $this->link1->query('SELECT * FROM '.$this->tbl_maket_data.' WHERE `MOCKUP_ID`="'.$maket_id.'" AND `POINT_ID`="'.$maket_kpp.'" AND `date`="'.$otch_data1.'"');
			
			//если код макета 7000, то копируем только поля с планами
			if ($maket_id == 314 && $only_plan)
			{
				$res1 = $this->link1->query('SELECT * FROM '.$this->tbl_maket_data.' WHERE `MOCKUP_ID`="'.$maket_id.'" AND `POINT_ID`="'.$maket_kpp.'" AND `date`="'.$otch_data1.'" AND `FIELD_ID` IN (2766, 2767, 2768, 2769)');
			}
			
			if ($res1->num_rows > 0)
			{
					while ($row_res1 = $res1->fetch_assoc())
					{
							$fraze_id = $row_res1['PH_ID'];
							$field_id = $row_res1['FIELD_ID'];
							$value = str_replace(",", ".", $row_res1['value']);

							$insert_arr[] = self::saveMaketCell($user_id, $otch_data2, $maket_id, $maket_kpp, $fraze_id, $field_id, $value);
					}
					
					
					//if (ExecuteSQLArray($this->link1, $sql_arr, true))
					if (ExecutePrepStatements_SaveData($this->link1, $insert_arr, $this->tbl_maket_data))
					{
							$maket_txt1 = self::FormMaketText($mkt2, $maket_id, $maket_kpp);

							$action = 3;
							self::LogUserAction($mkt2, $user_id, $maket_id, $maket_kpp, $maket_txt1, $action);

							return true;
					}
					else
					{
							return false;
					}
			}
            else
			{
					return true;
			}
	}
    

	//сохранение макета в БД
    function saveMaketData($user_id, $maket_id, $maket_kpp, $post_arr, $mkt1)
    {
			$maket_period = self::GetMaketPeriod($maket_id);
			
			$insert_arr = array();
			$otch_data = date('Y-m-d', $mkt1);
			if ($maket_period == 1)		$otch_data = date('Y-m-01', $mkt1);		//месячный макет
			if ($maket_period == 2)		$otch_data = date('Y-01-01', $mkt1);	//годовой макет



			
			$res1 = $this->link1->query('SELECT * FROM '.$this->tbl_makets_tables.' WHERE `MOCKUP_ID`="'.$maket_id.'"');
			while ($row_res = $res1->fetch_assoc())
			{
					$maket_table_id = $row_res['MOCKUP_TABLE_ID'];


					$res2 = $this->link1->query('SELECT * FROM '.$this->tbl_phrases.' WHERE `MOCKUP_TABLE_ID`="'.$maket_table_id.'"');
					while ($row_res2 = $res2->fetch_assoc())
					{
							$ph_mask = $row_res2['PH_MASKS'];
							$ph_mask_arr = array();
							$ph_mask_arr = array_merge($ph_mask_arr, explode(';',$ph_mask));


							//формируем массив с маской для фразы
							$mask_arr = array();
							foreach($ph_mask_arr as $k1 => $v1)
							{
								if ($v1 != '' && strstr($v1,'=') != false)
								{
									$var1 = explode('=',$v1);
									$fld_code = $var1[0];
									$mask = $var1[1];

									$mask_arr[$fld_code] = $mask;
								}
							}
							
							
							$res3 = $this->link1->query('SELECT * FROM '.$this->tbl_fields.' WHERE `MOCKUP_TABLE_ID`="'.$maket_table_id.'"');
							while ($row_res3 = $res3->fetch_assoc())
							{
									if (isset($post_arr['inp_'.$row_res2['PH_ID'].'_'.$row_res3['FIELD_ID']]))
									{
											$fraze_id = $row_res2['PH_ID'];
											$field_id = $row_res3['FIELD_ID'];
											$field_code = $row_res3['FIELD_CODE'];
											$value = str_replace(",", ".", $post_arr['inp_'.$row_res2['PH_ID'].'_'.$row_res3['FIELD_ID']]);


											//формируем маску для ячейки
											if (count($mask_arr) != 0)
											{
												$mask_cell = self::GetMaskStrForCELL($mask_arr, $field_code);
											}
											else
											{
												$mask_cell = '';
											}
											if ($mask_cell != 'string')	$value = (float) $value;


											$insert_arr[] = self::saveMaketCell($user_id, $otch_data, $maket_id, $maket_kpp, $fraze_id, $field_id, $value);
									}
							}
					}
			}
			

			//error_log(print_r($insert_arr,true));
			if (ExecutePrepStatements_SaveData($this->link1, $insert_arr, $this->tbl_maket_data))
			{
					$maket_txt1 = self::FormMaketText($mkt1, $maket_id, $maket_kpp);

					$action = 1;
					self::LogUserAction($mkt1, $user_id, $maket_id, $maket_kpp, $maket_txt1, $action);

					return true;
			}
			else
			{
					return false;
			}
    }
    
    
    private function saveMaketCell($user_id, $otch_data, $maket_id, $point_id, $fraze_id, $field_id, $value)
    {
            //определяем к каким полям и фразам пользователь имеет доступ
            $obj_user = new Users($this->link1);
            $obj_user->tbl_user_rights = $this->tbl_user_rights;
            list($arr_user_frazes, $arr_user_fields, $str_user_frazes_fields) = $obj_user->get_available_fields_frazes($maket_id, $user_id);
            
            
            
            $res1 = $this->link1->query('SELECT `PH_CODE`     FROM '.$this->tbl_phrases.'   WHERE `PH_ID`="'.$fraze_id.'"');
            $row_res1 = $res1->fetch_assoc();
            $fraze_code = $row_res1['PH_CODE'];
            
            $res1 = $this->link1->query('SELECT `FIELD_CODE`  FROM '.$this->tbl_fields.'    WHERE `FIELD_ID`="'.$field_id.'"');
            $row_res1 = $res1->fetch_assoc();
            $field_code = $row_res1['FIELD_CODE'];
            
            
            if ($str_user_frazes_fields == ''  ||  (in_array($field_code,$arr_user_fields) && in_array($fraze_code,$arr_user_frazes)))
            {
					return array(	0 => $otch_data, 
									1 => $user_id, 
									2 => $maket_id,
									3 => $point_id,
									4 => $fraze_id,
									5 => $field_id,
									6 => $value,
									7 => $_SERVER['REMOTE_ADDR']);
            }
			else
			{
					return array();
			}
    }
    
    
    
    function sendMaketData($user_id, $maket_id, $maket_kpp, $path_for_save_maket, $mkt1)
    {
			$maket_period = self::GetMaketPeriod($maket_id);
			
			$otch_data = date('Y-m-d', $mkt1);
			if ($maket_period == 1)		$otch_data = date('Y-m-01', $mkt1);		//месячный макет
			if ($maket_period == 2)		$otch_data = date('Y-01-01', $mkt1);	//годовой макет
			
			
            $res2 = $this->link1->query('SELECT `POINT_CODE` FROM '.$this->tbl_points.' WHERE `POINT_ID`='.$maket_kpp.'');
            while ($row_res2 = $res2->fetch_assoc())
            {
                    $maket_kpp_code = $row_res2['POINT_CODE'];
            }
            $res2 = $this->link1->query('SELECT `MOCKUP_CODE` FROM '.$this->tbl_makets.' WHERE `MOCKUP_ID`='.$maket_id.'');
            while ($row_res2 = $res2->fetch_assoc())
            {
                    $maket_code = $row_res2['MOCKUP_CODE'];
            }


			$maket_txt1 = self::FormMaketText($mkt1, $maket_id, $maket_kpp);


			$filename1 = $path_for_save_maket.$maket_code.'_'.$maket_kpp_code.'.txt';
            
			
            if (!$fp = fopen($filename1, 'w'))       {   echo '<p class=p1>Невозможно открыть файл '.$filename1.'</p>';               exit;   }
            if (fwrite($fp, $maket_txt1) === FALSE)  {   echo '<p class=p1>Невозможно произвести запись в файл '.$filename1.'</p>';   exit;   }
            fclose($fp);

            
            
            $action = 2;
            self::LogUserAction($mkt1, $user_id, $maket_id, $maket_kpp, $maket_txt1, $action);
			
			
			return self::sendMaketDataToDiskorDB($maket_txt1, $mkt1, $otch_data);
    }
	
	
    function sendMaketDataToDiskorDB($maket_txt, $mkt1, $otch_data)
	{
			//The url you wish to send the POST request to
			$url = 'http://esrr-skull.esrr.oao.rzd/load_maket_to_db_utf8.php';
			
			//The data you want to send via POST
			$fields = array(
				'sl_mak_str'			=> $maket_txt,
				'sl_log_error'			=> 0,
				'sl_fname'				=> 'arm_maket.txt',
				'sl_FN_LastWriteTime'	=> date('Y-m-d H:i:s', $mkt1),
				'sl_dat1'				=> $otch_data,
				'sl_decode'				=> '1',
			);
			
			//url-ify the data for the POST
			$fields_string = http_build_query($fields);
			
			//open connection
			$ch = curl_init();
			
			//set the url, number of POST vars, POST data
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_POST, true);
			curl_setopt($ch,CURLOPT_POSTFIELDS, $fields_string);
			//curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM); 
			
			//So that curl_exec returns the contents of the cURL; rather than echoing it
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, true); 
			
			//execute post
			$result = curl_exec($ch);
			
			return mb_convert_encoding($result, "windows-1251", "utf-8");
	}
    
    
    //логирование в таблице действий пользователя
    //1 - сохранить макет
    //2 - отправить макет
    private function LogUserAction($mkt1, $user_id, $maket_id, $maket_kpp, $maket_txt, $action)
    {
			$maket_period = self::GetMaketPeriod($maket_id);
			
			$otch_data = date('Y-m-d', $mkt1);			
			if ($maket_period == 1)		$otch_data = date('Y-m-01', $mkt1);	//месячный макет
			if ($maket_period == 2)		$otch_data = date('Y-01-01', $mkt1);	//годовой макет
			
            $sql_arr[] = 'INSERT INTO '.$this->tbl_log_user_action.' (`date`,`USER_ID`,`MOCKUP_ID`,`POINT_ID`,`text`,`action`,`ip`)
                                    VALUES ("'.$otch_data.'", "'.$user_id.'", "'.$maket_id.'", "'.$maket_kpp.'", "'.$maket_txt.'","'.$action.'","'.$_SERVER['REMOTE_ADDR'].'")';
            
            return ExecuteSQLArray($this->link1, $sql_arr, false);
    }
	
	


	//получение маски для ячейки по field_id и field_code
	function GetMaskStrForCELL($mask_arr, $field_code)
	{
			$mask_str = '';
			
			if (is_array($mask_arr) && array_key_exists($field_code, $mask_arr))	$mask_str = $mask_arr[$field_code];
			
			return $mask_str;
	}
	
	
	//получение значения ячейки (учитывая маску ячейки) и маски для этой ячейки по field_id и field_code
	function GetTextCELL($arr_data, $field_id, $mask_str)
	{
			//echo '<pre>';
			//print_r($arr_data);
			//echo '</pre>';
			//echo $mask_str.'__<br>';
			
			$val = '';
			
			if (array_key_exists($field_id, $arr_data))		$val = $arr_data[$field_id];
			
			if ($mask_str == '0'  &&  $val == '')	$val = 0;
			
			if ($mask_str != 'string' && substr($mask_str,0,8) != 'formul2(')
			{
					$kol_zn = 0;
					if ($mask_str != '' && strstr($mask_str,',') != false)
					{
							$var1 = explode(',',$mask_str);

							if (substr_count($var1[1],'#') > 0)	$kol_zn = substr_count($var1[1],'#');
							if (substr_count($var1[1],'0') > 0)	$kol_zn = substr_count($var1[1],'0');
					}

					if ($val == '')		$val = 0;
					$val = number_format($val, $kol_zn, '.', '');
					if ($val == 0)		$val = '';
			}
			
			//error_log($val.'__'.$mask_str);
			return $val;
	}
	
	
	
    //формирование текстового представления макета
    private function FormMaketText($mkt1, $maket_id, $maket_kpp)
    {
			$res1 = $this->link1->query('SELECT * FROM '.$this->tbl_makets.' WHERE `MOCKUP_ID`="'.$maket_id.'"');
			while ($row_res = $res1->fetch_assoc())
			{
				$maket_code = $row_res['MOCKUP_CODE'];
				$maket_start = $row_res['MOCKUP_START'];
				$maket_key = $row_res['MOCKUP_KEY'];
				$separator = $row_res['MOCKUP_PH_SEP'];
				$maket_end = $row_res['MOCKUP_END'];
			}


			$res2 = $this->link1->query('SELECT `POINT_CODE` FROM '.$this->tbl_points.' WHERE `POINT_ID`='.$maket_kpp.'');
			while ($row_res2 = $res2->fetch_assoc())
			{
					$maket_kpp_code = $row_res2['POINT_CODE'];
			}
			
			
			//$res_arr = array();
			$res = '';			
			$res.= $maket_start;
			/*
			$maket_key_arr = explode(' ', $maket_key);			
			foreach($maket_key_arr as $k1 => $v1)
			{
				
				switch ($v1)
				{
					case '@код':    $res.= $maket_code.' ';				$res_arr['code'] = $maket_code;				break;
					case '@кпп':    $res.= $maket_kpp_code.' ';			$res_arr['kpp'] = $maket_kpp_code;			break;
					case '@дд@мм':  $res.= date('dm',$mkt1).' ';		$res_arr['data'] = date('dm',$mkt1);		break;
					case '@мм@гг':  $res.= date('my',$mkt1).' ';		$res_arr['data'] = date('my',$mkt1);		break;
					default:        $res.= $v1.' ';						$res_arr['other'] = $v1;
				}				
			}
			*/
			$maket_key = str_replace("@код", $maket_code,		$maket_key);
			$maket_key = str_replace("@кпп", $maket_kpp_code,	$maket_key);
			$maket_key = str_replace("@дд", date('d',$mkt1),	$maket_key);
			$maket_key = str_replace("@мм", date('m',$mkt1),	$maket_key);
			$maket_key = str_replace("@гг", date('y',$mkt1),	$maket_key);
			$res.= $maket_key;
			$res.= $separator."\r\n";
			
			
            $maket_arr = array();


            $res1 = $this->link1->query('SELECT * FROM '.$this->tbl_makets_groups.' WHERE `MOCKUP_ID`="'.$maket_id.'" ORDER BY `MOCKUP_GROUP_ORDER_NO`');
            while ($row_res1 = $res1->fetch_assoc())
            {
                    $maket_group_id = $row_res1['MOCKUP_GROUP_ID'];


                    $res2 = $this->link1->query('SELECT * FROM '.$this->tbl_makets_tables.' WHERE `MOCKUP_ID`="'.$maket_id.'" ORDER BY `MOCKUP_TABLE_ORDER_NO`');
                    while ($row_res2 = $res2->fetch_assoc())
                    {
                            $maket_table_id = $row_res2['MOCKUP_TABLE_ID'];
                            $prev_ph_code = '';	
							
							
							$list_frazes = self::GetListFrazesByMaketTableID($maket_table_id);
							$arr_frazes_masks = self::GetArrFrazesMasksByMaketTableID($maket_table_id);
							$frazes_arr = self::GetFrazesArrByMaketTableID($maket_table_id);
							
							$list_fields = self::GetListFieldsByMaketTableID($maket_table_id);
							$fields_arr = self::GetFieldsArrByMaketTableID($maket_table_id);
							
							$mask_arr = self::GetMasksArr($arr_frazes_masks, $fields_arr);
							$temp_arr = self::GetDataFromTableMaketData(array_keys($list_frazes), array_keys($list_fields), $fields_arr, $frazes_arr, $mkt1, $maket_id, $maket_kpp, $mask_arr);
							//echo '<pre>';
							//print_r($temp_arr);
							//print_r($mask_arr);
							//echo '</pre>';							
							
							
                            $sql = 'SELECT * FROM '.$this->tbl_phrases.' WHERE `MOCKUP_TABLE_ID`="'.$maket_table_id.'" AND `MOCKUP_GROUP_ID`="'.$maket_group_id.'" ORDER BY `PH_CODE`,`PH_ORDER_NO` ';
                            $res3 = $this->link1->query($sql);
                            while ($row_res3 = $res3->fetch_assoc())
                            {
                                    $ph_code = $row_res3['PH_CODE'];
									$phraze_id = $row_res3['PH_ID'];
									
									
                                    if ($prev_ph_code != $ph_code  ||  $prev_ph_code == '')
                                    {
                                            switch ($row_res3['PH_KEY'])
                                            {
                                                case '@код':    $phr_key = $ph_code.' ';            break;
                                                case '@код.':   $phr_key = $ph_code.'. ';           break;
                                                default:        $phr_key = $row_res3['PH_KEY'].' ';
                                            }
                                    }

                                    //list($arr_data, $mask_arr) = self::GetDataFromFrazeMaket($maket_id, $maket_kpp, $row_res3['PH_ID']);
									//echo '<pre>';
									//print_r($arr_data);
									//print_r($mask_arr);
									//echo '</pre>';
									
									

                                    $coord_group = false;
                                    $coord_group_str = '';
                                    $coord_group_est_znach = false;
                                    $prev_field_id = '';


                                    $sql = 'SELECT * FROM '.$this->tbl_fields.' WHERE  `MOCKUP_TABLE_ID`="'.$maket_table_id.'"  ORDER BY `FIELD_ORDER_NO`';
                                    $res4 = $this->link1->query($sql);
                                    while ($row_res4 = $res4->fetch_assoc())
                                    {
                                            $field_id = $row_res4['FIELD_ID'];
											$field_code = $row_res4['FIELD_CODE'];
											$field_key = $row_res4['FIELD_KEY'];
											
											
                                            if ($row_res4['FIELD_COORD'] == 'T')
                                            {
                                                    //if ($coord_group_est_znach) $res.= $coord_group_str;
                                                    if ($coord_group_est_znach)
													{
															$maket_arr[$maket_group_id][$phr_key][$prev_field_id] = $coord_group_str;
													}
 

                                                    $coord_group = true;
                                                    $coord_group_str = '';
                                                    $coord_group_est_znach = false;
                                            }


                                            $fld_key = '';
                                            switch ($field_key)
                                            {
                                                case '@код':    $fld_key = $field_code.' ';  break;
                                                case '@код.':   $fld_key = $field_code.'. ';  break;
                                                default:        $fld_key = $field_key.' ';
                                            }
											
											
											
											//$mask_str = self::GetMaskStrForCELL($mask_arr, $field_code);											
											if (is_array($mask_arr) && array_key_exists($phraze_id, $mask_arr))
											{
													$mask_str = self::GetMaskStrForCELL($mask_arr[$phraze_id], $field_id);
											}
											else
											{
													$mask_str = '';
											}
											//$val = base64_encode(self::GetTextCELL($arr_data, $field_id, $mask_str));
											$val = base64_encode(self::GetTextCELL($temp_arr[$phraze_id], $field_id, $mask_str));
											
											
                                            $str = '';
                                            if ($val != '')
                                            {
                                                    $str = $fld_key.''.$val.'';
                                            }
                                            if ($val == '')
                                            {
                                                    if ($row_res4['FIELD_REQUIRED'] == 'T') $str = base64_encode('0');
                                                    if ($row_res4['FIELD_COORD'] == 'T')    $str = $fld_key.base64_encode('0');
                                                    if ($row_res4['FIELD_COORD'] == 'F')    $str = base64_encode('0');
                                            }
                                            if ($coord_group == false)
                                            {
                                                    $maket_arr[$maket_group_id][$phr_key][$field_id] = $str;
                                            }
                                            else
                                            {
                                                    $coord_group_str.= $str.' ';
                                                    if ($val != '') $coord_group_est_znach = true;
                                            }
                                            $prev_field_id = $field_id;
                                    }
                                    if ($coord_group_est_znach)
                                    {
                                            $maket_arr[$maket_group_id][$phr_key][$field_id] = $coord_group_str;
                                    }
                                    $prev_ph_code = $ph_code;
                            }
                    }
            }
			//print_r($maket_arr);
			
			
			//формируем обычный текст макета
			//коорд группы
			foreach($maket_arr as $k1 => $v1)
			{
					//фразы
					foreach($maket_arr[$k1] as $k2 => $v2)
					{
							//поля
							$res.= $k2;
							foreach($maket_arr[$k1][$k2] as $k3 => $v3)
							{
									$res.=$v3.' ';
							}
							$res.= $separator."\r\n";
					}
			}
			//$res.= $separator."\r\n";
			$res.= $maket_end."\r\n";
			
			
			return $res;
    }
    
    
    

	
	//составляем список id фраз, которые есть в таблице
	function GetListFrazesByMaketTableID($maket_table_id)
	{
			$res2 = $this->link1->query('SELECT `PH_ID`,`PH_CODE`,`PH_MASKS`,`PH_TITLE` FROM '.$this->tbl_phrases.' WHERE `MOCKUP_TABLE_ID`="'.$maket_table_id.'" ORDER BY `PH_ORDER_NO`');
			while ($row_res2 = $res2->fetch_assoc())
			{
					$list_frazes[$row_res2['PH_ID']] = $row_res2['PH_CODE'];
			}
			
			return $list_frazes;
	}
	
	function GetFrazesArrByMaketTableID($maket_table_id)
	{
			$res2 = $this->link1->query('SELECT `PH_ID`,`PH_CODE`,`PH_MASKS`,`PH_TITLE` FROM '.$this->tbl_phrases.' WHERE `MOCKUP_TABLE_ID`="'.$maket_table_id.'" ORDER BY `PH_ORDER_NO`');
			while ($row_res2 = $res2->fetch_assoc())
			{
					$list_frazes[$row_res2['PH_CODE']] = $row_res2['PH_ID'];
			}
			
			return $list_frazes;
	}

	function GetArrFrazesMasksByMaketTableID($maket_table_id)
	{
			$res2 = $this->link1->query('SELECT `PH_ID`,`PH_CODE`,`PH_MASKS`,`PH_TITLE` FROM '.$this->tbl_phrases.' WHERE `MOCKUP_TABLE_ID`="'.$maket_table_id.'" ORDER BY `PH_ORDER_NO`');
			while ($row_res2 = $res2->fetch_assoc())
			{
					$arr_frazes_masks[$row_res2['PH_ID']] = explode(';', $row_res2['PH_MASKS']);	//$arr_frazes_masks[$phraze_id] = [1=##,00],[2=##,00],[3=###,00],...
			}
			
			return $arr_frazes_masks;
	}
	
	function GetArrFrazesTitlesByMaketTableID($maket_table_id)
	{
			$res2 = $this->link1->query('SELECT `PH_ID`,`PH_CODE`,`PH_MASKS`,`PH_TITLE` FROM '.$this->tbl_phrases.' WHERE `MOCKUP_TABLE_ID`="'.$maket_table_id.'" ORDER BY `PH_ORDER_NO`');
			while ($row_res2 = $res2->fetch_assoc())
			{
					$arr_frazes_titles[$row_res2['PH_ID']] = $row_res2['PH_TITLE'];
			}
			
			return $arr_frazes_titles;
	}


	
	
	function GetListFieldsByMaketTableID($maket_table_id)
	{			
			$res1 = $this->link1->query('SELECT * FROM '.$this->tbl_fields.' WHERE `MOCKUP_TABLE_ID`="'.$maket_table_id.'" ORDER BY `FIELD_ORDER_NO`');
			while ($row_res1 = $res1->fetch_assoc())
			{
					$list_fields[$row_res1['FIELD_ID']] = $row_res1['FIELD_CODE'];
			}
			
			return $list_fields;
	}
	
	function GetFieldsArrByMaketTableID($maket_table_id)
	{			
			$res1 = $this->link1->query('SELECT * FROM '.$this->tbl_fields.' WHERE `MOCKUP_TABLE_ID`="'.$maket_table_id.'" ORDER BY `FIELD_ORDER_NO`');
			while ($row_res1 = $res1->fetch_assoc())
			{
					$fields_arr[$row_res1['FIELD_CODE']] = $row_res1['FIELD_ID'];
			}

			return $fields_arr;
	}
	
	function GetListFieldsWidthByMaketTableID($maket_table_id)
	{			
			$res1 = $this->link1->query('SELECT * FROM '.$this->tbl_fields.' WHERE `MOCKUP_TABLE_ID`="'.$maket_table_id.'" ORDER BY `FIELD_ORDER_NO`');
			while ($row_res1 = $res1->fetch_assoc())
			{
					$arr_widths[$row_res1['FIELD_ID']] = $row_res1['FIELD_WIDTH_WEB'];
			}

			return $arr_widths;
	}
	
	
	
	
	function GetMasksArr($arr_frazes_masks, $fields_arr)
	{
			$mask_arr = array();
			
			//проходимся по фразам
			foreach($arr_frazes_masks as $k1 => $v1)
			{
					//проходимся по маскам
					foreach($arr_frazes_masks[$k1] as $k2 => $v2)
					{
							if ($v2 != '' && strstr($v2,'=') != false)
							{
									$var1 = explode('=',$v2);
									$fld_code = $var1[0];
									$mask = $var1[1];
									
									//error_log( print_r( $mask_arr, true ) );
									
									$mask_arr[$k1][$fields_arr[$fld_code]] = $mask;	//$mask_arr[$phraze_id] : [1] => ######,00 [2] => ######,00 ... 
							}
					}
			}
			
			return $mask_arr;
	}



	/*
		расчитываем значение выражения вида sum_year[$phraze_id, $field_id]
	*/
	private function calc_sum_year($fields_arr, $frazes_arr, $mkt1, $maket_id, $maket_kpp, $phraze_id, $field_id)
	{
			$res2 = $this->link1->query('SELECT sum(`value`) as `value` FROM '.$this->tbl_maket_data.' WHERE
											`date` >= "'.date('Y-01-01', $mkt1).'"		AND 
											`date` <= "'.date('Y-m-d', $mkt1).'"		AND 
											`MOCKUP_ID`="'.$maket_id.'"					AND 
											`POINT_ID` IN ('.$maket_kpp.')				AND
											`FIELD_ID` = "'.$fields_arr[$field_id].'"	AND
											`PH_ID` = "'.$frazes_arr[$phraze_id].'"		');
			$row_res2 = $res2->fetch_assoc();
			$val = $row_res2['value'];
			
			return ($val == '' ? '0' : $val);
	}

	/*
		расчитываем значение выражения вида [$phraze_id, $field_id]
	*/				
	private function calc_cell_in_mask($fields_arr, $frazes_arr, $mkt1, $maket_id, $maket_kpp, $phraze_id, $field_id)
	{
			$maket_period = self::GetMaketPeriod($maket_id);
			
			$otch_data = date('Y-m-d', $mkt1);
			if ($maket_period == 1)		{	$otch_data = date('Y-m-01', $mkt1);		}	//месячный макет
			if ($maket_period == 2)		{	$otch_data = date('Y-01-01', $mkt1);	}	//годовой макет
			
			$res2 = $this->link1->query('SELECT sum(`value`) as `value` FROM '.$this->tbl_maket_data.' WHERE 
											`date` = "'.$otch_data.'"					AND 
											`MOCKUP_ID`="'.$maket_id.'"					AND 
											`POINT_ID` IN ('.$maket_kpp.')				AND
											`FIELD_ID` = "'.$fields_arr[$field_id].'"	AND
											`PH_ID` = "'.$frazes_arr[$phraze_id].'"		');
			$row_res2 = $res2->fetch_assoc();
			$val = $row_res2['value'];
			
			return ($val == '' ? '0' : $val);
	}



	
	
	
	function CalcFormulaCell($mask_str, $fields_arr, $frazes_arr, $mkt1, $maket_id, $maket_kpp, $phraze_id, $field_id)
	{
			//проверка на выражение вида sum_year[3123,3453]
			preg_match_all('/sum_year[[]\d+[,]\d+[]]/', $mask_str, $matches1);
			
			
			//echo '<pre>';
			//print_r($matches1[0]);
			//echo '</pre>';
			
			
			if (count($matches1[0]) > 0)
			{
					foreach($matches1[0] as $k1 => $str_to_replace)
					{
							$temp_str1 = str_replace("sum_year[", "", $str_to_replace);
							$temp_str2 = str_replace("]", "", $temp_str1);

							$phraze_id = 0;
							$field_id = 0;

							$parts = explode(',', $temp_str2);
							if (isset($parts[0]) && (integer) $parts[0] == $parts[0])	{	$phraze_id = $parts[0];	}
							if (isset($parts[1]) && (integer) $parts[1] == $parts[1])	{	$field_id = $parts[1];	}

							if ($phraze_id != 0 && $field_id != 0)
							{
									//расчитываем значение выражения вида sum_year[$phraze_id, $field_id]
									$insert_me = self::calc_sum_year($fields_arr, $frazes_arr, $mkt1, $maket_id, $maket_kpp, $phraze_id, $field_id);

									//заменяем в маске вида 
									//	1=sum_year[1]*sum_year[2]-sum_year[3];2=#;3=#;4=#;
									//на
									//	1=444*555-777;2=#;3=#;4=#;
									$findme = 'sum_year['.$phraze_id.','.$field_id.']';
									$mask_str = str_replace($findme, $insert_me, $mask_str);
							}
					}
			}
			
			//проверка на выражение вида [3123,3453]
			preg_match_all('/[[]\d+[,]\d+[]]/', $mask_str, $matches2);
			if (count($matches2[0]) > 0)
			{
					foreach($matches2[0] as $k1 => $str_to_replace)
					{
							$temp_str1 = str_replace("[", "", $str_to_replace);
							$temp_str2 = str_replace("]", "", $temp_str1);

							$phraze_id = 0;
							$field_id = 0;

							$parts = explode(',', $temp_str2);
							if (isset($parts[0]) && (integer) $parts[0] == $parts[0])	{	$phraze_id = $parts[0];	}
							if (isset($parts[1]) && (integer) $parts[1] == $parts[1])	{	$field_id = $parts[1];	}

							if ($phraze_id != 0 && $field_id != 0)
							{
									//расчитываем значение выражения вида sum_year[$phraze_id, $field_id]
									$insert_me = self::calc_cell_in_mask($fields_arr, $frazes_arr, $mkt1, $maket_id, $maket_kpp, $phraze_id, $field_id);

									//заменяем в маске вида 
									//	1=sum_year[1]*sum_year[2]-sum_year[3];2=#;3=#;4=#;
									//на
									//	1=444*555-777;2=#;3=#;4=#;
									$findme = '['.$phraze_id.','.$field_id.']';
									$mask_str = str_replace($findme, $insert_me, $mask_str);
							}
					}
			}
			
			$expr = $mask_str;
			//error_log($expr);
			$obj_calc = new MathExprCalculator();
			$rez = $obj_calc->calc_expression($expr);
			
			return $rez;
	}
	
	
	function GetDataFromTableMaketData($list_frazes, $list_fields, $fields_arr, $frazes_arr, $mkt1, $maket_id, $maket_kpp, $mask_arr)
	{
			$maket_period = self::GetMaketPeriod($maket_id);
			
			$otch_data = date('Y-m-d', $mkt1);
			if ($maket_period == 1)		$otch_data = date('Y-m-01', $mkt1);		//месячный макет
			if ($maket_period == 2)		$otch_data = date('Y-01-01', $mkt1);	//годовой макет
			
			
			$rez_arr = array();
			
			//нужно заполнить массив на тот случай, если макет еще ни разу не сохранялся
			foreach($list_frazes as $k1 => $v1)
			{
					foreach($list_fields as $k2 => $v2)
					{
							$phraze_id	= $v1;
							$field_id	= $v2;
							if (array_key_exists($phraze_id, $mask_arr) && is_array($mask_arr[$phraze_id]))
							{
									$mask_str = (array_key_exists($field_id, $mask_arr[$phraze_id]) ? $mask_arr[$phraze_id][$field_id] : '') ;
							}
							else
							{
									$mask_str = '';
							}
							$val = 0;
							//echo substr($mask_str,0,8).'__';
							
							if (substr($mask_str,0,8) == 'formul2(')
							{
									$start_pos = strpos($mask_str, 'formul2(');
									$mask_str = substr($mask_str, $start_pos+8, strlen($mask_str)-9);
									//ECHO $mask_str;
									$val = self::CalcFormulaCell($mask_str, $fields_arr, $frazes_arr, $mkt1, $maket_id, $maket_kpp, $phraze_id, $field_id);
							}
							
							$rez_arr[$phraze_id][$field_id] = ($val == '0' ? '' : $val);
					}				
			}
			
			
			if ($maket_id == 314)
			{
					$res1 = $this->link1->query('SELECT `FIELD_ID`,`PH_ID`,sum(`value`) as `value` FROM '.$this->tbl_maket_data.' WHERE
													`date`="'.$otch_data.'"							AND 
													`MOCKUP_ID`="'.$maket_id.'"						AND 
													`POINT_ID` IN ('.$maket_kpp.')					AND
													`FIELD_ID` IN ('.implode(',',$list_fields).')	AND
													`PH_ID` IN ('.implode(',',$list_frazes).')		
													GROUP BY `FIELD_ID`,`PH_ID`');
					while ($row_res = $res1->fetch_assoc())
					{
							$phraze_id	= $row_res['PH_ID'];
							$field_id	= $row_res['FIELD_ID'];

							if (array_key_exists($phraze_id, $mask_arr) && is_array($mask_arr[$phraze_id]))
							{
									$mask_str = (array_key_exists($field_id, $mask_arr[$phraze_id]) ? $mask_arr[$phraze_id][$field_id] : '') ;
							}
							else
							{
									$mask_str = '';
							}
							$val = $row_res['value'];
							$formula = false;

							if (substr($mask_str,0,8) == 'formul2(')
							{
									$formula = true;

									$start_pos = strpos($mask_str, 'formul2(');
									$mask_str = substr($mask_str, $start_pos+8, strlen($mask_str)-9);

									$val = self::CalcFormulaCell($mask_str, $fields_arr, $frazes_arr, $mkt1, $maket_id, $maket_kpp, $phraze_id, $field_id);
							}


							if (isset($rez_arr[$phraze_id][$field_id]))
							{
									//Если ячейка расчитывается по формуле, то суммировать уже не нужно. Это сделано в цикле выше.
									if (!$formula)
									{
											if ($rez_arr[$phraze_id][$field_id] == '')	$rez_arr[$phraze_id][$field_id] = 0;
											
											$rez_arr[$phraze_id][$field_id]+= ($val == '0' ? 0 : floatval($val));
									}
							}
							else
							{
									if ($rez_arr[$phraze_id][$field_id] == '')	$rez_arr[$phraze_id][$field_id] = 0;
									
									$rez_arr[$phraze_id][$field_id] = ($val == '0' ? 0 : floatval($val));
							}
					}
			}
			else
			{
					$res1 = $this->link1->query('SELECT `FIELD_ID`,`PH_ID`,`value` FROM '.$this->tbl_maket_data.' WHERE
													`date`="'.$otch_data.'"							AND 
													`MOCKUP_ID`="'.$maket_id.'"						AND 
													`POINT_ID`="'.$maket_kpp.'"						AND
													`FIELD_ID` IN ('.implode(',',$list_fields).')	AND
													`PH_ID` IN ('.implode(',',$list_frazes).')		');
					while ($row_res = $res1->fetch_assoc())
					{
							$phraze_id	= $row_res['PH_ID'];
							$field_id	= $row_res['FIELD_ID'];
							//$mask_str = $mask_arr[$phraze_id][$field_id];
							if (array_key_exists($phraze_id, $mask_arr) && is_array($mask_arr[$phraze_id]))
							{
									$mask_str = (array_key_exists($field_id, $mask_arr[$phraze_id]) ? $mask_arr[$phraze_id][$field_id] : '') ;
							}
							else
							{
									$mask_str = '';
							}
							$val = $row_res['value'];


							if (substr($mask_str,0,8) == 'formul2(')
							{
									$start_pos = strpos($mask_str, 'formul2(');
									$mask_str = substr($mask_str, $start_pos+8, strlen($mask_str)-9);

									$val = self::CalcFormulaCell($mask_str, $fields_arr, $frazes_arr, $mkt1, $maket_id, $maket_kpp, $phraze_id, $field_id);
							}

							$rez_arr[$phraze_id][$field_id] = ($val == '0' ? '' : $val);
					}
			}
			
			//echo '<pre>';
			//print_r($rez_arr);
			//echo '<pre>';
			
			return $rez_arr;
	}
}
<?php
	function CheckDTime($DTime)//2009-15-22
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
	function get_date_razdel($s)
	{
                $y = substr($s,0,4);
                $m = substr($s,5,2);
                $d = substr($s,8,2);

                return array($d, $m, $y);
	}
        
        
        
	function gettime()
	{
                $part_time = explode(' ',microtime());
                $real_time = $part_time[1].substr($part_time[0],1);
                return $real_time;
	}
        
        
        
	function ExecuteSQLArray($link1, $sql_arr, $save_to_file)
	{
                try
                {
                        $link1->autocommit(FALSE);

                        foreach ($sql_arr as $k1 => $v1) 
                        {
                                if (!$link1->query($v1))
                                {
										error_log('Ошибка: '.$link1->error.' file: '.__FILE__.' line: '.__LINE__);
										throw new Exception('Error transaction!');
                                }
                        }
                        $link1->commit();
                        $rez = true;
                }
                catch (Exception $ex)
                {
                        $link1->rollback();
                        $rez = false;
                }
                $link1->autocommit(true);
                
                
                return $rez;
	}
	
	
	function ExecutePrepStatements_SaveData($link1, $insert_arr, $tbl_maket_data)
	{
			try
			{
					$link1->autocommit(FALSE);
					
                    $query1 = 'DELETE FROM '.$tbl_maket_data.' WHERE	`date`=?			AND 
																		`MOCKUP_ID`=?		AND 
																		`POINT_ID`=?		AND 
																		`PH_ID`=?			AND 
																		`FIELD_ID`=?		';
					$stmt1 = $link1->prepare($query1);
					$stmt1 ->bind_param("siiii", $otch_data, $maket_id, $point_id, $fraze_id, $field_id);
					
					
                    $query2 = 'INSERT INTO '.$tbl_maket_data.' (`date`,`USER_ID`,`MOCKUP_ID`,`POINT_ID`,`PH_ID`,`FIELD_ID`,`value`,`ip`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)';
					$stmt2 = $link1->prepare($query2);
					$stmt2 ->bind_param("siiiiiss", $otch_data, $user_id, $maket_id, $point_id, $fraze_id, $field_id, $value, $ip);
					
					
					foreach ($insert_arr as $k1 => $v1)
					{
							if (count($v1) > 0)
							{
									$otch_data = $v1[0];
									$user_id = $v1[1];
									$maket_id = $v1[2];
									$point_id = $v1[3];
									$fraze_id = $v1[4];
									$field_id = $v1[5];
									$value = htmlspecialchars($v1[6]);
									$ip = $v1[7];
									
									
									$stmt1->execute();
									if ($value != '' && $value != '0' && $value != '0.0' && $value != '0.00' && $value != '0.000')
									{
											$stmt2->execute();
									}
							}
					}
					$stmt1->close();
					$stmt2->close();
					
					
					$link1->commit();
					$rez = true;
			}
			catch (Exception $ex)
			{
					error_log($ex);
					$link1->rollback();
					$rez = false;
			}
			$link1->autocommit(true);


			return $rez;
	}
	
	

?>
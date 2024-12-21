<?php

class ARMMaketObjects
{
    

    function SaveObject($link1, $tbl_name, $edit_id, $field_name, $obj)
    {
            $result1 = $link1->query('SELECT * FROM '.$tbl_name.' LIMIT 1');            
            while ($row_res1 = $result1->fetch_assoc())
            {
                    $i = 1;

                    foreach($row_res1 as $k1 => $v1)
                    {
                            if ($i != 1 && $i != count($row_res1))
                            {
									//error_log($_POST['sl_frm_'.$obj.'_element_'.$i]);
									//error_log(print_r($_POST, true));
									
									
                                    $val = (isset($_POST['sl_frm_'.$obj.'_element_'.$i]) ? $_POST['sl_frm_'.$obj.'_element_'.$i] : '');
                                    if ($val == '') $val = NULL;

                                    $sql = "UPDATE ".$tbl_name." SET ".$k1." = ? WHERE `".$field_name."`=".$edit_id."";
                                    //error_log($sql);
									//error_log($val);
                                    //SQL does not allow parameters for column aliases, expressions, or keywords.
                                    if ($stmt = $link1->prepare($sql))
                                    {
                                        $stmt->bind_param("s", $val);
                                        $stmt->execute();
                                    }
                                    else
                                    {
                                        $save_err_msg = 'Ошибка: '.$sql.'  '.$link1->error; 
                                        error_log($save_err_msg);
                                    }
                            }
                            $i++;
                    }
            }
    }
}
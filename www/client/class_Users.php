<?php

class Users extends ARMMaketObjects
{
    private $link1;
    
    public $tbl_users;
    public $tbl_user_rights;
    public $tbl_log_user_action;
    
    
    
    
    // конструктор для соединения с базой данных
    public function __construct($link1)
    {
        $this->link1 = $link1;
    }
    
    
    function CreateUser()
    {
            $sql_arr[] = 'INSERT INTO '.$this->tbl_users.' (`USER_TITLE`) VALUES ("Новый пользователь")';
            
            return ExecuteSQLArray($this->link1, $sql_arr, true);
    }
    
    function SaveUser($user_id, $field_name, $obj)
    {
            $this->SaveObject($this->link1, $this->tbl_users, $user_id, $field_name, $obj);
    }
    
    function DeleteUser($user_id)
    {
			$sql_arr[] = 'LOCK TABLES '.	//$this->tbl_log_user_action	.' WRITE, '.
											$this->tbl_user_rights		.' WRITE, '.
											$this->tbl_users		.' WRITE ';

			//$sql_arr[] = 'DELETE FROM '.$this->tbl_log_user_action.'    WHERE `USER_ID`="'.$user_id.'"';
			$sql_arr[] = 'DELETE FROM '.$this->tbl_user_rights.'        WHERE `USER_ID`="'.$user_id.'"';
			$sql_arr[] = 'DELETE FROM '.$this->tbl_users.'              WHERE `USER_ID`="'.$user_id.'"';

			$sql_arr[] = 'UNLOCK TABLES';

			return ExecuteSQLArray($this->link1, $sql_arr, true);
    }
    
    function CreateUserRights($maket_id, $user_id)
    {
            $result1 = $this->link1->query('SELECT * FROM '.$this->tbl_user_rights.' WHERE `MOCKUP_ID`="'.$maket_id.'" AND `USER_ID`="'.$user_id.'"');
            $row_cnt = $result1->num_rows;

            if ($row_cnt == 0 && $maket_id > 0 && $user_id > 0)
            {                
                    $sql_arr[] = 'INSERT INTO '.$this->tbl_user_rights.' (`MOCKUP_ID`,`USER_ID`) VALUES ("'.$maket_id.'","'.$user_id.'")';
                    
                    return ExecuteSQLArray($this->link1, $sql_arr, true);
            }
    }
    
    function SaveUserRights($user_rights_id, $field_name, $obj)
    {
            $this->SaveObject($this->link1, $this->tbl_user_rights, $user_rights_id, $field_name, $obj);
    }
    
    function DeleteUserRights($user_mockup_id)
    {
            $sql_arr[] = 'DELETE FROM '.$this->tbl_user_rights.'  WHERE `USER_MOCKUP_ID`="'.$user_mockup_id.'"';
            
            return ExecuteSQLArray($this->link1, $sql_arr, true);
    }
    
    //определяем коды полей и фраз, к которым имеет доступ пользователь в указанном макете
    function get_available_fields_frazes($maket_id, $user_id)
    {
            $str_user_frazes_fields = '';
            $arr_user_frazes = array();
            $arr_user_fields = array();

            $res3 = $this->link1->query('SELECT `USER_ACCESS_BLOCK` FROM '.$this->tbl_user_rights.' WHERE `MOCKUP_ID`="'.$maket_id.'" AND `USER_ID`="'.$user_id.'"');
            while ($row_res3 = $res3->fetch_assoc())
            {
                    $str_user_frazes_fields = $row_res3['USER_ACCESS_BLOCK'];
            }
            if ($str_user_frazes_fields != '')
            {
                $str_user_frazes_fields = str_replace("(", "", $str_user_frazes_fields);
                $str_user_frazes_fields = str_replace(")", "", $str_user_frazes_fields);

                $v = explode(';',$str_user_frazes_fields);
                $str_user_frazes = $v[0];
                $str_user_fields = $v[1];

                $arr_user_frazes = explode(',',$str_user_frazes);
                $arr_user_fields = explode(',',$str_user_fields);
            }

            return array($arr_user_frazes, $arr_user_fields, $str_user_frazes_fields);
    }
}
<?php

class PointTypes extends ARMMaketObjects
{
    private $link1;
    
    
    public $tbl_point_types;
    public $tbl_points;
    public $tbl_log_user_action;
    public $tbl_maket_data;
    
    // конструктор для соединения с базой данных
    public function __construct($link1)
    {
        $this->link1 = $link1;
    }
    
    
    
    function CreatePointTypes()
    {
            $sql_arr[] = 'INSERT INTO '.$this->tbl_point_types.' (`POINT_TYPE_TITLE`) VALUES ("Новый тип КПП")';
            
            return ExecuteSQLArray($this->link1, $sql_arr, true);
    }

    function CreatePoints($point_type_id)
    {
            $sql_arr[] = 'INSERT INTO '.$this->tbl_points.' (`POINT_TYPE_ID`,`POINT_TITLE`,`POINT_CODE`) VALUES ("'.$point_type_id.'","Новый КПП","1")';
            
            return ExecuteSQLArray($this->link1, $sql_arr, true);
    }
    
    function DeletePoints($point_id)
    {
            $sql_arr[] = 'DELETE FROM '.$this->tbl_log_user_action.'  WHERE `POINT_ID`="'.$point_id.'"';
            $sql_arr[] = 'DELETE FROM '.$this->tbl_maket_data.'       WHERE `POINT_ID`="'.$point_id.'"';
            $sql_arr[] = 'DELETE FROM '.$this->tbl_points.'           WHERE `POINT_ID`="'.$point_id.'"';
            
            return ExecuteSQLArray($this->link1, $sql_arr, true);
    }
    
    function DeletePointTypes($point_type_id)
    {

            $sql_arr[] = 'DELETE FROM '.$this->tbl_points.'       WHERE `POINT_TYPE_ID`="'.$point_type_id.'"';
            $sql_arr[] = 'DELETE FROM '.$this->tbl_point_types.'  WHERE `POINT_TYPE_ID`="'.$point_type_id.'"';
            
            return ExecuteSQLArray($this->link1, $sql_arr, true);
    }
}
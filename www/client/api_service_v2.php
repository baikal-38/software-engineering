<?php
require_once 'class_ARMMaketObjects.php';
require_once 'class_MaketManager.php';
require_once 'class_Users.php';

require_once 'sys_con_db.php';
require_once 'sys_vars.php';
require_once 'sys_func.php';


$method = $_SERVER['REQUEST_METHOD'];


if (in_array($method, array('PUT','DELETE','POST')))   $data = json_decode(file_get_contents("php://input"),true);
if ($method == 'GET')                                  $data = $_GET;

  
switch ($method)
{
    case "PUT":
            insertDataCell($link1, $data, $tbl_maket_data, $tbl_users, $tbl_points, $tbl_log_user_action, $tbl_user_rights, $tbl_makets, $tbl_makets_groups, $tbl_makets_tables, $tbl_phrases, $tbl_fields);
        break;
}

function insertDataCell($link1, $data, $tbl_maket_data, $tbl_users, $tbl_points, $tbl_log_user_action, $tbl_user_rights, $tbl_makets, $tbl_makets_groups, $tbl_makets_tables, $tbl_phrases, $tbl_fields)
{
    //var_error_log($data);
    
    
    $login = $data['login'];
    $pass = $data['pass'];
    $otch_data = $data['otch_data'];
    $maket_id = $data['maket_id'];
    $maket_kpp = $data['point_id'];
    
    //error_log($data->login);
    //error_log($data->pass);
    //error_log($data->otch_data);
    
    
    //проверить доступ пользователя к АРМ Макет
    if ($stmt = $link1->prepare("SELECT `USER_ID`,`USER_PASSWORD` FROM ".$tbl_users." WHERE `USER_NAME` = ?"))
    {
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $user_password);
    }
    if ($stmt->num_rows == 0)
    {
        header('HTTP/1.1 401');                    
        echo json_encode(array('message' => 'Unauthorized.'));
        die;
    }
    $stmt->fetch();
    
    
    if (hash("sha256", $pass) == $user_password)
    {
        //$_SESSION['user_id'] = $user_id;
    }
    else
    {
        header('HTTP/1.1 401');                    
        echo json_encode(array('message' => 'Unauthorized.'));
        die;
    }
    
    
    
    //проверить права пользователя к макету
    $arr_user_maket_id = array();
    $res1 = $link1->query('SELECT * FROM '.$tbl_user_rights.' WHERE `USER_ID`="'.$user_id.'"');
    while ($row_res1 = $res1->fetch_assoc())
    {
        $arr_user_maket_id[] = $row_res1['MOCKUP_ID'];
    }
    if ($maket_id > 0 && !in_array($maket_id, $arr_user_maket_id))
    {
        header('HTTP/1.1 403');                    
        echo json_encode(array('message' => 'Access denied.'));
        die;
    }
    
    
    
    
    
    
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
	
    $mkt1 = strtotime($otch_data);
    
	
    
    if ($obj_maket->saveMaketData($user_id, $maket_id, $maket_kpp, $data, $mkt1))
    {
        header('HTTP/1.1 201');                    
        echo json_encode(array('message' => 'Data inserted'));
    }
    else
    {
        header('HTTP/1.1 409');                    
        echo json_encode(array('message' => 'Can`t insert data. Operation failed.'));
    }
    /**/
}

?>
    

    
    
    
    
    
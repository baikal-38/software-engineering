<?php
header("Content-type: text/html; charset=UTF-8;");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
    
    require_once 'class_ARMMaketObjects.php';
    require_once 'class_MaketManager.php';
    require_once 'class_Users.php';
    require_once 'class_PointTypes.php';
    require_once 'sys_con_db.php';
    require_once 'sys_vars.php';
    require_once 'sys_func.php';
    require_once 'sys_get_post.php';
    require_once __DIR__.'/boot.php';
    
if (!check_auth())
{
    header('Location: index.php');
    die;
}
?>

<style>
		.table1     {                                        border-color: #787878; border-width: .5pt .5pt .5pt .5pt; border-style: solid; border-collapse:collapse; }
		.td_1       { font-family: Verdana; font-size: 10px; border-color: #787878; border-width: .5pt .5pt .5pt .5pt;     }
		.td_1_bold  { font-family: Verdana; font-size: 9px;  border-color: #787878; border-width: .5pt .5pt .5pt .5pt;  font-weight: bold;  }

		.a1         { font-family: Verdana; font-size: 12px;  color: blue;  text-decoration: none;          }
		.a2         { font-family: Verdana; font-size: 9px;  color: blue;   text-decoration: none;          }
		.a3         { font-family: Verdana; font-size: 20px;  color: blue;  text-decoration: none;          }
		.a3_underl  { font-family: Verdana; font-size: 20px;  color: blue;  text-decoration: underline;     }
</style>


<?php
$res1 = $link1->query('SELECT `ADMIN` FROM '.$tbl_users.' WHERE `USER_ID`="'.$_SESSION['user_id'].'"');
$row_res1 = $res1->fetch_assoc();
$admin = $row_res1['ADMIN'];

if ($obj === 'makets'        && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'coord_gr'      && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'tables'        && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'fields'        && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'frazes'        && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'point_types'   && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'points'        && $admin != 1)                     {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'users'         && !($admin == 1 || $admin == 2))   {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'user_rights'   && !($admin == 1 || $admin == 2))   {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
if ($obj === 'maket_rights'  && !($admin == 1 || $admin == 2))   {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
//error_log($obj);


$res1 = $link1->query('SELECT * FROM '.$tbl_makets.' ORDER BY `MOCKUP_CODE`');
while ($row_res1 = $res1->fetch_assoc())
{
        $id = $row_res1['MOCKUP_ID'];
        $code = $row_res1['MOCKUP_CODE'];

        $arr_makets[$id] = $code;
}


if ($obj == 'makets' || $obj == 'coord_gr' || $obj == 'tables' || $obj == 'fields' || $obj == 'frazes')
{
        $obj_maket = new MaketManager($link1);
        $obj_maket->tbl_points = $tbl_points;
        $obj_maket->tbl_point_types = $tbl_point_types;
        $obj_maket->tbl_makets = $tbl_makets;
        $obj_maket->tbl_user_rights = $tbl_user_rights;
        $obj_maket->tbl_maket_data = $tbl_maket_data;
		$obj_maket->tbl_maket_data_arh = $tbl_maket_data_arh;
        $obj_maket->tbl_makets_tables = $tbl_makets_tables;
        $obj_maket->tbl_makets_groups = $tbl_makets_groups;
        $obj_maket->tbl_phrases = $tbl_phrases;
        $obj_maket->tbl_fields = $tbl_fields;
        $obj_maket->tbl_log_user_action = $tbl_log_user_action;
}

if ($obj == 'users' || $obj == 'user_rights' || $obj == 'maket_rights')
{
        $obj_user = new Users($link1);
        $obj_user->tbl_users = $tbl_users;
        $obj_user->tbl_user_rights = $tbl_user_rights;
}
if ($obj == 'point_types' ||  $obj == 'points')
{
        $obj_point_types = new PointTypes($link1);
        $obj_point_types->tbl_point_types = $tbl_point_types;
        $obj_point_types->tbl_points = $tbl_points;
        $obj_point_types->tbl_log_user_action = $tbl_log_user_action;
        $obj_point_types->tbl_maket_data = $tbl_maket_data;
}




//error_log('--'.$obj);
if ($obj == 'makets')
{
        if ($obj_act == 1)  $obj_maket->CreateNewMaket();
        if ($obj_act == 2)  $obj_maket->DeleteMaket($edit_id);
        if ($obj_act == 3)  $obj_maket->SaveMaket($edit_id, 'MOCKUP_ID', $obj);

        $arr_titles = array('ID','CODE','KPP_TYPE_ID','KEY','Наименование макета','ORDER_NO','START','PH_SEP','END','PERIOD','HIDE_SEND_BTN','Кол-во польз.','time_ins');
        $arr_width  = array('30','70',  '50',         '100','200',                '50',      '30',   '30',    '30',     '30',              '30',           '20',      '90');
        $tbl_name = $vw_maket_and_user_count;
}
if ($obj == 'users')
{
        if ($obj_act == 1)  $obj_user->CreateUser();
        if ($obj_act == 2)  $obj_user->DeleteUser($edit_id);
        if ($obj_act == 3)  $obj_user->SaveUser($edit_id, 'USER_ID', $obj);

        $arr_titles = array('ID','Логин','Имя пользователя','Пароль','email','АС ОЗ №','АС ОЗ начало периода','АС ОЗ конец периода','Админ','Кол-во макет.','last_visit','time_ins');
        $arr_width  = array('30','150',   '150',             '100',  '100',  '80',      '50',                  '50',                 '50',   '20',           '50',        '90');
        $tbl_name = $vw_users_and_makets_count;
}
if ($obj == 'user_rights' || $obj == 'maket_rights')
{
        if ($obj_act == 1)  $obj_user->CreateUserRights($maket_id, $sel_polz_id);
        if ($obj_act == 2)  $obj_user->DeleteUserRights($edit_id);
        if ($obj_act == 3)  $obj_user->SaveUserRights($edit_id, 'USER_MOCKUP_ID', $obj);

        $arr_titles = array('ID','Код макета','Имя польз.','DATA_ONLY','USER_ACCESS_BLOCK','USER_ACCESS_KPP','USER_ID','MOCKUP_ID','time_ins');
        $arr_width  = array('30','50',        '150',       '30',       '200',              '150',				 '30',     '30',       '90');
        $tbl_name = $vw_users_rights_names;
}
if ($obj == 'point_types')
{
        if ($obj_act == 1)  $obj_point_types->CreatePointTypes();
        if ($obj_act == 2)  $obj_point_types->DeletePointTypes($edit_id);
        if ($obj_act == 3)  $obj_point_types->SaveObject($link1, $tbl_point_types, $edit_id, 'POINT_TYPE_ID', $obj);

        $arr_titles = array('POINT_TYPE_ID','POINT_TYPE_TITLE','Кол-во points','time_ins');
        $arr_width  = array('30',           '200',             '90',           '90');
        $tbl_name = $ptypes_and_points_count;
}
if ($obj == 'points')
{
        if ($obj_act == 1)  $obj_point_types->CreatePoints($point_type_id);
        if ($obj_act == 2)  $obj_point_types->DeletePoints($edit_id);
        if ($obj_act == 3)  $obj_point_types->SaveObject($link1, $tbl_points, $edit_id, 'POINT_ID', $obj);

        $arr_titles = array('ID','POINT_TYPE_ID','CODE','TITLE','TITLE_SHORT','ORDER_NO','TOTAL','time_ins');
        $arr_width  = array('30','30',           '30',  '200',  '200',      '30',   '90');
        $tbl_name = $tbl_points;
}

if ($obj == 'coord_gr')
{
        if ($obj_act == 1)  $obj_maket->CreateCoordGR($maket_id);
        if ($obj_act == 2)  $obj_maket->DeleteCoordGR($edit_id);
        if ($obj_act == 3)  $obj_maket->SaveCoordGR($edit_id, 'MOCKUP_GROUP_ID', $obj);

        $arr_titles = array('ID','MOCKUP_ID','GROUP_ORDER_NO','GROUP_KEY','time_ins');
        $arr_width  = array('30','30',       '30',            '30',       '90');
        $tbl_name = $tbl_makets_groups;
}
if ($obj == 'tables')
{
        if ($obj_act == 1)  $obj_maket->CreateTables($maket_id);
        if ($obj_act == 2)  $obj_maket->DeleteTables($edit_id);
        if ($obj_act == 3)  $obj_maket->SaveTables($edit_id, 'MOCKUP_TABLE_ID', $obj);

        $arr_titles = array('ID','MOCKUP_ID','TABLE_CODE','TABLE_ORDER_NO','TITLE_WIDTH_WEB','TITLE_WIDTH_XLS','CODE_WIDTH','TABLE_TITLE','time_ins');
        $arr_width  = array('30','30',       '30',        '30',            '30',             '30',             '30',        '100',         '90');
        $tbl_name = $tbl_makets_tables;
}
if ($obj == 'fields')
{
        if ($obj_act == 1 && $m_table_id > 0)   $obj_maket->CreateFields($m_table_id);
        if ($obj_act == 2)                      $obj_maket->DeleteFields($edit_id);
        if ($obj_act == 3)                      $obj_maket->SaveFields($edit_id, 'FIELD_ID', $obj);

        $arr_titles = array('ID','TABLE_ID','FIELD_TYPE_ID','CODE','ORDER_NO','FIELD_COORD','REQUIRED','WIDTH_WEB','WIDTH_XLS','KEY','TITLE','KPP_DEPENDENT','DUMMY','CONST','time_ins');
        $arr_width  = array('30','30',      '30',           '30',  '30',      '30',         '30',      '30',       '30',       '30', '300',  '30',           '30',   '30',   '90');
        $tbl_name = $tbl_fields;
}
if ($obj == 'frazes')
{
        if ($obj_act == 1 && $coord_gr_id > 0 && $m_table_id > 0)   $obj_maket->CreateFrazes($coord_gr_id, $m_table_id);
        if ($obj_act == 2)                                          $obj_maket->DeleteFrazes($edit_id);
        if ($obj_act == 3)                                          $obj_maket->SaveFrazes($edit_id, 'PH_ID', $obj);

        $arr_titles = array('ID','TABLE_ID','GROUP_ID','PH_CODE','PH_TYPE_ID','PH_ORDER_NO','PH_KEY','PH_MASKS','PH_TITLE','PH_KPP_DEPENDENT','PH_DUMMY','PH_REQUIRED','PH_CONST','time_ins');
        $arr_width  = array('30','30',      '30',      '30',     '30',        '30',         '30',    '400',      '200',     '30',              '30',      '30',         '30',      '90');
        $tbl_name = $tbl_phrases;
}



echo ShowTableElements($link1, $obj, $tbl_name, $order_fld_num, $order_dir, $maket_id, $user_id, $sel_polz_id, $edit_id, $coord_gr_id, $m_table_id, $point_type_id, $arr_titles, $arr_makets, $arr_width, $show_full_text);


function ShowTableElements($link1, $obj, $tbl_name, $order_fld_num, $order_dir, $maket_id, $user_id, $sel_polz_id, $edit_id, $coord_gr_id, $m_table_id, $point_type_id, $arr_titles, $arr_makets, $arr_width, $show_full_text)
{
        switch ($obj)
        {
            case 'makets':        $obj_name1 = 'макет';                 break;
            case 'users':         $obj_name1 = 'пользователя';          break;
            case 'coord_gr':      $obj_name1 = 'координатную группу';   break;
            case 'tables':        $obj_name1 = 'таблицу';               break;
            case 'fields':        $obj_name1 = 'поле';                  break;
            case 'frazes':        $obj_name1 = 'фразу';                 break;
            case 'user_rights':   $obj_name1 = 'права пользователя';    break;
            case 'maket_rights':  $obj_name1 = 'права к макету';        break;
            case 'point_types':   $obj_name1 = 'тип КПП';               break;
            case 'points':        $obj_name1 = 'КПП';                   break;
            default:              $obj_name1 = '';
        }
        
        
        
        $order_field = '';
        
        $result1 = $link1->query('SELECT * FROM '.$tbl_name.' LIMIT 1');
        while ($row_res1 = $result1->fetch_assoc())
        {
                $col = 1;
                foreach($row_res1 as $k1 => $v1)
                {
                        if ($order_fld_num == $col)  $order_field = $k1;
                        $col++;
                }
        }
        
        $row = 1;
        
        $order_dir_str = ' ASC ';
        $order_direction_new = '1';
        $order_direction_old = '1';
        if ($order_dir == '1')  {   $order_direction_new = '2';     $order_direction_old = '1';     $order_dir_str = ' ASC ';     }
        if ($order_dir == '2')  {   $order_direction_new = '1';     $order_direction_old = '2';     $order_dir_str = ' DESC ';    }
        
		
        $order_str = '';
		
		//сортировка по умолчанию
		if ($obj == 'frazes')			$order_str = ' ORDER BY `PH_ORDER_NO`';
		if ($obj == 'points')			$order_str = ' ORDER BY `POINT_ORDER_NO`';
		if ($obj == 'makets')			$order_str = ' ORDER BY `MOCKUP_ORDER_NO`';
		
		//сортировка по полю, указанному пользователем
        if ($order_field != '')		$order_str = ' ORDER BY '.$order_field.$order_dir_str;
        
        
        //кнопка "Добавить объект"
        echo '<p onclick=\'showObjTable("tbl_'.$obj.'","'.$obj.'", "'.
                '&sl_obj_act=1'.
                '&sl_order_dir='.$order_direction_old.
                '&sl_order_fld_num='.$order_fld_num.
                ($user_id > 0 ? '&sl_user_id='.$user_id : '').
				($sel_polz_id > 0 ? '&sl_sel_polz_id='.$sel_polz_id : '').
                ($maket_id > 0 ? '&sl_maket_id='.$maket_id : '').
                ($m_table_id > 0 ? '&sl_m_table_id='.$m_table_id : '').
                ($coord_gr_id > 0 && $m_table_id > 0 ? '&sl_coord_gr_id='.$coord_gr_id.'&sl_m_table_id='.$m_table_id : '').
                ($point_type_id > 0 ? '&sl_point_type_id='.$point_type_id : '').
                '");\' class=p1 style="text-align: center; cursor: pointer; color: blue;">Добавить '.$obj_name1.'</p>';
        //ссылка "Полные тексты"
        echo '<p onclick=\'showObjTable("tbl_'.$obj.'","'.$obj.'", "'.
                '&sl_order_dir='.$order_direction_old.
                '&sl_show_full_text='.($show_full_text == 0 ? '1' : '0').
                '&sl_order_fld_num='.$order_fld_num.
                ($user_id > 0 ? '&sl_user_id='.$user_id : '').
				($sel_polz_id > 0 ? '&sl_sel_polz_id='.$sel_polz_id : '').
                ($maket_id > 0 ? '&sl_maket_id='.$maket_id : '').
                ($m_table_id > 0 ? '&sl_m_table_id='.$m_table_id : '').
                ($coord_gr_id > 0 && $m_table_id > 0 ? '&sl_coord_gr_id='.$coord_gr_id.'&sl_m_table_id='.$m_table_id : '').
                ($point_type_id > 0 ? '&sl_point_type_id='.$point_type_id : '').
                '");\' class=p4 style="text-align: center; cursor: pointer; color: blue;">Показать полные тексты</p>';        
        
        $res = '<table class=table1  cellspacing=0 cellpadding=0 style="margin: auto; ">'."\r\n";
        
                                                                $sql = 'SELECT * FROM '.$tbl_name                                                                                               .' '.$order_str;
        if ($obj == 'tables')                                   $sql = 'SELECT * FROM '.$tbl_name.' WHERE `MOCKUP_ID`='.$maket_id                                                               .' '.$order_str;
        if ($obj == 'coord_gr')                                 $sql = 'SELECT * FROM '.$tbl_name.' WHERE `MOCKUP_ID`='.$maket_id                                                               .' '.$order_str;
        if ($obj == 'fields')                                   $sql = 'SELECT * FROM '.$tbl_name.' WHERE `MOCKUP_TABLE_ID`='.$m_table_id                                                       .' '.$order_str;
        if ($obj == 'frazes')                                   $sql = 'SELECT * FROM '.$tbl_name.' WHERE `MOCKUP_TABLE_ID`='.$m_table_id.' AND `MOCKUP_GROUP_ID`='.$coord_gr_id                .' '.$order_str;
        if ($obj == 'user_rights' || $obj == 'maket_rights')    $sql = 'SELECT * FROM '.$tbl_name.' WHERE 1=1 '.($sel_polz_id != 0 ? ' AND `USER_ID`='.$sel_polz_id : '').($maket_id != 0 ? '  AND `MOCKUP_ID`='.$maket_id : '')   .' '.$order_str;
        if ($obj == 'points')                                   $sql = 'SELECT * FROM '.$tbl_name.' WHERE `POINT_TYPE_ID`='.$point_type_id                                                      .' '.$order_str;
        
        //error_log('---'.$sql);
        
        $result1 = $link1->query($sql);
        while ($row_res1 = $result1->fetch_assoc())
        {
                if ($row == 1)
                {
                        //заголовки таблицы
                        $col = 1;
                        $res.= '<tr style="height: 30px; text-align: center;">'."\r\n";
                        $res.= '<td class=td_1><div style="width: 20px;">№</div></td>'."\r\n";
                        $res.= '<td class=td_1><div style="width: 30px;"></div></td>'."\r\n";
                        $res.= '<td class=td_1><div style="width: 30px;"></div></td>'."\r\n";
                        $res.= '<td class=td_1><div style="width: 30px;"></div></td>'."\r\n";
                        foreach($row_res1 as $k1 => $v1)
                        {
                                $res.= '<td class=td_1_bold style="cursor: pointer; ">'."\r\n";
                                
                                $res.= '<a onclick=\'showObjTable("tbl_'.$obj.'","'.$obj.'",   "'.
                                        '&sl_edit_id='.$edit_id.
                                        '&sl_order_dir='.$order_direction_new.
                                        '&sl_order_fld_num='.$col.
                                        ($user_id > 0 ? '&sl_user_id='.$user_id : '').
										($sel_polz_id > 0 ? '&sl_sel_polz_id='.$sel_polz_id : '').
                                        ($maket_id > 0 ? '&sl_maket_id='.$maket_id : '').
                                        ($m_table_id > 0 ? '&sl_m_table_id='.$m_table_id : '').
                                        ($coord_gr_id > 0 && $m_table_id > 0 ? '&sl_coord_gr_id='.$coord_gr_id.'&sl_m_table_id='.$m_table_id : '').
                                        ($point_type_id > 0 ? '&sl_point_type_id='.$point_type_id : '').
                                        '");\' class=a2>'.$arr_titles[$col-1].'</a>'."\r\n";
                                $res.= '</td>'."\r\n";
                                $col++;
                        }
                        $res.= '</tr>'."\r\n";
                }
                
                
                $col = 1;
                $link_edit_id = '';
                $flag_edit = false;
                
                
                $res.= '<tr style="height: 20px; text-align: center;">'."\r\n";
                $res.= '<td class=td_1>'.$row.'</td>'."\r\n";
                
                
                //вывод значений таблицы
                $row_str = '';
                foreach($row_res1 as $k1 => $v1)
                {
						if ($col == 1)   $link_id = $v1;
						if ($col == 1 && $edit_id == $link_id && $edit_id != 0)    $flag_edit = true;
						$row_str.= '<td class=td_1>';

						if ($flag_edit)
						{
								$inp_str = '';

								if ($col > 1 && $col < count($row_res1))			$inp_str = '<input type=text name="sl_frm_'.$obj.'_element_'.$col.'" value="';
								
								if ($obj == 'fields' && $col == 2)					$inp_str = '';
								if ($obj == 'frazes' && $col >= 2 && $col <= 3)		$inp_str = '';
								if ($obj == 'coord_gr' && $col == 2)				$inp_str = '';
								if ($obj == 'tables' && $col == 2)					$inp_str = '';
								if ($obj == 'user_rights' && ($col <= 3 || $col >= 7))	$inp_str = '';
								if ($obj == 'users' && $col >= 10)					$inp_str = '';
								if ($obj == 'makets' && $col >= 12)					$inp_str = '';
								if ($obj == 'point_types' && $col >= 3)				$inp_str = '';
								
								$row_str.= $inp_str;
						}
						
						
						$val = $v1;

						
						if (is_null($v1))	$v1 = '';
						if ($flag_edit)	$val = htmlspecialchars($v1);
						

						if (is_null($val))	$val = '';
						if (!$show_full_text  &&  !$flag_edit && strlen($val) > 12)  $val = mb_substr($val, 0, 12, "utf-8").'...';
						$row_str.= $val;


						if ($flag_edit)
						{
								$inp_str = '';

								if ($col > 1 && $col < count($row_res1))			$inp_str = '" style="background-color: #FFAEC9; border: 0px; width: '.$arr_width[$col-1].'px;" autocomplete="off">';

								if ($obj == 'fields' && $col == 2)					$inp_str = '';
								if ($obj == 'frazes' && $col >= 2 && $col <= 3)		$inp_str = '';
								if ($obj == 'coord_gr' && $col == 2)				$inp_str = '';
								if ($obj == 'tables' && $col == 2)					$inp_str = '';
								if ($obj == 'user_rights' && ($col <= 3 || $col >= 7))	$inp_str = '';
								if ($obj == 'users' && $col >= 10)					$inp_str = '';
								if ($obj == 'makets' && $col >= 12)					$inp_str = '';
								if ($obj == 'point_types' && $col >= 3)				$inp_str = '';

								$row_str.= $inp_str;
						}
						$row_str.= '</td>'."\r\n";

						$col++;
                }
                
                
                $onclick_str1 = '';
                $onclick_str2 = '';
                if ($obj == 'coord_gr')     $onclick_str1 = ' setCoordGR('.$link_id.'); ';
                if ($obj == 'tables')       $onclick_str2 = ' setTables('.$link_id.'); ';
                if ($obj == 'users')        $onclick_str1 = ' setUsers('.$link_id.'); ';
                if ($obj == 'makets')       $onclick_str2 = ' setMakets('.$link_id.'); ';
                if ($obj == 'point_types')  $onclick_str1 = ' setPointTypes('.$link_id.'); ';
                
                $link_str =	'&sl_edit_id='.$link_id.
				'&sl_order_dir='.$order_direction_old.
				'&sl_order_fld_num='.$order_fld_num.
				($user_id > 0 ? '&sl_user_id='.$user_id : '').
				($sel_polz_id > 0 ? '&sl_sel_polz_id='.$sel_polz_id : '').
				($maket_id > 0  ? '&sl_maket_id='.$maket_id : '').
				($m_table_id > 0 ? '&sl_m_table_id='.$m_table_id : '').
				($coord_gr_id > 0 && $m_table_id > 0 ? '&sl_coord_gr_id='.$coord_gr_id.'&sl_m_table_id='.$m_table_id : '').
				($point_type_id > 0 ? '&sl_point_type_id='.$point_type_id : '');
                
                $res.= '<td class=td_1                      onclick=\''.$onclick_str1.$onclick_str2.'showObjTable("tbl_'.$obj.'","'.$obj.'",   "'.$link_str.'");\'   style="cursor: pointer;">                  <img src="img/edit.jpg">            </td>'."\r\n";
                $res.= '<td class=td_1 '.($flag_edit ? '    onclick=\'send_post_param("'.$obj.'","'.'&sl_obj_act=3'.$link_str.'");\' ' : '').                   '    style="cursor: pointer;">'.($flag_edit ?   '<img src="img/save.jpg">' : '').'  </td>'."\r\n";
                $res.= '<td class=td_1                      onclick=\'deleteObject("tbl_'.$obj.'","'.$obj.'",   "'.'&sl_obj_act=2'.$link_str.'");\'               style="cursor: pointer;">                  <img src="img/delete.jpg">          </td>'."\r\n";
                
                $res.= $row_str;

                $res.= '</tr>'."\r\n";
                
                $row++;
        }
        $res.= '</table>'."\r\n";
        
        return $res;
}
?>

<?php
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
<!DOCTYPE html>
<html>
<head>
	<title>АРМ Макет</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8;">
        <meta http-equiv="expires" content="0">
        <meta http-equiv="cashe-control" content="no-cashe">
        <link rel="SHORTCUT ICON" href="../images/train.ico" type="image/x-icon">
        <?php   require_once 'style.css.php';    ?>
</head>
<body>
    
<?php
    $res1 = $link1->query('SELECT `ADMIN` FROM '.$tbl_users.' WHERE `USER_ID`="'.$_SESSION['user_id'].'"');
    $row_res1 = $res1->fetch_assoc();
    $admin = $row_res1['ADMIN'];
    
    if (!in_array($admin, array(1,2)))
    {
            echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';
            exit;
    }
                
?>
    <div class="box_user">
        <p class=p3 style="margin: 0 0 0 0;">Пользователь: <?php    echo $_SESSION['USER_TITLE'];   ?></p>
        <?php   if ($admin == 1 || $admin == 2)   echo '<a class=a1 style="margin: 0 0 0 0;" href="maket_input.php">Ввод макетов</a> / '; ?>
        <a class=a1 style="margin: 0 0 0 0;" href="do_exit.php">Выход</a>
    </div>


<?php
    if ($editor_razdel == 1 && $admin != 1)                         {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
    if ($editor_razdel == 2 && !($admin == 1 ||  $admin == 2))      {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
    if ($editor_razdel == 3 && $admin != 1)                         {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
    if ($editor_razdel == 4 && $admin != 1)                         {   echo '<p class=p3_red>У Вас нет прав на доступ к странице!</p>';    exit;   }
?>
<script>
<?php
    if ($editor_razdel == 1)
    {
        echo 'showObjTable("tbl_makets","makets","1");'."\r\n";
        echo 'showObjTable("tbl_maket_rights",  "maket_rights", "'.($maket_id > 0 ? '&sl_maket_id='.$maket_id : '1').'");'."\r\n";
    }
    if ($editor_razdel == 2)
    {
        echo 'showObjTable("tbl_users","users","1");'."\r\n";
        echo 'showObjTable("tbl_user_rights",  "user_rights", "'.($user_id > 0 ? '&sl_user_id='.$user_id : '1').'");'."\r\n";
    }
    if ($editor_razdel == 3)
    {
        echo 'showObjTable("tbl_coord_gr",     "coord_gr",    "'.($maket_id > 0 ? '&sl_maket_id='.$maket_id : '').'");'."\r\n";
        echo 'showObjTable("tbl_tables",       "tables",      "'.($maket_id > 0 ? '&sl_maket_id='.$maket_id : '').'");'."\r\n";
        echo 'showObjTable("tbl_fields",       "fields",      "'.($m_table_id > 0 ? '&sl_m_table_id='.$m_table_id : '').'");'."\r\n";
        echo 'showObjTable("tbl_frazes",       "frazes",      "'.($coord_gr_id > 0 && $m_table_id > 0 ? '&sl_coord_gr_id='.$coord_gr_id.'&sl_m_table_id='.$m_table_id : '').'");'."\r\n";
    }
    if ($editor_razdel == 4)
    {
        echo 'showObjTable("tbl_point_types","point_types","1");'."\r\n";
        echo 'showObjTable("tbl_points",  "points", "'.($point_type_id > 0 ? '&sl_point_type_id='.$point_type_id : '1').'");'."\r\n";
    }
    
?>
    
    function showObjTable(obj_str1, obj_str2, url_str)
    {
            //alert(url_str);
			//console.log(obj_str1+" "+obj_str2+" "+url_str);
            if (url_str=="")
            {
                //document.getElementById(obj_str1).innerHTML = "<div></div>";
                return;
            }
            var xmlhttp=new XMLHttpRequest();
            xmlhttp.onreadystatechange=function()
            {
                if (this.readyState==4 && this.status==200)     document.getElementById(obj_str1).innerHTML=this.responseText;
            }
            xmlhttp.open("GET","show_obj_inf.php?sl_obj=" + obj_str2 + "&" + url_str,true);
            xmlhttp.send();
    }
    function deleteObject(obj_str1, obj_str2, url_str)
    {
        if (obj_str2 == 'users' &&       window.confirm("Вы действительно хотите удалить пользователя?"))            showObjTable(obj_str1, obj_str2, url_str);
        if (obj_str2 == 'makets' &&      window.confirm("Вы действительно хотите удалить макет?"))                   showObjTable(obj_str1, obj_str2, url_str);
        if (obj_str2 == 'coord_gr' &&    window.confirm("Вы действительно хотите удалить координатную группу?"))     <?php   echo 'showObjTable(obj_str1, obj_str2, url_str );'."\r\n";    ?>   
        if (obj_str2 == 'tables' &&      window.confirm("Вы действительно хотите удалить таблицу?"))                 <?php   echo 'showObjTable(obj_str1, obj_str2, url_str );'."\r\n";    ?>
        if (obj_str2 == 'frazes' &&      window.confirm("Вы действительно хотите удалить фразу?"))                   <?php   echo 'showObjTable(obj_str1, obj_str2, url_str );'."\r\n";    ?>
        if (obj_str2 == 'fields' &&      window.confirm("Вы действительно хотите удалить поле?"))                    <?php   echo 'showObjTable(obj_str1, obj_str2, url_str );'."\r\n";    ?>
        if (obj_str2 == 'user_rights' && window.confirm("Вы действительно хотите удалить права пользователя?"))      <?php   echo 'showObjTable(obj_str1, obj_str2, url_str );'."\r\n";    ?>
        if (obj_str2 == 'point_types' && window.confirm("Вы действительно хотите удалить тип КПП?"))                 <?php   echo 'showObjTable(obj_str1, obj_str2, url_str );'."\r\n";    ?>
        if (obj_str2 == 'points' &&      window.confirm("Вы действительно хотите удалить КПП?"))                     <?php   echo 'showObjTable(obj_str1, obj_str2, url_str );'."\r\n";    ?>
    }
    
    
    function setCoordGR(id)
    {
                    document.getElementById("selected_coord_gr_id").value = id;
        tables_id = document.getElementById("selected_table_id").value;
        
        if (           tables_id > 0) showObjTable("tbl_fields",  "fields",  "&sl_m_table_id="+tables_id);
        if (id > 0  && tables_id > 0) showObjTable("tbl_frazes",  "frazes",  "&sl_m_table_id="+tables_id+"&sl_coord_gr_id="+id);
        //alert(id);
    }
    function setTables(id)
    {
                      document.getElementById("selected_table_id").value = id;
        coord_gr_id = document.getElementById("selected_coord_gr_id").value;
        
        if (id > 0)                     showObjTable("tbl_fields",  "fields",  "&sl_m_table_id="+id);
        if (id > 0  && coord_gr_id > 0) showObjTable("tbl_frazes",  "frazes",  "&sl_m_table_id="+id+"&sl_coord_gr_id="+coord_gr_id);
        //alert(id);
    }
    function setUsers(id)
    {
	//console.log("trr");
			document.getElementById("selected_polz_id").value = id;
        sel_maket_id = document.getElementById("selected_maket_id").value;
	
	//alert(id);
	
        showObjTable("tbl_user_rights",  "user_rights",  "&sl_sel_polz_id="+id+"&sl_maket_id="+sel_maket_id);
        //document.getElementById("drop_down_maket_list").selectedIndex = "0";
    }
    function setMakets(id)
    {
	//console.log("function setMakets");
			document.getElementById("selected_maket_id").value = id;
        sel_polz_id = document.getElementById("selected_polz_id").value;
        
	//alert("&sl_maket_id="+id);
        showObjTable("tbl_maket_rights",  "maket_rights",  "&sl_maket_id="+id+"&sl_sel_polz_id=" + sel_polz_id);
    }
    function setPointTypes(id)
    {
        showObjTable("tbl_points",  "points",  "&sl_point_type_id="+id);
    }
    

    function send_post_param(obj, url_str)
    {
        var elements = document.getElementsByTagName("input");
        //var elements = document.forms["save_form_" + obj].getElementsByTagName("input");
        //var frm = document.getElementById("save_form_" + obj).elements;
        //alert(elements.length);
        //alert("sdf");
        str = "";
        if (obj == 'makets')     str = "?&sl_obj=makets";
        if (obj == 'users')      str = "?&sl_obj=users";
        if (obj == 'coord_gr')   str = "?&sl_obj=coord_gr";
        if (obj == 'tables')     str = "?&sl_obj=tables";
        if (obj == 'fields')     str = "?&sl_obj=fields";
        if (obj == 'frazes')     str = "?&sl_obj=frazes";
        if (obj == 'user_rights')str = "?&sl_obj=user_rights";
        if (obj == 'point_types')str = "?&sl_obj=point_types";
        if (obj == 'points')     str = "?&sl_obj=points";
        
        str = str + url_str;
        
        for (var i = 0; i < elements.length; i++)
        {
            if (elements[i].type == "text"   && elements[i].name.indexOf("sl_frm_" + obj + "_element_") == 0)
            {
                //alert(i + '       ' + elements[i].name + '=' + elements[i].value);
                str = str + '&' + encodeURIComponent(elements[i].name) + '=' + encodeURIComponent(elements[i].value);
                //Do something here
            }
        }
        
        
        //var params = typeof str == 'string' ? str : Object.keys(str).map(
        //    function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(str[k]) }
        //).join('&');
		params = str;
        
        var xhr = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
        xhr.open('POST', "show_obj_inf.php");
        xhr.onreadystatechange = function() {
            if (xhr.readyState>3 && xhr.status==200)
            {
                    //success(xhr.responseText); 
            }
        };
        //xhr.setRequestHeader("Accept-Charset", "windows-1251"); // ralp add 3 str: 
        xhr.setRequestHeader("Accept-Language","ru, en");	  
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); 
        xhr.send(params);
        //console.log(params);
    }
</script>
    
    
  



<table border="0" style="width: 100%;">
<tr>
<td style="vertical-align: top;" colspan="2">
    <p class="a3" style="margin: 0 0 0 0; padding: 0 0 0 0;">
    <?php   if ($admin == 1)                echo '<a class="'.($editor_razdel == 1 ? 'a3_underl' : 'a3').'" href="?sl_editor_razdel=1">Макеты</a> / ';   ?>
    <?php   if ($admin == 1 || $admin == 2) echo '<a class="'.($editor_razdel == 2 ? 'a3_underl' : 'a3').'" href="?sl_editor_razdel=2">Пользователи</a>';?>
    <?php   if ($admin == 1)                echo ' / ';     ?>
    <?php   if ($admin == 1)                echo '<a class="'.($editor_razdel == 3 ? 'a3_underl' : 'a3').'" href="?sl_editor_razdel=3">Структура</a> / ';?>
    <?php   if ($admin == 1)                echo '<a class="'.($editor_razdel == 4 ? 'a3_underl' : 'a3').'" href="?sl_editor_razdel=4">КПП</a>';         ?>
    </p>

</td>
</tr>
<tr>
<td style="vertical-align: top; width: 180px; text-align: center;">    
<?php
if ($editor_razdel == 1)
{
    echo '<input type="hidden" id="selected_polz_id" value="0">'."\r\n";
    echo '<input type="hidden" id="selected_maket_id" value="0">'."\r\n";
    
    
    echo '<table border=0 style="width: 100%;">'."\r\n";
    echo '<tr>'."\r\n";
    echo '<td style="vertical-align: top; width: 50%;">'."\r\n";
    echo '  <div id=tbl_makets></div>';
    echo '</td>'."\r\n";
    echo '<td style="vertical-align: top;  width: 50%;">'."\r\n";
    
            echo '<select onchange=\'document.getElementById("selected_polz_id").value=this.value;setMakets(document.getElementById("selected_maket_id").value);\' id=drop_down_maket_list>';
            echo '<option value="0">Все пользователи</option>';
            $res1 = $link1->query('SELECT * FROM '.$tbl_users.' ORDER BY `USER_NAME`');
            while ($row_res1 = $res1->fetch_assoc())
            {
                    $id = $row_res1['USER_ID'];
                    $user_name = $row_res1['USER_NAME'];
                    
                    echo '<option value="'.$id.'">'.$user_name.'</option>';
            }
            echo '</select>';
            echo '  <div id=tbl_maket_rights></div>';
    
    echo '</td>'."\r\n";
    echo '</tr>'."\r\n";
    echo '</table>'."\r\n";
}
if ($editor_razdel == 2)
{
    echo '<form method=post action="maket_editor.php">';
    echo '<input type=text id="gen_user_pass" name="sl_user_pass" style="width: 300px;" value="'.(isset($_POST['sl_user_pass']) ? hash("sha256", $_POST['sl_user_pass']) : '').'">';
    echo ' <input type=submit value="Получить хеш пароля">';
    echo ' <input type=button value="Сгенерировать пароль" onclick=\'genPassword();\'>';
    //echo '<input type=hidden name="sl_edit_id" value="'.$edit_id.'">';
    //echo '<input type=hidden name="sl_user_id" value="'.$user_id.'">';
    echo '<input type=hidden name="sl_editor_razdel" value="2">';
    echo '</form>';
    
    echo '<input type="hidden" id="selected_polz_id" value="0">'."\r\n";
    echo '<input type="hidden" id="selected_maket_id" value="0">'."\r\n";
    
    
    echo '<table border=0 style="width: 100%;">'."\r\n";
    echo '<tr>'."\r\n";
    echo '<td style="vertical-align: top; width: 50%;">'."\r\n";
    echo '  <div id=tbl_users></div>';
    echo '</td>'."\r\n";
    echo '<td style="vertical-align: top;  width: 50%;">'."\r\n";
    
            echo '<select onchange=\'document.getElementById("selected_maket_id").value=this.value;setUsers(document.getElementById("selected_polz_id").value);\' id=drop_down_maket_list>';
            echo '<option value="0">Все макеты</option>';
            $res1 = $link1->query('SELECT * FROM '.$tbl_makets.' ORDER BY `MOCKUP_CODE`');
            while ($row_res1 = $res1->fetch_assoc())
            {
                    $id = $row_res1['MOCKUP_ID'];
                    $code = $row_res1['MOCKUP_CODE'];

                    echo '<option value="'.$id.'">'.$code.'</option>';
            }
            echo '</select>';
            echo '  <div id=tbl_user_rights></div>';
    
    echo '</td>'."\r\n";
    echo '</tr>'."\r\n";
    echo '</table>'."\r\n";
}
if ($editor_razdel == 3)
{
        echo '<input type="hidden" id="selected_coord_gr_id" value="">'."\r\n";
        echo '<input type="hidden" id="selected_table_id" value="">'."\r\n";
        
        
        echo '<table border=0 style="width: 100%;">'."\r\n";
        echo '<tr>'."\r\n";
        echo '<td rowspan=3 style="vertical-align: top; width: 100px;">'."\r\n";
        echo '<p class=p1>Макеты:</p>';
        
        $res1 = $link1->query('SELECT * FROM '.$tbl_makets.' ORDER BY `MOCKUP_CODE`');
        while ($row_res1 = $res1->fetch_assoc())
        {
                $id = $row_res1['MOCKUP_ID'];
                $code = $row_res1['MOCKUP_CODE'];

                echo '<a class=a1 href="?sl_maket_id='.$id.'&sl_editor_razdel=3" style="'.($id == $maket_id ? 'font-weight: bold; font-size: 16px;' : '').'">'.$code.'</a><br>'."\r\n";
        }
        echo '</td>'."\r\n";
        echo '<td style="width: 50%; height: 150px; vertical-align: '.($maket_id > 0 ? 'top' : 'middle').';">'."\r\n";
        if ($maket_id > 0)
        {
            //группы
            echo '<div id=tbl_coord_gr></div>'."\r\n";
        }
        else
        {
            echo '<p class=p1 style="vertical-align: middle; width: 100%; text-align: center;">Выберите макет в списке слева.</p>'."\r\n";
        }
        echo '</td>'."\r\n";
        echo '<td style="vertical-align: top; width: 50%; ">'."\r\n";
        if ($maket_id > 0)            echo '<div id=tbl_tables></div>'."\r\n";
        echo '</td>'."\r\n";
        echo '</tr>'."\r\n";
        echo '<tr>'."\r\n";
        echo '<td colspan=2  style="vertical-align: top;">'."\r\n";
        if ($maket_id > 0)            echo '<div id=tbl_fields></div>'."\r\n";
        if ($maket_id > 0)            echo '<div id=tbl_frazes></div>'."\r\n";
        echo '</td>'."\r\n";
        echo '</tr>'."\r\n";
        echo '</table>'."\r\n";
}
if ($editor_razdel == 4)
{
    echo '<table border=0 style="width: 100%;">'."\r\n";
    echo '<tr>'."\r\n";
    echo '<td style="vertical-align: top; width: 50%;">'."\r\n";
    echo '  <div id=tbl_point_types></div>';
    echo '</td>'."\r\n";
    echo '<td style="vertical-align: top;  width: 50%;">'."\r\n";
    echo '  <div id=tbl_points></div>';
    echo '</td>'."\r\n";
    echo '</tr>'."\r\n";
    echo '</table>'."\r\n";
}
?>
    
</td>
</tr>
</table>

<script>
    function genPassword()
    {
        var chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';       //!@#$%^&*()
        var passwordLength = 8;
        var password = '';
        for (var i = 0; i <= passwordLength; i++)
        {
            var randomNumber = Math.floor(Math.random() * chars.length);
            password += chars.substring(randomNumber, randomNumber +1);
        }
        document.getElementById('gen_user_pass').value = password;
    }
</script>
   
</body>
</html>


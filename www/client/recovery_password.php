<?php
require_once 'sys_vars.php';    
require_once 'sys_con_db.php';
require_once __DIR__.'/boot.php';



//если поступил запрос на смену пароля
if (isset($_POST['sl_email']))
{
		$eml_adr = $_POST['sl_email'];
		
		if ($stmt = $link1->prepare("SELECT `USER_ID`,`USER_TITLE` FROM ".$tbl_users." WHERE `email` = ?"))
		{
			$stmt->bind_param('s', $eml_adr);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($user_id, $user_title);
			//error_log($user_id);
		}
		if ($stmt->num_rows == 0)
		{
			flash('Пользователь с электронной почтой '.htmlentities($eml_adr).' не зарегистрирован!');
			header('Location: '.$_SERVER['PHP_SELF']);
			die;
		}
		if ($stmt->num_rows > 1)
		{
			flash('Найдено несколько пользователей с электронной почтой '.htmlentities($eml_adr).'! Пароль не может быть сменен. Обратитесь к администратору.');
			header('Location: '.$_SERVER['PHP_SELF']);
			die;
		}

		$stmt->fetch();

		if ($stmt->num_rows == 1)
		{
				$uniq_id = bin2hex(openssl_random_pseudo_bytes(40));

				$to  = $eml_adr;
				$subject = "Смена пароля в АРМ Макет";
				$message = 'Вы получили это письмо, потому что в АРМ Макет был запрос на смену пароля для пользователя '.$user_title.'.<br> ';
				$message.= 'Если Вам необходимо сменить пароль, пройдите по ссылке ';
				$message.= 'http://esrr-skull.esrr.oao.rzd/arm_maket/recovery_password.php?sl_recovery_id='.$uniq_id;
				$message.= '<br><br>';
				$message.= 'Срок действия ссылки - 1 час.';
				$message.= '<br><br>';
				$message.= 'Если Вы не делали запрос на смену пароля, просто удалите это сообщение.';
				$message.= '<br><br>';
				$message.= 'Письмо сформировано автоматически, не нужно на него отвечать.';
				$headers  = "Content-type: text/html; charset=utf-8; \r\n"; 
				$headers .= "From: APM MAKET <ivc_halitovdp@esrr.rzd>\r\n"; 
				$headers .= "Reply-To: ivc_halitovdp@esrr.rzd\r\n";
				mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, $headers);

				if ($stmt2 = $link1->prepare("INSERT INTO ".$tbl_recovery_pass." (`email`,`uniq_id`) VALUES (?,?)"))
				{
					$stmt2->bind_param("ss", $eml_adr, $uniq_id);
					$stmt2->execute();
				}

				flash_green('Информация о смене пароля направлена на электронный адрес '.htmlentities($eml_adr));
				header('Location: index.php');
				die;
		}
}


//если пользователь подтвердил, что хочет сменить пароль
if (isset($_GET['sl_recovery_id']))
{
		$uniq_id = $_GET['sl_recovery_id'];
		$pass = randomPassword(10);
		$pass_hash = hash("sha256", $pass);
				
		
		$mkt1 = mktime(date("H")-1, date("i"), date("s"), date("m"), date("d"), date("Y"));
				
		
		//получаем email пользователя из таблицы восстановления паролей
		if ($stmt = $link1->prepare("SELECT `email`,`changed`,`time_ins` FROM ".$tbl_recovery_pass." WHERE `uniq_id` = ?"))// AND `changed` IS NULL AND `time_ins`>='".date('Y-m-d H:i:s', $mkt1)."'
		{
			$stmt->bind_param('s', $uniq_id);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($eml_adr, $changed, $time_ins);
			$stmt->fetch();
			//error_log($user_id);
		}
		if ($stmt->num_rows == 1)
		{
				if (date('Y-m-d H:i:s', $mkt1) > $time_ins)
				{
					flash('Срок действия ссылки закончился.');
					header('Location: index.php');
					die;
				}
				if ($changed == '1')
				{
					flash('Ссылка уже была использована ранее.');
					header('Location: index.php');
					die;
				}
				
				//получаем ID пользователя и логин по email
				if ($stmt = $link1->prepare("SELECT `USER_ID`,`USER_NAME` FROM ".$tbl_users." WHERE `email` = ?"))
				{
					$stmt->bind_param('s', $eml_adr);
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result($user_id, $user_name);
					$stmt->fetch();
					//error_log($user_id);
				}
				//меняем пароль
				if ($stmt = $link1->prepare("UPDATE ".$tbl_users." SET `USER_PASSWORD` = ? WHERE `USER_ID` = ?"))
				{
					$stmt->bind_param("si", $pass_hash, $user_id);
					$stmt->execute();
					
					
					$to  = $eml_adr;
					$subject = "АРМ Макет";
					$message = 'Логин: '.$user_name.'<br>';
					$message.= 'Новый пароль: '.$pass;
					$headers  = "Content-type: text/html; charset=utf-8; \r\n"; 
					$headers .= "From: APM MAKET <ivc_halitovdp@esrr.rzd>\r\n"; 
					$headers .= "Reply-To: ivc_halitovdp@esrr.rzd\r\n";
					mail($to, '=?utf-8?B?'.base64_encode($subject).'?=', $message, $headers);
					
					
					//делаем отметку в таблице восстановления паролей
					if ($stmt = $link1->prepare("UPDATE ".$tbl_recovery_pass." SET `changed`=1 WHERE `uniq_id` = ?"))
					{
						$stmt->bind_param("s", $uniq_id);
						$stmt->execute();
					}
					
					
					flash_green('Пароль успешно изменен, отправлен на электронную почту.');
					header('Location: index.php');
					die;
				}
		}
		else
		{
				flash('Не удалось произвести смену пароля.');
		}
}


function randomPassword($pass_length)
{
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < $pass_length; $i++)
		{
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		
		return implode($pass); //turn the array into a string
}

?>
<!DOCTYPE html>
<html>
<head>
	<title>АРМ Макет</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8;">
	<meta http-equiv="expires" content="0">
	<meta http-equiv="cashe-control" content="no-cashe">
	<?php   require_once 'style.css.php';    ?>
<style>
ul {
    font-size: 0;
	/*
	background-color: yellow;
	*/
}
li { 
    display: inline-block;
    width: 100px;
    font-size: 30px;
    text-align: center;
	/*
	background-color: yellow;
	*/
}
li:nth-child(1) {
	width: 190px;
	/*
	background-color: green;
	*/
}
li:nth-child(2) {
    width: 370px;
	text-align: right;
	/*
	background-color: red;
	*/
}
</style>
</head>
<body style="height: 100%;">
    
    <div class="parent">
    <div class="block">
        <div style="width: 100%; text-align: center;">
            <h1 class="h1">АРМ МАКЕТ</h1>
        </div>
		<?php flash() ?>
		<?php flash_green() ?>
        <form method="post" action="">            
            <div>
              <label class="h2" for="username">Введите адрес электронной почты:</label>
              <input type="text" name="sl_email"  required class="form-control">
            </div>
			<ul>
				<li><button type="submit" class="btn-primary2">Сменить пароль</button></li>
				<li class="h2"><a href="index.php" style="text-decoration: none; color: #0055dd; font-size: 20px; font-family: Arial;">Вернуться к авторизации</a></li>
			</ul>
        </form>

    </div>
    </div>

</body>
</html>
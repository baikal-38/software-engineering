<?php
require_once 'sys_vars.php';    
require_once 'sys_con_db.php';
require_once __DIR__.'/boot.php';


if ($stmt = $link1->prepare("SELECT `USER_ID`,`USER_NAME`,`USER_TITLE`,`USER_PASSWORD`,`email`  FROM ".$tbl_users." WHERE `USER_NAME` = ?"))
{
    $stmt->bind_param('s', $_POST['username']);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $user_name, $user_title, $user_password, $user_email);
}
if ($stmt->num_rows == 0)
{
    flash('Пользователь с такими данными не зарегистрирован!');
    header('Location: index.php');
    die;
}
$stmt->fetch();


//if (password_verify($_POST['password'], $user['password']))
if (hash("sha256", $_POST['password']) == $user_password)
{
    /*
    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT))
    {
        $newHash = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $stmt = $conn_pdo->prepare('UPDATE `users` SET `password` = :password WHERE `username` = :username');
        $stmt->bind_param("ss", $_POST['username'], $newHash);
        $stmt->execute();
    }
    */
    $_SESSION['user_id'] = $user_id;
    $_SESSION['USER_NAME'] = $user_name;
    $_SESSION['USER_TITLE'] = $user_title;
    $_SESSION['USER_email'] = $user_email;
    header('Location: maket_input.php');
    die;
}

flash('Логин/пароль неверен!');
header('Location: index.php');

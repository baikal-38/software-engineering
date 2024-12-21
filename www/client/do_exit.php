<?php
require_once 'sys_vars.php';    
require_once 'sys_con_db.php';
require_once __DIR__.'/boot.php';

unset($_SESSION['user_id']);

header('Location: index.php');

?>
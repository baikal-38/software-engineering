<?php
set_time_limit(120);

//error_log(session_id());
    
session_start();


require_once 'visit_write.php';


function flash($message = null)
{
    if ($message)
    {
        $_SESSION['flash'] = $message;
    }
    else
    {
        if (!empty($_SESSION['flash']))
        {
            echo '<div class=box_error>';
            echo $_SESSION['flash'];
            echo '</div>';
        }
        unset($_SESSION['flash']);
    }
}

function flash_green($message = null)
{
    if ($message)
    {
        $_SESSION['flash_green'] = $message;
    }
    else
    {
        if (!empty($_SESSION['flash_green']))
        {
            echo '<div class=box_info>';
            echo $_SESSION['flash_green'];
            echo '</div>';
        }
        unset($_SESSION['flash_green']);
    }
}

function check_auth()
{
    //return (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : false);
    return (isset($_SESSION['user_id']) ? true : false);
}

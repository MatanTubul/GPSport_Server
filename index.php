<?php
/**
 * Created by PhpStorm.
 * User: Nir B
 * Date: 08/16/2015
 * Time: 15:24
 */

error_reporting(0);
require 'connection.php';

$tag = $_POST['tag'];
switch ($tag){
    case "login":
    {
        include 'login.php';
        $login = new Login();
        echo ($login->dataProcess($dblink));
        break;
    }
    case "signup":
    {
        include 'register.php';
        $register = new Register();
        echo ($register->dataProcess ($dblink));
        break;
    }
    case 'forgotpassword':
    {
        include 'forgot_password.php';
        $forgotpassword = new ForgotPassword();
        echo ($forgotpassword->dataProcess ($dblink));
        break;
    }
//case others...
}

$dblink->close();

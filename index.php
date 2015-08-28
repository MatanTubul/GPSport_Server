<?php
/**
 * Created by PhpStorm.
 * User: Nir B
 * Date: 08/16/2015
 * Time: 15:24
 */

error_reporting(0);
require 'connection.php';
$output = array();

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
    case "logout":
        $user = $_POST["username"];
        $logutres = mysqli_query($dblink,"UPDATE users SET userstatus = '0' WHERE users.email= '$user'");
        if(!$logutres)
        {
            $output["flag"] = "query failed";
        }else{
            $output["flag"] = "user logged out";
        }
        print(json_encode($output));

        break;
//case others...
}

$dblink->close();

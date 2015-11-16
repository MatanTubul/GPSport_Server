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
    case "logout": {
        $user = $_POST["username"];
        $logoutres = mysqli_query($dblink, "UPDATE users SET userstatus = '0' WHERE users.email= '$user'");
        if (!$logoutres) {
            $output["flag"] = "query failed";
        } else {
            $output["flag"] = "user logged out";
        }
        print(json_encode($output));
        break;
    }

    case "create_event": {
        include 'create_event.php';
        $create = new CreateEvent();
        echo($create ->dataProcess($dblink));
        break;
    }

    case "profile": {
        include 'update_profile.php';
        $profile = new UpdateProfile();
        echo($profile ->dataProcess($dblink));
        break;
    }

    case "search_user": {
        include 'search_user.php';
        $users = new SearchUser();
        echo($users->dataProcess($dblink));
        break;
    }
    case "response_invited_user": {
        include_once 'invited_user.php';
        $invited_user = new invited_user();
        echo($invited_user ->dataProcess($dblink));
        break;
    }
    case "get_event":
    {
        include_once 'get_events.php';
        $getEvent = new get_events();
        echo($getEvent -> dataProcess($dblink));
        break;
    }
    case "delete_event":
    {
        include_once 'delete_event.php';
        $dlevent = new delete_event();
        echo($dlevent -> dataProcess($dblink));
        break;
    }

//case others...
}

$dblink->close();

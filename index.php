<?php
/**
 * Created by PhpStorm.
 * User: Nir B
 * Date: 08/16/2015
 * Time: 15:24
 */

error_reporting(0);
require 'connection.php';
require_once 'DBFunctions.php';
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
        $dbF = new DBFunctions($dblink);
        $logoutres = $dbF -> LogOutUser($user);
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
        $inviteduser = new invited_user();
        echo($inviteduser ->dataProcess($dblink));
        break;
    }
    case "get_event":
    {
        include_once 'get_events.php';
        $getevents = new get_events();
        echo($getevents -> dataProcess($dblink));
        break;
    }
    case "delete_event":
    {
        include_once 'delete_event.php';
        $deleteevents = new delete_event();
        echo($deleteevents -> dataProcess($dblink));
        break;
    }
    case "search_events":
    {
        include 'search_events_by_default.php';
        $searchevents = new search_events();
        echo($searchevents -> dataProcess($dblink));
        break;
    }
    case "get_participating_event_list":{
        include_once "GetEventsByUser.php";
        $getparticipantsinevents = new GetEventsByUser();
        echo($getparticipantsinevents -> dataProcess($dblink));
        break;
    }
    case "remove_event_manager":
    {
        require_once "delete_manager.php";
        $removemanager = new delete_manager();
        echo($removemanager ->dataProcess($dblink));
        break;
    }
    case "remove_participant":
    {
        require_once "remove_participant.php";
        $remove_participant = new remove_participant();
        echo($remove_participant ->dataProcess($dblink));
        break;
    }

    case "get_event_users":
    {
        require_once "get_event_users.php";
        $eventusers = new get_event_users();
        echo($eventusers ->dataProcess($dblink));
        break;
    }





//case others...
}

$dblink->close();

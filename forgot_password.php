<?php
/**
 * Created by PhpStorm.
 * User: matant
 * Date: 8/10/2015
 * Time: 2:14 PM
 */
error_reporting(0);
$tag = $_POST['tag'];
$output = array();
$con = mysql_connect('localhost','root','');
if (!$con)
{
    die('could not connect:'.mysql_error());
}
mysql_select_db('gpsport',$con);
if($tag == "forgotpassword")
{
    $email = $_POST["email"];
    $query = "SELECT password FROM users where users.email = '$email'";
    $res = mysql_query($query);
    if(!$res)
    {
        $output["msg"] = "query failed";
        print(json_encode($output));
    }elseif(mysql_num_rows($res)!= 1 ){

        $output["msg"] = "Account not found please signup now";
        print(json_encode($output));
    }

    else{
        $row = mysql_fetch_assoc($result);
        $recoverdpassword = $row["password"];
        $message = "You ask to recover your password, if in case you didn't ask ignore this mail.";
        $to = $email;
        $subject = "Forgotten Password";
        $from = 'gpsportg@gmail.com';
        mail($to,$subject,$message);
        $output["msg"] = "Password Recovered";
        $output["email"]=$email;
        print(json_encode($output));
    }

}
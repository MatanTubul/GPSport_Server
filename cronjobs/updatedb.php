<?php
/**
 * Created by PhpStorm.
 * User: matant
 * Date: 11/17/2015
 * Time: 1:46 PM
 */
define ('mysql_host','pdb3.biz.nf');
define ('mysql_user','1934398_gpsport');
define ('mysql_password','gpsportBraude15');
define ('myDB','1934398_gpsport');
define ("GOOGLE_API_KEY","AIzaSyBSGW3kNZ_GNsBsTdJBKsyAbcTfaqv3uvo");

$dblink= mysqli_connect(mysql_host, mysql_user, mysql_password,myDB);


if (!$dblink)
{
    $message = sprintf(
        "Could not connect to local database: %s",
        mysql_error()
    );
    trigger_error($message);
    echo $message;
    return;
}
else{
    echo "connection success"."<br/>";
}
$c_date = date("Y-m-d");
$c_time = date("h:i:sa");
date_default_timezone_set('Asia/Jerusalem');
$c_time = date("h:i:sa");
echo $my_time."<br/>";
echo $c_time."<br/>";;
$query = "UPDATE event SET event_status = '0' WHERE event.event_date < '$c_date' OR '$c_time' >  event.start_time";
$res = mysqli_query($dblink,$query) or die (mysqli_error($dblink));
if(!$res){
    echo "failed to update event!"."<br/>";
    echo $my_time."<br/>";
}
else{
    echo "event updated successfully";
}
$dblink ->close();
?>
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


$dblink= mysqli_connect(mysql_host, mysql_user, mysql_password,myDB);
$logmessage;

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
else {
    $c_date = date("Y-m-d");
    date_default_timezone_set('Asia/Jerusalem');
    $c_time = date("Y-m-d G:i:s");
    echo $c_time."<br/>";
    $query = "UPDATE event SET event.event_status = '0' WHERE (event.event_date < '$c_date' OR '$c_time' > event.start_time)";
    $res = mysqli_query($dblink, $query) or die (mysqli_error($dblink));
    $affected_row = mysqli_affected_rows($dblink);
    if (!$res) {
        $logmessage = $c_time . ":failed to update event!";
    } else {
        $logmessage = $c_time . "  :event updated successfully numbers of rows that affected:".$affected_row;
    }
}
$filename = "updatelog.txt";
    if(file_exists($filename)) {
        file_put_contents($filename, $logmessage."\n",FILE_APPEND);
        echo "append";
    } else {
        $handle = fopen($filename, 'w+') or die("Unable to open file!");
        fwrite($handle, $logmessage);
        fclose($handle);
        echo "create";
    }

$dblink ->close();
?>
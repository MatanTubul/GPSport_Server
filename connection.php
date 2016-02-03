<?php
/**
 * Created by PhpStorm.
 * class that create a connection to the DB
 * User: Nir B
 * Date: 17/08/2015
 * Time: 09:39
 */


define ('mysql_host','localhost');
define ('mysql_user','root');
define ('mysql_password','');
define ('myDB','1934398_gpsport');


$dblink = mysqli_connect(mysql_host, mysql_user, mysql_password,myDB);
if (!$dblink)
{
    $message = sprintf(
        "Could not connect to local database: %s",
        mysql_error()
    );
    trigger_error($message);
    return;
}
if(!mysqli_set_charset($dblink, 'utf8')) {
    echo 'the connection is not in utf8';
    exit();
}
$resmysqli=mysqli_select_db($dblink,myDB);


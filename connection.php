<?php
/**
 * Created by PhpStorm.
 * User: Nir B
 * Date: 17/08/2015
 * Time: 09:39
 */

define ('mysql_host','localhost');
define ('mysql_user','root');
define ('mysql_password','');
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
    return;
}
$resmysqli=mysqli_select_db($dblink,myDB);

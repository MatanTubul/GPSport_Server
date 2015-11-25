<?php
/**
 * this script will update events that enabled the scheduling mode by manipulating the DB
 * Created by PhpStorm.
 * User: matant
 * Date: 11/25/2015
 * Time: 1:09 PM
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
    $update_sched_column = "UPDATE events
                                 SET  events.scheduled = (
                                 CASE WHEN sched_exp_type = 'counter' and events.sched_counter <= '0' THEN '0'
                                      WHEN sched_exp_type = 'date' and events.sched_expired < '$c_date' THEN '0'
                                      ELSE (events.scheduled)
                                 END)";

    $res_sched_column = mysqli_query($dblink, $update_sched_column) or die (mysqli_error($dblink));

    $sched_query = "UPDATE events SET events.start_time = (
                    CASE sched_exp_type
                        WHEN 'unlimited' THEN
                              CASE sched_type
                                WHEN 'Daily' THEN (DATE_ADD(events.start_time, interval 1*events.sched_duration day))
                                WHEN 'Weekly' THEN (DATE_ADD(events.start_time, interval 7*events.sched_duration day))
                                WHEN 'Monthly' THEN  (DATE_ADD(events.start_time, interval events.sched_duration MONTH ))
                                ELSE NULL
                              END
                        WHEN 'date'  THEN
                              CASE sched_type
                                WHEN 'Daily' and '$c_date' < sched_expired  THEN (DATE_ADD(events.start_time, interval 1*events.sched_duration day))
                                WHEN 'Weekly' and '$c_date' < sched_expired  THEN (DATE_ADD(events.start_time, interval 7*events.sched_duration day))
                                WHEN 'Monthly' THEN  (DATE_ADD(events.start_time, interval events.sched_duration MONTH )AND DATE_ADD(events.end_time, interval events.sched_duration MONTH ))
                                ELSE NULL
                              END
                        WHEN 'counter' THEN
                              CASE sched_type
                                 WHEN 'Daily' and '$c_date' < sched_expired  THEN (DATE_ADD(events.start_time, interval 1*events.sched_duration day))
                                 WHEN 'Weekly' and '$c_date' < sched_expired  THEN (DATE_ADD(events.start_time, interval 7*events.sched_duration day))
                                 WHEN 'Monthly' THEN  (DATE_ADD(events.start_time, interval events.sched_duration MONTH ))
                                 ELSE NULL
                              END
                    END )
                     ,events.end_time = (
                             CASE sched_exp_type
                                WHEN 'unlimited' THEN
                                     CASE sched_type
                                        WHEN 'Daily' THEN  (DATE_ADD(events.end_time, interval 1*events.sched_duration day) )
                                        WHEN 'Weekly' THEN  (DATE_ADD(events.end_time, interval 7*events.sched_duration day))
                                        WHEN 'Monthly' THEN  (DATE_ADD(events.end_time, interval events.sched_duration MONTH ))
                                        ELSE NULL
                                      END
                                WHEN 'date'  THEN
                                      CASE sched_type
                                        WHEN 'Daily' and '$c_date' < sched_expired  THEN (DATE_ADD(events.end_time, interval 1*events.sched_duration day))
                                        WHEN 'Weekly' and '$c_date' < sched_expired  THEN (DATE_ADD(events.end_time, interval 7*events.sched_duration day))
                                        WHEN 'Monthly' and '$c_date' < sched_expired THEN  (DATE_ADD(events.end_time, interval events.sched_duration MONTH ))
                                        ELSE NULL
                                      END
                                WHEN 'counter' THEN
                                        CASE sched_type
                                         WHEN 'Daily' and '0' < sched_counter  THEN (DATE_ADD(events.end_time, interval 1*events.sched_duration day) )
                                         WHEN 'Weekly' and '0' < sched_counter  THEN (DATE_ADD(events.end_time, interval 7*events.sched_duration day))
                                         WHEN 'Monthly' and '0' < sched_counter THEN  (DATE_ADD(events.end_time, interval events.sched_duration MONTH ))
                                         ELSE NULL
                                      END
                             END ),
                      events.event_status = '1'
                     WHERE events.scheduled = '1' and events.event_status = '0' ";
}
    echo $sched_query."<br/>";
    $res_sched_query = mysqli_query($dblink, $sched_query) or die (mysqli_error($dblink));
    echo "<br/>".$res_sched_query."<br/>";
$dblink ->close();
?>

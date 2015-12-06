<?php
/**
 * Created by PhpStorm.
 * User: nirb
 * Date: 11/18/2015
 * Time: 5:47 PM
 */

include 'response_process.php';
require_once 'DBFunctions.php';

class search_events implements ResponseProcess
{

    public function dataProcess($dblink)
    {
        $output = array();
        $dbF = new DBFunctions($dblink);

        $user_lat = floatval($_POST["lat"]);
        $user_long = floatval($_POST["lon"]);
        $radius = floatval($_POST["radius"]);

        date_default_timezone_set('Asia/Jerusalem');

        $current_time = date('Y-m-d H:i:s');

        $mng_id = $_POST["manager_id"];
        //$result_q = $dbF ->getEventsManagedById($mng_id);

        $event_query = "SELECT * from events WHERE acos(sin(events.latitude * 0.0175) * sin('$user_lat' * 0.0175)
        + cos(events.latitude * 0.0175) * cos('$user_lat' * 0.0175) *
        cos(('$user_long' * 0.0175) - (events.longitude * 0.0175))) * 6371 <= '$radius'
        AND events.event_status = '1' AND
        DATE(events.start_time) = DATE('$current_time') AND
        TIME(events.start_time) > TIME('$current_time')";

        $result_q = mysqli_query($dblink, $event_query) or die (mysqli_error($dblink));

        if (!$result_q) {
            $output["flag"] = "failed";
            $output["msg"] = $result_q;

        } else {
            $output["flag"] = "success";
            $output["time"] = $current_time;//test the time
            $events = array();
            $i = 0;
            while ($row = mysqli_fetch_assoc($result_q))
                $events[] = $row;

            $output["events"] = $events;
            $output["iterations"] = $i;

        }

        return json_encode($output);
    }
}


<?php
/**
 * Created by PhpStorm.
 * User: nirb
 * Date: 11/18/2015
 * Time: 5:47 PM
 */

include 'response_process.php';
require_once 'DBFunctions.php';

class search_events implements ResponseProcess{

    public function dataProcess($dblink)
    {
        $output = array();
        $dbF = new DBFunctions($dblink);

        $user_lat = floatval($_POST["lat"]);
        $user_long = floatval($_POST["lon"]);
        $radius = floatval($_POST["radius"]);

        $mng_id = $_POST["manager_id"];
        //$result_q = $dbF ->getEventsManagedById($mng_id);

        $event_query = "SELECT * from events WHERE acos(sin(events.latitude * 0.0175) * sin($user_lat * 0.0175)
        + cos(events.latitude * 0.0175) * cos($user_lat * 0.0175) *
        cos(($user_long * 0.0175) - (events.longitude * 0.0175))) * 6371 <= $radius";
        $result_q = mysqli_query($dblink,$event_query) or die (mysqli_error($dblink));

        if(!$result_q) {
            $output["flag"] = "failed";
            $output["msg"] = $result_q;

        }else{
            $output["flag"] = "success";
            $events = array();
            while($row = mysqli_fetch_assoc($result_q))
                $events[] = $row;

            $output["events"] = $events;
        }
        return json_encode($output);
    }
}
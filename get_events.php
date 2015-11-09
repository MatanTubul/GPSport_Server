<?php
/**
 * this class handle all the request to get events which managed by specific user
 * Created by PhpStorm.
 * User: matant
 * Date: 11/5/2015
 * Time: 3:05 PM
 */
include 'response_process.php';
require_once 'DBFunctions.php';

class get_events implements ResponseProcess{

    public function dataProcess($dblink)
    {
        $output = array();
        $dbF = new DBFunctions($dblink);
        $mng_id = $_POST["manager_id"];
        $result_q = $dbF ->getEventsManagedById($mng_id);

        /*$event_query = "SELECT * from event WHERE event.manager_id = '$mng_id' and event.event_status = '1'";
        $result_q = mysqli_query($dblink,$event_query) or die (mysqli_error($dblink));*/

        if(!$result_q) {
            $output["flag"] = "failed";
            $output["msg"] = $result_q;

        }else{
            $output["flag"] = "success";
            $events = array();
            while($row = mysqli_fetch_assoc($result_q))
            {
                $events[] = $row;
            }
            $output["events"] = $events;
        }
        return json_encode($output);
    }
}
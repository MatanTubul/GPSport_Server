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


        if(!$result_q) {
            $output["flag"] = "failed";
            $output["msg"] = $result_q;

        }else{
            $output["flag"] = "success";
            $events = array();
            $i=0;
            while($row = mysqli_fetch_assoc($result_q))
            {
                $participants = array();
                $event_users = array();
                if($row["current_participants"] > 1){
                    $i++;
                    $users_ids = $dbF ->getEventIdsByAttendingTable($row["event_id"]);
                    while($row_participants = mysqli_fetch_assoc($users_ids))
                    {
                        $participants[] = $row_participants["user_id"];
                    }
                    $users_details = $dbF -> getUserSByIds($participants,count($participants));
                    while($row_users =  mysqli_fetch_assoc($users_details)){
                        $event_users[]=$row_users;
                    }
                }
                //$row["participants"]=
                $row["event_users"] = $event_users;
                $row["event_date"] = date("Y-m-d",strtotime($row["start_time"]));
                $row["formatted_start_time"] = date("H:i",strtotime($row["start_time"]));
                $row["formatted_end_time"] = date("H:i",strtotime($row["end_time"]));
                $events[] = $row;
            }
            $output["iteratins"] = $i;
            $output["events"] = $events;
        }
        return json_encode($output);
    }
}
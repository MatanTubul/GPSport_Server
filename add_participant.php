<?php
/**
 * This class handle the request of inserting users to public events.
 * Created by PhpStorm.
 * User: nirb
 * Date: 12/12/2015
 * Time: 12:38 PM
 */
include 'response_process.php';
require_once 'DBFunctions.php';

class add_participant implements ResponseProcess
{

    public function dataProcess($dblink)
    {

        $output = array();
        $dbF = new DBFunctions($dblink);
        $frag_id = $_POST['view'];
        $user_id = $_POST['user_id'];
        $event_id = $_POST['event_id'];
        $event_details = $dbF->GetEventById($event_id);
        $event_waiting_list_size = "5";
        if(!$event_details)
        {
            $output["flag"]= "failed";
            $output["msg"] = $event_details;
        }
        else {

            $row_event = mysqli_fetch_assoc($event_details);
            $event_current_participants = $row_event["current_participants"];
            $event_max_participants = $row_event["max_participants"];

            //Check if there's a place on playing list
            if ($event_current_participants < $event_max_participants) {
                //add user to playing list
                $insert_res = $dbF->insertNewPublicParticipate($event_id, $user_id, "participate",-1);
                if(!$insert_res)
                {
                    $output["flag"]= "failed";
                    $output["msg"] = $insert_res;
                }
                else {
                    $update_res = $dbF->updateEventUsersCounting($event_id, "current_participants", "inc");
                    if(!$update_res)
                    {
                        $output["flag"]= "failed";
                        $output["msg"] = $update_res;
                    }
                    else
                        if ($frag_id == "view")
                            $output["flag"]= "view_succeed";
                        else
                            $output["flag"]= "success";
                }

            }else
            {
                $event_current_waiting = $row_event["current_waiting"];
                //Check if there's a place on waiting list
                if ($event_current_waiting < $event_waiting_list_size) {
                //add user to waiting list
                    $insert_res = $dbF->insertNewPublicParticipate($event_id, $user_id, "waiting", $event_current_waiting + 1);
                    if(!$insert_res)
                    {
                        $output["flag"]= "failed";
                        $output["msg"] = $insert_res;
                    }
                    else {
                        $update_res = $dbF->updateEventUsersCounting($event_id, "current_waiting", "inc");
                        if(!$update_res )
                        {
                            $output["flag"]= "failed";
                            $output["msg"] = $update_res;
                        }
                        else
                            if ($frag_id == "view")
                                $output["flag"]= "view_succeed";
                            else
                                $output["flag"]= "success";                    }
                }else
                {//no place in this event for this user
                    $output["flag"]= "failed";
                    $output["msg"]= "event_full";
                }
            }

        }
        echo json_encode($output);
    }

}
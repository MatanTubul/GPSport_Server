<?php
/**
 * Created by PhpStorm.
 * User: nirb
 * Date: 12/3/2015
 * Time: 5:42 PM
 */

include 'response_process.php';
require_once 'DBFunctions.php';

class get_event_users implements ResponseProcess{

    public function dataProcess($dblink)
    {
        $output = array();
        $dbF = new DBFunctions($dblink);
        $event_id = $_POST["event_id"];
        $event_is_private = $_POST["private"];

        $events_users = $dbF->getEventUsers($event_id);
        if(!$events_users) {
            $output["flag"] = "failed";
            $output["msg"] = $events_users;
            }
        else {
            $output["flag"] = "success";
            $event_users = array();
            //For both private and public event first go to attending table and get the ids according to the event_id
            while ($row_user = mysqli_fetch_assoc($events_users)) {
                $event_users[] = $row_user;
            }

            //If the event is private The manager will be among the events details with his status according to the event status
            //if the event status is 2 the manger is not attending else and 1 the manager is attending
            $mng_details = $dbF->GetEventManager($event_id);
            if(!$mng_details) {
                $output["flag"] = "failed";
                $output["msg"] = $mng_details;
            }
            else {
                $output["flag"] = "success";
                $row_mng = mysqli_fetch_assoc($mng_details);
                if ($event_is_private == "true") {
                    if ($row_mng["event_status"] == 2)         //check event status to know the manager status
                        $output["mng_status"] = "not attend";
                    else
                        $output["mng_status"] = "attend";
                }
                else
                    $output["mng_status"] = "participate"; //this is a a manager of public event and cause of that he's participate
                $output["mng_details"] = $mng_details;
            }
            $output["event_users"] = $event_users;
        }

            return json_encode($output);
        }
    }
<?php
/**
 * this class handle all the application request which should remove user from event.
 * Created by PhpStorm.
 * User: matant
 * Date: 12/9/2015
 * Time: 3:58 PM
 */
include 'response_process.php';
require_once 'DBFunctions.php';
class remove_participant implements ResponseProcess {

    public function dataProcess($dblink)
    {
        $output = array();
        $dbF = new DBFunctions($dblink);
        $event_id = $_POST["event_id"];
        $userid = $_POST["user_id"];
        $event_is_private = $_POST["event_is_private"];
        if($event_is_private == "true"){
            $curr_participants = $_POST["current_participants"];
            $result_q = $dbF -> UpdateUserchoiceIntoAttending("not attend",$event_id,$userid);
            if(!$result_q)
            {
                $output["flag"]= "failed";
                $output["msg"] = $result_q;

            }else {
                $curr_participants = $curr_participants-1;
                $result_q = $dbF -> UpdateCurrentParticipantsInEvent($event_id,$curr_participants);
                if(!$result_q)
                {
                    $output["flag"]= "failed";
                    $output["msg"] = $result_q;

                }else{
                    $output["flag"] = "success";
                    $output["msg"] = $result_q;
                }
            }
        }
        else{
                $result_q = $dbF -> DeleteUserFromAttending($event_id,$userid);
            if(!$result_q)
            {
                $output["flag"]= "failed";
                $output["msg"] = $result_q;

            }else {
                $output["flag"] = "success";
                $output["msg"] = $result_q;
            }

        }
        return json_encode($output);
    }
}
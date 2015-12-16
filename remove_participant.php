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
        $user_id = $_POST["user_id"];
        $event_is_private = $_POST["event_is_private"];
        if($event_is_private == "true"){
            $result_q = $dbF -> UpdateUserChoiceIntoAttending("not attend",$event_id,$user_id);
            if(!$result_q)
            {
                $output["flag"]= "failed";
                $output["msg"] = $result_q;

            }else {
                $result_q = $dbF -> updateEventUsersCounting($event_id, "current_participants", "dec");

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
            $result_status = $dbF -> DeleteUserFromAttending($event_id,$user_id);
            if(!$result_status)
            {
                $output["flag"]= "failed";
                $output["msg"] = $result_status;

            }else {
                if ($result_status == waiting) {
                    $update_res = $dbF->updateEventUsersCounting($event_id, "current_waiting", "dec");
                    if (!$update_res) {
                        $output["flag"] = "failed";
                        $output["msg"] = $update_res;
                    } else
                        $output["flag"] = "success";
                }
                else
                    {//if you delete user who were participating and was the last to get in the playing list - check if there's someone waiting to play
                    // if so change his status and just dec the current waiting list count
                        $change_status = $dbF->ChangeStatusForAWaitingUser($event_id);
                        if(!$change_status) {
                            $output["flag"] = "failed";
                            $output["msg"] = $change_status;
                        }
                        elseif ($change_status == "changed")
                               $update_res = $dbF->updateEventUsersCounting($event_id, "current_waiting", "dec");
                            else //else just dec the current participants list count // regular case
                                $update_res = $dbF->updateEventUsersCounting($event_id, "current_participants", "dec");
                        if(!$update_res)
                        {
                            $output["flag"]= "failed";
                            $output["msg"] = $update_res;
                        }
                        else
                            $output["flag"]= "success";
                    }
            }
        }
        return json_encode($output);
    }
}
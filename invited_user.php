<?php
/**
 * this class handle the request of inserting invited users to private events.
 * Created by PhpStorm.
 * User: matant
 * Date: 11/3/2015
 * Time: 5:25 PM
 */
include 'response_process.php';
require_once 'DBFunctions.php';

class invited_user implements ResponseProcess {

    public function dataProcess($dblink)
    {
        $output = array();
        $dbF = new DBFunctions($dblink);
        $user_id = $_POST['user_id'];
        $event_id = $_POST['event_id'];
        $user_status = $_POST['user_status'];
        $output["user_id"] =$user_id;
        $output["event_id"] = $event_id;
        $result_q = $dbF->GetEventById($event_id);
        if(!$result_q)
        {
            $output["flag"]= "update_failed";
            $output["msg"] = $result_q;
        }
        else{
            $myrow = mysqli_fetch_assoc($result_q);
            $output["curr"] = $myrow["current_participants"];
            if($myrow["current_participants"] >= 1){
                $result_q = $dbF ->UpdateUserChoiceIntoAttending($user_status,$event_id,$user_id);
                $affected_row = mysqli_affected_rows($dblink);
                if(!$result_q)
                {
                    $output["flag"]= "update_failed";
                    $output["msg"] = $result_q;
                    $output["affected row"] = $affected_row;

                }else{
                    if($user_status == "attend"){
                        $result_e_q = $dbF ->updateEventUsersCounting($event_id, "current_participants", "inc");
                        if(!$result_e_q){
                            $output["flag"]= "update_failed";
                            $output["msg"] = "failed to update event table";
                            $output["query_res"] = $result_e_q;
                        }
                        else{
                            $output["flag"]= "updated";
                            $output["msg"] = $result_q;
                        }
                    }
                }
            }//end of if number of participants is bigger than 1
            else{
                $result_q = $dbF ->DeleteUserFromAttending($event_id,$user_id);
                if(!$result_q)
                {
                    $output["flag"]= "failed";
                    $output["msg"] = $result_q;
                }
                else{
                    $result_q = $dbF ->UpdateManagerInDelayEvent($event_id,$user_id);
                    if(!$result_q)
                    {
                        $output["flag"]= "failed";
                        $output["msg"] = $result_q;
                    }else{
                        $output["flag"]= "updated";
                        $output["msg"] = $result_q;
                    }
                }
            }//if number of participants smaller than 1

        }//GetEventById query return true

        return json_encode($output);
    }
}
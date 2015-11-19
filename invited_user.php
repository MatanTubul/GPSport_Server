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
        $user_id = $_POST['userId'];
        $event_id = $_POST['event_id'];
        $status = "approve";

        $output["user_id"] =$user_id;
        $output["event_id"] = $event_id;
        $result_q = $dbF ->UpdateUserchoiceIntoAttending($status,$event_id,$user_id);

        $affected_row = mysqli_affected_rows($dblink);

        if(!$result_q)
        {
            $output["flag"]= "update_failed";
            $output["msg"] = $result_q;
            $output["affected row"] = $affected_row;

        }else{
            $output["flag"]= "updated";
            $output["msg"] = $result_q;
            $output["affected row"] = $affected_row;
            $result_e_q = $dbF ->UpdateCurrentParticipants($event_id);

            if(!$result_e_q){
                $output["flag"]= "update_failed";
                $output["msg"] = "failed to update event table";
                $output["query_res"] = $result_e_q;
            }

        }
        return json_encode($output);
    }
}
<?php
/**
 * This class proccess the request of the manager to quit from the event and pass the management to other user in case the number of the users
 * is greater than 1
 * Created by PhpStorm.
 * User: matant
 * Date: 12/1/2015
 * Time: 2:53 PM
 */
include 'response_process.php';
//require_once 'gcm.php';
require_once 'DBFunctions.php';

class delete_manager implements ResponseProcess {
       public function dataProcess($dblink)
    {
        $output = array();
        $dbF = new DBFunctions($dblink);
        $event_id = $_POST["event_id"];
        $curr_participants = $_POST["current_participants"];
        $output["event_id"]= $event_id;
        //if number of participants gt than 1
        if($curr_participants > 1){
            $result_q = $dbF -> getUserIDByEvent($event_id);
            if(!$result_q)
            {
                $output["flag"]= "failed";
                $output["msg"] = $result_q;

            }else{
                $output["flag"]= "success";
                $output["msg"] = $result_q;
                $no_of_rows = mysqli_num_rows($result_q);
                if($no_of_rows > 0)
                {
                    $row = mysqli_fetch_assoc($result_q);
                    $user_id = $row["user_id"];
                    $result_q = $dbF ->UpdateEventManagerId($user_id,$event_id);
                    $affected_row = mysqli_affected_rows($dblink);
                    if(!$result_q)
                    {
                        $output["flag"]= "failed";
                        $output["msg"] = $result_q;
                        $output["affected row"] = $affected_row;

                    }else{
                        $output["flag"]= "success";
                        $output["msg"] = "define new event manager";
                        $output["affected row"] = $affected_row;
                    }
                    $result_q = $dbF ->DeleteFromAttending($event_id,$user_id);
                    if(!$result_q)
                    {
                        $output["flag"]= "failed";
                        $output["msg"] = $result_q;
                    }else{
                        $output["flag"]= "success";
                        $output["msg"] = "deleted user from event";
                    }

                }
                else{
                    $result_q = $dbF -> UpdateEventStatus($event_id);
                    $affected_row = mysqli_affected_rows($dblink);

                    if(!$result_q)
                    {
                        $output["flag"]= "failed";
                        $output["msg"] = $result_q;
                        $output["affected row"] = $affected_row;
                    }else{
                        $output["flag"]= "success";
                        $output["msg"] = $result_q;
                        $output["affected row"] = $affected_row;
                    }
                }

            }
        }else{
            $result_q = $dbF->DeleteEvent($event_id);
            if(!$result_q)
            {
                $output["flag"]= "failed";
                $output["msg"] = $result_q;
            }else {
                $output["flag"]= "success";
                $output["msg"] = $result_q;
            }
        }

        return json_encode($output);
   }
}
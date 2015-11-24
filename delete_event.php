<?php
/**
 * this class update the DB after the user decide to delete a specific event.
 * Created by PhpStorm.
 * User: matant
 * Date: 11/7/2015
 * Time: 5:02 PM
 */
include 'response_process.php';
require_once 'DBFunctions.php';

class delete_event implements ResponseProcess {

    public function dataProcess($dblink)
    {
        /**
         * need to fix the delete DB issue right now working but without DBFunctions class
         */
        $output = array();
        $dbF = new DBFunctions($dblink);
        $event_id = $_POST["event_id"];
        $output["event_id"]= $event_id;
        $result_q = $dbF -> getEventByEventIdFromAttending($event_id);
        /*$attending_query = "SELECT * from attending WHERE attending.event_id = '$event_id'";
        $result_q = mysqli_query($dblink,$attending_query) or die (mysqli_error($dblink));*/

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
               /* $event_query = "UPDATE event SET event.manager_id = '$user_id',event.current_participants = event.current_participants - 1  WHERE event.event_id = '$event_id'";
                $result_q = mysqli_query($dblink,$event_query) or die (mysqli_error($dblink));*/
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
                /*$del_query = "DELETE from attending WHERE attending.event_id = '$event_id' and attending.user_id = '$user_id'";
                $result_q = mysqli_query($dblink,$del_query) or die (mysqli_error($dblink));*/
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
                /*$event_query = "UPDATE event SET event.event_status = '0' WHERE event.event_id = '$event_id'";
                $result_q = mysqli_query($dblink,$event_query) or die (mysqli_error($dblink));*/
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

        return json_encode($output);
    }
}
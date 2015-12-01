<?php
/**
 * this class update the DB after the user decide to delete a specific event.
 * Created by PhpStorm.
 * User: matant
 * Date: 11/7/2015
 * Time: 5:02 PM
 */
include 'response_process.php';
require_once 'gcm.php';
require_once 'DBFunctions.php';

class delete_event implements ResponseProcess {

    public function dataProcess($dblink)
    {

        $output = array();
        $dbF = new DBFunctions($dblink);
        $event_id = $_POST["event_id"];
        $date = $_POST["event_date"];
        $curr_participant =$_POST["current_participants"];
        if($curr_participant > 1)
        {
            $manager_name = $_POST["manager_name"];
            $result_q = $dbF -> GetParticipatingUserDetails($event_id);
            if(!$result_q)
            {
                $output["flag"]= "failed";
                $output["msg"] = $result_q;
                return json_encode($output);
            }else {
                $no_of_rows = mysqli_num_rows($result_q);
                $output["no_of_rows"] = $no_of_rows;
                //send notification to users before deleting the message
                if ($no_of_rows > 0) {
                    $reg_ids = array();
                    $i = 0;
                    while ($row_user = mysqli_fetch_assoc($result_q)) {
                        $reg_ids[$i] = $row_user["gcm_id"];
                        $i++;
                    }
                    $output["reg_ids"] = $reg_ids;
                    $gcm = new GCM();
                    $data = array();
                    $sport = $_POST["kind_of_sport"];
                    $place = $_POST["address"];
                    $s_time = $_POST["start_time"];
                    $e_time = $_POST["end_time"];
                    $message = "Canceled The event " . $sport . " in " . $place . " in " .$date;
                    $data['message'] = $message;
                    $data['msg_type'] = "canceled";
                    $data['date'] = $date;
                    $data['start_time'] = date("H:i", strtotime($s_time));
                    $data['end_time'] = date("H:i", strtotime($e_time));
                    $data['inviter'] = $manager_name;
                    $data['event_id'] = $event_id;
                    $data['location'] = $place;
                    $gcm_res = $gcm->send_notification($reg_ids, $data);
                }
            }
        }
        $result_q = $dbF -> DeleteEventFromAttending($event_id);
        if(!$result_q)
        {
            $output["flag"]= "failed";
            $output["msg"] = $result_q;
            return json_encode($output);
        }
        else {
            $result_q = $dbF->DeleteEvent($event_id);
            if(!$result_q)
            {
                $output["flag"]= "failed";
                $output["msg"] = $result_q;
            }else {
                $output["flag"]= "success";
                $output["msg"] = $result_q;
            }
            $output["second delete"] = $result_q;
        }
        return json_encode($output);
    }
}
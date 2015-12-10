<?php
/**
 * this  class retrieve all the invitations that related to a user.
 * Created by PhpStorm.
 * User: matant
 * Date: 12/10/2015
 * Time: 4:36 PM
 */
include 'response_process.php';
require_once 'DBFunctions.php';

class get_invitations implements ResponseProcess {

    public function dataProcess($dblink)
    {
        $output = array();
        $user_id = $_POST["user_id"];
        $dbF = new DBFunctions($dblink);
        $result = $dbF->getEventsInvitationsListByUserId($user_id);
        $output["query"] = $result;
       if (!$result) {
            $output["flag"] = "failed";
            $output["msg"] = $result;

        } else {
            $output["flag"] = "success";
            $invitations = array();
            //For both private and public event first go to attending table and get the ids according to the event_id
            while ($row_user = mysqli_fetch_assoc($result)) {
                $row_user["event_date"] = date("Y-m-d",strtotime($row_user["start_time"]));
                $row_user["formatted_start_time"] = date("H:i",strtotime($row_user["start_time"]));
                $row_user["formatted_end_time"] = date("H:i",strtotime($row_user["end_time"]));
                $invitations[] = $row_user;
            }
            $output["invitations"] = $invitations;
        }
        return json_encode($output);
    }
}
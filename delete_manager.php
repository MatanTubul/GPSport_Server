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
        $frag_id = $_POST['view'];
        $event_id = $_POST["event_id"];
        $userid = $_POST["user_id"];
        $event_details = $dbF-> GetEventById($event_id);

        if(!$event_details) {
            $output["flag"] = "failed";
            $output["msg"] = $event_details;
        }
        else {
            $row_event = mysqli_fetch_assoc($event_details);
            $event_is_private = $row_event["private"];
            $curr_participants = $row_event["current_participants"];

            //if number of participants gt than 1
            if ($curr_participants > 1) {
                $result_q = $dbF->getEventPotentialManagerIds($event_id);
                if (!$result_q) {
                    $output["flag"] = "failed";
                    $output["msg"] = $result_q;

                } else {
                    if ($frag_id == "view")
                        $output["flag"]= "view_succeed";
                    else
                        $output["flag"]= "success";
                    $output["msg"] = $result_q;
                    $no_of_rows = mysqli_num_rows($result_q);
                    //if number of candidates for manager gt than 0
                    if ($no_of_rows > 0) {
                        $row = mysqli_fetch_assoc($result_q);
                        $user_id = $row["user_id"];
                        $result_q = $dbF->UpdateEventManagerId($user_id, $event_id);
                        $affected_row = mysqli_affected_rows($dblink);
                        if (!$result_q) {
                            $output["flag"] = "failed";
                            $output["msg"] = $result_q;
                            $output["affected row"] = $affected_row;

                        } else {
                            $result_status = $dbF->DeleteUserFromAttending($event_id, $user_id);
                            if (!$result_status) {
                                $output["flag"] = "failed";
                                $output["msg"] = $result_status;
                            } else {
                                if ($event_is_private == "true") {
                                    $update_res = $dbF->updateEventUsersCounting($event_id, "current_participants", "dec");
                                    if (!$update_res) {
                                        $output["flag"] = "failed";
                                        $output["msg"] = $result_q;
                                    } else {
                                        $users = array();
                                        $users[] = $userid;
                                        $result_q = $dbF->InsertIntoAttendingUpdatedUsers($users, $event_id, count($users),"not attend");
                                        if (!$result_q) {
                                            $output["flag"] = "failed";
                                            $output["msg"] = $result_q;
                                        } else {
                                            if ($frag_id == "view")
                                                $output["flag"]= "view_succeed";
                                            else
                                                $output["flag"]= "success";
                                            $output["msg"] = "deleted user from event";
                                        }
                                    }
                                }
                                else
                                {   if ($result_status == waiting) {
                                        $update_res = $dbF->updateEventUsersCounting($event_id, "current_waiting", "dec");
                                        if (!$update_res) {
                                            $output["flag"] = "failed";
                                            $output["msg"] = $update_res;
                                        } else
                                            if ($frag_id == "view")
                                                $output["flag"]= "view_succeed";
                                            else
                                                $output["flag"]= "success";
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
                                            if ($frag_id == "view")
                                                $output["flag"]= "view_succeed";
                                            else
                                                $output["flag"]= "success";                                    }
                                }
                            }
                        }
                    }
                }
            }//end if current participants gt than 1
            else {
                if ($event_is_private == "true") {
                    $result_q = $dbF->UpdatePrivateEventWhenManagerIsLast($event_id);
                    if (!$result_q) {
                        $output["flag"] = "failed";
                        $output["msg"] = $result_q;
                    } else {
                        if ($frag_id == "view")
                            $output["flag"]= "view_succeed";
                        else
                            $output["flag"]= "success";
                        $output["msg"] = $result_q;
                    }
                } else {
                    $result_q = $dbF->DeleteEvent($event_id);
                    if (!$result_q) {
                        $output["flag"] = "failed";
                        $output["msg"] = $result_q;
                    } else {
                        if ($frag_id == "view")
                            $output["flag"]= "view_succeed";
                        else
                            $output["flag"]= "success";
                        $output["msg"] = $result_q;
                    }
                }

            }//case the event contain only 1 member or less.
        }
        return json_encode($output);
   }
}
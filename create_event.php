<?php
/**
 * Created by PhpStorm.
 * User: matant
 * Date: 9/17/2015
 * Time: 2:56 PM
 */
include 'response_process.php';
include 'gcm.php';
require_once 'DBFunctions.php';


class CreateEvent implements ResponseProcess {
    public function dataProcess($dblink)
    {
        $output = array();
        $dbF = new DBFunctions($dblink);
        $sport = $_POST["sport_type"];
        $date = date("Y-m-d",strtotime(str_replace('/','-',$_POST["date"])));
        $s_time =$date." ".$_POST["s_time"];
        $e_time = $date." ".$_POST["e_time"];
        $s_time =date("Y-m-d H:i:s",strtotime($s_time));
        $e_time = date("Y-m-d H:i:s",strtotime($e_time));
        $lon = $_POST["lon"];
        $lat = $_POST["lat"];
        $event_type = $_POST["event_type"];
        $max_p = $_POST["max_participants"];
        $sched = $_POST["scheduled"];
        $gen = $_POST["gender"];
        $min_age = $_POST["minAge"];
        $manager = $_POST["manager"];
        $mng_name = $_POST["manager_name"];
        $place = $_POST["address"];
        $mode = $_POST["mode"];
        if($sched == "true"){
            $exp_val = "";
            $type = "";
            $repeat = $_POST["repeat"];
            $duration = $_POST["duration"];
            $expiration_tag = $_POST["sched_tag"];

            switch($expiration_tag){
                case "unlimited":{
                    $exp_val = "unlimited";
                    $type = $exp_val;
                    break;
                }
                case "Year":{
                    $exp_val = date("Y-m-d",strtotime($_POST["value"]));
                    $type = "date";
                    break;
                }
                case "events_number":
                    $exp_val = $_POST["value"];
                    $type = "counter";
                    break;
                case "by_date":
                    $exp_val = date("Y-m-d",strtotime($_POST["value"]));
                    $type = "date";
                    break;
            }
            $output["repeat"] = $repeat;
            $output["duration"] = $duration;
            $output["exp_val"] = $exp_val;
            $output["type"] = $type;
        }

        if($mode == "edit"){
            $event_id = $_POST["event_id"];
            $invited_users_size = 0;
            if(isset($_POST["invitedUsers"])){
                $participants = $_POST["invitedUsers"];
                $json_uesr_ids = json_decode($participants);
                $invited_users_size = count($json_uesr_ids);
            }

            if(isset($_POST["invitedUsers"])){
                $result_q = $dbF -> DeleteEventFromAttending($event_id);
                if(!$result_q)
                {
                    $output["flag"]= "delete failed";
                    $output["msg"] = $result_q;
                    return json_encode($output);
                }else {
                    $participants = $_POST["invitedUsers"];
                    $json_uesr_ids = json_decode($participants);
                    $output["json_users"] = $json_uesr_ids;
                    $get_users_reg_ids = $dbF->getUserSByIds($json_uesr_ids, count($json_uesr_ids));
                    $reg_ids = array();
                    $i = 0;
                    while ($row_user = mysqli_fetch_assoc($get_users_reg_ids)) {
                        $reg_ids[$i] = $row_user["gcm_id"];
                        $i++;
                    }
                    $output["ids"] = $reg_ids;
                    $output["size"] = count($json_uesr_ids);


                    $result_q = $dbF->InsertIntoAttendingUpdatedUsers($json_uesr_ids, $event_id, count($json_uesr_ids),"awaiting reply");
                    $output["insert_res"] = $result_q;
                    if (!$result_q) {
                        $output["flag"] = "update_insert failed";
                        $output["msg"] = $result_q;
                        return json_encode($output);
                    } else {
                        $output["flag"] = "update_success";
                        $output["msg"] = $result_q;
                    }
                    //send notification on update to users
                    $gcm = new GCM();
                    $data = array();
                    $message = "The event " . $sport . " in " . $place . " in " . $date . " updated,Please click on Join in order to confirm registration.";
                    $data['message'] = $message;
                    $data['date'] = $date;
                    $data['private'] = $event_type;
                    $data['start_time'] = date("H:i", strtotime($s_time));
                    $data['end_time'] = date("H:i", strtotime($e_time));
                    $data['inviter'] = $mng_name;
                    $data['event_id'] = $event_id;
                    $data['location'] = $place;
                    $gcm_res = $gcm->send_notification($reg_ids, $data);
                    $output["gcm_res"] = $gcm_res;
                    //send notification on update to users
                }
            }
            $result_q = $dbF ->checkIfEventIsExistBeforeUpdate($lon,$lat,$date,$s_time,$e_time,$event_id);
            if(!$result_q)
            {
                $output["flag"]= "select failed";
                $output["msg"] = $result_q;
                return json_encode($output);
            }
            else {
                $no_of_rows_check_event = mysqli_num_rows($result_q);
                if ($no_of_rows_check_event > 0) {
                    $output["flag"] = "failed";
                    $output["msg"] = "Place is already occupied in this time";
                }else{
                    $result_q = $dbF -> UpdateEvent($event_id,$sport,$s_time,$e_time,$place,$lon,$lat,$event_type,$gen,$min_age,$max_p,'1',$invited_users_size,$sched,$output["repeat"],$output["duration"],$output["type"],$output["exp_val"]);
                    $output["res"] = $result_q;
                    $output["sched"] = $sched;

                    if($sched == "true")
                    {
                        $output["sched_res"] = "true";
                    }
                    else{
                        $output["sched_res"] = "false";
                    }
                    $affected_row = mysqli_affected_rows($dblink);
                    if(!$result_q)
                    {
                        $output["flag"]= "update_failed";
                        $output["query_res"] = $result_q;
                        $output["msg"] = "failed to update event";
                        $output["affected row"] = $affected_row;
                    }
                    else{
                        $output["flag"]= "update_success";
                        $output["query_res"] = $result_q;
                        $output["msg"] = "success to update event";
                        $output["affected row"] = $affected_row;
                    }
                }
            }

        }
        else{

            $result_q = $dbF ->checkIfEventIsExist($lon,$lat,$date,$s_time,$e_time);
            $output["query"] = $result_q;
            if(!$result_q)
            {
                $output["flag"]= "select failed";
                $output["msg"] = $result_q;
                return json_encode($output);
            }
            else{
                $no_of_rows_check_event = mysqli_num_rows($result_q);
                $output["no_of_rows"] = $no_of_rows_check_event;
                if($no_of_rows_check_event > 0)
                {
                    $output["flag"] = "failed";
                    $output["msg"] = "Place is already occupied in this time";
                }else{
                    $output["flag"] = "success";
                    $output["msg"] = "insert event";
                    $num_of_invited_users = 0;
                    if(isset($_POST["jsoninvited"])){
                        $json = $_POST["jsoninvited"];
                        $json = json_decode($json);
                        $num_of_invited_users = (count($json));
                        $output["size_invited"] = count($json);
                    }

                    $result = $dbF -> InsertNewEvent($manager,$sport,$s_time,$e_time,$place,$lon,$lat,$event_type,$gen,$min_age,$max_p,$num_of_invited_users,$sched,$output["repeat"],$output["duration"],$output["type"],$output["exp_val"]);
                    if (!$result) {
                        $output["flag"] = "failed to create event";
                        // return (json_encode($output));

                    }
                    else{

                        if(isset($_POST["jsoninvited"])){

                            $event_s_res = $dbF ->getEventIdByDateAndTime($date,$s_time,$e_time);
                            $output["my_squery"] =$event_s_res;

                            if(!$event_s_res)
                            {
                                $output["flag"] = "failed";
                                $output["msg"] = "Event id not found";
                            }
                            else{
                                $row = mysqli_fetch_assoc($event_s_res);
                                $no_of_rows = mysqli_num_rows($event_s_res);
                                if($no_of_rows > 1 || $no_of_rows == 0)
                                {
                                    $output["flag"] = "failed";
                                    $output["msg"] = "Event id not found";
                                }
                                else{
                                    $event_id = $row["event_id"];
                                    $json = $_POST["jsoninvited"];
                                    $json = json_decode($json);
                                    $output["size_invited"] = count($json);
                                    $size_of_param = (count($json));
                                    $event_user_s_res = $dbF -> getUserIdAndRegId($json,$size_of_param);

                                    if(!$event_user_s_res)
                                    {
                                        $output["flag"] = "failed";
                                        $output["msg"] = "user id not found";
                                    }

                                    $result = $dbF->insertIntoAttendingTable($event_user_s_res, $event_id, $size_of_param);
                                    $insert_query_res = $result["res"];
                                    $output["query"] = $result["query"];
                                    $registration_ids = $result["reg_ids"];

                                    if(!$insert_query_res)
                                    {
                                        $output["flag"] = "failed";
                                        $output["msg"] = "failed to insert to attending table";
                                    }
                                    else{

                                        $output["registred_ids"] = $registration_ids;
                                        $output["msg"] = "success to insert into attending";
                                        $gcm = new GCM();
                                        $data = array();
                                        $message = "Would like to invite you to play ".$sport.", Please click on Join in order to add you into the event.";
                                        $data['message'] = $message;
                                        $data['date'] = $date;
                                        $data['start_time'] = date("H:i",strtotime($s_time));
                                        $data['end_time'] = date("H:i",strtotime($e_time));
                                        $data['inviter'] = $mng_name;
                                        $data['private'] = $event_type;
                                        $data['event_id'] = $event_id;
                                        $data['location'] = $place;
                                        $output["gcm_message"]=$data;
                                        $gcm_res = $gcm->send_notification($registration_ids,$data);
                                        $output["gcm_res"] = $gcm_res;

                                    } //els of $insert_query_res
                                } //else of  $no_of_rows > 1 || $no_of_rows == 0


                            } // else  of $event_s_res
                        } //if isset($_POST["invitedUsers"]

                    } // if $result
                }
            }
        }//get inside creating event mode.
        return json_encode($output);
    }
}

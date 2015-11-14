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
        $s_time =$_POST["s_time"];
        $e_time = $_POST["e_time"];
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


        if($mode == "edit"){
            $event_id = $_POST["event_id"];
            $result_q = $dbF -> UpdateEvent($event_id,$sport,$date,$s_time,$e_time,$place,$lon,$lat,$event_type,$gen,$min_age,$max_p,$sched);
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
            if(isset($_POST["invitedUsers"])){
                $result_q = $dbF -> DeleteEventFromAttending($event_id);
                if(!$result_q)
                {
                    $output["flag"]= "delete failed";
                    $output["msg"] = $result_q;
                    return json_encode($output);
                }else{
                    $participants = $_POST["invitedUsers"];
                    $json_uesr = json_decode($participants);
                    $ids = array();
                    foreach($json_uesr as $user)
                    {
                        $ids[] = $user["id"];
                    }
                    $result_q = $dbF -> insertIntoAttendingTable($ids,$event_id,count($ids));
                    if(!$result_q)
                    {
                        $output["flag"]= "update_insert failed";
                        $output["msg"] = $result_q;
                        return json_encode($output);
                    }else{
                        $output["flag"]= "update_success";
                        $output["msg"] = $result_q;
                    }
                }
            }
        }
        else{

            $result_q = $dbF ->checkIfEventIsExist($lon,$lat,$date,$s_time,$e_time);

            if(!$result_q)
            {
                $output["flag"]= "select failed";
                $output["msg"] = $result_q;
                return json_encode($output);
            }
            //case date and time are available
            else {
                $no_of_rows = mysqli_num_rows($result_q);
                if ($no_of_rows < 1) {
                    $output["flag"] = "success";
                    $output["msg"] = "insert event";

                    $result = $dbF -> InsertNewEvent($manager,$sport,$date,$s_time,$e_time,$place,$lon,$lat,$event_type,$gen,$min_age,$max_p,$sched);
                    if (!$result) {
                        $output["flag"] = "failed to create event";
                        // return (json_encode($output));

                    }
                    else{

                        if(isset($_POST["invitedUsers"])){

                            $event_s_res = $dbF ->getEventIdByDateAndTime($date,$s_time,$e_time);

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
                                        $message = " ,invited you to play ".$sport.",please click on Join in order to add you into the event.";
                                        $data['message'] = $message;
                                        $data['date'] = $date;
                                        $data['start_time'] = $s_time;
                                        $data['end_time'] = $e_time;
                                        $data['inviter'] = $mng_name;
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


                } // if $no_of_rows < 1
                else {
                    $output["flag"] = "failed";
                    $output["msg"] = "Place is already occupied in this time";
                }
            }

        }

        return json_encode($output);
    }
}

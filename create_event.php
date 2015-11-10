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


        $result_q = $dbF ->checkIfEventIsExist($lon,$lat,$date,$s_time,$e_time);
        /*$query = "SELECT * FROM event WHERE (event.longtitude = '$lon' AND event.latitude = '$lat')
                AND event.event_date = '$date' And ((event.start_time BETWEEN '$s_time' AND '$e_time') OR (event.end_time BETWEEN '$s_time' AND '$e_time'))";

        //AND (event.start_time = '$s_time' AND event.end_time = '$e_time')


        //check time and place of the event
        $result_q = mysqli_query($dblink,$query) or die (mysqli_error($dblink));*/
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

                /*$result = mysqli_query($dblink, "INSERT into event(manager_id,kind_of_sport,event_date,start_time,end_time,address,longtitude,latitude,private,gender,min_age,max_participants,current_participants,scheduled,event_status)
             VALUES ('$manager','$sport','$date','$s_time','$e_time','$place','$lon','$lat','$event_type','$gen','$min_age','$max_p','1','$sched','1')") or die (mysqli_error($dblink));*/
                if (!$result) {
                    $output["flag"] = "failed to create event";
                    // return (json_encode($output));

                }
                else{

                    if(isset($_POST["invitedUsers"])){

                        $event_s_res = $dbF ->getEventIdByDateAndTime($date,$s_time,$e_time);
                        /*$query_id = "SELECT event_id From event WHERE event.event_date = '$date' and event.start_time = '$s_time' and event.end_time = '$e_time'";
                        $event_s_res = mysqli_query($dblink,$query_id) or die (mysqli_error($dblink));*/
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
                                /*$query_users = "SELECT id,gcm_id From users WHERE ";
                                $i=0;


                                foreach($json as $user) {
                                    if ($i < $size_of_param - 1)
                                        // add a space at end of this string
                                        $query_users .= "users.mobile = '".$user."' or ";
                                    else {
                                        // and this one too
                                        $query_users .= "users.mobile = '".$user."' ";
                                        $output["users"][] = $user['mobile'];
                                    }
                                    $i++;
                                    $output["index"]=$i;
                                }
                                $output["user_query"]= $query_users;

                                $event_user_s_res = mysqli_query($dblink,$query_users) or die (mysqli_error($dblink));*/
                                if(!$event_user_s_res)
                                {
                                    $output["flag"] = "failed";
                                    $output["msg"] = "user id not found";
                                }

                                    $result = $dbF->insertIntoAttendingTable($event_user_s_res, $event_id, $size_of_param);
                                    $insert_query_res = $result["res"];
                                    $output["query"] = $result["query"];
                                    $registration_ids = $result["reg_ids"];



                                /*$insert_query = "INSERT into attending (event_id,user_id,status) VALUES ";
                                $i=0;
                                $status = "deny";
                               $registration_ids = array();
                                while($row_user = mysqli_fetch_assoc($event_user_s_res))
                                {
                                    $registration_ids[$i]=$row_user["gcm_id"];
                                    if($i<$size_of_param - 1)
                                        $insert_query .= "('" .$event_id. "','" .$row_user["id"]. "','" .$status. "'), ";
                                    else
                                        $insert_query .= "('".$event_id."','".$row_user["id"]."','".$status."') ";
                                    $i++;
                                }
                                $insert_query_res = mysqli_query($dblink,$insert_query) or die (mysqli_error($dblink));*/
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
        return json_encode($output);
    }
}

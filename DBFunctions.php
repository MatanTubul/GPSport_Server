<?php
/**
 * this class holding all the functions that related to the DB management.
 * Created by PhpStorm.
 * User: matant
 * Date: 11/9/2015
 * Time: 2:27 PM
 */


include_once 'connection.php';
class DBFunctions {

    private $con;
    function __construct($dblink){
        $this ->con = $dblink;
    }
    //forgot password
    function getUserByEmail($con,$email){
        $result = mysqli_query($this ->con,"SELECT * FROM users WHERE users.email='$email'");
        return $result;
    }
    //check event
    function  checkIfEventAvailable($lon,$lat,$date,$s_time,$e_time){
        $query = "SELECT * FROM event WHERE (event.longtitude = '$lon' AND event.latitude = '$lat')
                AND event.event_date = '$date' And ((event.start_time BETWEEN '$s_time' AND '$e_time')
                OR (event.end_time BETWEEN '$s_time' AND '$e_time'))";

        $result_q = mysqli_query($this->con,$query) or die (mysqli_error($this->con));
        return $result_q;
    }

    function InsertNewEvent($manager,$sport,$date,$s_time,$e_time,$place,$lon,$lat,$event_type,$gen,$min_age,$max_p,$sched){
        $result = mysqli_query($this->con, "INSERT into event(manager_id,kind_of_sport,event_date,start_time,end_time,address,longtitude,latitude,private,gender,min_age,max_participants,current_participants,scheduled,event_status)
             VALUES ('$manager','$sport','$date','$s_time','$e_time','$place','$lon','$lat','$event_type','$gen','$min_age','$max_p','1','$sched','1')") or die (mysqli_error($this->con));
        return $result;
    }

    function getEventIdByDateAndTime($date,$s_time,$e_time){
        $query_id = "SELECT event_id From event WHERE event.event_date = '$date' and event.start_time = '$s_time' and event.end_time = '$e_time'";
        $event_s_res = mysqli_query($this->con,$query_id) or die (mysqli_error($this->con));
        return $event_s_res;
    }

    function  getUserIdAndRegId($json){
        $query_users = "SELECT id,gcm_id From users WHERE ";
        $i=0;

        $size_of_param = (count($json));
        foreach($json as $user) {
            if ($i < $size_of_param - 1)
                // add a space at end of this string
                $query_users .= "users.mobile = '".$user."' or ";
            else {
                // and this one too
                $query_users .= "users.mobile = '".$user."' ";
               // $output["users"][] = $user['mobile'];
            }
            $i++;
            //$output["index"]=$i;
        }
        //$output["user_query"]= $query_users;
        $event_user_s_res = mysqli_query($this->con,$query_users) or die (mysqli_error($this->con));
        return $event_user_s_res;
    }

    function insertIntoAttendingTable($event_user_s_res,$event_id,$size_of_param){
        $insert_query = "INSERT into attending (event_id,user_id,status) VALUES ";
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
        $insert_query_res = mysqli_query($this->con,$insert_query) or die (mysqli_error($this->con));
        return $insert_query_res;
    }
    //check event


    //delete event
    function  getEventByEventId($event_id){
        $attending_query = "SELECT * from attending WHERE attending.event_id = '$event_id'";
        $result_q = mysqli_query($this -> con,$attending_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    function UpdateEventManagerId($user_id,$event_id){
        $event_query = "UPDATE event SET event.manager_id = '$user_id',event.current_participants = event.current_participants - 1
                        WHERE event.event_id = '$event_id'";
        $result_q = mysqli_query($this->con,$event_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    function DeleteFromAttending($event_id,$user_id){
        $del_query = "DELETE from attending WHERE attending.event_id = '$event_id' and attending.user_id = '$user_id'";
        $result_q = mysqli_query($this->con,$del_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    function UpdateEventStatus($event_id){
        $event_query = "UPDATE event SET event.event_status = '0' WHERE event.event_id = '$event_id'";
        $result_q = mysqli_query($this->con,$event_query) or die (mysqli_error($this->con));
        return $result_q;
    }
    //delete event

    //get_event
    function getEventsManagedById($mng_id){
        $event_query = "SELECT * from event WHERE event.manager_id = '$mng_id' and event.event_status = '1'";
        $result_q = mysqli_query($this ->con,$event_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    //invited_users

    function UpdateUserchoiceIntoAttending($status,$event_id,$user_id){
        $query = "UPDATE attending SET attending.status = '$status' WHERE attending.event_id = '$event_id' AND attending.user_id = '$user_id'";
        $result_q = mysqli_query($this->con,$query) or die (mysqli_error($this->con));
        return $result_q;
    }

    function UpdateCurrentParticipants($event_id){
        $event_query = "UPDATE event SET event.current_participants = event.current_participants+1
                        WHERE event.event_id = '$event_id'
                        AND (event.max_participants > event.current_participants)";
        $result_e_q  = mysqli_query($this->con,$event_query) or die (mysqli_error($this->con));
        return $result_e_q;
    }

    //invited_users


}
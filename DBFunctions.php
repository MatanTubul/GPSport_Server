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
    //forgot password,login,register,update profile
    function getUserByEmail($email){
        $result = mysqli_query($this ->con,"SELECT * FROM users WHERE users.email='$email'");
        return $result;
    }
    //forgot password,login,register,update profile

    //create event
    function  checkIfEventIsExist($lon,$lat,$date,$s_time,$e_time){
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

    function  getUserIdAndRegId($json,$size_of_param){
        $query_users = "SELECT id,gcm_id From users WHERE ";
        $i=0;
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
        $insert_query_res = array();
        $registration_ids = array();
        while($row_user = mysqli_fetch_assoc($event_user_s_res))
        {
            $registration_ids[$i]= $row_user["gcm_id"];
            if($i<$size_of_param - 1)
                $insert_query .= "('" .$event_id. "','" .$row_user["id"]. "','" .$status. "'), ";
            else
                $insert_query .= "('".$event_id."','".$row_user["id"]."','".$status."') ";
            $i++;
        }
        $insert_query_res["query"]=$insert_query;
        $insert_query_res["res"] = mysqli_query($this->con,$insert_query) or die (mysqli_error($this->con));
        $insert_query_res["reg_ids"] = $registration_ids;
        return ($insert_query_res);
    }
    //create event


    //delete event
    function  getEventByEventIdFromAttending($event_id){
        $attending_query = "SELECT * from attending WHERE attending.event_id = '$event_id'";
        $result_q = mysqli_query($this->con,$attending_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    function UpdateEventManagerId($user_id,$event_id){
        $event_query = "UPDATE event SET event.manager_id = '$user_id',event.current_participants = event.current_participants - 1 WHERE event.event_id = '$event_id'";
        $result_q = mysqli_query($this->con,$event_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    function DeleteFromAttending($event_id,$user_id){
        $del_query = "DELETE from attending WHERE attending.event_id = '$event_id' AND attending.user_id = '$user_id'";
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
        $event_query = "SELECT * from event WHERE event.manager_id = '$mng_id' AND event.event_status = '1'";
        $result_q = mysqli_query($this->con,$event_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    //invited_users

    function UpdateUserchoiceIntoAttending($status,$event_id,$user_id){
        $query = "UPDATE attending SET attending.status = '$status' WHERE attending.event_id = '$event_id' AND attending.user_id = '$user_id'";
        $result_q = mysqli_query($this->con,$query) or die (mysqli_error($this->con));
        return $result_q;
    }

    function UpdateCurrentParticipants($event_id){
        $event_query = "UPDATE event SET event.current_participants = event.current_participants+1 WHERE event.event_id = '$event_id' AND (event.max_participants > event.current_participants)";
        $result_e_q  = mysqli_query($this->con,$event_query) or die (mysqli_error($this->con));
        return $result_e_q;
    }

    //invited_users

    //login
        function UpdateUserStatus($user){
            $result = mysqli_query($this->con,"UPDATE users SET userStatus = '1' WHERE users.email= '$user'")
            or die((mysqli_error($this->con)));
            return $result;
        }
    //login

    //register
    function getUserByMobile($mob){
        $result2 = mysqli_query($this->con,"SELECT * FROM users WHERE users.mobile= '$mob'")
        or die((mysqli_error($this->con)));
        return $result2;
    }

    function InsertUserIntoDB($name,$email,$gen,$birth,$pass,$salt,$userStatus,$imageName,$mob,$gcm_id){
        $insertResult=mysqli_query($this->con,"INSERT INTO users (fname, email, gender, age, password, salt, userstatus, image,mobile,gcm_id) VALUES
               ('$name', '$email', '$gen', '$birth', '$pass','$salt','$userStatus','$imageName','$mob','$gcm_id')") or die((mysqli_error($this->con)));
        return $insertResult;
    }

    //register

    //search user
    function SearchUserByName($name){
        $query = "SELECT * FROM users WHERE users.fname LIKE '$name%'";
        $result = mysqli_query($this->con,$query) or die (mysqli_error($this->con));
        return $result;
    }
    //search user

    //Update Profile

    function UpdateProfile($name,$newEmail,$gen,$birth,$pass,$salt,$imageName,$newMob,$gcm_id,$prevEmail){
        $updateResult = mysqli_query($this->con,"UPDATE users SET fname = '$name', email = '$newEmail', gender = '$gen',
        age = '$birth', password = '$pass', salt = '$salt', image = '$imageName', mobile = '$newMob', gcm_id = '$gcm_id'
        WHERE users.email = '$prevEmail' ") or die((mysqli_error($this->con)));

        return $updateResult;
    }
    //Update Profile

}
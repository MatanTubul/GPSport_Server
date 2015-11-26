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
        $result = mysqli_query($this ->con,"SELECT * FROM users WHERE users.email='$email'")
        or die (mysqli_error($this->con));
        return $result;
    }
    function getUserIDByEvent($event_id){
        $result = mysqli_query($this ->con,"SELECT * FROM attending WHERE attending.event_id = '$event_id'")
        or die (mysqli_error($this->con));
        return $result;
    }
    function getUserSByIds($user_ids,$size){
        $query_users = "SELECT * From users WHERE ";
        $i=0;
        foreach($user_ids as $id) {
            if ($i < $size - 1)
                $query_users .= "users.id = '".$id."' or ";
            else {
                // and this one too
                $query_users .= "users.id = '".$id."' ";
            }
            $i++;
        }
        //$output["user_query"]= $query_users;
        $event_user_s_res = mysqli_query($this->con,$query_users) or die (mysqli_error($this->con));
        return $event_user_s_res;

    }

    //forgot password,login,register,update profile

    //create event
    function  checkIfEventIsExist($lon,$lat,$date,$s_time,$e_time){
        $query = "SELECT * FROM events WHERE (events.longitude = '$lon' AND events.latitude = '$lat')
                AND DATE(events.start_time) = '$date' And ((events.start_time BETWEEN '$s_time' AND '$e_time')
                OR (events.end_time BETWEEN '$s_time' AND '$e_time'))";

        $result_q = mysqli_query($this->con,$query) or die (mysqli_error($this->con));
        return $result_q;
    }

    function InsertNewEvent($manager,$sport,$date,$s_time,$e_time,$place,$lon,$lat,$event_type,$gen,$min_age,$max_p,$sched,$repeat,$duration,$type,$val){
        if($sched == true){
            if($type == "date"){
                $sched_exp_type = "date";
                $result = mysqli_query($this->con, "INSERT into events(manager_id,kind_of_sport,start_time,end_time,address,longitude,latitude,private,gender,min_age,max_participants,current_participants,scheduled,sched_type,sched_duration,sched_expired,sched_exp_type,event_status)
             VALUES ('$manager','$sport','$s_time','$e_time','$place','$lon','$lat','$event_type','$gen','$min_age','$max_p','1','1','$repeat',$duration,'$val','$sched_exp_type','1')") or die (mysqli_error($this->con));

            }else if($type == "counter")
            {
                $sched_exp_type = "counter";
                $result = mysqli_query($this->con, "INSERT into events(manager_id,kind_of_sport,start_time,end_time,address,longitude,latitude,private,gender,min_age,max_participants,current_participants,scheduled,sched_type,sched_duration,sched_counter,sched_exp_type,event_status)
             VALUES ('$manager','$sport','$s_time','$e_time','$place','$lon','$lat','$event_type','$gen','$min_age','$max_p','1','1','$repeat',$duration,'$val','$sched_exp_type','1')") or die (mysqli_error($this->con));
            }else{
                $sched_exp_type = "unlimited";
                $result = mysqli_query($this->con, "INSERT into events(manager_id,kind_of_sport,start_time,end_time,address,longitude,latitude,private,gender,min_age,max_participants,current_participants,scheduled,sched_type,sched_duration,sched_exp_type,event_status)
             VALUES ('$manager','$sport','$s_time','$e_time','$place','$lon','$lat','$event_type','$gen','$min_age','$max_p','1','1','$repeat','$duration','$sched_exp_type','1')") or die (mysqli_error($this->con));
            }
        }
        else{
            $result = mysqli_query($this->con, "INSERT into events(manager_id,kind_of_sport,start_time,end_time,address,longitude,latitude,private,gender,min_age,max_participants,current_participants,scheduled,event_status)
             VALUES ('$manager','$sport','$s_time','$e_time','$place','$lon','$lat','$event_type','$gen','$min_age','$max_p','1','$sched','1')") or die (mysqli_error($this->con));
        }

        return $result;
    }

    function getEventIdByDateAndTime($date,$s_time,$e_time){
        $query_id = "SELECT event_id From events WHERE DATE (events.start_time) = '$date' and events.start_time = '$s_time' and events.end_time = '$e_time'";
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
        $event_query = "UPDATE events SET events.manager_id = '$user_id',events.current_participants = events.current_participants - 1 WHERE events.event_id = '$event_id'";
        $result_q = mysqli_query($this->con,$event_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    function DeleteFromAttending($event_id,$user_id){
        $del_query = "DELETE from attending WHERE attending.event_id = '$event_id' AND attending.user_id = '$user_id'";
        $result_q = mysqli_query($this->con,$del_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    function UpdateEventStatus($event_id){
        $event_query = "UPDATE events SET events.event_status = '0' WHERE events.event_id = '$event_id'";
        $result_q = mysqli_query($this->con,$event_query) or die (mysqli_error($this->con));
        return $result_q;
    }
    //delete event

    //get_event
    function getEventsManagedById($mng_id){
        date_default_timezone_set('Asia/Jerusalem');
        $c_time = date("Y-m-d H:i:s");
        $event_query = "SELECT * from events WHERE events.manager_id = '$mng_id' AND events.event_status = '1' AND '$c_time' <= events.start_time";
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
        $event_query = "UPDATE events SET events.current_participants = events.current_participants+1 WHERE events.event_id = '$event_id' AND (events.max_participants > events.current_participants)";
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

    //Update Event
    function UpdateEvent($event_id,$sport,$s_time,$e_time,$place,$lon,$lat,$event_type,$gen,$min_age,$max_p,$current_participants,$sched)
    {
        $result = mysqli_query($this->con, "UPDATE events SET kind_of_sport = '$sport',start_time ='$s_time'
        ,end_time = '$e_time',address ='$place',longitude = '$lon',latitude = '$lat',private = '$event_type',gender = '$gen',min_age = '$min_age',
        max_participants = '$max_p',current_participants = '$current_participants',scheduled = '$sched'
        WHERE events.event_id = '$event_id'") or die (mysqli_error($this->con));
        return $result;
    }

    function DeleteEventFromAttending($event_id){
        $del_query = "DELETE from attending WHERE attending.event_id = '$event_id'";
        $result_q = mysqli_query($this->con,$del_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    function InsertIntoAttendingUpdatedUsers($event_user_s_res,$event_id,$size_of_param){
        $insert_query = "INSERT into attending (event_id,user_id,status) VALUES ";
        $status = "deny";
        for($i=0;$i<$size_of_param;$i++)
        {
            if($i<$size_of_param - 1)
                $insert_query .= "('" .$event_id. "','" .$event_user_s_res[$i]. "','" .$status. "'), ";
            else
                $insert_query .= "('".$event_id."','".$event_user_s_res[$i]."','".$status."') ";

        }
        $insert_query_res["query"]=$insert_query;
        $insert_query_res["res"] = mysqli_query($this->con,$insert_query) or die (mysqli_error($this->con));
        return ($insert_query_res);
    }
    //Update Event

    //logout
    function  LogOutUser($user){
        $result = mysqli_query($this ->con, "UPDATE users SET userstatus = '0' WHERE users.email= '$user'")
        or die (mysqli_error($this->con));
        return $result;
    }


}
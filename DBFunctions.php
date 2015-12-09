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
    /**
     * query which get user details by the email from users table
     * @param $email
     * @return bool|mysqli_result
     */
    function getUserByEmail($email){
        $result = mysqli_query($this ->con,"SELECT * FROM users WHERE users.email='$email'")
        or die (mysqli_error($this->con));
        return $result;
    }

    /**
     * query which retrieve potentials managers after the event manager decide to leave event.
     * @param $event_id
     * @return bool|mysqli_result
     */
    function getEventPotentialManagerIds($event_id){ // formally getUserIDByEvent
        $result = mysqli_query($this ->con,"SELECT * FROM attending WHERE attending.event_id = '$event_id' and (attending.status LIKE 'attend' or attending.status LIKE 'participate' or attending.status LIKE 'awaiting reply' )")
        or die (mysqli_error($this->con));
        return $result;
    }

    /**
     * query which bring all the rows from attending table by intersect the event_id column.
     * @param $event_id
     * @return bool|mysqli_result
     */
    function getEventIdsByAttendingTable($event_id){
        $result = mysqli_query($this ->con,"SELECT * FROM attending WHERE attending.event_id = '$event_id'")
        or die (mysqli_error($this->con));
        return $result;
    }

    /**
     * query which get array of users id and is size and intersect the users table by user id of each one of them.
     * @param $user_ids
     * @param $size
     * @return bool|mysqli_result
     */
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
    /**
     * query which check if other event is exist in the DB by checking the location date and time.
     * @param $lon
     * @param $lat
     * @param $date
     * @param $s_time
     * @param $e_time
     * @return bool|mysqli_result
     */
    function  checkIfEventIsExist($lon,$lat,$date,$s_time,$e_time){
        $query = "SELECT * FROM events WHERE (events.longitude = '$lon' AND events.latitude = '$lat')
                  AND DATE(events.start_time) = '$date'
                  And (('$s_time' BETWEEN events.start_time AND events.end_time) OR ('$e_time' BETWEEN events.start_time AND events.end_time))";
        $result_q = mysqli_query($this->con,$query) or die (mysqli_error($this->con));
        return $result_q;
    }

    /**
     * query which insert new event into the DB
     */
    function InsertNewEvent($manager,$sport,$s_time,$e_time,$place,$lon,$lat,$event_type,$gen,$min_age,$max_p,$sched,$repeat,$duration,$type,$val){
        if($sched == "true"){
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

    /**
     * query which get event id by date start time and end time.
     * @param $date
     * @param $s_time
     * @param $e_time
     * @return bool|mysqli_result
     */
    function getEventIdByDateAndTime($date,$s_time,$e_time){
        $query_id = "SELECT event_id From events WHERE DATE (events.start_time) = '$date' and events.start_time = '$s_time' and events.end_time = '$e_time'";
        $event_s_res = mysqli_query($this->con,$query_id) or die (mysqli_error($this->con));
        return $event_s_res;
    }

    /**
     * query which retrieve all users id and registrations id.
     * @param $json
     * @param $size_of_param
     * @return bool|mysqli_result
     */
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

    /**
     * query which insert multiple rows into attending table.
     * @param $event_user_s_res
     * @param $event_id
     * @param $size_of_param
     * @return array
     */
    function insertIntoAttendingTable($event_user_s_res,$event_id,$size_of_param){
        $insert_query = "INSERT into attending (event_id,user_id,status) VALUES ";
        $i=0;
        $status = "awaiting reply";
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
    /**
     * query which updating the Manager of event.
     * @param $user_id
     * @param $event_id
     * @return bool|mysqli_result
     */
    function UpdateEventManagerId($user_id,$event_id){
        $event_query = "UPDATE events SET events.manager_id = '$user_id',events.current_participants = events.current_participants-1 WHERE events.event_id = '$event_id'";
        $result_q = mysqli_query($this->con,$event_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    /**
     * query which delete user from attending table
     * @param $event_id
     * @param $user_id
     * @return bool|mysqli_result
     */
    function DeleteUserFromAttending($event_id,$user_id){
        $del_query = "DELETE from attending WHERE attending.event_id = '$event_id' AND attending.user_id = '$user_id'";
        $result_q = mysqli_query($this->con,$del_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    /**
     * query which updating event status.
     * @param $event_id
     * @return bool|mysqli_result
     */
    function UpdateEventStatus($event_id){
        $event_query = "UPDATE events SET events.event_status = '-1' WHERE events.event_id = '$event_id'";
        $result_q = mysqli_query($this->con,$event_query) or die (mysqli_error($this->con));
        return $result_q;
    }
    //delete event

    //get_event
    /**
     * query which get all the events that manged by user.
     * @param $mng_id
     * @return bool|mysqli_result
     */
    function getEventsManagedById($mng_id){
        date_default_timezone_set('Asia/Jerusalem');
        $c_time = date("Y-m-d H:i:s");
        $event_query = "SELECT * from events WHERE events.manager_id = '$mng_id' AND events.event_status = '1' AND '$c_time' <= events.start_time";
        $result_q = mysqli_query($this->con,$event_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    //invited_users
    /**
     * query which updating the status of user in the attending table.
     * @param $status
     * @param $event_id
     * @param $user_id
     * @return bool|mysqli_result
     */
    function UpdateUserchoiceIntoAttending($status,$event_id,$user_id){
        $query = "UPDATE attending SET attending.status = '$status' WHERE attending.event_id = '$event_id' AND attending.user_id = '$user_id'";
        $result_q = mysqli_query($this->con,$query) or die (mysqli_error($this->con));
        return $result_q;
    }

    /**
     * query which increasing the number of participants by one.
     * @param $event_id
     * @return bool|mysqli_result
     */
    function UpdateCurrentParticipants($event_id){
        $event_query = "UPDATE events SET events.current_participants = events.current_participants+1 WHERE events.event_id = '$event_id' AND (events.max_participants > events.current_participants)";
        $result_e_q  = mysqli_query($this->con,$event_query) or die (mysqli_error($this->con));
        return $result_e_q;
    }

    //invited_users

    //login
    /**
     * query which updating the user status which mean if the user is connected.
     * 1 - connected
     * 0 - logged out.
     * @param $user
     * @return bool|mysqli_result
     */
    function UpdateUserStatus($user){
        $result = mysqli_query($this->con,"UPDATE users SET userStatus = '1' WHERE users.email= '$user'")
        or die((mysqli_error($this->con)));
        return $result;
    }
    //login

    //register
    /**
     * query which get user details by mobile number.
     * @param $mob
     * @return bool|mysqli_result
     */
    function getUserByMobile($mob){
        $result2 = mysqli_query($this->con,"SELECT * FROM users WHERE users.mobile= '$mob'")
        or die((mysqli_error($this->con)));
        return $result2;
    }

    /**
     * query which insert into users table new user
     * @param $name
     * @param $email
     * @param $gen
     * @param $birth
     * @param $pass
     * @param $salt
     * @param $userStatus
     * @param $imageName
     * @param $mob
     * @param $gcm_id
     * @return bool|mysqli_result
     */
    function InsertUserIntoDB($name,$email,$gen,$birth,$pass,$salt,$userStatus,$imageName,$mob,$gcm_id){
        $insertResult=mysqli_query($this->con,"INSERT INTO users (fname, email, gender, age, password, salt, userstatus, image,mobile,gcm_id) VALUES
               ('$name', '$email', '$gen', '$birth', '$pass','$salt','$userStatus','$imageName','$mob','$gcm_id')") or die((mysqli_error($this->con)));
        return $insertResult;
    }

    //register

    //search user
    /**
     * query which get all the rows there name begin with the parameter $name
     * @param $name
     * @return bool|mysqli_result
     */
    function SearchUserByName($name){
        $query = "SELECT * FROM users WHERE users.fname LIKE '$name%'";
        $result = mysqli_query($this->con,$query) or die (mysqli_error($this->con));
        return $result;
    }
    //search user

    //Update Profile
    /**
     * query which updating user details.
     * @param $name
     * @param $newEmail
     * @param $gen
     * @param $birth
     * @param $pass
     * @param $salt
     * @param $imageName
     * @param $newMob
     * @param $gcm_id
     * @param $prevEmail
     * @return bool|mysqli_result
     */
    function UpdateProfile($name,$newEmail,$gen,$birth,$pass,$salt,$imageName,$newMob,$gcm_id,$prevEmail){
        $updateResult = mysqli_query($this->con,"UPDATE users SET fname = '$name', email = '$newEmail', gender = '$gen',
        age = '$birth', password = '$pass', salt = '$salt', image = '$imageName', mobile = '$newMob', gcm_id = '$gcm_id'
        WHERE users.email = '$prevEmail' ") or die((mysqli_error($this->con)));

        return $updateResult;
    }
    //Update Profile

    //Update Event
    /**
     * query which updating event details
     * @param $event_id
     * @param $sport
     * @param $s_time
     * @param $e_time
     * @param $place
     * @param $lon
     * @param $lat
     * @param $event_type
     * @param $gen
     * @param $min_age
     * @param $max_p
     * @param $current_participants
     * @param $sched
     * @param $repeat_type
     * @param $duration
     * @param $type
     * @param $val
     * @return bool|mysqli_result
     */
    function UpdateEvent($event_id,$sport,$s_time,$e_time,$place,$lon,$lat,$event_type,$gen,$min_age,$max_p,$current_participants,$sched,$repeat_type,$duration,$type,$val)
    {
        if($sched == "true") {
            if ($type == "date") {
                $sched_exp_type = "date";
                $result = mysqli_query($this->con, "UPDATE events SET kind_of_sport = '$sport',start_time ='$s_time'
                ,end_time = '$e_time',address ='$place',longitude = '$lon',latitude = '$lat',private = '$event_type',gender = '$gen',min_age = '$min_age',
                max_participants = '$max_p',current_participants = '$current_participants',scheduled = '$sched', sched_type = '$repeat_type',sched_duration = '$duration',sched_exp_type = '$sched_exp_type',sched_expired = '$val'
                WHERE events.event_id = '$event_id'") or die (mysqli_error($this->con));


            } else if ($type == "counter") {
                $sched_exp_type = "counter";
                $result = mysqli_query($this->con, "UPDATE events SET kind_of_sport = '$sport',start_time ='$s_time'
                ,end_time = '$e_time',address ='$place',longitude = '$lon',latitude = '$lat',private = '$event_type',gender = '$gen',min_age = '$min_age',
                max_participants = '$max_p',current_participants = '$current_participants',scheduled = '$sched', sched_type = '$repeat_type',sched_duration = '$duration',sched_exp_type = '$sched_exp_type',sched_counter = '$val'
                WHERE events.event_id = '$event_id'") or die (mysqli_error($this->con));
            }
        }
        else{
            $sched_exp_type = "update";
            $result = mysqli_query($this->con, "UPDATE events SET kind_of_sport = '$sport',start_time ='$s_time'
            ,end_time = '$e_time',address ='$place',longitude = '$lon',latitude = '$lat',private = '$event_type',gender = '$gen',min_age = '$min_age',
            max_participants = '$max_p',current_participants = '$current_participants',scheduled = '$sched'
            WHERE events.event_id = '$event_id'") or die (mysqli_error($this->con));
        }

        return $result;
    }

    /**
     * query which delete all the rows in attending table which have the $event_id
     * @param $event_id
     * @return bool|mysqli_result
     */
    function DeleteEventFromAttending($event_id){
        $del_query = "DELETE from attending WHERE attending.event_id = '$event_id'";
        $result_q = mysqli_query($this->con,$del_query) or die (mysqli_error($this->con));
        return $result_q;
    }

    /**
     * query which insert array of users into attending table
     * @param $event_user_s_res
     * @param $event_id
     * @param $size_of_param
     * @param $user_status
     * @return mixed
     */
    function InsertIntoAttendingUpdatedUsers($event_user_s_res,$event_id,$size_of_param){
        $insert_query = "INSERT into attending (event_id,user_id,status) VALUES ";
        $status = "awaiting reply";
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
    /**
     * query which updating user status after logout
     * @param $user
     * @return bool|mysqli_result
     */
    function  LogOutUser($user){
        $result = mysqli_query($this ->con, "UPDATE users SET userstatus = '0' WHERE users.email= '$user'")
        or die (mysqli_error($this->con));
        return $result;
    }

    /**
     * get all the events that manager by $user_id
     * @param $user_id
     * @return bool|mysqli_result
     */
    function GetEventListFromEvents($user_id){
        date_default_timezone_set('Asia/Jerusalem');
        $c_time = date("Y-m-d H:i:s");
        $query = "select * from events WHERE events.manager_id = '$user_id' and '$c_time' < events.start_time and events.event_status = '1'";
        $result = mysqli_query($this ->con,$query) or die (mysqli_error($this->con));
        return $result;
    }

    /**
     * query which retrieve all the events id which related to $user_id
     * @param $user_id
     * @return bool|mysqli_result
     */
    function GetEventListFromAttendingByUser($user_id){
        $query = "SELECT events.* from events,attending WHERE attending.user_id = '$user_id' and events.event_id = attending.event_id and attending.status LIKE 'attend'";
        $result = mysqli_query($this ->con,$query) or die (mysqli_error($this->con));
        return $result;
    }

    /**
     * get all the users that participating in a specific event
     * @param $event_id
     * @return bool|mysqli_result
     */
    function GetParticipatingUserDetails($event_id){
        $query = "SELECT  users.* FROM  users,(SELECT  attending.user_id  FROM  attending  WHERE  attending.event_id  = '$event_id') as  tmpatt  WHERE  tmpatt.user_id  =  users.id";
        $result = mysqli_query($this ->con,$query) or die (mysqli_error($this->con));
        return $result;
    }

    /**
     * query which updating event status to deleted in events table
     * @param $event_id
     * @return bool|mysqli_result
     */
    function DeleteEvent($event_id){
        $query = "UPDATE events set event_status = '-1' WHERE events.event_id = '$event_id'";
        $result = mysqli_query($this ->con,$query) or die (mysqli_error($this->con));
        return $result;
    }

    /**
     * query which updating private event into specific status when the manager is leave and is the last user.
     * @param $event_id
     * @return bool|mysqli_result
     */
    function UpdatePrivateEventWhenManagerIsLast($event_id){
        $query = "Update events set event_status = '2',events.current_participants='0' WHERE  events.event_id = '$event_id'";
        $result = mysqli_query($this ->con,$query) or die (mysqli_error($this->con));
        return $result;
    }

    /**
     * @param $event_id
     * @return bool|mysqli_result
     */
    function GetEventUsers($event_id){
        $query = "SELECT users.id ,users.fname,users.image, attending.status FROM users,attending WHERE attending.event_id = '$event_id' AND users.id = attending.user_id";
        $result = mysqli_query($this ->con,$query) or die (mysqli_error($this->con));
        return $result;
    }

    /**
     * @param $event_id
     * @return bool|mysqli_result
     */
    function GetEventManager($event_id){
        $query = "SELECT users.id ,users.fname,users.image,events.event_status FROM users, events WHERE events.event_id = '$event_id' AND users.id = events.manager_id";
        $result = mysqli_query($this ->con,$query) or die (mysqli_error($this->con));
        return $result;
    }

    /**
     * query which get event details by event_id
     * @param $event_id
     * @return bool|mysqli_result
     */
    function GetEventById($event_id){
        $query = "SELECT * from events WHERE events.event_id = '$event_id'";
        $result = mysqli_query($this ->con,$query) or die (mysqli_error($this->con));
        return $result;
    }

    /**
     * query which updating event manager and status when event is private and the original event manager leave the event
     * @param $event_id
     * @param $user_id
     * @return bool|mysqli_result
     */
    function UpdateManagerInDelayEvent($event_id,$user_id){
        $query = "UPDATE events set events.event_status = '1', events.current_participants = '1',events.manager_id = '$user_id' WHERE events.event_id = '$event_id'";
        $result = mysqli_query($this ->con,$query) or die (mysqli_error($this->con));
        return $result;
    }

    /**
     * query which check if there is other events which  exist in the same place and time and the event_id is not himself
     * @param $lon
     * @param $lat
     * @param $date
     * @param $s_time
     * @param $e_time
     * @param $event_id
     * @return bool|mysqli_result
     */
    function  checkIfEventIsExistBeforeUpdate($lon,$lat,$date,$s_time,$e_time,$event_id)
    {
        $query = "SELECT * FROM events WHERE (events.longitude = '$lon' AND events.latitude = '$lat')
                  AND DATE(events.start_time) = '$date'
                  And (('$s_time' BETWEEN events.start_time AND events.end_time) OR ('$e_time' BETWEEN events.start_time AND events.end_time))AND events.event_id != '$event_id'";
        $result_q = mysqli_query($this->con, $query) or die (mysqli_error($this->con));
        return $result_q;
    }

    /**
     * query which updating events table current participants column
     * @param $event_id
     * @param $val
     * @return bool|mysqli_result
     */
    function UpdateCurrentParticipantsInEvent($event_id,$val){
        $query = "UPDATE events SET events.current_participants = '$val' WHERE events.event_id = '$event_id'";
        $result_q = mysqli_query($this->con, $query) or die (mysqli_error($this->con));
        return $result_q;
    }



}
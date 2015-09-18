<?php
/**
 * Created by PhpStorm.
 * User: matant
 * Date: 9/17/2015
 * Time: 2:56 PM
 */
include 'response_process.php';


class CreateEvent implements ResponseProcess {



    public function dataProcess($dblink)
    {
        $output = array();
        $date = $_POST["date"];
        $sport = $_POST["sport_type"];
        $date = date("Y-m-d",strtotime(str_replace('/','-',$date)));
        $s_time =$_POST["s_time"];
        $e_time = $_POST["e_time"];
        $lon = $_POST["lon"];
        $lat = $_POST["lat"];
        $event_type = $_POST["event_type"];
        $max_p = $_POST["max_participants"];
        $sched = $_POST["scheduled"];

        $result = mysqli_query($dblink,"INSERT into event(kind_of_sport,event_date,start_time,end_time,longtitude,latitude,type_of_event,max_participants,scheduled,event_status)
        VALUES ('$sport','$date','$s_time','$e_time','$lon','$lat','$event_type','$max_p','$sched','1')") or die (mysqli_error($dblink));

        if(!$result){
        $output["flag"] = "query failed";
       // return (json_encode($output));
    }else {
            $no_of_rows = mysqli_num_rows($result);
            if ($no_of_rows < 1)
                $output["flag"] = "failed";   //user not found
            else {
                $row = mysqli_fetch_assoc($result);
                $output["flag"] = "success";
            }
        }
        return json_encode($output);
    }
}
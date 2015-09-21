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
        //$date = $_POST["date"];
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

        $query = "SELECT * FROM event WHERE (event.longtitude = '$lon' AND event.latitude = '$lat')
                AND event.event_date = '$date' And ((event.start_time BETWEEN '$s_time' AND '$e_time') OR (event.end_time BETWEEN '$s_time' AND '$e_time'))";

        //AND (event.start_time = '$s_time' AND event.end_time = '$e_time')


        //check time and place of the event
        $result_q = mysqli_query($dblink,$query) or die (mysqli_error($dblink));
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
                $result = mysqli_query($dblink, "INSERT into event(kind_of_sport,event_date,start_time,end_time,longtitude,latitude,private,gender,max_participants,scheduled,event_status)
             VALUES ('$sport','$date','$s_time','$e_time','$lon','$lat','$event_type','$gen','$max_p','$sched','1')") or die (mysqli_error($dblink));
                if (!$result) {
                    $output["flag"] = "insert failed";
                    // return (json_encode($output));
                }
            }
            else {

                $output["flag"] = "failed";
                $output["msg"] = "Place is already occupied in this time";
            }
        }
        return json_encode($output);
    }
}
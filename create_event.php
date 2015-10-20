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
        $min_age = $_POST["minAge"];

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

                $result = mysqli_query($dblink, "INSERT into event(kind_of_sport,event_date,start_time,end_time,longtitude,latitude,private,gender,min_age,max_participants,scheduled,event_status)
             VALUES ('$sport','$date','$s_time','$e_time','$lon','$lat','$event_type','$gen','$min_age','$max_p','$sched','1')") or die (mysqli_error($dblink));
                if (!$result) {
                    $output["flag"] = "failed to create event";
                    // return (json_encode($output));
                }
                if(isset($_POST["invitedUsers"])){
                    $query = "SELECT id From event WHERE event.event_date = '$date' and event.start_time = '$s_time'";
                    $event_s_res = mysqli_query($dblink,$query) or die (mysqli_error($dblink));

                    if(!$event_s_res)
                    {
                        $output["flag"] = "failed";
                        $output["msg"] = "Event id not found";
                    }
                    else{
                        /*$no_of_rows = mysqli_num_rows($event_s_res);
                        if ($no_of_rows < 1)
                            $output["event"]="event not found";   //user not found
                        else {
                            $output["flag"] = "user found";
                            $output["users"] = array();
                            $row = mysqli_fetch_assoc($result);
                            $output["row"]= $row;
                        }
                        $query = "insert into attending (event_id,user_mobile,status)VALUES";
                        // this is where the magic happens
                        $array = json_decode($_POST["invitedUsers"],true);
                        $it = new ArrayIterator( $array );
                        $output["invited_users"]=$array;

                        // a new caching iterator gives us access to hasNext()
                        $cit = new CachingIterator( $it );
                        $status = "deny";
                        // loop over the array
                        foreach ( $cit as $value )
                        {
                            $cit->
                            // add to the query
                            $query .= "('".$row["id"]."','" .$cit->current()."','".$status."')";
                            // if there is another array member, add a comma
                            if( $cit->hasNext() )
                            {
                                $query .= ",";
                            }
                        }
                        $insert_u_res = mysqli_query($dblink,$query) or die (mysqli_error($dblink));
                        if(!$insert_u_res){
                            $output["flag"] = "failed to insert invited users";
                            $output["msg"] = $insert_u_res;
                        }
                        */
                    }
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
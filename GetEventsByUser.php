<?php
/**
 * this class handle the request to get the events that user is participating in.
 * Created by PhpStorm.
 * User: matant
 * Date: 11/29/2015
 * Time: 8:47 PM
 */
include 'response_process.php';
require_once 'DBFunctions.php';
class GetEventsByUser implements ResponseProcess {

    public function dataProcess($dblink)
    {
        $output = array();
        $dbF = new DBFunctions($dblink);
        $user_id = $_POST["user_id"];
        $result_q_attending = $dbF ->GetEventListFromAttendingByUser($user_id,"attend");
        $result_q_events = $dbF -> GetEventListFromEvents($user_id);
        $result_q_participating = $dbF ->GetEventListFromAttendingByUser($user_id,"participate");


         if($result_q_attending == false || $result_q_events == false  || $result_q_participating == false)
         {
             $output["flag"] = "failed";
             $output["result_q_attending"] = $result_q_attending;
             $output["result_q_events"] = $result_q_events;
             $output["result_q_participating"] = $result_q_participating;

         }else{
             $no_of_rows_att = mysqli_num_rows($result_q_attending);
             $no_of_rows_events = mysqli_num_rows($result_q_events);
             $no_of_rows_par = mysqli_num_rows($result_q_participating);
             $output["no_rows_events"] = $no_of_rows_events;
             $output["no_rows_attending"] = $no_of_rows_att;
             $output["no_rows_participating"] = $no_of_rows_par;
             $output["numofrows"] = $no_of_rows_att+$no_of_rows_events+$no_of_rows_par;
             if($no_of_rows_att < 1 && $no_of_rows_events < 1 && $no_of_rows_par < 1  ){
                 $output["msg"] = "Events was not found";
                 $output["flag"] = "success";
             }else{
                 $events = array();
                 $i=0;
                 while($row = mysqli_fetch_assoc($result_q_attending)){
                     $row["event_date"] = date("Y-m-d",strtotime($row["start_time"]));
                     $row["formatted_start_time"] = date("H:i",strtotime($row["start_time"]));
                     $row["formatted_end_time"] = date("H:i",strtotime($row["end_time"]));
                     $events[$i] = $row;
                     $i++;
                 }
                 while ($row = mysqli_fetch_assoc($result_q_events)){
                     $row["event_date"] = date("Y-m-d",strtotime($row["start_time"]));
                     $row["formatted_start_time"] = date("H:i",strtotime($row["start_time"]));
                     $row["formatted_end_time"] = date("H:i",strtotime($row["end_time"]));
                     $events[$i] = $row;
                     $i++;
                 }
                 while ($row = mysqli_fetch_assoc($result_q_participating)){
                     $row["event_date"] = date("Y-m-d",strtotime($row["start_time"]));
                     $row["formatted_start_time"] = date("H:i",strtotime($row["start_time"]));
                     $row["formatted_end_time"] = date("H:i",strtotime($row["end_time"]));
                     $events[$i] = $row;
                     $i++;
                 }
                 $output["events"] = $events;
                 $output["flag"] = "success";
             }
         }
        return json_encode($output);
    }
}
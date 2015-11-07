<?php
/**
 * this class update the DB after the user decide to delete a specific event.
 * Created by PhpStorm.
 * User: matant
 * Date: 11/7/2015
 * Time: 5:02 PM
 */
include 'response_process.php';

class delete_event implements ResponseProcess {

    public function dataProcess($dblink)
    {
        $output = array();
        $event_id = $_POST["event_id"];
        $event_query = "UPDATE event SET event.event_status = '0' WHERE event.event_id = '$event_id'";
        $result_q = mysqli_query($dblink,$event_query) or die (mysqli_error($dblink));
        $affected_row = mysqli_affected_rows($dblink);

        if(!$result_q)
        {
            $output["flag"]= "failed";
            $output["msg"] = $result_q;
            $output["affected row"] = $affected_row;

        }else{
            $output["flag"]= "success";
            $output["msg"] = $result_q;
            $output["affected row"] = $affected_row;
        }
        return json_encode($output);
    }
}
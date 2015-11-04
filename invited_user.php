<?php
/**
 * this class handle the request of inserting invited users to private events.
 * Created by PhpStorm.
 * User: matant
 * Date: 11/3/2015
 * Time: 5:25 PM
 */
include 'response_process.php';

class invited_user implements ResponseProcess {

    public function dataProcess($dblink)
    {
        $output = array();
        $user_id = $_POST['userId'];
        $event_id = $_POST['event_id'];
        $status = "approved";

        $output["user_id"] =$user_id;
        $output["event_id"] = $event_id;

        $query = "UPDATE attending SET attending.status = '$status' WHERE attending.event_id = '$event_id' AND attending.user_id = '$user_id'";
        $result_q = mysqli_query($dblink,$query) or die (mysqli_error($dblink));
        $affected_row = mysqli_affected_rows($dblink);

        if(!$result_q)
        {
            $output["flag"]= "update_failed";
            $output["msg"] = $result_q;
            $output["affected row"] = $affected_row;
            return json_encode($output);
        }else{
            $output["flag"]= "updated";
            $output["msg"] = $result_q;
            $output["affected row"] = $affected_row;

        }
        return json_encode($output);
    }
}
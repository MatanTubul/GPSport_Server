<?php
/**
 * Created by PhpStorm.
 * User: Nir B
 * Date: 8/17/2015
 * Time: 1:34 AM
 */

include 'response_process.php';

class ForgotPassword implements ResponseProcess{

    public function dataProcess($dblink) {

        $email = $_POST['email'];
        $output = array();

        $result = mysqli_query($dblink,"SELECT * FROM users WHERE users.email='$email'");

        if(!$result){
            $output["dblink"]= $dblink;
            $output["error_msg"] = "query failed";
            $output["query_msg"] = $result;
            print(json_encode($output));
        }

        $no_of_rows = mysqli_num_rows($result);

        if ($no_of_rows < 1)
            $output["flag"]="user not found";   //user not found
        else {
            $row = mysqli_fetch_assoc($result);
            $output["flag"] = "recovered";  //password recovered
            $output["password"] =  $row["password"];
        }
        return json_encode($output);
    }
}

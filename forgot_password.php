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

        $result = mysqli_query($dblink,"SELECT * FROM users WHERE users.email= '$email'");

        if(!$result){
            $output["error_msg"] = "query failed";
            print(json_encode($output));
        }

        $no_of_rows = mysqli_num_rows($result);

        if ($no_of_rows < 1)
            $output["flag"]="user";   //user not found
        else
            $output["flag"] = "recovered";  //password recovered

        return json_encode($output);
    }
}

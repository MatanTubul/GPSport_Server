<?php
/**
 * Created by PhpStorm.
 * User: Nir B
 * Date: 17/09/2015
 * Time: 23:18
 */

include 'response_process.php';
class GetProfile implements ResponseProcess{

    public function dataProcess($dblink) {

        $user = $_POST['username'];

        $output = array();

        $result = mysqli_query($dblink,"SELECT * FROM users WHERE users.email= '$user'");

        if(!$result){
            $output["error_msg"] = "signup query failed";
            print(json_encode($output));
        }

        $output["flag"]="profile details retrieval";
        return json_encode($output);
    }

}

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
        $row = mysqli_fetch_assoc($result);
        $output["flag"]="profile details retrieval";

        $passFunc = new PasswordFunctions();
        $output["password"] =  $passFunc->decrypt($row["password"],$row["salt"]);
        $output["name"] =  $row["name"];
        $output["email"] =  $row["email"];
        $output["gender"] =  $row["gender"];
        $output["age"] =  $row["age"];
        $output["mobile"] =  $row["mobile"];

        return json_encode($output);
    }

}

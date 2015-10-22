<?php
/**
 * Created by PhpStorm.
 * User: Nir B
 * Date: 16/08/2015
 * Time: 15:24
 */

include 'response_process.php';
require_once 'PasswordFunctions.php';

class Login implements ResponseProcess{

    public function dataProcess($dblink) {

        $user = $_POST['username'];
        $pass = ($_POST['password']);
        $output = array();

        $passFunc = new PasswordFunctions();

        $result = mysqli_query($dblink,"SELECT * FROM users WHERE users.email= '$user'");

        if(!$result){
            $output["error_msg"] = "query failed";
            print(json_encode($output));
        }

        $no_of_rows = mysqli_num_rows($result);

        if ($no_of_rows < 1)
            $output["flag"]="User was not found";   //user not found
        else
        {
            $row = mysqli_fetch_assoc($result);
            $dbPass = $passFunc->decrypt($row["password"],$row["salt"]);
            if($pass != $dbPass)
            {
                $output["flag"]="Password is incorrect";   //password is incorrect
            }
            else
                if ($row["userstatus"]== 1)
                    $output["flag"]="already connected"; //user already connected
                else
                {
                    $output["flag"]="verified";          //user can login
                    $output["name"]=$row["name"];
                    $output["mobile"] =$row["mobile"];
                    $output["user_id"] = $row["id"];
                    mysqli_query($dblink,"UPDATE users SET userStatus = '1' WHERE users.email= '$user'");
                }
        }
        return json_encode($output);
    }


}


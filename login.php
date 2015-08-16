<?php
/**
 * Created by PhpStorm.
 * User: Nir B
 * Date: 16/08/2015
 * Time: 15:24
 */

include 'response_process.php';

class Login implements ResponseProcess{

    public function dataProcess($dblink) {

        $user = $_POST['username'];
        $pass = $_POST['password'];
        $output = array();

        $result = mysqli_query($dblink,"SELECT * FROM users WHERE users.email= '$user'");

        if(!$result){
            $output["error_msg"] = "query failed";
            print(json_encode($output));
        }

        $no_of_rows = mysqli_num_rows($result);

        if ($no_of_rows < 1)
            $output["flag"]="user";   //user not found
        else
        {
            $row = mysqli_fetch_assoc($result);
            if($pass != $row["password"])
            {
                $output["flag"]="password";   //password is incorec
            }
            else
                if ($row["userStatus"]== 1)
                    $output["flag"]="already connected"; //user allready connected
                else
                {
                    $output["flag"]="verified";          //user can login
                    mysqli_query($dblink,"UPDATE users SET userStatus = '1' WHERE users.email= '$user'");
                }
        }
        return json_encode($output);
    }


}


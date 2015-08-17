<?php
/**
 * Created by PhpStorm.
 * User: Nir B
 * Date: 16/08/2015
 * Time: 17:52
 */

include 'response_process.php';
class Register implements ResponseProcess{

    public function dataProcess($dblink) {

        $name = $_POST['firstname'];
        $user = $_POST['username'];//send the email as userName
        $pass = $_POST['password'];
        $mob = $_POST['mobile'];
        $birth = $_POST['birthyear'];
        $gen = $_POST['gender'];
        $pic = $_POST['picture'];
        $pic = base64_decode($pic);

        $output = array();

        $result1 = mysqli_query($dblink,"SELECT * FROM users WHERE users.user= '$user'");
        $result2 = mysqli_query($dblink,"SELECT * FROM users WHERE users.mobile= '$mob'");

        if((!$result1) || (!$result2)){
            $output["error_msg"] = "query failed";
            print(json_encode($output));
        }

        $no_of_rows1 = mysqli_num_rows($result1);
        $no_of_rows2 = mysqli_num_rows($result2);

        if ($no_of_rows1 == 1)
            $output["flag"]="user already exists";   //user already exists
        else if ($no_of_rows2 == 1)
            $output["flag"]="mobile already exists";   //mobile already registered
        else
        {
            $output["flag"]="succeed";  //user registered
            mysqli_query($dblink,"INSERT INTO users (username, password, name, mobile, picture, gender, birthyear) VALUES
               ('$user', '$pass', '$name', '$mob', '$pic', '$gen', '$birth')");
        }

        return json_encode($output);
    }

}

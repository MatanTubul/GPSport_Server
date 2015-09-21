<?php
/**
 * Created by PhpStorm.
 * User: Nir B
 * Date: 22/09/2015
 * Time: 02:00
 */

include 'response_process.php';
require_once 'PasswordFunctions.php';
class Register implements ResponseProcess{

    public function dataProcess($dblink) {

        $name = $_POST['firstname'];
        $email = $_POST['email'];//send the email as userName
        $pass = ($_POST['password']);
        $mob = $_POST['mobile'];
        $birth = $_POST['birthyear'];
        $gen = $_POST['gender'];
        $pic = $_POST['picture'];

        $passFunc = new PasswordFunctions();

        $salt = $passFunc->random_password();
        $pass = $passFunc->encrypt($pass, $salt);


        $imageName = $mob.".jpg";
        $filePath = "images/".$imageName;
        if(file_exists($filePath))
        {
            unlink($filePath); // delete the old file
        }

        //create a new empty file
        $myfile =  fopen($filePath,"w") or die("uUnable to open file!");
        file_put_contents($filePath,base64_decode($pic));

        $output = array();

        $result1 = mysqli_query($dblink,"SELECT * FROM users WHERE users.email= '$email'");
        $result2 = mysqli_query($dblink,"SELECT * FROM users WHERE users.mobile= '$mob'");

        if((!$result1) || (!$result2)){
            $output["error_msg"] = "signup query failed";
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
            $userStatus = 0;
            //user registered
            //insert user details to DB
            $insertResult=mysqli_query($dblink,"INSERT INTO users (name, email, gender, age, password, salt, userstatus, image,mobile) VALUES
               ('$name', '$email', '$gen', '$birth', '$pass','$salt','$userStatus','$imageName','$mob')") or die((mysqli_error($dblink)));
            if(!$insertResult)
            {
                $output["query"]="error";
                $output["error_msg"] = $insertResult;
                print(json_encode($output));
            }else
                $output["flag"]="succeed";
        }

        return json_encode($output);
    }

}

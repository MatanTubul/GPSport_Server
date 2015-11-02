<?php
/**
 * Created by PhpStorm.
 * User: Nir B
 * Date: 16/08/2015
 * Time: 17:52
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
        $gcm_id = $_POST['regid'];
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
        if ($no_of_rows1 == 1) {
            $output["usercheck"] = "user already exists";       //user already exists
            $output["flag"] = "wrong input";
        }
        else if ($no_of_rows2 == 1){
            $output["mobilecheck"] = "mobile already exists";   //mobile already registered
            $output["flag"] = "wrong input";
        }
        else
        {
            $userStatus = 0;
            //user registered
            //insert user details to DB
            $insertResult=mysqli_query($dblink,"INSERT INTO users (fname, email, gender, age, password, salt, userstatus, image,mobile,gcm_id) VALUES
               ('$name', '$email', '$gen', '$birth', '$pass','$salt','$userStatus','$imageName','$mob','$gcm_id')") or die((mysqli_error($dblink)));
            if(!$insertResult)
            {
                $output["query"]="error";
                $output["error_msg"] = $insertResult;
                print(json_encode($output));
            }else {
                $output["flag"]="succeed";
                $output["usecase"]="register";
            }
        }
        return json_encode($output);
    }
}
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
        $email = $_POST['email'];//send the email as userName
        $pass = $_POST['password'];
        $mob = $_POST['mobile'];
        $birth = $_POST['birthyear'];
        $gen = $_POST['gender'];
        $pic = $_POST['picture'];


        $imageName = $mob.".jpg";
        $filepath = "images/".$imageName;
        if(file_exists($filepath))
        {
            unlink($filepath); // delete the old file
        }

        //create a new empty file
        $myfile =  fopen($filepath,"w") or die("uUnable to opne file!");
        file_put_contents($filepath,base64_decode($pic));

        $output = array();

        $result1 = mysqli_query($dblink,"SELECT * FROM users WHERE users.email= '$email'");
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
            $userStatus = 0;
              //user registered
            $inserResult=mysqli_query($dblink,"INSERT INTO users (name, email, gender, age, password, userstatus, image,mobile) VALUES
               ('$name', '$email', '$gen', '$birth', '$pass','$userStatus','$imageName','$mob')") or die((mysqli_error($dblink)));
            if(!$inserResult)
            {
                $output["query"]="error";
                $output["error_msg"] = $inserResult;
                print(json_encode($output));
            }else
            $output["flag"]="succeed";
        }

        return json_encode($output);
    }

}

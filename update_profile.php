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

    function ValidateUniqueField($dblink , $fieldToBeChecked)
    {
        $result = mysqli_query($dblink,"SELECT * FROM users WHERE users.email= '$fieldToBeChecked'");

        if(!$result) {
            $output["error_msg"] = "signup query failed";
            print(json_encode($output));
        }

        $no_of_rows = mysqli_num_rows($result);
        if ($no_of_rows == 1)
            return false;
        return true;
    }

    public function dataProcess($dblink) {
        $name = $_POST['firstname'];

        $prevEmail = $_POST['prevemail'];
        $newEmail = $_POST['newemail'];
        $pass = ($_POST['password']);
        $newMob = $_POST['newmobile'];
        $prevMob = $_POST['prevmobile'];
        $birth = $_POST['birthyear'];
        $gen = $_POST['gender'];
        $pic = $_POST['picture'];
        $whoAsChanged = $_POST ['changed'];
        //none, email only, mobile only, both
        $output = array();

        $output["usercheck"] = "user check";
        $output["mobilecheck"] = "mobile check";
//validation of unique fields
        switch ($whoAsChanged) {
            case "none":
                break;
            case "email only":
                if (!ValidateUniqueField($dblink,$newEmail)) {
                    $output["usercheck"] = "user already exists";       //user already exists
                    $output["flag"] = "wrong input";
                    return json_encode($output);
                }
                break;
            case "mobile only":
                if (!ValidateUniqueField($dblink,$newMob)) {
                    $output["mobilecheck"] = "mobile already exists";   //mobile already registered
                    $output["flag"] = "wrong input";
                    return json_encode($output);
                }
                break;
            case "both":
                if (!($valid1 = ValidateUniqueField($dblink,$newEmail)))
                    $output["usercheck"] = "user already exists";       //user already exists
                if (!($valid2 = ValidateUniqueField($dblink,$newMob)))
                    $output["mobilecheck"] = "mobile already exists";   //mobile already registered
                if ((!$valid1) || (!$valid2)) {
                    $output["flag"] = "wrong input";
                    return json_encode($output);
                }
                    break;
        }

        $passFunc = new PasswordFunctions();

        $salt = $passFunc->random_password();
        $pass = $passFunc->encrypt($pass, $salt);

        $imageName = $prevMob.".jpg";
        $filePath = "images/".$imageName;
        if(file_exists($filePath))
        {
            unlink($filePath); // delete the old file
        }

        $imageName = $newMob.".jpg";
        $filePath = "images/".$imageName;
        //create a new empty file
        $myfile =  fopen($filePath,"w") or die("Unable to open file!");
        file_put_contents($filePath,base64_decode($pic));

        //update user details to DB
        $updateResult=mysqli_query($dblink,"UPDATE users SET (name, email, gender, age, password, salt, image,mobile) VALUES
        ('$name', '$newEmail', '$gen', '$birth', '$pass','$salt','$imageName','$newMob') WHERE users.email='$prevEmail'") or die((mysqli_error($dblink)));

        if(!$updateResult)
            {
                $output["query"]="error";
                $output["error_msg"] = $updateResult;
                print(json_encode($output));}
        else {
            $output["flag"]="succeed";
            $output["usecase"] = "update";
        }

        return json_encode($output);
    }


}

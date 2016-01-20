<?php
/**
 * Created by PhpStorm.
 * handle all the requests for updating the user profile.
 * User: Nir B
 * Date: 22/09/2015
 * Time: 02:00
 */

include 'response_process.php';
require_once 'PasswordFunctions.php';
class UpdateProfile implements ResponseProcess{

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
        $gcm_id = $_POST['regid'];
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
        $dbF = new DBFunctions($dblink);

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
        $oldFilePath = "images/".$imageName;
        unlink(realpath($oldFilePath)); // delete the old file


        $imageName = $newMob.".jpg";
        $newFilePath = "images/".$imageName;

        //create a new empty file
        $myfile =  fopen($newFilePath,"w") or die("Unable to open file!");
        file_put_contents($newFilePath,base64_decode($pic));

        //update user details to DB
        /*$updateResult=mysqli_query($dblink,"UPDATE users SET fname = '$name', email = '$newEmail', gender = '$gen',
        age = '$birth', password = '$pass', salt = '$salt', image = '$imageName', mobile = '$newMob', gcm_id = '$gcm_id'
        WHERE users.email = '$prevEmail' ") or die((mysqli_error($dblink)));*/
        $updateResult = $dbF ->  UpdateProfile($name,$newEmail,$gen,$birth,$pass,$salt,$imageName,$newMob,$gcm_id,$prevEmail);

        if(!$updateResult)
            {
                $output["query"]="error";
                $output["error_msg"] = $updateResult;
                print(json_encode($output));}
        else {
            $output["flag"]="succeed";
        }

        return json_encode($output);
    }


}

<?php
/**
 * Created by PhpStorm.
 * class that handle all the registration requests,
 * in case the user is already register the registration failed.
 * User: Nir B
 * Date: 16/08/2015
 * Time: 17:52
 */
include 'response_process.php';
require_once 'PasswordFunctions.php';
require_once 'DBFunctions.php';
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
        $dbF = new DBFunctions($dblink);
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

        $result1 = $dbF ->getUserByEmail($email);
        $result2 = $dbF -> getUserByMobile($mob);

        if((!$result1) || (!$result2)){
            $output["error_msg"] = "signup query failed";
            print(json_encode($output));
        }
        $no_of_rows1 = mysqli_num_rows($result1);
        $no_of_rows2 = mysqli_num_rows($result2);
        $output["usercheck"] = "true";
        $output["mobilecheck"] = "true";
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
            $insertResult = $dbF -> InsertUserIntoDB($name,$email,$gen,$birth,$pass,$salt,$userStatus,$imageName,$mob,$gcm_id);

            if(!$insertResult)
            {
                $output["query"]="error";
                $output["error_msg"] = $insertResult;
                print(json_encode($output));
            }else {
                    $output["flag"]="succeed";
                    $to      = $email;
                    $subject = 'GPSport Registration';
                    $message = $name.",\n"."Thank you for signing  up to GPSport application"."\n"."In any problem please contact our support at GPSport.braude@gmail.com"."\n"."Thank You"."\n"."GPSport Team";
                    $headers = 'From: noreply@gpsport.co.nf' . "\r\n" .
                        'X-Mailer: PHP/' . phpversion();
                    mail($to, $subject, $message, $headers);

                }
        }
        return json_encode($output);
    }
}
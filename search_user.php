<?php
/**
 * Created by PhpStorm.
 * class that handle all the requests of searching users via the
 * creation of private event or update private event
 * User: matant
 * Date: 9/24/2015
 * Time: 11:29 AM
 */
include 'response_process.php';
require_once 'DBFunctions.php';
class SearchUser implements ResponseProcess{

    public function dataProcess($dblink)
    {
        $output = array();
        $name = $_POST["name"];
        $output["flag"]="user found";
        $dbF = new DBFunctions($dblink);
        $result = $dbF -> SearchUserByName($name);

        if(!$result){
            $output["flag"] = "query failed";
            $output["query_msg"] = $result;

        }else{
            $no_of_rows = mysqli_num_rows($result);
            if ($no_of_rows < 1)
                $output["flag"]="user not found";   //user not found
            else{
                $output["flag"]="user found";
                $output["users"] = array();
                while($row = mysqli_fetch_assoc($result))
                {
                    $img_path = "images/".$row["image"];

                    $imgdata = base64_encode(file_get_contents($img_path));
                    $output["users"][] =  array("id" =>$row["id"],"name"=> $row["fname"],"email"=> $row["email"],"mobile" => $row["mobile"],"image" => $imgdata, "gender" => $row["gender"],"age" => $row["age"]);
                }
            }
        }
        echo json_encode($output);

    }
}
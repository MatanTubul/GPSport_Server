<?php
/**
 * Created by PhpStorm.
 * User: matant
 * Date: 9/24/2015
 * Time: 11:29 AM
 */
include 'response_process.php';
class SearchUser implements ResponseProcess{


    public function dataProcess($dblink)
    {
        $output = array();
        $name = $_POST["name"];
        $output["flag"]="user found";
        $query = "SELECT * FROM users WHERE users.name LIKE '$name%'";

        $result = mysqli_query($dblink,$query) or die (mysqli_error($dblink));

        if(!$result){
            $output["flag"] = "query failed";
            $output["query_msg"] = $result;

        }else{
            $no_of_rows = mysqli_num_rows($result);
            if ($no_of_rows < 1)
                $output["flag"]="user not found";   //user not found
            else{
                $output["flag"]="user found";
                while($row = mysqli_fetch_assoc($result))
                {
                    $jsonArrayPbject[]= array("name"=> $row["name"],"email"=> $row["email"],"mobile" => $row["mobile"]);
                    $output[$row["email"]] = $jsonArrayPbject;
                }
            }

        }

        echo json_encode($output);

    }
}
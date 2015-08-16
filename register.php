<?php
/**
 * Created by PhpStorm.
 * User: matant
 * Date: 8/10/2015
 * Time: 3:23 PM
 */
//implement register to GPSport
class Register{
    public  $output;

    function _construct()
    {
        $param =array('msg'=>'false');

    }

    public  function reg($params){
        $output = array();
        $query = "SELECT * FROM users WHERE users.username ='$params->username' OR users.email = '$params->email'OR users.mobile='$params->mobile'";
        $result = mysql_query($query);
        $no_of_rows = mysql_num_rows($result);
        if($no_of_rows > 0)
        {
            $row = mysql_fetch_assoc($result);
            if($params->username == $row["username"])
            {
                $output["user_record"]="username already exist";
            }elseif($params->email == $row["email"])
            {
                $output["email_record"]= "email already exist";
            }elseif($params->mobile == $row["mobile"])
            {
                $output["mobile_record"]="mobile already exist";
            }
            $output['msg']=""
            return false;
        }else
        {

        }

    }


}
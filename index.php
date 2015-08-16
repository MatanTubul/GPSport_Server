<?php
/**
 * Created by PhpStorm.
 * User: Nir B
 * Date: 16/08/2015
 * Time: 15:24
 */

error_reporting(0);
require 'connection.php';

$tag = $_POST['tag'];
if ($tag == "login")
{
    include 'login.php';
    $login = new Login();
    echo ($login->dataProcess($dblink));
}
else if ($tag == "signup")
  {
      include 'register.php';
      $register = new register();
      echo ($register->dataProcess ($dblink));
  }

//else signup,forgotpassword and other...

$dblink->close();

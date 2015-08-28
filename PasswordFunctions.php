<?php
/**
 * Created by PhpStorm.
 * User: matant
 * Date: 8/28/2015
 * Time: 10:08 AM
 */

class PasswordFunctions {
    function random_password($length = 8)
    {
        // start with a blank password
        $password = "";

        //define our alphabet for the salt
        $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";

        // we refer to the length of $possible a few times, so let's grab it now
        $maxlength = strlen($possible);

        // check for length overflow and truncate if necessary
        if ($length > $maxlength) {
            $length = $maxlength;
        }

        // set up a counter for how many characters are in the password so far
        $i = 0;

        // add random characters to $password until $length is reached
        while ($i < $length) {

            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, $maxlength-1), 1);

            // have we already used this character in $password?
            if (!strstr($password, $char)) {
                // no, so it's OK to add it onto the end of whatever we've already got...
                $password .= $char;
                // ... and increase the counter by one
                $i++;
            }
        }

        return $password;
    }

    function encrypt($plaintext, $salt)
    {
        $td = mcrypt_module_open('cast-256', '', 'ecb', '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $salt, $iv);
        $encrypted_data = mcrypt_generic($td, $plaintext);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        $encoded_64 = base64_encode($encrypted_data);
        return trim($encoded_64);
    }

    function decrypt($crypttext, $salt)
    {
        $decoded_64=base64_decode($crypttext);
        $td = mcrypt_module_open('cast-256', '', 'ecb', '');
        $iv = mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $salt, $iv);
        $decrypted_data = mdecrypt_generic($td, $decoded_64);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return trim($decrypted_data);
    }

}
<?php
/**
 * Created by PhpStorm.
 * interface that implemented by most of the classes.
 * this interface has a method that processing the request and send the result back.
 * User: Nir B
 * Date: 16/08/2015
 * Time: 15:18
 */

interface ResponseProcess
{
    public function dataProcess($dblink);

}


<?php
/**
 * Created by PhpStorm.
 * class that handle all the search (from current location or specific address)
 * the class return a list of all the events. in case the list is empty the user get a proper message.
 * User: nirb
 * Date: 11/18/2015
 * Time: 5:47 PM
 */

include 'response_process.php';
require_once 'DBFunctions.php';

class search_events implements ResponseProcess
{

    public function dataProcess($dblink)
    {
        $output = array();
        $dbF = new DBFunctions($dblink);

        $search = $_POST["search"];
        $user_lat = floatval($_POST["lat"]);
        $user_long = floatval($_POST["lon"]);
        $radius = floatval($_POST["radius"]);

        if ($search == "search_by_default")
            $event_query = $dbF->SearchEventsForDefault($user_long, $user_lat, $radius);
        elseif ($search == "search_all_events")
                $event_query = $dbF->getAllDBActiveEvents();
            else {
                        $sport = $_POST["kind_of_sport"];
                        $gen = $_POST["gender"];
                        $age = $_POST["min_age"];
                        $public = $_POST["public"];
                        $private = $_POST["private"];

                        //Process start date and time
                        $start_date = $_POST["start_date"];
                        list($day, $month, $year) = explode('/', $start_date);
                        $start_date = $year . '-' . $month . '-' . $day;

                        $start_time = $_POST["start_time"];
                        $combined_date_and_time_start = $start_date . ' ' . $start_time .':00';
                        $start = strtotime($combined_date_and_time_start);

                        //Process end date and time
                        $end_date = $_POST["end_date"];
                        list($day, $month, $year) = explode('/', $end_date);
                        $end_date = $year . '-' . $month . '-' . $day;

                        $end_time = $_POST["end_time"];
                        $combined_date_and_time_end = $end_date . ' ' . $end_time.':00';
                        $end = strtotime($combined_date_and_time_end);

                        $output["start"] = $combined_date_and_time_start;
                        $output["end"] = $combined_date_and_time_end;

                        $event_query = $dbF-> SearchEventsForRequest($user_long, $user_lat, $radius, $start, $end, $sport,$gen, $age, $public, $private, $combined_date_and_time_start, $combined_date_and_time_end);
                    }

        if (!$event_query) {
            $output["flag"] = "failed";
            $output["msg"] = $event_query;

        } else {
            $output["flag"] = "success";

            $events = array();
            $i = 0;
            while ($row = mysqli_fetch_assoc($event_query)) {
                $events[] = $row;
                $i++;
            }
            $output["events"] = $events;
            $output["iterations"] = $i;

        }


        return json_encode($output);
    }
}


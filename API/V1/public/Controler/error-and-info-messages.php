<?php
    /**
     * prints out a error message and instantly shuts down
     */
    function error_function($status_code, $message) {
        $array = array("error" => $message);
        echo json_encode($array, true);
        http_response_code($status_code);
        die();
    }
    /**
     * prints out a information message and instantly shuts down
     */
    function message_function($status_code, $message) {
        $array = array("Message:" => $message);
        echo json_encode($array, true);
        http_response_code($status_code);
        die();
    }
?>
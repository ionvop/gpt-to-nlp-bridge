<?php

function Debug() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

function Breakpoint($message) {
    header("Content-type: application/json");
    print_r($message);
    exit();
}

function LogData($input) {
    $date = date("Y-m-d H-i-s");
    return file_put_contents("log/{$date}.json", $input);
}

?>
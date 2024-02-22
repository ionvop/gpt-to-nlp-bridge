<?php

function SendCurl($url, $method, $headers, $data) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);
    return $result;
}

function LogData($input) {
    if (file_exists("log") == false) {
        mkdir("log");
    }
    
    $date = date("Y-m-d H-i-s");
    return file_put_contents("log/{$date}.json", $input);
}

function GetHeaders() {
    if (!function_exists('getallheaders')) {
        function getallheaders() {
            $headers = [];
    
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            
            return $headers;
        }
    }
    
    return getallheaders();
}

?>
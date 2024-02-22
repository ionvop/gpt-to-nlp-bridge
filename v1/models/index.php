<?php

include("../common.php");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
$headers = GetHeaders();
$model = explode("/v1/models/", $_SERVER["REQUEST_URI"])[1];

$models = [
    "object" => "list",
    "data" => [
        [
            "id" => "finetuned-llama-2-70b",
            "object" => "model",
            "created" => null,
            "owned_by" => "nlpcloud"
        ],
        [
            "id" => "dolphin",
            "object" => "model",
            "created" => null,
            "owned_by" => "nlpcloud"
        ],
        [
            "id" => "chatdolphin",
            "object" => "model",
            "created" => null,
            "owned_by" => "nlpcloud"
        ]
    ]
];

if (strlen($model) > 0) {
    foreach ($models["data"] as $key => $value) {
        if ($value["id"] == $model) {
            $result = $value;

            $log = [
                "headers" => $headers,
                "request" => $_POST,
                "response" => $result,
                "raw_body" => file_get_contents("php://input")
            ];
            
            LogData(json_encode($log));
            exit(json_encode($result, JSON_PRETTY_PRINT));
        }
    }

    $result = [
        "error" => [
            "message" => "The model '" . $model . "' does not exist",
            "type" => "invalid_request_error",
            "param" => "model",
            "code" => "model_not_found"
        ]
    ];

    $log = [
        "headers" => $headers,
        "request" => $_POST,
        "response" => $result,
        "raw_body" => file_get_contents("php://input")
    ];
    
    LogData(json_encode($log));
    exit(json_encode($result, JSON_PRETTY_PRINT));
}

$result = $models;

$log = [
    "headers" => $headers,
    "request" => $_POST,
    "response" => $result,
    "raw_body" => file_get_contents("php://input")
];

LogData(json_encode($log));
exit(json_encode($result, JSON_PRETTY_PRINT));

?>
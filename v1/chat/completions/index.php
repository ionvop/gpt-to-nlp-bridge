<?php

include("../../common.php");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
$headers = GetHeaders();
$apiKey = explode("Bearer ", $headers["Authorization"])[1];

if (json_decode(file_get_contents("php://input")) != null) {
    $_POST = json_decode(file_get_contents("php://input"), true);
}

if (substr($_POST["model"], 0, 13) == "gpt-3.5-turbo") {
    $_POST["model"] = "chatdolphin";
}

$previousRole = "user";
$context = "";
$history = [];

$message = [
    "input" => "",
    "response" => ""
];

foreach ($_POST["messages"] as $key => $value) {
    switch ($value["role"]) {
        case "system":
            $context = $value["content"];
            break;
        case "user":
            if ($previousRole == "assistant") {
                $history[] = $message;
                
                $message = [
                    "input" => "",
                    "response" => ""
                ];
            }

            $message["input"] .= $value["content"];
            $previousRole = "user";
            break;
        case "assistant":
            $message["response"] .= $value["content"];
            $previousRole = "assistant";
            break;
        default:
            $result = [
                "error" => [
                    "code" => "invalid_role",
                    "message" => "Invalid role"
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
}

$reqHeaders = [
    "Content-Type: application/json",
    "Authorization: Token " . $apiKey
];

$reqData = [
    "input" => $message["input"],
    "context" => $context,
    "history" => $history
];

$reqData = json_encode($reqData);
$res = SendCurl("https://api.nlpcloud.io/v1/gpu/" . $_POST["model"] . "/chatbot", "POST", $reqHeaders, $reqData);
$res = json_decode($res, true);

if ($res == null) {
    $result = [
        "error" => [
            "code" => "unknown_error",
            "message" => "Something went wrong"
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

$result = [
    "id" => uniqid("chatcmpl-"),
    "object" => "chat.completion",
    "created" => time(),
    "model" => $_POST["model"],
    "system_fingerprint" => null,
    "choices" => [
        [
            "index" => 0,
            "message" => [
                "role" => "assistant",
                "content" => $res["response"]
            ],
            "logprobs" => null,
            "finish_reason" => "stop"
        ]
    ],
    "usage" => [
        "prompt_tokens" => null,
        "completion_tokens" => null,
        "total_tokens" => null
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

?>
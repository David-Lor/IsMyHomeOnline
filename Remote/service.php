<?php

/*
This script is programmed with a task manager like cron.
service.php will check lastContact time and send a message via Telegram if the status changed.
Global session variables used:
    * lastContact: time() of the lastContact received at the endpoint
    * lastStatus: home status (up=TRUE, down=FALSE) on the previous execution of service.php
Session ID is the Endpoint Token defined on config.json
*/

//read json and get variables
$json = json_decode(file_get_contents("config.json"), true);
$token = $json["Endpoint"]["Token"];
$maxSeconds = $json["Time"]["LastContactMaxSeconds"];
//telegram variables
$tokenTelegram = $json["TelegramBot"]["Token"];
$chatidTelegram = $json["TelegramBot"]["ChatID"];
$textDown = $json["TelegramBot"]["MessageDown"];
$textUp = $json["TelegramBot"]["MessageUp"];

//get lastContact time from session
session_id($token);
session_start();
if (!isset($_SESSION["lastContact"])) { //if lastContact not set, initialize to current time
    $_SESSION["lastContact"] = time();
}
if (!isset($_SESSION["lastStatus"])) { //if lastStatus not set, initialize to FALSE
    $_SESSION["lastStatus"] = FALSE;
}
$lastContact = $_SESSION["lastContact"];
$lastStatus = $_SESSION["lastStatus"];

//check if time limit expired
if (time() - $lastContact >= $maxSeconds) { //lastContact not received in time - DOWN
    echo "Last Contact NOT received in time, HOME DOWN!\n";
    $currentStatus = FALSE;
} else { //lastContact received correctly, in time - UP
    echo "Last Contact received in time, HOME UP\n";
    $currentStatus = TRUE;
}

//define Telegram sending functions
function send($text) {
    global $tokenTelegram;
    global $chatidTelegram;
    $url = "https://api.telegram.org/bot";
    $url .= $tokenTelegram;
    $url .= "/sendMessage";
    $data = array("chat_id" => $chatidTelegram, "text" => $text);
    $options = array(
        'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    //TODO check if message was sent correctly?
    //TODO hide errors for not showing URL with the token
}
function sendUp() {
    global $textUp;
    echo "Sending message to Telegram (home UP)";
    send($textUp);
}
function sendDown() {
    global $textDown;
    echo "Sending message to Telegram (home DOWN)";
    send($textDown);
}

//send message if status changed
if ($lastStatus != $currentStatus) {
    $_SESSION["lastStatus"] = $currentStatus;
    session_write_close();
    if ($currentStatus) { //home UP
        sendUp();
    } else { //home DOWN
        sendDown();
    }
}

?>
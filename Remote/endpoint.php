<?php

//read json and get valid token and return message
$json = json_decode(file_get_contents("config.json"), true);
$token = $json["Endpoint"]["Token"];
$return = $json["Endpoint"]["Return"];

//parse POST data, get token received
$postToken = htmlspecialchars($_POST["token"]);

if ($token == $postToken) { //valid token
    //get session (id=token)
    session_id($token);
    session_start();
    //update lastContact variable, echo "OK" and close session
    $_SESSION["lastContact"] = time();
    session_write_close();
    echo $return;
} else { //wrong token
    echo "Access denied";
}

?>
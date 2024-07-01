<?php
include('../bd/connect.php');

$db = new db;

$maps = array();
$maps += ["get" => true];

$messages = $db->q("SELECT `id`, `who`, `what`, `whenDate` FROM (SELECT * FROM `post` WHERE `whom`='WORLD' ORDER BY `whenDate` DESC LIMIT 0,100) AS `newTab` ORDER BY `whenDate` ASC;");
$maps += ["countM" => mysqli_num_rows($messages)];
$i = 0;
while($message = mysqli_fetch_array($messages)){
    $maps += [ $i => [
        "id" => $message['id'],
        "who" => $message['who'],
        "what" => $message['what'],
        "whenDate" => $message['whenDate']
    ]];
    $i = $i + 1;
}

$err = $db->err();
if (!is_null($err)){
    $maps += ["err" => $err];
    $maps["send"] = false;
}


$json = json_encode($maps);
echo $json;
?>
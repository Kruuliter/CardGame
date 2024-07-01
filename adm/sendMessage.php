<?php
include('../bd/connect.php');

$maps = array();
$maps += ["send" => false];
if (isset($_POST['user'], $_POST['message'])) {
    $db = new db;

    $db->q("INSERT INTO `post`(`who`, `whom`, `what`) VALUES ('".$_POST['user']."','WORLD','".$_POST['message']."');");
    $maps["send"] = true;

    $err = $db->err();
    if (!is_null($err)){
        $maps += ["err" => $err];
        $maps["send"] = false;
    }
    unset($_POST['user'], $_POST['message']);
}

$json = json_encode($maps);
echo $json;
?>
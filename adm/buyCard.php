<?php
include('../bd/connect.php');

$maps = array();
$maps += ["bougth" => false];
if (isset($_POST['user'], $_POST['idCard'])) {
    $db = new db;


    $price = $db->q("SELECT * FROM `cards` WHERE `id`=".$_POST['idCard'].";");

    $p = 0;
    while($pr = mysqli_fetch_array($price)){
        $p = $pr["price"];
    }
    $price = $p;

    $userCash = $db->q("SELECT `cash` FROM `users` WHERE `login`='".$_POST['user']."';");

    $usercas = 0;
    while($userC = mysqli_fetch_array($userCash)){
        $usercas = $userC["cash"];
    }
    $userCash = $usercas;

    if ($userCash >= $price){
        $countCards = $db->q("SELECT `count` FROM `cardsinbox` WHERE `users`='".$_POST['user']."' AND `idCards`=".$_POST['idCard'].";");
    

        if (mysqli_num_rows($countCards) > 0){
            $count = 1;
            while($countC = mysqli_fetch_array($countCards)){
                $count = $countC["count"];
            }
            $countCards = $count + 1;

            $db->q("UPDATE `cardsinbox` SET `count` = ".$count." WHERE `users`='".$_POST['user']."' AND `idCards`=".$_POST['idCard'].";");
        }else{
            $db->q("INSERT INTO `cardsinbox`(`users`,`idCards`) VALUES ('".$_POST['user']."', ".$_POST['idCard'].");");
        }

        $db->q("UPDATE `users` SET `cash` = ".($userCash - $price)." WHERE `login`='".$_POST['user']."';");

        $maps["bougth"] = true;
    }else{
        $maps += ["errCash" => "недостаточно денег ".($price - $userCash)." - не хватает"];
    }
    


    $err = $db->err();
    if (!is_null($err)){
        $maps += ["err" => $err];
    }
    unset($_POST['user'], $_POST['idCard']);
}

$json = json_encode($maps);
echo $json;
?>
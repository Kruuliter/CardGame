<?php
include('../bd/connect.php'); # Подключается класс БД

$maps = array();
if (isset($_POST['user'], $_POST['list'])) { # Создание "таблетки"
    $db = new db;

    $cardsList = $_POST['list'];

    if (empty($cardsList)){
        $cardsList = 1;
    }

    $countCards =  mysqli_num_rows($db->q("SELECT `id` FROM `cards`;"));
    $maps +=["countCards" => $countCards];
    $countCards = ceil($countCards / 21);

    if ($cardsList <= 0){
        $cardsList = 1;
    }
    $maps +=["countButtons" => $countCards];
    $cards = $db->q("SELECT `cards`.`id` AS `idCards`, CASE WHEN `cardsinbox`.`users` = '".$_POST['user']."' THEN 'yes' ELSE 'no' END AS `have`, `cardImage`, `name`, `hp`, `mp`, `atk`, `price` FROM `cards` LEFT OUTER JOIN `cardsinbox` ON `cards`.`id` = `cardsinbox`.`idCards` LIMIT ".(($cardsList - 1)*21).", 21;");


    if ($cards == '') {
        $maps += ["itSell" => false];
    } else {
        $maps += ["itSell" => true];
        $i = 0;
        while($oneCard = mysqli_fetch_array($cards)){
            $maps += ["settingsCardId=".$i."" => [
                "idCard" => $oneCard['idCards'],
                "userHave" => $oneCard['have'],
                "cardImage" => $oneCard['cardImage'],
                "cardName" => $oneCard['name'],
                "cardMp" => $oneCard['mp'],
                "cardAtk" => $oneCard['atk'],
                "cardHp" => $oneCard['hp'],
                "cardPrice" => $oneCard['price']
            ]];
            $i = $i + 1;
        }
    }
    unset($_POST['user'], $_POST['list']);
}

$json = json_encode($maps);
echo $json;
?>
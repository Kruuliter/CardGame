<?php
    include('../bd/battle.php');
    include('../bd/connect.php');

    $maps = array();
if (isset($_POST['user'], $_POST['isNext'], $_POST['info'])) { # Создание "таблетки"
    $db = new db;
    $battl = new Battle($_POST['user'], $db);
    $maps += ['isFind' => $battl->oponentIsFind()];
    $mass = $_POST['info'];
    if($maps['isFind']){
        if($_POST['isNext']){
            if($battl->getStatus() == $battl->getStatusOwner()){
                $battl->attack();
            }
            $massCards = array();
            $massCards = explode("|", $mass[0]);
            for ($i = 0; $i < count($massCards); $i++){
                $battl->addInBattle($i, $massCards[$i]);
            }

            $massCards = explode("|", $mass[1]);
            for ($i = 0; $i < count($massCards); $i++){
                $battl->addInHands($i, $massCards[$i]);
            }

            $oldRound = $battl->getRound();
            $battl->next();
            $nowRound = $battl->getRound();

            if(($nowRound - $oldRound) > 0){
                $battl->generate();
            }
        }
        $maps += ['info' => $battl->getInfo()];
    }

    unset($_POST['user'], $_POST['isNext'], $_POST['info']);
}

$json = json_encode($maps);
echo $json;
?>
<?php
/*
    * Класс боя
    *
    */
include("user.php");

class Battle {
    private db $db;
    private User $owner;
    private User $oponent;
    private int $idCom;

    function __construct(string $u, db $db)
    {
        $this->db = new db();

        $this->owner = new User($u, $db);

        $rooms = $this->db->q("SELECT `idRoom` FROM `camera` WHERE `player` = '".$this->owner->getName()."';");
        

        if (mysqli_num_rows($rooms) == 1){
            while($row = mysqli_fetch_array($rooms)){
                $this->idCom = $row["idRoom"];
            }
        }elseif(mysqli_num_rows($rooms) == 0){
            $oponents = $this->db->q("SELECT `idRoom`, COUNT(`player`) AS `countPlayer` FROM `camera` GROUP BY `idRoom`;");
            $mass = [];
            while($row = mysqli_fetch_array($oponents)){
                if((int)$row["countPlayer"] > 0){
                    $mass += [['idRoom' => $row["idRoom"], 'count' => $row["countPlayer"]]];
                }
            }
            $count = count($mass);

            if ($count > 0){
                $oponentId = rand(1, $count);

                $this->db->q("INSERT INTO `camera`(`player`, `idRoom`) VALUES ('".$this->owner->getName()."',".$mass[$oponentId]['idRoom'].");");
                $stamp = $this->getDate();
                $this->db->q("UPDATE `battle` SET `status`='P1',`nowRound`=1,`nextChange`=".$stamp." WHERE `id` = ".$mass[$oponentId]['idRoom'].";");
                $this->owner->setStatus("P2");
                $this->idCom = $mass[$oponentId]['idRoom'];
            }elseif($count == 0){
                $idCom = $this->db->q("SELECT CASE WHEN MAX(id) IS NOT NULL THEN MAX(id) ELSE 0 END AS `id` FROM `battle`;");
                $idCom = mysqli_fetch_assoc($idCom);
                $this->idCom = (int) $idCom;
                $this->idCom = $this->idCom + 1;
                $this->db->q("INSERT INTO `battle`(`id`) VALUES (".$this->idCom.");");
                $this->db->q("INSERT INTO `camera`(`player`, `idRoom`) VALUES ('".$this->owner->getName()."',".$this->idCom.");");
            }
        }

        $this->oponentIsFind();
    }

    public function oponentIsFind(){
        $flag = false;
        $oponent = $this->db->q("SELECT `player` FROM `camera` WHERE `idRoom`=".$this->idCom." AND `player`<>'".$this->owner->getName()."';");
        if(mysqli_num_rows($oponent) > 0){
            $flag = true;
            $logOponent = "";
            while($row = mysqli_fetch_array($oponent)){
                $logOponent = $row["player"];
            }

            if($logOponent != ""){
                if(is_null($this->oponent)){
                    $this->oponent = new User($logOponent, $this->db);
                }
            }else{
                throw new Exception('I don`t know oponent');
            }
            
            if($this->owner->getStatus()==$this->oponent->getStatus()){
                $this->owner->setStatus("P1");
                $this->oponent->setStatus("P2");
            }
        }
        return $flag;
    }
    
    public function getNameOwner(){
        return $this->owner->getName();
    }

    public function getNameOponent(){
        return $this->oponent->getName();
    }

    private function getDate(){
        $dateTime = new DateTime();
        $dateTime->modify('+1 minutes');
        return $dateTime->format('Y-m-d H:i:s');
    }

    private function nextRound(){
        $nowRound = 0;
        $round = $this->db->q("SELECT `nowRound` FROM `battle` WHERE `id` = ".$this->idCom.";");
        while($row = mysqli_fetch_array($round)){
            $nowRound = $row['nowRound'];
        }
        $this->db->q("UPDATE `battle` SET `status`='P1',`nowRound`=".($nowRound + 1).",`nextChange`=".$this->getDate()." WHERE `id` = ".$this->idCom.";");
    }

    public function getRound(){
        $nowRound = 0;
        $round = $this->db->q("SELECT `nowRound` FROM `battle` WHERE `id` = ".$this->idCom.";");
        while($row = mysqli_fetch_array($round)){
            $nowRound = $row['nowRound'];
        }
        return $nowRound;
    }

    public function next(){
        if($this->getStatus() == 'P2'){
            $this->nextRound();
        }elseif($this->getStatus() == 'P1'){
            $this->db->q("UPDATE `battle` SET `status`='P2',`nextChange`=".$this->getDate()." WHERE `id` = ".$this->idCom.";");
        }
    }

    public function addInBattle(int $point, string $card){
        $this->owner->setCardsInBattle($point, $card);
    }

    public function addInHands(int $point, string $card){
        $this->owner->setCardsInHands($point, $card);
    }

    public function getStatus(){
        $status = 'find';
        $stat = $this->db->q("SELECT `status` FROM `battle` WHERE `id`=".$this->idCom.";");
        while($row = mysqli_fetch_array($stat)){
            $status = $row['status'];
        }
        return $status;
    }

    public function getStatusOwner(){
        return $this->owner->getStatus();;
    }

    public function getInfo(){
        $mass = [];
        if($this->oponentIsFind()){
            $mass += [
                'owner' => [
                    'name' => $this->owner->getName(),
                    'hp' => $this->owner->getHealthPoint(),
                    'mp' => $this->owner->getManaPoint(),
                    'status' => $this->owner->getStatus(),
                    'cardsInBattle' => $this->owner->getCardsInBattle(),
                    'cardsInHands' => $this->owner->getCardsInHands()
                ],
                'oponent' => [
                    'name' => $this->oponent->getName(),
                    'hp' => $this->oponent->getHealthPoint(),
                    'mp' => $this->oponent->getManaPoint(),
                    'status' => $this->oponent->getStatus(),
                    'cardsInBattle' => $this->oponent->getCardsInBattle(),
                    'cardsInHands' => $this->oponent->getCardsInHands()
                ]
            ];
        }else{
            $mass += [
                'owner' => [
                    'name' => $this->owner->getName(),
                    'hp' => $this->owner->getHealthPoint(),
                    'mp' => $this->owner->getManaPoint(),
                    'status' => $this->owner->getStatus(),
                    'cardsInBattle' => $this->owner->getCardsInBattle(),
                    'cardsInHands' => $this->owner->getCardsInHands()
                ]
            ];
        }

        return $mass;
    }

    public function attack(){
        $this->owner->attackUser($this->oponent);
    }

    public function generate(){
        $random = $this->db->q("SELECT `idCards` FROM `cardsinbox` WHERE `users`='".$this->owner->getName()."';");
        $id = 1;
        while($row = mysqli_fetch_array($random)){
            $id = $row['idCards'];
            if(rand(0, 10) == 5){
                break;
            }
        }
        $massCards = explode("|", $this->owner->getCardsInHands());
        for ($i = 0; $i < count($massCards); $i++){
            if($massCards[$i] == 'none'){
                $this->addInBattle($i, $id);
                break;
            }
        }
    }
}

?>
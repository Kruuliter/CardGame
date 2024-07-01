<?php
/*
    * Класс информации игрока для боя
    * card1|card2|card3|card4
    */

include("card.php");

class User {
    private string $nameUser; 
    private string $cardsInHave;
    private string $cardsInBattle;
    private string $status;
    private int $id;
    private int $hp;
    private int $mp;
    private db $db;

    function __construct(string $nameUser, db $db)
    {
        $this->nameUser = $nameUser;
        $this->db = new db();
        $this->getUserFromSQL($this->$nameUser);
    }

    private function getUserFromSQL(string $idC){
        $settingsCard = $this->db->q("SELECT `id`, `login`, `hp`, `mp`, `cardsInHand`, `CardsInBattle`, `status` FROM `usersinbattle` WHERE `login`='".$idC."';");

        if (mysqli_num_rows($settingsCard) == 1){
            while($row = mysqli_fetch_array($settingsCard)){
                $this->id = $row["id"];
                $this->nameUser = $row["login"];
                $this->cardsInHave = $row["hp"];
                $this->cardsInBattle = $row["mp"];
                $this->hp = $row["cardsInHand"];
                $this->mp = $row["CardsInBattle"];
                $this->status = $row["status"];
            }
        }elseif(mysqli_num_rows($settingsCard) == 0){
            $this->db->q("INSERT INTO `usersinbattle`(`login`) VALUES ('".$idC."')");
            $settingsCard = $this->db->q("SELECT `id`, `login`, `hp`, `mp`, `cardsInHand`, `CardsInBattle`, `status` FROM `usersinbattle` WHERE `login`='".$idC."';");
            while($row = mysqli_fetch_array($settingsCard)){
                $this->id = $row["id"];
                $this->nameUser = $row["login"];
                $this->cardsInHave = $row["hp"];
                $this->cardsInBattle = $row["mp"];
                $this->hp = $row["cardsInHand"];
                $this->mp = $row["CardsInBattle"];
                $this->status = $row["status"];
            }
        }
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->nameUser;
    }

    public function getStatus(){
        return $this->status;
    }

    public function setStatus(string $status){
        $this->db->q("UPDATE `usersinbattle` SET `status`='".$status."' WHERE `login`='".$this->nameUser."';");
        $this->status = $status;
    }

    public function getHealthPoint(){
        return $this->hp;
    }

    public function getManaPoint(){
        return $this->mp;
    }

    public function setHealthPoint(int $hp){
        $this->db->q("UPDATE `usersinbattle` SET `hp`=".$hp." WHERE `login`='".$this->nameUser."';");
        $this->hp = $hp;
    }

    public function setManaPoint(int $mp){
        $this->db->q("UPDATE `usersinbattle` SET `mp`=".$mp." WHERE `login`='".$this->nameUser."';");
        $this->mp = $mp;
    }

    public function getCardsInBattle(){
        return $this->cardsInBattle;
    }

    public function getCardsInHands(){
        return $this->cardsInHave;
    }

    public function getCountCard(){
        $cards = explode("|", $this->cardsInHave);
        $count = 0;
        for($i = 0; $i < count($cards); $i=$i+1){
            if($cards[$i] != 'none'){
                $count = $count + 1;
            }
        }
        return $count;
    }


    // point = 0, 1, 2, 3
    public function setCardsInBattle(int $point, string $settings){
        $cards = explode("|", $this->cardsInBattle);
        $card = new Card($settings, $this->db);
        $cards[$point] = $card->getCardSettings();
        $this->cardsInBattle = $cards[0]."|".$cards[1]."|".$cards[2]."|".$cards[3];
    }

    // point = 0, 1, 2, 3
    public function setCardsInHands(int $point, string $settings){
        $cards = explode("|", $this->cardsInHave);
        $card = new Card($settings, $this->db);
        $cards[$point] = $card->getCardSettings();
        $this->cardsInHave = $cards[0]."|".$cards[1]."|".$cards[2]."|".$cards[3];
    }

    // point = 0, 1, 2, 3
    public function getCardStatusInBattle(int $point){
        $cards = explode("|", $this->cardsInBattle);
        $card = new Card($cards[$point], $this->db);
        return $card->getCardSettings();
    }

    // point = 0, 1, 2, 3
    public function getCardStatusInHands(int $point){
        $cards = explode("|", $this->cardsInHave);
        $card = new Card($cards[$point], $this->db);
        return $card->getCardSettings();
    }

    public function attackUser(User $u){
        $cardsP1 = explode("|", $this->getCardsInBattle());
        $cardsP2 = explode("|", $u->getCardsInBattle());

        for ($i = 0; $i < 4; $i = $i + 1){
            $cardP1 = new Card($cardsP1[$i], $this->db);
            $cardP2 = new Card($cardsP2[$i], $this->db);

            if($cardP1->getCardStatus() == "battle"){
                if ($cardP2->getCardStatus() == "battle"){
                    $cardP1->attackCard($cardP2);
                }else{
                    $u->setHealthPoint(($u->getHealthPoint() - $cardP1->getAttack()));
                }
            }
            
            $u->setCardsInBattle($i, $cardP2->getCardSettings());
        }
    }
}

?>
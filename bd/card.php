<?php
/*
    * Класс информации игрока для боя
    * id=6;name=Vampi;mp=13;hp=13;atk=4;status=move(or status=attak)
    */
class Card {
    private string $name;
    private string $move;
    private string $cardImage;
    private int $hp;
    private int $mp;
    private int $atk;
    private int $idCard;
    private db $db;

    function __construct(string $cardStatus, db $db)
    {
        $this->db = new db();
        if($cardStatus == "none"){
            $this->move = $cardStatus;
        }elseif(str_contains($cardStatus, ";")){
            $mass = [];
            $pars = explode(";", $cardStatus);
            foreach($pars as &$mValue){
                if(str_contains($cardStatus, "=")){
                    list($key, $value) = explode("=", $mValue);
                    $mass += [$key => $value];
                }
            }

            $this->idCard = $mass["id"];
            $this->name = $mass["name"];
            $this->mp = $mass["mp"];
            $this->hp = $mass["hp"];
            $this->atk = $mass["atk"];
            $this->move = $mass["move"];
            $this->cardImage = $mass["cardImage"];

            
            if ($this->move == "died"){
                $this->move = "none";
            }

            if ($this->move == "move"){
                $this->move = "battle";
            }

        }elseif(str_contains($cardStatus, "=")){
            list($key, $value) = explode("=", $cardStatus);
            if ($key != "id"){
                throw new Exception('card not have id');
            }
            $this->getCardFromSQL($value);
        }elseif(is_numeric($cardStatus)){
            $this->getCardFromSQL($cardStatus);
        }else{
            throw new Exception('card need id or standart settings');
        }
    }

    private function getCardFromSQL(string $idC){
        $settingsCard = $this->db->q("SELECT `id`, `cardImage`, `name`, `hp`, `mp`, `atk` FROM `cards` WHERE `id`=".$idC.";");

        while($row = mysqli_fetch_array($settingsCard)){
            $this->idCard = $row["id"];
            $this->name = $row["name"];
            $this->mp = $row["mp"];
            $this->hp = $row["hp"];
            $this->atk = $row["atk"];
            $this->cardImage = $row["cardImage"];
            $this->move = "move";
        }
    }

    public function getCardSettings(){
        if ($this->move == "none"){
            return $this->move;
        }
        return "id=".$this->idCard.";cardImage=".$this->cardImage.";name=".$this->name.";mp=".$this->mp.";hp=".$this->hp.";atk=".$this->atk.";status=".$this->move;
    }

    public function getCardStatus(){
        return $this->move;
    }

    public function getAttack(){
        return $this->atk;
    }

    public function getHealthPoint(){
        return $this->hp;
    }

    public function setHealthPoint(int $hp){
        $this->hp = $hp;

        if ($this->hp <= 0){
            $this->move = "died";
        }
    }

    public function attackCard(Card $otherCard){
        if ($this->move == "died" || $this->move == "none" || $this->move == "move"){
            return false;
        }

        if ($otherCard->getHealthPoint() <= 0){
            return false;
        }

        $otherCard->setHealthPoint($otherCard->getHealthPoint() - $this->getAttack());
        return true;
    }
}

?>
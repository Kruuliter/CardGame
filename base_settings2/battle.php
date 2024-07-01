<?php
    include('../bd/battle.php');
    $battle = new Battle($_SESSION['user_login'], $db);
    $isFind = $battle->oponentIsFind();
    $mass = $battle->getInfo();
?>


<tr style="width: 100%; height: 100%">
    <td style="width: 100%; height: 100%">
        <div id="player_two" class="player two">
            <div id="player_status">
                <div id="user">
                    <?php
                        if(in_array("oponent", $mass)){
                            echo "<div class=\"user\" style=\"background-image: url(https://media.discordapp.net/attachments/930046306834087996/1074717621561327666/player.png);\">
                                    <div class=\"user_settings\">
                                        <div class=\"heal\">
                                            <div>".$mass['oponent']['hp']."</div>
                                        </div>
                                        <div class=\"lvl\">
                                            <div>0</div>
                                        </div>
                                        <div class=\"mp\">
                                            <div>".$mass['oponent']['mp']."</div>
                                        </div>
                                    </div>
                                    <div class=\"user_nickname\">
                                        <div>".$mass['oponent']['name']."</div>
                                    </div>
                                </div>";
                        }else{
                            echo "not find";
                        }
                    ?>
                </div>
            </div>
            <div id="player_cards" class="flex-battle cards">
                <?php
                    if(in_array("oponent", $mass)){
                        $str = "";
                        $cards = explode("|", $mass['oponent']['cardsInHands']);
                        for ($i = 0; $i < count($cards); $i = $i + 1){
                            if($cards[$i] != 'none'){
                                $settingsCard = [];
                                $pars = explode(";", $cards[$i]);
                                foreach($pars as &$mValue){
                                    if(str_contains($cards[$i], "=")){
                                        list($key, $value) = explode("=", $mValue);
                                        $settingsCard += [$key => $value];
                                    }
                                }

                                $str += "<div id=\"frame_background\">
                                            <div id=\"image_card\" style=\"background-image: url(".$settingsCard["cardImage"].");\">
                                                <div class=\"id\">".$settingsCard["id"]."</div>
                                                <div class=\"move\">".$settingsCard["move"]."</div>
                                                <div id=\"card_frame\">
                                                    <div class=\"settings_up\">
                                                        <div class=\"mana\">
                                                            <div>".$settingsCard["mp"]."</div>
                                                        </div>
                                                    </div>
                                                    <div class=\"settings_center\">
                                                        <div class=\"name_card\">
                                                        ".$settingsCard["name"]."
                                                        </div>
                                                    </div>
                                                    <div class=\"settings_down\">
                                                        <div class=\"attack\">
                                                            <div>".$settingsCard["atk"]."</div>
                                                        </div>
                                                        <div class=\"hp\">
                                                            <div>".$settingsCard["hp"]."</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>";
                            }
                        }
                        echo $str;
                    }else{
                        echo "not find";
                    }
                ?>
            </div>
        </div>
    </td>
</tr>
<tr style="width: 100%; height: 100%">
    <td style="width: 100%; height: 100%">
        <div id="battle">
            <div id="history">
                <div id="clock">
                    <div><span class="round">0</span> раунд</div>
                    <div>Осталось <span class="seconds">0</span> секунд</div>
                </div>
                <div id="changes">
                    ходит <span class="changePlayers">User1</span>
                </div>
                <div>
                    <input id="logout" type="button" value="Сдаться">
                    <input id="next" type="button" value="Конец хода">
                </div>
            </div>
            <div id="battle_two_players">
                <div id="player" class="flex-battle two">
                    
                    <div id="frame_background">
                        <div id="image_card_none">
                        </div>
                    </div>
                    
                    <div id="frame_background">
                        <div id="image_card_none">
                        </div>
                    </div>
                    
                    <div id="frame_background">
                        <div id="image_card_none">
                        </div>
                    </div>
                    
                    <div id="frame_background">
                        <div id="image_card_none">
                        </div>
                    </div>
                    
                </div>
                <div id="player" class="flex-battle one">
                    
                    <div id="frame_background">
                        <div id="image_card_none">
                        </div>
                    </div>
                    
                    <div id="frame_background">
                        <div id="image_card_none">
                        </div>
                    </div>
                    
                    <div id="frame_background">
                        <div id="image_card_none">
                        </div>
                    </div>
                    
                    <div id="frame_background">
                        <div id="image_card_none">
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </td>
</tr>

<tr style="width: 100%; height: 100%">
    <td style="width: 100%; height: 100%">
        <div id="player_one" class="player one">
            <div id="player_status">
                <div id="user">
                    <?php
                        echo "<div class=\"user\" style=\"background-image: url(https://media.discordapp.net/attachments/930046306834087996/1074717621561327666/player.png);\">
                                <div class=\"user_settings\">
                                    <div class=\"heal\">
                                        <div>".$mass['owner']['hp']."</div>
                                    </div>
                                    <div class=\"lvl\">
                                        <div>0</div>
                                    </div>
                                    <div class=\"mp\">
                                        <div>".$mass['owner']['mp']."</div>
                                    </div>
                                </div>
                                <div class=\"user_nickname\">
                                    <div>".$_SESSION['user_login']."</div>
                                </div>
                            </div>";
                    ?>
                </div>
            </div>
            <div id="player_cards" class="flex-battle cards">

                <?php

                    $str = "";
                    $cards = explode("|", $mass['owner']['cardsInHands']);
                    for ($i = 0; $i < count($cards); $i = $i + 1){
                        if($cards[$i] != 'none'){
                            $settingsCard = [];
                            $pars = explode(";", $cards[$i]);
                            foreach($pars as &$mValue){
                                if(str_contains($cards[$i], "=")){
                                    list($key, $value) = explode("=", $mValue);
                                    $settingsCard += [$key => $value];
                                }
                            }

                            $str += "<div id=\"frame_background\">
                                        <div id=\"image_card\" style=\"background-image: url(".$settingsCard["cardImage"].");\">
                                            <div class=\"id\">".$settingsCard["id"]."</div>
                                            <div class=\"move\">".$settingsCard["move"]."</div>
                                            <div id=\"card_frame\">
                                                <div class=\"settings_up\">
                                                    <div class=\"mana\">
                                                        <div>".$settingsCard["mp"]."</div>
                                                    </div>
                                                </div>
                                                <div class=\"settings_center\">
                                                    <div class=\"name_card\">
                                                    ".$settingsCard["name"]."
                                                    </div>
                                                </div>
                                                <div class=\"settings_down\">
                                                    <div class=\"attack\">
                                                        <div>".$settingsCard["atk"]."</div>
                                                    </div>
                                                    <div class=\"hp\">
                                                        <div>".$settingsCard["hp"]."</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>";
                        }
                    }
                    echo $str;
                ?>
                    

                    
                    
            </div>
        </div>
        
    </td>
</tr>

<script type="module" language="JavaScript" src="javascript/boevka.js"></script>
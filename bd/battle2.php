<?php
/*
    * Класс боя
    *
    */

class battle {

    private $W_krit = array(); # Список слов для критического удара
    private $W_att = array(); # Список слов для простого удара
    private $W_miss = array(); # Список слов для промаха
       
    # Инициализируется бой
    # $p1:Array - характеристики нападающего (Н)
    # $p2:Array - характеристики защищающегося (З)
    # $db:Class - класс БД
    public function rush($p1,$p2,$db) {
     $db->q("INSERT INTO `battle` (`p1`,`p2`,`time`) VALUES ('".$p1['id']."','".$p2['id']."','".date('Y-d H:i:s')."')"); # Созадется запись боя
     $battle_id = 1; # Запоминается id боя
     $map1 = explode("|",$p1['position']); # Список карт Н
     $map2 = explode("|",$p2['position']); #  Список карт З
     # Создание списка слов для лога
     $sql_word = $db->q("SELECT * FROM `word`");
     while($word = mysqli_fetch_array($sql_word)) {
      if ($word['type'] == 1) {
       $this->W_att[] = $word['desc'];
      } elseif ($word['type'] == 2) {
       $this->W_miss[] = $word['desc'];
      } elseif ($word['type'] == 3) {
       $this->W_krit[] = $word['desc'];
      }
     }
        
     $j=0; # Текущая карта Н
     $k=0; # Текущая карта З
     $_map1 = array(); # Характеристики карт Н
     $_map2 = array(); # Характеристики карт З
     $ids1 = array(); # Список карт Н
     $ids2 = array(); # Список карт З
     $log1 = array(); # Список характеристик карт Н для вывода в лог
     $log2 = array(); # Список характеристик карт З для вывода в лог
     # Запоминается расположение карт игроков
     for ($i=0;$i<5;$i++) {
      $n_map1 = $map1[$j];
      if ($n_map1 == 0) { # Если в текцщем слоте Н нет карты, то записывается первая карта
       $n_map1 = $map1[0];
       $j=0;
      }
      $n_map2 = $map2[$k];
      if ($n_map2 == 0) { # Если в текцщем слоте З нет карты, то записывается первая карта
       $n_map2 = $map2[0];
       $k=0;
      }
      # Получение характеристик карты. Если характеристики карты были уже получены, то считывается с переменной
      if (!isset($ids1[$n_map1])) {
       $sql = $db->q("SELECT `user_map`.`att`,`user_map`.`abi`,`user_map`.`skill`,`user_map`.`lvl`,`user_map`.`exp`,`user_map`.`id`,`map`.`name`,`map`.`lvl_exp`,`map`.`c har` FROM `user_map`,`map` WHERE `user_map`.`map_id`=`map`.`id` AND `user_map`.`id`='".$n_map1."'");
       $_map1[] = mysqli_fetch_array($sql);
      } else {
       $_map1[] = $_map1[$j];
      }
      # Получение характеристик карты. Если характеристики карты были уже получены, то считывается с переменной
      if (!isset($ids2[$n_map2])) {
       $sql = $db->q("SELECT `user_map`.`att`,`user_map`.`abi`,`user_map`.`skill`,`user_map`.`lvl`,`user_map`.`exp`,`user_map`.`id`,`map`.`name`,`map`.`lvl_exp`,`map`.`c har` FROM `user_map`,`map` WHERE `user_map`.`map_id`=`map`.`id` AND `user_map`.`id`='".$n_map2."'");
       $_map2[] = mysqli_fetch_array($sql);
      } else {
       $_map2[] = $_map2[$k];
      }
      # Запись характеристик карт для лога
      $log1[] = array(
       'name'=>$_map1[$j]['name'],
       'att'=>$_map1[$j]['att'],
       'abi'=>$_map1[$j]['abi'],
       'skill'=>$_map1[$j]['skill'],
       'lvl'=>$_map1[$j]['lvl']
      );
      $log2[] = array(
       'name'=>$_map2[$k]['name'],
       'att'=>$_map2[$k]['att'],
       'abi'=>$_map2[$k]['abi'],
       'skill'=>$_map2[$k]['skill'],
       'lvl'=>$_map2[$k]['lvl']
      );
      # Записывает характеристики карты, что бы не проверять каждый раз одну и ту же карту в БД, будут браться характеристики с этой переменной
      $ids1[$n_map1] = $_map1[$j];
      $ids2[$n_map2] = $_map2[$k];
         
      $j++;
      $k++;
     }
        
     $life1 = $p1['life']; # Жизненная энергия Н
     $life2 = $p2['life']; # Жизненная энергия З
     $data = array(); # Выигрышь победителя
     $all_damage1 = 0; # Всего урона Н
     $all_damage2 = 0; # Всего урона З
     $win = -1; # Определяет кто победитель
     # Запускается бой на 5 ходов
     for ($i=0;$i<5;$i++) {
      if (!isset($ids1[$_map1[$i]['id']]['k'])) $ids1[$_map1[$i]['id']]['k'] = 0; # Количество урона, который нанесла карта Н за все ходы
      if (!isset($ids2[$_map2[$i]['id']]['k'])) $ids2[$_map2[$i]['id']]['k'] = 0; # Количество урона, который нанесла карта З за все ходы
         
      $damage1 = $this->damage($_map1[$i],$_map2[$i]); # Урон в текущем ходе Н
      $damage2 = $this->damage($_map2[$i],$_map1[$i]); # Урон в текущем ходе З
      # Создается запись в лог
      $db->q("INSERT INTO `battle_log` (`battle_id`,`desc`) VALUES ('".$battle_id."','".$damage1['txt']." - ".$damage2['txt']."')");
         
      $life1 -= $damage2['damage']; # Уменьшается жизненная энергия Н
      $life2 -= $damage1['damage']; # Уменьшается жизненная энергия З
         
      $ids1[$_map1[$i]['id']]['k'] += $damage1['damage']; # Увеличивается количество урона, нанесенного картой Н
      $ids2[$_map2[$i]['id']]['k'] += $damage2['damage']; # Увеличивается количество урона, нанесенного картой З
         
      $all_damage1 += $damage1['damage']; # Увеличивается количество общего урона Н
      $all_damage2 += $damage2['damage']; # Увеличивается количество общего урона З
      # Если жизненная энергия обои игроков закончилась или какого то одного из них, а ходы еще остались, то записывается победитель и его приз
      if ($life1 <= 0 && $life2 <= 0) {
       $win = 0;
       $desc = '';
       break;
      } elseif ($life1 <= 0) {
       $win = $p2['id'];
       $data['money'] = mt_rand(0,$all_damage2);
       $desc = json_encode($data);
       break;
      } elseif ($life2 <= 0) {
       $win = $p1['id'];
       $data['money'] = mt_rand(0,$all_damage1);
       $desc = json_encode($data);
       break;
      }
     }
     # Если прошло 5 ходов, а жизненная энергия игроков еще не закончилась, то в зависимости от нанесенного урона выбирается победитель и получает свой приз
     if ($win == -1) {
      if ($all_damage1 == $all_damage2) {
       $win = 0;
       $desc = '';
      } elseif ($all_damage1 < $all_damage2) {
       $win = $p2['id'];
       $data['money'] = mt_rand(0,$all_damage2);
       $desc = json_encode($data);
      } elseif ($all_damage2 < $all_damage1) {
       $win = $p1['id'];
       $data['money'] = mt_rand(0,$all_damage1);
       $desc = json_encode($data);
      }
     }
     # Запись карт игроков в лог, так же их фильтрация
     $_p1_map = json_encode($log1);
     $_p2_map = json_encode($log2);
     $_p1_map = str_replace("\\","\\\\",$_p1_map);
     $_p1_map = str_replace("'","\\'",$_p1_map);
     $_p2_map = str_replace("\\","\\\\",$_p2_map);
     $_p2_map = str_replace("'","\\'",$_p2_map);
     $db->q("UPDATE `battle` SET `win`='".$win."', `desc`='".$desc."',`p1_map`='".$_p1_map."',`p2_map`='".$_p2_map."' WHERE `id`='".$battle_id."'");
     if ($win != 0) $db->q("UPDATE `user` SET `money`=`money`+'".$data['money']."' WHERE `id`='".$win."'"); # Если есть победитель, увеличивается его количество монет
        
     $char_list = array('att','abi','skill'); # Список характеристик для карт
     # Список карт Н
     foreach($ids1 as $id => $val) {
      $exp = $val['exp']+$val['k']; # Расчитывается количество опыта для карты
      $lvl = $val['lvl']; # Уровень карты
      $char = array($val['att'],$val['abi'],$val['skill']); # Характеристики карты
      $plvl = $val['lvl']-1; # Позиция уровня
      $extra_sql = '';
         
      if ($lvl == 5) continue; # Если уровень карты 5 (максимальный), то переходим к следующей
         
      $_exp = explode("|",$val['lvl_exp']); # Количество опыта для поднятия уровня
      $_char = explode("|",$val['char']); # Процент увеличения для каждой характеристики отдельно для всех уровней
      if ($_exp[$plvl] <= $exp) { # Если текущий опыт больше опыта для текущего уровня, то увеличивается уровень
       $lvl++;
       $exp -= $_exp[$plvl]; # Указывается реальный опыт на текущем уровне
          
       $__char = explode(":",$_char[$plvl]); # Процент увеличения для каждой характеристики отдельно для текущего уровня карты
       # Случайным образом увеличивается характеристика, либо не увеличивается, как повезет <img src="http://s12.ucoz.net/sm/1/smile.gif" border="0" align="absmiddle" alt="smile" />
       for ($i=0;$i<3;$i++) {
        $rnd = mt_rand(1,100);
        if ($rnd >= $__char[$i]) {
         $extra_sql = ",`".$char_list[$i]."`=`".$char_list[$i]."`+'".mt_rand(1,5)."'";
         break;
        }
       }
      }
      $db->q("UPDATE `user_map` SET `exp`='".$exp."', `lvl`='".$lvl."' ".$extra_sql." WHERE `id`='".$id."'"); # Обновление информации о карте
     }
     # Отправляется уведомление о бое
     $db->q("INSERT INTO `post` (`p1`,`p2`,`read`,`theme`,`text`,`time`) VALUES    
                     ('".$p1['id']."','0','1','Вы участвовали в сражении','<a href=\'?a=battle&id=".$battle_id."\'>Просмотреть лог боя</a>','".date('Y-m-d H:i:s')."'),
                     ('".$p2['id']."','0','1','Вы участвовали в сражении','<a href=\'?a=battle&id=".$battle_id."\'>Просмотреть лог боя</a>','".date('Y-m-d H:i:s')."')");
     # Список карт З
     foreach($ids2 as $id => $val) {
      $exp = $val['exp']+$val['k']; # Расчитывается количество опыта для карты
      $lvl = $val['lvl']; # Уровень карты
      $char = array($val['att'],$val['abi'],$val['skill']); # Характеристики карты
      $plvl = $val['lvl']-1; # Позиция уровня
      $extra_sql = '';
         
      if ($lvl == 5) continue; # Если уровень карты 5 (максимальный), то переходим к следующей
         
      $_exp = explode("|",$val['lvl_exp']); # Количество опыта для поднятия уровня
      $_char = explode("|",$val['char']); # Процент увеличения для каждой характеристики отдельно для всех уровней
      if ($_exp[$plvl] <= $exp) { # Если текущий опыт больше опыта для текущего уровня, то увеличивается уровень
       $lvl++;
       $exp -= $_exp[$plvl]; # Указывается реальный опыт на текущем уровне
          
       $__char = explode(":",$_char[$plvl]); # Процент увеличения для каждой характеристики отдельно для текущего уровня карты
       # Случайным образом увеличивается характеристика, либо не увеличивается, как повезет <img src="http://s12.ucoz.net/sm/1/smile.gif" border="0" align="absmiddle" alt="smile" />
       for ($i=0;$i<3;$i++) {
        $rnd = mt_rand(1,100);
        if ($rnd >= $__char[$i]) {
         $extra_sql = ",`".$char_list[$i]."`=`".$char_list[$i]."`+'".mt_rand(1,5)."'";
         break;
        }
       }
      }
      $db->q("UPDATE `user_map` SET `exp`='".$exp."', `lvl`='".$lvl."' ".$extra_sql." WHERE `id`='".$id."'"); # Обновление информации о карте
     }
     # Переадресация на лог боя
     go2page("game.php?a=battle&id=".$battle_id);
    }
    # Расчет урона
    # $m1:Array - характеристики атакующей карты
    # $m2:Array - характеристики защищающейся карты
    private function damage($m1,$m2) {
     $ret = array('damage'=>0,'txt'=>''); # Данные, которые будут возвращаться текущей функцией
        
     $is_miss = round($m1['att']/(mt_rand($m1['att'],($m1['att']+($m2['abi']*2))))); # Расчитывает возможность промаха
     if ($is_miss == 1) { # Если промаха нет, то расчитывает урон
      $is_krit = round(round(mt_rand(0,10)/10)*$m1['skill']/($m1['skill']+$m2['abi'])); # Расчитывает критический удар
      if ($is_krit == 1) { # Если удар критический, то увеличивается урон
       $m1['att'] = round(($m1['att']*150)/100);
      }
      $_damage = round($m1['att']*(mt_rand(75,150)/100)); # Общий урон
       
      $_block = round($m2['abi']*(mt_rand(5,10)/10)); # Защита защищающегося
         
      $ret['damage'] = $_damage-$_block; # Окончательный урон
     }
        
     if ($ret['damage']<0) $ret['damage'] = 0; # Что бы не было минусового урона
     # Указывает какие слова для лога использовать
     if ($is_krit == 0) {
      $word = $this->W_att;
     } elseif ($is_krit == 1) {
      $word = $this->W_krit;
     }
     if ($ret['damage'] == 0) {
      $word = $this->W_miss;
     }
     # Получает случайную фразу из списка фраз
     $sizeof = sizeof($word)-1;
     $arr_word_rand = mt_rand(0,$sizeof);
     $ret['txt'] = $word[$arr_word_rand];
     if ($ret['txt'] == "") {
      $ret['txt'] = $word[0];
     }
     # Преобразовывает зарезервированые слова в нужные
     $ret['txt'] = str_replace('{name1}','<b>'.$m1['name'].'</b>',$ret['txt']);
     $ret['txt'] = str_replace('{name2}','<b>'.$m2['name'].'</b>',$ret['txt']);
     $ret['txt'] = str_replace('{damage}','<b>'.$ret['damage'].'</b>',$ret['txt']);
     # Возвращает данные
     return $ret;
    }
}

?>
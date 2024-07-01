<?php
$User = mysqli_fetch_array($db->q("SELECT * FROM `user` WHERE `id`='".$_SESSION['user_id']."'"));

?>
<div style='text-align: center;'>
  <a href="?a=trade">Купить</a> | <a href="?a=trade&type=1">Продать</a>
</div>
<?php
if (empty($_GET['type'])) { # Просмотр аукциона
  if (!empty($_POST['buy'])) { # Покупка карты
   # Фильтрация
   $id = preg_replace("/[^0-9]/","",$_POST['id']);
   if ($id == '') go2page("game.php");
   # Проверка аукциона, покупка это или нет
   $sql = $db->q("SELECT * FROM `trade` WHERE `id`='".$id."' AND (`type`='1' OR `type`='3') AND `p1`<>'".$_SESSION['user_id']."'");
   if (mysqli_num_rows($sql) == 1) { # Проверка прошла успешно
    $trade = mysqli_fetch_array($sql);
    if ($trade['cost'] <= $User['money']) { # Если хватает денег, то отнимаются деньги у покупателя и прибавляются продавцу. Создается уведомление и завершается аукцион
     $item = mysqli_fetch_array($db->q("SELECT `map`.`name`,`user_map`.`id` FROM `map`,`user_map` WHERE `user_map`.`id`='".$trade['map_id']."' AND `map`.`id`=`user_map`.`map_id`"));
     $db->q("UPDATE `user` SET `money`=CASE `id` WHEN '".$trade['p2']."' THEN `money`-'".$trade['cost']."' WHEN '".$trade['p1']."' THEN `money`+'".$trade['cost']."' ELSE `money` END");
     $db->q("UPDATE `user_map` SET `user_id`='".$trade['p2']."' WHERE `id`='".$trade['map_id']."'");
     $db->q("INSERT INTO `post` (`p1`,`p2`,`read`,`theme`,`text`,`time`) VALUES  
                              ('".$trade['p1']."','0','1','Аукцион','Ваш лот <b>".$item['name']."</b> выкуплен за ".$trade['cost']." монет','".date('Y-d H:i:s')."'),
                              ('".$trade['p2']."','0','1','Аукцион','Вы выкупили лот <b>".$item['name']."</b> за ".$trade['cost']." монет','".date('Y-m-d H:i:s')."')");
     $db->q("UPDATE `trade` SET `type`='4' WHERE `id`='".$id."'");
     $User['money'] -= $trade['cost'];
    }
   }
  }
  if (!empty($_POST['bet'])) { # Повышение ставки для лота
   # Фильтрация
   $id = preg_replace("/[^0-9]/","",$_POST['id']);
   $num = preg_replace("/[^0-9]/","",$_POST['num']);
   if ($id == '') go2page("game.php");
   if ($num == '') go2page("game.php");
   # Проверка аукциона, аукцион это или нет
   $sql = $db->q("SELECT * FROM `trade` WHERE `id`='".$id."' AND (`type`='2' OR `type`='3') AND `p1`<>'".$_SESSION['user_id']."'");
   if (mysqli_num_rows($sql) == 1) { # Проверка прошла успешно
    $trade = mysqli_fetch_array($sql);
    if ($num >= ($trade['bet']+10) && $num <= $User['money']) { # Если хватает денег, то отнимаются деньги у покупателя. Если уже кто то поставил ставку, то ему возвращаются его деньги.
     if ($trade['p2'] != 0) $db->q("UPDATE `user` SET `money`=CASE `id` WHEN '".$trade['p2']."' THEN `money`+'".$trade['bet']."' WHEN '".$_SESSION['user_id']."' THEN `money`-'".$num."' ELSE `money` END");
     else $db->q("UPDATE `user` SET `money`=`money`-'".$num."' WHERE `id`='".$_SESSION['user_id']."'");
     $db->q("UPDATE `trade` SET `bet`='".$num."', `p2`='".$_SESSION['user_id']."' WHERE `id`='".$id."'");
     $User['money'] -= $trade['bet'];
    }
   }
  }
  # Проверка на окончание аукционов
  $end_trade = $db->q("SELECT * FROM `trade` WHERE `type`<'4' AND `time`<='".date("Y-m-d H:i:s")."'");
  $end_map_id = '';
  $end_trade_id = '';
  while($ret = mysqli_fetch_array($end_trade)) {
   if ($ret['p2'] == 0) { # Если уже кто то делал ставку, то ему дается эта карта
    $end_map_id .= "`id`='".$ret['map_id']."' OR";
    $end_trade_id .= "`id`='".$ret['id']."' OR";
   } else { # Иначе возвращается продавцу
    $db->q("UPDATE `user_map` SET `type`='1', `user_id`='".$ret['p2']."' WHERE `id`='".$ret['map_id']."'");
    $db->q("UPDATE `user` SET `money`=`money`+'".$ret['bet']."' WHERE `id`='".$ret['p1']."'");
    $end_trade_id .= "`id`='".$ret['id']."' OR";
   }
  }
  if ($end_map_id != '') {
   $end_map_id = substr($end_map_id,0,-3);
   $db->q("UPDATE `user_map` SET `type`='1' WHERE ".$end_map_id);
  }
  if ($end_trade_id != '') {
   $end_trade_id = substr($end_trade_id,0,-3);
   $db->q("UPDATE `trade` SET `type`='4' WHERE ".$end_trade_id);
  }
   
  echo '<table style="width:600px;" border="0"><tr><td style="width:120px;" align="center">Карта</td><td style="width:120px;" align="center">Продавец</td><td style="width:90px;" align="center">Цена</td><td style="width:90px;" align="center">Ставка</td><td style="width:180px;" align="center"></td></tr>';
  # Листалка
  $perpage = 10;
  if (empty($_GET['start'])) {
   $start = 0;
   $gstart = 1;
  } else {
   $gstart = preg_replace("/[^0-9]/","",$_GET['start']);
   $start = (int)($gstart - 1) * $perpage;
  }
  $pages = ceil(mysqli_num_rows($db->q("SELECT * FROM `trade` WHERE `time`>='".date('Y-m-d H:i:s')."' AND `type`<'4'")) / $perpage);
   
  if ($start > $pages) $start = (int)($pages - 1) * $perpage;
  # Собственно вывод лотов
  $names = array();
  $sql = $db->q("SELECT `trade`.*,`user_map`.*,`map`.`name`,`trade`.`id` as `tid`,`trade`.`type` as `ttype` FROM `trade`,`user_map`,`map` WHERE `trade`.`map_id`=`user_map`.`id` AND `user_map`.`map_id`=`map`.`id` AND `trade`.`time`>='".date('Y-m-d H:i:s')."' AND `trade`.`type`<'4' ORDER BY `trade`.`time` DESC LIMIT ".$start.",".$perpage);
  while($row = mysqli_fetch_array($sql)) {
   $name['login'] = '';
   $name2['login'] = '';
   if ($names[$row['p1']]) {
    $name['login'] = $names[$row['p1']];
   } else {
    $name = mysqli_fetch_array($db->q("SELECT `login` FROM `user` WHERE `id`='".$row['p1']."'"));
    $names[$row['p1']] = $name['login'];
   }
   if ($names[$row['p2']]) {
    $name2['login'] = $names[$row['p2']];
   } else {
    if ($row['p2'] > 0) {
     $name2 = mysqli_fetch_array($db->q("SELECT `login` FROM `user` WHERE `id`='".$row['p2']."'"));
     $names[$row['p2']] = $name2['login'];
    }
   }
   echo "<tr><td>
     <table style='width:110px;display:block;border:1px solid black;'>
     <tr>
      <td style='text-align:center;width:140px;' colspan='2'><b>".$row['name']."</b></td>
     </tr>
     <tr>
      <td>Уровень</td>
      <td>".$row['lvl']."</td>
     </tr>
     <tr>
      <td>Атака</td>
      <td>".$row['att']."</td>
     </tr>
     <tr>
      <td>Ловкость</td>
      <td>".$row['abi']."</td>
     </tr>
     <tr>
      <td>Мастерство</td>
      <td>".$row['skill']."</td>
     </tr>";
   # Сколько времени осталось до окончания лота
   $time = explode(' ',$row['time']);
   $time[0] = explode('-',$time[0]);
   $time[1] = explode(':',$time[1]);
    
   $time2 = explode(' ',date('Y-m-d H:i:s'));
   $time2[0] = explode('-',$time2[0]);
   $time2[1] = explode(':',$time2[1]);
   $time3 = array();
    
   $time3[1][2] = $time[1][2] - $time2[1][2];
   if ($time3[1][2] < 0) {
    $time3[1][2] = 60 + $time3[1][2];
    $time[1][1]--;
   }
   $time3[1][1] = $time[1][1] - $time2[1][1];
   if ($time3[1][1] < 0) {
    $time3[1][1] = 60 + $time3[1][1];
    $time[1][0]--;
   }
   $time3[1][0] = $time[1][0] - $time2[1][0];
   if ($time3[1][0] < 0) {
    $time3[1][0] = 24 + $time3[1][0];
    $time[0][2]--;
   }
   $time3[0][2] = $time[0][2] - $time2[0][2];
   if ($time3[0][2] < 0) {
    $time3[0][2] = date('t') + $time3[0][2];
   }
    
   $end_time = $time3[0][2].'д '.$time3[1][0].'ч '.$time3[1][1].'м '.$time3[1][2].'с';
   echo "</table>
    </td><td align='center'>".$name['login']."<br>".$end_time."</td>";
   if ($row['ttype'] == 1 || $row['ttype'] == 3) echo "<td align='center'>".$row['cost']."</td>";
   else echo "<td align='center'>---</td>";
   if ($row['ttype'] == 2 || $row['ttype'] == 3) {
    echo "<td align='center'>".$name2['login']."<br>".$row['bet']."</td>";
   }
   else echo "<td align='center'>---</td>";
    
   echo "<td align='center'>";
   # Отображать кнопки только для чужих лотов
   if ($row['p1'] != $_SESSION['user_id']) {
    if ($row['ttype'] == 2 || $row['ttype'] == 3) {
     if ($User['money'] >= ($row['bet']+10)) {
      echo "<form method='post'>
       <input type='hidden' value='".$row['tid']."' name='id' style='width:50px;'>
       <input type='text' value='".($row['bet'] + 10)."' name='num' style='width:50px;'><br>
       <input type='submit' value='Сделать ставку' name='bet'>
      </form>";
     }
    }
    if ($row['ttype'] == 1 || $row['ttype'] == 3) {
     if ($User['money'] >= $row['cost']) {
      echo "<form method='post'>
       <input type='hidden' value='".$row['tid']."' name='id' style='width:50px;'>
       <input type='submit' value='Выкупить' name='buy'>
      </form>";
     }
    }
   }
    
   echo "</td></tr>";
  }
  if ($pages > 1) {
   $show_page = '';
   $separate = ' ';
   $style = 'style="color: #000000; text-decoration: none;"';
   for ($i=1;$i<=$pages;$i++) {
    $show_page .= '<a href="?a=trade&start='.$i.'" '.$style.'>'.($i==$gstart ? '<b>[' : '').$i.($i==$gstart ? ']</b>' : '').'</a>'.$separate;
   }
   echo "<tr><td colspan='4' align='center'>".$show_page."</td></tr>";
  }
  echo "</table>";
} elseif ($_GET['type'] == 1) { # Просмотр карт, которые можно выставить на аукцион
  if (!empty($_POST['go'])) { # Выставить лот на ауцион
   # Фильтрация
   $id = preg_replace("/[^0-9]/","",$_POST['map_id']);
   $cost = preg_replace("/[^0-9]/","",$_POST['cost']);
   $bet = preg_replace("/[^0-9]/","",$_POST['bet']);
   $d = preg_replace("/[^0-9]/","",$_POST['day']);
   $h = preg_replace("/[^0-9]/","",$_POST['hour']);
   $m = preg_replace("/[^0-9]/","",$_POST['min']);
    
   if ($id == '') go2page("game.php");
   # Вывод всех карт, которые не выставлены на аукцион и не принимают участие в бое
   $sql = $db->q("SELECT * FROM `user_map` WHERE `id`='".$id."' AND `type`='1' AND `user_id`='".$_SESSION['user_id']."'");
   if (mysqli_num_rows($sql) == 1) {
    $extra_sql = '';
    if ($d == '') $d = 1; if ($h == '') $h = 1; if ($m == '') $m = 1;
    if ($d > 10) $d = 10; if ($h > 24) $h = 24; if ($m > 60) $m = 60;
    if ($d == 10 && ($h > 0 || $m > 0)) {
     $h = 0;
     $m = 0;
    }
    if ($cost > 0 && $bet > 0) $type = 3;
    elseif ($bet > 0) $type = 2;
    elseif ($cost > 0) $type = 1;
    $t = ($m+($h*60)+($d*24*60))*60;
    $time = date("Y-m-d H:i:s",($t+time()));
    $db->q("INSERT INTO `trade` (`p1`,`p2`,`map_id`,`cost`,`bet`,`type`,`time`) VALUES ('".$_SESSION['user_id']."','0','".$id."','".$cost."','".$bet."','".$type."','".$time."')");
    $db->q("UPDATE `user_map` SET `type`='2' WHERE `id`='".$id."'");
    echo "<div style='text-align: center;color: green;'>Лот выставлен</div>";
   }
  }
  # Информация о картах
  $sql = $db->q("SELECT *,`user_map`.`id` as `uid`,`user_map`.`att` as `uatt`,`user_map`.`abi` as `uabi`,`user_map`.`skill` as `uskill` FROM `user_map`,`map` WHERE `user_map`.`user_id`='".$_SESSION['user_id']."' AND `map`.`id`=`user_map`.`map_id` AND `user_map`.`type`='1'");
  echo '<table style="width:600px;" border="0"><tr><td style="width:120px;" align="center">Карта</td><td style="width:60px;" align="center">Цена</td><td style="width:60px;" align="center">Ставка</td><td style="width:230px;" align="center">Время</td><td style="width:120px;" align="center"></td></tr>';
  while($row = mysqli_fetch_array($sql)) {
   $exp = explode("|",$row['lvl_exp']);
   echo "<tr><td>
     <table style='width:140px;display:block;border:1px solid black;'>
     <tr>
      <td style='text-align:center;width:140px;' colspan='2'><b>".$row['name']."</b></td>
     </tr>
     <tr>
      <td>Уровень</td>
      <td>".$row['lvl']."</td>
     </tr>
     <tr>
      <td>Опыт</td>
      <td>".$row['exp']."/".$exp[$row['lvl']-1]."</td>
     </tr>
     <tr>
      <td>Атака</td>
      <td>".$row['uatt']."</td>
     </tr>
     <tr>
      <td>Ловкость</td>
      <td>".$row['uabi']."</td>
     </tr>
     <tr>
      <td>Мастерство</td>
      <td>".$row['uskill']."</td>
     </tr>
     </table>
    </td>";
   echo "<form method='post'><td align='center'><input type='text' name='cost' value='".$row['cost']."' style='width:50px;'></td><td align='center'><input type='text' name='bet' value='0' style='width:50px;'></td><td align='center'><input type='text' name='day' value='1' style='width:50px;'>д <input type='text' name='hour' value='0' style='width:50px;'>ч <input type='text' name='min' value='0' style='width:50px;'>м</td><td align='center'><input type='hidden' value='".$row['uid']."' name='map_id'><input type='submit' value='Выставить' name='go'></td></form>";
   echo "</tr>";
  }
  echo '</table>';
}
?>
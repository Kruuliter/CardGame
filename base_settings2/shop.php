<?php
$User = mysqli_fetch_array($db->q("SELECT * FROM `users` WHERE `login`='".$_SESSION['user_login']."'"));

if (!empty($_POST['buy'])) { # Покупка
        if (!empty($_POST['map_id'])) { # Покупка карты
         # Фильтрация данных
         $id = preg_replace("/[^0-9]/","",$_POST['map_id']);
         if ($id == '') go2page("game.php");
         # Проверка на существование карты
         $map = $db->q("SELECT * FROM `cards` WHERE `id`='".$id."'");
         if (mysqli_num_rows($map) == 1) {
          $row_map = mysqli_fetch_array($map);
          if ($row_map['card_price'] <= $User['money']) { # Если хватает денег, то добавляем игроку карту, отнимаем деньги
           $db->q("INSERT INTO `users_card` (`user_login`,`id_card`,`level_card`,`count`) VALUES ('".$_SESSION['user_login']."','".$row_map['id']."','1','1')");
           $db->q("UPDATE `users` SET `money`=`money`-'".$row_map['card_price']."' WHERE `login`='".$_SESSION['user_login']."'");
           $User['money'] -= $row_map['card_price']; # Изменяем количество монет у игрока, т.к. количество монет мы получили раньше (строка 2), затем изменили их (строка 15), но данные полученные раньше не изменились, поэтому изменяются в данной строке
           echo "<div style='text-align: center;color: green;'>Спасибо за покупку</div>";
          } else {
           echo "<div style='text-align: center;color: red;'>Не хватает денег</div>";
          }
         }
        } elseif (!empty($_POST['item_id'])) { # Покупка "таблетки"
         # Фильтрация данных
         $id = preg_replace("/[^0-9]/","",$_POST['item_id']);
         if ($id == '') go2page("game.php");
         # Проверка на существование "таблетки"
         $map = $db->q("SELECT * FROM `shop` WHERE `id`='".$id."'");
         if (mysqli_num_rows($map) == 1) {
          $row_item = mysqli_fetch_array($map);
          if ($row_item['card_price'] <= $User['money']) { # Если хватает денег, то увеличиваем количество жизненной энергии и отнимаем деньги
                  
           if ($row_item['type'] == 1) {
            $action = json_decode($row_item['action']);
            $extra_sql = '';
            if (!empty($action->life)) {
             $extra_sql = ", `life`=`life`+'".$action->life."', `mlife`=`mlife`+'".$action->life."'";
            }
                   
            $db->q("UPDATE `user` SET `money`=`money`-'".$row_item['card_price']."' ".$extra_sql." WHERE `id`='".$_SESSION['user_login']."'");
           }
           $User['money'] -= $row_item['card_price'];
           echo "<div style='text-align: center;color: green;'>Спасибо за покупку</div>";
          } else {
           echo "<div style='text-align: center;color: red;'>Не хватает денег</div>";
          }
         }
        }
}
?>


  <div style='text-align: center;'>
    <a href="?a=shop">Карты</a>
  </div>
<table><tr>
<?php
$sql = $db->q("SELECT * FROM `map` ORDER BY `id` ASC");
$i=1;
while($row = mysqli_fetch_array($sql)) {
 echo "<td style='padding:4px;'>
   <table style='width:180px;display:block;border:1px solid black;'>
   <tr>
    <td style='text-align:center;width:180px;' colspan='2'><b>".$row['name']."</b></td>
   </tr>
   <tr>
    <td colspan='2'><div style='width:170px;height:120px;overflow:auto;display:block;border-bottom:1px solid black;'>".$row['desc']."</div></td>
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
   </tr>
   <tr>
    <td>Цена</td>
    <td>".$row['card_price']."</td>
   </tr>";
 if ($User['money'] >= $row['card_price']) { # Хватает денег?
  echo "<tr>
    <td style='text-align:center;width:180px;' colspan='2'>
     <form method='post'>
     <input type='hidden' value='".$row['id']."' name='map_id'>
     <input type='submit' value='Купить' name='buy'>
     </form>
    </td>
   </tr>";
 }
 echo "    </table>
  </td>";
 if (($i%5)==0) echo "</tr><tr>"; # По 5 штук в строку
 $i++;
}
?>
</tr></table>
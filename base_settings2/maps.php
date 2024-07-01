<?php
# Характеристики игрока
$User = mysqli_fetch_array($db->q("SELECT * FROM `user` WHERE `id`='".$_SESSION['user_login']."'"));

if (!empty($_POST['add'])) { # Добавление карты в список карт для боя
      # Фильтрация данных
      $id = preg_replace("/[^0-9]/","",$_POST['map_id']);
      if ($id == '') go2page("game.php");
      # Проверка на существование карты, которую игрок хочет добавить в слот для боя
      $umap = $db->q("SELECT * FROM `user_map` WHERE `user_login`='".$_SESSION['user_login']."' AND `id`='".$id."'");
      if (mysqli_num_rows($umap) == 1) {
       # Проверка на существование свободного слота
       $_ids = explode("|",$User['position']);
       $_cell = array();
       $_free_cell = false;
       foreach($_ids as $ind => $val) {
        if ($val == 0) { # Если слот свообден, то указываем что он занят новой картой
         $_ids[$ind] = $id;
         $_free_cell = true;
         break;
        } else {
         $_cell[$val] = 1;
        }
       }
       # Если слот с id новой карты не существует и есть свободный слот, то добавляем новую карту в список карт для боя
       if ($_cell[$id] != 1 && $_free_cell == true) {
        $pos = implode("|",$_ids);
        $User['position'] = $pos;
        $db->q("UPDATE `user` SET `position`='".$pos."' WHERE `id`='".$_SESSION['user_login']."'");
        $db->q("UPDATE `user_map` SET `type`='2' WHERE `id`='".$id."'"); # Указывает что карта добавлена в список карт для боя (нужно для аукциона: что бы невозможно было выставить такую карту на аукцион)
       }
      }
} else if (!empty($_POST['del'])) { # Удаление карты из списка карт для боя
      # Фильтрация данных
      $cell_id = preg_replace("/[^0-9]/","",$_POST['cell_id']);
      if ($cell_id == '') go2page("game.php");
      if ($cell_id < 0) $cell_id = 0;
      else if ($cell_id > 4) $cell_id = 4;
           
      $_ids = explode("|",$User['position']); # Список слотов
      $id = $_ids[$cell_id]; # Позиция удаляемой карты
      $_ids[$cell_id] = 0; # Указываем что слот свободен
      # После удаления карты, остальные карты должны сместиться влево, что бы не болы разрывов между ними. Иначе в бою это приведет к пропуску хода игрока.
      if ($cell_id < 4) {
       if ($_ids[$cell_id+1] != 0) {
        for ($i=$cell_id;$i<5;$i++) {
         $_ids[$i] = $_ids[$i+1];
         $_ids[$i+1] = 0;
        }
       }
      }
      # Сохранение позиций и указывает что карта удалена из списка карт для боя (нужно для аукциона: что бы МОЖНО было выставить такую карту на аукцион)
      $pos = implode("|",$_ids);
      $User['position'] = $pos;
      $db->q("UPDATE `user` SET `position`='".$pos."' WHERE `id`='".$_SESSION['user_login']."'");
      $db->q("UPDATE `user_map` SET `type`='1' WHERE `id`='".$id."'");
}

$ids = explode("|",$User['position']);
$ids2 = array();
$cell = array();
$map_num=0;
# Чтение количества карт в списке карт для боя
foreach($ids as $ind => $val) {
      if ($val != 0) {
       $ids2[] = $val; # Запоминает какие карты есть в верхних слотах
       $map_num++;
       $cell[$val] = 1;
      }
}
?>
<div style='text-align: center;'><b>Последовательность карт для боя <?php echo $map_num; ?>/5</b></div>
<table><tr>
<?php
$free_cell = false; # Указывает что свободных слотов нет
# Отображает список карт для боя
if (sizeof($ids2) > 0) { # Если есть карты в списке карт для боя
      for ($i=0;$i<5;$i++) {
       if ($ids[$i] == 0) { # Указывает что есть свободный слот
        echo "<td style='padding:4px;'>
         <table style='width:180px;height:300px;display:block;border:1px solid black;'>
         <tr>
          <td style='text-align:center;width:180px;'>Пусто</td>
         </tr>
         </table>
        </td>";
        $free_cell = true;
       } else {
        # Получает характеристики карты и отображает их
        $sql = $db->q("SELECT *,`user_map`.`att` as `uatt`,`user_map`.`abi` as `uabi`,`user_map`.`skill` as `uskill` FROM `user_map`,`map` WHERE `user_map`.`user_login`='".$_SESSION['user_login']."' AND `map`.`id`=`user_map`.`map_id` AND `user_map`.`id`='".$ids[$i]."'");
        $row = mysqli_fetch_array($sql);
        $exp = explode("|",$row['lvl_exp']);
        echo "<td style='padding:4px;'>
          <table style='width:180px;display:block;border:1px solid black;'>
          <tr>
           <td style='text-align:center;width:180px;' colspan='2'><b>".$row['name']."</b></td>
          </tr>
          <tr>
           <td colspan='2'><div style='width:170px;height:120px;overflow:auto;display:block;border-bottom:1px solid black;'>".$row['desc']."</div></td>
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
          <tr>
           <td colspan='2' style='text-align:center;width:180px;'>
           <form method='post'>
            <input type='hidden' value='".$i."' name='cell_id'>
            <input type='submit' value='Убрать' name='del'>
           </form>
           </td>
          </tr>
          </table>
         </td>";
       }
      }
      ?>
      </tr></table>
<?php
} else { # Если их нет
      $free_cell = true;
      echo "Выберите карты из списка ниже";
}
?>

<div style='text-align: center;'><b>Список всех карт</b></div>
<table><tr>
<?php
# Отображает список всех карт игрока, кроме тех что выставлены на бой
$sql = $db->q("SELECT *,`user_map`.`id` as `uid`,`user_map`.`att` as `uatt`,`user_map`.`abi` as `uabi`,`user_map`.`skill` as `uskill` FROM `user_map`,`map` WHERE `user_map`.`user_login`='".$_SESSION['user_login']."' AND `map`.`id`=`user_map`.`map_id` AND `user_map`.`type`='1'");
$i=1;
while($row = mysqli_fetch_array($sql)) {
      $exp = explode("|",$row['lvl_exp']);
      echo "<td style='padding:4px;'>
        <table style='width:180px;display:block;border:1px solid black;'>
        <tr>
         <td style='text-align:center;width:180px;' colspan='2'><b>".$row['name']."</b></td>
        </tr>
        <tr>
         <td colspan='2'><div style='width:170px;height:120px;overflow:auto;display:block;border-bottom:1px solid black;'>".$row['desc']."</div></td>
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
        </tr>";
      # Если есть свободный слот и карта не находится в списке карт для боя [1], то отображается кнопка добавления в список карт для боя
      # [1] - Продвинутый пользователь может указать id карты, которая уже есть в списке карт для боя что приведен к неправильной работе скрипта, поэтому введена такая проверка
      if ($free_cell == true && $cell[$row['uid']] != 1) {
       echo "<tr>
         <td colspan='2' style='text-align:center;width:180px;'>
         <form method='post'>
          <input type='hidden' value='".$row['uid']."' name='map_id'>
          <input type='submit' value='Добавить' name='add'>
         </form>
         </td>
        </tr>";
      }
      echo "    </table>
       </td>";
      if (($i%5)==0) echo "</tr><tr>"; # Вывод карт по 5 штук в строку
      $i++;
}
?>
</tr></table>
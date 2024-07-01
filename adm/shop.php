<?php
include('../inc/db.php'); # Подключается класс БД
if (!empty($_POST['create'])) { # Создание "таблетки"
        $db = new db;
               
        if (!empty($_GET['id'])) { # Если существует id "таблетки", то "таблетка" редактируется
         $sql = $db->q("SELECT * FROM `shop` WHERE `id`='".$_GET['id']."'");
         if (mysqli_num_rows($sql) == 1) {
          $_POST['desc'] = str_replace("\n","<br>",$_POST['desc']);
          $hp = array(
           'life'=>'+'.$_POST['life']
          );
          $hp = json_encode($hp);
                 
          $sql = $db->q("UPDATE `shop` SET `name`='".$_POST['name']."',`desc`='".$_POST['desc']."',`action`='".$hp."',`cost`='".$_POST['cost']."' WHERE `id`='".$_GET['id']."'");
          if ($sql) echo '<div style="color:green;">Товар изменен</div>';
          else echo "<div style='color:red;'>Ошибка<br>".$db->err()."</div>";
         }
        } else { # Если не существует, то создается новая "таблетка"
         $_POST['desc'] = str_replace("\n","<br>",$_POST['desc']);
         $hp = array(
          'life'=>'+'.$_POST['life']
         );
         $hp = json_encode($hp);
                 
         $sql = $db->q("INSERT INTO `shop` (`name`,`desc`,`type`,`ptype`,`action`,`cost`) VALUES ('".$_POST['name']."','".$_POST['desc']."','1','1','".$hp."','".$_POST['cost']."')");
         if ($sql) echo '<div style="color:green;">Товар добавлен <a href="?id='.$_GET['id'].'">Просмотреть</a></div>';
         else echo "<div style='color:red;'>Ошибка<br>".$db->err()."</div>";
        }
}

if (!empty($_GET['id'])) { # Просмотр "таблетки"
        $db = new db;
               
        $sql = $db->q("SELECT * FROM `map` WHERE `id`='".$_GET['id']."'");
        if (mysqli_num_rows($sql) == 1) {
         $row = mysqli_fetch_array($sql);
         $row['desc'] = str_replace("<br>","\n",$row['desc']);
         $name = $row['name'];
         $desc = $row['desc'];
         $hp = json_decode($row['action']);
         $life = $hp->life;
         $cost = $row['cost'];
        } else {
         echo "<div style='color:red;'>Товар не найден</div>";
        }
}
?>
<form method="post">
        <table style="width:400px;">
         <tr><td style="width:110px;">Наименование</td><td><input style="width:100%;" type="text" name="name" value="<?php echo $name; ?>" /></td></tr>
         <tr><td colspan="2"><textarea name="desc" style="width:400px;height:150px;"><?php echo $desc; ?></textarea></td></tr>
                
         <tr><td style="width:110px;">Цена</td><td><input style="width:100%;" type="text" name="cost" value="<?php echo $cost; ?>" /></td></tr>
                
         <tr><td style="width:110px;">Здоровье+</td><td><input style="width:100%;" type="text" name="life" value="<?php echo $life; ?>" /></td></tr>
                
         <tr><td colspan="2"><input type="submit" value="<?php if (empty($name)) { echo 'Создать'; } else { echo 'Изменить'; } ?>" name="create" /></td></tr>
        </table>
</form>
<?php

if ($_SESSION['user_role'] != 'admin') {
    session_destroy();
    header("Location: ../index.php");
    exit;
}

include('../bd/db.php'); # Подключается класс БД
if (!empty($_POST['create'])) { # Создание карты
      $db = new db;
           
      if (!empty($_GET['id'])) { # Если существует id карты, то карта редактируется
       $sql = $db->q("SELECT * FROM `map` WHERE `id`='".$_GET['id']."'");
       if (mysqli_num_rows($sql) == 1) {
        $_POST['desc'] = str_replace("\n","<br>",$_POST['desc']);
        $exp = $_POST['exp0']."|".$_POST['exp1']."|".$_POST['exp2']."|".$_POST['exp3'];
             
        $char = $_POST['att2'].":".$_POST['abi2'].":".$_POST['skill2']."|";
        $char .= $_POST['att3'].":".$_POST['abi3'].":".$_POST['skill3']."|";
        $char .= $_POST['att4'].":".$_POST['abi4'].":".$_POST['skill4']."|";
        $char .= $_POST['att5'].":".$_POST['abi5'].":".$_POST['skill5'];
             
        $sql = $db->q("UPDATE `map` SET `name`='".$_POST['name']."',`desc`='".$_POST['desc']."',`att`='".$_POST['att']."',`abi`='".$_POST['abi']."',`skill`='".$_POST['skill']."',`lvl_exp`='".$exp."',`char`='".$char."',`cost`='".$_POST['cost']."' WHERE `id`='".$_GET['id']."'");
        if ($sql) echo '<div style="color:green;">Карта изменена</div>';
        else echo "<div style='color:red;'>Ошибка<br>".$db->err()."</div>";
       }
      } else { # Если не существует, то создается новая карта
       $_POST['desc'] = str_replace("\n","<br>",$_POST['desc']);
       $exp = $_POST['exp0']."|".$_POST['exp1']."|".$_POST['exp2']."|".$_POST['exp3'];
       $char = $_POST['att2'].":".$_POST['abi2'].":".$_POST['skill2']."|";
       $char .= $_POST['att3'].":".$_POST['abi3'].":".$_POST['skill3']."|";
       $char .= $_POST['att4'].":".$_POST['abi4'].":".$_POST['skill4']."|";
       $char .= $_POST['att5'].":".$_POST['abi5'].":".$_POST['skill5'];
             
       $sql = $db->q("INSERT INTO `map` (`name`,`desc`,`att`,`abi`,`skill`,`lvl_exp`,`char`,`cost`) VALUES ('".$_POST['name']."','".$_POST['desc']."','".$_POST['att']."','".$_POST['abi']."','".$_POST['skill']."','".$exp."','".$char."','".$_POST['cost']."')");
       if ($sql) echo '<div style="color:green;">Карта добавлена <a href="?id='.$_GET['id'].'">Просмотреть</a></div>';
       else echo "<div style='color:red;'>Ошибка<br>".$db->err()."</div>";
      }
}

if (!empty($_GET['id'])) { # Просмотр карты
      $db = new db;
           
      $sql = $db->q("SELECT * FROM `map` WHERE `id`='".$_GET['id']."'");
      if (mysqli_num_rows($sql) == 1) {
       $row = mysqli_fetch_array($sql);
       $row['desc'] = str_replace("<br>","\n",$row['desc']);
       $name = $row['name'];
       $desc = $row['desc'];
       $att = $row['att'];
       $abi = $row['abi'];
       $skill = $row['skill'];
       $exp = explode("|",$row['lvl_exp']);
       $char = explode("|",$row['char']);
       foreach($char as $ind => $val) {
        $char[$ind] = explode(":",$val);
       }
       $cost = $row['cost'];
      } else {
       echo "<div style='color:red;'>Карта не найдена</div>";
      }
}
?>
<form method="post">
      <table style="width:400px;">
       <tr><td style="width:110px;">Наименование</td><td><input style="width:100%;" type="text" name="name" value="<?php echo $name; ?>" /></td></tr>
       <tr><td colspan="2"><textarea name="desc" style="width:400px;height:150px;"><?php echo $desc; ?></textarea></td></tr>
            
       <tr><td style="width:110px;">Цена</td><td><input style="width:100%;" type="text" name="cost" value="<?php echo $cost; ?>" /></td></tr>
            
       <tr><td style="width:110px;">Атака</td><td><input style="width:100%;" type="text" name="att" value="<?php echo $att; ?>" /></td></tr>
       <tr><td style="width:110px;">Ловкость</td><td><input style="width:100%;" type="text" name="abi" value="<?php echo $abi; ?>" /></td></tr>
       <tr><td style="width:110px;">Мастерство</td><td><input style="width:100%;" type="text" name="skill" value="<?php echo $skill; ?>" /></td></tr>
            
       <tr><td style="width:110px;">Опыт 2 ур.</td><td><input style="width:100%;" type="text" name="exp0" value="<?php echo $exp[0]; ?>" /></td></tr>
       <tr><td style="width:110px;">Опыт 3 ур.</td><td><input style="width:100%;" type="text" name="exp1" value="<?php echo $exp[1]; ?>" /></td></tr>
       <tr><td style="width:110px;">Опыт 4 ур.</td><td><input style="width:100%;" type="text" name="exp2" value="<?php echo $exp[2]; ?>" /></td></tr>
       <tr><td style="width:110px;">Опыт 5 ур.</td><td><input style="width:100%;" type="text" name="exp3" value="<?php echo $exp[3]; ?>" /></td></tr>
            
       <tr><td colspan="2"><b>Повышение хар-к для 2 ур.</b></td></tr>
       <tr><td style="width:110px;">Атака</td><td><input style="width:100%;" type="text" name="att2" value="<?php echo $char[0][0]; ?>" /></td></tr>
       <tr><td style="width:110px;">Ловкость</td><td><input style="width:100%;" type="text" name="abi2" value="<?php echo $char[0][1]; ?>" /></td></tr>
       <tr><td style="width:110px;">Мастерство</td><td><input style="width:100%;" type="text" name="skill2" value="<?php echo $char[0][2]; ?>" /></td></tr>
       <tr><td colspan="2"><b>Повышение хар-к для 3 ур.</b></td></tr>
       <tr><td style="width:110px;">Атака</td><td><input style="width:100%;" type="text" name="att3" value="<?php echo $char[1][0]; ?>" /></td></tr>
       <tr><td style="width:110px;">Ловкость</td><td><input style="width:100%;" type="text" name="abi3" value="<?php echo $char[1][1]; ?>" /></td></tr>
       <tr><td style="width:110px;">Мастерство</td><td><input style="width:100%;" type="text" name="skill3" value="<?php echo $char[1][2]; ?>" /></td></tr>
       <tr><td colspan="2"><b>Повышение хар-к для 4 ур.</b></td></tr>
       <tr><td style="width:110px;">Атака</td><td><input style="width:100%;" type="text" name="att4" value="<?php echo $char[2][0]; ?>" /></td></tr>
       <tr><td style="width:110px;">Ловкость</td><td><input style="width:100%;" type="text" name="abi4" value="<?php echo $char[2][1]; ?>" /></td></tr>
       <tr><td style="width:110px;">Мастерство</td><td><input style="width:100%;" type="text" name="skill4" value="<?php echo $char[2][2]; ?>" /></td></tr>
       <tr><td colspan="2"><b>Повышение хар-к для 5 ур.</b></td></tr>
       <tr><td style="width:110px;">Атака</td><td><input style="width:100%;" type="text" name="att5" value="<?php echo $char[3][0]; ?>" /></td></tr>
       <tr><td style="width:110px;">Ловкость</td><td><input style="width:100%;" type="text" name="abi5" value="<?php echo $char[3][1]; ?>" /></td></tr>
       <tr><td style="width:110px;">Мастерство</td><td><input style="width:100%;" type="text" name="skill5" value="<?php echo $char[3][2]; ?>" /></td></tr>
            
       <tr><td colspan="2"><input type="submit" value="<?php if (empty($name)) { echo 'Создать'; } else { echo 'Изменить'; } ?>" name="create" /></td></tr>
      </table>
</form>
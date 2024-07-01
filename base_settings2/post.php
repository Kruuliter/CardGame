<?php
$User = mysqli_fetch_array($db->q("SELECT * FROM `user` WHERE `id`='".$_SESSION['user_id']."'"));
?>
<div style='text-align: center;'>
  <a href="?a=post">Сообщения</a> | <a href="?a=post&type=new">Написать</a>
</div>
<?php
if (empty($_GET['type'])) { # Просмотр сообщений
  if (!empty($_POST['del']) && $_POST['msg_id'] != '') { # Удаление выбранных сообщений
   $ids = $_POST['msg_id']; # Список id сообщений, которые нужно удалить
   $_ids = array();
   # Фильтрация
   foreach($ids as $val) {
    $val = preg_replace("/[^0-9]/","",$val);
    if ($val != '') $_ids[] = $val;
   }
   if (sizeof($_ids) > 0) {
    # Удаление сообщений и улаоений с БД
    $ids = implode(' OR `id`=',$_ids);
    $db->q("DELETE FROM `post` WHERE `p1`='".$_SESSION['user_id']."' AND (`id`=".$ids.")");
   }
  }
   
  $perpage = 10; # Количество сообщений на страницу
  if (empty($_GET['start'])) { # Стандартно происходит выборка сообщений с первой страницы
   $start = 0;
   $gstart = 1;
  } else { # Если указана страница, то выбираем сообщения начиная с неё
   $gstart = preg_replace("/[^0-9]/","",$_GET['start']);
   $start = (int)($gstart - 1) * $perpage;
  }
  $pages = ceil(mysqli_num_rows($db->q("SELECT * FROM `post` WHERE `p1`='".$_SESSION['user_id']."'")) / $perpage); # Количество страниц
  # Фильтрация страниц, если указана страница бОльшая чем их всего существует, то указывает последнюю страницу
  if ($start > $pages) $start = (int)($pages - 1) * $perpage;
   
  echo "<form method='post'><table style='width:600px;'><tr><td style='width:20px'></td><td style='width:130px'></td><td style='width:450px'></td></tr>";
  # Выборка сообщений в зависимости от страницы
  $sql = $db->q("SELECT * FROM `post` WHERE `p1`='".$_SESSION['user_id']."' ORDER BY `time` DESC LIMIT ".$start.",".$perpage);
  $isset = false; # Сообщений нет
  $names = array(); # Список логинов игроков. Если есть несколько писем от одного игрока, что бы каждый раз не получать его логин с БД, заносится в переменную
  while($post = mysqli_fetch_array($sql)) {
   $isset = true; # Сообщения есть
   $pre = '';
   if ($post['p2'] > 0) { # Если есть отправитель, то указывается его логин
    if ($names[$post['p2']]) { # Если логин сохранен в переменной, то выводит его
     $name['login'] = $names[$post['p2']];
    } else { # Если нет, то получает логин с БД
     $name = mysqli_fetch_array($db->q("SELECT `login` FROM `user` WHERE `id`='".$post['p2']."'"));
     $names[$post['p2']] = $name['login'];
    }
    $pre = "Сообщение от <b>".$name['login']."</b>:<br>";
   }
   # Время отправки сообщения
   $time = explode(" ",$post['time']);
   $time[1] = substr($time[1],0,-3);
   $time[0] = explode("-",$time[0]);
   $time[0] = $time[0][2].'.'.$time[0][1].'.'.$time[0][0];
   $time = implode(" ",$time);
   echo "<tr><td><input type='checkbox' value='".$post['id']."' name='msg_id[]'></td><td>".$time."</td><td>".$pre."<b>".$post['theme']."</b><br>".$post['text']."</td></tr>";
  }
  # Если есть сообщения, то выводится кнопка "Удалить"
  if ($isset)    echo "<tr><td colspan='2'><input type='submit' value='Удалить' name='del'></td></tr>";
  else echo "<tr><td colspan='4' align='center'>Писем нет</td></tr>";
  # Если страниц больше одной, то выводится листалка
  if ($pages > 1) {
   $show_page = '';
   $separate = ' ';
   $style = 'style="color: #000000; text-decoration: none;"';
   for ($i=1;$i<=$pages;$i++) {
    $show_page .= '<a href="?a=post&start='.$i.'" '.$style.'>'.($i==$gstart ? '<b>[' : '').$i.($i==$gstart ? ']</b>' : '').'</a>'.$separate;
   }
   echo "<tr><td colspan='4' align='center'>".$show_page."</td></tr>";
  }
  echo "</table></form>";
} else if ($_GET['type'] == 'new') { # Создание сообщения
  if (!empty($_POST['send'])) { # Отправка сообщения
   # Фильтрация данных
   $name = preg_replace("/'/","\\'",$_POST['name']);
   $name = htmlspecialchars($name);
   $theme = preg_replace("/'/","\\'",$_POST['theme']);
   $theme = htmlspecialchars($theme);
   $msg = preg_replace("/'/","\\'",$_POST['desc']);
   $msg = htmlspecialchars($msg);
   $msg = str_replace("\n","<br>",$msg);
   # Нельзя отправить самому себе
   if ($name != $User['login']) {
    # Проверка на существование игрока, которому отправляется письмо
    $sql = $db->q("SELECT `id` FROM `user` WHERE `login`='".$name."'");
    if (mysqli_num_rows($sql) == 1) {
     $row = mysqli_fetch_array($sql);
     # Отправка письма, либо вывод ошибки
     $sql2 = $db->q("INSERT INTO `post` (`p1`,`p2`,`read`,`theme`,`text`,`time`) VALUES ('".$row['id']."','".$_SESSION['user_id']."','1','".$theme."','".$msg."','".date("Y-d H:i:s")."')");
     $err = false;
     if ($sql2) echo "<div style='text-align: center;color: green;'>Письмо отправлено</div>";
     else {
      echo "<div style='text-align: center;color: red;'>Ошибка при отправке письма. Попробуйте снова. Если ошибка повторится, обратитесь к администрации</div>";
      $err = true;
     }
    }
   }
  }
?>
<form method="post">
  <table style="width:400px;">
   <tr><td style="width:110px;">Кому</td><td><input style="width:99%;" type="text" name="name" value="<?php if ($err) { echo $_POST['name']; } ?>"></td></tr>
   <tr><td style="width:110px;">Тема</td><td><input style="width:99%;" type="text" name="theme" value="<?php if ($err) { echo $_POST['theme']; } ?>"></td></tr>
   <tr><td colspan="2"><textarea name="desc" style="width:400px;height:150px;"><?php if ($err) { echo $_POST['desc']; } ?></textarea></td></tr>
    
   <tr><td colspan="2" align="right"><input type="submit" value="Отправить" name="send" ></td></tr>
  </table>
</form>
<?php
}
?>
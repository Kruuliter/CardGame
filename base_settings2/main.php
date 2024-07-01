<?php
$User = mysqli_fetch_array($db->q("SELECT * FROM `users` WHERE `login`='".$_SESSION['user_login']."';"));
$User['count_cards'] = mysqli_num_rows($db->q("SELECT * FROM `users_card` where `user_login`='".$_SESSION['user_login']."';"));
?>
<table>
<tr>
      <td>Игрок: </td><td><b><?php echo $User['login']; ?></b></td>
</tr>
<tr>
      <td>Количество карт: </td><td><?php echo $User['count_cards']; ?></td>
</tr>
<tr>
      <td>Деньги: </td><td><?php echo $User['money']; ?></td>
</tr>
</table>
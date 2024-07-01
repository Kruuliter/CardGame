<?php
$read_post = $db->q("SELECT `id` FROM `messages` WHERE `to_whom`='".$_SESSION['login']."' AND `m_read`='1'");
$num_messages = mysqli_num_rows($read_post); # Список полученых и непрочтенных сообщений
# Если найдены непрочтенные сообщения, то выводится их количество
if ($num_messages == 0)    $num_messages = '';
else $num_messages = '(<b>'.$num_messages.'</b>)';
# Если есть непрочтенные сообщения и игрок перешел в модуль почты, то отмечаем сообщения прочтенными
if ($num_messages != '' && $a == 'post') {
      $db->q("UPDATE `messages` SET `m_read`='0' WHERE `to_whom`='".$_SESSION['login']."'");
      $num_messages = '';
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Игра</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" href="css/menu.css" />

        <?php
            echo `<link rel="stylesheet" href="css/`.$a.`_style.css" />`;
        ?>

        <!-- Bootstrap CSS (jsDelivr CDN) -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
        <!-- Bootstrap Bundle JS (jsDelivr CDN) -->
        <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

    </head>
    <body>
        <table style="width:100%;">
                <tr>
                    <td valign="top" class="flex-container" style="width:100%;">
                        <div class="dropdown">
                            <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                                    Ссылка выпадающего списка
                            </a>

                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <li><a class="dropdown-item" href="main">Персонаж</a></li>
                                    <li><a class="dropdown-item" href="maps">Список карт</a></li>
                                    <li><a class="dropdown-item" href="exit">Выход</a></li>
                            </ul>
                        </div>
                        <div>
                            <a class="btn btn-secondary" href="post">&#9993;</a>
                        </div>
                        <div>
                            <a class="btn btn-secondary" href="shop">Аукцион</a>
                        </div>
                        <div>
                            <a class="btn btn-secondary" href="battle">Арена</a>
                        </div>
                    </td>
                </tr>

<!--
<table>
            <tr>
                <td valign="top" style="width:120px;display:block;">

                    <nav class="navbar navbar-default">

                        <div class="navbar-header">
                            <a class="navbar-brand" href="#">Меню</a>
                        </div>

                        <div class="container-fluid">
                            <ul class="nav navbar-nav">
                                <li class="dropdown dropdown-custom">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">&#9776;<span class="caret"></span></a>
                                    <div class="dropdown-menu">
                                        <ul class="list-unstyled" role="menu">
                                            <li>
                                                <a href="?a=main">Персонаж</a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="?a=maps">Список карт</a>
                                            </li>
                                            <li class="divider"></li>
                                            <li>
                                                <a href="?a=exit">Выход</a>
                                            </li>
                                        </ul>
                                    </div>!-- /.dropdown-menu --
                                </li>
                                <li><a href="?a=post">&#9993; <php echo $num_messages; ?></a><</li>
                                <li><a href="?a=trade">Аукцион</a></li>
                                <li><a href="?a=shop">Магазин</a></li>
                                <li><a href="?a=battle">Арена</a></li>
                            </ul>!-- /.navbar-nav --
                        </div>!-- /.container-fluid --

                    </nav>!-- /.navbar-default --
                </td>
                <td valign="top">
                -->
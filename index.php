<?php
    session_start(); # подключаем сессию
    if (!empty($_POST['auth'])){
        include('bd/connect.php'); # подключение класса для работы с БД
        $db = new db;

        # получаем данные от пользователя
        $login = $_POST['login'];
        $passwd = $_POST['passwd'];

        # проверяем существует ли пользователь
        # $sql = $db->q("SELECT 'login', 'passwd' FROM 'users' WHERE 'login' = '".$login."';");

        if ($sql = $db->q("SELECT `login` FROM `users` WHERE `login` = '".$login."' AND `passwd`=MD5('".$passwd."');")){
            if(mysqli_num_rows($sql) == 1){
                $row = mysqli_fetch_array($sql);
                $_SESSION['user_login'] = $row['login'];
                setcookie("userLogin", $row['login']);
                header("Location: game.php");
            }else{
                $err = '<p style="color: red;">Неверный логин или пароль</p>';
            }
        }
    }
?>


<?php
include 'base_settings2/header.php';
?>
<link rel="stylesheet" href="css/auth_reg.css" type="text/css"/>
<title>Вход</title>
<?php
include 'base_settings2/centers.php';
?>

<div class="form">
    <form class="form-horizontal" role="form" method="POST">
        <div class="form-group">
            <?php
                echo $err;
            ?>
            <div class="form-group">
                <label for="inputLogin3" class="col-sm-2 control-label">Логин</label>
                <div class="col-sm-10">
                    <input id="login" type="text" class="form-control" placeholder="Логин"  name="login" />
                </div>
            </div>
            <p></p>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">Пароль</label>
                <div class="col-sm-10">
                    <input id="passwd" type="password" class="form-control" placeholder="Пароль" name="passwd" />
                </div>
            </div>
            <p></p>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                <input id="submit" type="submit" value="Войти" name="auth" />
                </div>
            </div>
            <p></p>
            <div class="form-group">
            <a href="registration.php">Зарегестрироваться</a>
            </div> 
        </div>
    </form>
</div>
<?php
include 'base_settings2/ends.php';
?>
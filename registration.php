<?php
    session_start();
    if(!empty($_POST['reg'])){
        include('bd/connect.php');
        $db = new db;

        $login = $_POST['login'];
        $email = $_POST['email'];
        $passwd = $_POST['passwd'];
        $passwd_confirm = $_POST['repasswd'];
        $flag = TRUE;

        $pattern_login = '/(?=.{4,})(?!.{16})^[A-Z](.[a-zA-Z0-9_-]*)$/';
        $pattern_email = '/^[\w\.]+@[\w-]+\.[a-z]{2,4}$/';
        $pattern_passwd = '/^.*(?=.{6,})(?=.*[a-zA-Z])(?=.*\d)(?=.*[!#$%&? "]).*$/';
    
        if(!preg_match($pattern_login, $login)){
            $_SESSION['message'] = 'В логине используется английские буквы, начиная с заглавной и цифры и размер логина от 4 символов и не превышает 16';
            $flag = FALSE;
        }
    
        if(!preg_match($pattern_email, $email)){
            $_SESSION['message'] = 'Неверно введена почта';
            $flag = FALSE;
        }

        if(!preg_match($pattern_passwd, $passwd)){
            $_SESSION['message'] = 'Пароль должен быть больше 6 символов, так же пароль должен состоять из английских букв, цифр и спец символов (!#$%&?)';
            $flag = FALSE;
        }
    
        if ($passwd === $passwd_confirm && $flag) {

            $sql = $db->q("SELECT `login` FROM `users` WHERE `login`='".$login."' OR `email`='".$email."';");
            if(mysqli_num_rows($sql) > 0)
            {
                $_SESSION['message'] = 'Такой логин/email уже существует';
            }
            else
            {
                if($db->q("INSERT INTO `users` (`login`,`passwd`,`email`) VALUES ('".$login."', MD5('".$passwd."'),'".$email."');"))
                {
                    header("Location: index.php");
                }else{
                    $_SESSION['message'] = 'Произошла ошибка при выполнении запроса';
                }
            }
        } else {
            if($flag){
                $_SESSION['message'] = 'Пароли не совпадают';
            }
        }

        
    }
?>

<?php
include 'base_settings2/header.php';
?>
<link rel="stylesheet" href="css/auth_reg.css" type="text/css"/>
<title>Регистрация</title>
<?php
include 'base_settings2/centers.php';
?>
<div>
<div class="form">
    <form class="form-horizontal" role="form" method="POST">
        <div class="form-group">
            <?php
                if ($_SESSION['message']) {
                    echo '<p style="color: red;"> ' . $_SESSION['message'] . ' </p>';
                }
                unset($_SESSION['message']);
            ?>
            <div class="form-group">
                <label for="inputLogin3" class="col-sm-2 control-label">Логин</label>
                <div class="col-sm-10">
                    <input id="login" type="text" class="form-control" placeholder="Логин"  name="login" />
                </div>
            </div>
            <p></p>
            <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">Электронная почта</label>
                <div class="col-sm-10">
                    <input id="email" type="text" class="form-control" placeholder="Электронная почта"  name="email" />
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
                <label for="inputPassword3" class="col-sm-2 control-label">Подтверждение пароля</label>
                <div class="col-sm-10">
                    <input id="repasswd" type="password" class="form-control" placeholder="Подтверждение пароля" name="repasswd" />
                </div>
            </div>
            <p></p>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <input id="submit" type="submit" value="Зарегестрироваться" name="reg" />
                </div>
            </div>
            <p></p>
            <div class="form-group">
                <a href="index.php">Главная</a>
            </div> 
        </div>
    </form>
</div>
<?php
include 'base_settings2/ends.php';
?>
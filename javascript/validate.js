function ValidNickname(){
    var re = /^[a-zA-Z](.[a-zA-Z0-9_-]*)$/;
    var myLogin = document.getElementById('login').value;
    var valid = re.test(myLogin);
    return valid;
}

function ValidMail(){
    var re = /^[\w\.]+@[\w-]+\.[a-z]{2,4}$/;
    var myEmail = document.getElementById('email').value;
    var valid = re.test(myLogin);
    return valid;
}

function ValidPassword(){
    var re = /^.*(?=.{8,})(?=.*[a-zA-Z])(?=.*\d)(?=.*[!#$%&? "]).*$/;
    var myPasswd = document.getElementById('passwd').value;
    var valid = re.test(myLogin);
    return valid;
}

function ConfirmationPassword(){
    var passwd1 = document.getElementById('passwd').value;
    var passwd2 = document.getElementById('repasswd').value;
    return passwd1 === passwd2;
}

var form = document.querySelector('form');
form.onsubmit = function(){
    var err = true;

    if(!ValidNickname()){
        err = false;
        document.getElementById('login').innerHTML = document.getElementById('login').innerHTML + '<br / >' + "<font color='red'>В логине должны присутствовать заглавные буквы и цифры</font>";
    }

    if(!ValidMail()){
        err = false;
        document.getElementById('email').innerHTML = document.getElementById('email').innerHTML + '<br / >' + "<font color='red'>Неверно введена почта</font>";
    }

    if(!ValidPassword()){
        err = false;
        document.getElementById('passwd').innerHTML = document.getElementById('passwd').innerHTML + '<br / >' + "<font color='red'>Пароль должен быть больше 8 символов, так же пароль должен состоять из английских букв, цифр и спец символов (!#$%&?)</font>";
    }

    if(!ConfirmationPassword()){
        err = false;
        document.getElementById('repasswd').innerHTML = document.getElementById('repasswd').innerHTML + '<br / >' + "<font color='red'>Пароли должны совпадать</font>";
    }

    return err;
};
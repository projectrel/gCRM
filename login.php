<?php
//session_start();
//$_SESSION['id'] = 3;
?>

<!DOCTYPE html>
<html>
<title>gCRM</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="../../css/style.css" rel="stylesheet">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />
<script>
    $(window).load(function() {
        $(".loader").fadeOut("slow");

    });
</script>
<body>
<div class="login-page">
    <form class="login-form" id="login-form">
        <h2 class="login-form-header">Вход</h2>
        <div class="login-box">
            <input id="loginField" autocomplete="username" placeholder="Логин" type="text" name="login">
        </div>
        <div class="pass-box">

            <input id="passwordField" placeholder="Пароль" type="password" autocomplete="current-password"
                   name="password">
        </div>
            <div class="sign-in-box">
                <!--
                <label class="container">Запомнить меня
                    <input type="checkbox" id="remember-me-check">
                    <span class="checkmark"></span>
                </label> -->
                <button type="submit" class="login-form-submit">Войти</button>

            </div>
    </form>
</div>
<div class="loader"><div class="spinner"></div></div>
<script src="js/listeners.js"></script>
<script src="js/auth.js"></script>
<div id="user-inactive-modal" class="modal">
    <h2>Пользователь деактивирован</h2>
    <h3>Обратитесь к администратору</h3>
</div>
</body>
</html>

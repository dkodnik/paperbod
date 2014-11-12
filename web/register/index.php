<?php
# Подключаем конфиг
require_once '../conf.php';
require_once '../functions.php';

$retSess=checkSession(); 
if ($retSess==RS_OK) {
  # Переадресовываем браузер на страницу проверки нашего скрипта 
  header("Location: /profile/"); exit(); 
}

if(isset($_POST['submit'])) {

    $err = array();

    $email= htmlspecialchars($_POST['email']);
    $email=mysql_real_escape_string($email);

    # проверям логин 
    if(!checkEmail($email)) { 
        $err[] = "Incorrect email!"; 
    }
         
    # проверяем, не сущестует ли пользователя с таким именем 
    $query = mysql_query("SELECT COUNT(id) FROM users WHERE email='".mysql_real_escape_string($email)."'");//or die ("<br>Invalid query: " . mysql_error());
    if(mysql_result($query, 0) > 0) { 
        $err[] = "User with this email already exists in the system."; //Пользователь с таким логином уже существует в базе данных
    }
  
     
    # Если нет ошибок, то добавляем в БД нового пользователя 
    if(count($err) == 0) {

      $pswd=mb_strtolower(trim($_POST['password']));

      $salt = substr(md5($email), 10, 20)."\3\1\2\6";
      $password = md5(md5($pswd).$salt);
               
      mysql_query("INSERT INTO users SET email='".$email."', pswrd='".$password."'"); 
      $uid = (string)mysql_insert_id();

      $newVerCode=GUID();
      mysql_query("INSERT INTO verifi (idusr, vercode) VALUES ($uid, '$newVerCode');");
      // Отправляем письмо с проверочным адресом
      sendEMail(EMAIL_BOT, $email, "Verification PaperboD*", "Verification KEY: ".$newVerCode."<br/><a href='".URL_SITE_FULL."/verifi/?k=".$newVerCode."'>Go verification</a>");


      header("Location: /login/"); exit(); 
    }
} 

require_once('../h.php');
?>

      <form class="form-signin" role="form" method="POST" action="">
        <h2 class="form-signin-heading">Please sign up</h2>
        <input type="email" name="email" id="reg_inp" class="form-control" placeholder="Email address" required autofocus>
        <input type="password" name="password" id="reg_inp" class="form-control" placeholder="Password" required>
        By clicking "Sign up", you agree to our <a href="#" data-toggle="modal" data-target="#myModalTerms">terms</a> of service.
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Sign up</button>
      </form>

<?php
    if (isset($err)) {
      print '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
      print '<b>When registering the following errors occurred:</b><br>'; //При регистрации произошли следующие ошибки:
      foreach($err AS $error) 
      { 
        print $error."<br>"; 
      }
      print '</div>';
    }
?>

<?php
require_once('../f.php');
?>
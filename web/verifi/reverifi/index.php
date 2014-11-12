<?php
# Подключаем конфиг
require_once '../../conf.php';
require_once '../../functions.php';

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

    # проверяем, сущестует ли пользователя с таким именем 
    $query = mysql_query("SELECT COUNT(id) FROM users WHERE email='".mysql_real_escape_string($email)."'");//or die ("<br>Invalid query: " . mysql_error());
    if(mysql_result($query, 0) > 0) { 
    	$queryOne = mysql_query("SELECT COUNT(id) FROM users WHERE email='".mysql_real_escape_string($email)."' AND verified='0'");
        if(mysql_result($queryOne, 0) > 0) {
        	$qrID = mysql_query("SELECT id FROM users WHERE email='".mysql_real_escape_string($email)."' AND verified='0' LIMIT 1");
        	while ($rwID = mysql_fetch_array($qrID)) {
        		$uid=$rwID['id'];
        		$newVerCode=GUID();
        		mysql_query("UPDATE verifi SET vercode = '".$newVerCode."' WHERE idusr='".$uid."'");
        		// Отправляем письмо с проверочным адресом
        		sendEMail(EMAIL_BOT, $email, "Verification PaperboD*", "Verification KEY: ".$newVerCode."<br/><a href='".URL_SITE_FULL."/verifi/?k=".$newVerCode."'>Go verification</a>");
        	}
        	header("Location: /login/");
        	exit();
        } else {
        	$err[] = "User already verified!"; //Пользователь уже верифицирован
        }
    } else {
    	$err[] = "User with this email does not exist in the system."; //Пользователь с таким логином не существует в базе данных
    }
}

require_once('../../h.php');

    if (isset($err)) {
      print '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
      print '<b>When verification the following errors occurred:</b><br>'; //При верификации произошли следующие ошибки:
      foreach($err AS $error) 
      { 
        print $error."<br>"; 
      }
      print '</div>';
    }
?>

      <form class="form-signin" role="form" method="POST" action="">
        <h2 class="form-signin-heading">reVerified</h2>
        <input type="email" name="email" id="reg_inp" class="form-control" placeholder="Please your email" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Send</button>
      </form>
<?php
    
require_once('../../f.php');
?>
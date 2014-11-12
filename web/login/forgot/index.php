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
    	$qrID = mysql_query("SELECT id FROM users WHERE email='".mysql_real_escape_string($email)."' LIMIT 1");
        while ($rwID = mysql_fetch_array($qrID)) {
        	$uid=$rwID['id'];
        	
            $strTxtPswrd=generateCode();
        	
        	$pswd=mb_strtolower(trim($strTxtPswrd));
        	$salt = substr(md5($email), 10, 20)."\3\1\2\6";
        	$password = md5(md5($pswd).$salt);

        	mysql_query("UPDATE users SET pswrd = '".$password."' WHERE id='".$uid."'");
        	// Отправляем письмо с проверочным адресом
        	sendEMail(EMAIL_BOT, $email, "New data for PaperboD*", "New password: ".$strTxtPswrd."");
        }
        header("Location: /login/");
        exit();
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
        <h2 class="form-signin-heading">I forgot my password</h2>
        <input type="email" name="email" id="reg_inp" class="form-control" placeholder="Please your email" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Send</button>
      </form>
<?php
    
require_once('../../f.php');
?>
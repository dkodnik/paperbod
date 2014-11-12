<?php
# Подключаем конфиг
require_once '../conf.php';

$retSess=checkSession(); 
if ($retSess==RS_OK) {
  # Переадресовываем браузер на страницу проверки нашего скрипта 
  header("Location: /profile/"); exit(); 
}

# Если есть куки с ошибкой то выводим их в переменную и удаляем куки
if (isset($_COOKIE['errors'])){
  $errors = $_COOKIE['errors'];
  $url = parse_url('');
  setcookie('errors', '', time() - 60*24*30*12, '/', $url['host'], false, true );
}

if(isset($_POST['submit'])) {
    $email= htmlspecialchars($_POST['email']);
    $email=mysql_real_escape_string($email);
    
    # Вытаскиваем из БД запись, у которой логин равняеться введенному 
    $data = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE email='".$email."' LIMIT 1"));

    $pswd=mb_strtolower(trim($_POST['password']));

    $salt = substr(md5($email), 10, 20)."\3\1\2\6";
    $hashpass = md5(md5($pswd).$salt);

    # Сравниваем пароли 
    if($data['pswrd'] === $hashpass) { 
      if($data['verified'] == 1) { 
        # Генерируем случайное число и шифруем его 
        $hash = md5(generateCode(10)); 
           
        # Записываем в БД новый хеш авторизации и IP 
        mysql_query("UPDATE users SET hash='".$hash."' WHERE id='".$data['id']."'");// or die("MySQL Error: " . mysql_error()); 
       
        # Ставим куки
        $url = parse_url('');
        setcookie('id', $data['id'], time()+60*60*24*30, '/', $url['host'], false, true );
        setcookie('hash', $hash, time()+60*60*24*30, '/', $url['host'], false, true );
       
        # Переадресовываем браузер на страницу проверки нашего скрипта 
        header("Location: /profile/"); 
        exit(); 
      } else {
        require_once('../h.php');
        print '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
        print 'Your email is not verified! '; // 
        print '<a href="/verifi/reverifi">Resend verification code?</a>.<br>'; // Выслать повторно код верификации?!
        print '</div>';
      }
    } else { 
      require_once('../h.php');
      print '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
      print 'You have entered incorrect email/password</div><br>'; //Вы ввели неправильный email/пароль
    } 
} else {
  require_once('../h.php');
}


if(isset($_GET['e'])) {
  if($_GET['e']==1) {
    print '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    print '<h4>';
    print 'Error: No verifi key';
    print '</h4></div>';
  } else if($_GET['e']==2) {
    print '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    print '<h4>';
    print 'Error: Incorrect verifi key';
    print '</h4></div>';
  } else if($_GET['e']==3) {
    print '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    print '<h4>';
    print 'Error: This user is already verified';
    print '</h4></div>';
  } else if($_GET['e']=='good') {
    print '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    print '<h4>';
    print 'Good! Verifi!';
    print '</h4></div>';
  }
  // this request withdraw money is already verified
}
?>

      <form class="form-signin" role="form" method="POST">
        <h2 class="form-signin-heading">Please sign in</h2>
        <input type="email" name="email" class="form-control" placeholder="Email address" required autofocus>
        <input type="password" name="password" class="form-control" placeholder="Password" required>
        <a href="/login/forgot" data-toggle="modal">Forgot?</a>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="submit">Sign in</button>
      </form>

<?php
  # Проверяем наличие в куках номера ошибки
  if (isset($errors)) {
    print '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    print '<h4>'.$error[$errors].'</h4></div>';
  }
?>

<?php
require_once('../f.php');
?>
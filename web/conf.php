<?php
# настройки

define ('SITE_NAME', 'PaperboD*');
define ('URL_SITE', 'localhost');
define ('URL_SITE_FULL', 'http://paperbod.com');
define ('EMAIL_BOT', 'noreply@paperbod.com');

define ("DB_HOST", "localhost");
define ("DB_LOGIN", "**!**");
define ("DB_PASSWORD", "**!**");
define ("DB_NAME", "**!**");

define ('RS_OK', '0');
define ('RS_BAD', '1');
define ('RS_NO', '2');

define('ABSPATH', '/home/korn/prj/paperbod/web'); // WORK-PC
//define('ABSPATH', '/home/webpaper/www'); // SERVER
// куда писать логи
define('LOGFILE',          ABSPATH . '/_logs/log.txt');
define('PROBLEM_LOGFILE',  ABSPATH . '/_logs/log-problem.txt');
define('ERROR_LOGFILE',    ABSPATH . '/_logs/log-error.txt');
define('BAD_PAGE_LOGFILE', ABSPATH . '/_logs/log-bad-page.txt');
require_once ABSPATH . "/errors.php";
enable_errors();

mysql_connect(DB_HOST, DB_LOGIN, DB_PASSWORD);// or die ("MySQL Error: " . mysql_error());
mysql_query("set names utf8");// or die ("<br>Invalid query: " . mysql_error());
mysql_select_db(DB_NAME);// or die ("<br>Invalid query: " . mysql_error());

# массив ошибок
$error[0] = 'I do not know'; //Я вас не знаю
$error[1] = 'Turn cookies'; //Включи куки
$error[2] = 'You can not be here'; //Тебе сюда нельзя

// Проверка адреса почтового ящика
function checkEmail($str) {
	return preg_match("/^[\.A-z0-9_\-\+]+[@][A-z0-9_\-]+([.][A-z0-9_\-]+)+[A-z]{1,4}$/", $str);
}

// Проверка сессии
function checkSession() {
  $rSt=RS_OK;
  if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
    $userdata = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE id = '".intval($_COOKIE['id'])."' LIMIT 1"));
    if(($userdata['hash'] !== $_COOKIE['hash']) or ($userdata['id'] !== $_COOKIE['id'])) {
        $rSt=RS_BAD;
    } 
  } else { 
    $rSt=RS_NO;
  }
  return $rSt;
}

# Функция для генерации случайной строки 
function generateCode($length=6) { 
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789"; 
    $code = ""; 
    $clen = strlen($chars) - 1;   
    while (strlen($code) < $length) { 
        $code .= $chars[mt_rand(0,$clen)];   
    } 
    return $code; 
}
?>
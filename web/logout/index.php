<?php
# Подключаем конфиг
require_once '../conf.php';

if (isset($_COOKIE['id']) and isset($_COOKIE['hash'])) {
	$url = parse_url('');
	setcookie('id', '', time() - 60*60*24*30, '/', $url['host'], false, true );
	setcookie('hash', '', time() - 60*60*24*30, '/', $url['host'], false, true );
}
header('Location: /'); exit();
?>
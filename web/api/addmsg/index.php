<?php
# Подключаем конфиг
require_once '../../conf.php';
require_once '../../functions.php';

if(isset($_GET['t']) & isset($_GET['m'])) {
	$token= htmlspecialchars($_GET['t']);
    $token=mysql_real_escape_string($token);

    $msg= htmlspecialchars($_GET['m']);
    $msg=mysql_real_escape_string($msg);

    $query = mysql_query("SELECT COUNT(*) FROM sites WHERE feed_url='".$token."' AND feed_type='token' LIMIT 1");
    if(mysql_result($query, 0) > 0) { 
    	$data = mysql_fetch_assoc(mysql_query("SELECT * FROM sites WHERE feed_url='".$token."' AND feed_type='token' LIMIT 1"));
    	$idSites=$data['id'];

    	$txtQr="INSERT INTO tkn_post SET idSites='".$idSites."', message='".$msg."'";
    	mysql_query($txtQr);
    	print "{'status':'ok','msg':''}";
    } else {
    	print "{'status':'error','msg':'no - token'}";
    }
} else {
	print "{'status':'error','msg':'no - token and/or message'}";
}
exit();
?>
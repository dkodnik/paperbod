<?php
# подключаем конфиг
require_once '../conf.php';

# проверка авторизации
$retSess=checkSession();
if ($retSess==RS_OK) {
  header('Location: /profile/'); exit();
}

if(isset($_GET['k'])) {
	$vrfKEY= htmlspecialchars($_GET['k']);
	$vrfKEY=mysql_real_escape_string($vrfKEY);

	$query = "
        SELECT idusr, verified
        FROM verifi
        WHERE vercode='".$vrfKEY."'
        LIMIT 1;
    ";
    $result = mysql_query($query);
    $nayden=false;
    while ($row = mysql_fetch_array($result)) {
    	if($row['verified']!=1) {
            $nayden=true;
    		$query = "
    		UPDATE verifi
    		SET verified = '1'
    		WHERE vercode='".$vrfKEY."'
    		";
    		$result = mysql_query($query);
    		
    		$query = "
    		UPDATE users
    		SET verified = '1'
    		WHERE id='".$row['idusr']."'
    		";
    		$result = mysql_query($query);
    		
    		header('HTTP/1.1 301 Moved Permanently');
            header('Location: /login/?e=good');
    		exit();
    	} else {
    		header('HTTP/1.1 301 Moved Permanently');
            header('Location: /login/?e=3');
    		exit();
    	}
    }
    if(!$nayden) {
    	header('HTTP/1.1 301 Moved Permanently');
    	header('Location: /login/?e=2');
    	exit();
    }
} else {
	header('HTTP/1.1 301 Moved Permanently');
    header('Location: /login/?e=1');
    exit();
}

?>
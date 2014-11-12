<?php
# подключаем конфиг
require_once '../conf.php';

# проверка авторизации
$retSess=checkSession();
if ($retSess!=RS_OK) {
  header('Location: /login/'); exit();
}

if(isset($_POST['submit'])) {
    
    $inpFeedURL=mysql_real_escape_string(htmlspecialchars($_POST['inputFeedURL']));
    $inpURLPOD=mysql_real_escape_string(htmlspecialchars($_POST['inputURLPOD']));
    $inpUsrNmPOD=mysql_real_escape_string(htmlspecialchars($_POST['inputUsrNmPOD']));
    $inpPswrdAcPOD=mysql_real_escape_string(htmlspecialchars($_POST['inputPasswordAcPOD']));
    $slctFeedType=mysql_real_escape_string(htmlspecialchars($_POST['selectFeedType']));
    $inpStringFooter=mysql_real_escape_string(htmlspecialchars($_POST['inputStringFooter']));
    if (isset($_POST['inputViewURLFeeds'])) {
        $inpViewURLFeeds=1;
    } else {
        $inpViewURLFeeds=0;
    }

    if($slctFeedType=='')$slctFeedType='rss';
    if($slctFeedType=='rss') $typeFeedDB='rss';
    else if($slctFeedType=='twitter') $typeFeedDB='twitter';
    else if($slctFeedType=='facebook') $typeFeedDB='facebook';
    else if($slctFeedType=='token') $typeFeedDB='token';
    else $typeFeedDB='rss';

    if(mb_strlen($inpFeedURL)==0 | mb_strlen($inpURLPOD)==0 | mb_strlen($inpUsrNmPOD)==0 ) {
        /*echo "1=".$inpFeedURL."<br>";
        echo "2=".$inpURLPOD."<br>";
        echo "3=".$inpUsrNmPOD."<br>";*/
    	header('Location: /profile/?e=5'); 
        exit();
    }
    // mb_strtolower <- нижний регистр

    if($typeFeedDB=='twitter') {
        // Обрезать, если имеется такое http(s)://twitter.com/username до просто username
        if (preg_match("/twitter.com/i", $inpFeedURL)) {
            preg_match('@^(?:https?://twitter.com/)?([^/]+)@i', $inpFeedURL, $matches);
            $inpFeedURL = $matches[1];
        }
    }

    if($typeFeedDB=='facebook') {
        // Обрезать, если имеется такое http(s)://www.facebook.com/username до просто username
        if (preg_match("/facebook.com/i", $inpFeedURL)) {
            preg_match('@^(?:https?://www.facebook.com/)?([^/]+)@i', $inpFeedURL, $matches);
            $inpFeedURL = $matches[1];
        }
    }

    // 1- Проверяем на наличие inputFeedURL и inputURLPOD + inputUsrNmPOD
    $query = mysql_query("SELECT COUNT(*) FROM sites WHERE feed_url='".$inpFeedURL."'");
    if(mysql_result($query, 0) > 0) { 
        header('Location: /profile/?e=1'); exit();
    }
    $query = mysql_query("SELECT COUNT(*) FROM sites WHERE pod_url='".$inpURLPOD."' AND usrnm='".$inpUsrNmPOD."'");
    if(mysql_result($query, 0) > 0) { 
        header('Location: /profile/?e=2'); exit();
    }
    $data = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE id='".$_COOKIE['id']."' LIMIT 1"));
    $limFeedAcnt=$data['feedlim'];
    $query = mysql_query("SELECT COUNT(*) FROM sites WHERE idusr='".$_COOKIE['id']."'");
    $amntUsrFeeds=mysql_result($query, 0);
    if($amntUsrFeeds >= $limFeedAcnt) {
        header('Location: /profile/?e=3');
        exit();
    }
    $txtQr="INSERT INTO sites SET idusr='".$_COOKIE['id']."', feed_url='".$inpFeedURL."', feed_type='".$typeFeedDB."', pod_url='".$inpURLPOD."', usrnm='".$inpUsrNmPOD."', pswrd='".$inpPswrdAcPOD."', string_footer='".$inpStringFooter."', view_url='".$inpViewURLFeeds."'";
    mysql_query($txtQr);
    header('Location: /profile/?g=1'); exit();
} else {
	header('Location: /profile/?e=4'); exit();
}

?>

<?php
# подключаем конфиг
require_once '../conf.php';
require_once '../functions.php';

# проверка авторизации
$retSess=checkSession();
if ($retSess!=RS_OK) {
  $url = parse_url('');
  if ($retSess==RS_BAD) {
    setcookie('id', '', time() - 60*24*30*12, '/', $url['host'], false, true );
    setcookie('hash', '', time() - 60*24*30*12, '/', $url['host'], false, true );
    setcookie('errors', '1', time() + 60*24*30*12, '/', $url['host'], false, true );
    header('Location: /login/'); exit();
  } else if ($retSess==RS_NO) {
    setcookie('errors', '2', time() + 60*24*30*12, '/', $url['host'], false, true );
    header('Location: /login/'); exit();
  }
}
?>


<?php
require_once('../h2.php');

//?e=1 ,?e=2
if(isset($_GET['e'])) {
  if($_GET['e']==1) {
    //print '<div class="container">';
    print '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    print '<h4>';
    print 'This feed has already passed in the Diaspora*';
    print '</h4></div>';
    //print '</div>';
  } else if($_GET['e']==2) {
    //print '<div class="container">';
    print '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    print '<h4>';
    print 'This account is already activated Diaspora* for another feed';
    print '</h4></div>';
    //print '</div>';
  } else if($_GET['e']==3) {
    //print '<div class="container">';
    print '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    print '<h4>';
    print 'Add does not work, because you have reached the limit flows on account.';
    print '</h4></div>';
    //print '</div>';
  } else if($_GET['e']==4) {
    //print '<div class="container">';
    print '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    print '<h4>';
    print '?';
    print '</h4></div>';
    //print '</div>';
  } else if($_GET['e']==5) {
    //print '<div class="container">';
    print '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    print '<h4>';
    print 'What is not entered, try again';
    print '</h4></div>';
    //print '</div>';
  }
}
if(isset($_GET['g'])) {
  if($_GET['g']==1) {
    print '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
    print '<h4>';
    print 'Good! Feed is added!';
    print '</h4></div>';
  }
}

$result = mysql_query("SELECT * FROM sites WHERE idusr='".$_COOKIE['id']."'");
$i = 0;
while ($row = mysql_fetch_array($result)) {
  $i++;
  if($i==1) {
    ?> 
  <div style="color:#fff">
    <h2 style="padding: 0 0 0 40%;">List feed's</h2>
    <table class="table table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th></th>
          <th></th>
          <th><i class="fa fa-users"></i></th>
          <th>URL feed</th>
          <th>POD D*</th>
          <th>Username</th>
        </tr>
      </thead>
      <tbody>
  <?php
  }
  $dtAmnt_D=0;
  $rslt = mysql_query("SELECT COUNT(*) as a FROM feeds WHERE idusr='".$_COOKIE['id']."' AND idst='".$row['id']."' AND date > NOW() - INTERVAL 1 DAY;");
  while ($rw0 = mysql_fetch_array($rslt)) {
    $dtAmnt_D = $rw0["a"];
  }

  $dtAmnt_W=0;
  $rslt = mysql_query("SELECT COUNT(*) as a FROM feeds WHERE idusr='".$_COOKIE['id']."' AND idst='".$row['id']."' AND date > NOW() - INTERVAL 7 DAY;");
  while ($rw0 = mysql_fetch_array($rslt)) {
    $dtAmnt_W = $rw0["a"];
  }

  $dtAmnt_M=0;
  $rslt = mysql_query("SELECT COUNT(*) as a FROM feeds WHERE idusr='".$_COOKIE['id']."' AND idst='".$row['id']."' AND date > NOW() - INTERVAL 1 MONTH;");
  while ($rw0 = mysql_fetch_array($rslt)) {
    $dtAmnt_M = $rw0["a"];
  }

  $stS=$row['status'];
  if($stS==1) $tSt='<font color="green"><i class="fa fa-check-circle-o"></i></font>';
  else if($stS==0) $tSt='<font color="grey"><i class="fa fa-clock-o"></i></font>';
  else if($stS==2) $tSt='<font color="red"><i class="fa fa-times-circle-o"></i></font>';
  else if($stS==3) $tSt='<font color="red"><i class="fa fa-trash-o"></i></font>';
  if($row['feed_type']=='rss') $typeFeed = '<i class="fa fa-rss"></i>';
  else if($row['feed_type']=='twitter') $typeFeed = '<i class="fa fa-twitter"></i>';
  else if($row['feed_type']=='facebook') $typeFeed = '<i class="fa fa-facebook"></i>';
  else if($row['feed_type']=='token') $typeFeed = '<i class="fa fa-key"></i>';
  else $typeFeed = '';
  print "<tr onclick='gotoFeed(\"".n2c64($row['id'])."\")' style='cursor:pointer' title='amount: today=".$dtAmnt_D.", week=".$dtAmnt_W.", month=".$dtAmnt_M."'><td>".($i)."</td><td>".$tSt."</td><td>".($typeFeed)."</td><td>".$row['followers']."</td><td><p class='min_str'>".$row['feed_url']."</p></td><td>".$row['pod_url']."</td><td>".$row['usrnm']."</td></tr>";
}

if($i>0) {
  ?>
      </tbody>
    </table>
  </div>
  <script>
  function gotoFeed(nFeed){
    window.location.href = '/feed/?n='+nFeed;
  }
  </script>
  <?php
} else {
  ?>
  <div style="padding: 10% 0 20% 40%;font-size:30px;">
  <!--<div style="padding: 10% 0 20% 30%;font-size:30px;">-->  
    <!--<a href="#" data-toggle="modal" data-target="#myModal" style="color:#fff"><i class="fa fa-plus-circle fa-5x"></i></a>
    &nbsp;-->
    <a href="#" data-toggle="modal" data-target="#myModalHelp" style="color:gold"><i class="fa fa-question-circle fa-5x"></i></a>
  </div>
  <?php
}

require_once('../f2.php');
?>
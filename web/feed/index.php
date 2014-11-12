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

if(isset($_POST['submit'])) {
  if(isset($_POST['n'])) {
    $idFeed=c2n64($_POST['n']);
    if($_POST['delFeed']==1) {
      // Delete
      $query = "UPDATE sites SET status = '3' WHERE idusr='".$_COOKIE['id']."' AND id='".$idFeed."'";
      $result = mysql_query($query);
      header('HTTP/1.1 301 Moved Permanently');
      header('Location: /profile/');
      exit();
    } else {
      if($_POST['updateStatus']==1) {
        // Update
        $query = "UPDATE sites SET status = '0' WHERE idusr='".$_COOKIE['id']."' AND id='".$idFeed."'";
        $result = mysql_query($query);
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: /profile/');
        exit();
      } else {
        // Save
        $inpFeedURL=mysql_real_escape_string(htmlspecialchars($_POST['editFeedURL']));
        $inpURLPOD=mysql_real_escape_string(htmlspecialchars($_POST['editURLPOD']));
        $inpUsrNmPOD=mysql_real_escape_string(htmlspecialchars($_POST['editUsrNmPOD']));
        $inpPswrdAcPOD=mysql_real_escape_string(htmlspecialchars($_POST['editPasswordAcPOD']));
        $inpStringFooter=mysql_real_escape_string(htmlspecialchars($_POST['editStringFooter']));
        if (isset($_POST['editViewURLFeeds'])) {
          $inpViewURLFeeds=1;
        } else {
          $inpViewURLFeeds=0;
        }

        if(mb_strlen($inpFeedURL)==0 | mb_strlen($inpURLPOD)==0 | mb_strlen($inpUsrNmPOD)==0 | mb_strlen($inpPswrdAcPOD)==0 ) {
          header('HTTP/1.1 301 Moved Permanently');
          header('Location: /profile/?e=5'); 
          exit();
        }
        // mb_strtolower <- нижний регистр

        // 1 - Помечаем на проверку, на время
        $query = "UPDATE sites SET status = '0' WHERE idusr='".$_COOKIE['id']."' AND id='".$idFeed."'";
        $result = mysql_query($query);

        // 2 - Проверяем на наличие inputFeedURL
        $query = mysql_query("SELECT COUNT(*) FROM sites WHERE feed_url='".$inpFeedURL."' AND status = '1'");
        if(mysql_result($query, 0) > 0) { 
          header('HTTP/1.1 301 Moved Permanently');
          header('Location: /profile/?e=1'); 
          exit();
        }
        
        // 3 - Проверяем на наличие inputURLPOD + inputUsrNmPOD
        $query = mysql_query("SELECT COUNT(*) FROM sites WHERE pod_url='".$inpURLPOD."' AND usrnm='".$inpUsrNmPOD."' AND status = '1'");
        if(mysql_result($query, 0) > 0) { 
          header('HTTP/1.1 301 Moved Permanently');
          header('Location: /profile/?e=2'); 
          exit();
        }

        // 4 - Обновляем
        $query = "UPDATE sites SET status = '0', feed_url='".$inpFeedURL."', pod_url='".$inpURLPOD."', usrnm='".$inpUsrNmPOD."', pswrd='".$inpPswrdAcPOD."', string_footer='".$inpStringFooter."', view_url='".$inpViewURLFeeds."' WHERE idusr='".$_COOKIE['id']."' AND id='".$idFeed."'";
        $result = mysql_query($query);
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: /profile/');
        exit();
      }
    }
  } else {
    // BAD
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: /profile/');
    exit();
  }
}
?>


<?php
require_once('../h2.php');

$amntI=0;
if(isset($_GET['n'])) {
  $idFeed=c2n64($_GET['n']);
  // Обязательно и пользователя проверяем, т.к. злоумышленник может раздобыть код ФИДа
  $result = mysql_query("SELECT * FROM sites WHERE idusr='".$_COOKIE['id']."' AND id='".$idFeed."'");

  while ($row = mysql_fetch_array($result)) {
    $amntI++;
    ?>
    <div style="color:#fff;background-color: rgba(0,0,0,.6);padding: 5px 30px;margin: 5px;">
      <h2 style="padding: 0 0 0 40%;">Feed <small>edit</small></h2>
        <form class="form-horizontal" role="form" method="POST" action="/feed/">
          <input type="hidden" name="updateStatus" value="1">
          <input type="hidden" name="delFeed" value="0">
          <input type="hidden" name="n" value="<?php echo $_GET['n'];?>">
          <div class="form-group">
            <label for="editStatusFeed" class="col-sm-2 control-label">Status</label>
            <div class="col-sm-10">
              <?php
              $stS=$row['status'];
              if($stS==1) $tSt='<font color="green"><i class="fa fa-check-circle-o"></i></font> - Ok';
              else if($stS==0) $tSt='<font color="grey"><i class="fa fa-clock-o"></i></font> - Not verified';
              else if($stS==2) $tSt='<font color="red"><i class="fa fa-times-circle-o"></i></font> - Error <button type="submit" class="btn btn-warning" name="submit" title="Update status"><i class="fa fa-repeat"></i></button>';
              else if($stS==3) $tSt='<font color="red"><i class="fa fa-trash-o"></i></font> - Trash';
              echo $tSt;?>
            </div>
          </div>
        </form>
        <form class="form-horizontal" role="form" method="POST" action="/feed/">
          <input type="hidden" name="updateStatus" value="0">
          <input type="hidden" name="delFeed" value="0">
          <input type="hidden" name="n" value="<?php echo $_GET['n'];?>">
          <div class="form-group">
            <label for="editFeedURL" class="col-sm-2 control-label">Type</label>
            <div class="col-sm-10">
              <?php 
              if($row['feed_type']=='rss') $typeFeed = '<i class="fa fa-rss"></i> - RSS/Atom';
              else if($row['feed_type']=='twitter') $typeFeed = '<i class="fa fa-twitter"></i> - Twitter';
              else if($row['feed_type']=='facebook') $typeFeed = '<i class="fa fa-facebook"></i> - Facebook';
              else if($row['feed_type']=='token') $typeFeed = '<i class="fa fa-key"></i> - Token API';
              else $typeFeed = '?';
              echo $typeFeed;?>
            </div>
          </div>
          <div class="form-group">
            <label for="editFeedFollowers" class="col-sm-2 control-label">Followers</label>
            <div class="col-sm-10">
              <i class="fa fa-users"></i> <?php echo $row['followers'];?>
            </div>
          </div>
          <div class="form-group">
            <label for="editFeedURL" class="col-sm-2 control-label">Feed</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="editFeedURL" autocomplete="off" name="editFeedURL" placeholder="Url feed RSS/Atom or Twitter account" value="<?php echo $row['feed_url'];?>" <?php if($row['feed_type']=='token') echo 'readonly="readonly"';?> >
            </div>
          </div>
          <div class="form-group">
            <label for="editURLPOD" class="col-sm-2 control-label">POD</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="editURLPOD" autocomplete="off" name="editURLPOD" placeholder="Url pod, e.g.: https://diasp.org" value="<?php echo $row['pod_url'];?>">
            </div>
          </div>
          <div class="form-group">
            <label for="editUsrNmPOD" class="col-sm-2 control-label">Username</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="editUsrNmPOD" autocomplete="off" name="editUsrNmPOD" placeholder="Username pod" value="<?php echo $row['usrnm'];?>">
            </div>
          </div>
          <div class="form-group">
            <label for="editPasswordAcPOD" class="col-sm-2 control-label">Password</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" id="editPasswordAcPOD" autocomplete="off" name="editPasswordAcPOD" placeholder="Password pod">
            </div>
          </div>

          <div class="form-group">
            <label for="editStringFooter" class="col-sm-2 control-label">Footer</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="editStringFooter" autocomplete="off" name="editStringFooter" placeholder="String for footer feeds" value="<?php echo $row['string_footer'];?>">
            </div>
          </div>
          <div class="form-group">
            <label for="editViewURLFeeds" class="col-sm-2 control-label">View URL</label>
            <div class="col-sm-10">
              <?php
              if($row['view_url']==1) {
                ?>
                <input type="checkbox" id="editViewURLFeeds" name="editViewURLFeeds" checked> View URL for feeds
                <?php
              } else {
                ?>
                <input type="checkbox" id="editViewURLFeeds" name="editViewURLFeeds"> View URL for feeds
                <?php
              }
              ?>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="button" class="btn btn-default" onclick="javascript:window.location.href='/'">Close</button>
              <button type="submit" class="btn btn-primary" name="submit">Save</button>
              </form>
              
              <form class="form-horizontal" style="float:right;" role="form" method="POST" action="/feed/">
                <input type="hidden" name="updateStatus" value="0">
                <input type="hidden" name="delFeed" value="1">
                <input type="hidden" name="n" value="<?php echo $_GET['n'];?>">
                <button type="submit" class="btn btn-danger" name="submit">Delete</button>
              </form>

            </div>
          </div>
    </div>
    <?php
  }
}
if($amntI==0) {
  ?>
  <div style="padding: 10% 0 20% 40%;font-size:30px;">
    <a href="#" data-toggle="modal" data-target="#myModalHelp" style="color:gold"><i class="fa fa-question-circle fa-5x"></i></a>
  </div>
  <?php
}

require_once('../f2.php');
?>
<?php
// Подключаем конфиг
require_once 'conf.php';

$retSess=checkSession(); 
if ($retSess==RS_OK) {
  header("Location: /profile/"); exit(); 
}

require_once('h.php');

?>

          <div class="inner cover">
            <h1 class="cover-heading">Feeds to Diaspora*</h1>
            <p class="lead">Follow the news from your favorite site in Diaspora.</p>
            <p class="lead">
              <a href="/top/" class="btn btn-lg btn-default">Top-10</a>
            </p>
          </div>
<?php
require_once('f.php');
?>
<?php
# Подключаем конфиг
require_once '../conf.php';

require_once('../h.php');

?>
<h2>TOP 10</h2>
<table class="table table-hover">
      <thead>
        <tr>
          <th>#</th>
          <th>URL</th>
        </tr>
      </thead>
      <tbody>

<?php

$result = mysql_query("SELECT * FROM sites ORDER BY rating DESC LIMIT 10");
$i = 1;
while ($row = mysql_fetch_array($result)) {
	$urlHref=$row['pod_url']."/u/".$row['usrnm'];
  print '<tr><td>'.($i).'</td><td><a href="'.$urlHref.'" target="_blank">'.$urlHref.'</a></td></tr>';
  $i++;
}

?>
      	  </tbody>
    </table>
<?php
require_once('../f.php');
?>
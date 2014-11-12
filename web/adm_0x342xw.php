<?php
// Некоторые администрационные данные

# подключаем конфиг
require_once './conf.php';

$query = mysql_query("SELECT DISTINCT address FROM `flwrs_sites` WHERE 1");
$i = 0;
while ($row = mysql_fetch_array($query)) {
  $i++;
}
echo "Unique followers =".$i."; <br/>";

$query = mysql_query("SELECT COUNT(*) FROM `sites` WHERE status=1");
echo "Feeds =".mysql_result($query, 0)."; <br/>";

$query = mysql_query("SELECT COUNT(*) FROM `feeds` WHERE 1");
echo "Number of posts =".mysql_result($query, 0)."; <br/>";
?>
<?php
error_reporting(E_ALL);
$db_username = "user"; //Данные доступа БД
$db_password = "pass";
$db_name = "database";
$table_name = "table1";
$conn = new mysqli("localhost", $db_username, $db_password, $db_name); // устанавливаем соединение с БД
if ($conn->connect_error) {die("Ошибка подключения: " . $conn->connect_error);}
$graburl = "https://codificator.ru/code/mobile/regions.html"; // url для парсинга
ini_set('max_execution_time', 600);
function curl_get($host, $referer = null) 
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, "Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.9.168 Version/11.51");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_URL, $host);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $content = curl_exec($ch);
    echo curl_error($ch);
    curl_close($ch);
    return $content;
}
$url_readfile1 = curl_get($graburl); // считываем содержимое с адреса URL в переменную
$url_readfile = iconv("utf-8","cp1251",$url_readfile1); // конвертим в utf-8
$rf = trim(chop($url_readfile)); 
$s1 = 0; $i=0; // устанавливаем переменные
$mask1 = "<tr><td rowspan=\"";
$mask2 = "</tbody>";
$mask3 = "</td>";
$number = 0;
while ($s2 = strpos($rf,$mask1,$s1+5)) // создаем массивы $met и $region 
{
	$i++;
	$met[$i] = $s2;
	$s1 = $s2;
	$s2 = strpos($rf,$mask3,$s1);
	$s = $s2-$s1;
	$reg = trim(substr($rf,$s1,$s));
	$reg = str_replace($mask1,'',$reg);
	$finstr = strpos($reg, ">") ;
	$strlen = strlen($reg);
	$region[$i] = substr($reg, ++$finstr, $strlen);
}
$met[$i] = strpos($rf,$mask2,$s1)-57;  // последняя метка в строке
$e = 1;
$col = count($met); // количество регионов
$imask1 = "<td>";
$imask2 = "</td>";
$ss1 = strpos($rf,$imask1,$met[1]);
for ($t = 2; $t <= $col; $t++) // основной цикл по регионам
{
while ($ss1 < $met[$t]) // цикл по строкам
{	 
for ($x=1; $x<=3; $x++) // цикл по данным в строке
{
$ss2 = strpos($rf,$imask2,$ss1);
$ss = $ss2-$ss1;
$dat = trim(substr($rf,$ss1,$ss));
$dat = str_replace($imask1,"",$dat);
$ss1 = strpos($rf,$imask1,$ss2);
if ($x == 1) {$code = $dat;}
if ($x == 3) {$operator = $dat;}
}
$sql = "INSERT INTO ".$table_name." (code,region,operator) VALUES (\"".$code."\",\"".$region[$t-1]."\",\"".$operator."\")"; // строка запроса в БД
if ($conn->query($sql) === TRUE) 
	{
	$number++;
	} 
else {echo "Ошибка: " . $sql  . $conn->error . "</br>";}
}
}
echo "Успешно создано записей - ".$number." в таблице ".$table_name;
$conn->close();
?>

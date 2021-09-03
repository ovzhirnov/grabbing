<?php
function operator($telnumb) // Входные данные - номер телефона 
{
$db_username = "user"; // Данные доступа к БД
$db_password = "pass";
$db_name = "database";
$table_name = "table1";
$conn = new mysqli("localhost", $db_username, $db_password, $db_name); // устанавливаем соединение с БД
if ($conn->connect_errno) {
    echo "Ошибка подключения: MySQL: (" . $conn->connect_errno . ") " . $conn->connect_error;
}
$telephone = preg_replace("/[^,.0-9]/", '', $telnumb); // обрабатываем строку с номером
$strlen = strlen($telephone);
$telephone = substr($telephone,($strlen-10),3); // выделяем код
$res = $conn->query("SELECT * FROM ".$table_name." WHERE code='".$telephone."'"); //соединяемся с БД
if ($res->num_rows > 0)
{
	// Создаем массив из найденных в базе строк
$mas = array();
$i = 1;
while ($row = $res->fetch_assoc()) 
		{
		$tmp = array();
    	$code = $row['code'];
		$region = $row['region'];
		$operator = $row['operator'];
		$tmp[$i] = array ("code"=>$code,"region"=>$region,"operator"=>$operator."<br>");
		$mas[] = $tmp[$i]; 
		$i++;
		}
//return ($mas); //при запросе из вне можно вернуть массив
// выводим массив $dat на экран
foreach ($mas as $dat)
{
echo $dat['code']." ".$dat['region']."  ".$dat['operator']."<br>"; // выводим массив на экран
}
}
else {echo "В базе не удалось найти данных!";}
}
?>

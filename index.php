<?php
$link = mysqli_connect("localhost", "user", "password", "table");

// проверка соединения
if (mysqli_connect_errno()) {
    printf("Не удалось подключиться: %s\n", mysqli_connect_error());
    exit();
}
// параметры
$owner_id = 1234567;
$page_url = 'http://www.site.ru/pages/';
$status = 1;
$update = 0;

$result = mysqli_query($link, "SELECT `id`, `url` FROM `coach` WHERE `status` ='".$status."' and `update`='".$update."' LIMIT 50"); 
while($row = mysql_fetch_assoc($result)){
	$users[] = $row['url'];
	$url = $row['url'];
	$id = $row['id'];
	$res = file_get_contents('https://api.vk.com/method/likes.getList?type=sitepage&owner_id='.$owner_id.'&page_url='.$page_url.$url);
	$resp = json_decode($res, true);
	$kol = $resp['response']['count'];
	
	$rs = mysqli_query($link, "UPDATE `coach` SET `likes`= '{$kol}',`update`=1 WHERE `id` = '{$id}'");
	if(!$rs){die('Error: ' . mysqli_error());}
	$output = $url,' Количество лайков: '.$resp['response']['count'],'<br>';
}

//echo '<pre>'.print_r($users,true).'</pre>';
$vsego = count($users);
echo 'Количество: ',$vsego,'<hr>';

if (empty($vsego) OR $vsego == 0)
{
	$rs = mysqli_query($link, "UPDATE `coach` SET `update`=0");
	if(!$rs){die('Error: ' . mysqli_error());}
	$output = 'Обновление начинаем сначала!';
}
return $output;
mysqli_close($link);
?>

<?php
header('Access-Control-Allow-Origin:*');

if(isset($_GET['log']) && ($_GET['log'] === 'out')){
	unset($_SESSION['username']);
	unset($_SESSION['identity']);
	header('Location:http://localhost/backend/login.html');
	exit;
}

if(!isset($_POST['submit'])){
	exit('非法访问');
}
require_once('../common/database.php');
require_once('../common/response.php');

$username = htmlspecialchars($_POST['username']);
$password = md5($_POST['password']);

try{
	$database = Database::getInstance();
	$connect = $database->connect();
}catch(Exception $e){
	return Response::json(200,'数据库链接失败');
}

$sql = "SELECT username,password,identity FROM user WHERE username='$username' AND password='$password' limit 1";

$result = mysql_query($sql,$connect);

if($row = mysql_fetch_array($result)){
	if($row['identity'] === '1'){
		$identity = '管理员';
	}else if($row['identity'] === '0'){
		$identity = '游客';
	}
	session_start();
	$_SESSION['username'] = $username;
	$_SESSION['identity'] = $row['identity'];
	
	return Response::json(200,"欢迎 $identity:$username 登录");
	exit;
}else{
	return Response::json(405,"没有该用户信息，怕是弄错账号密码了呦，再想想吧~");
	exit;
}


?>
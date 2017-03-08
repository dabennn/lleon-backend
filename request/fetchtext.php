<?php
header('Access-Control-Allow-Origin:*');
require_once('../common/response.php');
require_once('../common/database.php');

try{
	$database = Database::getInstance();
	$connect = $database->connect();
}catch(Exception $e){
	return Response::json(403,'数据库链接失败');
}

if(isset($_GET['id'])){
	$id = $_GET['id'];
	$data = array();
	
	if(substr($id,0,1) === 'a'){
		$sql = "SELECT * FROM article WHERE text_id='$id'";
		$result = mysql_query($sql,$connect);
		
		while($row = mysql_fetch_array($result)){
			$data = array(
				'title'=>$row['title'],
				'type'=>'article',
				'category'=>$row['category'],
				'excerpt'=>$row['excerpt'],
				'content'=>$row['content'],
				'status'=>getStatus($row['status'])
			);
		}
		
	}else if(substr($id,0,1) === 'n'){
		$sql = "SELECT * FROM note WHERE text_id='$id'";
		$result = mysql_query($sql,$connect);
		
		while($row = mysql_fetch_array($result)){
			$data = array(
				'title'=>$row['title'],
				'type'=>'note',
				'category'=>$row['category'],
				'content'=>$row['content'],
				'status'=>getStatus($row['status'])
			);
		}
	}else{
		return FALSE;
	}
	
	$database->close();
	
	if(count($data) !== 0){
		return Response::json(200,'文章信息获取成功',$data);
	}else{
		return Response::json(402,'文章信息不存在');
	}
	
}

function getStatus($status){
	if($status === '0'){
		$status = '已删除';
	}else if($status === '1'){
		$status = '已发布';
	}else if($status === '2'){
		$status = '待发布';
	}
	return $status;
}
?>
<?php
header('Access-Control-Allow-Origin:*');
require_once('../common/database.php');
require_once('../common/response.php');

try{
	$database = Database::getInstance();
	$connect = $database->connect();
}catch(Exception $e){
	return Response::json(403,'数据库链接失败');
}

if(isset($_GET['keyword'])){
	$keyword = $_GET['keyword'];
	if($keyword !== ''){
		$data = array();
		$sql = 'SELECT title,text_id FROM article WHERE status=1 AND title LIKE "%'.$keyword.'%" UNION SELECT title,text_id FROM note WHERE status=1 AND title LIKE "%'.$keyword.'%";';
		$result = mysql_query($sql,$connect);
		if(mysql_num_rows($result) < 1){
			return Response::json(402,'无搜索结果');
		}else{
			$type;
			while($row = mysql_fetch_assoc($result)){
				$path = strchr($row['text_id'], 'a') ? '/article/' : '/note/';
				$data[] = array(
					'title'=>$row['title'],
					'path'=>$path.$row['text_id']
				);
			}
			return Response::json(200,'查询数据获取成功',$data);
		}
	}
	$database->close();
}

if(isset($_GET['type'],$_GET['query'])){
	$type = $_GET['type'];
	$qry = $_GET['query'];
	$sql = 'SELECT content,text_id FROM '.$type.' WHERE text_id='.'"'.$qry.'"'.';';
	$result = mysql_query($sql,$connect);
	$content = mysql_fetch_assoc($result)['content'];
	
	$database->close();
	
	return Response::json(200,'文章数据获取成功',$content);
}


?>
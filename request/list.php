<?php
header('Access-Control-Allow-Origin:*');
require_once('../common/database.php');
require_once('../common/response.php');

if(isset($_GET['page']) && $_GET['page'] === 'list'){
	try{
		$database = Database::getInstance();
		$connect = $database->connect();
	}catch(Exception $e){
		return Response::json(403,'数据库链接失败');
	}
	
	$data = array();
	
	$sql_arti = 'SELECT title,date,status,text_id FROM article ORDER BY date DESC';
	$article = array();
	
	$arti_result = mysql_query($sql_arti,$connect);
	while($arti_row = mysql_fetch_array($arti_result)){
		$article[] = array(
			'title'=>$arti_row['title'],
			'date'=>$arti_row['date'],
			'status'=>$arti_row['status'],
			'text_id'=>$arti_row['text_id']
		);
	}
	
	$sql_note = 'SELECT title,date,status,text_id FROM note ORDER BY date DESC';
	$note = array();
	
	$note_result = mysql_query($sql_note,$connect);
	while($note_row = mysql_fetch_array($note_result)){
		$note[] = array(
			'title'=>$note_row['title'],
			'date'=>$note_row['date'],
			'status'=>$note_row['status'],
			'text_id'=>$note_row['text_id']
		);
	}
	
	$data = array(
		'article'=>$article,
		'note'=>$note
	);
	
	$database->close();
	
	return Response::json(200,'数据获取成功',$data);
}

if(isset($_GET['status'],$_GET['id'])){
	try{
		$database = Database::getInstance();
		$connect = $database->connect();
	}catch(Exception $e){
		return Response::json(403,'数据库链接失败');
	}
	
	$status = $_GET['status'];
	$id = $_GET['id'];
	
	if(substr($id,0,1) === 'a'){
		$type = 'article';
	}else if(substr($id,0,1) === 'n'){
		$type = 'note';
	}else{
		return false;
	}
	
	$sql = "UPDATE $type SET status='$status' WHERE text_id='$id'";
	
	$database->update($sql);
	
	if(mysql_affected_rows()){
		echo 'success';
	}else{
		echo 'error';
	}
	
	$database->close();
}

?>
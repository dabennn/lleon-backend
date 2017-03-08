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

if(isset($_GET['type'])){
	$options = array();
	$type = $_GET['type'];
	$sql = "SELECT category_name FROM ".$type."_category";
	$result = mysql_query($sql,$connect);
	
	while($row = mysql_fetch_array($result)){
		$options[] = array($row['category_name']);
	}
	
	$database->close();
	
	return Response::json(200,'option数据获取成功',$options);
}

if(isset($_POST['title'],$_POST['category'],$_POST['content'],$_POST['type'],$_POST['handle'])){
	$title = $_POST['title'];
	$category = $_POST['category'];
	$content = $_POST['content'];
	$type = $_POST['type'];
	$handle = $_POST['handle'];
	$excerpt = isset($_POST['excerpt']) ? $_POST['excerpt'] : '';
	$status = isset($_POST['status']) ? $_POST['status'] : 2;
	$tid = isset($_POST['id']) ? $_POST['id'] : '';
	$text_id = '';
	$sql = '';
	
	$content = mysql_real_escape_string($content,$connect);
	
	date_default_timezone_set('PRC');
	$date = date("Y-m-d H:i:s");
	
	if($handle === 'insert'){
		switch ($type) {
			case 'article':
				$text_id = 'a'.time();
				$sql = "INSERT article (title,date,excerpt,content,category,text_id,status) VALUES ('$title','$date','$excerpt','$content','$category','$text_id','$status')";
				
				$database->insert($sql);
				
				setCategory('article_category',$category,$connect);
				break;
				
			case 'note':
				$text_id = 'n'.time();
				$sql = "INSERT note (title,date,content,category,text_id,status) VALUES ('$title','$date','$content','$category','$text_id','$status')";
				$database->insert($sql);
				
				setCategory('note_category',$category,$connect);
				
				break;
		}
		
		echo 'OK！文章已经被收录了';
		
	}else if($handle === 'update'){
		switch ($type) {
			case 'article':
				$sql = "UPDATE article SET title='$title',excerpt='$excerpt',content='$content',category='$category',status='$status' WHERE text_id='$tid'";
				
				setCategory('article_category',$category,$connect);
				
				$database->update($sql);
				if(mysql_affected_rows()){
					echo '文章修改成功';
				}else{
					echo '文章修改失败';
				}
				
				break;
			
			case 'note':
				$sql = "UPDATE note SET title='$title',content='$content',category='$category',status='$status' WHERE text_id='$tid'";
				
				setCategory('note_category',$category,$connect);
				
				$database->update($sql);
				if(mysql_affected_rows()){
					echo '文章修改成功';
				}else{
					echo '文章修改失败';
				}
				
				break;
		}
		
		$database->close();
		
	}else{
		echo '文章信息要写完整哦';
	}
}else{
	echo '快去写文章吧';
}

function setCategory($tb_name,$category = '',$connect){
	$result = mysql_query("SELECT category_name FROM $tb_name WHERE category_name='$category'",$connect);
	
	if(mysql_num_rows($result) < 1 && $category !== ''){
		$sql = "INSERT $tb_name (category_name) VALUES ('$category')";
		
		$GLOBALS['database']->insert($sql);
	}
	return;
}
?>
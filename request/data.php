<?php
header('Access-Control-Allow-Origin:*');
require_once('../common/response.php');
require_once('../common/database.php');
require_once('../common/cache.php');

try{
	$database = Database::getInstance();
	$connect = $database->connect();
}catch(Exception $e){
	return Response::json(403,'数据库链接失败');
}

$data = array();
$cache = new Cache();

if(!$data = $cache->cacheData('lleon_home_cache')){
	
	$arti_resource = mysql_query('SELECT title,date,excerpt,text_id FROM article WHERE status = 1 ORDER BY date DESC',$connect);
	
	while($arti_row = mysql_fetch_array($arti_resource)){
		$article[] = array(
			'title'=>$arti_row['title'],
			'date'=>$arti_row['date'],
			'excerpt'=>$arti_row['excerpt'],
			'index'=>$arti_row['text_id']
		);
	}
	
	$note_resource = mysql_query('SELECT title,date,text_id FROM note WHERE status = 1 ORDER BY date DESC',$connect);
	
	while($note_row = mysql_fetch_array($note_resource)){
		$note[] = array(
			'title'=>$note_row['title'],
			'date'=>$note_row['date'],
			'index'=>$note_row['text_id']
		);
	}
	
	$arti_category = getCategory('article_category','article');
	$note_category = getCategory('note_category','note');
	
	$articles = array('article'=>$article,'category'=>$arti_category);
	$notes = array('note'=>$note,'category'=>$note_category);
	$data = array(
		'articles'=>$articles,
		'notes'=>$notes
	);
	
	if($data){
		$cache->cacheData('lleon_home_cache',$data,1800);
	}
}


if($data){
	return Response::json(200,'首页数据获取成功',$data);
}else{
	return Response::json(400,'首页数据获取失败');
}

$database->close();


function getCategory($cate_table,$table){
	$cate_sql = 'SELECT category_name FROM '.$cate_table.' ORDER BY category_name';
	$data_sql = 'SELECT title,text_id FROM '.$table.' WHERE category = "';
	
	$connect = $GLOBALS['connect'];
	$title = array();
	
	$cate_resource = mysql_query($cate_sql,$connect);
	
	while($cate_row = mysql_fetch_array($cate_resource)){
		$name = $cate_row['category_name'];
		$sql = $data_sql.$name.'"'.';';
		$title_resource = mysql_query($sql,$connect);
		while($title_row = mysql_fetch_array($title_resource)){
			$title[] = array(
				'title'=>$title_row['title'],
				'index'=>$title_row['text_id']
			);
		}
		$category[] = array(
			'name'=>$cate_row['category_name'],
			'titles'=>$title,
			'num'=>count($title)
		);
		$title = array();
	}
	
	if($table === 'note'){
		$note_title = array();
		
		$all = mysql_query('SELECT title,text_id FROM note ORDER BY date DESC;',$connect);
		while($all_row = mysql_fetch_array($all)){
			$note_title[] = array(
				'title'=>$all_row['title'],
				'index'=>$all_row['text_id']
			);
		}
		
		$note_all = array(
			'name'=>'全部',
			'titles'=>$note_title,
			'num'=>count($note_title)
		);
		
		array_unshift($category,$note_all);
		
	}

	return $category;
}


?>
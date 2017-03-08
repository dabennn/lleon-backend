<?php
header('Access-Control-Allow-Origin:*');
require_once('../common/response.php');

if(isset($_GET['cache'])){
	
	$path = dirname(__FILE__).'/cache/';
	
	if($_GET['cache'] === 'now'){
		date_default_timezone_set('PRC');
		$filenames = array_slice(scandir($path), 2);
		$data = array();
		
		foreach ($filenames as $value) {
			$filename = $value;
			$time = date("Y-m-d H:i:s",filectime($path.$value));
			$description = '未知缓存数据';
			
			if(strstr($filename,'home')){
				$description = '首页缓存数据';
			}
			
			$data[] = array(
				'filename'=>$filename,
				'description'=>$description,
				'time'=>$time
			);
			clearstatcache();
		}
		
		return Response::json(200,'文件信息获取成功',$data);
	}
	
	if(isset($_GET['filename'])){
		if($_GET['cache'] === 'del'){
			$filename = $path.$_GET['filename'];
			
			return @unlink($filename);
		}
	}
}

?>
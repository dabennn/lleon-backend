<?php
	class Cache {
		private $_dir;
		const EXT = '.txt';
		
		public function __construct(){
			$this->_dir = dirname(__FILE__).'/cache/';
		}
		public function cacheData($name,$value = '',$cacheTime = 0){
			$filename = $this->_dir.$name.self::EXT;
			
			if($value !== ''){
				if(is_null($value)){
					return @unlink($filename);
				}
				
				$dir = dirname($filename);
				if(!is_dir($dir)){
					mkdir($dir,0777);
				}
				
				$cacheTime = sprintf('%011d',$cacheTime);//设置缓存时间为11位，不足11位在前面补0
				return file_put_contents($filename, $cacheTime.json_encode($value));
			}
			
			if(!is_file($filename)){
				return FALSE;
			}
			$contents = file_get_contents($filename);
			$cacheTime = (int)substr($contents, 0, 11);//截取缓存时间
			$value = substr($contents, 11);
			if($cacheTime != 0 && ($cacheTime + filemtime($filename) < time())){	//判断缓存是否失效，缓存时间加文件创建时间小于当前时间，表示缓存失效
				unlink($filename);
				return FALSE;
			}
			return json_decode($value,true);
		}
	}
	
?>
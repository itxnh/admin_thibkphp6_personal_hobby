<?php
/**
*	后台管理系统
*/
namespace app\admin\model;
use think\Model;
use think\facade\App;

class Base extends Model{
	public function logs($data=null,$fileName=''){
		if(is_null($data) || is_null($fileName)){
			return false;
		}
		//获取Runtime路径
		$path = App::getRuntimePath() . 'logs' . DIRECTORY_SEPARATOR;
		if(!is_dir($path)){
			$mkdir_re = mkdir($path,0777,TRUE);
			if(!$mkdir_re){
				$this -> logs($data,$fileName);
			}
		}
		$info = ['data'=>$data];
		$content = json_encode($info, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
		if(empty($fileName)){
			$filePath = $path . "/" . date("Ymd",time()).'.info.log';
		}else{
			$filePath = $path . "/" . $fileName . '.info.log';
		}
		$time = "[".date("Y-m-d H:i:s",time())."]";
		$re = file_put_contents($filePath, $time." ".$content , FILE_APPEND);
		if(!$re){
			$this -> logs($data,$fileName);
		}else{
			return true;
		}
	}
}
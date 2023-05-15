<?php
/**
* 配置表
*/
namespace app\admin\model;
use app\admin\model\Base;

class BewAdminConfig extends Base{
	protected $pk = 'config_id';
	/**
	* 列出全部配置，key对应value
	*/
	public function getAll(){
		$aList = static::where('config_status',1)->order('config_sort DESC')->select()->toArray();
		if(empty($aList)){
			return [];
		}else{
			$return = [];
			foreach($aList as $k=>$v){
				$return[$v['config_name']] = $v['config_value'];
			}
		}
		return $return;
	}
	/**
	* 多条数据更新
	*/
	public function updateAll($data){
		$lists = static::order('config_sort DESC,config_id')->select()->toArray();
		if(empty($lists)){
			return false;
		}else{
			foreach($lists as &$lists_v){
				$lists_v['config_value'] = $data[$lists_v['config_name']];
			}
			$save = static::saveAll($lists);
			if(empty($save)){
				return false;
			}
		}
		return true;
	}
}
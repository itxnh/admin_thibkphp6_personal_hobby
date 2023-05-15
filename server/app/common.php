<?php
/*
 * 递归处理菜单数据，解决新菜单（菜单展示时，使用）
 */
function gettreeitemsnew($items){
	$tree = array();
	foreach ($items as $item) {
		if(isset($items[$item['parent_id']])){
			$items[$item['parent_id']]['children'][] = &$items[$item['smid']];
		}else{
			$tree[] = &$items[$item['smid']];
		}
	}
	return $tree;
}
/**
 * 递归删除目录和文件
 * @param string $dir_name
 * @return bool
 */
function delete_dir_file($dir_name){
	$result = false;
	if (is_dir($dir_name)) {
		if ($handle = opendir($dir_name)) {
			while (false !== ($item = readdir($handle))) {
				if ($item != '.' && $item != '..') {
					if (is_dir($dir_name . DIRECTORY_SEPARATOR . $item)) {
						delete_dir_file($dir_name . DIRECTORY_SEPARATOR . $item);
					} else {
						unlink($dir_name . DIRECTORY_SEPARATOR . $item);
					}
				}
			}
			closedir($handle);
			if (rmdir($dir_name)) {
				$result = true;
			}
		}
	}
	return $result;
}
/**
 * 递归处理菜单数据
 */
function formatMenus($items){
	foreach($items as &$item){
		if(isset($item['children'])){
			$item['children'] = array_values($item['children']);
			formatMenus($item['children']);
		}
	}
	$items = array_values($items);
	return $items;
}
/**
* 在文本里加反斜杠
*/
function enSql($str){
	return addslashes($str);
}
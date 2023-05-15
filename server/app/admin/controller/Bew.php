<?php
/**
 *	后台管理系统-核心功能
 */
namespace app\admin\controller;
use think\facade\Db;
use think\facade\Request;

class Bew extends Base{
    # 导航列表
    public function menulists(){
        $lists = Db::table('bew_admin_sys_menu')->order('parent_id ACS,sort DESC')->select()->toArray();
        if(empty($lists)){
            $this->returnCode(91000005);
        }
        foreach($lists as &$lists_v){
            $lists_v['key'] = $lists_v['mid'];
            if($lists_v['status'] == 1){
                $lists_v['status_s'] = '开启';
            }else{
                $lists_v['status_s'] = '关闭';
            }
            if($lists_v['type'] == 0){
                $lists_v['type_s'] = '分组';
            }elseif($lists_v['type'] == 1){
                $lists_v['type_s'] = '模块';
            }elseif($lists_v['type'] == 2){
                $lists_v['type_s'] = '超链接';
            }
            if($lists_v['parent_id'] == 0){
                $tmp[$lists_v['mid']] = $lists_v;
            }else{
                $tmp[$lists_v['parent_id']]['son'][] = $lists_v;
            }
        }
        $arr = [
            'lists' => array_merge($tmp)
        ];
        $this->returnCode(0,$arr);
    }
    # 导航添加、修改
    public function menusave(){
        $mid = (int)trim(input('post.mid'));
        $data['parent_id'] = trim(input('post.parent_id',0));
        $data['label'] = trim(input('post.label'));
        if(!$data['label']){
            $this->returnCode(90000100);
        }
        $data['type'] = trim(input('post.type',0));
        if($data['type'] != 0){
            $data['src'] = trim(input('post.src'));
        }else{
            $data['src'] = '';
        }
        $data['icon_class'] = trim(input('post.icon_class'));
        $data['sort'] = trim(input('post.sort',0));
        $data['status'] = trim(input('post.status',1));

        if(empty($mid) || $mid === 0){
            $res = Db::table('bew_admin_sys_menu')->insert($data);
            if(!$res){
                $this->returnCode(91000001);
            }
            $this->returnCode(0);
        }else{
            $res = Db::table('bew_admin_sys_menu')->where('mid',$mid)->update($data);
            if(!$res){
                $this->returnCode(91000002);
            }
            $data['mid'] = $mid;
            $this->returnCode(0,$data);
        }
    }
    # 导航删除
    public function menudel(){
        $mid = (int)input('post.mid');
        $menu = Db::table('bew_admin_sys_menu')->where('parent_id',$mid)->find();
        if(!empty($menu)){
            $this->returnCode(90000101);
        }
        $res = Db::table('bew_admin_sys_menu')->where('mid',$mid)->delete();
        if(empty($res)){
            $this->returnCode(91000003);
        }
        $this->returnCode(0);
    }
}
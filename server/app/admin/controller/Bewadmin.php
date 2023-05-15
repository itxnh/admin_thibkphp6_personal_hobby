<?php
/**
 *	后台管理系统-核心功能
 */
namespace app\admin\controller;
use think\facade\Db;
use think\facade\Request;

class Bewadmin extends Base{
    # 部门列表
    public function grouplists(){
        $lists = Db::table('bew_admin_user_group')->select()->toArray();
        if(empty($lists)){
            $this->returnCode(91000005);
        }
        foreach($lists as &$lists_v){
            $lists_v['key'] = $lists_v['group_id'];
            if($lists_v['status'] == 1){
                $lists_v['status_s'] = '开启';
            }else{
                $lists_v['status_s'] = '关闭';
            }
            $lists_v['time_add'] = date('Y年m月d日',$lists_v['time_add']);
            if($lists_v['rights']){
                $lists_v['rights'] = json_decode($lists_v['rights']);
            }
        }
        $menu = Db::table('bew_admin_sys_menu')->where('status',1)->order('mid ASC')->select()->toArray();
        if(empty($menu)){
            $this->returnCode(91000005);
        }
        $tmp_menu = [];
        foreach($menu as $menu_v){
            if($menu_v['parent_id'] == 0){
                $tmp_menu[$menu_v['mid']] = $menu_v;
            }
        }
        foreach($menu as $menu_v){
            if($menu_v['parent_id'] != 0){
                $tmp_menu[$menu_v['parent_id']]['son'][] = $menu_v;
            }
        }
        $arr = [
            'lists' => $lists,
            'menu' => $tmp_menu
        ];
        $this->returnCode(0,$arr);
    }
    # 部门添加、修改
    public function groupsave(){
        $group_id = (int)trim(input('post.group_id'));
        $data['group_name'] = trim(input('post.group_name'));
        if(!$data['group_name']){
            $this->returnCode(90000017);
        }
        $data['status'] = (int)trim(input('post.status'));
        $menus = input('post.menus');
        if($menus){
            $data['rights'] = json_encode($menus);
        }else{
            $data['rights'] = '';
        }

        if(empty($group_id) || $group_id === 0){
            $data['time_add'] = time();
            $res = Db::table('bew_admin_user_group')->insert($data);
            if(!$res){
                $this->returnCode(91000001);
            }
            $this->returnCode(0);
        }else{
            $res = Db::table('bew_admin_user_group')->where('group_id',$group_id)->update($data);
            if(!$res){
                $this->returnCode(91000002);
            }
            $data['group_id'] = $group_id;
            $data['rights'] = json_decode($data['rights']);
            $this->returnCode(0,$data);
        }
    }
    # 部门删除
    public function groupdel(){
        $group_id = (int)input('post.group_id');
        $res = Db::table('bew_admin_user_group')->where('group_id',$group_id)->delete();
        if(empty($res)){
            $this->returnCode(91000003);
        }
        $this->returnCode(0);
    }
    # 管理员列表
    public function userlists(){
        $lists = Db::table('bew_admin_user')->select()->toArray();
        if(empty($lists)){
            $this->returnCode(91000005);
        }
        $group = [];
        $groups = Db::table('bew_admin_user_group')->select()->toArray();
        if(empty($groups)){
            $this->returnCode(90000012);
        }
        foreach ($groups as $key => $value) {
            $group[$value['group_id']] = $value;
        }
        foreach($lists as &$lists_v){
            $lists_v['key'] = $lists_v['uid'];
            $lists_v['group_name'] = $group[$lists_v['group_id']]['group_name'];
            if($lists_v['time_last'] == 0){
                $lists_v['time_last'] = '从未登录';
            }else{
                $lists_v['time_last'] = date('Y-m-d H:i',$lists_v['time_last']);
            }
            if($lists_v['status'] == 1){
                $lists_v['status_s'] = '开启';
            }else{
                $lists_v['status_s'] = '关闭';
            }
            if($lists_v['sex'] == 1){
                $lists_v['sex_s'] = '男';
            }else{
                $lists_v['sex_s'] = '女';
            }
        }
        $arr = [
            'lists' => $lists,
            'group' => $group
        ];
        $this->returnCode(0,$arr);
    }
    # 管理员添加
    public function usersave(){
        $data['account'] = trim(input('post.account'));
        if(empty($data['account'])){
            $this->returnCode(90000005);
        }
        $pattern = "/^([0-9A-Za-z-_.]+)@([0-9a-z]+.[a-z]{2,3}(.[a-z]{2})?)$/i";
        if(!preg_match($pattern,$data['account'])){
            $this->returnCode(90000006);
        }
        $data['name'] = trim(input('post.name'));
        if(empty($data['name'])){
            $this->returnCode(90000013);
        }
        $data['group_id'] = (int)input('post.group_id');
        if(empty($data['group_id'])){
            $this->returnCode(90000014);
        }
        $password = trim(input('post.password'));
        
        $data['phone'] = trim(input('post.phone'));
        $data['qq'] = (int)trim(input('post.qq'));
        $data['sex'] = (int)(input('post.sex'));
        $data['status'] = (int)(input('post.status'));

        $uid = (int)trim(input('post.uid'));
        if(empty($uid) || $uid === 0){
            $item = Db::table('bew_admin_user')->where('account',$data['account'])->find();
            if($item){
                $this->returnCode(90000015);
            }
            $data['time_add'] = time();
            if(empty($password)){
                $this->returnCode(90000007);
            }else{
                $data['password'] = md5($password);
            }
            $res = Db::table('bew_admin_user')->insert($data);
            if(!$res){
                $this->returnCode(91000001);
            }
            $this->returnCode(0);
        }else{
            if(!empty($password)){
                $data['password'] = md5($password);
            }
            $res = Db::table('bew_admin_user')->where('uid',$uid)->update($data);
            if(!$res){
                $this->returnCode(91000002);
            }
            $data['uid'] = $uid;
            $this->returnCode(0,$data);
        }
    }
    # 删除管理员
    public function userdel(){
        $uid = (int)input('post.uid');
        $res = Db::table('bew_admin_user')->where('uid',$uid)->delete();
        if(empty($res)){
            $this->returnCode(91000003);
        }
        $this->returnCode(0);
    }
}
<?php
/**
 *	后台管理系统-首页
 */
namespace app\admin\controller;
use think\facade\Db;
use think\facade\Request;

class Index extends Base{
    # 首页
    public function index(){
        $where = ['group_id' => $this->adminUser['group_id']];
        $group = Db::table('bew_admin_user_group')->where($where)->find();
        if (empty($group)) {
            $this->returnCode(90000003);
        }
        if (empty($group['rights'])) {
            $this->returnCode(90000004);
        }
        $group['rights'] = json_decode($group['rights'], true);

        $menus = [];
        if ($group['rights']) {
            $where = [
                ['mid', 'in', implode(',', $group['rights'])],
                ['status', '=', 1],
            ];
            $menus = Db::table('bew_admin_sys_menu')->order('type,sort desc')->where($where)->select();
            foreach ($menus as $menus_v) {
                if ($menus_v['parent_id'] == 0) {
                    $menu[$menus_v['mid']] = $menus_v;
                } else {
                    $menu[$menus_v['parent_id']]['children'][] = $menus_v;
                }
            }
        }
        $arr = [
            'group' => $group,
            'menu' => $menu,
            'user' => $this->adminUser
        ];
        $this->returnCode(0, $arr);
    }
    # 管理员个人信息
    public function userinfo(){
        $data['account'] = trim(input('post.account'));
        $data['name'] = trim(input('post.name'));
        $data['phone'] = trim(input('post.phone'));
        $data['qq'] = (int)trim(input('post.qq'));
        $data['sex'] = (int)(input('post.sex'));
        if (empty($data['name'])) {
            $this->returnCode(90000013);
        }
        $password = trim(input('post.password'));
        if (!empty($password)) {
            $data['password'] = md5($password);
        }
        // 保存用户
        $res = Db::table('bew_admin_user')->where('uid', $this->adminUser['uid'])->update($data);
        if (!$res) {
            $this->returnCode(91000002);
        }
        $user = Db::table('bew_admin_user')->where('uid', $this->adminUser['uid'])->find();
        unset($user['password']);
        $this->returnCode(0, $user);
    }
    # 图片上传
    public function upload_img(){
        $file = request()->file();
        $files = request()->file('file');
        if ($file == null) {
            exit(json_encode(array('code' => 1, 'msg' => '没有文件上传')));
        }
        try {
            validate(['image' => 'filesize:10240|fileExt:jpg,png,gif,jpeg'])->check($file);
            $info = \think\facade\Filesystem::disk('public')->putFile('bews', $file['file']);
        } catch (\think\exception\ValidateException $e) {
            exit(json_encode(array('code' => 1, 'msg' => $e->getMessage())));
        }
        $info = str_replace("\\", "/", $info);
        $img = '/storage/' . $info;
        $this->config['admin_domain'] = 'http://admin-thinkphp-antd-vue-learning-api.ouyangke.com/';
        exit(json_encode(array('code' => 0, 'data' => $img, 'url' => $this->config['admin_domain'] . $img)));
    }
    # 图片上传
    public function upload_img_s(){
        $file = request()->file();
        $files = request()->file('file');
        if ($file == null) {
            exit(json_encode(array('errno' => 1, 'msg' => '没有文件上传')));
        }
        try {
            validate(['image' => 'filesize:10240|fileExt:jpg,png,gif,jpeg'])->check($file);
            $info = \think\facade\Filesystem::disk('public')->putFile('bews', $file['file']);
        } catch (\think\exception\ValidateException $e) {
            exit(json_encode(array('errno' => 1, 'message' => $e->getMessage())));
        }
        $this->config['admin_domain'] = 'http://admin-thinkphp-antd-vue-learning-api.ouyangke.com/';
        $info = str_replace("\\", "/", $info);
        $img = '/storage/' . $info;
        exit(json_encode(array('errno' => 0, 'data' => ['data' => $img, 'url' => $this->config['admin_domain'] . $img])));
    }
}
<?php

namespace app\admin\controller;

use think\facade\Db;
use think\facade\View;
use think\facade\Request;

class Bewtest extends Base{
    public function testlists(){
        $p = input('post.p',1);
        $type = input('post.type',1);
        $value = input('post.value');
        $test_reference = input('post.test_reference',-1);

        
        $where = [];

        if(!empty($value)){
            if($type == 1){
                $where[] = ['test_input','like','%'.$value.'%'];
            }else{
                $where[] = ['test_rich','like','%'.$value.'%'];
            }
        }
        if($test_reference == 1 || $test_reference == 0){
            $where[] = ['test_reference','=',$test_reference];
        }

        $total = Db::table('bew_z_test')->where($where)->count();
        $lists = Db::table('bew_z_test')->where($where)->page($p,10)->select()->toArray();

        if (empty($lists)) {
            $this->returnCode(91000005);
        }
        foreach ($lists as &$lists_v) {
            $lists_v['key'] = $lists_v['test_id'];
            if ($lists_v['test_reference'] == 1) {
                $lists_v['test_reference_s'] = '开启';
            } else {
                $lists_v['test_reference_s'] = '关闭';
            }
            if ($lists_v['test_radio'] == 1) {
                $lists_v['test_radio_s'] = '开启';
            } else {
                $lists_v['test_radio_s'] = '关闭';
            }
            $this->config['admin_domain'] = 'http://admin-thinkphp-antd-vue-learning-api.ouyangke.com/';
            $lists_v['test_time_s'] = date('Y-m-d H:i:s', $lists_v['test_time']);
            if (!empty($lists_v['test_img'])) {
                $lists_v['test_img_s'] = $this->config['admin_domain'] . $lists_v['test_img'];
            }
            if (!empty($lists_v['test_img_many'])) {
                $test_img_many = explode(';', $lists_v['test_img_many']);
                foreach ($test_img_many as $k => $test_img_many_v) {
                    if (!empty($test_img_many_v)) {
                        $lists_v['test_img_many_s'][] = [
                            'uid' => $k,
                            'url' => $this->config['admin_domain'] . $test_img_many_v,
                            'data' => $test_img_many_v
                        ];
                    }
                }
            }
        }
        $arr = [
            'lists' => $lists,
            'total' => $total
        ];
        $this->returnCode(0,$arr);
    }
    public function testsave(){
        $data['test_input'] = input('post.test_input');
        $data['test_rich'] = input('post.test_rich');
        $data['test_img'] = input('post.test_img');
        $data['test_img_many'] = input('post.test_img_many');
        $data['test_reference'] = input('post.test_reference');
        $data['test_time'] = input('post.test_time');
        if (!empty($data['test_time'])) {
            $data['test_time'] = strtotime($data['test_time']);
        }
        $data['test_data'] = date("Y-m-d", strtotime(input('post.test_data')));
        $data['test_datatime'] = date("Y-m-d H:i:s", strtotime(input('post.test_datatime')));
        $data['test_rich_editor'] = input('post.test_rich_editor');
        $data['test_url'] = input('post.test_url');
        $data['test_radio'] = input('post.test_radio');

        $test_id = (int)trim(input('post.test_id', 0));
        if (empty($test_id) || $test_id === 0) {
            $res = Db::table('bew_z_test')->insert($data);
            if (!$res) {
                $this->returnCode(91000001);
            }
            $this->returnCode(0);
        } else {
            $res = Db::table('bew_z_test')->where('test_id', $test_id)->update($data);
            if (!$res) {
                $this->returnCode(91000002);
            }
            if (!empty($data['test_img'])) {
                $data['test_img_s'] = $this->config['admin_domain'] . $data['test_img'];
            }
            $this->config['admin_domain'] = 'http://admin-thinkphp-antd-vue-learning-api.ouyangke.com/';
            if (!empty($data['test_img_many'])) {
                $test_img_many = explode(';', $data['test_img_many']);
                foreach ($test_img_many as $k => $test_img_many_v) {
                    if (!empty($test_img_many_v)) {
                        $data['test_img_many_s'][] = [
                            'uid' => $k,
                            'url' => $this->config['admin_domain'] . $test_img_many_v,
                            'data' => $test_img_many_v
                        ];
                    }
                }
            }
            $data['test_time_s'] = date('Y-m-d H:i:s', $data['test_time']);
            $data['test_id'] = $test_id;
            $this->returnCode(0, $data);
        }
    }
    public function testdel(){
        $test_id = (int)input('post.test_id');
        $res = Db::table('bew_z_test')->where('test_id', $test_id)->delete();
        if (empty($res)) {
            $this->returnCode('91000003');
        }
        $this->returnCode(0);
    }
}

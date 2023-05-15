<?php
/**
 * Created by PhpStorm.
 * User: 丁宁
 * Date: 2022/11/21
 * Time: 23:18
 */

namespace app\admin\controller;
use think\facade\Db;

/**
 * Class: Hobby
 * User: 丁宁
 * Time: 2022/11/24 18:08
 */
class Hobby extends Base
{
    /**
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function hobbylist(){
        // 获取兴趣列表
        $data = Db::table('gf_user_hobby')->where('delete_time','null')->select()->toArray();
        if(empty($data)){
            $this->returnCode(91000005);
        }
        foreach ($data as &$data_v){
            if($data_v['status'] == 1){
                $data_v['status_s'] = '开启';
            }else{
                $data_v['status_s'] = '关闭';
            }
        }
        $this->returnCode(0,$data);
    }

    /**
     * @return void
     * @throws \think\db\exception\DbException
     */
    public function hobbysave(){
        // 添加兴趣
        $hobby_id = (int)trim(input('post.hobby_id'));
        $data['hobby'] = trim(input('post.hobby'));
        $data['status'] = (int)trim(input('post.status'));

        if(empty($hobby_id) || $hobby_id === 0){
            $data['create_time'] = date('Y-m-d H:i:s', time());
            $res = Db::table('gf_user_hobby')->insert($data);
            if(!$res){
                $this->returnCode(91000001);
            }
            $this->returnCode(0);
        }else{
            $data['update_time'] = date('Y-m-d H:i:s', time());
            $res = Db::table('gf_user_hobby')->where('hobby_id',$hobby_id)->update($data);
            if(!$res){
                $this->returnCode(91000002);
            }
            $data['hobby_id'] = $hobby_id;
            $this->returnCode(0,$data);
        }
    }

    /**
     * @return void
     * @throws \think\db\exception\DbException
     */
    public function hobbydel(){
        $hobby_id = (int)input('post.hobby_id');
        $data['delete_time'] = date('Y-m-d H:i:s', time());
        $data['status'] = 0;
        $res = Db::table('gf_user_hobby')->where('hobby_id',$hobby_id)->update($data);
        if(empty($res)){
            $this->returnCode(91000003);
        }
        $this->returnCode(0);
    }

    /**
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function hobbyuserlist(){
        // 获取用户列表
        $data = Db::table('gf_user_info')->where('delete_time','null')->select()->toArray();
        if(empty($data)){
            $this->returnCode(91000005);
        }
        foreach ($data as &$data_v){
            if($data_v['sex'] == 1){
                $data_v['sex_s'] = '男';
            }else{
                $data_v['sex_s'] = '女';
            }

            if($data_v['studentType'] == 1){
                $data_v['studentType_s'] = '专科生';
            }elseif ($data_v['studentType'] == 2){
                $data_v['studentType_s'] = '本科生';
            }else{
                $data_v['studentType_s'] = '专升本';
            }

            // 获取该用户的兴趣
            $data_v['hobby_s'] = Db::table('gf_user_info_hobby')
                                ->join('gf_user_hobby','gf_user_hobby.hobby_id = gf_user_info_hobby.hobby_id')
                                ->where('gf_user_info_hobby.user_info_id',$data_v['user_info_id'])
                                ->where('delete_time',null)
                                ->field('id,hobby')
                                ->select()
                                ->toArray();
            // 获取该用户对应的城市
            $data_v['province'] = Db::table('gf_province')->where('cityID',$data_v['provinceID'])->find();
            $data_v['city'] = Db::table('gf_city')->where('cityID',$data_v['cityID'])->find();
            if (empty($data_v['ip'])){
                $data_v['ip_address'] = '内网ip';
            }else{
                $data_v['ip_address'] = $this->getCity($data_v['ip']);
            }
        }
        $this->returnCode(0,$data);
    }

    /**
     * @return void
     */
    public function hobbyuserstate(){
        $user_info_id = (int)input('post.user_info_id');
        $data['status'] = 1;
        $res = Db::table('gf_user_info')->where('user_info_id',$user_info_id)->update($data);
        if(empty($res)){
            $this->returnCode(91000007);
        }
        $this->returnCode(0);
    }

    /**
     * @return void
     *
     */
    public function hobbyuserdel(){
        $user_info_id = (int)input('post.user_info_id');
        $data['delete_time'] = date('Y-m-d H:i:s', time());
        $res = Db::table('gf_user_info')->where('user_info_id',$user_info_id)->update($data);
        if(empty($res)){
            $this->returnCode(91000003);
        }
        $this->returnCode(0);
    }

    /**
     * @param $ip
     * 根据传过来的ip地址转换成具体的地址  即 ip =》真实地址
     */
    public function getCity($ip): string
    {
            $ip2region = new \Ip2Region();
            $info = $ip2region->btreeSearch($ip);
            $city = explode('|', $info['region']);
            $ip_address = $city['0'] . $city['1'] . $city['2'] . $city['3'] . $city['4'];
            return str_ireplace('0','',$ip_address);
        }
}
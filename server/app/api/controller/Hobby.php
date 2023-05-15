<?php
declare (strict_types=1);

namespace app\api\controller;

use think\facade\Db;
use think\Request;
use think\facade\Validate;

/**
 * Class: Hobby
 * User: 丁宁
 * Time: 2022/11/24 17:31
 */
class Hobby extends Base
{
    /**
     * 显示指定的资源
     * 根据前端传来的兴趣 id 查找对应兴趣的人
     * @param int $id
     * @return \think\Response
     */
    public function gethobbyuser()
    {
        // 判断 id 是否是整形
        $hobby_id = input('post.hobby_id');
        $data = Db::table('gf_user_hobby')
            ->join('gf_user_info_hobby', 'gf_user_hobby.hobby_id = gf_user_info_hobby.hobby_id')
            ->join('gf_user_info', 'gf_user_info.user_info_id = gf_user_info_hobby.user_info_id')
            ->where('gf_user_hobby.hobby_id', $hobby_id)
            ->where('gf_user_info.status', 1)
            ->where('gf_user_info.delete_time', null)
            ->field('name,userName,sex,studentType,comment')
            ->select()
            ->toArray();

        //判断是否有值
        if (empty($data)) {
            return $this->create([], '无数据', 204);
        } else {
            return $this->create($data, '数据请求成功', 200);
        }

    }

    /**
     * 获取爱好列表123
     */
    public function hobbylist()
    {
        $data = Db::table('gf_user_hobby')->where('delete_time', null)->where('status',1)->select();
        return $this->create($data, '获取列表成功~', 200);
    }

    // ip 定位
//    public function getCity()
//    {
//        $ip = $this->get_client_ip();
//        $ip2region = new \Ip2Region();
//        $info = $ip2region->btreeSearch($ip);
//        $city = explode('|', $info['region']);
//        print_r($info);
//        if($info['city_id'] != 0){
//            return json(['code' => 1, 'data' => $city['2'] . $city['3'] . $city['4'], 'msg' => 'ok']);
//        }else{
//            return json(['code' => 1, 'data' => $city['0'], 'msg' => 'ok']);
//        }
//    }
}


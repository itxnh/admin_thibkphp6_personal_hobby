<?php
declare (strict_types = 1);

namespace app\api\controller;

use app\api\validate\UserInfo as UserInfoValidate;
use app\api\model\GfUserInfo as UserInfoModel;
use think\exception\ValidateException;
use think\facade\Db;
use think\Request;
use think\captcha\facade\Captcha;
use imyfone\TheCaptcha;
use think\api\Client;

class Userinfo extends Base
{
    /**
     * 显示资源列表
     *
     */
    public function index()
    {
        $data = Db::table('gf_user_info')
            ->where('delete_time',null)
            ->where('status',1)
            ->field('user_info_id,name,userName,sex,provinceID,cityID,comment')
            ->select()
            ->toArray();
        foreach ($data as &$v)
        {
            $provinces = Db::table('gf_province')->where('cityID',$v['provinceID'])->field('city')->select();
            $citys = Db::table('gf_city')->where('cityID',$v['cityID'])->field('city')->select();
            foreach ($provinces as $pro)
            {
                $v['province'] = $pro;
            }
            foreach ($citys as $cit)
            {
                $v['city'] = $cit;
            }
        }

        return $this->create($data, '数据请求成功', 200);
    }

    /**
     * 保存新建的资源
     * @return \think\Response
     */
    public function save()
    {
        //获取数据
        $data = input('post.');
        // 获取 ip 地址 将ip地址存入数据库
        $ip =  $this->get_client_ip();
        $toData = [
            'name' => (string)$data['name'],
            'userName' => (string)$data['userName'],
            'password' => (string)$data['password'],
            'passwordSet' => (string)$data['passwordSet'],
            'studentType' => $data['studentType'],
            'sex' => $data['sex'],
            'provinceID' => $data['city'][0],
            'cityID' => $data['city'][1],
            'comment' => $data['comment'],
            'ip' => $ip,
        ];


        // 检测输入的验证码是否正确，$value为用户输入的验证码字符串
        if( captcha_check($data['verify']))
        {
            // 验证失败
            return $this->create([], '验证码不正确~', 400);
        }

        // 验证返回
        try {
            // 验证
            validate(UserInfoValidate::class)->check($data);
        }catch (ValidateException $exception){
            // 错误返回
            return $this->create([],$exception->getError(), 400);
        }

        // 添加数据后如果需要返回新增数据的自增主键，可以使用insertGetId方法新增数据并返回主键值
        $id = Db::table('gf_user_info')->insertGetId($toData);


        foreach ($data['hobby'] as $v)
        {
            $hobby = [
                ['hobby_id' => $v, 'user_info_id' => $id]
            ];
            $i = Db::table('gf_user_info_hobby')->insertAll($hobby);
        }

        //判断是否有值
        if (empty($id)) {
            return $this->create([], '注册失败~', 400);
        } else {
            return $this->create([], '注册成功~', 200);
        }
    }

    /**
     * 输出验证码
     */
    public function captcha()
    {
        return Captcha::create();
    }

    /**
     * @param int $type
     * @return mixed
     * 获取当前浏览用户的ip地址
     */
    function get_client_ip(int $type = 0)
    {
        $type = $type ? 1 : 0;
        static $ip = NULL;
        if ($ip !== NULL) {
            return $ip[$type];
        }
        if (isset($_SERVER['HTTP_X_REAL_IP'])) {
            //nginx 代理模式下，获取客户端真实IP
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            //客户端的ip
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //浏览当前页面的用户计算机的网关
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            //浏览当前页面的用户计算机的ip地址
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: 丁宁
 * Date: 2022/11/22
 * Time: 18:24
 */

namespace app\api\controller;
use think\facade\Db;

/**
 * Class: City
 * User: 丁宁
 * Time: 2022/11/22 18:28
 * 省区两级联动
 */
class City extends Base
{
    /**
     * @return \think\Response
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function city()
    {
        $province = Db::table('gf_province')->field('cityID,city')->select()->toArray();
        foreach ($province as &$province_v)
        {
            $city = Db::table('gf_city')->where('father',$province_v['cityID'])->field('cityID,city')->select();
            $province_v['children'] = $city->toArray();
        }
        return $this->create($province, '数据请求成功~', 200);
    }
}
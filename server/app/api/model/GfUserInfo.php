<?php
declare (strict_types = 1);

namespace app\api\model;

use think\Model;

/**
 * @mixin \think\Model
 */
class GfUserInfo extends Model
{
    /**
     * @param $v
     * @return Model
     * 获取单条数据
     */
    public function getOne($v): Model
    {
        // find(1) 寻找的是 主键‘ID’ 为 1 的值
        $find = GfUserInfo::find($v);
        return $find;
    }

    /**
     *
     * 获取所有数据
     */
    public function getSelect()
    {
        return GfUserInfo::field('user_info_id,name,userName,sex,cityID,provinceID,comment')->select()->toArray();
    }
}

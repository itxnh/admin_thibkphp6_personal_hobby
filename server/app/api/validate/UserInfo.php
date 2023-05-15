<?php
declare (strict_types = 1);

namespace app\api\validate;

use think\Validate;

class UserInfo extends Validate
{
    /**
     * 定义验证规则
     * 格式：'字段名' =>  ['规则1','规则2'...] "^[ ]+$"
     *
     * @var array
     */
    protected $rule = [
        'name|用户名'      => ['require', 'max' => 25],
        'userName|真实姓名'=> ['require', 'max' => 25],
        'password|密码'   => 'require|confirm:passwordSet',
        'comment|备注'=> ['require', 'max' => 100],
    ];

    /**
     * 定义错误信息
     * 格式：'字段名.规则名' =>  '错误信息'
     *
     * @var array
     */
    protected $message = [];
}

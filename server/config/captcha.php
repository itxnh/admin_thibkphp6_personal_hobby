<?php
// 验证码配置
return [
	// 验证码的字符集abcdefhijkmnpqrstuvwxyzABCDEFHJKMNPQRSTUVWXYZ
	'codeSet' => '123456789',
	// 验证码过期时间
	'expire' => 1800,
	// 设置验证码字体大小
	'fontSize' => 14,
	// 添加混淆曲线
	'useCurve' => false,
	// 设置图片的高度、宽度
	'imageW' => 128,
	'imageH' => 36,
	// 验证码位数
	'length' => 4,
	// 验证成功后重置
	'reset' => true
];
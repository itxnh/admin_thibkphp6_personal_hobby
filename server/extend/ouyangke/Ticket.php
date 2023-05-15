<?php
namespace ouyangke;
/**
 * ticket
 * @author ouyangke
 */
class Ticket {
    /**
     * 只允许静态调用
     */
    final private function __construct() {
        throw new ThinkException('只允许静态调用');
    }
    /**
     * 创建ticket
     *
     * @param integer $uid 用户id
     * @param integer $bind 关键词
     * @param number $time 有效期
     * @param string $durable
     * @return multitype:number string
     */
    public static function create(int $uid, int $bind, $time=7*24*60*60, $durable = false) {
        $expire = time() + $time;
        // 获取key
        $key = self::_genKey($uid, $bind, $expire);
        // 生成票据 ticket
        return self::_buildTicket($bind, $uid, $key, $expire, $durable);
        // return array('ticket' => $ticket, 'expire_time' => $expire);
    }
    /**
     * 获取ticket信息
     *
     * @param integer $ticket
     * @param boolean $checked
     * @return Ambigous <mixed, NULL, multitype:number >
     */
    public static function get(int $ticket, $bind, bool $checked = false) {
        $checked || self::checkFormat($ticket);
        $uid        = 0;
        $key        = 0;
        $expire     = 0;
        $durable    = false;

        $info = self::_parseTicket($ticket, $bind, $uid, $key, $expire, $durable);

        if (time() < $expire) {
            $info = array();
            $info['uid'] = $uid;
            $info['expire'] = $expire;
            return $uid;
        }
        return false;
    }
    /**
     * 生成ticket
     * @param string  $bind
     * @param integer $uid
     * @param integer $key
     * @param integer $expire
     * @param boolean $durable
     * @return string
     */
    protected static function _buildTicket($bind, $uid, $key, $expire, $durable = false) {
        // decbin 函数把十进制数转换为二进制数
        $uBit = decbin($uid);
        // strlen 函数返回字符串的长度
        $uLen = strlen($uBit);
        // sprintf 把百分号（%）符号替换成一个作为参数进行传递的变量
        $eBit = sprintf('%032b', $expire);
        $kBit = sprintf('%032b', $key);
        // mt_rand 生成随机数
        // uniqid 生成一个唯一的 ID
        // md5 函数计算字符串的 MD5 散列
        // substr 函数返回字符串的一部分
        // base_convert 把十六进制数转换为八进制数
        $rBit = sprintf('%058s%05b%d', substr(base_convert(substr(md5(uniqid(mt_rand(), true)), mt_rand(0, 16), 16), 16, 2), 0, 58), $uLen % 32, $durable ? 1 : 0);
        $cBit = sprintf('%032b', crc32($uBit . $eBit . $kBit . $rBit . $bind));
        $uBit .= substr(sprintf('%032b', mt_rand()), $uLen - 32);

        $bin = '';
        for ($i = 0; $i < 32; $i++) {
            $bin .= $rBit[$i * 2];
            $bin .= $eBit[$i];
            $bin .= $kBit[$i];
            $bin .= $rBit[$i * 2 + 1];
            $bin .= $uBit[$i];
            $bin .= $cBit[$i];
        }
        // bindec 函数把二进制转换为十进制。
        // chr 函数从指定的 ASCII 值返回字符
        // str_split 函数把字符串分割到数组中
        // array_map 函数将用户自定义函数作用到数组中的每个值上，并返回用户自定义函数作用后的带有新的值的数组。
        // implode 函数返回一个由数组元素组合成的字符串。
        // base64_encode 函数用base64加密
        return strtr(base64_encode(implode('', array_map(function($item){ return chr(bindec($item)); }, str_split($bin, 8)))), '+/', '-_');
    }
    /**
     * 解析Ticket
     *
     * @param string $ticket
     * @param string  $bind
     * @param integer $uid
     * @param integer $key
     * @param integer $expire
     * @param boolean $durable
     * @return boolean
     */
    protected static function _parseTicket($ticket, $bind, &$uid = NULL, &$key = NULL, &$expire = NULL, &$durable = NULL) {
        $rBit = '';
        $eBit = '';
        $kBit = '';
        $uBit = '';
        $cBit = '';
        // strtr 函数转换字符串中特定的字符
        // base64_decode 函数用base64解密
        // str_split 函数把字符串分割到数组中
        // sprintf 把百分号（%）符号替换成一个作为参数进行传递的变量
        // array_map 函数将用户自定义函数作用到数组中的每个值上，并返回用户自定义函数作用后的带有新的值的数组
        // implode 函数返回一个由数组元素组合成的字符串
        $bin = implode('', array_map(function($item){ return sprintf('%08b', ord($item)); }, str_split(base64_decode(strtr($ticket, '-_', '+/')), 1)));
        for ($i = 0; $i < 192; $i += 6) {
            $rBit .= $bin[$i];
            $eBit .= $bin[$i + 1];
            $kBit .= $bin[$i + 2];
            $rBit .= $bin[$i + 3];
            $uBit .= $bin[$i + 4];
            $cBit .= $bin[$i + 5];
        }
        // substr 函数返回字符串的一部分
        // bindec 函数把二进制转换为十进制
        $uLen = bindec(substr($rBit, 58, 5));
        if ($uLen < 32) {
            $uBit = substr($uBit, 0, $uLen);
        }
        // crc32 函数计算字符串的 32 位 CRC（循环冗余校验）
        //32位系统会产生整型溢出，必需输出格式化
        // bindec 函数把二进制转换为十进制
        // sprintf 把百分号（%）符号替换成一个作为参数进行传递的变量
        if (sprintf('%u', crc32($uBit . $eBit . $kBit . $rBit . $bind)) == bindec($cBit)) {
            $uid = bindec($uBit);
            if ($uid > 0) {
                $expire     = bindec($eBit);
                $key        = bindec($kBit);
                $durable    = ($rBit[63] === '1');
                return true;
            }
        }
        throw new ThinkException('Ticket error');
    }
    /**
     * 生成key
     *
     * @param integer $uid
     * @param string $bind
     * @param integer $expire
     * @return integer
     */
    protected static function _genKey($uid, $bind, $expire) {
        // sprintf 把百分号（%）符号替换成一个作为参数进行传递的变量
        // md5 函数计算字符串的 MD5 散列
        // crc32 函数计算字符串的 32 位 CRC（循环冗余校验）
        return crc32(md5(sprintf('%d|%s|%d', $uid, $bind, $expire), true));
    }
    /**
     * 发送ticket http头
     *
     * @param string $ticket
     * @param integer $uid
     * @param integer $ttl
     */
    protected static function _headerTicket($ticket, $uid, $ttl) {
        header(sprintf('Set-Ticket: %s; uid=%d; expires=%s; Max-Age=%d', $ticket, $uid, Request::httpDate(time() + $ttl), $ttl));
    }
    /**
     * 判断格式是否正确
     *
     * @param string $ticket
     * @return boolean
     */
    protected static function _isTicket($ticket) {
        return strlen($ticket) == 32 && strspn($ticket, '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_') == 32;
    }
    /**
     * 检查格式是否正确
     *
     * @param string $ticket
     */
    public static function checkFormat($ticket) {
        if (!self::_isTicket($ticket)) {
            return false;
        }
    }
}
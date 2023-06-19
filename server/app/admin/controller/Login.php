<?php
/**
 * 后台管理系统-登录
 */
namespace app\admin\controller;
use think\App;
use app\AppApi;
use think\facade\Db;
use think\facade\Request;
use ouyangke\Ticket;

class Login{
    public function __construct(){
        header("Access-Control-Allow-Origin: *");
        date_default_timezone_set('PRC');
        if(empty(Request::isPost())){
            $this->returnCode(1,'请用post提交');
        }
    }
    public function login(){
        $account = trim(input('post.account'));
        if(empty($account)){
            $this->returnCode(90000005);
        }
        $pattern = "/^([0-9A-Za-z-_.]+)@([0-9a-z]+.[a-z]{2,3}(.[a-z]{2})?)$/i";
        if(!preg_match($pattern,$account)){
            $this->returnCode(90000006);
        }
        $password = trim(input('post.password'));
        if(empty($password)){
            $this->returnCode(90000007);
        }
        $aUser = Db::table('bew_admin_user')->where('account',$account)->find();
        if (empty($aUser)) {
            $this->returnCode(90000008);
        }
        if ($aUser['status'] != 1) {
            $this->returnCode(90000009);
        }
        if($aUser['password'] != md5($password)){
            $this->returnCode(90000010);
        }
        unset($aUser['password']);
        Db::table('bew_admin_user')->where('uid',$aUser['uid'])->update(
            ['times_login' => $aUser['times_login']+1,'time_last' => time()]
        );
        $ticket = Ticket::create($aUser['uid'],'ouyangke');
        $aUser['ticket'] = $ticket;
        $this->returnCode(0,$aUser,'登陆成功');
    }
    protected function returnCode($code,$data=[],$msg=''){
        header('Content-type:application/json');
        if($code == 0){
            $arr = array(
                'code'=>$code,
                'msg'=>$msg,
                'data' => $data
            );
        }else if($code == 1){
            $arr = array(
                'code'	=>	1,
                'msg'	=>	$data
            );
        }else if($code == 2){
            $arr = array(
                'code'	=>	2,
                'msg'	=>	$data
            );
        }else{
            $appapi = new AppApi();
            $arr = array(
                'code'=>$code,
                'msg'=>$appapi::errorTip($code)
            );
        }
        echo json_encode($arr);
        if($code != 0){
            exit;
        }
    }
}

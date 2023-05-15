<?php
/**
 *	后台管理系统-管理员
 */
namespace app\admin\controller;
use app\AppApi;
use think\facade\Db;
use think\facade\Request;
use ouyangke\Ticket;

class Base{
    public $adminUid = null;
    public $adminUser = [];
    public $config = [];
    public function __construct(){
        header("Access-Control-Allow-Origin:*");
        date_default_timezone_set('PRC');
        if(empty(Request::isPost())){
            $this->returnCode(1,'请用post提交');
        }
        $token = Request::header('Authorization');
        if(empty($token)){
            $token = Request::param('Authorization');
        }
        if(empty($token)){
            $this->returnCode(90000001);
        }
        $this->adminUid = Ticket::get($token,'ouyangke');
        $this->adminUser = Db::table('bew_admin_user')->where(['uid'=>$this->adminUid])->find();
        if(empty($this->adminUser)){
            $this->returnCode(90000002);
        }

        # 获取当前链接，查询是否有权限
        $controller = request()->controller();
        $action = request()->action();
        $key = $controller.'/'.$action;
    }
    /**
     * 返回json对象
     */
    protected function returnCode($code,$data=[]){
        header('Content-type:application/json');
        if($code == 0){
            $arr = array(
                'code'=>$code,
                'msg'=>'成功',
                'data' => $data
            );
        }else if($code >= 1 && $code <= 100){
            $arr = array(
                'code'	=>	$code,
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
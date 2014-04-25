<?php
class yunTongBu{

	private $mysql = null;

	function __construct(){
		$this -> mysql = new SaeMysql();
	}


	//用户注册
	function insertUserInfo($sina_username,$sina_token,$qq_username,$qq_token,$qq_openid){
      	$sqlStr = "select * from yuntongbu where sina_username = '{$sina_username}' and qq_username = '{$qq_username}'"; 
      	$user_info = $this -> mysql -> getData( $sqlStr );

		if(empty($user_info)){
			$sqlStr2 = "insert into yuntongbu(sina_username,sina_token,qq_username,qq_token,qq_openid,signtime ) values('{$sina_username}','{$sina_token}','{$qq_username}','{$qq_token}','{$qq_openid}',now())";
			$ret = $this -> mysql -> runSql( $sqlStr2 );
		}else{
			$sqlStr3 = "update yuntongbu set sina_token='{$sina_token}',qq_token='{$qq_token}',qq_openid='{$qq_openid}',signtime = now() where sina_username='{$sina_username}' and qq_username='{$qq_username}'";
			$ret = $this -> mysql -> runSql( $sqlStr3 );
		}
	}

	//获取用户数据并加入队列
	function getUserInfo($sina_username=0,$qq_username=0){
		$sqlStr = "select * from yuntongbu where 1";
      	if(!empty($sina_username) && !empty($qq_username)){
      		$sqlStr .= " and sina_username=".$sina_username." and qq_username='{$qq_username}'";	
      	}else{
        	$sqlStr .= ' and !(tosina = 1 && toqq=1) and blacklist = 0 order by id desc';
        }
      	$user_info = $this -> mysql -> getData( $sqlStr );
		return $user_info;
	}

	//修改同步状态
	function changeSync($tosina=0,$toqq=0,$sina_username,$qq_username){
		$sqlStr = "update yuntongbu set tosina={$tosina},toqq={$toqq} where 1 ";
      	if(!empty($sina_username)){
      		$sqlStr .= " and sina_username = ".$sina_username;
      	}
      	if(!empty($qq_username)){
      		$sqlStr .= " and qq_username = '{$qq_username}'";
      	}
		$ret = $this -> mysql -> runSql( $sqlStr );
	}

	//判断是否可以同步
	function isSync($app_title,$app_db_title,$timestamp,$timestamp_bd,$retweeted_status,$text){
		if($app_title == $app_db_title){
			return false;
		}
		if($timestamp <= $timestamp_bd){
			return false;
		}
		if(preg_match('/^-/', trim($text))){
			return false;
		}
		if(!empty($retweeted_status)){
			return false;
		}
		return true;
		
	}

	//同步后更新时间戳
	function updateLastTime($time,$sina_username,$qq_username,$type){
		$sqlStr = "update yuntongbu set {$type}_last_update={$time} where sina_username= {$sina_username} and qq_username = '{$qq_username}'";
		$ret = $this -> mysql -> runSql( $sqlStr );
	}

}
?>
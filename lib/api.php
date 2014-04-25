<?php 

class weiboClient extends OAuth{
	public $format = 'json';
	public $decode_json = TRUE;
	public $sina_host = "https://api.weibo.com/2/";
	public $qq_host = "https://open.t.qq.com/api/";


	/**
	 * 根据用户UID获取用户资料(包括最新一条微博)
	 *
	 * 按用户UID或昵称返回用户资料，同时也将返回用户的最新发布的微博。
	 * <br />对应API：{@link http://open.weibo.com/wiki/2/users/show users/show}
	 * 
	 * @access public
	 * @param int  $uid 用户UID。
	 * @return array
	 */
	function showUserById($uid,$access_token,$type){
        switch ($type) {
        	case 'sina':
        		$params = array(
		            'uid' => $uid,
		            'access_token' => $access_token
		        );
        		$response = $this->request($this->sina_host.'users/show.json',$params,'GET');
        		break;

        	case 'qq':
        		$params = array(
		            'name' => $uid,
		            'access_token' => $access_token,
		            'format' => 'json',
		            'oauth_version' => '2.a', 
		            'oauth_consumer_key' => QQ_AKEY, 
		            'openid' => '02EFBEFB0B681FB73A8E2761F41CE66B'
		        );
        		$response = $this->request($this->qq_host.'user/other_info',$params,'GET');
        		break;
        }		
		return json_decode($response, true);
	}

	/**
	* 发微博
	*/
	function postTweet($access_token,$status,$pic,$lat = NULL,$long = NULL,$type,$qq_openid = NULL){
		switch ($type) {
			case 'sina':
				$params = array(
		            'access_token' => $access_token,
		            'status' => $status
		        );
				if(!empty($lat) && !empty($long)){
					$params['lat'] = $lat;
					$params['long'] = $long;
				}
				if(empty($pic)){
					$response = $this->request($this->sina_host.'statuses/update.json',$params,'POST');
				}else{
		        	$params['url'] = $pic.'/2000';
					$response = $this->request($this->sina_host.'statuses/upload_url_text.json',$params,'POST');
				}
				break;
			
			case 'qq':
				$params = array(
		            'access_token' => $access_token,
		            'content' => $status,
		            'format' => 'json',
		            'openid' => $qq_openid,
		            'scope' => 'all',
		            'oauth_version' => '2.a',
		            'oauth_consumer_key' => QQ_AKEY,
		            'clientip' => '192.168.0.1'
		        );
				if(!empty($lat) && !empty($long)){
					$params['latitude'] = $lat;
					$params['longitude'] = $long;
				}
				if(empty($pic)){
					$response = $this->request($this->qq_host.'t/add',$params,'POST');
				}else{
		        	$params['pic_url'] = $pic;
		        	//$multi = array('pic' => dirname(__FILE__).'/logo.png');
					$response = $this->request($this->qq_host.'t/add_pic_url',$params,'POST');
				}
				break;
		}
		return json_decode($response, true);
	}



	protected function id_format(&$id) {
		if ( is_float($id) ) {
			$id = number_format($id, 0, '', '');
		} elseif ( is_string($id) ) {
			$id = trim($id);
		}
	}

	//获取用户信息及最新一条微博信息
	function readFirstTweet($uid,$access_token,$type,$openid=NULL){
        switch ($type) {
        	case 'sina':
        		$params = array(
		            'count' => 1,
		            'access_token' => $access_token
		        );
        		$response = $this->request($this->sina_host.'statuses/user_timeline.json',$params,'GET');
        		break;

        	case 'qq':
        		$params = array(
		            'name' => $uid,
		            'access_token' => $access_token,
		            'format' => 'json',
		            'oauth_version' => '2.a', 
		            'oauth_consumer_key' => QQ_AKEY, 
		            'openid' => $openid,
		            'pagetime' => 0,
		            'reqnum' => 1,
		            'lastid' => 0,
		            'type' => '0x1',
		            'contenttype' => 0
		        );
        		$response = $this->request($this->qq_host.'statuses/broadcast_timeline',$params,'GET');
        		break;
        }		
		return json_decode($response, true);
	}

	//判断是否可以同步
	function isSync($app_title,$app_db_title,$timestamp,$timestamp_bd,$retweeted_status,$text){
		//echo $app_title.'/'.$app_db_title.'/'.$timestamp.'/'.$timestamp_bd.'/'.$retweeted_status.'/'.$text;
		
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

}

?>
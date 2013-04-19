<?php
/**
 * 开放平台操作类
 * @param 
 * @return
 * @author tuguska
 */


class MBApiClient
{
    /** 
     * 构造函数 
     *  
     * @access public 
     * @param mixed $wbakey 应用APP KEY 
     * @param mixed $wbskey 应用APP SECRET 
     * @param mixed $accecss_token OAuth认证返回的token 
     * @param mixed $accecss_token_secret OAuth认证返回的token secret 
     * @return void 
	 */
	public $host = 'open.t.qq.com';
    function __construct( $wbakey , $wbskey , $accecss_token , $accecss_token_secret ) 
	{
        $this->oauth = new MBOpenTOAuth( $wbakey , $wbskey , $accecss_token , $accecss_token_secret ); 
	}

	/******************
	 * 获取用户消息
     * @access public 
	*@f 分页标识（0：第一页，1：向下翻页，2向上翻页）
	*@t: 本页起始时间（第一页 0，继续：根据返回记录时间决定）
	*@n: 每次请求记录的条数（1-20条）
	*@name: 用户名 空表示本人
	 * *********************/
	public function getTimeline($p){
		if(!isset($p['name'])){
			$url = 'http://open.t.qq.com/api/statuses/home_timeline?f=1';
			$params = array(
				'format' => 'json',
				'pageflag' => $p['f'],
				'reqnum' => $p['n'],
				'pagetime' =>  $p['t']
			);					
		}else{
			$url = 'http://open.t.qq.com/api/statuses/user_timeline?f=1';
			$params = array(
				'format' => 'json',
				'pageflag' => $p['f'],
				'reqnum' => $p['n'],
				'pagetime' =>  $p['t'],
				'name' => $p['name']
			);					
		}
	 	return $this->oauth->post($url,$params); 
	}

	/******************
	*获取关于我的消息 
	*@f 分页标识（0：第一页，1：向下翻页，2向上翻页）
	*@t: 本页起始时间（第一页 0，继续：根据返回记录时间决定）
	*@n: 每次请求记录的条数（1-20条）
	*@l: 当前页最后一条记录，用用精确翻页用
	*@type : 0 提及我的, other 我发表的
	**********************/
	public function getMyTweet(){
		$p =array(
			'f' => 0,
			'n' => 1,		
			't' => 0,
			'l' => '',
			'type' => 1
		);
		$p['type']==0?$url = 'http://open.t.qq.com/api/statuses/mentions_timeline?f=1':$url = 'http://open.t.qq.com/api/statuses/broadcast_timeline?f=1';
		$params = array(
			'format' => 'json',
			'pageflag' => $p['f'],
			'reqnum' => $p['n'],
			'pagetime' => $p['t'],
			'lastid' => $p['l']	
		);	
	 	return $this->oauth->get($url,$params); 
	}

	/******************
	*发表一条消息
	*@c: 微博内容
	*@ip: 用户IP(以分析用户所在地)
	*@j: 经度（可以填空）
	*@w: 纬度（可以填空）
	*@p: 图片
	*@r: 父id
	*@u: Url:音乐地址
	*@tit Title:音乐名
	*@a Author:演唱者
	*@type: 1 发表 2 转播 3 回复 4 点评 5 发音乐微博 6 发视频微博
	**********************/
	public function postOne($text,$img,$jing,$wei){			
		$p =array(
			'c' => $text,
			'ip' => $_SERVER['REMOTE_ADDR'], 
			'j' => $jing,
			'w' => $wei,
		);

		$p['type'] = 1;	

		$params = array(
			'format' => 'json',
			'content' => $p['c'],
			'clientip' => $p['ip'],
			'jing' => $p['j'],
			'wei' => $p['w']
		);
		switch($p['type']){
			case 2:
				$url = 'http://open.t.qq.com/api/t/re_add?f=1';
				$params['reid'] = $p['r'];
				return $this->oauth->post($url,$params); 
				break;
			case 3:
				$url = 'http://open.t.qq.com/api/t/reply?f=1';
				$params['reid'] = $p['r'];
				return $this->oauth->post($url,$params); 
				break;
			case 4:
				$url = 'http://open.t.qq.com/api/t/comment?f=1';
				$params['reid'] = $p['r'];
				return $this->oauth->post($url,$params); 
				break;
			case 5:
				$url = 'http://open.t.qq.com/api/t/add_music?f=1';
				$params['url'] = $p['u'];
				$params['title'] = $p['tit'];
				$params['author'] = $p['a'];
				return $this->oauth->post($url,$params); 
				break;
			case 6:
				$url = 'http://open.t.qq.com/api/t/add_video?f=1';
				$params['url'] = $p['u'];
				return $this->oauth->post($url,$params); 
				break;
				
			default:
				if(!empty($img)){
					$url = 'http://open.t.qq.com/api/t/add_pic_url?f=1';
					$params['pic_url'] = $img;
					return $this->oauth->post($url,$params); 
				}else{
					$url = 'http://open.t.qq.com/api/t/add?f=1';
					return $this->oauth->post($url,$params); 
				}
			break;			
		}	
	}

	/******************
	*获取视频信息
	*@u: 视频url
	**********************/
	public function getVideo($p){
		$url = 'http://open.t.qq.com/api/t/getvideoinfo?f=1';
		$params = array(
			'format' => 'json',
			'url' => $p['u']
		);
	 	return $this->oauth->post($url,$params); 
	}

	/******************
	*获取当前用户的信息
	*@n:用户名 空表示本人
	**********************/
	public function getUserInfo($p=false){
		if(!$p || !$p['n']){
			$url = 'http://open.t.qq.com/api/user/info?f=1';
			$params = array(
				'format' => 'json'
			);
		}else{
			$url = 'http://open.t.qq.com/api/user/other_info?f=1';
			$params = array(
				'format' => 'json',
				'name' => $p['n']
			);
		}
	 	return $this->oauth->get($url,$params); 	
	}

	/******************
	*发私信
	*@c: 微博内容
	*@ip: 用户IP(以分析用户所在地)
	*@j: 经度（可以填空）
	*@w: 纬度（可以填空）
	*@n: 接收方微博帐号
	**********************/
	public function postOneMail($p){
		$url = 'http://open.t.qq.com/api/private/add?f=1';
		$params = array(
			'format' => 'json',
			'content' => $p['c'],
			'clientip' => $p['ip'],
			'jing' => $p['j'],
			'wei' => $p['w'],
			'name' => $p['n']
			);
		return $this->oauth->post($url,$params); 
	}
	/******************
	*查看数据更新条数
	*@op :请求类型 0：只请求更新数，不清除更新数，1：请求更新数，并对更新数清零
	*@type：5 首页未读消息记数，6 @页消息记数 7 私信页消息计数 8 新增粉丝数 9 首页广播数（原创的）
	**********************/	
	public function getUpdate($p){
		$url = 'http://open.t.qq.com/api/info/update?f=1';
		if(isset($p['type'])){
			if($p['op']){
				$params = array(
					'format' => 'json',
					'op' => $p['op'],
					'type' => $p['type']
				);			
			}else{
				$params = array(
					'format' => 'json',
					'op' => $p['op']
				);			
			}
		}else{
			$params = array(
				'format' => 'json',
				'op' => $p['op']
			);
		}
	 	return $this->oauth->get($url,$params);		
	}	

}

class MBOpenTOAuth {
	public $host = 'http://open.t.qq.com/';
	public $timeout = 30; 
	public $connectTimeout = 30;
	public $sslVerifypeer = FALSE; 
	public $format = 'json';
	public $decodeJson = TRUE; 
	public $httpInfo; 
	public $userAgent = 'oauth test'; 
	public $decode_json = FALSE; 

    function accessTokenURL()  { return 'https://open.t.qq.com/cgi-bin/access_token'; } 
    //function authenticateURL() { return 'http://open.t.qq.com/cgi-bin/authenticate'; } 
	function authenticateURL() { return 'http://open.t.qq.com/oauth_html/loginnew.php'; } 
    function authorizeURL()    { return 'http://open.t.qq.com/cgi-bin/authorize'; } 
	function mobelURL()    { return 'https://open.t.qq.com/oauth_html/mobel.php'; } 
	function requestTokenURL() { return 'https://open.t.qq.com/cgi-bin/request_token'; } 

	function lastStatusCode() { return $this->http_status; } 

    function __construct($consumer_key, $consumer_secret, $oauth_token = NULL, $oauth_token_secret = NULL) { 
        $this->sha1_method = new OAuthSignatureMethod_HMAC_SHA1(); 
        $this->consumer = new OAuthConsumer($consumer_key, $consumer_secret); 
        if (!empty($oauth_token) && !empty($oauth_token_secret)) { 
            $this->token = new OAuthConsumer($oauth_token, $oauth_token_secret); 
        } else { 
            $this->token = NULL; 
        } 
	}

    /** 
     * oauth授权之后的回调页面 
	 * 返回包含 oauth_token 和oauth_token_secret的key/value数组
     */ 
    function getRequestToken($oauth_callback = NULL) { 
        $parameters = array(); 
        if (!empty($oauth_callback)) { 
            $parameters['oauth_callback'] = $oauth_callback; 
        }  

        $request = $this->oAuthRequest($this->requestTokenURL(), 'GET', $parameters); 
		$token = OAuthUtil::parse_parameters($request); 
        $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']); 
        return $token; 
    } 

    /** 
     * 获取授权url
     * @return string 
     */ 
    function getmobelURL($token, $signInWithWeibo = TRUE , $url='') { 
        if (is_array($token)) { 
            $token = $token['oauth_token']; 
        } 
        if (empty($signInWithWeibo)) { 
            return $this->mobelURL() . "?oauth_token={$token}"; 
        } else { 
            return $this->mobelURL() . "?oauth_token={$token}"; 
        } 
	} 

    /** 
     * 获取授权url
     * @return string 
     */ 
    function getAuthorizeURL($token, $signInWithWeibo = TRUE , $url='') { 
        if (is_array($token)) { 
            $token = $token['oauth_token']; 
        } 
        if (empty($signInWithWeibo)) { 
            return $this->authorizeURL() . "?oauth_token={$token}"; 
        } else { 
            return $this->authenticateURL() . "?oauth_token={$token}"; 
        } 
	} 	

    /** 
	* 交换授权
	* Exchange the request token and secret for an access token and 
     * secret, to sign API calls. 
     * 
     * @return array array("oauth_token" => the access token, 
     *                "oauth_token_secret" => the access secret) 
     */ 
    function getAccessToken($oauth_verifier = FALSE, $oauth_token = false) { 
        $parameters = array(); 
        if (!empty($oauth_verifier)) { 
            $parameters['oauth_verifier'] = $oauth_verifier; 
        } 
		$request = $this->oAuthRequest($this->accessTokenURL(), 'GET', $parameters);
		$token = OAuthUtil::parse_parameters($request); 
        $this->token = new OAuthConsumer($token['oauth_token'], $token['oauth_token_secret']); 
        return $token; 
	} 

	function jsonDecode($response, $assoc=true)	{
		//echo $response;
		$response = preg_replace('/[^\x20-\xff]*/', "", $response);	
		$jsonArr = json_decode($response, $assoc);
		if(!is_array($jsonArr))
		{
			throw new Exception('格式错误!');
		}
		
		$ret = $jsonArr["ret"];
		$msg = $jsonArr["msg"];
		/**
		 *Ret=0 成功返回
		 *Ret=1 参数错误
		 *Ret=2 频率受限
		 *Ret=3 鉴权失败
		 *Ret=4 服务器内部错误
		 */
		switch ($ret) {
			case 0:
				return $jsonArr;;
				break;
			case 1:
				throw new Exception('参数错误!');
				break;
			case 2:
				throw new Exception('频率受限!');
				break;
			case 3:
				throw new Exception('鉴权失败!');
				break;
			default:
				$errcode = $jsonArr["errcode"];
				if(isset($errcode))			//统一提示发表失败
				{
					throw new Exception("发表失败");
					break;
					//require_once MB_COMM_DIR.'/api_errcode.class.php';
					//$msg = ApiErrCode::getMsg($errcode);
				}
				throw new Exception('服务器内部错误!');
				break;
		}
	}
	
    /** 
     * 重新封装的get请求. 
     * @return mixed 
     */ 
    function get($url, $parameters) { 

		$response = $this->oAuthRequest($url, 'GET', $parameters); 
		if ('json' === 'json') { 
            return $this->jsonDecode($response, true);
			
		}
        return $response; 
	}

	 /** 
     * 重新封装的post请求. 
     * @return mixed 
     */ 
    function post($url, $parameters = array() , $multi = false) { 
        $response = $this->oAuthRequest($url, 'POST', $parameters , $multi );
		if ('json' === 'json') { 
            return $this->jsonDecode($response, true); 
        } 
        return $response; 
	}

	 /** 
     * DELTE wrapper for oAuthReqeust. 
     * @return mixed 
     */ 
    function delete($url, $parameters = array()) { 
        $response = $this->oAuthRequest($url, 'DELETE', $parameters); 
		if ('json' === 'json') { 
            return $this->jsonDecode($response, true); 
        } 
        return $response; 
    } 

    /** 
     * 发送请求的具体类
     * @return string 
     */ 
    function oAuthRequest($url, $method, $parameters , $multi = false) { 
        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'https://') !== 0) { 
            $url = "{$this->host}{$url}.{$this->format}"; 
		}
		
        $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters); 
		$request->sign_request($this->sha1_method, $this->consumer, $this->token);
        switch ($method) { 
        case 'GET': 
            return $this->http($request->to_url(), 'GET'); 
        default: 
            return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata($multi) , $multi ); 
        } 
	}     

	function http($url, $method, $postfields = NULL , $multi = false){
		//$https = 0;
		//判断是否是https请求
		if(strrpos($url, 'https://')===0){
			$port = 443;
			$version = '1.1';
			$host = 'ssl://open.t.qq.com';	
		}else{
			$port = 80;	
			$version = '1.0';
			$host = 'open.t.qq.com';
		}
		$header = "$method $url HTTP/$version\r\n";	
		$header .= "Host: open.t.qq.com"."\r\n";
		if($multi){
			$header .= "Content-Type: multipart/form-data; boundary=" . OAuthUtil::$boundary . "\r\n";	
		}else{	
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";  
		}
		if(strtolower($method) == 'post' ){
			$header .= "Content-Length: ".strlen($postfields)."\r\n";
			$header .= "Connection: Close\r\n\r\n";  
			$header .= $postfields;
		}else{
			$header .= "Connection: Close\r\n\r\n";  
		}
		$ret = '';	
		$fp = fsockopen($host,$port,$errno,$errstr,30);
		if(!$fp){
			$error = '建立sock连接失败';
			throw new Exception($error);
		}else{
			fwrite ($fp, $header);  
			while (!feof($fp)) {
				$ret .= fgets($fp, 4096);
			}
			fclose($fp);
			if(strrpos($ret,'Transfer-Encoding: chunked')){
				$info = @split("\r\n\r\n",$ret);
				$response = @split("\r\n",$info[1]);
				$t = array_slice($response,1,-1);

				$returnInfo = implode('',$t);
			}else{
				$response = @split("\r\n\r\n",$ret);
				$returnInfo = $response[1];
			}
			//转成utf-8编码
			return iconv("utf-8","utf-8//ignore",$returnInfo);
		}
	}
 

	/*
	使用curl库的请求函数,可以根据实际情况使用
	function http($url, $method, $postfields = NULL , $multi = false){
        $this->http_info = array(); 
        $ci = curl_init(); 
        curl_setopt($ci, CURLOPT_USERAGENT, $this->userAgent); 
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout); 
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout); 
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->sslVerifypeer); 
        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader')); 
        curl_setopt($ci, CURLOPT_HEADER, FALSE); 

        switch ($method) { 
        case 'POST': 
            curl_setopt($ci, CURLOPT_POST, TRUE); 
            if (!empty($postfields)) { 
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields); 
            } 
            break; 
        case 'DELETE': 
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE'); 
            if (!empty($postfields)) { 
                $url = "{$url}?{$postfields}"; 
            } 
        } 

        $header_array = array(); 
        $header_array2=array(); 
        if( $multi ) 
        	$header_array2 = array("Content-Type: multipart/form-data; boundary=" . OAuthUtil::$boundary , "Expect: ");
        foreach($header_array as $k => $v) 
            array_push($header_array2,$k.': '.$v); 

        curl_setopt($ci, CURLOPT_HTTPHEADER, $header_array2 ); 
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE ); 

        curl_setopt($ci, CURLOPT_URL, $url); 

        $response = curl_exec($ci); 
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE); 
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci)); 
        $this->url = $url; 
		print_r($response);	
        curl_close ($ci); 
        return $response; 

	}*/
	
    function getHeader($ch, $header) { 
        $i = strpos($header, ':'); 
        if (!empty($i)) { 
            $key = str_replace('-', '_', strtolower(substr($header, 0, $i))); 
            $value = trim(substr($header, $i + 2)); 
            $this->http_header[$key] = $value; 
        } 
        return strlen($header); 
	} 
}
?>

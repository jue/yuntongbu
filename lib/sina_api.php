<?php 
/** 
 * 新浪微博操作类 
 * 
 * @package sae 
 * @author Easy Chen 
 * @version 1.0 
 */ 
class WeiboClient 
{ 
    /** 
     * 构造函数 
     *  
     * @access public 
     * @param mixed $akey 微博开放平台应用APP KEY 
     * @param mixed $skey 微博开放平台应用APP SECRET 
     * @param mixed $accecss_token OAuth认证返回的token 
     * @param mixed $accecss_token_secret OAuth认证返回的token secret 
     * @return void 
     */ 
    function __construct( $akey , $skey , $accecss_token , $accecss_token_secret ) 
    { 
        $this->oauth = new WeiboOAuth( $akey , $skey , $accecss_token , $accecss_token_secret ); 
    } 

    function postTosina($text,$img){
        $param = array(); 
        $param['status'] = $text; 
        if(!empty($img)){
            $param['pic'] = '@'.$img;
            return $this->oauth->post( 'http://api.t.sina.com.cn/statuses/upload.json' , $param , true );
        }else{
            return $this->oauth->post( 'http://api.t.sina.com.cn/statuses/update.json' , $param ); 
        }
    }

	/**
	 * 获取用户发布的微博信息列表
	 * 返回用户的发布的最近n条信息，和用户微博页面返回内容是一致的。此接口也可以请求其他用户的最新发表微博。
	 * 
	 * @access public
	 * @param int $page 页码
	 * @param int $count 每次返回的最大记录数，最多返回200条，默认20。
	 * @param mixed $uid_or_name 指定用户UID或微博昵称
	 * @param int64 $since_id 若指定此参数，则只返回ID比since_id大的微博消息（即比since_id发表时间晚的微博消息）。可选。
	 * @param int64 $max_id 若指定此参数，则返回ID小于或等于max_id的提到当前登录用户微博消息。可选。
	 * @return array
	 */
	function user_timeline( $page = 1 , $count = 20 , $uid_or_name = NULL , $since_id = NULL, $max_id = NULL)
	{
		$params = array();
		if ($since_id) {
			$this->id_format($since_id);
			$params['since_id'] = $since_id;
		}
		if ($max_id) {
			$this->id_format($max_id);
			$params['max_id'] = $max_id;
		}

		return $this->request_with_uid( 'http://api.t.sina.com.cn/statuses/user_timeline.json' ,  $uid_or_name , $page , $count , NULL , true, $params );
	}
    // ========================================= 

    /** 
     * @ignore 
     */ 
    protected function request_with_pager( $url , $page = false , $count = false ) 
    { 
        $param = array(); 
        if( $page ) $param['page'] = $page; 
        if( $count ) $param['count'] = $count; 

        return $this->oauth->get($url , $param ); 
    } 

    /** 
     * @ignore 
     */ 
    protected function request_with_uid( $url , $uid_or_name , $page = false , $count = false , $cursor = false , $post = false ) 
    { 
        $param = array(); 
        if( $page ) $param['page'] = $page; 
        if( $count ) $param['count'] = $count; 
        if( $cursor )$param['cursor'] =  $cursor; 

        if( $post ) $method = 'post'; 
        else $method = 'get'; 

        if( is_numeric( $uid_or_name ) ) 
        { 
            $param['user_id'] = $uid_or_name; 
            return $this->oauth->$method($url , $param ); 

        }elseif( $uid_or_name !== null ) 
        { 
            $param['screen_name'] = $uid_or_name; 
            return $this->oauth->$method($url , $param ); 
        } 
        else 
        { 
            return $this->oauth->$method($url , $param ); 
        } 

    } 

} 

/** 
 * 新浪微博 OAuth 认证类 
 * 
 * @package sae 
 * @author Easy Chen 
 * @version 1.0 
 */ 
class WeiboOAuth { 
    /** 
     * Contains the last HTTP status code returned.  
     * 
     * @ignore 
     */ 
    public $http_code; 
    /** 
     * Contains the last API call. 
     * 
     * @ignore 
     */ 
    public $url; 
    /** 
     * Set up the API root URL. 
     * 
     * @ignore 
     */ 
    public $host = "http://api.t.sina.com.cn/"; 
    /** 
     * Set timeout default. 
     * 
     * @ignore 
     */ 
    public $timeout = 30; 
    /**  
     * Set connect timeout. 
     * 
     * @ignore 
     */ 
    public $connecttimeout = 30;  
    /** 
     * Verify SSL Cert. 
     * 
     * @ignore 
     */ 
    public $ssl_verifypeer = FALSE; 
    /** 
     * Respons format. 
     * 
     * @ignore 
     */ 
    public $format = 'json'; 
    /** 
     * Decode returned json data. 
     * 
     * @ignore 
     */ 
    public $decode_json = TRUE; 
    /** 
     * Contains the last HTTP headers returned. 
     * 
     * @ignore 
     */ 
    public $http_info; 
    /** 
     * Set the useragnet. 
     * 
     * @ignore 
     */ 
    public $useragent = 'Sae T OAuth v0.2.0-beta2'; 
    /* Immediately retry the API call if the response was not successful. */ 
    //public $retry = TRUE; 
    



    /** 
     * Set API URLS 
     */ 
    /** 
     * @ignore 
     */ 
    function accessTokenURL()  { return 'http://api.t.sina.com.cn/oauth/access_token'; } 
    /** 
     * @ignore 
     */ 
    function authenticateURL() { return 'http://api.t.sina.com.cn/oauth/authenticate'; } 
    /** 
     * @ignore 
     */ 
    function authorizeURL()    { return 'http://api.t.sina.com.cn/oauth/authorize'; } 
    /** 
     * @ignore 
     */ 
    function requestTokenURL() { return 'http://api.t.sina.com.cn/oauth/request_token'; } 


    /** 
     * Debug helpers 
     */ 
    /** 
     * @ignore 
     */ 
    function lastStatusCode() { return $this->http_status; } 
    /** 
     * @ignore 
     */ 
    function lastAPICall() { return $this->last_api_call; } 

    /** 
     * construct WeiboOAuth object 
     */ 
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
     * Get a request_token from Weibo 
     * 
     * @return array a key/value array containing oauth_token and oauth_token_secret 
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
     * Get the authorize URL 
     * 
     * @return string 
     */ 
    function getAuthorizeURL($token, $sign_in_with_Weibo = TRUE , $url) { 
        if (is_array($token)) { 
            $token = $token['oauth_token']; 
        } 
        if (empty($sign_in_with_Weibo)) { 
            return $this->authorizeURL() . "?oauth_token={$token}&oauth_callback=" . urlencode($url); 
        } else { 
            return $this->authenticateURL() . "?oauth_token={$token}&oauth_callback=". urlencode($url); 
        } 
    } 

    /** 
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

    /** 
     * GET wrappwer for oAuthRequest. 
     * 
     * @return mixed 
     */ 
    function get($url, $parameters = array()) { 
        $response = $this->oAuthRequest($url, 'GET', $parameters); 
        if ($this->format === 'json' && $this->decode_json) { 
            return json_decode($response, true); 
        } 
        return $response; 
    } 

    /** 
     * POST wreapper for oAuthRequest. 
     * 
     * @return mixed 
     */ 
    function post($url, $parameters = array() , $multi = false) { 
        
        $response = $this->oAuthRequest($url, 'POST', $parameters , $multi ); 
        if ($this->format === 'json' && $this->decode_json) { 
            return json_decode($response, true); 
        } 
        return $response; 
    } 

    /** 
     * DELTE wrapper for oAuthReqeust. 
     * 
     * @return mixed 
     */ 
    function delete($url, $parameters = array()) { 
        $response = $this->oAuthRequest($url, 'DELETE', $parameters); 
        if ($this->format === 'json' && $this->decode_json) { 
            return json_decode($response, true); 
        } 
        return $response; 
    } 

    /** 
     * Format and sign an OAuth / API request 
     * 
     * @return string 
     */ 
    function oAuthRequest($url, $method, $parameters , $multi = false) { 

        if (strrpos($url, 'http://') !== 0 && strrpos($url, 'http://') !== 0) { 
            $url = "{$this->host}{$url}.{$this->format}"; 
        } 

        // echo $url ; 
        $request = OAuthRequest::from_consumer_and_token($this->consumer, $this->token, $method, $url, $parameters); 
        $request->sign_request($this->sha1_method, $this->consumer, $this->token); 
        switch ($method) { 
        case 'GET': 
            //echo $request->to_url(); 
            return $this->http($request->to_url(), 'GET'); 
        default: 
            return $this->http($request->get_normalized_http_url(), $method, $request->to_postdata($multi) , $multi ); 
        } 
    } 

    /** 
     * Make an HTTP request 
     * 
     * @return string API results 
     */ 
    function http($url, $method, $postfields = NULL , $multi = false) { 
        $this->http_info = array(); 
        $ci = curl_init(); 
        /* Curl settings */ 
        curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent); 
        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connecttimeout); 
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout); 
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE); 

        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->ssl_verifypeer); 

        curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader')); 

        curl_setopt($ci, CURLOPT_HEADER, FALSE); 

        switch ($method) { 
        case 'POST': 
            curl_setopt($ci, CURLOPT_POST, TRUE); 
            if (!empty($postfields)) { 
                curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields); 
                //echo "=====post data======\r\n";
                //echo $postfields;
            } 
            break; 
        case 'DELETE': 
            curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE'); 
            if (!empty($postfields)) { 
                $url = "{$url}?{$postfields}"; 
            } 
        } 

        $header_array = array(); 
        
/*
        $header_array["FetchUrl"] = $url; 
        $header_array['TimeStamp'] = date('Y-m-d H:i:s'); 
        $header_array['AccessKey'] = SAE_ACCESSKEY; 


        $content="FetchUrl"; 

        $content.=$header_array["FetchUrl"]; 

        $content.="TimeStamp"; 

        $content.=$header_array['TimeStamp']; 

        $content.="AccessKey"; 

        $content.=$header_array['AccessKey']; 

        $header_array['Signature'] = base64_encode(hash_hmac('sha256',$content, SAE_SECRETKEY ,true)); 
*/
        //curl_setopt($ci, CURLOPT_URL, SAE_FETCHURL_SERVICE_ADDRESS ); 

        //print_r( $header_array ); 
        $header_array2=array(); 
        if( $multi ) 
        	$header_array2 = array("Content-Type: multipart/form-data; boundary=" . OAuthUtil::$boundary , "Expect: ");
        foreach($header_array as $k => $v) 
            array_push($header_array2,$k.': '.$v); 

        curl_setopt($ci, CURLOPT_HTTPHEADER, $header_array2 ); 
        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE ); 

        //echo $url."<hr/>"; 

        curl_setopt($ci, CURLOPT_URL, $url); 

        $response = curl_exec($ci); 
        $this->http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE); 
        $this->http_info = array_merge($this->http_info, curl_getinfo($ci)); 
        $this->url = $url; 

        //echo '=====info====='."\r\n";
        //print_r( curl_getinfo($ci) ); 
        
        //echo '=====$response====='."\r\n";
        //print_r( $response ); 

        curl_close ($ci); 
        return $response; 
    } 

    /** 
     * Get the header info to store. 
     * 
     * @return int 
     */ 
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


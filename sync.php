<?php 
	@header('Content-Type:text/html;charset=utf-8'); 
	
	include_once( 'config.php' );
	
	include_once( 'lib/oauth.php' );
	include_once( 'lib/sina_api.php' );
	include_once( 'lib/qq_api.php' );

	$sina_c = new WeiboClient( SINA_AKEY , SINA_SKEY , SINA_TOKEN , SINA_SECRET  );
	$qq_c = new MBApiClient(QQ_AKEY,QQ_SKEY,QQ_TOKEN,QQ_SECRET);	
		
	$mmc=memcache_init();
	
	/* if(!isset(memcache_get($mmc,"sina_last_update"))){
		memcache_set($mmc,"sina_last_update","0");
	}
	
	if(!isset(memcache_get($mmc,"qq_last_update"))){
		memcache_set($mmc,"qq_last_update","0");
	} */
	
	$sina_last_update = memcache_get($mmc,"sina_last_update");
	$qq_last_update = memcache_get($mmc,"qq_last_update");	


	//同步腾讯微博到新浪微博
	if(TOSINA == 0){
		try{
			$qq_tweets = $qq_c->getMyTweet();
		}catch(Exception $e){
			exit;
		}
		$isQqTrue = true;
			
		switch($isQqTrue){						
			case $qq_tweets['data']['info'][0]['timestamp'] <= $qq_last_update :
				$isQqTrue = false;
				break;
				
			case !empty($qq_tweets['data']['info'][0]['source']) :
				$isQqTrue = false;
				break;

			case preg_match('/^-/', trim($qq_tweets['data']['info'][0]['origtext'])) : // 以 "-" 开关的微博不同步
				$isQqTrue = false;
				break;					
		}
			
		if($isQqTrue){
			if(!empty($qq_tweets['data']['info'][0]['image'][0])){
				$imgurl = $qq_tweets['data']['info'][0]['image'][0].'/2000';
			}
			$ret = $sina_c ->postTosina('- '.$qq_tweets['data']['info'][0]['origtext'],$imgurl);	
			if(!empty($ret['user'])){
				memcache_set($mmc,"qq_last_update",$qq_tweets['data']['info'][0]['timestamp']);
			}
		}
	}
	
	//同步新浪微博到腾讯微博
	if(TOQQ == 0){		
		$sina_tweets = $sina_c->user_timeline(1,1);	
		if(!empty($sina_tweets['error_code'])){
			exit;
		}else{		
			$isSinaTrue = true;		
			switch($isSinaTrue){			
				case strtotime($sina_tweets[0]['created_at']) <= $sina_last_update :  
					$isSinaTrue = false;
					break;
					
				case !empty($sina_tweets[0]['retweeted_status']) :  //判断是否转发、评论、对话
					$isSinaTrue = false;
					break;

				case preg_match('/^-/', trim($sina_tweets[0]['text'])) : // 以 "-" 开关的微博不同步
					$isSinaTrue = false;
					break;
			}
			if($isSinaTrue){
				//同步操作
				try{
					$ret = $qq_c->postOne('- '.$sina_tweets[0]['text'],$sina_tweets[0]['original_pic'],$sina_tweets[0]['geo']['coordinates'][1],$sina_tweets[0]['geo']['coordinates'][0]);
					if($ret['ret'] == 0){
						memcache_set($mmc,"sina_last_update",strtotime($sina_tweets[0]['created_at']));
					}
							
				}catch(Exception $e){
					
				}																						
			}
		}	
	}	
?>
<?php 
	@header('Content-Type:text/html;charset=utf-8'); 
	
	include_once( './config.php' );	
	include( './lib/oauth.php' );
	include( './lib/api.php' );

	$sina = new weiboClient( WB_AKEY , WB_SKEY ,'','', SINA_TOKEN );
	$qq = new weiboClient( QQ_AKEY , QQ_SKEY ,'','', QQ_TOKEN );

	$sinaTweet = $sina->readFirstTweet(SINA_USERNAME,SINA_TOKEN,'sina');
	if(!empty($sinaTweet['error'])){
		exit;
	}
	$qqTweet = $qq->readFirstTweet(QQ_USERNAME,QQ_TOKEN,'qq',QQ_OPENID);
	echo "<pre>";

	print_r($sinaTweet);
	$kv = new SaeKV();
	$ret = $kv->init();

	$sinaLastUpdate = $kv->get('sinaLastUpdate');

	if(empty($sinaLastUpdate)){
		$sinaLastUpdate = $kv->set('sinaLastUpdate', '100');
	}

	$qqLastUpdate = $kv->get('qqLastUpdate');

	if(empty($qqLastUpdate)){
		$qqLastUpdate = $kv->set('qqLastUpdate', '100');
	}

	//同步新浪微博到腾讯微博
	if(TOQQ == 0){
		$isSync = $sina -> isSync(strip_tags($sinaTweet['statuses'][0]['source']),SINA_TITLE,strtotime($sinaTweet['statuses'][0]['created_at']),$sinaLastUpdate,$sinaTweet['statuses'][0]['retweeted_status'],$sinaTweet['statuses'][0]['text']);
		if($isSync){
			$postToQq = $qq -> postTweet(QQ_TOKEN,$sinaTweet['statuses'][0]['text'],$sinaTweet['statuses'][0]['original_pic'],$sinaTweet['statuses'][0]['geo']['coordinates'][0],$sinaTweet['statuses'][0]['geo']['coordinates'][1],'qq',QQ_OPENID);
			$kv->replace('sinaLastUpdate', strtotime($sinaTweet['statuses'][0]['created_at']));
		}
	}


	//同步腾讯微博到新浪微博
	if(TOSINA == 0){
		$isSync = $qq -> isSync($qqTweet['data']['info'][0]['from'],QQ_TITLE,$qqTweet['data']['info'][0]['timestamp'],$qqLastUpdate,$qqTweet['data']['info'][0]['source'],$qqTweet['data']['info'][0]['origtext']);
		if($isSync){
          	$postToSina = $sina -> postTweet(SINA_TOKEN,$qqTweet['data']['info'][0]['origtext'],$qqTweet['data']['info'][0]['image'][0],$qqTweet['data']['info'][0]['latitude'],$qqTweet['data']['info'][0]['longitude'],'sina','');
			$kv->replace('qqLastUpdate', $qqTweet['data']['info'][0]['timestamp']);
		}
	}
	
		
?>
<?php
	//新浪微博开放平台应用信息
	define( "SINA_AKEY" , ''); //新浪微博 App Key
	define( "SINA_SKEY" , ''); //新浪微博 App Secret

	/***********************
	* 同步帐号对应的新浪微博token/secret值
	* 到 http://open.weibo.com/tools/console 获取 
	**********************/
	define( "SINA_USERNAME" , ''); 
	define( "SINA_TOKEN" , '');

  	
  	//腾讯微博开放平台应用信息
  	define( "QQ_AKEY" , '' ); //腾讯微博 App Key
	define( "QQ_SKEY" , '' ); //腾讯微博 App Secret

	/***********************
	* 同步帐号对应的腾讯微博token/secret值
	* 到 http://test.open.t.qq.com/ 获取 
	**********************/
	define( "QQ_USERNAME" , '');
	define( "QQ_TOKEN" , '' );
	define( "QQ_OPENID" , '' );


	define( "SINA_TITLE" , '' ); //新浪微博APP小尾巴
	define( "QQ_TITLE" , '' ); //腾讯微博APP小尾巴	


	/***********************
	* 是否同步腾讯微博到新浪微博
	* 0 开启同步
	* 1 关闭同步
	**********************/
	define( "TOSINA" , '0' );

	/***********************
	* 是否同步新浪微博到腾讯微博
	* 0 开启同步
	* 1 关闭同步
	**********************/
	define( "TOQQ" , '0' );	

?>
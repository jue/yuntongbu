<?php

	//新浪微博开放平台应用信息
	define( "SINA_AKEY" , 'Sina App Key' ); //新浪微博 App Key
	define( "SINA_SKEY" , 'Sina App Secret' ); //新浪微博 App Secret

	/***********************
	* 同步帐号对应的新浪微博token/secret值
	* 到 http://yuntongbu.app.nipao.com/get/ 获取 
	**********************/
	define( "SINA_TOKEN" , '你的token值' );
	define( "SINA_SECRET" , '你的secret值' );


  	
  	//腾讯微博开放平台应用信息
  	define( "QQ_AKEY" , 'QQ App Key' ); //腾讯微博 App Key
	define( "QQ_SKEY" , 'QQ App Secret' ); //腾讯微博 App Secret

	/***********************
	* 同步帐号对应的腾讯微博token/secret值
	* 到 http://yuntongbu.app.nipao.com/get/ 获取 
	**********************/
	define( "QQ_TOKEN" , '你的token值' );
	define( "QQ_SECRET" , '你的secret值' );
	


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
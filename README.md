## 微博云同步

一款基于新浪SAE的微博同步工具，支持新浪微博、腾讯微博原创微博同步

预览：[新浪微博](http://weibo.com/nipao)
	  [腾讯微博](http://t.qq.com/xiangjianfeng)

### 一、目录结构    
    .
    ├── README.md           --- 使用帮助
    ├── lib              
		├── oauth.php       --- oauth协议文件    	
		├── qq_api.php      --- 腾讯微博开放接口
		└── sina_api.php    --- 新浪微博开放接口    	
    ├── config.yaml         --- SAE服务配置
    └── sync.php			--- 微博云同步执行文件


### 二、功能
1. 将腾讯微博与新浪微博双向同步
2. 仅将腾讯微博同步新浪微博
3. 仅将新浪微博同步腾讯微博
4. 发微博时，只要在微博信息前加 - (中划线或减号)，这样该微博信息将不会被自动同步到另一微博。

### 三、准备工作
* 申请Sina App Engine(简称SAE) http://sae.sina.com.cn/?m=front
	> 登录成功后开启 服务管理中的 Memcache 、Cron 、 FetchURL
* 申请 新浪微博开放平台应用 http://open.weibo.com/development
	> 成功后您将获得新浪微博的 App Key 、 App Secret
* 申请 腾讯微博开放平台应用 http://dev.open.t.qq.com/developer/
	> 成功后您将获得腾讯微博的 App Key 、 App Secret

###安装说明
1. 下载 源文件 并解压 或者 GIT 到本地
2. 打开config.yaml文件修改‘accesskey’值 （SAE 应用信息->汇总信息 里查看）
3. 打开config.php文件修改新浪微博app key、腾讯微博app key 及其它信息
4. 将修改后的文件一起上传到SAE平台即可 (上传地址： 应用管理->代码管理->上传代码包)
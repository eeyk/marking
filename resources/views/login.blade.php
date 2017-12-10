<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="csrf-token" content="{{ csrf_token() }}"/>
	<title>登录界面</title>
	<link rel="stylesheet" type="text/css" href="/css/mainStyle.css">
	<link rel="stylesheet" type="text/css" href="/css/login.css">
	<script type="text/javascript" src="/js/jquery-2.1.4.min.js"></script>
</head>
<body>
	<!-- 自定义弹框 -->
	<div id="msgBox">
		<label class="msgHead"><span class="msgTip">提示</span><span id="msgClose" class="msgClose" onclick="closeMsgBox()"></span></label>
		<label id="msgBody" class="msgBody"></label>
		<label class="msgBottom"><span id="no" onclick="closeMsgBox()"> 取消 </span><span id="yes" onclick="closeMsgBox()"> 确定 </span></label>
	</div>
	<div id="loginBox">
		<label id="boxTop"><img src="/img/loginBoxTitle.png"></label>
		<form id="myForm">

			<div id="userName">
				<input id="account" name="account" type="text" placeholder="请输入您的帐号" />
				<span id="dropDown"></span>
			</div>
			<input id="password" name="password" type="password" placeholder="密码" />
			<input id="verification" name="verification" type="text" placeholder="输入验证码" />
			<input id="verificationCode" type = "button" onclick="createCode()"/>
			<span id="changeOne" onclick="createCode()">换一张</span>
		</form>
		<span id="loginBtn" onclick="login()">登&nbsp;&nbsp;录</span>
		<div style="clear:both"></div>
		<div id="rememberMeBox">
			<input id="rememberMe" name="rememberMe" type="checkbox" name="remember" checked="checked">&nbsp;记住我
		</div>
		<a id="forgetPsw" href="#########">忘记密码</a>
	</div>
	<script type="text/javascript" src="/js/main.js"></script>
	<script type="text/javascript" src="/js/login.js"></script>
</body>
</html>

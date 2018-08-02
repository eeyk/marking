<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	
	<!-- 与响应式相关的代码 -->
	<!-- user-scalable属性能够解决ipad切换横屏之后触摸才能回到具体尺寸的问题。 -->
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">

	<title>评分系统登录页面</title>
	<link rel="stylesheet" type="text/css" href="../styles/LoginPageStyles/LoginPage.css">
	<link rel="stylesheet" type="text/css" href="../styles/alertBox.css"><!-- ***********弹框样式*********** -->
</head>
<body>
	<div id="contain">
		<div id="centerBox">
			<header>
				<label id="imgArea"></label>
			</header>
			<form id="loginForm">
				<input id="account" name="account" type="text" placeholder="请输入用户名" />
				<input id="password" name="password" type="password" placeholder="请输入密码" />
				<label id="identity">
					<input type="radio" checked="checked" name="identity" value="administrator" />管理员
					<input type="radio" name="identity" value="user" />评委
				</label>
				<input id="loginBtn" type="button" value="登 录" />
			</form>
		</div>
	</div>

	<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script type="text/javascript">
	if (typeof jQuery == "undefined") {
		document.write(unescape('%3Cscript type="text/javascript" src="../scripts/jquery-2.1.4.min.js"%3E%3C/script%3E'));
	}
	</script><!-- *******************************CDN不可用时从本地服务器下载JQ****************************** -->
	<script type="text/javascript" src="../scripts/CommonScripts/jquery.serializejson.min.js"></script>
	<script type="text/javascript" src="../scripts/CommonScripts/alertBox.js"></script><!-- ****弹框功能**** -->
	<script type="text/javascript" src="../scripts/CommonScripts/CustomFunction.js"></script><!-- 自定义函数 -->
	<script type="text/javascript" src="../scripts/LoginPageScripts/login.js"></script><!-- ************************************************主页函数************************************************ -->
</body>
</html>
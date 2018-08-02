<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">

	<!-- 与响应式相关的代码 -->
	<!-- user-scalable属性能够解决ipad切换横屏之后触摸才能回到具体尺寸的问题。 -->
	<meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="HandheldFriendly" content="true">

	<title>评分系统</title>
	<link rel="stylesheet" type="text/css" href="../styles/HomePageStyles/HomePage.css">
	<link rel="stylesheet" type="text/css" href="../styles/alertBox.css"><!-- ***********弹框样式*********** -->
</head>
<body>
	<div id="contain">
		<!-- 页面头部 -->
		<header id="pageHeader">
			<label class="pageHeaderPart sameFontSize" id="helloName">你好，<span>某某某</span>!</label>
			<input class="pageHeaderPart sameFontSize" id="cancellation" type="button" value="注销" />
			<label class="pageHeaderPart sameFontSize" id="searchBox">
				<input id="searchInput" class="sameFontSize" type="text" placeholder="请输入活动名称">
				<span id="searchImg"></span>
			</label>
		</header>
		<!-- 页面头部以下 -->
		<div id="pageContainer">
			<!-- 左侧菜单栏 -->
			<ul id="menu">
				<li class="unchecked sameFontSize" id="createActivityLi"><span></span>创建新活动</li>
				<li class="unchecked sameFontSize" id="onGoingActivityLi"><span></span>正举办活动</li>
				<li class="unchecked sameFontSize" id="finishActivityLi"><span></span>已结束活动</li>
			</ul>
			<!-- 右侧核心区域 -->
			<div id="corePart"></div>
		</div>
	</div>

	<script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
	<script type="text/javascript">
	if (typeof jQuery == "undefined") {
		document.write(unescape('%3Cscript type="text/javascript" src="../scripts/CommonScripts/jquery-2.1.4.min.js"%3E%3C/script%3E'));
	}
	</script><!-- *******************************CDN不可用时从本地服务器下载JQ****************************** -->
	<script type="text/javascript" src="../scripts/CommonScripts/jquery.serializejson.min.js"></script>
	<script type="text/javascript" src="../scripts/CommonScripts/alertBox.js"></script><!-- ***********弹框功能*********** -->
	<script type="text/javascript" src="../scripts/CommonScripts/CustomFunction.js"></script><!-- *******自定义函数******* -->
	<script type="text/javascript" src="../scripts/CommonScripts/HomePageScripts.js"></script><!-- ************************************************主页函数************************************************ -->
</body>
</html>